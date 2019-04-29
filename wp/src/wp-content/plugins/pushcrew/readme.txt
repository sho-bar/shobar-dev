=== PushCrew ===
Contributors: Wingify
Plugin Name: PushCrew
Plugin URI: https://pushcrew.com/
Tags: push notifications, website push notifications, chrome push notifications.
Requires at least: 2.7
Tested up to: 4.9.1
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

With PushCrew, any website on the web can get up and running with browser push notifications in less than a minute.

== Description ==
Installing the PushCrew plugin will automatically insert the PushCrew Smart Code on your WordPress website. To get started, you just have to copy the PushCrew Account Id(Your Account ID can be found in the Account Details section under Settings area in the PushCrew dashboard) and paste it in this plugins’s settings.

What is PushCrew?

PushCrew lets you talk to your subscribers in a succinct, easy and delightful manner, using push notifications on browser. Push Notifications are clickable messages sent directly to your subscribers’ browsers (even when they are not on your website). These work on all devices — desktops, tablets and even mobile phones — so you don’t even have to invest in building a mobile app for your business. The opt-in and click rates are amazing! Some of our early adopters have seen an opt-in rate of 40% (10X the rate at which an average email list builds, and 20X the rate at which an average Twitter list populates) and a click rate of 20%. Of course, you get to see all this data, right in your PushCrew dashboard, updated real-time.

Let us help you get amazing returns on your communications. For any questions, please get in touch with us at info@pushcrew.com

== Installation ==
Wordpress : Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

WordpressMu : Same as above

== Frequently Asked Questions ==
= I can't see any code added to my header or footer when I view my page source =
Your theme needs to have the header and footer actions in place before the `</head>` and before the `</body>`

= If I use this plugin, do I need to enter any other code on my website? =
No, this plugin is sufficient by itself

== Screenshots ==
1. Settings page
2. Opt-in on website
3. PushCrew dashboard
4. This is how a notification looks like. Lands even when the webpage is closed.

== ChangeLog ==

= 1.1 =
* Fixed Bug related to Account ID

= 1.0 =
* First Version

== Configuration ==

Enter your PushCrew Account ID in the field marked 'YOUR PUSHCREW ACCOUNT ID'

== Adding to your template ==

header code :
`<?php wp_head();?>`

footer code : 
`<?php wp_footer();?>`