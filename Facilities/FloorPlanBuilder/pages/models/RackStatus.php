<?php

namespace FloorPlanBuilder\webservice\models;

enum RackStatus: string
{
    case RESERVED_WITH_CUSTOMER = 'reserved_with_customer';
    case IN_PRODUCTION_WITH_CUSTOMER = 'in_production_with_customer';
    case IN_PRODUCTION_WITHOUT_CUSTOMER = 'in_production_without_customer';
    case OTHER = 'other';
}
