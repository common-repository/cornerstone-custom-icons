=== Cornerstone Custom Icons ===
Contributors: michaelbourne
Donate link: https://www.paypal.me/yycpro
Tags: cornerstone, x theme, themeco, x pro, pro theme, icons, images, fontello, icon fonts
Requires at least: 4.5
Tested up to: 5.5
Stable tag: 0.2.2
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Add custom icon fonts from Fontello to the built in Cornerstone and Pro elements controls

== Description ==

Enables the user to add their own custom icons to the built in Cornerstone and Pro Theme icon controls and elements, thereby removing the reliance on FontAwesome and providing a better opportunity for branding with custom icon sets on your websites. Works exclusively through the use of Fontello's free icon font service.

= Plugin Features =
 
*   Add unlimited icons and SVG icons to your website through Fontello's free service
*   Upload multiple icon packs from Fontello
*   View the icons in each uploaded pack, delete individually if desired
*   Use icons anywhere you would normally with the default Cornerstone icon selector

Please note, this plugin requires the Cornerstone Page Builder (or the Pro Theme by Themeco) to be installed and active. [Cornerstone on CodeCanyon](https://codecanyon.net/item/cornerstone-the-wordpress-page-builder/15518868). [Pro by Themeco](https://theme.co/pro/)

This plugin relies on a third party service for it's functionality provided by [Fontello](http://fontello.com). No private information is sent to their server, rather their provided webfont downloads are what's used to add fonts to this plugin.

Looking for a video tutorial? [Here it is!](https://www.youtube.com/watch?v=OxCU4T82DKo)

== Installation ==

1.  Upload your plugin folder to the '/wp-content/plugins' directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  Go to Fontello.com to create your own icon font. Download the zip when done.
4.  Upload previous zip file to the plugins settings page.

== Frequently Asked Questions ==

= What can I do with this plugin? =

You can add your own icons to the Cornerstone and Pro builders. From exisiting fonts to totally custom SVG icons, no more messing around with CSS or image elements. It's now all baked in.

= Can I upload more than one Fontello package to a single site? =

You bet! Upload as many as you like, they will all work. However, there are a couple tips to make it work better: 1) make sure you give each font a unique name (text box beside the Fontello download button). 2) Edit the unicode values on each font to be different. This step is a little time consuming, but by default, Fontello starts most new fonts with E800, which will cause a conflict when you upload more than one package.

= HELP! It doesnt work?! =

There is a small, small chance this plugin may not work on your web host. This is caused by two things generally: a mod_security rule flagging the ZIP upload, or the lack of PHP libraries needed to unzip files. Here's the good news: your host can fix both of these easily. If they refuse, consider moving to a more modern host.


== Plugin Removal ==

Removing this plugin will render your custom icons to be broken. Take care to remove them from your elements prior to plugin removal.

== Screenshots ==

None yet

== Changelog ==

= 0.2.2 =
* Fix: display glitch in icon selector.

= 0.2.1 =
* Fix: regen CSS now regenerates options table as well for file paths
* Tweak: CSS files use relatie URLs instead of absolute
* Added: uninstall.php file to clean up font files after plugin removal

= 0.1.9 =
* Fix: compatiblity with Pro 2.3.2+

= 0.1.8 =
* Fix: delete font button not working

= 0.1.7 =
* Limit upload area to zip files only to prevent confusion
* Added javascript translations
* Fixed icon font rendering after upload
* Added additional error alerts on font upload for hosts with no zip support
* Fix jQuery reloading of stylsheet in admin page 
* Fix error where an un-named font will not render properly

= 0.1.6 =
* Fixed empty icon boxes in swatch for Pro version 2.2.5+
* Compatible with pre and post v2.2.x
* Regeneration of CSS is now heavily suggested after plugin and theme upgrades

= 0.1.5 =
* Added fix for Pro version 2.2.0+ (FontAwesome 5 update)
* Added "regenerate CSS" option to fix domain errors

= 0.1.4 =
* Added full l18n support for translations
* Fixed PHP 7.2 Notices

= 0.1.3 =
* Add SSL check for rare occurance of insecure content warning
* Add video tutorial to docs

= 0.1.2 =
* Minor text tweaks, spelling, etc

= 0.1.0 =
* Initial Public Version


== Upgrade Notice ==

= 0.2.2 =
Fix icon selector panel display glitch

= 0.2.1 =
Improved CSS regen and support for domain or protocol changes

= 0.1.9 =
Fix 500 error on newest theme releases. Update this plugin prior to a theme update.

= 0.1.8 =
Bug fix for delete button

= 0.1.6 =
Fix empty box icons. Be sure to regenerate CSS after update.

= 0.1.5 =
Added l18n support and FA5 fix

= 0.1.2 =
ReadMe update only

= 0.1.0 =
You can't upgrade, but you can install.

== Copyright ==

Cornerstone Custom Icons is a plugin for WordPress that enables you to add custom icon fonts to the built in Elementor controls.
Copyright (c) 2018 Michael Bourne.

The Cornerstone Custom Icons Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>

You can contact me at michael@michaelbourne.ca