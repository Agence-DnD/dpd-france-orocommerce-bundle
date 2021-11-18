# Agence Dn'D - DPD France Shipping Bundle:

`Agence Dn'D - DPD France Shipping Bundle` is an OroCommerce bundle that allows you to use DPD France Shipping service.

### Documentation:

`Agence Dn'D - DPD France Shipping Bundle` adds "DPD Classic", "DPD Predict" and "DPD Relais" shipping methods to OroCommerce.

Also, it adds the possibility to export command to "DPD Station" service by FTP.

See the full documentaton [here](doc/documentation.md).

### Installation:

First, you need to follow our OroCommerce module install guide: [Install Guide](https://agencednd.atlassian.net/wiki/spaces/MI/pages/2145681458/Installation+des+modules+Marketplace+interne+OroCommerce)

After that, you can require the module with composer:
```bash
composer require agencednd/dpd-france-orocommerce-bundle
```

Flush the cache:
```bash
bin/console cache:clear
```

Run the migrations:
```bash
bin/console oro:migration:load --force
bin/console oro:migration:data:load --bundles=DndDpdFranceShippingBundle
```

Reinstall the assets to copy bundle public folder (for integration logo):
```bash
bin/console assets:install
```

Load the modified workflows and reload the translations:
```bash
bin/console oro:workflow:definitions:load
php bin/console oro:translation:load
```


### Configuration:

#### Create a DPD France shipping integration

On the admin section, go to System > Integrations > Manage integrations > Create Integration

Select DPD France integration type and fill in the requested configurations.

#### Create shipping rules

On the admin section, go to System > Shipping rules > Create shipping rule

Select the proper currency / website combination for your store, set a base price for the services and an eventual extra fee per service in additional options section.

#### Activate the checkout workflow "With DPD France"

The two native checkout workflows have been cloned into their "...with DPD France" declinations. Activate the one corresponding with the previously active.

### Flush the cache, again

```bin/console cache:clear```


### Requirements:

| OroCommerce           | PHP                               |
| :---------------------| :--------------------------------:|
|                       | \>= 7.1.3, >= 7.2, >= 7.3, >= 7.4 |

### About us:

Founded by lovers of innovation and design, [Agence Dn'D](https://www.dnd.fr) assists companies in the creation and development of customized digital (open source) solutions for web and E-commerce since 2004.
