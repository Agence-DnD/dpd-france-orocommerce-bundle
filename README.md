# Agence Dn'D - DPD France Shipping Bundle:

`Agence Dn'D - DPD France Shipping Bundle` is an OroCommerce bundle that allows you to use DPD France Shipping service.

### Documentation:

`Agence Dn'D - DPD France Shipping Bundle` adds "DPD Classic", "DPD Predict" and "DPD Relais" shipping methods to OroCommerce.

Also, it adds the possibility to export command to "DPD Station" service by FTP.

See the full documentation [here](doc/documentation.md).

### Installation:

Require the module with composer:
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
bin/console oro:translation:load
```

### Requirements:

| OroCommerce |   PHP   |
|:------------|:-------:|
| \>= 5.1     | \>= 8.2 |

### About us:

Founded by lovers of innovation and design, [Agence Dn'D](https://www.dnd.fr) assists companies in the creation and development of customized digital (open source) solutions for web and E-commerce since 2004.
