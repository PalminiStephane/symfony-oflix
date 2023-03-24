# ORM : quelles différences entre le Active Record pattern et le Data Mapper pattern?

Article posté le 05-06-2015 dans la catégorie Développement
Article mis à jour le : 05-05-2022

Rapide explication de la différence entre les deux types de pratiques des ORM.
La manipulation des données stockées par une application utilisant un architecture-pattern de type MVC entraine souvent l'utilisation d'un ORM pour réaliser cette tâche. Par exemple j'utilise personnellement Doctrine que ce soit avec Zend Framework 2 ou Symfony 2. Il en existe d'autres, comme Propel ou Eloquent.

Il existe néanmoins une différence de paradigme  parmi les ORM. Et celle-ci "déchire" parfois les utilisateurs d'ORM... Il s'agit de deux patrons (patterns) :

Le Active Record pattern
Le Data Mapper pattern (parfois aussi appelé Repository Pattern)

## Active Record pattern

Dans un article précédent, voici ce que j'avais écrit à propos de ce pattern :

...utilisé par certains ORM, on le rencontre quand une classe implémente une interface avec les signatures des méthodes pour l'accès (sauvegarde, suppression) à la base de données de manière opaque. L'utilisateur n'a pas à se soucier comment les données sont gérées en BDD, il a juste à se soucier des données...
Ce procédé consiste à ce que chaque entité manipulée ait accès à la base de données via une classe dont elle hérite. Cette classe est capable de savoir à quelle colonne d'une table correspond tel ou tel champ de l'objet manipulé, juste en "regardant" le schéma de la table.

Ce procédé présente notamment un avantage, il est intuitif et simple à utiliser.

Par exemple, pour la création d'un utilisateur :


```php
$Utilisateur = new Utilisateur();
$Utilisateur->setNom("Eric");
$Utilisateur->save();
```

Et c'est tout...

## Data Mapper pattern

Le Data Mapper pattern lui est complètement détaché de la couche d'abstraction. En d'autres termes : vos objets n'ont aucune connaissance sur la base de données!

Pour manipuler vos objets, vous utiliserez un Entity Manager, qui présentera toutes les méthodes nécessaires (lecture, mise à jour, sauvegarde, effacer), sur la base d'un modèle CRUD, et bien plus encore. Il gèrera également toutes les données avancées comme les transactions par exemple.

```php
$Utilisateur = new Utilisateur();
$Utilisateur->setNom("Eric");
$EntityManager->save($Utilisateur);
```

Pour fonctionner, votre Entity Manager utilisera souvent un fichier de mapping, par exemple type XML. Ce dernier listera tous les champs d'une table, ses particularités, les clefs étrangères...

## Quel est le meilleur?

Aucune idée... Comme je l'ai dis plus haut, c'est un sujet de discussions engagées parfois...

A titre personnel, je n'utilise que Doctrine et son Entity Manager, donc un Data Mapper pattern.
