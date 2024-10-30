=== Limepay WooCommerce Gateway ===
Contributors: limepay
Tags: Split Payments, Checkout, Credit card, Debit card
Tested up to: 6.4.2

Woo-Commerce gateway extension to support Limepay payments


== Description ==
Woo-Commerce gateway extension to integrate Limepay payment option in to checkout page


== Installation ==
Make sure Woo-commerce plugin is installed and activated.
1. Obtain API Access keys from Limepay
2. Install Limepay woo-commerce gateway plugin
3. Activate the plugin
4. From woo-commerce payments tab open Limepay Payments setup page
5. Enter you Publishable key and Secret key provided by Limepay and enable the gateway


== Change Log ==

= v4.2.3 =
*Release Date: 11th August 2023*

1. Display 3DS modal closing error messages [sc-15591]

= v4.2.2 =
*Release Date: 11th July 2023*

1. Removed errors from known errors list and added more details to final error message in checkout [sc-15332]

== Change Log ==

= v4.2.1 =
*Release Date: 19th April 2023*

1. Option for Admin to enable automatic place order with wallet payments [sc-14886]

= v4.2.0 =
*Release Date: 20st December 2022*

1. Support for WooCommerce-Subscriptions [sc-10939]

= v4.1.2 =
*Release Date: 6th October 2022*

1. Changed helper methods to static [sc-13131]
2. Added missing customerToken parameter [sc-13131]
3. Fixed ApplePay domain association file priority issue [sc-13131]
4. Handle checkout amount and currency variables missing error [sc-13131]
5. Include user email and billing-phone in order data [sc-13235]

= v4.1.1 =
*Release Date: 14th September 2022*

1. Pass platform related data in to checkout [sc-11940]

= v4.1.0 =
*Release Date: 29th July 2022*

1. Implemented support for order-pay page [sc-11375]
2. Potential fix for CSRF error in checkout [sc-11398]
3. Fix for some sites not rendering payment_fields section.
4. Fix for get_woocommerce_currency() returning null.

= v4.0.0 =
*Release Date: 12th Apr 2022*

1. Stack checkout implemented [sc-9891]

= v3.2.3 =
*Release Date: 12th Apr 2022*

1. Fixed "The order request has failed" error in checkout [sc-9726]

= v3.2.2 =
*Release Date: 31st Jan 2022*

1. Added "Minimum Amount for 3DS" setting in admin [sc-8458]
2. Disabled SSL warning in admin [sc-6978]

= v3.2.1 =
*Release Date: 17th Jan 2022*

1. Fixed: checkout not loading when description is empty issue [sc-8332]

= v3.2.0 =
*Release Date: 7th Jan 2022*

1. Individual payment methods for full and BNPL payments.

= v3.1.0 =
*Release Date: 27th Oct 2021*

1. Handle 3DS modal on plugin side.
2. File and folder structure changes.
3. Followed WP coding standards.

= v3.0.3 =
*Release Date: 21st Sep 2021*

1. Added a fix for the issue jQuery 3.6 not supporting attr method.

= v3.0.2 =
*Release Date: July 2021*

1. Fixed PHP Notice which appears in checkout page

= v3.0.1 =
*Release Date: July 2021*

1. Fixed js file caching issue

= v3.0.0 =
*Release Date: April 2021*

1. Limepay v2 api and checkout
2. Ability refund v1 transactions
3. Automatically scroll back to Limepay iframe in case of an error or 3DS being triggered after placing the order

= v2.4.4 =
*Release Date: March 2021*

1. Fixed shortcode woocommerce php issue.

= v2.4.3 =
*Release Date: Feb 2021*

1. Updated iFrame parameters to help with KYC

= v2.4.0 =
*Release Date: Thursday, 18 Jun 2020*

*Release Notes*
1. BNPL switcher
2. BNPL offer on product page
3. BNPL Toggle switcher on product page
4. Support of shortcodes for payment switcher and showing installment price on product pages
5. Changing final "Place Order" button  color when all steps to create a payplan are done

= 2.3.0 =
*Release Date: Tuesday, 09 Jun 2020*

* Added payment option selection setting in configuration
