echo '========= Pull new changes from repository ==========='
git pull

echo '========= Updating database structure ==========='
bin/console doctrine:schema:update --force

echo '========= Clear cache ==========='
bin/console cache:clear --no-warmup
bin/console cache:clear --env=prod

echo '========= Deploy finished! ==========='
