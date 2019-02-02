=== Dead-Letter ===
Contributors: SoftCreatR
Tags: blacklist, whitelist, email, disposable email, disposable emails, trashmail, trash mail, trash email, disposable domain, disposable domains, disposable, email validation, email verification, validation, verification
Donate link: https://www.paypal.me/wlplugins
Requires at least: 2.9
Tested up to: 5.0.3
Requires PHP: 5.4
Stable tag: 0.0.4
License: LGPL
License URI: https://github.com/SoftCreatR/wp-dead-letter/blob/master/LICENSE

Dead-Letter.Email for Wordpress. Dead simple disposable email check that just works.

== Description ==

This wordpress plugin utilizes our daily dump of disposable email address domains to detect the use of disposable email addresses.

It uses the Wordpress internal [is_email](https://developer.wordpress.org/reference/hooks/is_email/) hook, therefore it integrates seamlessly into 3rd party plugins like WooCommerce, Gravity Forms, Jetpack and every other plugin that uses the [is_email()](https://developer.wordpress.org/reference/functions/is_email/) function.

This plugin follows the "Privacy by design" principles by not sharing any personal informations of your users with 3rd parties. By installing/activating this plugin, you agree, that you've read and understood our [privacy policy](https://www.dead-letter.email/privacy-policy).

== Installation ==

= Download, Install and forget! =
As of now, there are no options. So you can easily install and forget it. It works right after it's installation/activation.

== Frequently Asked Questions ==

= What API is used? =
The Dead-Letter plugin for Wordpress doesn't use any external API. Every 24 hours, we publish an updated list of disposable email domains. This list is publicy accessible here: [https://www.dead-letter.email/blacklist_flat.json](https://www.dead-letter.email/blacklist_flat.json). This plugin just pulls this list every 24 hours and saves it locally.

We think, that this is the most reliable and privacy friendly solution.

= How is the list generated? =
Business secret ;)

= Is there any way to exclude domains from being blocked? =
Currently not, but that's already on our list.

= How can i report undetected domains? =
Contributing to this project is easy as 1-2-3, even without a Github account. If you have a Github account, just [create an issue](https://github.com/SoftCreatR/dead-letter-dump/issues/new) and list all domains you believe they are missing in the list.

If you don't have a Github account or prefer staying anonymous, use our  [Git Reports](https://gitreports.com/issue/SoftCreatR/dead-letter-dump)  form.

However, before creating an issue, please make sure, that the concerning domain is really undetected. To do so, simply perform a quick check at [https://www.dead-letter.email](https://www.dead-letter.email/)

== Changelog ==

= 0.0.4 - 2019-02-02 =
* BUG FIX: Changed text domain to match the WordPress plugin url

= 0.0.3 - 2019-02-02 =
* BUG FIX: Fixed translation init
* BUG FIX: Removed obsolete static keyword from method `doLog`

= 0.0.2 - 2019-01-31 =
* BUG FIX: Changed the download URL as requested by the WordPress Plugin Review Team
* OTHER: Removed non-SSL support

= 0.0.1 - 2019-01-30 =
* Initial release
