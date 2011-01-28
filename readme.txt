=== Plugin Name ===
Contributors: jquindlen
Donate link: http://www.wpstorecart.com/
Tags: cart,ecommerce,store,paypal,shopping-cart,ecomm,e-commerce,authorize.net,affiliate,affiliates,2co,2checkout,shop,merchant,business,sales,sell
Requires at least: 2.8.0
Tested up to: 3.1.0
Stable tag: 2.1.3
Version: 2.1.3

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

= 2.1.3 =
* Added left and right currency symbols to front end
* Fixed bug with username not appearing in the User field when editing an order
* Streamlined the order page so that the interface was less cluttered and important information was more prevalent

= 2.1.2 =
* Fixed a fatal error that effected some users

= 2.1.1 =
* Forcing an update for the Wordpress.org Plugin Repository

= 2.1.0 =
* Product variations added
* Products can now have multiple downloadable files and multiple pictures added in the Add/Edit Products admin
* Progress bars added to uploads
* Admin panel upgraded to dropdown menus, CSS redone to make the interface more professional
* When editing a product, a link is now present that lets you view the product's page
* Added preliminary support for jQuery UI, which will be powering both the frontend (optionally) and the admin panel backend in future versions of wpStoreCart.
* Included the 24 default jQuery UI Themes.
* Added Donation options for product payment, supported only in PayPal payment module, but falls back to a standard purchase for other payment gateways or if the cart contains non-discount items.
* Updated the database schema so that products have a donation boolean flag
* Added the $wpstorecart_version_int variable
* $testing_mode variable added.  If set to true, this will enable bleeding edge features that may not be stable.  This should only be set to true on test sites
* TESTING MODE ONLY: Added basic Import/Export functionality.  Will be unlocked in version 2.2 and above (or sooner)
* Last 30 days gross revenue added to Overview/admin dashboard widget
* All time gross revenue added to Overview/admin dashboard widget
* Fixed issue with category thumbnails URLs not sticking inside the admin form after update
* Added .wpsc-products and .wpsc-categories CSS classes that are added to .wpsc-grid and .wpsc-list styled DIVs.
* Currency not asks for Left Currency Symbol and Right Currency Symbol

= 2.0.13 =
* Orders can now be sorted in the admin panel by user, status, processor, date, affiliate, or price.
* Fixed bug with coupon not being applied during PayPal checkout
* Images and other settings that were not correctly applied to browsing categories are fixed
* Fixed bug with checkout page not displaying for some users

= 2.0.12 =
* New shortcode added for wpStoreCart PRO users: [wpstorecart display="affiliate"] which displays the user's affiliate management panel
* .wpsc-table-tr-colorstrip and .wpsc-table-tr CSS classes added

= 2.0.11 =
* Added error messages to the registration process during checkout
* Added graceful exit via redirect to the registration process during checkout
* Added error messages in the admin panel if /uploads/wpstorecart/ or /uploads/ directories are missing and could not be created
* Fixed problem with checkout_2checkout_button and checkout_checkmoneyorder_button not displaying text
* Updated the database schema and added a new table: wpstorecart_meta.  This table holds generic information about tons of small, added detail
* New CSS class .wpsc-textarea added
* Added the ability for a manual order customer to send a note to the store admin regarding their purchase after checkout
* Updated the email that is sent after orders so that it sends from the wpStoreCart email address specified in the settings

= 2.0.10 =
* Fixed non-updating quantities in shortcode
* Made charts wider by default
* Fixed a problem with the login widget for users not using custom permalinks
* Added buttons that were missing for check/money order purchasing

= 2.0.9 =
* Fixed problem with cash/check payment gateway
* Added category widget
* Added pie chart to admin dashboard widget
* Added bar chart with last 30 days sales chart to admin dashboard widget
* Cleaned up the admin dashboard a little bit

= 2.0.8 =
* Fixed a problem with coupons not working: http://wpstorecart.com/forum/viewtopic.php?f=12&t=52#p120
* Ability to accept mailed in payments added (cash, money order, check, etc)
* .wpsc-checkmoneyordercheckout CSS class added
* checkout_checkmoneyorder_button $devOptions added
* Fixed problem with payment gateway coming up blank
* Fixed problem with shipping not getting recorded to the database

= 2.0.7 =
* New shortcode [wpstorecart display="orders"] added
* Fixed categories not displaying correctly
* Forced default thumbnails for categories, even if none was specifically specified

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

= 2.0.8 =
* wpStoreCart is an open source ecommerce solution for Wordpress.  In this patch, we fix a number of bugs.  It is highly recommended that all users upgrade immediately.
