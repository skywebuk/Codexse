<?php
/**
 * Plugin Name: MailHive
 * Plugin URI: https://example.com/mailhive
 * Description: A powerful email subscription plugin with form builder, AJAX submission, and subscriber management.
 * Version: 1.0.0
 * Author: MailHive
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mailhive
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MAILHIVE_VERSION', '1.0.0' );
define( 'MAILHIVE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MAILHIVE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MAILHIVE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once MAILHIVE_PLUGIN_DIR . 'includes/class-mailhive.php';

/**
 * Returns the main instance of MailHive.
 *
 * @return MailHive
 */
function mailhive() {
    return MailHive::instance();
}

mailhive();

register_activation_hook( __FILE__, array( 'MailHive', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'MailHive', 'deactivate' ) );
