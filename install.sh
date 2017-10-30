echo '
 _____________________________________
|                                     |	
| Welcome to the DKJ.96 installation! |
|_____________________________________|
'
echo '1. CHECKING FOR THE NEWEST VERSION OF COMPOSER'
php bin/composer.phar self-update

echo '2.INSTALLING DEPENDENCIES'
php bin/composer.phar install

echo '3. CREATING DATABASE'
php bin/console doctrine:database:create

echo "4. IMPORT DATABASE?"
select yn in "Yes" "No"; do
    case $yn in
        Yes ) 	echo 'Importing database' && 
				php bin/console doctrine:database:import bin/db_dump.sql; break;;
        No ) 	echo 'Creating database schema' && 
				php bin/console doctrine:schema:update --force &&
				echo 'Creating an admin user'
				php bin/console fos:user:create --super-admin; break;;
    esac
done

echo "5. INSTALLING NODE PAKAGES"
cd web/ && npm install

echo " 5. CLEARING THE CACHE"
cd ../ && php bin/console cache:clear --no-warmup && php bin/console cache:clear --env=prod
