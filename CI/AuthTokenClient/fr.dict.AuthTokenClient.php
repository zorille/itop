<?php

Dict::Add('FR FR', 'French', 'Français', array(
	'Class:AuthTokenClient' => 'Indicateur BI',
	'Class:AuthTokenClient+' => 'Chaque objet représente un indicateur BI calculé depuis une requête OQL et un champ de GroupBy. Cela rend la liste exportable dans le livre des requêtes',
	'Class:AuthTokenClient/Attribute:name' => 'Nom',
	'Class:AuthTokenClient/Attribute:name+' => 'Nom technique ou fonctionnel de l’indicateur.',
	'Class:AuthTokenClient/Attribute:result' => 'Résaltat affiché',
	'Class:AuthTokenClient/Attribute:result+' => 'Résultat de la requête affiché dans la fiche. Attention : Seul le premier résultat de l\'OQL est traité',
	'Class:AuthTokenClient/Attribute:description' => 'Description',
	'Class:AuthTokenClient/Attribute:description+' => 'Texte d’aide affiché au-dessus du tableau.',
	'Class:AuthTokenClient/Attribute:oql_query' => 'Requête OQL',
	'Class:AuthTokenClient/Attribute:oql_query+' => 'Exemple : SELECT UserRequest WHERE status != "closed"',
	'Class:AuthTokenClient/Attribute:group_by_attcode' => 'Attribut de regroupement',
	'Class:AuthTokenClient/Attribute:group_by_attcode+' => 'Code attribut iTop utilisé pour regrouper les objets, par exemple status ou org_id.',
	'Menu:AuthTokenClient' => 'Client Auth Token',
	'Menu:AuthTokenClient+' => 'Configure Auth connexion based on Token.',
));
