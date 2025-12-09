<?php

use Zorille\salesforce\data_models\Account;
use Zorille\salesforce\data_models\Contact;
use salesforce_data_collector\collectors\CustomCollector;
use Zorille\framework\QueryBuilderOperator as QLOperator;
use Zorille\salesforce\SalesforceFactory;

/**
 * @property string $salesforceServeurOption
 *
 * @method string getSalesforceServeurOption()
 */
class SFDCContactsTeamsCollector extends CustomCollector
{
    private static array $allContacts = [];
    private string $listSeparator = "|";

    private SalesforceFactory $sf_factory;

    public function __construct()
    {
        parent::__construct();

        $this->sf_factory = SalesforceFactory::new();
    }

    protected function getOutputCsvItemTemplate(): array
    {
        return [
            'primary_key' => fn (Account $account) => "Team {$account->getCustomerNumberTextC()}",
            'name' => fn (Account $account) => "Team {$account->getCustomerNumberTextC()}",
            'notify' => 'no',
            'org_id' => fn (Account $account) => $account->getCustomerNumberTextC(),
            'persons_list' => fn (Account $account) => implode(
                $this->getListSeparator(),
                array_map(
                    fn (Contact $contact) =>
                        'person_id->email:' . str_replace(
                            '+', '-',
                            $contact->getEmail()
                        ),
                    array_filter(
                        $this->getAllContacts(),
                        fn (Contact $contact) =>
                            $contact->getAccount()
                                ->getCustomerNumberTextC() === $account->getCustomerNumberTextC()
                    )
                )
            )
        ];
    }

    protected function fetchData(): array|stdClass
    {
        static::$allContacts = array_filter(
            $this->sf_factory->createContactQueryBuilder()
                ->select()
                ->where('IsDeleted', QLOperator::EQUALS, false)
                ->build()->toModel()['records'],
            fn (Contact $contact) => !empty($contact->getEmail())
        );

        return $this->sf_factory->createCustomerQueryBuilder()
            ->select()
            ->where('Customer_Number_Text__c', QLOperator::DIF, '')
            ->build()->toModel()['records'];
    }

    public function getAllContacts(): array
    {
        return self::$allContacts;
    }

    public function getListSeparator(): string
    {
        return $this->listSeparator;
    }
}