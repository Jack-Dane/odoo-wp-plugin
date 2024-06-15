#!/bin/bash

docker compose -f tests/end_to_end_tests/wordpress-compose.yaml rm -f --volumes --stop
docker compose -f tests/end_to_end_tests/wordpress-compose.yaml up --force-recreate --build --wait

echo "Waiting for WP containers to start"
sleep 30

echo "Starting PHP container"
dockerPHPImageId=$(docker build -t cf7-odoo-connector-php -f tests/end_to_end_tests/php-composer-dockerfile . -q)
dockerPHPContainerId=$(docker container run -d --network=host ${dockerPHPImageId} tail -f /dev/null)

echo "Running Endpoint Tests"
docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/endpoint_tests'

echo "Running Selenium Tests"
dockerSeleniumContainerId=$(docker run -d --rm -it --network host --shm-size 2g selenium/standalone-chrome)
docker compose -f tests/end_to_end_tests/odoo-compose.yaml rm -f --volumes --stop
docker compose -f tests/end_to_end_tests/odoo-compose.yaml up --force-recreate --build --wait

echo "Waiting for Odoo"
sleep 20

docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/selenium_tests/Create_Test.php'
docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/selenium_tests/SendData_Test.php'
docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/selenium_tests/Error_Test.php'
docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/selenium_tests/Update_Test.php'
docker exec $dockerPHPContainerId /bin/sh -c 'cd /opt/odoo_conn; vendor/bin/phpunit tests/end_to_end_tests/tests/selenium_tests/Delete_Test.php'

# cleanup the containers
docker compose -f tests/end_to_end_tests/odoo-compose.yaml rm -f --volumes --stop
docker compose -f tests/end_to_end_tests/wordpress-compose.yaml rm -f --volumes --stop

docker container rm -f $dockerPHPContainerId
docker container rm -f $dockerSeleniumContainerId

docker image rm $dockerPHPImageId
