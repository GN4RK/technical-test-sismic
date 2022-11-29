# Projet
Test technique pour une offre d'emploi chez SISMIC.

## Description des besoins
Fournir une api pour répondre à une gestion d'événements.

Explication :
- On souhaite faire une gestion d'événements pour réaliser des réservations en ligne.
- Un événement est défini par une date de début et une date de fin.
- Un événement est limité par un nombre d'inscrits.
- Une inscription est composée d'un nom, prénom, email et d'un numéro de téléphone.

L'api REST devra permettre de créer un événement, modifier un événement et supprimer un événement.

Elle devra également permettre l'inscription en vérifiant que le nombre d'inscrits sur l'événement n'est pas atteint.

## Environnement de développement
Le projet à été développé avec Symfony 6.1, Postgresql et Doctrine. 

## Endpoints
Events :
| Verb | Endpoint | Description |
| --- | --- | --- |
| POST | /events | create a new event |
| GET | /events | read event list |
| GET | /events/{id} | read event details |
| PUT | /events/{id} | update an event |
| DELETE | /events/{id} | delete an event |

Registrations
| Verb | Endpoint | Description |
| --- | --- | --- |
| POST | /events/{idEvent}/registrations | create a new registration for a specific event |
| GET | /events/{idEvent}/registrations | read registration list for a specific event |
| GET | /events/{idEvent}/registrations/{idRegistration} | read registration details |
| PUT | /events/{idEvent}/registrations/{idRegistration} | update a registration |
| DELETE | /events/{idEvent}/registrations/{idRegistration} | delete a registration |

## Installation

### Cloner le projet
```
git clone https://github.com/GN4RK/technical-test-sismic
```

### Installer les dépendances
```
composer install
```

### Configurations

#### Base de données
Changer la connexion à la base de donnée dans le fichier .env : 
```
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=14&charset=utf8"
```

#### Création de la base de données
```
php bin/console doctrine:database:create
```

#### Mettre à jour le schema de la base
```
php bin/console doctrine:schema:update --force
```

### Lancer le serveur
```
symfony server:start
```

## Badge Codacy
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/56f6031a38ac428ebfe81699c49c6a03)](https://www.codacy.com/gh/GN4RK/technical-test-sismic/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=GN4RK/technical-test-sismic&amp;utm_campaign=Badge_Grade)