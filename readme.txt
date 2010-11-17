=== Plugin Name ===
Contributors: jquindlen
Donate link: http://www.wpstorecart.com/
Tags: cart,ecommerce,store,paypal,shopping-cart,ecomm,e-commerce,authorize.net,affiliate,affiliates
Requires at least: 2.8.0
Tested up to: 3.0.1
Stable tag: 2.0.6
Version: 2.0.6

== Description ==

[wpStoreCart](http://wpstorecart.com/ "Wordpress eCommerce Plugin") wpStoreCart is a free, open source, and full featured e-commerce platform built atop of Wordpress.
It is easy to use, lightweight, heavily supported and constantly updated with new features.

[wpStoreCart](http://wpstorecart.com/ "Wordpress eCommerce Plugin") is an open source eCommerce solution for WordPress that allows you to quickly and easily sell your physical and digital downloads using your new or existing WordPress website. [wpStoreCart](http://wpstorecart.com/ "Wordpress eCommerce Plugin") was developed only after we had spent a few years being totally frustrated with other WordPress eCommerce plugins, which we found to be generally either unprofessional, hard to use, or would break everytime we’d upgrade the plugin, not to mention most of them cost a lot money. So, we spent the last year developing [wpStoreCart](http://wpstorecart.com/ "Wordpress eCommerce Plugin") to be THE free, open source, easy to use, yet extremely powerful eCommerce plugin for WordPress that everyone has always dreamed of.  We're always looking for criticisms, suggestions, and tips on how we can make our plugin the best, so feel free to contact us on our [support forums](http://wpstorecart.com/forum/ "wpStoreCart Support Forums").  We also do custom Wordpress themes, plugins, modifications, consultation, business support, and more.  [Contact us by clicking here](http://wpstorecart.com/design-mods-support/ " ") today with your project details.
== Installation ==

For complete detail and initial configuration tutorials and documentation, please visit the [Installation Documentation](http://wpstorecart.com/documentation/installation/ "wpStoreCart Installation")

The recommended way to install wpStoreCart is to go into the Wordpress admin panel, and click on Add New under the 
Plugins menu.  Search for wpStoreCart, and then click on Install, then click Install Now.  Once the installation 
completes, Activate the plugin

Or, if you want to install manually:

1. Download the wpStoreCart.zip file
1. Extract the zip file to your hard drive, using 7-zip or your archiver of choice.
1. Upload the `/wpstorecart/` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new page, call it something like Store
1. Visit the wpStoreCart admin page and select a "mainpage" for wpStoreCart to use, like the Store page we told you to create in the last step

== Frequently Asked Questions ==

= I have questions, where do I get answers? =
- FAQ: http://wpstorecart.com/faq/
- DOCUMENTATION: http://wpstorecart.com/documentation/
- INSTALLATION: http://wpstorecart.com/documentation/installation/
- FORUMS: http://wpstorecart.com/forum/
- HELP: http://wpstorecart.com/help-support/

== Screenshots ==
 
1. No picture

== Changelog ==

= 2.0.6 =
* Added the ability to switch the sites currency (for PayPal only at this time)
* Fixed a bug where commas could not be used in product titles
* 3 new wpsc CSS themes added, which include image buttons: bright.css, small-grey.css, and blue-and-blocky
* Added the following CSS class attributes to client side elements: wpsc-addtocart, wpsc-thumbnail, wpsc-individualqtylabel, wpsc-individualqty, wpsc-qtylabel, wpsc-qty, wpsc-h1, wpsc-checkout, wpsc-empty, wpsc-update, wpsc-paypalcheckout, wpsc-authorizenetcheckout, wpsc-2checkoutcheckout
* Fixed a bug that required admin to use the shopping cart widget in order for the cart to work
* Admin CSS altered for low resolutions, buttons resized
* jQuery added to the Settings page during updates
* When you update your settings on the Settings page, the tab you were on is selected automatically
* Updated the payment gateways for wpStoreCart PRO

= 2.0.5 =
* Changed the way admin notices are displayed
* Added an error message if register_globals is set to On
* Changed the notification for when the main page and checkout page are not set
* Added the ability for wpStoreCart to automatically create and set the main page and checkout page

= 2.0.4 =
* Made Add New Category always display
* Added additional category settings such as description, thumbnail, and POST ID
* Fixed bug with categories not displaying
* Fixed bug with shipping not calculating

= 2.0.3 =
* Fixed some CSS issues

= 2.0.2 =
* Disabled error reporting in wpsc-config.php
* Tons of stuff added to the display settings
* Fixed shipping error
* Added new category settings
* Now keeping track of the database schema via version numbering

= 2.0.1 =
* Added version information to the wpStoreCart overview
* Fixed a bug on the orders page regarding notices of undefined offsets

= 2.0.0 =
* Coupon code added
* Orders in admin area now does so with a gui instead of the raw database format
* Shortcode for if a customer has bought something, then show this
* Customer registration required
* Permissions Error fixed
* Theme/CSS loading in admin panel added
* Max widths and height for product images in widgets & product pages added
* Finished the "MY Orders" download page
* Product category shortcodes added
* Dashboard widget charts & graphs in works
* Reliable code to select the last 30 calendar days implemented
* Basic stats done in admin panel
* Border around sidebar removed
* Bulk actions in edit product
* Categories say the name of the category, not just the category number, this effects most admin pages
* Send emails to customers
* Quantity at zero prevents purchase until stock is replinished and an email is sent to the admin
* Way to ignore inventory for digital downloads added
* Help section added
* Default product image added

= 1.0.0 =
* First private version of wpStoreCart

= 0.62 =
* First draft of wpStoreCart.

== Upgrade Notice ==

= 0.62 =
* First draft of wpStoreCart.
