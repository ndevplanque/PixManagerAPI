php bin/console doctrine:database:drop --force -y
php bin/console doctrine:database:create -y
php bin/console doctrine:migrations:migrate -y
php bin/console doctrine:fixtures:load -y
symfony serve
