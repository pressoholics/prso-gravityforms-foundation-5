=== Gravity Forms for Zurb Foundation 5 by Benjamin Moody ===
Contributors: ben.moody
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Tags: gravity forms, zurb, foundation, zurb foundation, foundation 5, zurb foundation 5
Requires at least: 3.0
Tested up to: 3.8
Stable tag: 1.1

Want to use Gravity Forms with Zurb Foundation 5? Look no further. This plugin filters form styles to use Foundation 5 out of the box, inc validation.

== Description ==

Want to use Gravity Forms with Zurb Foundation 5? Look no further. This plugin filters form styles to use Foundation 5 right out of the box, including validation.

Complex fields such as Address are broken into columns to provide a neat field 'bundle' (see screenshots).

Radio and check lists are formatted as shown in the Foundation 5 docs. Also fields like 'website' make use of the Pre/Postfix labels to add a little Foundation flare.

Validation is also taken care of, making sure to use the form Error States laid out in the Foundation docs.

There are also a host of Wordpress filters for devs to hook into to alter DOM classes, IDs, and content.

This plugin has also been tested with Multisite installs.

Please Note: The plugin simply converts Gravity Form output to fit with Foundation 5 convensions. You will still need to style form elements in your theme's CSS. It just saves devs who use Foundation 5 allot of time :)

If you have any suggestions or bugfixes please checkout the GitHub repo at https://github.com/pressoholics/prso-gravityforms-foundation-5

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

== Why doesn't my form look perfect and fit my theme ==

This plugin simply ensures that the form elements make use of Foundation 5 classes and html layout. You will still have to put the finishing touches to your theme's form and field styles.

== Screenshots ==

1. Here is the default Gravity Forms Address field in Foundation 5.
2. Here is the Address field with Foundation 5 applied.
3. Here is the Website field without Foundation 5.
4. The Website field with Foundation 5 applied.
5. Example of form validation errors.
6. Example of form success message.

== Changelog ==

= 1.1 =
* Validation now effects all form fields
* Added style for Email field
* Added new filters for both website and email field column classes

= 1.0 =
* Updated code to use Foundation 5 html stucture and CSS classes
* Added some more Wordpress Filter hooks

== Upgrade Notice ==

= 1.0 =
* Initial plugin launch, this is the first version.

== Filter Hooks ==

1. Filter:: 'prso_theme_gform_validation_wrapper_class' 	- change class for div around field validation message
2. Filter:: 'prso_theme_gform_form_confirmation_class' 	- change class for div around form confirmation message
3. Filter:: 'prso_theme_gform_form_validation_class' 		- change class for div around form validation message
4. Filter:: 'prso_theme_gforms_address_city_class' 		- change class for div around city field
5. Filter:: 'prso_theme_gforms_address_state_class' 		- change class for div around state field
6. Filter:: 'prso_theme_gforms_address_zip_class' 			- change class for div around zip field
7. Filter:: 'prso_theme_gforms_website_class' 				- change class for div around website field
8. Filter:: 'prso_theme_gforms_website_placeholder' 		- change the placeholder value for website input field
9. Filter:: 'prso_theme_gforms_website_field_class' 		- change class for website field input
10. Filter:: 'prso_theme_gforms_name_class' 				- change class for div around name fields
11. Filter:: 'prso_theme_gforms_name_field_class' 			- change class for name field inputs
12.	Filter:: 'prso_theme_gforms_email_prepend_col_size'	- change foundation col size classes
13.	Filter:: 'prso_theme_gforms_email_col_size'	- change foundation col size classes
14.	Filter:: 'prso_theme_gforms_website_prepend_col_size'	- change foundation col size classes
15.	Filter:: 'prso_theme_gforms_website_col_size'			- change foundation col size classes
16. See 'core.prso-garvityforms-foundation-5.php' for all filters/actions used