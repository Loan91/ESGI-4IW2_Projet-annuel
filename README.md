# ESGI-4IW2_Projet-annuel

![PHP](https://img.shields.io/badge/PHP-^7.4-informational)
![License](https://img.shields.io/badge/license-none-informational)

## Description

Le projet annuel consiste à réaliser un site web sur la vente de biens immobiliers.

Le site possède une gestion utilisateur, une gestion de rendez-vous, une administration depuis un pannel dédié...

## Installation

Lancez les commandes suivantes :

+ docker-compose up -d

### Initialisation de la base de données
+ docker-compose exec php bin/console d:s:u --force
+ docker-compose exec php bin/console d:f:l --append

## Lancement manuel de commandes
### Console symfony
+ docker-compose exec php bin/console ...
### Composer
+ docker-compose run --rm composer ...

### Compilation des assets
+ docker-compose run --rm node yarn install
+ docker-compose run --rm node yarn run dev | prod

### Tests phpunit

+ docker-compose run --rm phpunit bin/phpunit

## Licence

Le projet est privé et n'est donc soumis à aucune lisence. Toute décision de changement devra être au préalable discuté par tout les membres du projet.

## Bonus
### Alias
+ alias dps="docker ps"
+ alias drun="docker run"
+ alias dexec="docker exec"

- alias dup="docker-compose up"
- alias dupd="docker-compose up -d"
- alias ddn="docker-compose down"
- alias dcbd="docker-compose build"

+ alias dcrun="docker-compose run"
+ alias dcexec="docker-compose exec"
+ alias dcexec-console="docker-compose exec php bin/console"

