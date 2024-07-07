![Tests](https://github.com/Jack-Dane/odoo-wp-plugin/actions/workflows/run-tests.yml/badge.svg)
![PHP](https://img.shields.io/badge/php-%3E%3D8.1-6495ED)
![Static Badge](https://img.shields.io/badge/odoo-%3E%3D14.0-9c5789)

# Odoo Contact Form 7 Connector

Integrate your WordPress Contact 7 Forms to Odoo. You can easily set up the integration yourself to create contacts or
leads when a form has been submitted.

## Motivation

Due to Odoo not providing a rest API it is not so easy to implement this feature without needing to create a bespoke
plugin for each website/form.

This plugin provides admins an interface in which they can create dynamic connections to different Odoo instances
without needing to code anything.

## Installation

You can download the latest version of the plugin via the [releases page](https://github.com/Jack-Dane/odoo-wp-plugin/releases). 

### Install from source code

Due to some difficulties with XML-RPC support, composer is needed.

1. First you need to clone the repository.
2. Install the dependencies of the repository using [composer](https://getcomposer.org/). This can be done simply by
   calling `composer install` when composer is complete.
3. Zip the plugin and upload to your wordpress site.

This plugin can be tested within a [docker environment](https://hub.docker.com/_/wordpress).

## How to use the Wordpress plugin

1. [Create an API Key](https://www.odoo.com/documentation/16.0/developer/api/external_api.html#api-keys) in your Odoo
   instance.
2. Create an Contact 7 Form, unless one has already been created.
3. Once the WordPress plugin has been installed you will be able to see a new menu item down the side. Now create a
   connection with the following from Odoo: the Odoo URL, the Odoo database name, the email of your Odoo user and the
   newly created API Key.
4. Create an Odoo form with the newly created connection and the contact 7 form. Also add the Odoo model that has been
   created the, eg for creating a contact in Odoo use "res.partner".
5. Now map the Contact 7 Form fields to what values they will fill within Odoo using the Odoo Form Mapping. You can also
   set constant values that the form should fill in within Odoo.
6. Submit the form and you should see the new object created in your odoo instance.

**The UI has changed since this video was release but the concept is still the same**

[Video Tutorial](https://www.youtube.com/watch?v=xhAvrEaBXAA)


## Examples

See [examples](examples) for examples of supported Odoo field types and how to use them. 

## Security

* API keys are encrypted in the system using Sodium symmetric key encryption.
* Encryption keys can be refreshed if you think it has been leaked. Warning - Refreshing an encryption key will remove
  all the Odoo connection, form and form mapping data.

## Requirements

* PHP 8.1 or greater
* Ideally Odoo 14 or greater as versions before 14 don't support API keys. A password should work instead of an API key
  but is ill-advised.

## Troubleshooting
* There is a test button for each connection row. Press this to make a test connection to check authentication before 
assuming the connection details are correct.
* Errors that have caused a form to not submit to Odoo will be shown in the "Odoo Submit Errors" tab. 

# Contributing

* Run `runE2ETests.sh` before making a pull request.
* Assume that this will be run on a Linux machine and has docker installed. 

## Try with Docker

You can easily try the plugin within docker, first install the composer dependencies and then use the
command `docker compose up`. You will have a newly wordpress instance running on port 8080. You just need to install the
plugin from the install page along with Contact Form 7. 
