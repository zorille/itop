<?php

Dict::Add('FR FR', 'French', 'Français', array(
	'Class:OQLGroupByIndicator' => 'Indicateur BI',
	'Class:OQLGroupByIndicator+' => 'Chaque objet représente un indicateur BI calculé depuis une requête OQL et un champ de GroupBy. Cela rend la liste exportable dans le livre des requêtes',
	'Class:OQLGroupByIndicator/Attribute:name' => 'Nom',
	'Class:OQLGroupByIndicator/Attribute:name+' => 'Nom technique ou fonctionnel de l’indicateur.',
	'Class:OQLGroupByIndicator/Attribute:result' => 'Résaltat affiché',
	'Class:OQLGroupByIndicator/Attribute:result+' => 'Résultat de la requête affiché dans la fiche. Attention : Seul le premier résultat de l\'OQL est traité',
	'Class:OQLGroupByIndicator/Attribute:description' => 'Description',
	'Class:OQLGroupByIndicator/Attribute:description+' => 'Texte d’aide affiché au-dessus du tableau.',
	'Class:OQLGroupByIndicator/Attribute:oql_query' => 'Requête OQL',
	'Class:OQLGroupByIndicator/Attribute:oql_query+' => 'Exemple : SELECT UserRequest WHERE status != "closed"',
	'Class:OQLGroupByIndicator/Attribute:group_by_attcode' => 'Attribut de regroupement',
	'Class:OQLGroupByIndicator/Attribute:group_by_attcode+' => 'Code attribut iTop utilisé pour regrouper les objets, par exemple status ou org_id.',
	'Menu:OQLGroupByIndicator' => 'Indicateurs BI',
	'Menu:OQLGroupByIndicator+' => 'Configurer les indicateurs BI via OQL.',
	'Class:OQLGroupByIndicator/Error:EmptyOQL' => 'Requête OQL vide.',
	'Class:OQLGroupByIndicator/Error:EmptyGroupBy' => 'Attribut de regroupement vide.',
	'Class:OQLGroupByIndicator/Error:InvalidAttribute' => 'Attribut "%1$s" invalide pour la classe %2$s.',
));
