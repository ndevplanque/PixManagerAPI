# PixManagerAPI

## Pré-requis

### Packages

- Installer make
```sh
composer require --dev symfony/maker-bundle
composer require form validator twig-bundle orm security-csrf  
```

- Installer Doctrine
```sh
composer require symfony/orm-pack
```

- Installer PhpUnit
```sh
composer require --dev symfony/test-pack
```

### Base de données
- Installer PostgreSQL version 16 via Google
- Configurer `DATABASE_URL` dans le fichier `.env`
- Créer la BDD avec cette commande
```sh
php bin/console doctrine:database:create*
```
- La peupler avec cette commande
```sh
php bin/console doctrine:migrations:migrate
```
- Se connecter pour vérifier que tout est bon
```sh
symfony run psql --dbname=pix_manager --username=postgres
pix_manager=# \dt
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
