<?php

use salesforce_data_collector\collectors\CustomCollector;
use Zorille\salesforce\data_models\Account;
use Zorille\framework\QueryBuilderOperator as QLOperator;
use Zorille\salesforce\SalesforceFactory;

/**
 * @property string $salesforceServeurOption
 *
 * @method string getSalesforceServeurOption()
 */
class SFDCContractsCollector extends CustomCollector
{
    protected function getOutputCsvItemTemplate(): array
    {
        return [
            'primary_key' => fn (Account $account) => "Contract {$account->getCustomerNumberTextC()}",
            'name' => fn (Account $account) => "Contract {$account->getCustomerNumberTextC()}",
            'org_id' => fn (Account $account) => $account->getCustomerNumberTextC(),
            'provider_id' => '90010',
            'start_date' => fn () => date('Y-m-d'),
            'status' => 'production',
            'contacts_list' => fn (Account $account) => "contact_id->name:Team {$account->getCustomerNumberTextC()}",
            'services_list' => fn (Account $account) => implode(
                '|',
                array_merge(array_map(
                    fn (array $item) =>
                        "service_id->productcode:{$item['productcode']}",
                    $this->connexion->getBillingPartsArr()[
                        $account->getCustomerNumberTextC()
                    ] ?? []
                ), [
                    'service_id->productcode:SETUP.ACCESS',
                    'service_id->productcode:SETUP.OTHER'
                ])
            ) ?? ''
        ];
    }

    protected function fetchData(): array|stdClass
    {
        return SalesforceFactory::new()->createCustomerQueryBuilder()
            ->select()->where('Customer_Number_Text__c', QLOperator::DIF, '')
            ->build()->toModel()['records'];
    }
}