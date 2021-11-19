# Agence Dn'D - DPD France Shipping Bundle:

### Documentation:

After installing `DPD France Shipping Bundle`, please follow the following steps.

#### Create the DPD France Shipping Integration

In the admin section, go to `System > Integrations > Manage integrations > Create Integration`.

Select the `DPD France` integration type and fill in the requested configurations.

#### Configure DPD France Shipping Integration

* Type
* Name
* Agency code
* Contract number
* Max quantity
* Shipping Services
* DPD Classic service label
* DPD Classic service description
* DPD Predict service label
* DPD Predict service description
* DPD Relay service label
* DPD Relay service description
* Google Maps API key
* Enable station export
* Station FTP Host
* Station FTP User
* Station FTP Password
* Station FTP Port
* Order statuses sent to station
* Default owner

#### Create shipping rules

In the admin section, go to `System > Shipping rules > Create shipping rule`.

Select the proper currency / website combination for your store, set a base price for the services and an eventual extra fee per service in additional options section.

In order to use the quantity limitation at product level, you can set the following rule expression:
```
lineItems.all(
    (lineItem.product.maxQtyForDpdFr < 0)
    or
    (lineItem.product.maxQtyForDpdFr > lineItem.quantity)
)
```

* A product with the attribute `DPD France Max qty` set to "-1" have no specific limitation.
* A product with the attribute `DPD France Max qty` set to "0" is not shippable with DPD France.

There are 3 levels of limitation for product quantities:
* 1- DPD France `Max Quantity` Integration global setting
* 2- Method specific limitation in db table `dnd_dpd_fr_shipping_service`
* 3- Product specific limitation with the attribute `DPD France Max qty`

#### Enable the checkout workflow "With DPD France"

The two native checkout workflows have been cloned into their "*...with DPD France*" declinations.

Enable the one corresponding to the workflow desired.

### Flush the cache

Then, flush the cache:
```bash
bin/console cache:clear
```

##### [> Back to Readme](../README.md)