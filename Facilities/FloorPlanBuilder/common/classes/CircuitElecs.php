<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes;

use ArchivedObjectException;
use CoreException;
use DBObject;

class CircuitElecs extends FromLocation
{
    protected string $class = 'CircuitElec';
    protected array $additionalFields = ['status', 'name'];

    /**
     * @throws CoreException
     * @throws ArchivedObjectException
     */
    protected function format(DBObject $object): array
    {
        return [
            'id' => $object->GetKey(),
            'name' => $object->GetName(),
            'status' => $object->Get('status'),
            'jsondata' => json_decode($object->Get('jsondata'))
        ];
    }
}