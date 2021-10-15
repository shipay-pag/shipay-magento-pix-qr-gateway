## Shipay Pix Qr Gateway Module for Magento 2

## Overview
This module provides integration between Shipay Payment Provider for PIX payment transaction operations and Magento 2 platform.

## Installation

#### Install the Shipay PixQrGateway module

Unzip the module file in app/code/Shipay/PixQrGateway folder 

After successful module placement Shipay PixQrGateway module should be enabled.

```bash
$ bin/magento module:enable Shipay_PixQrGateway --clear-static-content
```

Run `setup:upgrade` command:
```bash
$ bin/magento setup:upgrade
```

Finally flush cache:
```bash
$ bin/magento cache:flush
```

#### Enable token cache for better performance
bash
```
$ bin/magento cache:enable shipay_token
```

#### Further docs (Portuguese)
https://marketplace.magento.com/media/catalog/product/shipay_pagamentos_digitais-shipay-magento-pix-qr-gateway-1-0-2-ce/user_guides.pdf
