<?php

use salesforce_data_collector\collectors\ConnexionManager;
use salesforce_data_collector\collectors\CustomCollector;
use Zorille\salesforce\data_models\Account;
use Zorille\salesforce\data_models\Asset;

/**
 * @property string $salesforceServeurOption
 *
 * @method string getSalesforceServeurOption()
 */
class SFDCLocationsCollector extends CustomCollector
{
    private array $dedup = [];

    protected ?ConnexionManager $connexion;

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
            'primary_key' => fn (Asset $asset) => ($parts = $this->connexion->getBillingParts($asset->getCustomerNumberC(), $asset->getBillingCodeC()))
                ? "{$asset->getCustomerNumberC()}.{$parts['location']}" : '',
            'name' => fn (Asset $asset) => ($parts = $this->connexion->getBillingParts($asset->getCustomerNumberC(), $asset->getBillingCodeC())) ?
                "{$asset->getCustomerNumberC()}.{$parts['location']}" : '',
            'status' => 'active',
            'locationtype_id' => 'Datacenter',
            'org_id' => 'Customer_Number__c'
        ];
    }

    /**
     * @return Account[]
     * @throws Exception
     */
    public function fetchData(): array|stdClass
    {
        /*
            On récupére la liste des assets
        */
        return array_merge(
            $this->connexion->getData()->getAssetData()->getAssetsToDecomissoning(),
            $this->connexion->getData()->getAssetData()->getAssets()
        );
    }

    public function filterCsvItem(array $item): array|stdClass
    {
        if (isset($this->dedup[$item['primary_key']])) return [];
        $this->dedup[$item['primary_key']] = true;
        return $item;
    }
}