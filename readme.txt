=== Plugin Name ===
Contributors: braveryk7
Tags: send chat tools, send, chat, slack, discord, chatwork, update
Requires at least: 5.7.2
Tested up to: 5.9.1
Requires PHP: 7.3.0
Stable tag: 1.3.0
License: GpLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

=== Description ===

Send Chat Tools is a plugin that allows you to send WordPress announcements to chat tools.
Currently, it sends an announcement when a comment is received.
You can instantly check comments that you didn't notice with the default email notification. 

=== Changelog ===

= 1.3.0 =

Miner update.

Updated to version 1.3.0!
Here are the main changes...

Important:

* PHP 8.0 and earlier will no longer be supported after next minor update (1.4.0). The update is now scheduled for April.

Improvements:

* The admin panel has been replaced with React for a more modern design using WordPress components that are familiar to everyone.
* The chat tool now saves up to 3 individual logs.
* The Send Chat Tools widget is now available on the WordPress dashboard.
* A warning will be displayed when the next update changes the supported PHP version.
* We will send you the updated content vwhen the plugin is updated.
* The internal design of PHP has been substantially revised.
* Most options starting with "sct" have been removed and replaced by the sct_options column.
* The sct table is no longer available and has been unified with the sct_logs column.
* The sct_update_notify hook is now publicly available for developers.

Fixes:

* The encryption of API values has been removed.
* You will not receive a response if the API value is invalid.
* You won't be able to change the WordPress settings from the Send Chat Tools settings screen.

Development:

* The development environment has been largely upgraded for future updates.
* PHPUnit has been installed to enable a more precise testing environment.
* We installed PHPStan to build a static analysis environment.
* The development environment has been built by combining WordPress components and TypeScript.
* Updated settings for ESLint, Stylelint, and PHP_Codesniffer.
* Adopted small tools (zip.sh, git hooks).

= 1.2.0 =

Miner update.

Improvement.

* UI for Slack messages(Adopted Block Kit)

Fix.

* The problem that the corresponding WordPress version was not reflected.

= 1.1.1 =

Support WordPress5.8.

Addition of functions:

* Add Update notifications

	* Plugins:
		* [THE SONIC SEO Plugin](https://the-sonic.jp/manual/start/plugin-install/)
		* [THE SONIC Gutenberg Blocks](https://the-sonic.jp/manual/start/plugin-install/)
		* [THE SONIC COPIA](https://the-sonic.jp/plugin-copia/)
		* [SANGO Gutenberg](https://saruwakakun.com/sango/gutenberg-introduction)

Improvements:

* Fix to hide OGP in Discord.
* Removed unnecessary option columns.

= 1.1.0 =

Miner update.

Addition of functions:

* Add Update notifications

	* Themes:
		* [Cocoon](https://wp-cocoon.com/)
		* [SANGO](https://saruwakakun.design/)
		* [THE SONIC](https://the-sonic.jp/)
	* Plugins:
		* [Rinker](https://oyakosodate.com/rinker/)

Improvements:

* Processing to go through sending process if API is not input.

= 1.0.0 =

Major update.

Addition of functions:

* Add Discord.
* Add WordPress Core, theme and plugin update notifications.
* Add API value automatic check.
* Add Communication log.

Improvements:

* UI of the admin panel

Fix.

* Problem that error mail may be sent even if the message is sent normally.

= 0.1.6 =
Fix crypt logic
Fix characters

= 0.1.5 =
Add error code 1000.

= 0.1.4 =
Added an exception handling when "Use chat tool" is checked but the required value is not entered.

= 0.1.3 =
Add WordPress standard settings to admin page

= 0.1.2 =
Add send email if could not be sent successfully.

= 0.1.1 =
Add database process.
Add Chatwork discryption.

= 0.1.0 =
Miner update.

Add Chatwork.
Add Icons, Banners, and Screenshots.
Small fix.

= 0.0.1 =
Beta version release.

== Screenshots ==
1. screenshot-1.png
2. screenshot-2.png