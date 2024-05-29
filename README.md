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

Set **API Token** and **API Code** will be provided by AdScout platform once your account is approved.

Select integration type

Click **Save Config** button in top right corner and flush the cache. You can do it through admin panel or with command:

```
bin/magento cache:flush
```

![Setup](https://raw.githubusercontent.com/adscout-io/magento-connector/master/docs/adscout-settings-demo-2024-05-29.jpg)

## License

[MIT](https://opensource.org/licenses/MIT)
