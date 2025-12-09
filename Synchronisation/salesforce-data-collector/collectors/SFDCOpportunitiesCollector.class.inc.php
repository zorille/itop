<?php

use Zorille\salesforce\data_models\OpportunityProduct;
use salesforce_data_collector\collectors\CustomCollector;
use Zorille\salesforce\SalesforceFactory;

/**
 * @property string $salesforceServeurOption
 *
 * @method string getSalesforceServeurOption()
 */
class SFDCOpportunitiesCollector extends CustomCollector
{
    /**
     * Renvoie un template de rendu csv.
     * En clés sont les clés du CSV en sortie
     * et en valeur sont :
     *  - Si c'est une string :
     *      - Si la string existe dans les clés de l'objet salesforce, c'est la valeur associé.
     *      - Si la string n'existe pas alors elle est renseignée tel quel en valeur.
     *  - Si c'est une fonction, la fonction prend en paramètre
     * l'itération courrente du tableau d'objet retourné par sqlesforce et retourne une valeur
     * qui sera renseigné comme valeur pour la clée associée.
     *
     * @return array
     */
    protected function getOutputCsvItemTemplate(): array
    {
        return [
            'primary_key' => 'Id',
            'name' => 'Name',
            'description' => 'Description',
            'org_id' => function(?OpportunityProduct $record) {
                if (!$record || !$record->getOpportunity() || !$record->getOpportunity()->getAccount()) {
                    return '??';
                }
                return $record->getOpportunity()->getAccount()->getCustomerNumberTextC() ?? '??';
            },
            'business_criticity' => 'medium',
            'needmonitoring' => 'no',
            'billing_code' => function(?OpportunityProduct $record) {
                if (!$record || !$record->getAsset()) {
                    return '??';
                }
                return $record->getAsset()->getBillingCodeC() ?? '??';
            },
            'status' => 'production',
            'crm_reference' => function(?OpportunityProduct $record) {
                if (!$record || !$record->getOpportunity()) {
                    return '??';
                }
                return $record->getOpportunity()->getId() ?? '??';
            },
            'location_id' => 'Location_Ref__c'
        ];
    }

    /**
     * Fait la requête pour récupérer les donnés sur l'API Rest de salesforce
     * grâce à un request builder par objet salesforce.
     *
     * @return OpportunityProduct[] Le tableau d'objets sur lequel on bouclera pour générer le CSV final
     * @throws Exception
     */
    protected function fetchData(): array|stdClass
    {
        return SalesforceFactory::new()->createOpportunityProductQueryBuilder()
            ->select()->build()->toModel()['records'];
    }

    public function filterCsvItem(array $item): array|stdClass
    {
        if (empty($item['org_id'])) return [];
        return $item;
    }
}