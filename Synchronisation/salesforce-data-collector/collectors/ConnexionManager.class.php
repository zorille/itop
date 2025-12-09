<?php

namespace salesforce_data_collector\collectors;

require_once APPROOT . 'core/utils.class.inc.php';

use Exception;
use stdClass;
use Zorille\framework\options;
use Utils;

class ConnexionManager
{
    private array $argv = [];
    protected ?options $listOptions = null;

    private static array $billingParts = [];

    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    /**
     * @throws Exception
     */
    public function LoadData(): void
    {
        $rep_document = '/TOOLS';
        $argv = [
            ...($this->argv ?? ["fichier"]),
            "--conf",
            APPROOT . 'conf/prod_salesforce_serveurs.xml',
            APPROOT . 'conf/prod_utilisateurs.xml',
        ];

        if (!empty(Utils::GetConfigurationValue('flags'))) {
            foreach (Utils::GetConfigurationValue('flags') as $flag_name => $flag_value) {
                $argv[] = "--{$flag_name}";
                $argv[] = $flag_value;
            }
        }

        if (Utils::ReadParameter('console_log_level', 0) == LOG_NOTICE) {
            $argv[] = '--verbose';
            $argv[] = '1';
        }
        elseif (Utils::ReadParameter('console_log_level', 0) == LOG_DEBUG) {
            $argv[] = '--verbose';
            $argv[] = '2';
        }
        elseif (Utils::ReadParameter('console_log_level', 0) == LOG_USER) {
            $argv[] = '--verbose';
            $argv[] = '3';
        }

        $argc = count($argv);

        require_once $rep_document . '/php_framework/config.php';
        /** @var options $liste_option */
        $this->setListOptions($liste_option);
    }

    public function getData(): DataFactory
    {
        return new DataFactory($this->getListOptions());
    }

    public function setListOptions(options $listOptions): self
    {
        $this->listOptions = $listOptions;

        return $this;
    }

    public function getListOptions(): options
    {
        return $this->listOptions;
    }

    public function getBillingParts(?string $customerCode = null, ?string $billingCode = null): array|stdClass
    {
        if (preg_match(
            "/^(?<location>[A-Z]{2}\.[A-Z0-9]{4,5})\.(?<productcode>.*\..*)\.(?<unicity_code>[0-9]*)$/",
            $billingCode,
            $matches
        )) {
            static::$billingParts[$customerCode][$matches['productcode']] = $matches;
        }
        return $matches;
    }

    public function getBillingPartsArr(): array|stdClass
    {
        return self::$billingParts;
    }
}