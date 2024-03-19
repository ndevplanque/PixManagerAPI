# PixManagerAPI

## Pré-requis

### Packages

- Installer les dépendances
```sh
composer install
```

### Base de données
- Installer PostgreSQL version 16 via Google
- Configurer `DATABASE_URL` dans le fichier `.env`
- Créer la BDD
```sh
php bin/console doctrine:database:create
```
- Créer la structure
```sh
php bin/console doctrine:migrations:migrate
```
- La peupler de données
```sh
php bin/console doctrine:fixtures:load --purge-with-truncate
```
- Se connecter pour vérifier que tout est bon
```sh
symfony run psql --dbname=pix_manager --username=postgres
pix_manager=# \dt
pix_manager=# select * from app_user;
```
Fin des pré-requis.

## Utiliser `make`
- Pour créer un simple Controller
```sh
php bin/console make:controller HealthCheckController  
```

- Pour créer un Controller CRUD (il faut d'abord créer l'Entity)
```sh
php bin/console make:crud Photo
```

- Pour créer une Entity [(petite aide)](https://symfony.com/doc/current/doctrine.html#creating-an-entity-class) + générer une migration
```sh
php bin/console make:entity
php bin/console make:migration
```

## Lancer les tests

Pour lancer les tests, utilisez cette commande
```sh
php bin/phpunit
```

## Lancer le serveur

Si vous rencontrez l'erreur `Server is already running`, utilisez cette commande :
```sh
symfony server:stop
```

Pour lancer le serveur, utilisez cette commande :
```sh
symfony server:start
```
