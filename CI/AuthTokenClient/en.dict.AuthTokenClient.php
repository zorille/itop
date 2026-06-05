<?php

Dict::Add('EN US', 'English', 'English', array(
	'Class:AuthTokenClient' => 'BI Indicator',
	'Class:AuthTokenClient+' => 'Each object displays an indicator based on OQL query and groupby field. These data are exportable with query phrase book.',
	'Class:AuthTokenClient/Attribute:name' => 'Name',
	'Class:AuthTokenClient/Attribute:name+' => 'Technical or functional indicator name.',
	'Class:AuthTokenClient/Attribute:result' => 'Displayed result',
	'Class:AuthTokenClient/Attribute:result+' => 'Calculated result displayed in the object view.',
	'Class:AuthTokenClient/Attribute:description' => 'Description',
	'Class:AuthTokenClient/Attribute:description+' => 'Help text displayed above the table.',
	'Class:AuthTokenClient/Attribute:oql_query' => 'OQL query',
	'Class:AuthTokenClient/Attribute:oql_query+' => 'Example: SELECT UserRequest WHERE status != "closed"',
	'Class:AuthTokenClient/Attribute:group_by_attcode' => 'Group by attribute',
	'Class:AuthTokenClient/Attribute:group_by_attcode+' => 'iTop attribute code used to group objects, for example status or org_id.',
	'Menu:AuthTokenClient' => 'Auth Token Client',
	'Menu:AuthTokenClient+' => 'Configure Auth connexion based on Token.',
));
