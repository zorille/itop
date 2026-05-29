<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes;

use CoreException;
use DBObject;
use DBObjectSet;
use DBSearch;
use OQLException;

abstract class FromLocation
{
    protected string $class;
    protected array $additionalFields = [];

    abstract protected function format(DBObject $object): array;

    /**
     * @throws CoreException
     * @throws OQLException
     */
    public function getFromLocation(int $id, bool $format = true): array
    {
        $oSearch = DBSearch::FromOQL("SELECT {$this->class} WHERE location_id = :location_id");
        $oSet = new DBObjectSet($oSearch, [], ['location_id' => $id]);
        $oSet->OptimizeColumnLoad([$this->class => ['location_id', 'jsondata', ...$this->additionalFields]]);

        $objects = [];
        while ($object = $oSet->Fetch()) {
            if ($format) {
                $objects[] = $this->format($object);
            }
            else {
                $objects[] = $object;
            }
        }

        return $objects;
    }
}