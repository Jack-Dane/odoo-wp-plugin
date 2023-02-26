# Odoo Contact Form 7 Connector

Integrate your WordPress Contact 7 Forms to Odoo. You can easily set up the integration yourself to create contacts or leads when a form has been submitted. 

## Motivation

Due to Odoo not providing a rest API it is not so easy to implement this feature without needing to create a bespoke plugin for each website/form. 

This plugin provides admins an interface in which they can create dynamic connections to different Odoo instances without needing to code anything.  

## Installation

Due to some difficaulties with XML-RPC support, composer is needed. 

1. First you need to clone the repoistory. 
2. Install the dependencies of the repository using [composer](https://getcomposer.org/). This can be done simply by calling `composer install` when composer is complete. 
3. Zip the plugin and upload to your wordpress site. 


This plugin can be test within a [docker environment](https://hub.docker.com/_/wordpress). 

## How to use the Wordpress plugin

1. [Create an API Key](https://www.odoo.com/documentation/16.0/developer/api/external_api.html#api-keys) in your Odoo instance. 
2. Create an Contact 7 Form, unless one has already been created. 
3. Once the WordPress plugin has been installed you will be able to see a new menu item down the side. Now create a connection with the following from Odoo: the Odoo URL, the Odoo database name, the email of your Odoo user and the newly created API Key. 
4. Create an Odoo form with the newly created connection and the contact 7 form. Also add the Odoo model that has been created the, eg for creating a contact in Odoo use "res.partner".
5. Now map the Contact 7 Form fields to what values they will fill within Odoo using the Odoo Form Mapping. You can also set constant values that the form should fill in within Odoo. 
6. Submit the form and you should see the new object created in your odoo instance. 

[Youtube video]

## Security

* API keys are encrypted in the system using Sodium symmetric key encryption.
* Encryption keys can be refreshed if you think it has been leaked. Warning - Refreshing an encryption key will remove all the Odoo connection, form and form mapping data. 

## Requirements

* PHP 7.3 or greater
* Ideally Odoo 14 or greater as version before 14 don't support API keys. 

## Try with Docker

You can easily try the plugin within docker, first install the composer dependencies and then use the command `docker compose up`. You will have a newly wordpress instance running on port 8080. You just need to install the plugin from the install page along with Contact Form 7. 
