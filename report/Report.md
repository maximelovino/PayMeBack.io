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

### DBConnection.php

### DataValidator.php

### API fixer.io et page d'accueil



## Marche à suivre pour l'utilisateur

## Déployement

Tout d'abord, sur une machine linux, il faudra installer Apache et MySQL avec la commande:

```
sudo apt-get install apache2 mysql-server php7.0 libapache2-mod-php7.0
```

L'installation du serveur MySQL vous demandera de saisir un mot de passe root pour le serveur MySQL.

Ensuite, il faut se connecter à la base MySQL à l'aide de la commande:

```
mysql -u root -p
```

Et ensuite rentrer le mot de passe root défini précedemment.

Ensuite, depuis le prompt de MySQL, nous allons charger le fichier sql concernant la configuration de la base de données avec la commande suivante:

```
source <path_to_sql_file>;
```

Ensuite, il suffira de copier tous les fichiers contenus dans le dossier `src` dans le dossier racine du serveur apache, c'est-à-dire `/var/www/html`