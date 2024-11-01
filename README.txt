=== SKSoftware Postone for WooCommerce ===
Contributors: sksoft
Donate link: https://sk-soft.net/
Tags: shipping, postone, postone shipping, woocommerce
Requires at least: 4.7
Requires PHP: 5.6
Tested up to: 6.5
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The SKSoftware PostOne for WooCommerce plugin calculates rates for shipping dynamically using PostOne API during cart/checkout.

== Description ==

SKSoftware PostOne for WooCommerce is a plugin which enables your WooCommerce store to deliver goods using PostOne shipping method.
It does automatic calculation of the shipping price based on country, ZIP code, weight and volume for the products in
the cart directly in the checkout. The administrator of the website can then create orders in PostOne directly from the
store itself and print shipping labels provided by PostOne or their partners.

#### Features

* Manage shipments and generate them directly to PostOne
* Print shipping labels
* Automatically email tracking numbers to clients
* Set default values for weight, volume and any others that may be required by PostOne when the product has none
* Overwrite defined values by default when creating a shipment, even if the product has them set
* Convert PostOne default currency (EUR) to the default currency of your store (if compatible with PostOne) based on realtime rates.
* Modern design, accompanied by WooCommerce styling

#### PostOne services support:

* ONE TRACK
* ONE BASIC
* ONE PREMIUM
* ONE EXPRESS
* ONE RETURN
* ONE LETTER
* ONE PACK
* ONE SELECT

== Installation ==

First, go to [sk-soft.net](https://sk-soft.net) and get your license key. You can request 14 days trial period to test the plugin.
Then, generate API credentials from api.postone.eu.

#### Method 1: Get directly from WordPress repository
1. Navigate to Plugins -> Add New and search for SKSoftware Postone for WooCommerce
2. Click the install button on the plugin with the corresponding name and activate it
3. Navigate to WooCommerce -> Shipping -> PostOne and enter your credentials. Instructions are provided below the input fields.
4. Setup your shipping methods and zones on WooCommerce -> Shipping section

#### Method 2: Upload via WordPress plugins:
1. Download the plugin from the WordPress plugins repo
2. Navigate to Plugins -> Add New -> Upload Plugin and select the 'sksoftware-postone-for-woocommerce.zip' archive
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to WooCommerce -> Shipping -> PostOne and enter your credentials. Instructions are provided below the input fields.
5. Setup your shipping methods and zones on WooCommerce -> Shipping section

#### Method 3: Upload via FTP:
1. Download the plugin from the WordPress plugins repo
2. Upload 'sksoftware-postone-for-woocommerce.zip' to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to WooCommerce -> Shipping -> PostOne and enter your credentials. Instructions are provided below the input fields.
5. Setup your shipping methods and zones on WooCommerce -> Shipping section

== Frequently Asked Questions ==

= Does the plugin provide free trial? =

Yes, it does. We provide 14 days free trial which can be started on [our website](https://sk-soft.net/plugins/postone-for-woocommerce/).

= Can my clients see the price for shipping in checkout? =

Yes, your clients can see the calculated price based on their filled shipping info.

= Can I create shipment directly on PostOne via my WordPress dashboard? =

Yes, you can.

= Can I print a label for my shipment from my WordPress dashboard? =

Yes, you can.

= How is tax for shipping price calculated? =

By default, shipping prices are with included tax. PostOne has fixed this to 20%, so we have too. You don't have to worry about shipping price tax not being calculated.

= Is there any included support? =

Yes, there is. You can write us [in our live chat](https://sk-soft.net/contacts/) or [send us an e-mail](mailto:office@sk-soft.net).

= Can you install the plugin for me? =

We offer free installation if you struggle to do it yourself. [Contact us for assistance](https://sk-soft.net/contacts/)

= Does the plugin collect any data? =

No, we do not track you or your clients.

== Changelog ==

= 1.1.1 =
* Tweak - Enhancement - Added PostOne SELECT product.

= 1.1.0 =
* Add - Support for WooCommerce's High performance order storage (HPOS).
* Add - Support for bulk shipment creation.

= 1.0.5 =
* Add - Support for RON currency.
* Add - Enhancement - Add integration with Advanced Shipment Tracking for WooCommerce plugin.

= 1.0.4 =
* Add - Enhancement - Add filter for default shipment parameters.

= 1.0.3 =
* Tweak - Enhancement - Add support for WooCommerce tax rules.

= 1.0.2 =
* Add - Default setting for HS Tariff Code.
* Add - Default setting for Terms of Delivery.

= 1.0.1 =
* Tweak - Enhancement - Add support for all WooCommerce weight and dimension units.

= 1.0.0 =
* Initial upload.
