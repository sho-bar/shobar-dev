=== Dyad 2 ===

Contributors: automattic
Requires at least: 4.0
Tested up to: 4.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Dyad 2 Theme, Copyright 2017 Automattic
Dyad 2 is distributed under the terms of the GNU GPL


== Description ==
Dyad 2 pairs your written content and images together in perfect balance. The theme is geared towards photographers, foodies, artists, and anyone who is looking for a strong photographic presence on their website.


== Installation ==

1. In your admin panel, go to Appearance > Themes and click the Add New button.
2. Click Upload and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme right away.


== Frequently Asked Questions ==

= How do I set up the Featured Content slider? =

The Featured Content slider can be seen on the theme's demo site, https://dyad2demo.wordpress.com/?demo. To add to your site:

1. Install and activate the Jetpack plugin.
2. Go to Customizer > Featured Content from the WP Admin Dashboard. Enter a tag that you would like to use on Featured Content posts. For example, you could use the tag 'featured'.
3. Add that tag on posts on your website and they will appear in the Featured Content slider. Note: only tagged posts with a Featured Image in the slider.


= Does this theme support any plugins? =

Dyad 2 includes support for Infinite Scroll, Author Bio and Featured Content in Jetpack.


== Credits ==

* Based on Underscores http://underscores.me/, (C) 2012-2017 Automattic, Inc., [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
* normalize.css http://necolas.github.io/normalize.css/, (C) 2012-2017 Nicolas Gallagher and Jonathan Neal, [MIT](http://opensource.org/licenses/MIT)
* slick.js, (C) Ken Wheeler. [MIT](http://opensource.org/licenses/MIT)
* Lato font, (C) Łukasz Dziedzic. [SIL Open Font License, 1.1] (http://scripts.sil.org/OFL)
* Noto Serif font, (C) Google. [Apache License, version 2.0] (http://www.apache.org/licenses/LICENSE-2.0.html)
* Genericons, (C) 2015 Automattic, Inc. [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
* ImagesLoaded (C) Tomas Sardyha and David DeSandro.[MIT](http://opensource.org/licenses/MIT)
* Images used in screenshot from Unsplash http://unsplash.com [CC0 1.0] (http://creativecommons.org/publicdomain/zero/1.0/). From top to bottom:
     * https://unsplash.com/photos/eqsEZNCm4-c
     * https://unsplash.com/photos/Dq5g1u1eg1Q
     * https://unsplash.com/photos/VR28K9_iRgc
     * https://unsplash.com/photos/QlnUpMED6Qs
     * https://unsplash.com/photos/N_Y88TWmGwA
     * https://unsplash.com/photos/jivmv9hE6bM
     * https://unsplash.com/photos/0RUlEosIP8Y
* Fork of the Receptar theme by WebMan Design (https://en-ca.wordpress.org/themes/receptar). Copyright (c) 2015 WebMan (http://www.webmandesign.eu/) [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)


== Changelog ==

= 25 January 2018 =
* Prevent site header overlay from covering text when printing, which was causing text to print gray rather than black.

= 15 January 2018 =
* Add Featured Image for featured post in Headstart annotations.
* Simplify Headstart annotations for all themes in signup.

= 11 January 2018 =
* Added support for WooCommerce RTL
* Minor CSS formatting

= 10 January 2018 =
* Code cleanup; removed commented out code.
* Added WooCommerce support

= 13 October 2017 =
* Fixes #4856, a visual bug with navigation menus when sub-menu parents have long titles

= 3 October 2017 =
* Changing post height check to check if contents are less than or equal to the container height, rather than less than. The functions' being fired twice for custom fonts, and the second firing is breaking it again because it's not less than.

= 25 August 2017 =
* Replace line of smarter featured image code that was not compatible with PHP 5.3.x, for backwards compatibility.

= 26 June 2017 =
* Remove editor styles for links.

= 6 June 2017 =
* Add style for the Social Icons widget.

= 20 April 2017 =
* We don't want to use Smarter Featured Images on pages
* Add support for Smarter Featured Images

= 31 March 2017 =
* Rename Custom Logo CSS classes.
* Update pot filename
* Make sure sidebar on small screens returns 100% even on Image Post Format.

= 22 March 2017 =
* add Custom Colors annotations directly to the theme
* move fonts annotations directly into the theme

= 6 March 2017 =
* Update footer credit to use correct theme name. This may be preventing users from hiding the footer credit.

= 5 March 2017 =
* Correcting a syntax error that was causing display issues in Safari.

= 4 March 2017 =
* Making sure block for entry-media is displayed whether or not there's an image - otherwise posts without images collapse.

= 3 March 2017 =
* Update image size and styles used for mobile on blog index page. This helps make sure the image is cropped in a similar fashion to the single posts on mobile, and also helps make sure it shows as much of the original image as possible, assuming the user is using the recommended image size/aspect ratio. Also tweaked the Edit link to make it centred on mobile, instead of slightly offset.

= 27 February 2017 =
* Adding a '.button' class so users can create styled buttons in their content

= 24 February 2017 =
* Removing changelog from readme file
* Use Jetpack genericons over local theme genericons when possible
* Code cleanup and tidying

= 23 February 2017 =
* Remove De-queuing Google fonts, Adding support for menu drop-down icons when not using a custom menu
* De-queue Google fonts if custom fonts are being used instead, Add print styles for wpcom
* Removing 'admin-head-callback' and 'admin-preview-callback' callbacks, also some code cleanup
* Removing imagesloaded script since it’s pre-packaged with wordpress already
* Updating readme.txt
* Replacing Jetpack Site Logo with Core Logo
* Adding support for Jetpack Author Bio
* Refactoring Jetpack social menu to fix duplicat SVG bug
* Replacing custom 'social-menu' with 'jetpack-social-menu' along with SVG icon support
* Updating theme screenshot to reflect new theme name and two column layout

= 21 February 2017 =
* Changing primary menu location ID to 'menu-1' to match tux list

= 14 February 2017 =
* Adds dropdown menu icons to menu items that contain sub-menus
* Updating social-menu slug to match TUX list
* Changing 'dyad-' slugs and variables to 'dyad-2-'
* Theme refresh initial commit
