=== IDB Ecommerce (wpStoreCart 5) ===
Contributors: jquindlen
Donate link: http://indiedevbundle.com/bundles/webdev/idb-ultimate-wordpress-bundle/
Tags: cart,ecommerce,store,shop,merchant,paypal,shopping-cart,ecomm,e-commerce,authorize.net,2checkout,qbms,skrill,wpstorecart
Requires at least: 3.3.0
Tested up to: 4.2
Stable tag: 5.0.7
License: LGPL

Selling customizable t-shirts, physical or digital items like software or games, subscriptions or anything else?  IDB Ecommerce has you covered.

== Description ==

IDB Ecommerce is the name of wpStoreCart 5. Fulfilling the promise we made when wpStoreCart first launched, we have now made all of our paid plugins 
free & open source, to make our full featured Wordpress e-commerce solution even more powerful. It is easy to use, lightweight, heavily supported and 
constantly updated with new features for over 4 years.

**Hightlighted Features:**

* Included payment gateways with the plugin are PayPal, Authorize.NET SIM, 2CheckOut, Quickbooks, and Skrill/Moneybookers
* Web based "Setup Wizard"
* Unlimited number of products &amp; categories
* Product Variations such as size & color
* Define products that are customizable by users, including uploading pictures & custom text
* Product properties such as weight & dimensions
* Fully configurable product & category display
* Search products using default Wordpress search
* Track ecommerce statistics
* Affiliate system included
* USPS & UPS shipping integration
* Multi-language & currency support
* Works with any theme
* Dedicated high end, Premium Wordpress theme for free (separate download)
* Optional coupon and discount system
* Includes several widgets
* Customizable registration process
* Guest checkout
* Run your shop as "Digital Products Only", "Physical Products Only" or with both
* Products support limited or unlimited quantities
* Advanced group settings, including groups discounts, group only products, unlimited groups
* Tested on IE 7,8,9, Opera 11, Chrome 7 and 11, Firefox 3.6, 4, 5, 6, & 7, and Safari 5
* **and much, much MORE!**

![The IDB Ecommerce Setup Wizard allows you to easily get IDB Ecommerce setup to sell your products.](http://wpstorecart.com/wp-content/plugins/wpstorecart/screenshot-1.jpg "The IDB Ecommerce Setup Wizard")

**About IDB Ecommerce:**
[IDB Ecommerce](http://indiedevbundle.com/bundles/webdev/idb-ultimate-wordpress-bundle/ "Wordpress eCommerce Plugin") is an open source eCommerce solution for WordPress that allows you to quickly and easily sell your physical and digital downloads using your new or existing WordPress website. [IDB Ecommerce](http://indiedevbundle.com/bundles/webdev/idb-ultimate-wordpress-bundle/ "Wordpress eCommerce Plugin") was developed only after we had spent a few years being totally frustrated with other WordPress eCommerce plugins, which we found to be generally either unprofessional, hard to use, or would break every time we’d upgrade the plugin, not to mention most of them cost a lot money. So, in March 2010, we began developing [IDB Ecommerce](http://indiedevbundle.com/bundles/webdev/idb-ultimate-wordpress-bundle/ "Wordpress eCommerce Plugin") to be THE free, open source, easy to use, yet extremely powerful ecommerce plugin for WordPress that everyone has always dreamed of.  That's a difficult thing to achieve, and to that end, we're always looking for criticisms, suggestions, and tips on how we can make our plugin the best.

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

== Documentation ==

= Initial Settings =
IDB Ecommerce/wpStoreCart requires a few initial settings to be configured before it will function correctly. We will discuss those initial settings and point you to the information regarding the rest of the configuration of the plugin.

If you use the included Setup Wizard, you can have IDB Ecommerce/wpStoreCart walk you through the initial setup which is described in this document. The Setup Wizard is available by clicking wp-admin > IDB Ecommerce > Wizard >

1. The first thing you’ll need to do is access your WordPress admin panel. If IDB Ecommerce hasn’t been activated, do so now. Inside the admin panel, in the left side menu, scroll down until you reach IDB Ecommerce, then click Settings.  We'll start in the General tab.
1. "IDB Ecommerce Main Page" setting – This setting should be pointed to a pre-existing WordPress page that will act as the main storefront if you will. This page must already exist, but we recommend creating a new WordPress page with the title "Store", or something similar. All products that you add will have subpages that descend from the IDB Ecommerce Main Page that you set here.
1. "Checkout Page" setting – Like the IDB Ecommerce Main Page setting, this setting is also asking for a WordPress page. This should be a different page then the IDB Ecommerce Main Page, and we recommend that you call the page Checkout to make it clear what it does.
1. Click on the "Payments" tab so begin accepting payments 

== Frequently Asked Questions ==

= **Does IDB Ecommerce/wpStoreCart have any incompatibilities with other plugins?** =
There have been reports of incompatibility with the following plugins:
 -   Wibstats
 -   Register Plus
 -   Broken Link Checker
 -   Track That Stat (only breaks coupon system)

= **What are the minimum requirements to use IDB Ecommerce/wpStoreCart?** =
 - PHP 5.3+, MySQL 5+, WordPress 4.0+

= **Why is my IDB Ecommerce/wpStoreCart mainpage empty, & what should I put there?** =
You have to create and/or select a WordPress page to act as the base or “mainpage” of IDB Ecommerce/wpStoreCart. All products that you add will be created as child pages of this page. It is up to what you place on the mainpage, but we recommend using the IDB Ecommerce/wpStoreCart shortcodes to insure that the default IDB Ecommerce/wpStoreCart pages will work.

= **How come the Checkout button does not redirect to my checkout page?** =
Goto the admin panel, then IDB Ecommerce > Settings. Find the Checkout Page setting under the General tab. In the drop down list, select your checkout page and then click "Update"

== Screenshots ==
 
1. View your orders, which appear in an Ajax table.  Click on any field to edit it.

2. Options page

3. Edit products

4. Adding a new product


== Changelog ==

= 5.0.7 =
* Updated: Replaced depreciated calls to wpdb::escape() with esc_sql() instead
* Updated: Incorrect Use of wpdb::prepare() in a few spots was fixed with esc_sql() instead

= 5.0.6 =
* Updated: The .POT file has been updated to the latest version

= 5.0.5 =
* Updated: Design your store page updated
* Fixed: A few non-critical admin text were not the correct color and were hard to read.  Fixed this.
* Updated: Admin menu edited
* Updated: USPS broken, so hiding it until it is fixed

= 5.0.4 =
* Updated: Now hides upload debug information by default in the admin panel

= 5.0.3 =
* Updated: Cleared up a potentially confusing comment regarding the license of some of the 3rd party included in the software

= 5.0.2 =
* Fixed: Cosmetic fix for empty progress bars
* Updated: Text change

= 5.0.1 =
* Updated: Compatible with 4.1 and 4.2 nightly build
* Updated: Some links are fixed, shortcode documentation updated

= 5.0.0 =
* Fixed: Adding a new Category now correctly adds the category to the drop down list of available parent categories instantly
* Added: User Customized Products is now included in the open source edition of wpStoreCart
* Added: Free Shipping is now included in the open source edition of wpStoreCart
* Added: PayPal Memberships are now included in the open source edition of wpStoreCart
* Added: Skrill/Moneybookers payment gateway is now included in the open source edition of wpStoreCart
* Added: Authorize.NET SIM payment gateway is now included in the open source edition of wpStoreCart
* Added: Quickbooks QBMS payment gateway is now included in the open source edition of wpStoreCart
* Added: UPS Shipping is now included in the open source edition of wpStoreCart
* Updated: Admin UI revised, refined, and polished
* Updated: Rebranded for new website

= 4.6.2 =
* Updated: Only loads benchmarking if benchmarking is enabled

= 4.6.1 =
* Fixed: Super Admin are now able to use admin panels for wpStoreCart

= 4.6.0 =
* Added: Affiliate functionality is now open source and included in wpStoreCart by default
* Added: Statistic functionality is now open source and included in wpStoreCart by default
* Update: Removed the hard coded debugging tools I used in the code

= 4.5.20 =
* Update: Finally remembered to update the .POT file so that all the new translatable strings are available for translation

= 4.5.19 =
* Fixed: Unnamed categories can no longer be created

= 4.5.18 =
* Update: Widgets are now i18n ready

= 4.5.17 =
* Update: Small security patch

= 4.5.16 =
* Updated: A few untranslatable strings are now i18n ready
* Updated: Fixed an if statement that was slightly malformed

= 4.5.15 =
* Updated: Updated readme.txt tags in order to be in compliance with Wordpress.org policies

= 4.5.14 =
* Updated: Corrected some the text in the Payment Wizard

= 4.5.13 =
* Fixed: Added i18n translation strings to the Tax admin panel

= 4.5.12 =
* Updated: Does not load the debugging functions unless dev mode is turned on

= 4.5.11 =
* Fixed: patched a bug preventing product thumbnails from displaying in checkout

= 4.5.10 =
* Fixed: patched a minor problem with the email system

= 4.5.9 =
* Fixed: Found a few untranslatable strings in the admin panel and fixed them

= 4.5.8 =
* Added: Free Shipping is now an option out of the box, no PRO version required.  PRO users should disable the Free Shipping plugin

= 4.5.7 =
* Fixed: Patched rare issue affecting the ability to upgrade from old 2.x versions of wpStoreCart to the current version
* Removed: Deleted unused Piwik code

= 4.5.6 =
* Added: Added bug tracking & feature requests to the help menu

= 4.5.5 =
* Updated: /languages/wpstorecart.pot file added for i18n translations

= 4.5.4 =
* Updated: Security updates for securimage captcha library

= 4.5.3 =
* Fixed: Found a few untranslatable strings in the admin panel and fixed them

= 4.5.2 =
* Fixed: Found a few untranslatable strings in the admin panel and fixed them
* Fixed: Changed sort.png to match the new Wordpress admin panel UI

= 4.5.1 =
* Added: Created a new setting to completely enable or disable the Alert system

= 4.5.0 =
* Removed: Got rid of the annoying ads!
* Fixed: Patched an issue with order note's not being properly escaped
* Added: Created a flat rate shipping plugin, and then auto integrated it into wpStoreCart core.
* Fixed: Patched an issue where adding a new attribute group would cause the ajax to fail, meaning you could not edit the new attribute or group until refreshing the page
* Fixed: Patched an issue where you could not immediately delete new product attributes unless you refreshed or reloaded the browser page

= 4.4.8 =
* Updated: QueryLoader updated from 2.1 to 2.8.3
* Updated: jQuery Superfish Menu Plugin updated from 1.4.8 to 1.7.3

= 4.4.7 =
* Removed: Deleted some unused scripts

= 4.4.6 =
* Fixed: Patched several small CSS errors

= 4.4.5 =
* Updated: Language .POT file updated to the latest revision

= 4.4.4 =
* Update: Minor cosmetic tweak.

= 4.4.3 =
* Updated: Legacy menu items updated.
* Updated: added ob_flush to downloads per this thread: http://wordpress.org/support/topic/memory-issue-in-wpstorecart-version-371-for-large-files

= 4.4.2 =
* Updated: Alerts have left beta

= 4.4.1 =
* Fixed: Patched bug with Product Designer

= 4.4.0 =
* Added: Featured Products Widget

= 4.3.2 =
* Removed: Piwik functionality that was never finished was removed

= 4.3.1 =
* Fixed: Patched a complex issue that prevented multiple variations from loading when used from individual shortcodes
* Fixed: More jQuery UI z-index fixes

= 4.3.0 =
* Added: Added the ability to create clones of product fields 
* Added: Prompt added before deleting a custom field
* Updated: Add a custom field now displays the custom field immediately after creating or editing
* Updated: Added a form to easily edit custom fields
* Fixed: Attempted to fix an issue with jQuery UI windows appearing below body content

= 4.2.5 =
* Updated: Removed the addons page

= 4.2.4 =
* Fixed: Corrected color issue on payment redirect

= 4.2.3 =
* Updates: Misc minor updates

= 4.2.2 =
* Fixed: Patched a regression from 4.2.1

= 4.2.1 =
* Fixed: Minor JS changes

= 4.2.0 =
* Fixed: Can now delete custom product fields

= 4.1.0 =
* Fixed: Patched an issue where saving products on Physical Only stores was not functioning
* Added: Ads are removed if you've already purchased everything and have them uploaded

= 4.0.4 =
* Added: Custom fields now save to Order Notes
* Removed: References to deleted js files removed

= 4.0.3 =
* Removed: Deleted all unused javascript libraries

= 4.0.2 =
* Updated: More minor admin CSS adjustments

= 4.0.1 =
* Updated: Minor admin CSS adjustments

= 4.0.0 =
* Updated: mp6 admin UI is now in place and the new default
* Removed: Rotating wheel removed
* Removed: Dashboard now redirects to the order page
* Updated: Wizard page updated
* Added: Store sales overview added to all admin pages

= 3.9.31 =
* Updated: Minor adjustments to make the addon wheel less intrusive

= 3.9.30 =
* Updated: Hiding the Custom product fields until they are ready

= 3.9.29 =
* Updated: .pot language definition file updated

= 3.9.28 =
* Added: Custom product fields added

= 3.9.27 =
* Removed: Liberty Reserve has been removed

= 3.9.26 =
* Updated: Downloads moved to the Downloads tab when editing products
* Added: Most of the code is in there for the new product fields feature, but is currently hidden

= 3.9.25 =
* Fixed: Some minor javascript fixes for variation loading

= 3.9.24 =
* Fixed: Reverted the changes from 3.9.23
 
= 3.9.23 =
* Updated: Tax calculator updated

= 3.9.22 =
* Added: Manual payments now send an email summary of the order to the admin
* Updated: Some minor tlc was done to the email system in regards to guest purchases and emails

= 3.9.21 =
* Fixed: Patched an issue with custom registration fields not always loading correctly on user profile screens

= 3.9.20 =
* Fixed: Combo Add to Cart button now displays correctly

= 3.9.19 =
* Fixed: Patched an issue with shipping city not saving correctly in some server environments

= 3.9.18 =
* Fixed: Patched issue with registration failing when collecting certain shipping information.

= 3.9.17 =
* Fixed: Patched an issue that prevented attributes from being deleted
* Fixed: Patched a minor security flaw

= 3.9.16 =
* Fixed: Patched a bug where the image size for categories was not adjusting correctly
* Fixed: Registration now explicitly starts a session if one isn't detected as started

= 3.9.15 =
* Updated: Settings page updated to remove settings depreciated from 2.x

= 3.9.14 =
* Fixed: Corrected an incorrect URL 

= 3.9.13 =
* Fixed: More Info button should now always direct to the product's page

= 3.9.12 =
* Updated: /languages/wpstorecart.pot file added for i18n translations
* Updated: Wizards was added to the Dashboard menu

= 3.9.11 =
* Updated: upgraded jquery.dataTables.min.js to version 1.9.4
* Fixed: Minor bug fixes to the shipping system
* Fixed: Patched issue where total price would display incorrectly on some orders over $1000 with shipping enabled

= 3.9.10 =
* Updated: Removed outdated links and replaced with new links

= 3.9.9 =
* Fixed: Alert settings admin window resized for correct display on lower resolutions

= 3.9.8 =
* Updated: Even more of the admin panel is translatable 

= 3.9.7 =
* Updated: More of the admin panel is translatable 

= 3.9.6 =
* Updated: Did the same thing with the Main Page designer, prompting admin's to create and set the Main Page before attempting to use the Main Page designer.

= 3.9.5 =
* Updated: If you have no products published and try to use the Product Designer, it will now inform you to create a product first instead of taking you to a 404 page

= 3.9.4 =
* Fixed: Patched a bug with pagination when the new Ajax Product Filtering (BETA) was enabled

= 3.9.3 =
* Added: Ajax Product Filtering (BETA) option added to wp-admin > wpStoreCart > Settings > Display > Main Page >
* Updated: wpscProductGetGrid() function has been depreciated

= 3.9.2 =
* Updated: Minor admin updates, including wpStoreCart 4 admin beta update

= 3.9.1 =
* Fixed: When manually adding a new order, email was not sending even when selecting to notify the customer. This is now resolved.
* Fixed: Patched an issue with datatables displaying the view invoice icon on non-orders
* Added: wpStoreCart 4 admin panel redesign beta included, but hidden. If you activate the mp6 plugin and then delete the /wpstorecart/images/ folder and then rename the /wpstorecart/images_new/ folder as /wpstorecart/images/ then you will activate the full hidden wpStoreCart 4 admin panel.
* Fixed: Minor admin UI issues

= 3.9.0 =
* Updated: jQuery UI theme updated for jQuery UI 1.10.x in Wordpress 3.6 alpha and above
* Added: Created the new wpscProductGetNameById($id) function to quickly grab a product's name from it's ID
* Updated: Adjusted the coupon page in advance of Wordpress' upcoming admin redesign (MP6)
* Updated: Minor admin UI updates

= 3.8.1 =
* Fixed: Patched an issue with shipping tab still bleeding information into edit products screen when store set to Digital Products Only
* Updated: Updated prices inside the addon wheel

= 3.8.0 =
* Fixed: fixed an issue with affiliates
* Added: added the wpscProductSelectDropdown() function

= 3.7.2 =
* Updated: Some strings that weren't translatable now are ready for translation

= 3.7.1 =
* Fixed: Patched minor issue with new installations and default images

= 3.7.0 =
* Fixed: Patched an issue with the Individual Product Page Designer not saving correctly

= 3.6.0 =
* Added: View Page added to Edit Product
* Fixed: Corrected an issue with [wpstorecart display="categories" thecategory="2"] not working under certain conditions

= 3.5.0 =
* Fixed: wpsc Membership PRO display and edit product issues resolved.
* Fixed: Patched an issue where the z-index of the slider went over the Combo admin panel, causing some buttons to be unclickable
* Fixed: Misc CSS and HTML formating fixes on the Edit Products - Accessories tab
* Fixed: Patched an issue where picture gallery photos were not deleting correctly from Edit Products > Pictures tab.
* Updated: Darkened the Users Pages and Checkout Designers so that users can tell that the feature is currently unavailable

= 3.4.4 =
* Fixed: Corrected an issue with the wizard not enabling manual payment gateway

= 3.4.3 =
* Fixed: Corrected an issue with the manual payment gateway not showing details after order.

= 3.4.2 =
* Fixed: Corrected an issue with [wpstorecart display="categories" thecategory="2"] displaying all categories instead

= 3.4.1 =
* Fixed: Patched an issue that affected users who forced PHP notices to display that prevented from successful purchased emails from sending
* Fixed: Patched an issue where the shipping tab on the Edit Products admin page was missing.

= 3.4.0 =
* Updated: Due to popular demand, categories shortcode and frontpage settings now work the same as they used to in wpStoreCart 2.x
* Fixed: Category thumbnail picture uploading has been restored
* Updated: Alerts can now be cleared directly from the admin dashboard
* Updated: Added a clear:both inline style so that product page navigation would remain consistant

= 3.3.1 =
* Updated: Added links to the admin invoice page for each order in orders db. 
* Fixed: Patched multiple minor issues regarding orders, invoices, and admin user profile viewing

= 3.3.0 =
* Fixed: Patched an issue where add to cart did not work if you did not have a checkout widget in your sidebar/footer etc.
* Fixed: Patched a small warning that appeared when saving products via Ajax on servers that forced warnings to be displayed

= 3.2.0 =
* Fixed: Import/Export CSV functionality has been restored
* Fixed: Fixed an issue with customer profile fields returning Array Array() instead of the profile fields

= 3.1.6 =
* Updated: Added all available addons to the slider.

= 3.1.5 =
* Updated: Changed the ad slider so that it doesn't display products you already have. 

= 3.1.4 =
* Added: The admin menu for Edit Products now expands with upto 15 products for you to edit
* Updated: Updated jQuery Sparkline from 1.5 to 2.1

= 3.1.3 =
* Fixed: Patched an issue where the inventory was not working properly for attributes

= 3.1.2 =
* Added: A whole new set of customer side catalog filters have been added. Full details: https://wpstorecart.com/documentation/wpstorecart-3/developers-api/extending-products/

= 3.1.1 =
* Added: There is now a Delete All Pending Orders button on the Orders page. Fulfilling a request.

= 3.1.0 =
* Updated: Improved compatibility with wpStoreCart Desktop Alert version 3
* Fixed: Coupons for attributes and variations now correctly display the master products name before the variation or attributes name
* Fixed: All data tables where products can be selected now correctly display the master products name before the variation or attributes name

= 3.0.18 =
* Fixed: Patched an issue with Advanced Attributes not working as intended

= 3.0.17 =
* Fixed: Removed yellow background on Advanced Category widget and replaced with transparent background
* Added: New jQuery/JS console logging enabled when wpStoreCart is in testing mode.  http://happygiraffe.net/blog/2007/09/26/jquery-logging/
* Added: Now includes the Spectrum.js color picker http://bgrins.github.com/spectrum/

= 3.0.16 =
* Fixed: Adding more items to the cart than are in stock has been patched

= 3.0.15 =
* Added: A whole new set of customer side product filters have been added.  Full details:  https://wpstorecart.com/documentation/wpstorecart-3/developers-api/extending-products/
* Added: colorbox.js added

= 3.0.14 =
* Updated: Replaced NicEdit with CLEditor due to compatiblity issues with NicEdit.
* Updated: More action hooks added https://wpstorecart.com/documentation/wpstorecart-3/developers-api/extending-products/

= 3.0.13 =
* Added: A whole new set of product action hooks have been added.  Full details here: https://wpstorecart.com/documentation/wpstorecart-3/developers-api/extending-products/

= 3.0.12 =
* Added: The variation system has received some TLC to make choosing between variation types more intuitive.  
* Misc bug fixes

= 3.0.11 =
* Just trying to get the Wordpress SVN server to correctly parse the plugin to resolve the updating/download issues

= 3.0.10 =
* Just trying to get the Wordpress SVN server to correctly parse the plugin to resolve the updating/download issues

= 3.0.9 =
* Fixed: Some users had issues updating to 3.0.8, which I think was caused by the zip file size.  I have deleted tons of unnecessary files from wpStoreCart, like multiple jQuerys, Thumbs.db, and other wastes. 

= 3.0.8 =
* Fixed: Restored missing UI elements and icons in the Designer
* Fixed: Patched an issue where the Storefront Designer was not functioning correctly

= 3.0.7 =
* Added: New Advanced Category widget featuring expandable hierarchy of categories 

= 3.0.6 =
* Fixed: Patched an issue on the Edit Products screen preventing paged mode from functioning correctly
* Added: Introduced the $wpscPaymentGateway['final_price_with_discounts'] value for payment gateways
* Fixed: Fixed an issue with orders over $1000 on some payment gateways

= 3.0.5 =
* Updated intro video

= 3.0.4 =
* Updated screenshots

= 3.0.3 =
* Fixed: Patched an issue where the newsales() alert was not being reset correctly
* Added: New Shipping tab in edit products admin panel
* Worked on getting the shipping API finished.  Made several changes and patched some issues.
* Fixed: Google Charts would not display on SSL secured sites.  Switched to https://chart.googleapis.com/chart which fixes the issue
* Added: wp_wpstorecart_packages database table added
* Added: Quick validation added to Guest checkout
* Updated: Login widget addressed so that it better handles guests, including allowing guest to sign out
* Added: Alerts via Text messaging completed
* Added: Alerts via Email completed
* Added: Admin panel is now responsive to screen size.
* Fixed: Hiding prices for guests patch
* Fixed: Previous users who are missing a required registration field must enter in the missing information before completing checkout.
* Fixed: Guests now have to fill out all required information, to enable shipping for guests to function properly
* Added: Upgrade/App Store Added

= 3.0.2 =
* wpStoreCart 3 BETA 2 released
* Fixed: Setting an Alert to wp-admin = OFF now correctly hides the alert from the admin panel

= 3.0.1 =
* wpStoreCart 3 BETA 1 released

= 2.5.45 =
* Updated: Settings updated

= 2.5.44 =
* Updated: 2 year anniversary sale announced

= 2.5.43 =
* Fixed: Google Charts would not display on SSL secured sites.  Switched to https://chart.googleapis.com/chart which fixes the issue

= 2.5.42 =
* Updated: Warning added regarding wpStoreCart 2.5.x being discontinued after the release of wpStoreCart 3

= 2.5.41 =
* Warning added regarding wpStoreCart 2.5.x being discontinued after the release of wpStoreCart 3

= 2.5.40 =
* Fixed: Fixed a bug where the Quickbooks & Moneybookers checkout buttons wouldn't appear after calculating shipping costs

= 2.5.39 =
* Updated: PayPal changed the way IPN works.  This patches wpStoreCart to work with the new changes. 

= 2.5.38 =
* Updated: Replaced dead URLs with corrected versions

= 2.5.37 =
* Fixed: Call to undefined function wpscMakeEmailTxt() in wpstorecart.php on line 13228

= 2.5.36 =
* Fixed: Corrected an issue with notices and warnings when sending serial numbers
* Fixed: Corrected an issue where [sitename] and [downloadurl] were not being correctly substituted when sending the serial number email

= 2.5.35 =
* Updated: Added list of installed Wordpress plugins & themes to the diagnostic data

= 2.5.34 =
* Updated: Replaced dead URLs with corrected versions

= 2.5.33 =
* Fixed: Fixed innappropriate white pixels in the small-grey and bright button themes

= 2.5.32 =
* Fixed: Removed dead URLs from context help.
* Updated: mail() function replaced with wp_mail()

= 2.5.31 =
* Fixed: Patched an issue where very large order amounts were not being correctly passed to payment gateways

= 2.5.30 =
* Fixed: Security vulnerability patched

= 2.5.29 =
* Fixed: Additional issues with wpsc Membership PRO & the wpStoreCart Default theme were fixed.

= 2.5.28 =
* Fixed: Issue with wpsc Membership PRO & more than one subscription based product was fixed

= 2.5.27 =
* Updated: Minor admin panel updates

= 2.5.26 =
* Updated: Updated to latest ShareYourCart release

= 2.5.25 =
* Fixed: Bug report wpsc-158  -  Coupon amount not being taken off 

= 2.5.24 =
* Fixed: Security vulnerability patched

= 2.5.23 =
* botched release, no difference between this and 2.5.22

= 2.5.22 =
* Fixed: Corrected a couple of typos

= 2.5.21 =
* Updated: jQuery Sparkline upgraded from version 1.5.1 to 1.6
* Updated: jQuery Imagemapster upgraded from version 1.2 beta 2 to ImageMapster 1.2.4

= 2.5.20 =
* Fixed: Patched an issue with detecting whether or not the main page is set
* Updated: Removed unused variables and did some general code housekeeping

= 2.5.19 =
* Wordpress 3.4 Beta 1 compatible

= 2.5.18 =
* Fixed: Removed references to WP_PLUGIN_URL and replaced them with plugin_url() which supports SSL

= 2.5.17 =
* Fixed: Patched an issue where register_globals was reported as on when it was really off

= 2.5.16 =
* Added: Captch security added to guest order lookup

= 2.5.15 =
* Updated: Updated to latest ShareYourCart point release

= 2.5.14 =
* Fixed: Did some things to try and minimize the email issues users have been reporting.

= 2.5.13 = 
* Updated: Updated to latest ShareYourCart point release
* Updated: Added the allowguests attribute for the haspurchased shortcode, to allow guests who have purchased access to hidden content.

= 2.5.12 =
* Updated: Updated to ShareYourCart 2.0
* Added: Benchmarking 

= 2.5.11 =
* Fixed: Patched an issue where emails were not sent out if the sendmail_from address did not match the wpStoreCart admin email, which should improve the number of emails successfully sent

= 2.5.10 = 
* Fixed: Patched an issue where editing the body of emails would result in cascading escaping issues
* Fixed: Patched an issue where the edit title field wouldn't properly display double quotes
* Added: Support for wpStoreCart.com authentication

= 2.5.9 = 
* Fixed: Importing CSV is now much more compatible than before, with several enhancements

= 2.5.8 = 
* Fixed: Cleaned up issues that were preventing XHTML validation (transitional)

= 2.5.7 = 
* Fixed: An extra space someone got placed at the top wpstorecart.php in 2.5.6.  This release simply removes the extra space

= 2.5.6 =
* Fixed: Spelling & typo corrections

= 2.5.5 =
* Added: Began adding unit testing to wpStoreCart.

= 2.5.4 =
* Fixed: patched an issue that allowed html from a product to be displayed in wp-admin > wpStoreCart > Edit Products
* Added: Mockpress added to assist in automatic unit testing using PHPUnit

= 2.5.3 =
* Fixed: Testing mode was enabled accidentally on 2.5.2.  This update simply turns it off.

= 2.5.2 = 
* Added: Related items & accessories, including discounts and combo packs.
* Added: By default, clicking add to cart will now redirect to the checkout page
* Fixed: there was an issue with group discounts not applying to the main page.  This has been fixed.
* Updated: Switched to NicEdit instead of TinyMCE in the administration due to new issues with TinyMCE and Wordpress 3.3 beta
* Updated: Added Qty (quantity) to the Language settings
* Fixed: the invisible login and register buttons should now appear
* Fixed: patched a few minor issues with Quickbooks Merchant Services payment gateway
* Fixed: Uploads will now attempt to chmod to 655 permissions automatically
* Updated: Updated to ShareYourCart version 2.0 (Commented out while issues are resolved however)
* Fixed: Login widget now takes the "Other button classes" and has the .wpsc-button and .wpsc-login-button classes attached to it
* Updated: By default, new installs will now see the price in grid and list views
* Fixed: There were multiple CSS issues on the Overview/MyStore page that were fixed
* Added: XDebug support added when using TESTING MODE.  This is so that the wpStoreCart developers can use XDebug to easily debug the Ajax code in Netbeans.
* Fixed: 2CO now redirects after IPN call
* Updated: Every method inside the wpStoreCart class now contains proper PHPDOC blocks.
* Updated: PHPUnit testing implementation started

= 2.5.1 =
* Fixed: There was an issue with the settings variable being renamed. 

= 2.5.0 =
* Updated: Changes the Orders and Coupons pages so that they match the rest of the admin panel by having icons
* Added: Customers admin page
* Added: Add Users to Group admin page
* Added: Added Customer Profile, including the ability to see what each user currently has in their cart
* Added: Added a new Detailed Order admin page
* Updated: Improved the remove from cart functionality
* Added: Got wpStoreCart prepared for Wordpress 3.3.  wpStoreCart 2.4.x had several issues with Wordpress 3.3 beta 1, which are fixed in this version.
* Fixed: Resolved an Authorize.NET error message
* Updated: Database schema updated for the wpstorecart_categories table, adding 4 new fields to enable the group discount functionality.
* Added: You can now uninstall wpStoreCart completely, deleting all products, settings, coupons, orders, etc.
* Fixed: Bug on Edit Product page that sometimes resulted in hidden products on the sortable page.

= 2.4.14 =
* Fixed: Page pagination now displays correctly on ALL category pages.
* Updated: Stream lined the main wpStoreCart menu in the Wordpress admin panel

= 2.4.13 =
* Fixed: Page pagination now displays correctly on category pages.

= 2.4.12 =
* Added: Skrill/Moneybookers added to wpStoreCart PRO and Payments PRO.

= 2.4.11 =
* Fixed: Subcategories now display when you click on a parent category.

= 2.4.10 =
* Added: The Category page now allows you to upload images directly
* Added: The product Add and Edit pages now show the current thumbnail for the product
* Fixed: Corrected class attributes when browsing products from specific categories.
* Updated: Changed the affiliate and statistics page for regular users
* Updated: Add and Edit products no longer displays the description by default.
* Updated: Category page no longer asks for post ID
* Fixed: There was an issue on wpStoreCart installations that do not properly set the cart's session. The issue was that during checkout, when a user registered, it would drop all their cart contents.  This has been fixed.

= 2.4.9 =
* Fixed: Fixed several issues with the diagnostics screen showing a blank textarea.  Now, regardless of the restrictions on Apache and PHP, it will show whatever diagnostics information it can.
* Added: The wpstorecart shortcode can now be used with other shortcodes.  For example, this shortcode combination now works: [wpstorecart display="haspurchased" primkey="X"] [another_shortcode] [/wpstorecart]
* Fixed: Removed a hard coded reference to my local database table and replaced it with the correct dynamic wpdb table name

= 2.4.8 =
* Fixed: there was an issue with 2.4.6 for people who were upgrading.  2.4.7 didn't update the stable tag in this readme.txt file

= 2.4.7 =
* Fixed: there was an issue with 2.4.6 for people who were upgrading.

= 2.4.6 =
* Fixed: wpsc-14 Sessions cart issues fixed! http://wpstorecart.com/bugtracker/thebuggenie/wpstorecart/issues/wpsc-14

= 2.4.5 =
* Fixed: Select/Unselect All in Edit Product admin page now works correctly
* Added: In the Edit Product admin page, drag and drop the order you wish products to appear on the front end.
* Added: In wp-admin > wpStoreCart > Settings > Display > Main Page > Content of the Main Page > added a new option: List all products in custom order
* Fixed: Removed excess information from the Edit Products admin page

= 2.4.4 =
* Fixed: Updated the text on the Products page to no longer reference the ability to specify URLs for thumbnails.
* Added: New shortcode.  Display an Add to Cart button for any product, using [wpstorecart display="addtocart" primkey="3"] etc.
* Added: Started adding the first of many Wordpress actions and filters.  By version 3.0, we intend for wpStoreCart to have almost everything available via an action or filter, for full modification

= 2.4.3 =
* Added: You can now delete individual product downloads from the Downloads tab in Edit/Add Product admin panel.  This will also attempt to remove them from the file system

= 2.4.2 =
* Added: Support for wpsc Membership PRO added
* Removed: Default wpStoreCart theme no longer included.

= 2.4.1 =
* Added: You can easily add CSS classes to any Add to Cart, Checkout, or other wpStoreCart produced button from Settings > Display >
* Fixed: PayPal success page now specifies the correct page
* Added: Ability to manually specify the PayPal IPN URL (advanced users only)
* Added: New Diagnostic page added, to help users help the admin solve bugs

= 2.4.0 =
* Added: Admin panel now has drop downs for all administrative menus
* Added: Enhancement wpsc-5 - New payment failure page - http://wpstorecart.com/bugtracker/thebuggenie/wpstorecart/issues/wpsc-5
* Fixed: wpStoreCart Default theme now displays the Home link if you set it to in the admin panel 
* Fixed: wpStoreCart is now working on WPMS, as long as you don't try and "Network Activate"  Individual WPMS sites should have no problem using wpStoreCart individually.
* Fixed: There was a recently discovered issue with product downloads which contained apostrophes.  This has been fixed
* Fixed: Solved an issue with exporting via CSV with line breaks.
* Fixed: CSV importing now works!  Yea!
* Fixed: Updated SQL exporting to the latest database schema for the products table
* Updated: The register/login/guest form on the checkout page is now in tables by default
* Added: Taxes are now fully operational!!! http://wpstorecart.com/bugtracker/thebuggenie/wpstorecart/issues/wpsc-4

= 2.3.17 =
* Added: You can now specify a regular and a discount price.  If a discount price is present, then the regular price is crossed out, replaced with the discount price.
* Fixed: Stripslashes was added to most output

= 2.3.16 =
* Added: Custom menu support to wpStoreCart Default theme
* Added: Post thumbnail support added to wpStoreCart Default theme
* Added: Can now toggle between HTML and Rich Editor in the products description
* Added: Draft and Publish modes for products. When you click the Add Product menu, it now immediately creates a new draft product & page.
* Added: Client side form validation added to the admin panel on Add/Edit products page.
* Added: When a PayPal dispute or claim is settled, instead of marking the Order Status as Canceled_Reversal, it is now remarked Completed, and the order is made available to download again
* Fixed: The edit product page now detects if your product page was deleted, and will optionally recreate a new one, or allow you to edit the product without a page associated with it
* Fixed: Bug report wpsc-37 - http://wpstorecart.com/bugtracker/thebuggenie/wpstorecart/issues/wpsc-37
* Fixed: Bug report wpsc-49 - http://wpstorecart.com/bugtracker/thebuggenie/wpstorecart/issues/wpsc-49
* Fixed: Bug report wpsc-11 - http://wpstorecart.com/bugtracker/thebuggenie/wpstorecart/issues/wpsc-11
* Fixed: Removed depreciated attribute_escape() function calls and replaced them with esc_attr()
* Fixed: All wpStoreCart Default theme issues listed here: http://themes.trac.wordpress.org/ticket/4149

= 2.3.15 =
* New wpStoreCart admin panel interface finished and made default
* Fixed a bug with the wpStoreCart 3 beta menu system disappearing when updating any setting
* Added more PHPDOC blocks to whip this code base into order before the big revisions really begin
* Added /wpstorecart/php/class.shipping.php which allows UPS shipping quotes without needing to signup to their process
* Removed the jqueryui folder from the plugin

= 2.3.14 =
* Added the ability to determine the order in which the quantity, pic, name, price, & remove are displayed on the checkout page & widget wpStoreCart > Settings > Display > Checkout Page > Field Order
* Added the ability to switch between a new table based checkout or the original DIV based checkout wpStoreCart > Settings > Display > Checkout Page > Checkout XHTML Type
* Icky.  Used a few inline styles on the registration/checkout process.  You can turn this off from wpStoreCart > Settings > Display > Checkout Page > Disable Inline Styles? set to Yes
* jQuery UI Theme setting in wpStoreCart > Settings > Display > has been depreciated. If you want jQuery UI styles, then use a plugin dedicated to that. jQuery UI CSS markup is still present in wpStoreCart
* Fixed a bug which caused the product title to be displayed on the main page, even when it was set to Off
* Added new shortcode options orderby and ordertype, see the shortcode documentation for full details

= 2.3.13 =
* Updated the readme.txt file

= 2.3.12 =
* User profiles are now optionally editable from the My Orders & Downloads page. Configure this at wpStoreCart > Settings > Display > My Orders & Downloads Page > Profile Editable?
* Fixed bug wpsc-40 : http://wpstorecart.com/bugtracker/thebuggenie/wpstorecart/issues/wpsc-40
* Fixed the Display settings tab, as it was incorrectly labeled Products since 2.3.11

= 2.3.11 =
* Fixed Bug report wpsc-26
* Fixed the Wordpress admin footer on wpStoreCart pages, to make it compatible with Wordpress 3.2
* Changed the logo size for Wordpress 3.2
* Admin tab buttons recolored to match Wordpress 3.2

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

= 4.5.8 =
* PRO users should disable the Free Shipping plugin because Free Shipping is now an option out of the box, no PRO version required. Failure to do so will result in 2 Free Shipping options

= 2.5.24 =
* Important Security Update! Please update now!

= 2.3.16 =
* Please backup your wpStoreCart Default theme before upgrading. This will over write the wpStoreCart Default theme, including any changes you did to the theme.  This is why you should only use CHILD THEMES when editing a theme.
