<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes;

use ArchivedObjectException;
use CoreException;
use DBObject;
use FloorPlanBuilder\webservice\models\RackStatus;

class Racks extends FromLocation
{
    protected string $class = 'Rack';
    protected array $additionalFields = ['width', 'length', 'status', 'name', 'customer_name'];

    /**
     * @throws CoreException
     * @throws ArchivedObjectException
     */
    protected function format(DBObject $object): array
    {
        $jsondata = json_decode($object->Get('jsondata'));
        return [
            'id' => $object->GetKey(),
            'name' => $object->GetName(),
            'status' => match ($object->Get('status')) {
                'production' => match($object->Get('customer_id')) {
                    null, '', 0 => RackStatus::IN_PRODUCTION_WITHOUT_CUSTOMER->value,
                    default => RackStatus::IN_PRODUCTION_WITH_CUSTOMER->value,
                },
                'reserve' => match($object->Get('customer_id')) {
                    null, '', 0 => RackStatus::OTHER->value,
                    default => RackStatus::RESERVED_WITH_CUSTOMER->value,
                },
                default => RackStatus::OTHER->value,
            },
            'roomId' => $object->Get('location_id'),
            'width' => $object->Get('width'),
            'height' => $object->Get('length'),
            'x' => $jsondata->x ?? null,
            'y' => $jsondata->y ?? null,
            'rotation' => $jsondata->rotation ?? null,
            'podId' => $jsondata->podId ?? null,
        ];
    }
}