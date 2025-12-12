=== SSL SMS Login ===
Contributors: jeeon
Tags: sms login, otp, mobile login, ssl wireless, bangladesh sms, elementor
Requires at least: 5.8
Tested up to: 6.9
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Modern SMS-based login and registration system for WordPress using SSL Wireless SMS Gateway with Elementor support.

== Description ==

SSL SMS Login allows your users to register and login using their mobile number with OTP verification through SSL Wireless SMS Gateway (Bangladesh).

= Features =

* **OTP Login** - Login with mobile number and OTP
* **Password Login** - Login with mobile number and password
* **Mobile Registration** - Register with mobile number verification
* **Forgot Password** - Reset password via SMS
* **Elementor Widget** - Drag and drop login form widget
* **5 Form Styles** - Modern, Minimal, Rounded, Bordered, Gradient
* **Color Settings** - Customize colors from admin panel
* **Rate Limiting** - Prevent abuse with configurable attempt limits
* **SMS Logging** - Track all SMS sent through the system
* **Customizable Messages** - Configure OTP and welcome message templates

= Shortcodes =

* `[ssl_sms_login]` - Complete login/registration form with tabs
* `[ssl_sms_login_form]` - Login form only
* `[ssl_sms_register_form]` - Registration form only
* `[ssl_sms_forgot_password]` - Forgot password form

= Elementor Widget =

The plugin includes an Elementor widget with full customization options:
* Choose form type (Login, Register, Combined, Forgot Password)
* Select from 5 beautiful form styles
* Customize colors, padding, border radius
* Set custom redirect URL

= Requirements =

* SSL Wireless SMS API account
* Valid API Token and Sender ID

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/ssl-sms-login/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to **SMS Login > Settings** to configure API credentials
4. Add the shortcode `[ssl_sms_login]` to any page or use Elementor widget

== Frequently Asked Questions ==

= How do I get SSL Wireless API credentials? =

Contact SSL Wireless at https://sslwireless.com to get your API token and sender ID.

= Can I customize the form colors? =

Yes, go to Settings > Appearance to customize primary color, secondary color, text color, and border radius.

= Can I customize the SMS messages? =

Yes, go to Settings > Message Templates to customize OTP, welcome, and password reset messages.

== Changelog ==

= 1.0.1 =
* Removed deprecated load_plugin_textdomain() call
* Updated tested up to WordPress 6.9
* Modern custom radio button styles for login form
* Improved admin UI with custom form controls

= 1.0.0 =
* Initial release
* OTP login and registration
* Password login with mobile
* Forgot password via SMS
* Elementor widget support
* 5 form styles
* Admin color settings
* SMS logging
* Rate limiting

== Upgrade Notice ==

= 1.0.0 =
Initial release of SSL SMS Login.
