Blue Logic API Proxy
====================

Accepts all fields in a single request and utilized them to process 4 requests to Blue logic:


Fields Required
===============

The following fields are required to start a transaction

Surname=Contact Last Name,
GUID=Contact ID,
Address1=Contact Shipping Address,
County=Contact Shipping County,
PostCode=Contact Shipping Postal Code,
Country=Contact Shipping Country,
OptIn=1 OR 0, depending on the contact is opting in to mailing (the client might know more about this),
ShippingMethod=One of the following: 1st Class Packet, 1st Class Post, 2nd Class Packet, 2nd Class Post, Airmail, Carrier, City Link, Collected, Delivered, Direct Delivery, International Signed, Nightfreight, Parcel Force, Recorded Del 1st Class, Recorded Delivery 2nd Class, Royal Mail - Despatch Express, Special Delivery
PaymentMethod=One of the following: BACS, Balance Used, Cash, Cheque, Credit Card, Credit Card (PDQ), Credit Card (Protx - Payment On Despatch), Credit Card (Protx - Payment Up Front), Direct Debit, FOC, On A/C, Replaced, Reservation
Currency=One of the following: Australian Dollar, Canadian Dollar, Euro, Indian Rupee, Krona, Pakistan Rupee, South African Rand, Sterling, Swiss Franc, US Dollars, Yen
CurrencyRate=Conversion factor from Sterling to specified currency, to 4 decimal places. (I'm assuming "0" for anything in US Dollars),
ProductCode=Product code of item to order
Qnty=Number of products being ordered,
UnitPrice=Price of a single product

Example Request
===============

```
http://api.bluelogic.tronnet.me?Surname=Tronhammer&GUID=5&Address1=123 Test St.&PostCode=93101&County=Santa Barbara&Country=US&OptIn=1&ShippingMethod=Carrier&PaymentMethod=Credit Card&Currency=US Dollars&CurrencyRate=0.00&ProductCode=912&Qnty=1&UnitPrice=17.80
```


Server Requirements
===================

Apache 2
PHP 5.3
Memcache (w/ PHP module)