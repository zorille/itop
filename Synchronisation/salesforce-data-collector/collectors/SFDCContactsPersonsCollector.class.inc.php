<?php

use Zorille\framework\QueryBuilderOperator as QLOperator;
use Zorille\salesforce\data_models\Account;
use Zorille\salesforce\data_models\Contact;
use salesforce_data_collector\collectors\CustomCollector;
use Zorille\salesforce\SalesforceFactory;

/**
 * @property string $salesforceServeurOption
 *
 * @method string getSalesforceServeurOption()
 */
class SFDCContactsPersonsCollector extends CustomCollector
{
    public function getOutputCsvItemTemplate(): array
    {
        return [
            'primary_key' => 'Email',
            'name' => 'Lastname',
            'first_name' => fn (Contact $contact) => $contact->getFirstName() === null
                ? $contact->getAccount()->getCustomerNumberTextC() : $contact->getFirstName(),
            'status' => 'active',
            // '+' not accepted by itop
            'email' => fn (Contact $contact) => str_replace("+", '-', $contact->getEmail()),
            'notify' => fn (Contact $contact) => $contact->getId() === null ? 'no' : 'yes',
            'is_support_team' => fn (Contact $contact) => $contact->getId() === null ? 'yes' : 'no',
            'techintervention_flag' => 'yes',
            'portail_flag' => fn (Contact $contact) => $contact->getId() === null ? 'no' : 'yes',
            'org_id' => fn (?Contact $record) =>
                !$record || !$record->getAccount() || !$record->getAccount()->getCustomerNumberTextC()
                    ? '??'
                    : $record->getAccount()->getCustomerNumberTextC(),
        ];
    }

    protected function fetchData(): array|stdClass
    {
        /**
         * Récupère tous les contacts qui n'ont pas été supprimés
         * et dont l'email n'est pas vide
         * @var Contact[] $allContacts
         */
        $allContacts = SalesforceFactory::new()->createContactQueryBuilder()
            ->select()
            ->where('IsDeleted', QLOperator::EQUALS, false)
            ->and()
            ->where('Email', QLOperator::DIF, '')
            ->build()
            ->toModel()['records'];

        /**
         * On construit un contact de support pour
         * toutes les organisations qui ont un numéro de client
         *
         * @var Contact[] $organisationSupportContacts
         */
        $organisationSupportContacts = array_map(
            fn(Account $customer) => Contact::create()
                ->setFirstName($customer->getCustomerNumberTextC())
                ->setLastName("support")
                ->setEmail("{$customer->getCustomerNumberTextC()}.support@company.com")
                ->setAccountId($customer->getId())
                ->setAccount($customer),
            SalesforceFactory::new()->createCustomerQueryBuilder()
                ->select()->where('Customer_Number_Text__c', QLOperator::DIF, '')
                ->build()->toModel()['records']
        );

        return [
            ...$allContacts,
            ...$organisationSupportContacts
        ];
    }

    public function filterCsvItem(array $item): array|stdClass
    {
        return !empty($item['org_id']) ? $item : [];
    }
}
