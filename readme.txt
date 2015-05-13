=== IgnitionDeck ===
Contributors: virtuousgiant
Donate link: http://IgnitionDeck.com
Tags: crowdfunding, crowd, funding, ecommerce, commerce, marketplace, order, transaction, widget, skin, fundraising
Requires at least: 3.2
Tested up to: 3.8.1
Stable tag: 1.3.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A powerful crowdfunding plugin for WordPress.

**Plugin not yet active, no files to download (Updated 3/24/14)**

== Description ==

**Plugin not yet active, no files to download (Updated 3/24/14)**

A crowdfunding plugin for WordPress that features the ability to add an unlimited number of crowdfunding, pre-order, or fundraising projects to your WordPress website. IgnitionDeck works with any of the thousands of themes available for Wordpress, offers an incredible amount of free and premium extensions, and is incredibly easy to customize.

== Installation ==

http://docs.ignitiondeck.com

== Frequently Asked Questions ==

== Upgrade Notice ==

== Screenshots ==

== Changelog ==

= 1.3.9 =

* Public facing backer profile added to IDC Dashboard (IDE)
* Facebook metadata added to HTTP headers on project pages which results in proper variables being sent to Facebook during Like/Share
* Drafts can now be previewed by project submitter/owner (IDE)
* Automatic pointing to proper project page for non-50o IDE installations (IDE)
* Disable purchase form submit after click to prevent multiple submissions
* Fix default thank you URL
* Better handling in removal of WP SEO hook (IDE) which results in a proper working relationship between WP SEO and IgnitionDeck
* Optimize getProductDefaultSettings function
* Update outdated or incorrect shortcode listings
* Remove default terms and conditions text so that it doesn't display if not edited
* Echo cURL error on license key validation, if error exists

= 1.3.8 =

* Added dropdown to choose between 100% funding and capture mode on FES (IDE)
* Prevent level title, price, and limit from being edited after project is published (IDE)
* Fix numerous bugs with FES levels (IDE)

= 1.3.7 =

* Fix decimal bug on PWYW projects
* Delete projects when uninstall plugin
* Update translation strings
* Fix level limit bug causing some levels to not link
* Disallow edit of level pricing on live projects (IDE)

= 1.3.6 =

* Commerce and customization now requires a license key be entered in order to function
* Add category to grid shortcode
* Add 'default' option to purchase and thank you page drop down selector
* Fix percentage bar on Deck widget
* Fix email settings save bug
* Added project status message to new project submissions and edit project page
* Fix FES form to properly save radio, select, and checkboxes (IDE)
* Hide license key from non-super-admins on multisite (IDE)
* Optimize front-end and admin js/CSS to load only when necessary
* Disable autosave on ignition_product in order to prevent broken project ID
* Improved license key status message
* Fixed multiple issues with image uploads on FES form (IDE)
* Fixed bug with level links for sold out levels
* Add persistent help/documentation links to admin menus
* Fix links to Aweber and Mailchimp in email settings menu
* Hide trashed projects from 'My Projects' screen

= 1.3.5 =

* Fix numerous bugs with FES including image4 and end type saving (IDE)
* Remove project type selector to default to admin selection (IDE)
* Add new project_category taxonomy to the ignition_product post type
* Add support for project_category to FES (IDE)
* Fix bug resulting in Paypal item number always being set to 0
* Fix significant multisite bug resulting in broken admin (IDE)
* Update FES to take on skin colors (IDE)
* Fixed project raised shortcode bug
* Add author to IgnitionDeck Project Screen
* Fix embed code icon
* Upgrade to Font Awesome 3
* new ide_fes_redirect filter (IDE)

= 1.3.4 =

* Creator profile added to FES form and front-end project display (IDE)
* Limit number of editable fields on live user-submitted projects (IDE)
* Add FAQ to FES creation and edit (IDE)
* Fix number format bug on percentage bar
* Alert if PWYW is left blank
* Strip non-numeric characters from PWYW field
* Fix level save bug on front-end submission (IDE)
* Unlink levels on closed projects that have ended
* Fix date format on FES (IDE)
* Visual indicator that key is valid or invalid
* Fix multiple bugs on FES form that led to missing and/or duplicate posts/fields (IDE)

= 1.3.3 =

* Fixed issue with missing project id in project and purchase URL's
* Stripslashes on product name in embedded deck
* Remove deprecated 'products' folder
* Updated translations
* Fix to purchase URL query string
* Update Mailchimp function to fix multiple errors

= 1.3.2 =

* Updated styling on my projects tab to include view/edit actions
* New default purchase and thank you URL option available from product settings screen
* Solve duplicate projects display after editing on front end
* Better fallback from image thumbnails
* Fix display of project images on front end edit
* Third option in email settings to disable auto-subscription after purchase
* Fix purchase receipt value on recurring payments

= 1.3.1 =

* Support for IgnitionDeck Enterprise (0.5)
* Updated translation strings
* Fix bugs related to bbPress protection
* In absence of timezone string, set to default of UTC
* New Form class
* IDE users can remove 'powered by IgnitionDeck' text on embedded deck
* Introduction of following IDE features:
* 

* Front End Submission
* Stripe Connect
* Creator account registration and account menus



= 1.3 =

* New extensions menu allows you to browse/purchase extensions from within WordPress
* Level order is now customizable
* Use your license key to automatically receive updates
* Embed widget code now displays underneath video next to other share buttons
* New global currency code
* New class methods
* Minor styling fixes on widgets, deck, and purchase form
* Switch from Timthumb to WP thumbnail
* Added license key functionality
* Fix bug allowing purchase of level 1 even when sold out
* Updated translation strings
* Security updates

= 1.22 =

* Fix missing function declaration
* Include language files (.po/.mo) in languages folder

= 1.21 =

* Fixed bad jQuery selector breaking some menus in FF
* Fixed edit order bug that was causing currency code to insert into price data
* New cron job to update ign_days_left and ign_percent_raised postmeta
* Minor CSS/HTML display updates
* Fix bug preventing projects from being completely deleted
* Ensure default Paypal email is used if custom address is empty
* Ensure all date/time functions use WP timezone
* Fix issue causing hidden embed widget button in admin

= 1.2 =

* Deck builder allows you to build your own instance of the deck via shortcode or widget
* Re-designed skins
* Grid shortcode, using grid="x" for width, and max="y" for maximum number of projects
* Deck levels (via shortcode and widget) are now selectable
* Upgrade Font Awesome to 3.2.1
* Consolidate js
* Strip slashes from projects in drop downs
* Update multiple functions based on new class methods
* new id_pre_order_delete and id_order_delete hooks
* Prevent URL hacking on purchase form. Sold out levels load drop down instead.
* Update datepicker.js to latest
* Minor markup updates, most notably, removal of id's from deck template
* XSS vulnerability patch on purchase form
* Hourly cron to update new meta field, ign_fund_raised

= 1.11 =

* Prevent URL hacking on sold out levels
* Update admin styling to metabox layout
* Cleanup of templates and CSS to prepare for deck builder and skin re-design
* Ensure jQuery enqueues before other scripts
* Fix days left function
* Fix complete shortcode float issue
* Set default project type to level based to prevent errors when not set
* Better support for IDFU extension
* Default to USD when currency not set
* Make sure all variables are sanitized prior to sending to Paypal (Matthieu Aubry)
* Prevent Facebook.js from loading on purchase form
* Remove supporter exclusive skin from package

= 1.1 =

* Support for level short descriptions (currently not used)
* Purchase form will accept $_GET var 'level' in URL and auto-select that level
* Fixed Adaptive Payments bug causing test App ID to be used when in live mode
* Admin nags can be cleared and will not return
* Easily check/clear all checkbox fields in admin menus
* New hook 'id_order_update' that returns order id
* New filters 'id_level_after' , 'id_widget' , 'id_mini_widget'
* Filter default metaboxes via 'id_postmeta_boxes' filter
* Major additions to ID_Project class
* New Deck, Order, Project, Purchase Form, and Widget classes
* Updated project postmeta class
* Add commas to number_format function in deck
* Aweber support fixed and upgraded
* Purchase form now filterable via 'id_purchase_form' filter
* Complete rewrite of all shortcodes using new classes
* Several purchase form bugfixes to better handle pay selection, button active/disabled states, and json parsing
* Further isolate javascript from admin and front-end
* Removed deprecated functions and files
* Updated text variables
* Hide mini-deck image if it doesn't exist
* Minor CSS improvements
* Cleanup admin notices and errors found in debug mode
* Option to bypass success hook when manually adding orders
* Update activation, deactivation, and deletion hooks to be more efficient
* Added minimum price of 99 cents in order to better handle pricing issues
* Fixed social buttons template path
* Fix -custom.css include

= 1.03 Bug Fixes =

* Fixed 'Payal' misspelling
* Fixed preview bug that was breaking projects. You are now free and clear to use preview!

= 1.02 Updates =

* Use separate Paypal email for each project (works with Paypal Standard only)
* Use of new ID_Project class
* Back-end rewrite of custom settings menu
* New id_after_install, id_set_defaults hooks

= 1.02 Bug Fixes =

* Fixed order details level 1 display issue

= 1.01 Updates =

* Removed deprecated code
* Improved Paypal Form and IPN Handling
* Admin nags remind you to save settings and create first project

= 1.01 Bug Fixes =

* Fixed Paypal IPN handling on amounts greater than 999
* Fixed thank you URL bug that sometimes led to 404

= 1.0 Updates =

* Removed unused js
* Minor CSS improvements
* Auto-loading purchase form data based on shortcode when no GET vars detected

= 1.0 Bug Fixes =

* Strip slashes from Paypal form data
* Fixed thank you URL encoding issue that led to 404

= 1.0 RC3 Updates =

* Upgraded Font Awesome to 3.1.1
* Additional hooks and filters in project content, decks, and shortcode list
* Improved front-end CSS to account for theme inconsistencies
* Support for Stretch Goals extension

= 1.0 RC3 Bug Fixes =

* Fixed occasional missing div in full deck shortcode
* Fixing unset variables in email settings
* Fixed bug in send to mailchimp function

= 1.0 RC2 Updates =

* Removed public error function
* Update to CMB_META_BOX_URL function to better set location

= 1.0 RC2 Bug Fixes =

* Fixed level 1 limit bug
* Fixed issue causing emails not to add to list
* Stripping commas from price on PWYW form

= 1.0 RC1 Updates =

* Dropping all ign_ tables as part of uninstall
* Better support for foreign characters in product/level titles and descriptions
* Major security update to better sanitize GET and POST data
* Removed deprecated install function
* Removed deprecated FAQ/Updates files
* Minor visual updates
* Purchase form button says 'processing' after click to provide visual cue
* Order menu now sorts descending by order ID
* Converting timestamps to 24 hour format
* No longer showing project ID in shortcode list until project is saved

= 1.0 RC1 Bug Fixes =

* Loading postmeta file only when creating or editing post/page. Solves unexpected output error on activation
* Removed a variety of debug errors, warnings, and notices
* FB script now loads properly on posts and pages
* Fixed custom settings purchase form bug
* Updated project admin to uniformly display pricing
* Ensured missing project details do not prevent insertion of new product
* Several fixes to order details menu to improve sorting

= 1.0 Beta5 Hotfix 9 Updates =

* 100% funding added to Paypal Adaptive Payments
* HTML support for level displays
* Ajax validation for level/purchase price on level based projects
* Removal of multiple unused functions
* Minor changes to database schema
* Improvements to shortcode HTML/CSS
* Improved security
* Font Awesome added to core
* Categories added to project data
* Removed unused submenus that were hidden in sidebar

= 1.0 Beta5 Hotfix 9 Bug Fixes =

* Massive removal of WP and PHP debugger notices, warnings, and errors
* Updated conditional to detect mini/full deck
* Replace deprecated WP functions
* Fixes to prevent html in purchase form dropdown from breaking form
* Multiple fixes to prevent project postmeta from saving/recalling correctly
* Minor fix to image deletion ajax in project postmeta
* Fixed differing percentage bar displays when over 100%
* Changed the Purchase Page Url text from 'Thank You page' to 'Checkout Page' in the metabox
* Description fields now properly support line breaks
* Fix to the way product array was stored and called
* Ensuring purchase can only take place when value is above 0
* Fixed issue causing inability to save social and skin settings

= 1.0 Beta5 Hotfix 8b Updates =

* New hooks to enable updates and FAQ extensions
* Removed level description from dropdown
* Updated submenu filter to allow multiple tabs

= 1.0 Beta5 Hotfix 8b Bug Fixes =

* Fixed several warnings and error notices
* Form field alignment fixed

= 1.0 Beta5 Hotfix 8 Updates =

* Removed WooThemes media uploader on new/edit project admin
* Improved order view/edit interface
* Enhanced purchase form dropdown
* Easily add new skins from the settings menu
* Custom settings gets its own tab
* New hooks to prepare for additional payment gateways
* New open/closed campaign type

= 1.0 Beta5 Hotfix 8 Bug Fixes =

* Centered purchase form
* Properly setting selected level in order edit screen
* Shortcodes now always show right product id
* Fixed invalid product_id insertion on new projects

= 1.0 Beta5 Hotfix 7b Updates =
-Only display FAQ/Updates if present
= 1.0 Beta5 Hotfix 7b Bug Fixes =

* Replaced invalid include wrapper for mini widget
* Fixed invalid product_id insertion on new projects

= 1.0 Beta5 Hotfix 7a Updates =

* Updated order view/edit styling

= 1.0 Beta5 Hotfix 7a Bug Fixes =

* Fixed order view/edit bug that restricted viewing/editing to a single order
* Clearing divs below complete shortcode elements

= 1.0 Beta5 Hotfix 7 Updates =

* Updated styles to avoid conflicts and add enhancements
* Removed deprecated Paypal button
* Minor security enhancements to database queries
* Two additional hooks added (create/save project)
* Adding product number to postmeta
* Switched directory conventions to plugins_url
* Removed deprecated files/functions
* Can now select manual amount when manually adding orders
* Can now edit level in order admin
* Renamed CMB scripts to avoid conflicts

= 1.0 Beta5 Hotfix 7 Bug Fixes =

* Properly decoding long description on complete shortcode
* Capping percentage raised at 100%
* Fixed order menu showing wrong level description
* Added handler for pending payments
* Stopped replacing WP default jQuery

= 1.0 Beta5 Hotfix 6 Updates =

* Pay what you want added as project type
* Improved error reporting on Paypal form
* Added project filter and order search to orders menu

= 1.0 Beta5 Hotfix 6 Bug Fixes =

* Fixed short/long description wysiwyg editors and editor toggling
* Fixed image deletion bug

= 1.0 Beta5 Hotfix 5 Updates =

* Installation of two action hooks: id_payment_success (triggers after a successful purchase) and id_payment_return (triggers after returning from paypal)
* Improved CSS handling to prevent theme override

= 1.0 Beta5 Hotfix 5 Bug Fixes =

* Fixed broken redirect to thank you page
* Payment selection properly loads saved settings/selection
* Properly sanitizing prices going into db
* Properly formatting numbers on all front end displays

= 1.0 Beta5 Hotfix 4f Bug Fixes =

* Fixed bug reducing percentage bar to 0% after fund reaches 1,000

= 1.0 Beta5 Hotfix 4e Bug Fixes =

* Fixed bug keeping Paypal email from saving

= 1.0 Beta5 Hotfix 4d Bug Fixes =

* Removed stray debugging text

= 1.0 Beta5 Hotfix 4c Bug Fixes =

* Fixed bug causing sandbox mode not to save on standard Paypal

= 1.0 Beta5 Hotfix 4b Update =

* Modified CSS to better adapt to long level titles
* Added script to auto-upgrade from version prior to B54a

= 1.0 Beta5 Hotfix 4a Bug Fixes =

* Fixed broken date picker

= 1.0 Beta5 Hotfix 4 Updates =

* Added sharing via App.net
* Added level titles
* Added level limits
* Numerous styling tweaks and updates
* Updating database structure
* Shortcodes are now templated and can be modified in a single file instead of multiple locations

= 1.0 Beta5 Hotfix 4 Bug Fixes =

* Fixed missing Facebook share button
* Numerous Adaptive Payments bugs addressed, including IPN

= 1.0 Beta5 Hotfix 3a Bug Fixes =

* Updated adaptive payments path so it loads properly
* Price descriptions updating properly on purchase form

= 1.0 Beta5 Hotfix 3 Updates =

* Frontend/Backend scripts are now separated to only load when required
* Adaptive Payments lib only loads when Adaptive Payments are activated
* Sanitized payment variables to Paypal
* Removing deprecated files/functions

= 1.0 Beta5 Hotfix 3 Bug Fixes =

* Fixed standard Paypal IPN bug
* Standardizing prices w/ decimal in levels

= 1.0 Beta5 Hotfix 2g Bug Fixes =

* Deleting product post now deletes from product database

= 1.0 Beta5 Hotfix 2f Updates =

* Rearranging city, state, zip on purchase form
* Updates to admin css

= 1.0 Beta5 Hotfix 2e Bug Fixes =

* Added missing translation fields
* Fixed PHP short open tags

= 1.0 Beta5 Hotfix 2e Bug Fixes =

* TBD

= 1.0 Beta5 Hotfix 2e Updates =

* Fixing non-fatal PHP errors
* Removed deprecated sharing features
* Improving use of templates within shortcodes

= 1.0 Beta5 Hotfix 2d Updates =

* Removing unnecessary js redirects

= 1.0 Beta5 Hotfix 2d Bug Fixes =

* Updated project and purchase URLs

= 1.0 Beta5 Hotfix 2c Updates =

* Removed unnecessary javascript from widget links

= 1.0 Beta5 Hotfix 2b Bug Fixes =

* Fixed = bug in PHP short tags

= 1.0 Beta5 Hotfix 2a Updates =

* Removing unnecessary jQuery
* Minor CSS improvements
* Updating to 24 hour timestamps
* 1.0 Beta5 Hotfix 2a Fixes
* Purchase URL fix

TBD
= 1.0 Beta5 Hotfix 1 Bug Fixes =

* Fixed PHP bugs caused by stray = signs
* Fixed blank project settings screen
* 1.0 Beta5 Hotfix 2 Bug Fixes
* Fixed bug in social sharing resulting in stray 0 in template

= 1.0 Beta5 Updates =

* TBD

= 1.0 Beta5 Bug Fixes =
= 1.0 Beta4 Hotfix 5c Bug Fixes =

* Added missing currencies

= 1.0 Beta4 Hotfix 5b Bug Fixes =

* Fixed embed widget CSS directory

= 1.0 Beta4 Hotfix 5 Updates =

* Easy upgrade button added to initial settings menu
* Minor changes to widget layout/text

= 1.0 Beta4 Hotfix 5 Bug Fixes =

* Fixed bug causing progress data to show 0/0%
* Fixed bug causing certain Paypal redirect URL's to format incorrectly upon return
* Fixed multiple order 'refresh bug'
* Fixed FB sharing to include proper image and title
* Fixed IE dollar sign issue
* Asked questions now delete properly
* First price level price now shows in manual order screen

= 1.0 Beta4 Hotfix 4 Updates =

* Added functions for project level &amp; buy button
* New CSS structure
* Minor CSS tweaks
* Help links added to admin screens

= 1.0 Beta4 Hotfix 4 Bug Fixes =

* Fixed translation/buy button issue
* Fixed delete button for project images
* Days left stops at 0

= 1.0 Beta4 Hotfix 3Updates =

* Modified admin menu with logo
* Added help text to admin menu
* Updated currency display
* Added shortcodes to side menu

= 1.0 Beta4 Hotfix 3 Bug Fixes =

* Fixed shortcodes
* Fixed percentage display

= 1.0 Beta4 Hotfix 2 Updates =

* Changed shortcode verbage from 'product' to 'project'

= 1.0 Beta4 Hotfix 2 Bug Fixes =

* Fixed a return URL issues causing orders not to post

= 1.0 Beta4 Hotfix 1 Bug Fixes =

* Fixed Mailchimp bug in default project settings
* Modified template global variables

= 1.0 Beta4 Bug Fixes =

* Better enqueueing of javascript to prevent conflicts.
* Fixed translation variables.
* Removed thumbnail script causing 404 errors.
* Fixed bug breaking custom project form settings.
* Updated manual order function to ensure buyer are subscribed via email.
* Fixed header, menu, and multiple content display issues.
* All project images now show.
* Fixed shortcode errors.
* Currencies and percentages can now display cents.
* Paypal project names now displaying properly.
* Fixed table layouts in order view menu.

= 1.0 Beta4 Updates =

* Replaced auto-insertion of project data with shortcodes.
* Language support has been upgraded and includes multiple language files. If you are interested in aiding with translation, please send an email to hello at virtuousgiant.com.
* Simplified plugin menu.
* Added new shortcodes for project content, widgets, and images.
* Updated internal documentation.

= 1.0 Beta3 Updates =

* 'Products' have been renamed to 'Projects'.
* Language support has been enabled. If you are interested in aiding with translation, please send an email to hello at virtuousgiant.com.
* Support for over a dozen new currencies.
* Added address override setting to fix Paypal shipping issues.
* Default and custom settings for each project.
* Renamed buy button text.
* Various label changes.

= 1.0 Beta3 Bug Fixes =

* Fixed button used to remove additional project levels.
* Fixed post-meta labeling issues.
* Fixed Facebook connect conflict when using mulitple Facebook plugins.
* Fixed bug causing widget to disappear when choosing any but default project.
* Renamed database tables for consistency.
* Fixed blank avatar bug for Twitter shares.
* Fixed labels on widget drop down and pop-up overlay.
