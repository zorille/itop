<?php

Dict::Add('EN US', 'English', 'English', array(
	'Class:OQLGroupByIndicator' => 'BI Indicator',
	'Class:OQLGroupByIndicator+' => 'Each object displays an indicator based on OQL query and groupby field. These data are exportable with query phrase book.',
	'Class:OQLGroupByIndicator/Attribute:name' => 'Name',
	'Class:OQLGroupByIndicator/Attribute:name+' => 'Technical or functional indicator name.',
	'Class:OQLGroupByIndicator/Attribute:result' => 'Displayed result',
	'Class:OQLGroupByIndicator/Attribute:result+' => 'Calculated result displayed in the object view.',
	'Class:OQLGroupByIndicator/Attribute:description' => 'Description',
	'Class:OQLGroupByIndicator/Attribute:description+' => 'Help text displayed above the table.',
	'Class:OQLGroupByIndicator/Attribute:oql_query' => 'OQL query',
	'Class:OQLGroupByIndicator/Attribute:oql_query+' => 'Example: SELECT UserRequest WHERE status != "closed"',
	'Class:OQLGroupByIndicator/Attribute:group_by_attcode' => 'Group by attribute',
	'Class:OQLGroupByIndicator/Attribute:group_by_attcode+' => 'iTop attribute code used to group objects, for example status or org_id.',
	'Menu:OQLGroupByIndicator' => 'BI Indicators',
	'Menu:OQLGroupByIndicator+' => 'Configure OQL BI indicators.',
	'Class:OQLGroupByIndicator/Error:EmptyOQL' => 'Empty OQL query.',
	'Class:OQLGroupByIndicator/Error:EmptyGroupBy' => 'Empty group by attribute.',
	'Class:OQLGroupByIndicator/Error:InvalidAttribute' => 'Invalid attribute "%1$s" for class %2$s.',
));
