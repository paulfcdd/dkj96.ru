echo '========= Pull new changes from repository ==========='
git pull && php bin/composer.phar self-update && php bin/composer.phar install

echo '========= Updating database structure ==========='
bin/console doctrine:schema:update --force

echo '========= Clear cache ==========='
bin/console cache:clear --no-warmup
bin/console cache:clear --no-warmup  --env=prod

echo '========= Update finished! ==========='
