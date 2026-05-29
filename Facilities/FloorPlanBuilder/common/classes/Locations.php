<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes;

use ArchivedObjectException;
use CoreException;
use Location;
use MetaModel;

class Locations
{
    /**
     * @throws CoreException
     * @throws ArchivedObjectException
     */
    public function getFromId(int $id): Location|null
    {
        return MetaModel::GetObject('Location', $id, false);
    }
}