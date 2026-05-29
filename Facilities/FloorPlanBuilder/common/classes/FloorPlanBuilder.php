<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes;

use ArchivedObjectException;
use CoreException;
use DBObject;
use OQLException;

class FloorPlanBuilder extends FromLocation
{
    protected string $class = 'FloorPlanBuilder';
    protected array $additionalFields = ['location_name'];

    /**
     * @throws CoreException
     * @throws ArchivedObjectException
     */
    protected function format(DBObject $object): array
    {
        return [
            'id' => $object->GetKey(),
            'name' => $object->Get('location_name'),
            'jsondata' => json_decode($object->Get('jsondata'))
        ];
    }

    /**
     * @param string $defaultName
     * @return array[]
     * @throws CoreException
     * @throws OQLException
     */
    public function getFromLocation(int $id, mixed $defaultName = ''): array
    {
        $r = json_decode(json_encode(parent::getFromLocation($id)), true);

        if (count($r) === 0) {
            return [
                [
                    'id' => 1,
                    'location_id' => $id,
                    'name' => $defaultName,
                    'jsondata' => null
                ]
            ];
        }
        return $r;
    }
}