### 1.0.1  

* Add frontend checkout PIX and QR Code payment methods select input
* Add payment transaction data to the admin panel order transaction details page
* Add Cron to process the paid payments
* Add Webhook notification Rest API to process payments
* Add Rest API endpoint to return the wallets enabled in Shipay system
* Add the payment QR Code for the admin panel order details, customer order details and order success page
* Add the PIX copy/paste buttom for the admin panel order details, customer order details and order success page
* Add Wallet deep link button for the admin panel order details, customer order details and order success page
  
### 1.0.4

* Support Magento 2.4.6
* Support PHP 8.2 and 8.1
* Fixes order expired webhook
* Cancel automatically orders expired and canceled in Shipay.
* Correction at checkout when the module was not active, the wallets api was called
