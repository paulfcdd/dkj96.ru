#!/usr/bin/env bash
echo '========= Pull new changes from repository ==========='
git pull && php bin/composer.phar self-update && php bin/composer.phar install

echo '========= Updating database structure ==========='
bin/console doctrine:schema:update --force

echo '========= Updating node modules ==========='
cd web/ &&
npm update

echo '========= Clear cache ==========='
cd ../ &&
bin/console cache:clear --no-warmup &&
bin/console cache:clear --no-warmup  --env=prod

echo '========= Update finished! ==========='
