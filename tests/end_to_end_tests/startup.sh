#!/bin/bash

wp_install() {
	wp core install --path=/var/www/html --url=localhost:8000 --admin_user=admin --admin_email=test@test.com --admin_password=password --title="Odoo Conn"
}

sleep 10
wp_install
retVal=$?
while [ $retVal -ne 0 ]
do
	sleep 10
	wp_install
	retVal=$?
done

wp plugin install contact-form-7 --activate
wp plugin activate odoo
