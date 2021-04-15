# ECF Banque
[![CircleCI](https://circleci.com/gh/trouvainGregoire/studi-ecf-bank.svg?style=svg)](https://github.com/trouvainGregoire/studi-ecf-bank)

## Description :
Réaliser une application qui simule un portail de banque en ligne permettant d’effectuer des opérations de création de compte client, validation de la création par un banquier, consultation de compte bancaire, réalisation de virement en lien avec une BDD.

Sommaire
=========

<!--ts-->
* [Détails du projet](#details-du-projet)
* [Guide d'utilisation](#guide-dutilisation)
* [Déploiement](#deploiement)
* [Questions et réflexions](#questions-et-reflexions)
<!--te-->

Détails du projet
=========

### Objectifs :
L’objectif du projet est de mener une étude et développer l’application présentée ci-dessous. Il convient également d’élaborer un dossier d’architecture logiciel qui documente entre autres les choix des technologies, les choix d’architecture logicielle et de configuration, les bonnes pratiques de sécurité implémentées, etc.

Il est également demandé d’élaborer un document spécifique sur les mesures et bonnes pratiques de sécurité mises en place ainsi que la justification de chacune d’entre elles. Les bases de données et tout autre composant nécessaire pour faire fonctionner le projet sont également accompagnés d’un manuel de configuration et d’utilisation.
### Exigences :
Réaliser une application qui simule un portail de banque en ligne permettant d’effectuer des opérations de création de compte client, validation de la création par un banquier, consultation de compte bancaire, réalisation de virement en lien avec une BDD.

#### Profils utilisateurs :
##### Client : 
* Soumet une demande de création de compte pour les nouveaux clients. La création de compte s’effectue à travers un formulaire avec upload d’une pièce d’identité du client. Une fois le compte validé, le client sera en mesure d’effectuer des virements et consulter son compte. Il peut également soumettre une demande de suppression de son compte à travers un formulaire avec upload d’une demande signée. Pensez à sécuriser vos formulaires.

##### Banquier :
* Valide les demandes de création et suppression de compte, d’ajout de bénéficiaires suite à la consultation des pièces jointes soumises par les clients

### Descriptions des fonctionnalités :
#### Menu non authentifié : 
La première interface du site contient une présentation de la banque ainsi que trois liens pour accéder au menu d’authentification, le menu création de compte et la console d’administration. Cette console est visible que si l’utilisateur est authentifié.

#### Fonctionnalités (à gérer dans l’interface d’administration) :
* La fonctionnalité de création de compte permet aux utilisateurs de remplir une demande d’ouverture de compte en fournissant les informations suivantes : nom, prénom, date de naissance, adresse postale, adresse mail, mot de passe, et uploader un scan de pièce jointe.
* La demande de création est attribuée automatiquement à un des 5 banquiers (prédéfinis dans la base de données), en fonction du nombre de demande à la charge de chacun d’entre eux.
* La fonctionnalité d’authentification permet aux utilisateurs de se connecter sur leurs portails afin de suivre l’état de la demande de création.
* La fonctionnalité d’authentification permet aux 5 banquiers (dont les comptes sont également prédéfinis), de se connecter et de valider les demandes de création envoyées par les clients.

#### Menu authentifié client :
* Si le compte n’est pas encore validé, l’utilisateur ne peut visualiser sur son portail que l’état d’avancement de la demande (en attende de validation). Les autres options (ajout d’un bénéficiaire, virement et visualisation de compte et demande de suppression de compte) doivent être inaccessibles.
* Une fois le compte validé, un identifiant de compte bancaire est automatiquement généré (le développeur est libre de choisir le format que l’ensemble des identifiants doivent suivre). Le client est alors capable d’ajouter des bénéficiaires, effectuer des virements, visualiser son compte et demander une suppression de compte. L’ajout de bénéficiaire doit aussi être validé par un banquier.

#### Menu authentifié banquier :
Il permet aux 5 comptes banquiers créés sur la base, de valider les demandes de création de compte, d’ajouter des bénéficiaires et de supprimer des comptes.

Guide d'utilisation
=========
Le guide d'utilisation ce trouve dans le dossier pdf. 
Vous pouvez également cliquer sur ce [lien](https://github.com/trouvainGregoire/studi-ecf-bank/blob/master/pdf/Manuel%20d'utilisation%20-%20ECF%20Banque.pdf)

Déploiement
=========
Afin de déployer le projet sur Heroku. Il est important d'avoir créer un compte sur celui-ci et également avoir créer un compte sur aws.

Il faut avoir créer un bucket s3 ainsi qu'avoir récupéré :
* La clé du bucket s3
* La clé secret du bucket s3
* Le nom du bucket s3

[Documentation aws bucket s3](https://docs.aws.amazon.com/fr_fr/AmazonS3/latest/dev/UsingBucket.html)

####Déploiement sur Heroku

* Créer une nouvelle aplication avec la cli
  * ````heroku create````
* Configurer les variables d'environnement
  * ```heroku config:set APP_ENV=prod```
  * ```heroku config:set AWS_KEY=votreKeyS3```
  * ```heroku config:set AWS_SECRET=votreKeySecretS3```
  * ```heroku config:set AWS_BUCKET=votreNomDuBucketS3```
* Ajouter une instance de Postgresql pour votre projet
  * ```heroku addons:create heroku-postgresql:hobby-dev```
* Lancer le déploiement
  * ```git push heroku master```

Questions et réflexions
=========