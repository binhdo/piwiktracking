=== Piwiktracking ===
Contributors: binhdo
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QZM4M7KZUEZML
Tags: piwik, tracking, analytics, data, privacy
Requires at least: 3.3
Tested up to: 3.4
Stable tag: 1.1

Adds the Piwik Tracking Code to your WordPress blog.

== Description ==

A basic plugin to conveniently add the **Piwik tracking code** to your WordPress posts and pages.

Piwik is an open source online analytics tool much like Google Analytics, with the main difference 
that you can host it on your own server and retain full control over your data. 

= Features =

* Set the Piwik Base URL and the corresponding SiteID on the settings page
* Choose between the standard javascript tracker or the modern asynchronous tracker (recommended to improve page loading time)
* Enable or disable download & outlink tracking
* Advanced settings: select user groups (identified by roles) to exclude from being tracked

= Requirements = 
This plugin requires a properly configured, up to date installation of the Piwik Analytics software (tested with 1.6 and higher)
to work. Please visit the [Piwik website](http://piwik.org "Piwik") for detailed reference on Piwik.

Feel free to visit [my blog](http://binaryhideout.com/piwiktracking-wordpress-plugin/)

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make sure to set the correct URL to your Piwik installation and the corresponding SiteID on the settings page.
4. Done!

...or simply use the built in plugin browser to handle the uploading.

== Screenshots ==

1. Piwiktracking Main Setting
2. Piwiktracking Advanced Settings

== Changelog ==

= 1.1 =

* fixing a stupid bug related to standard javascript tracking mode

= 1.0 =

* intitial release
