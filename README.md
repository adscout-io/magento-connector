<h1 align="center">AdScout_Connector</h1> 

## Table of contents

- [Summary](#summary)
- [Installation](#installation)
- [Setup](#setup)
- [License](#license)

## Summary

This module enable integration between AdScout platform and your Magneto/Adobe Commerce store.

## Installation

```
composer require adscout-io/magento-connector
bin/magento module:enable AdScout_Connector
bin/magento setup:upgrade
```

## Setup

Login in admin panel of your store and go to:

`Stores -> Configuration -> Ad Scout -> General`

Set **Enable** to `Yes`

Then login to your AdScout profile. After successful login press the gear icon in the upper right corner, from the
drop-down menu choose General and find API Token and API Code.

Copy and paste them in the fields **API Token** and **API Code**.

Select integration type.

Copy **CSV url** address and paste it in the field Feed URL in the settings of your AdScout profile. In order to open
the
settings, press the gear icon in the upper right corner and from the drop-down choose General. After you have
completed the step scroll down to the bottom of the page and press the button Update to save the changes.

Click **Save Config** button in top right corner and flush the cache. You can do it through admin panel or with command:

```
bin/magento cache:flush
```

Congratulations! You have successfully enabled our Magento/Adobe Commerce plugin for your store.

![Setup](https://raw.githubusercontent.com/adscout-io/magento-connector/master/docs/adscout-settings-demo-2024-05-29.jpg)

## License

[MIT](https://opensource.org/licenses/MIT)
