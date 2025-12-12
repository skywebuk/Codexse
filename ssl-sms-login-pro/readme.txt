=== SSL SMS Login Pro ===
Contributors: codexse
Tags: sms login, otp, mobile login, ssl wireless, bangladesh sms
Requires at least: 5.8
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional SMS-based login and registration system for WordPress using SSL Wireless SMS Gateway.

== Description ==

SSL SMS Login Pro allows your users to register and login using their mobile number with OTP verification through SSL Wireless SMS Gateway (Bangladesh).

= Features =

* **OTP Login** - Login with mobile number and OTP
* **Password Login** - Login with mobile number and password
* **Mobile Registration** - Register with mobile number verification
* **Forgot Password** - Reset password via SMS
* **Rate Limiting** - Prevent abuse with configurable attempt limits
* **SMS Logging** - Track all SMS sent through the system
* **Customizable Messages** - Configure OTP and welcome message templates
* **Shortcodes** - Easy integration with any page

= Shortcodes =

* `[ssl_sms_login]` - Complete login/registration form with tabs
* `[ssl_sms_login_form]` - Login form only
* `[ssl_sms_register_form]` - Registration form only
* `[ssl_sms_forgot_password]` - Forgot password form

= Requirements =

* SSL Wireless SMS API account
* Valid API Token and Sender ID

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/ssl-sms-login-pro/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to **SMS Login > Settings** to configure API credentials
4. Add the shortcode `[ssl_sms_login]` to any page

== Frequently Asked Questions ==

= How do I get SSL Wireless API credentials? =

Contact SSL Wireless at https://sslwireless.com to get your API token and sender ID.

= Can I customize the SMS messages? =

Yes, go to Settings > Message Templates to customize OTP, welcome, and password reset messages.

= How do I prevent abuse? =

The plugin includes rate limiting. You can configure:
* Max OTP attempts (default: 3)
* Block duration (default: 24 hours)
* OTP expiry time (default: 5 minutes)

== Changelog ==

= 1.0.0 =
* Initial release
* OTP login and registration
* Password login with mobile
* Forgot password via SMS
* Admin settings panel
* SMS logging
* Rate limiting

== Upgrade Notice ==

= 1.0.0 =
Initial release of SSL SMS Login Pro.
