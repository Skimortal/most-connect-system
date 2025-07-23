docker exec app_php bin/console make:migration
docker exec app_php bin/console doctrine:migrations:migrate --no-interaction
docker exec app_php bin/console cache:clear
