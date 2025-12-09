<?php

namespace salesforce_data_collector\collectors\data;

use Exception;
use Zorille\framework as core;
use Zorille\salesforce\data_models\Product2;
use Zorille\framework\QueryBuilderOperator as QLOperator;
use Zorille\salesforce\SalesforceFactory;

class ProductData
{
    private array $selectedFields = [
        'Id', 'Name',
        'ProductCode', 'Family',
        'IsActive'
    ];

    /** @var Product2[] $products */
    private array $products = [];

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly core\options $optionList
    )
    {
        $f = SalesforceFactory::new()->createOpportunityProductQueryBuilder();

        $this->setProducts($f
            ->select(...$this->selectedFields)
            ->where('ProductCode', QLOperator::DIF, '')->and()
            ->where('IsActive', QLOperator::EQUALS, true)
            ->build()->toModel()['records']);
    }

    public function getSalesforceServeurOption(): mixed
    {
        return $this->optionList->getOption('salesforce_serveur');
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    private function setProducts(array $products): void
    {
        $this->products = $products;
    }

}