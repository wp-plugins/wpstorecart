=== wpStoreCart ===
Contributors: jquindlen
Donate link: http://wpstorecart.com/
Tags: cart,ecommerce,store,paypal,shopping-cart,ecomm,e-commerce,authorize.net,affiliate,affiliates,2co,2checkout,shop,merchant,business,sales,sell,liberty reserve,libertyreserve
Requires at least: 2.8.0
Tested up to: 3.2.0
Stable tag: 2.3.10

The next generation of Wordpress ecommerce.  Easy to use & fully customizable, it's the store front of the future; today.

== Description ==

[wpStoreCart](http://wpstorecart.com/ "Wordpress eCommerce Plugin") wpStoreCart is a free, open source, and full featured e-commerce platform built atop of Wordpress.
It is easy to use, lightweight, heavily supported and constantly updated with new features.

**Hightlighted Features:**

* Web based "Setup Wizard"
* Unlimited number of products &amp; categories
* Product Variations such as size & color
* Product properties such as weight & dimensions
* Fully configurable product & category display
* Search products using default Wordpress search
* USPS shipping integration
* Multi-language & currency support
* Works with any theme
* Includes dedicated high end, Premium Wordpress theme for free
* Optional coupon and discount system
* Includes several widgets
* Customizable registration process
* Guest checkout
* Run your shop as "Digital Products Only", "Physical Products Only" or with both
* Products support limited or unlimited quantities
* PayPal payments (more payment processors available)
* Tested on IE 7,8,9, Opera 11, Chrome 7 and 11, Firefox 3.6, 4, and Safari 5
* **and much, much MORE!**

![The wpStoreCart Setup Wizard allows you to easily get wpStoreCart setup to sell your products.](http://wpstorecart.com/wp-content/plugins/wpstorecart/screenshot-1.jpg "The wpStoreCart Setup Wizard")

**About wpStoreCart:**
[wpStoreCart](http://wpstorecart.com/ "Wordpress eCommerce Plugin") is an open source eCommerce solution for WordPress that allows you to quickly and easily sell your physical and digital downloads using your new or existing WordPress website. [wpStoreCart](http://wpstorecart.com/ "Wordpress eCommerce Plugin") was developed only after we had spent a few years being totally frustrated with other WordPress eCommerce plugins, which we found to be generally either unprofessional, hard to use, or would break every time we’d upgrade the plugin, not to mention most of them cost a lot money. So, in March 2010, we began developing [wpStoreCart](http://wpstorecart.com/ "Wordpress eCommerce Plugin") to be THE free, open source, easy to use, yet extremely powerful eCommerce plugin for WordPress that everyone has always dreamed of.  That's a difficult thing to achieve, and to that end, we're always looking for criticisms, suggestions, and tips on how we can make our plugin the best.  Please feel free to contact us on our [support forums](http://wpstorecart.com/forum/ "wpStoreCart Support Forums").  We also do custom Wordpress themes, plugins, modifications, consultation, business support, and more.  [Contact us by clicking here](http://wpstorecart.com/design-mods-support/ " ") today with your requirements.
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
 
1. The optional "Setup Wizard"

2. More of the "Setup Wizard", this time asking what type of stuff you sell

3. Screenshot of the included default wpStoreCart Wordpress theme

4. Registration options

5. More registration options

6. Shipping options

== Changelog ==

= 2.3.10 =
* Turned testing mode and error reporting off

= 2.3.9 =
* Fixed an incompatiblitiy with Wordpress 3.2, bug [wpsc-31](http://wpstorecart.com/bugtracker/thebuggenie/wpstorecart/issues/wpsc-31 "Fixed bug wpsc-31")
* Initial support added for Quickbooks Merchant Services payment gateway (PRO only)

= 2.3.8 =
* My Orders and Downloads page now has editable form fields (optionally)
* Downloads altered to stop the attempt to turn off gzip
* Added support for the Bug Genie bug tracker, testing it with this release.  Bug wpsc-1 fixed

= 2.3.7 =
* Added a new role to Wordpress: wpStoreCart Manager
* Added the ability to change the role needed to access wpStoreCart admin pages.  Check it out from the General Settings options page.
* Exposed a pre-release version of the new wpStoreCart 3 admin design.  Check it out from the General Settings options page.

= 2.3.6 =
* New shortcode added: [wpstorecart displaytype="grid"] and [wpstorecart displaytype="list"]
* Prices of products can now be displayed on the main page
* Addressed some issues with categories rendering, including free shipping implementation

= 2.3.5 =
* Fixed issues with adding categories under IIS

= 2.3.4 =
* Added the ability to display the product's thumbnails during checkout (set it up at wpStoreCart > Settings > Display > Checkout Page > Display product thumbnails? > Yes)
* Updated wpStoreCart Default theme to version 1.1.2 (for new installs only, for other users, please use the automatic theme upgrade!)

= 2.3.3 =
* ShareYourCart integration now uses SSL by default
* New ShareYourCart shortcode: [wpstorecart display="shareyourcart"]

= 2.3.2 =
* Fixed bug that caused the My Orders & Downloads link in the login widget to point to the wrong page.
* Picture gallery debug input boxes are now hidden when editing products
* Updated wpStoreCart Default theme to version 1.1.1 (for new installs only, for other users, please use the automatic theme upgrade!)
* Now, when you change from one Mainpage to another, the product pages will automatically reassign the new mainpage as their parent.
* Added some code to try and fix the SESSION issues for some wpStoreCart users

= 2.3.1 =
* Fixed an issue with the picture galleries not rendering

= 2.3.0 =
* wpStoreCart Desktop Alert API added for wpStoreCart PRO users
* Added an optional lightbox for product thumbnail
* Added a new shortcode: [wpstorecart display="gallery" primkey="X"]
* Did some minor UI updates to the admin panel
* Added ShareYourCart.com integration for social networking marketing
* Fixed bug with store on/off feature not actually disabling the store.
* Fixed an issue with the coupons not applying during checkout at PayPal

= 2.2.9 =
* Added the ability to issue serial numbers and other information with each downloadable product.  Use the Edit Products > Downloads tab to add serial numbers
* Added the ability to specify an Orders & Downloads page in General Settings
* New email added: Email Sent When Issuing Serial Number
* Fixed a bug that had caused the emails that were to be sent after an order is approved to fail
* Added a new tab for product editing: downloads
* Fixed a bug that caused PayPal donations to be activated when they should not be
* Added the echoFields() method to the PaymentGateway() class for debugging problems in the payment gateway
* Started adding a new mode to the cart that uses Cookies instead of Sessions to store the carts contents

= 2.2.8 =
* Fixed a bug that prevented the add to cart button from being hidden when it is set to be hidden

= 2.2.7 =
* Coupons can now have a percentage discount, a flat rate discount, or both.
* Coupons can now be used on the entire cart, as well as being limited to specific products
* Added support for ThreeWP Activity Monitor plugin: http://wordpress.org/extend/plugins/threewp-activity-monitor/
* ThreeWP Activity Monitor shows all: product views, downloads, checkouts, affiliate page views, orders page views for logged in users
* Fixed a bug that happened when using "Store Mode" set to "Digital Products Only".  The bug was that inventory was not disabled in the front end, but it was disabled in the back end.
* Products now have the option to not display an "Add to Cart" button unless you're viewing the product on the products page. This setting is in the Variations tab, when editing products

= 2.2.6 =
* Product admin area now has a tabbed interface
* Inline editing of variations using Ajax (previously editing variations was not possible)
* Added Advanced Variations, including a new database table: wpstorecart_av
* Updated the system that checks for updates, and depreciated the database versioning system in favor of using $wpstorecart_version_int

= 2.2.5 =
* Updated the readme.txt file

= 2.2.4 =
* Fixed a bug with shipping calculations not working on product variations
* Fixed a bug with the Calculate Shipping prompt appearing when all items are flat rate shipped
* Added a new text field: Guest Checkout, which is displayed when registration is optional before checkout
* Fixed a number of minor issues

= 2.2.3 =
* Fixed a bug with shipping calculations coming up blank with flat rate shipping selected
* Fixed a bug with the USPS shipping calculator not calculating the weight properly when quantity was more than 1 of an item
* Finished making registration an optional requirement
* Updated the My Orders and Purchases page

= 2.2.2 =
* wpStoreCart and the wpStoreCart Theme now (mostly) passes w3c validation for XHTML 1.0 Transitional, although there's still an issue left

= 2.2.1 =
* Turned the advanced error reporting off by default (it was left on by mistake.)

= 2.2.0 =
* Added page pagination for products and categories
* Added the ability to make registration optional (still a work in progress)
* Added the ability to turn coupons on or off globally and to hide the coupon form when they are disabled
* Added the ability to create, edit, delete, and reorder optional and required profile fields for registration and/or checkout
* Added the "Setup Wizard", a new, user friendly way to quickly and easily setup wpStoreCart
* Initial "wpStoreCart needs to be configured" error message now prompts first time users to run the Setup Wizard.
* Added UPS, FedEx, and USPS shipping options with calculations (disabled on digital only stores) (work in progress)
* Added weight, length, width, and height attributes to products (disabled on digital only stores)
* Added shipping to the checkout information
* Added $devOptions['storetype'] which configures the site for physical, digital, or mixed stores
* Added integration with the wpStoreCartTheme wordpress theme.
* Added Username, Password, Email, Total, Shipping, Login, Logout, My Orders and Purchases, Required Symbol, Required Symbol Description, and Register to the language menu
* Added the ability to display shipping, subtotal, and total in the checkout page and widget, configurable from the Display Settings admin page
* Started the process of adding phpDoc blocks to the source code of wpStoreCart, for better readability and 3rd party code additions
* Removed usage of WP_PLUGIN_URL and replaced with plugins_url() to help make wpStoreCart more SSL friendly (front end only, admin not finished)
* Changes the logo to wpStoreCart PRO when PRO is installed, changed most admin images
* Disables the Wordpress 3.1 admin bar for users who are not administrators, for compatibilities sake
* Reorganized the Display Settings admin page
* Loads the Wordpress core file pluggable.php to insure compatibility
* Fixed default value missing for PayPal active
* Fixed a bug with Authorize.NET and 2CheckOut not getting the right price for multiple items
* wpStoreCart News added to the overview page
* Added advanced error reporting tools for developers working with wpStoreCart
* Added configurable page pagination
* By request, added the ability to accept Liberty Reserve with wpscPayments PRO and wpStoreCart PRO

= 2.1.8 =
* Fixed more issues with the downloads not working correctly
* Fixed a problem with variations not working for new downloads. This problem didn't effect those who upgraded to 2.1.x, only those who did fresh installs

= 2.1.7 =
* Fixed an issue with downloads not working on specific server installations.

= 2.1.6 =
* When visiting the checkout page when there are no items in the cart, the user will from now on not see any of the buttons (update, checkout, etc)
* Order page now lets you show Completed, Pending, and Refunded orders from the main admin menu and from the order sorting options
* Customers who upgrade wpStoreCart PRO installations automatically will no longer have to reinstall their PRO modules separately after the auto upgrade

= 2.1.5 =
* Fixed an issue with variation displaying unwanted output on the frontend

= 2.1.4 =
* Fixed fatal error when adding products

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
