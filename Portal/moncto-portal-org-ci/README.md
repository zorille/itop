# moncto-portal-org-ci

Extension iTop 3.2 Enhanced Portal pour ajouter dans le portail utilisateur :

- le champ **CI impactés** (`functionalcis_list`) dans le formulaire de création de ticket ;
- une tuile **Équipements de mon organisation** affichant les CI de l'organisation du contact connecté ;
- des scopes portail limitant la visibilité aux CI dont `org_id` correspond à l'organisation du contact courant.

## Filtre livré

```sql
SELECT FunctionalCI WHERE org_id = :current_contact->org_id
```

Ce filtre est volontairement uniquement basé sur l'organisation, sans jointure avec `lnkPersonToCI`.

## Installation

1. Copier le dossier `moncto-portal-org-ci` dans le répertoire `extensions/` de l'instance iTop.
2. Relancer le setup iTop depuis l'interface web ou en CLI selon votre procédure habituelle.
3. Installer l'extension **Enhanced Portal - CI par organisation sur les tickets**.
4. Vider le cache applicatif si nécessaire.

## Ce que l'extension modifie

- Ajoute un scope portail sur `FunctionalCI` pour les utilisateurs portail.
- Ajoute/autorise la relation `lnkFunctionalCIToTicket` pour associer les CI au ticket.
- Insère `functionalcis_list` dans les formulaires `ticket-create` et `incident-create` s'ils existent.
- Ajoute une brique `organization-cis` dans le portail.

## Point d'attention

Les formulaires `ticket-create` et `incident-create` sont redéfinis avec `_delta="force"` pour maîtriser l'emplacement de `functionalcis_list`.
Si votre portail a déjà une personnalisation de ces formulaires, fusionnez manuellement cette ligne :

```xml
<div class="form_field" data-field-id="functionalcis_list" data-field-opened="true"/>
```

## Test rapide

- Connectez-vous au portail avec un utilisateur dont le contact a une organisation renseignée.
- Ouvrez **Équipements de mon organisation** : les CI de cette organisation doivent apparaître.
- Créez un ticket : le bloc **CI impactés** doit permettre d'ajouter un ou plusieurs CI de la même organisation.
