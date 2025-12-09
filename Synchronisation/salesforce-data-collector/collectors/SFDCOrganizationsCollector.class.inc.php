<?php

use Zorille\salesforce\data_models\Account;
use salesforce_data_collector\collectors\CustomCollector;
use Zorille\framework\QueryBuilderOperator as QLOperator;
use Zorille\salesforce\SalesforceFactory;

/**
 * @property string $salesforceServeurOption
 *
 * @method string getSalesforceServeurOption()
 */
class SFDCOrganizationsCollector extends CustomCollector
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
            'code' => 'Customer_Number_Text__c',
            'parent_id' => '10000',
            'deliverymodel_id' => '10000 CODE Service Delivery',
            'status' => 'active'
        ];
    }

    /**
     * @return Account[]
     * @throws Exception
     */
    protected function fetchData(): array
    {
        return SalesforceFactory::new()->createCustomerQueryBuilder()
            ->select()->where('Customer_Number_Text__c', QLOperator::DIF, '')
            ->build()->toModel()['records'];
    }
}
