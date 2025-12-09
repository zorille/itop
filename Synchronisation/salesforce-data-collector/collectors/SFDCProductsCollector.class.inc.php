<?php

use salesforce_data_collector\collectors\CustomCollector;
use Zorille\salesforce\query_builder;
use Zorille\salesforce\query_fetchers\OpportunityProductFetcher;

/**
 * @property string $salesforceServeurOption
 *
 * @method string getSalesforceServeurOption()
 */
class SFDCProductsCollector extends CustomCollector
{
    protected function getOutputCsvItemTemplate(): array
    {
        return [
            'primary_key'           => 'ProductCode',
            'name'                  => 'Name',
            'productcode'           => 'ProductCode',
            'servicefamily_id'      => 'Family',
            'org_id'                => '90010',
            'status'                => 'production',
        ];
    }

    protected function fetchData(): array|stdClass
    {
        return $this->connexion->getData()->getProductData()->getProducts();
    }
}
