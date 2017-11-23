#!/usr/bin/env bash

echo '
*********************************************
*                                           *
*    Welcome to the DKJ.96 installation!    *
*                                           *
*********************************************
'

echo '1. CHECKING FOR THE NEWEST VERSION OF COMPOSER'
php bin/composer.phar self-update

echo '2.INSTALLING DEPENDENCIES'
php bin/composer.phar install

echo '3. CREATING DATABASE'
php bin/console doctrine:database:create

echo "4. IMPORT DATABASE?"
select choice in "Yes" "No" "Skip"; do
    case $choice in
        Yes ) 	echo 'Importing database' && 
				php bin/console doctrine:database:import bin/dump.sql; break;;

        No ) 	echo 'Creating database schema' && 
				php bin/console doctrine:schema:update --force &&
				echo 'Creating an admin user'
				php bin/console fos:user:create --super-admin; break;;

	    Skip ) break;;
    esac
done

echo "5. INSTALLING NODE PAKAGES"
cd web/ && npm install

echo " 5. CLEARING THE CACHE"
cd ../ &&
php bin/console cache:clear --no-warmup &&
php bin/console cache:clear --no-warmup --env=prod

echo '
*********************************************
*                                           *
*          Installation complete!           *
*                                           *
*********************************************
'
exit 0;
