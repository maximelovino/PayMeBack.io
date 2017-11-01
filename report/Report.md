# PayMeBack.io - Rapport

## Introduction

## Structure de la base de données

![Database schema](../db/database.png)

### t_users

Cette table contiendra donc les informations sur les utilisateurs, y compris le hash de leur mot de passe pour l'authentification.

### t_events

Cette table contient la liste des événements, la colonne `event_id` est l'identifiant de l'événement qui s'auto-incrémente à chaque insertion.

### t_currencies

Cette table définit une liste de monnaies disponibles lors de la création d'événements. Elle regroupe également des infos sur comment sont arrondies les différentes monnaies.

### t_group_membership

Cette table sert à définir l'appartenance d'un utilisateur à un événement ainsi que son coefficient dans cet événement.

### t_expenses

Cette table contient la liste des dépenses, avec une référence à l'ID de l'événement en question.

### t_expense_membership

Cette table définit les utilisateurs concernés par une dépense.

### t_reimbursement

Cette table contient les remboursements (ou paiements directs) d'un utilisateur à un autre.

### t_coeffsbytransaction

Cette vue a été créé pour pouvoir accéder facilement à la somme des coefficients pour chaque dépense, étant donné que le `SELECT` correspondants est assez long, du coup une vue a été faite pour directement accéder aux informations.

## Structure du projet

Etant donné l'envergure assez restreinte de ce projet, j'ai décidé de ne pas faire de MVC ni spécialement d'utiliser l'orienté objet de PHP pour les données à traiter. Je n'ai également pas utilisé de frameworks ou de générateurs de code et ai tout fait "from scratch" à l'exception du CSS qui vient de Bootstrap.

Deux classes ont été réalisées uniquement.

### DBConnection.php

Il s'agit ici d'une classe suivant le pattern singleton et fournissant une instance de connexion à notre base de données sur laquelle on pourra appeler des fonctions pour récupérer des informations à partir des différentes tables de la base. Cette classe contient toutes les interactions de récupération de données depuis la BDD.

### DataValidator.php

Il s'agit ici d'une classe non instantiable contenant uniquement des fonctions statiques servant à la validation des données à la réception des formulaires. Cette classe contient donc des fonction qui vérifient la validité des données saisies à l'aide de RegEx ainsi que leur logique en interrogeant la BDD, par exemple pour vérifier qu'un utilisateur a accès à un certain événement.

### API fixer.io et page d'accueil

//TODO

## Marche à suivre pour l'utilisateur

## Déploiement

Tout d'abord, sur une machine linux, il faudra installer Apache et MySQL avec la commande:

```
sudo apt-get install apache2 mysql-server php7.0 libapache2-mod-php7.0 php7.0-mysql
```

L'installation du serveur MySQL vous demandera de saisir un mot de passe root pour le serveur MySQL.

Ensuite, il faut se connecter à la base MySQL à l'aide de la commande:

```
mysql -u root -p
```

Et ensuite rentrer le mot de passe root défini précedemment.

Ensuite, depuis le prompt de MySQL, nous allons charger le fichier sql (le fichier sql est `db/sql.db`) concernant la configuration de la base de données avec la commande suivante:

```
source <path_to_sql_file>;
```

Ensuite, il suffira de copier tous les fichiers contenus dans le dossier `src` dans le dossier racine du serveur apache, c'est-à-dire `/var/www/html`