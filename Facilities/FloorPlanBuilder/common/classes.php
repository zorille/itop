<?php
require_once dirname(__FILE__).'/classes/Locations.php';
require_once dirname(__FILE__).'/classes/FromLocation.php';
require_once dirname(__FILE__).'/classes/Racks.php';
require_once dirname(__FILE__).'/classes/Footprints.php';
require_once dirname(__FILE__).'/classes/CircuitElecs.php';
require_once dirname(__FILE__).'/classes/FloorPlanBuilder.php';
require_once dirname(__FILE__).'/classes/Response.php';

require_once dirname(__FILE__).'/classes/attributes/FromItopParam.php';
require_once dirname(__FILE__).'/classes/attributes/FromRequestBody.php';
require_once dirname(__FILE__).'/classes/attributes/ListItem.php';
require_once dirname(__FILE__).'/classes/attributes/Instance.php';

require_once dirname(__FILE__).'/classes/webservice/Ws.php';

require_once dirname(__FILE__).'/../pages/models/BuilderBody.php';
require_once dirname(__FILE__).'/../pages/models/Circuit.php';
require_once dirname(__FILE__).'/../pages/models/Footprint.php';
require_once dirname(__FILE__).'/../pages/models/Layer.php';
require_once dirname(__FILE__).'/../pages/models/Pod.php';
require_once dirname(__FILE__).'/../pages/models/Point.php';
require_once dirname(__FILE__).'/../pages/models/Rack.php';
require_once dirname(__FILE__).'/../pages/models/RackStatus.php';