<?php

namespace salesforce_data_collector\collectors;

use Collector;
use stdClass;
use Utils;
use Zorille\framework as core;
use Exception;

abstract class CustomCollector extends Collector
{
    use core\FlagsParser;
    use core\CsvFormatterFromTemplate;

    protected ?ConnexionManager $connexion = null;
    private core\options $list_options;

    protected function getAdditionalUsedOptions(): array
    {
        return [
            'salesforce_serveur' => [
                'aliasEnv' => 'SALESFORCE_SERVEUR',
                'value' => ''
            ],
        ];
    }

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        /** @var ConnexionManager $connexion */
        global $connexion;
        $this->connexion = $connexion;
        $this->list_options = $connexion->getListOptions();

        $this->setOptionsIfExists();
    }

    /**
     * Fait la requête pour récupérer les donnés sur l'API Rest de salesforce
     * grâce à un request builder par objet salesforce.
     *
     * @return array Le tableau d'objets sur lequel on bouclera pour générer le CSV final
     * @throws Exception
     */
    abstract protected function fetchData(): array|stdClass;

    /**
     * @throws Exception
     */
    public function Prepare(): bool
    {
        parent::Prepare();
        static::$records = $this->fetchData();

        return true;
    }

    /**
     * Lancé à chaque itération de la boucle.<br>
     * Ici, lance la fonction de formattage de l'itération courrente pour le CSV final.
     *
     * @return array|false
     */
    public function Fetch()
    {
        if ($this->idx >= count(static::$records))
            return false;

        return $this->formatCsvItem(
            static::$records[$this->idx++]
        );
    }

    public function Collect($iMaxChunkSize = 0)
    {
        $bResult = true;
        Utils::Log(LOG_INFO, get_class($this)." beginning of data collection...");
        try
        {
            $bResult = $this->Prepare();
            if ($bResult)
            {
                $idx = 0;
                $aColumns = [];
                $aHeaders = null;
                while(!is_bool($aRow = $this->Fetch()))
                {
                    if (!empty($aRow)) {
                        if ($aHeaders == null) {
                            // Check that the row names are consistent with the definition of the task
                            $aHeaders = array_keys($aRow);
                        }

                        if (($idx == 0) || (($iMaxChunkSize > 0) && (($idx % $iMaxChunkSize) == 0))) {
                            $this->NextCSVFile();
                            $this->AddHeader($aHeaders);
                        }

                        $this->AddRow($aRow);

                        $idx++;
                    }
                }
                $this->Cleanup();
                Utils::Log(LOG_INFO,  get_class($this)." end of data collection.");
            }
            else
            {
                Utils::Log(LOG_ERR, get_class($this)."::Prepare() returned false");
            }
        }
        catch(Exception $e)
        {
            $bResult = false;
            Utils::Log(LOG_ERR, get_class($this)."::Collect() got an exception: ".$e->getMessage());
        }

        return $bResult;
    }

    protected function getListOptions(): core\options
    {
        return $this->list_options;
    }
}