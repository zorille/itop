<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes;

use ArchivedObjectException;
use CoreException;
use DBObject;

class Footprints extends FromLocation
{
    protected string $class = 'FootPrint';
    protected array $additionalFields = ['width', 'length', 'status', 'name'];

    /**
     * @throws ArchivedObjectException
     * @throws CoreException
     */
    protected function format(DBObject $object): array
    {
        $jsondata = json_decode($object->Get('jsondata'));
        return [
            'id' => $object->GetKey(),
            'name' => $object->GetName(),
            'status' => $object->Get('status'),
            'width' => $object->Get('width'),
            'height' => $object->Get('length'),
            'color' => $jsondata->color,
            'units' => $jsondata->units,
        ];
    }
}