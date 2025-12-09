<?php

namespace salesforce_data_collector\collectors;

use Exception;
use salesforce_data_collector\collectors\data\AssetData;
use salesforce_data_collector\collectors\data\ProductData;
use Zorille\framework as core;

class DataFactory extends core\SingletonFactory
{
    public function __construct(
        private readonly core\options $options
    )
    {}

    /**
     * @throws Exception
     */
    public function getAssetData(): AssetData
    {
        return $this->setIfNotExists('asset', new AssetData($this->options))
            ->getSingleton('asset');
    }

    /**
     * @throws Exception
     */
    public function getProductData(): ProductData
    {
        return $this->setIfNotExists('product', new ProductData($this->options))
            ->getSingleton('product');
    }
}