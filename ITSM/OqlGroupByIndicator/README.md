# OQL GroupBy Indicator

Extension iTop permettant de créer des objets `OQLGroupByIndicator` qui affichent dans leur fiche un tableau de regroupement calculé depuis une requête OQL.

## Principe

L’OQL retourne des objets. L’extension parcourt le `DBObjectSet`, regroupe les objets côté PHP sur l’attribut configuré, puis affiche un tableau dans `OnDisplayProperties()`.

Aucun résultat n’est persisté en base.

## Champs

- `name` : nom de l’indicateur.
- `title` : titre affiché au-dessus du tableau.
- `description` : description affichée au-dessus du tableau.
- `oql_query` : requête OQL source.
- `group_by_attcode` : code attribut utilisé pour le regroupement.
- `display_mode` : `table`, `table_percent`, `table_bar`.
- `sort_mode` : tri par libellé ou par nombre.
- `max_rows` : limite d’affichage. `0` signifie aucune limite.
- `show_empty_value` : inclure ou non les valeurs vides.
- `empty_value_label` : libellé des valeurs vides.
- `show_total` : afficher le total.

## Exemple

```text
oql_query: SELECT UserRequest WHERE status != "closed"
group_by_attcode: status
display_mode: table_bar
sort_mode: count_desc
```

## Installation

1. Copier le dossier `oql-groupby-indicator` dans le dossier `extensions/` de l’instance iTop.
2. Relancer le setup iTop en mode mise à jour.
3. Sélectionner l’extension `OQL GroupBy Indicator`.
4. Créer un objet `OQLGroupByIndicator` depuis le menu d’administration généré.

## Limites connues

- Le regroupement est fait côté PHP, pas par un `GROUP BY` OQL natif.
- Une requête OQL très large peut être coûteuse : filtrer autant que possible dans `oql_query`.
- L’extension cible iTop 3.x. Pour iTop 2.7, déplacer les dictionnaires à la racine et ajuster `module.oql-groupby-indicator.php` si nécessaire.
