=== Plugin Name ===
Contributors: braveryk7
Tags: send chat tools, send, chat, slack, discord, chatwork, update
Requires at least: 5.7.2
Tested up to: 6.4.2
Requires PHP: 8.0.0
Stable tag: 1.5.4
License: GpLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

=== Description ===

Send Chat Tools is a plugin that allows you to send WordPress announcements to chat tools.
Currently, it sends an announcement when a comment is received.
You can instantly check comments that you didn't notice with the default email notification. 

=== Changelog ===

= 1.5.4 =

Important:

* Active support for PHP 8.1 ends November 25, 2023. Therefore, PHP 8.2 will become a requirement around November 2023.

Improvements:

* WordPress 6.3.1 is now supported.

Fixes:

* Fixed deprecated options in new WordPress packages.

Development:

* Existing packages have been modernized.

= 1.5.3 =

Important:

* PHP8.0 is required for v1.4.0 and later.

Feature:

* Added JIN:R to update notifications.

Improvements:

* The process of generating and saving logs has been changed.

Fixes:

* Fixed problem with update notifications not being sent when Cocoon is enabled.
* Fixed to remove WP-Cron from Rinker notifications upon uninstallation.

= 1.5.2 =

Important:

* PHP8.0 is required for v1.4.0 and later.

Fixes:

* Fixed an issue where notification was sent despite the end-of-sale flag not being set in the Rinker end-of-sale notification.

= 1.5.1 =

Important:

* PHP8.0 is required for v1.4.0 and later.

Fixes:

* Fixed wp-cron settings.
* A problem with missing translation files has been corrected.

= 1.5.0 =

Important:

* PHP8.0 is required for v1.4.0 and later.

Feature:

* Added the ability to notify when a user logs in.
* Added a function to notify when an Amazon/Rakuten product is no longer available in the Rinker product management plugin.

Improvements:

* We added more items to the settings screen and made it easier to understand by adding labels and headings.
* We have added a feature to turn off comment notifications.
* Improved so that other setting items cannot be operated if the Use chat tool is not checked in the settings screen.
* Added pictograms and text decoration to Discord notifications to improve visibility.

Fixes:

* Changed the name of the comment contributor from Author to Commenter.

Development:

* Generation of sent data has been split for each chat tool to improve maintainability.
* The internal design was reviewed mainly in the generation of outgoing messages.

= 1.4.0 =

Important:

* We no longer support PHP7 series. This plugin can only be activated with PHP8 or higher.
* The chat log limit has been changed to 300 entries.

Improvements:

* The PHP versioning logic has been revised.
* We have expanded the testing of various methods.

Development:

* The genuine WordPress wp-env environment has been installed.

= 1.3.1 =

Important:

* Please update your PHP version to 8.0 or higher. The next update will not allow use with PHP 8.0 or lower and will automatically disable the plugin. This change is scheduled around April 15th.
* The limit for logs will be reduced from 1,000 to 300 in the next update. Please make a backup if needed.

Improvements:

* It is now ready for WordPress 6.2.
* The new functionality to reject certain developer messages has been implemented
* The @wordpress/api-fetch package has been adopted for quicker and safer use of the setting page.
* The design of the screen during data loading has been simplified.
* We have moved the time settings for update notification to the Basic Settings tab. With this change, the Update Notification tab has been eliminated.
* An output function for logs has been installed. You can choose to copy to the clipboard and output to text/CSV.
* We now show an error message when data retrieval failures occur in the WordPress REST API.

Fixes:

* We have fixed a problem that was causing API errors being output to PHP.
* Refactoring of code has been implemented.
* Some legacy code has been removed.
* We have resolved an issue that caused a critical error in some environments during updates.

Development:

* The development environment has been reorganized.

= 1.3.0 =

Miner update.

Important:

* PHP 8.0 and earlier will no longer be supported after next minor update (1.4.0). The update is now scheduled for April.

Improvements:

* The admin panel has been replaced with React for a more modern design using WordPress components that are familiar to everyone.
* The chat tool now saves up to 3 individual logs.
* The Send Chat Tools widget is now available on the WordPress dashboard.
* A warning will be displayed when the next update changes the supported PHP version.
* We will send you the updated content when the plugin is updated.
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