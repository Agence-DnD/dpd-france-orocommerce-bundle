# Agence Dn'D - DPD France Shipping Bundle:

### Documentation:

After installing `DPD France Shipping Bundle`, please follow the following steps.

#### Create the DPD France Shipping Integration

In the admin section, go to `System > Integrations > Manage integrations > Create Integration`.

Select the `DPD France` integration type and fill in the requested configurations.

#### Configure DPD France Shipping Integration



#### Create shipping rules

In the admin section, go to `System > Shipping rules > Create shipping rule`.

Select the proper currency / website combination for your store, set a base price for the services and an eventual extra fee per service in additional options section.

Set the following expression in your shopping rule if you want to use the `maxQtyForDpdFr` product attribute to enforce limitations at product level:
```
lineItems.all(
    (lineItem.product.maxQtyForDpdFr < 0)
    or
    (lineItem.product.maxQtyForDpdFr > lineItem.quantity)
)
```

#### Enable the checkout workflow "With DPD France"

The two native checkout workflows have been cloned into their "*...with DPD France*" declinations.

Enable the one corresponding to the workflow desired.

### Flush the cache

Then, flush the cache:
```bash
bin/console cache:clear
```

##### [> Back to Readme](../README.md)
