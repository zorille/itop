<?php

use salesforce_data_collector\collectors\CustomCollector;
use Zorille\itop\data_models\CrmAsset;
use Zorille\salesforce\data_models\Asset;

/**
 * @property string $salesforceServeurOption
 *
 * @method string getSalesforceServeurOption()
 */
class SFDCAssetsCollector extends CustomCollector
{
    /**
     * Modifie la valeur du status en fonction de sa valeur actuelle.
     * @param Zorille\salesforce\data_models\Asset $asset
     * @return string
     */
    protected function getStatus(Asset $asset):string
    {
        return match ($asset->getStatus()) {
            'Awaiting Termination' => CrmAsset::STATUS_DECOMMISSIONING,
            default => CrmAsset::STATUS_IMPLEMENTATION,
        };
    }
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
            'name' => [$this, 'getAssetName'],
            'org_id' => 'Customer_Number__c',
            'business_criticity' => 'medium',
            'needmonitoring' => 'no',
            'billing_code' => 'Billing_Code__c',
            'status' => [$this, 'getStatus'],
            'crm_reference' => 'Id',
            'location_id' => [$this, 'getLocationId']
        ];
    }

    public function getAssetName(Asset $asset): string
    {
        return ($title = empty($asset->getDescription())
            ? $asset->getName() : $asset->getDescription()) && strlen($title) > 250
            ? substr($title, 0, 250).' ...' : $title;
    }

    public function getLocationId(Asset $asset)
    {
        return $this->connexion->getBillingParts(
            $asset->getCustomerNumberC(),
            $asset->getBillingCodeC()
        )['location'] ?? '';
    }

    /**
     * @param Asset[] $input
     * @return Asset[]
     */
    private function removeDuplicates(array $input): array
    {
        return array_reduce($input, fn (array $r, Asset $asset) => in_array(
            "{$this->getAssetName($asset)};{$asset->getId()};{$asset->getCustomerNumberC()};{$asset->getBillingCodeC()}",
            array_map(
                fn(Asset $asset) => "{$this->getAssetName($asset)};{$asset->getId()};{$asset->getCustomerNumberC()};{$asset->getBillingCodeC()}",
                $r
            )
        ) ? $r : [...$r, $asset], []);
    }

    /**
     * @return Asset[]
     * @throws Exception
     * Récupère les données de l'API Salesforce
     */
    public function fetchData(): array|stdClass
    {
        return array_merge(
            $this->removeDuplicates($this->connexion->getData()->getAssetData()->getAssetsToDecomissoning()),
            $this->removeDuplicates($this->connexion->getData()->getAssetData()->getAssets())
        );
    }

    public function filterCsvItem(array $item): array|stdClass
    {
        return empty($item['name']) ? [] : $item;
    }
}