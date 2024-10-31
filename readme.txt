=== Reset Database ===

Contributors: maltesesolutions

Tags: Reset Database, reset wordpress, database reset, reset wordpress database, clean wordpress, default wordpress, restore wordpress, database reset, developers, programmers, wordpress reset

License: GPLv2

Requires at least: 4.0

Tested up to: 5.0

Stable tag: 1.1.3

It resets your database to the default installation and deletes any media files. 

== Description ==

It resets your database to the default installation and deletes any media files.

* It adds a settings page to "Dashboard"->"Tools"->"Reset Database" where you can simply reset the database of your WordPress.

* Very useful tool for programmers to reset the database.

* Secure - requires admin password to complete.

* If you find this plugin useful please rate it.

== Installation ==

1. Download and extract this plugin to `wp-content/plugins/`

2. Activate the plugin through the 'Plugins' menu in WordPress.

3. "Dashboard"->"Tools"->"Reset Database"

== Frequently Asked Questions ==

= Does it delete plugins? =
No, but it will reset them.
You will have to reactivate any plugins except Reset Database.

= Does it delete themes? =
No, but it will reset the theme back to current default theme.

= Why does it delete the media? =
WordPress stores a record of the media in the dabase.  We have reset the database, so we need to clear the media directory.

== Changelog ==
= 1.1.3 =

* Tested up to WordPress 5.0

= 1.1.2 =

* Fixed a typo in the input password
* Added Autocomplete 'Off'

= 1.1.1 =

* Added a reminder to not use admin as username
* passed compatibility test for WordPress 4.6

= 1.1 =

* Fixed a typo and eliminated the random password email.
* Continue to use your admin password.

= 1.0 =

* First release.