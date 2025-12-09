<?php

namespace salesforce_data_collector\collectors\data;

use Exception;
use Zorille\framework\QueryBuilderLikeOperatorType;
use Zorille\salesforce\data_models\Location;
use Zorille\salesforce\query_builder as query;
use Zorille\framework as core;
use Zorille\framework\QueryBuilderOperator as QLOperator;
use Zorille\salesforce\SalesforceFactory;

class AssetData
{
    private array $datacenters = [];
    private array $assets = [];
    private array $assetsToDecomissoning = [];

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly core\options $optionList
    )
    {
        $this->datacenters = array_filter(
            array_map(
                fn(Location $l) => $l->getName(),
                SalesforceFactory::new()->createLocationQueryBuilder()
                    ->select()
                    ->where('Name', ...query::like(QueryBuilderLikeOperatorType::CONTAINS, '.'))
                    ->build()->toModel()['records']
            ),
            fn(string $l) => !!preg_match(
                "/^[a-zA-Z]{2}\.[a-zA-Z]{3}[0-9]{1,3}$/",
                $l
            )
        );

        $f = SalesforceFactory::new();

        $q = $f->createAssetQueryBuilder()->select()
            ->where('Status', QLOperator::EQUALS, 'Purchased')->and()
            ->where('Customer_Number__c', QLOperator::DIF, '')->and();

        foreach ($this->datacenters as $i => $d) {
            $q->where('Billing_Code__c', ...query::like(QueryBuilderLikeOperatorType::PATTERN, "{$d}.%.%.%"));

            if ($i < count($this->datacenters) - 1) {
                $q->or();
            }
        }

        $this->setAssets($q->build()->toModel()['records']);

        $q = $f->createAssetQueryBuilder()->select()
            ->where('Status', QLOperator::EQUALS, 'Awaiting Termination')->and()
            ->where('Customer_Number__c', QLOperator::DIF, '')->and();

        foreach ($this->datacenters as $i => $d) {
            $q->where('Billing_Code__c', ...query::like(QueryBuilderLikeOperatorType::PATTERN, "{$d}.%.%.%"));

            if ($i < count($this->datacenters) - 1) {
                $q->or();
            }
        }

        $this->setAssetsToDecomissoning($q->build()->toModel()['records']);
    }

    public function getSalesforceServeurOption(): mixed
    {
        return $this->optionList->getOption('salesforce_serveur');
    }

    public function getAssets(): array
    {
        return $this->assets;
    }

    private function setAssets(array $assets): void
    {
        $this->assets = $assets;
    }

    public function getAssetsToDecomissoning(): array
    {
        return $this->assetsToDecomissoning;
    }

    private function setAssetsToDecomissoning(array $assetsToDecomissoning): void
    {
        $this->assetsToDecomissoning = $assetsToDecomissoning;
    }
}