<?php

use salesforce_data_collector\collectors\CustomCollector;
use Zorille\salesforce\data_models\Product2;
use Zorille\salesforce\data_models\ProductFamily;

/**
 * @property string $salesforceServeurOption
 *
 * @method string getSalesforceServeurOption()
 */
class SFDCProductFamiliesCollector extends CustomCollector
{
    /**
     * Renvoie un template servant à la génération du CSV.
     *
     * @return string[]
     */
    protected function getOutputCsvItemTemplate(): array
    {
        return [
            'primary_key'   => 'Name',
            'name'          => 'Name',
        ];
    }

    /**
     * Pour m'assurer de l'unicité de chaque Family,
     * Je récuère d'abbord les Families puis je map dessus pour
     * faire de chaque ligne un tableau qui as comme clé le nom de la Family.
     * Je merge ensuite toutes les lignes ce qui aura pour effet que si une
     * clé existe déjà, elle sera ecrasée.
     * Je récuère ensuite les valeurs puisque nous avons besoin d'un tableau indexé.
     *
     * @return ProductFamily[]
     * @throws Exception
     */
    protected function fetchData(): array|stdClass
    {
        return array_values(
            array_merge(
                ...array_map(
                    fn (Product2 $product) => [$product->getFamily() => ProductFamily::create()
                        ->setName($product->getFamily())],
                    $this->connexion->getData()->getProductData()->getProducts()
                )
            )
        );
    }
}
