---
author: axytos GmbH
title: "Installation Guide"
subtitle: "axytos Buy Now Pay Later, Shopware5"
header-right: axytos Buy Now Pay Later, Shopware5
lang: "en"
titlepage: true
titlepage-rule-height: 2
toc-own-page: true
linkcolor: blue
---

## Installation Guide

The plugin provides the payment method purchase on account for shopping in your Shopware shop.

Purchases made with this payment method may be accepted by axytos up to receivables management.

All relevant changes to orders with this payment method are automatically sent to axytos.

Adjustments beyond the installation, e.g. of invoice and e-mail templates, are not necessary.

For more information, visit [https://www.axytos.com/](https://www.axytos.com/en).


## Requirements

1. Contractual relationship with [https://www.axytos.com/](https://www.axytos.com/en).

2. Connection data to connect the plugin to [https://portal.axytos.com/](https://portal.axytos.com/).

3. Shop is required to run [Cronjobs](https://docs.shopware.com/en/shopware-5-en/settings/system-cronjobs#setting-up-a-cronjob).

In order to be able to use this plugin, you first need a contractual relationship with [https://www.axytos.com/](https://www.axytos.com/en).

During onboarding you will receive the necessary connection data to connect the plugin to [https://portal.axytos.com/](https://portal.axytos.com/).

## Plugin installation via Plugin Manager

1. Buy and add the plugin in the Shopware Store or via Plugin Manager within your Shopware distribution for free.

2. Open the plugin configuration. The plugin axytos BNPL is listed under Configuration > Plugin Manager > Management > Installed 

3. Run Install.

4. Run Activate.

You can buy and add the plugin for free via the Shopware Store within your Shopware distribution.

Once added, it will be listed under My Extensions.

Run Install.

Run Activate.

The plugin is now installed and can be configured and activated.

In order to be able to use the plugin, you need valid connection data for [https://portal.axytos.com/](https://portal.axytos.com/) (see requirements).


## Plugin and shop configuration in Shopware

1. Switch to the backend of your Shopware distribution. The axytos BNPL plugin is listed under Configuration > Plugin Manager > Management > Installed.

2. Click the pencil icon in order to open the configuration of axytos BNPL.

3. Enter API host. Either [https://api.axytos.com/](https://api.axytos.com/) or [https://api-sandbox.axytos.com/](https://api-sandbox.axytos.com/), the correct values ​​will be communicated to you by axytos during onboarding (see requirements)

4. Enter API key. You will be informed of the correct value during the onboarding of axytos (see requirements).

5. Enter Client secret. You will be informed of the correct value during the onboarding of axytos (see requirements).

6. Execute save.

7. Run Test API Connection.

8. If the connection test fails, clear all caches and check again. If the problem is not solved, please get in touch with your contact person at axytos.

9. If the connection test ends successfully, you are done here. 

10. Map the new payment method to shipping under Configuration > Shipping Costs > (Selected Shipping) > Payment methods.

For configuration, you must save valid connection data to [https://portal.axytos.com/](https://portal.axytos.com/) (see requirements), i.e. API host and API key for the plugin.

Then run Test API Connection.

If the connection test fails, please get in touch with your contact person at axytos, if not you are done here.

Now activate the payment method purchase on account | Axytos purchase on account in the storefront.


## Can't select purchase on account for purchases?

Check the following points:

1. The axytos BNPL plugin is installed.

2. The axytos BNPL plugin is activated.

3. The axytos BNPL plugin is configured with correct connection data (API host & API key).

4. The axytos BNPL plugin is associated with at least one dispatch method.

5. All caches are cleard.

Check the correctness of the connection data with Test API connection.

Incorrect connection data means that the plugin cannot be selected for purchases.

