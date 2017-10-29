echo '========= Welcome to the DKJ.96 installation! ==========='

echo '1. Checking for the newest version of Composer and install dependencies'
php bin/composer.phar self-update && php bin/composer.phar install

echo '2. Creating database'
php bin/console doctrine:database:create

echo "3. Do you wish to import database?"
select yn in "Yes" "No"; do
    case $yn in
        Yes ) echo 'Importing database' && mysql -u root -h localhost dkj<bin/db_dump.sql; break;;
        No ) echo 'Creating database schema' && 
			 php bin/console doctrine:schema:update --force &&
			 echo 'Creating an admin user'
			 php bin/console fos:user:create --super-admin; break;;
    esac
done

echo "4. Install Node packages"
cd web/ && npm install

echo "5. Clear cache"
cd ../ && php bin/console cache:clear --no-warmup && php bin/console cache:clear --env=prod
