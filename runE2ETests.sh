#!/bin/bash

docker compose -f tests/end_to_end_tests/compose.yaml rm -f
docker compose -f tests/end_to_end_tests/compose.yaml up --force-recreate --build --wait

echo "Waiting for WP containers to start"
sleep 30

echo "Starting PHP container"
dockerPHPImageId=$(docker build -f tests/end_to_end_tests/php-composer-dockerfile . -q)
dockerPHPContainerId=$(docker container run -d --network=host ${dockerPHPImageId} tail -f /dev/null)

echo "Installing Vendor"
# no-cache because the container fails to create directories when cloning
# from git eg:
# could not create leading directories of
# '/.composer/cache/vcs/https ---github.com-bovigo-vfsStream.git'
docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; composer install --no-cache'

echo "Running Endpoint Tests"
docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/endpoint_tests'

echo "Running Selenium Tests"
dockerSeleniumContainerId=$(docker run -d --rm -it --network host --shm-size 2g selenium/standalone-chrome)
docker compose -f tests/end_to_end_tests/odoo_compose.yaml rm -f
docker compose -f tests/end_to_end_tests/odoo_compose.yaml up --force-recreate --build --wait

echo "Waiting for Odoo"
sleep 20

docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/selenium_tests/Create_Test.php'
docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/selenium_tests/SendData_Test.php'
docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/selenium_tests/Error_Test.php'
docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/selenium_tests/Update_Test.php'
docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/selenium_tests/Delete_Test.php'

# cleanup the containers
docker compose -f tests/end_to_end_tests/odoo_compose.yaml down
docker compose -f tests/end_to_end_tests/odoo_compose.yaml rm -f
docker compose --project-directory tests/end_to_end_tests down
docker compose --project-directory tests/end_to_end_tests rm -f

docker container rm --force $dockerPHPContainerId
docker container rm --force $dockerSeleniumContainerId
