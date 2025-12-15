<?php
if ( ! class_exists( 'Brainforward_Plugins' ) && is_admin() ) {
    class Brainforward_Plugins {
        public static $instance;
        public $plugins = array();
        public $menu = 'brainforward_plugins-install-plugins';
        public $default_path = '';
        public $has_notices = true;
        public $dismissable = true;
        public $dismiss_msg = '';
        public $is_automatic = false;
        public $message = '';
        public $strings = array();
        public $wp_version;
        public function __construct() {
            self::$instance = $this;
            $this->initialize_strings();
        
            global $wp_version;
            $this->wp_version = $wp_version;
        
            do_action_ref_array('brainforward_init', array($this));
            add_action('init', array($this, 'init'));
        }

        
        private function initialize_strings() {
            $this->strings = array(
                'page_title' => __('Install Required Plugins', 'brainforward'),
                'menu_title' => __('Install Plugins', 'brainforward'),
                'installing' => __('Installing Plugin: %s', 'brainforward'),
                'oops' => __('Something went wrong.', 'brainforward'),
                'notice_can_install_required' => _n_noop('This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'brainforward'),
                'notice_can_install_recommended' => _n_noop('This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'brainforward'),
                'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin.', 'Sorry, but you do not have the correct permissions to install the %s plugins.', 'brainforward'),
                'notice_can_activate_required' => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'brainforward'),
                'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'brainforward'),
                'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin.', 'Sorry, but you do not have the correct permissions to activate the %s plugins.', 'brainforward'),
                'notice_ask_to_update' => _n_noop('The following plugin needs to be updated: %1$s.', 'The following plugins need to be updated: %1$s.', 'brainforward'),
                'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin.', 'Sorry, but you do not have the correct permissions to update the %s plugins.', 'brainforward'),
                'install_link' => _n_noop('Begin installing plugin', 'Begin installing plugins', 'brainforward'),
                'activate_link' => _n_noop('Begin activating plugin', 'Begin activating plugins', 'brainforward'),
                'return' => __('Return to Required Plugins Installer', 'brainforward'),
                'dashboard' => __('Return to the dashboard', 'brainforward'),
                'plugin_activated' => __('Plugin activated successfully.', 'brainforward'),
                'activated_successfully' => __('The following plugin was activated successfully:', 'brainforward'),
                'complete' => __('All plugins installed and activated successfully. %1$s', 'brainforward'),
                'dismiss' => __('Dismiss this notice', 'brainforward'),
            );
        }
        


        public function init() {
            do_action('brainforward_register');
            if ($this->plugins) {
                $this->register_admin_hooks();
            }
        }

        private function register_admin_hooks() {
            $sorted = array_column($this->plugins, 'name');
            array_multisort($sorted, SORT_ASC, $this->plugins);

            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('admin_head', array($this, 'dismiss'));
            add_filter('install_plugin_complete_actions', array($this, 'actions'));
            add_action('switch_theme', array($this, 'flush_plugins_cache'));

            if ($this->is_brainforward_page()) {
                remove_action('wp_footer', 'wp_admin_bar_render', 1000);
                remove_action('admin_footer', 'wp_admin_bar_render', 1000);
                add_action('wp_head', 'wp_admin_bar_render', 1000);
                add_action('admin_head', 'wp_admin_bar_render', 1000);
            }

            if ($this->has_notices) {
                add_action('admin_notices', array($this, 'notices'));
                add_action('admin_init', array($this, 'admin_init'), 1);
                add_action('admin_enqueue_scripts', array($this, 'thickbox'));
                add_action('switch_theme', array($this, 'update_dismiss'));
            }

            foreach ($this->plugins as $plugin) {
                if (!empty($plugin['force_activation'])) {
                    add_action('admin_init', array($this, 'force_activation'));
                    break;
                }
            }

            foreach ($this->plugins as $plugin) {
                if (!empty($plugin['force_deactivation'])) {
                    add_action('switch_theme', array($this, 'force_deactivation'));
                    break;
                }
            }
        }

        
        public function admin_init() {

            if ( ! $this->is_brainforward_page() ) {
                return;
            }

            if ( isset( $_REQUEST['tab'] ) && 'plugin-information' == $_REQUEST['tab'] ) {
                require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for install_plugin_information().

                wp_enqueue_style( 'plugin-install' );

                global $tab, $body_id;
                $body_id = $tab = 'plugin-information';

                install_plugin_information();

                exit;
            }

        }
        public function thickbox() {

            if ( ! get_user_meta( get_current_user_id(), 'brainforward_dismissed_notice', true ) ) {
                add_thickbox();
            }

        }
        public function admin_menu() {

            // Make sure privileges are correct to see the page
            if ( ! current_user_can( 'install_plugins' ) ) {
                return;
            }

            $this->populate_file_path();

            foreach ( $this->plugins as $plugin ) {
                if ( ! is_plugin_active_custom( $plugin['file_path'] ) ) {
                    add_theme_page(
                        $this->strings['page_title'],          // Page title.
                        $this->strings['menu_title'],          // Menu title.
                        'edit_theme_options',                  // Capability.
                        $this->menu,                           // Menu slug.
                        array( $this, 'install_plugins_page' ) // Callback.
                    );
                break;
                }
            }

        }

        public function install_plugins_page() {
            // Store new instance of plugin table in object.
            $plugin_table = new Brainforward_List_Table;

            // Sanitize and validate the 'action' parameter from POST.
            $action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : '';

            // Return early if processing a plugin installation action.
            if ( 'brainforward_plugins-bulk-install' === $action && $plugin_table->process_bulk_actions() || $this->do_plugin_install() ) {
                return;
            }

            ?>
            <div class="brainforward_plugins wrap">
                <?php if ( version_compare( $this->wp_version, '3.8', '<' ) ) : ?>
                    <div class="notice notice-warning">
                        <p><?php _e( 'This theme requires WordPress version 3.8 or higher. Please upgrade your WordPress installation.', 'brainforward' ); ?></p>
                    </div>
                <?php endif; ?>
                
                <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
                <?php $plugin_table->prepare_items(); ?>

                <?php if ( isset( $this->message ) ) {
                    echo wp_kses_post( $this->message );
                } ?>

                <form id="brainforward_plugins-plugins" action="" method="post">
                    <input type="hidden" name="brainforward_plugins-page" value="<?php echo esc_attr($this->menu); ?>" />
                    <?php $plugin_table->display(); ?>
                </form>

            </div>
            <?php
        }

        protected function do_plugin_install() {
            // All plugin information will be stored in an array for processing.
            $plugin = array();
        
            // Checks for actions from hover links to process the installation.
            if ( isset( $_GET['plugin'] ) && ( isset( $_GET['brainforward_plugins-install'] ) && 'install-plugin' == $_GET['brainforward_plugins-install'] ) ) {
                check_admin_referer( 'brainforward_plugins-install' );
        
                // Sanitize input values
                $plugin['name']   = isset($_GET['plugin_name']) ? sanitize_text_field($_GET['plugin_name']) : '';
                $plugin['slug']   = isset($_GET['plugin']) ? sanitize_text_field($_GET['plugin']) : '';
                $plugin['source'] = isset($_GET['plugin_source']) ? esc_url_raw($_GET['plugin_source']) : '';
        
                $url = wp_nonce_url(
                    add_query_arg(
                        array(
                            'page'          => urlencode( $this->menu ),
                            'plugin'        => urlencode( $plugin['slug'] ),
                            'plugin_name'   => urlencode( $plugin['name'] ),
                            'plugin_source' => urlencode( $plugin['source'] ),
                            'brainforward_plugins-install' => 'install-plugin',
                        ),
                        network_admin_url( 'themes.php' )
                    ),
                    'brainforward_plugins-install'
                );
        
                // Direct file operations instead of using WP_Filesystem
                if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), '', false, false, array( 'brainforward_plugins-install' ) ) ) ) {
                    return true;
                }
        
                // If credentials are provided, proceed with file operations
                if ( ! $creds ) {
                    request_filesystem_credentials( esc_url_raw( $url ), '', true, false, array( 'brainforward_plugins-install' ) );
                    return true;
                }
        
                require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api
                require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Need for upgrade classes
        
                // Set plugin source to WordPress API link if available.
                if ( 'repo' === $plugin['source'] ) {
                    $api = plugins_api( 'plugin_information', array( 'slug' => $plugin['slug'], 'fields' => array( 'sections' => false ) ) );
        
                    if ( is_wp_error( $api ) ) {
                        wp_die( $this->strings['oops'] . var_dump( $api ) );
                    }
        
                    if ( isset( $api->download_link ) ) {
                        $plugin['source'] = $api->download_link;
                    }
                }
        
                // Set type, based on whether the source starts with http:// or https://.
                $type = preg_match( '|^http(s)?://|', $plugin['source'] ) ? 'web' : 'upload';
        
                // Prep variables for Plugin_Installer_Skin class.
                $title = sprintf( $this->strings['installing'], $plugin['name'] );
                $url   = add_query_arg( array( 'action' => 'install-plugin', 'plugin' => urlencode( $plugin['slug'] ) ), 'update.php' );
                if ( isset( $_GET['from'] ) ) {
                    $url .= add_query_arg( 'from', urlencode( stripslashes( $_GET['from'] ) ), $url );
                }
        
                $url   = esc_url_raw( $url );
        
                $nonce = 'install-plugin_' . sanitize_key( $plugin['slug'] );
        
                // Prefix a default path to pre-packaged plugins.
                $source = ( 'upload' === $type ) ? $this->default_path . $plugin['source'] : $plugin['source'];
        
                // Create a new instance of Plugin_Upgrader.
                $upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'type', 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
        
                // Perform the action and install the plugin from the $source.
                $upgrader->install( $source );
        
                // Flush plugins cache so we can make sure that the installed plugins list is always up to date.
                wp_cache_flush();
        
                // Only activate plugins if the config option is set to true.
                if ( $this->is_automatic ) {
                    $plugin_activate = $upgrader->plugin_info(); // Grab the plugin info from the Plugin_Upgrader method.
                    $activate        = activate_plugin( $plugin_activate ); // Activate the plugin.
                    $this->populate_file_path(); // Re-populate the file path now that the plugin has been installed and activated.
        
                    if ( is_wp_error( $activate ) ) {
                        echo '<div id="message" class="error"><p>' . esc_html( $activate->get_error_message() ) . '</p></div>';
                        echo '<p><a href="' . esc_url( add_query_arg( 'page', urlencode( $this->menu ), network_admin_url( 'themes.php' ) ) ) . '" title="' . esc_attr( $this->strings['return'] ) . '" target="_parent">' . esc_html( $this->strings['return'] ) . '</a></p>';
                        return true; // End it here if there is an error with automatic activation
                    } else {
                        echo '<p>' . esc_html( $this->strings['plugin_activated'] ) . '</p>';
                    }
                }
        
                // Display message based on if all plugins are now active or not.
                $complete = array();
                foreach ( $this->plugins as $plugin ) {
                    if ( ! is_plugin_active_custom( $plugin['file_path'] ) ) {
                        echo '<p><a href="' . esc_url( add_query_arg( 'page', urlencode( $this->menu ), network_admin_url( 'themes.php' ) ) ) . '" title="' . esc_attr( $this->strings['return'] ) . '" target="_parent">' . esc_html( $this->strings['return'] ) . '</a></p>';
                        $complete[] = $plugin;
                        break;
                    } else {
                        $complete[] = '';
                    }
                }
        
                // Filter out any empty entries.
                $complete = array_filter( $complete );
        
                // All plugins are active, so we display the complete string and hide the plugin menu.
                if ( empty( $complete ) ) {
                    echo '<p>' .  sprintf( $this->strings['complete'], '<a href="' . esc_url( network_admin_url() ) . '" title="' . esc_attr__( 'Return to the Dashboard', 'brainforward' ) . '">' . esc_html__( 'Return to the Dashboard', 'brainforward' ) . '</a>' ) . '</p>';
                    echo '<style type="text/css">#adminmenu .wp-submenu li.current { display: none !important; }</style>';
                }
        
                return true;
            }
            // Checks for actions from hover links to process the activation.
            elseif ( isset( $_GET['plugin'] ) && ( isset( $_GET['brainforward_plugins-activate'] ) && 'activate-plugin' == $_GET['brainforward_plugins-activate'] ) ) {
                check_admin_referer( 'brainforward_plugins-activate', 'brainforward_plugins-activate-nonce' );
        
                // Sanitize input values
                $plugin['name']   = isset($_GET['plugin_name']) ? sanitize_text_field($_GET['plugin_name']) : '';
                $plugin['slug']   = isset($_GET['plugin']) ? sanitize_text_field($_GET['plugin']) : '';
                $plugin['source'] = isset($_GET['plugin_source']) ? esc_url_raw($_GET['plugin_source']) : '';
        
                // Populate $plugin array with necessary information.
                $plugin_data = get_plugins( '/' . $plugin['slug'] ); // Retrieve all plugins.
                $plugin_file = array_keys( $plugin_data ); // Retrieve all plugin files from installed plugins.
                $plugin_to_activate = $plugin['slug'] . '/' . $plugin_file[0]; // Match plugin slug with appropriate plugin file.
                $activate = activate_plugin( $plugin_to_activate ); // Activate the plugin.
        
                if ( is_wp_error( $activate ) ) {
                    echo '<div id="message" class="error"><p>' . esc_html( $activate->get_error_message() ) . '</p></div>';
                    echo '<p><a href="' . esc_url( add_query_arg( 'page', urlencode( $this->menu ), network_admin_url( 'themes.php' ) ) ) . '" title="' . esc_attr( $this->strings['return'] ) . '" target="_parent">' . esc_html( $this->strings['return'] ) . '</a></p>';
                    return true; // End it here if there is an error with activation.
                } else {
                    $action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : '';
                    // Make sure message doesn't display again if bulk activation is performed immediately after a single activation.
                    if ( ! isset( $action ) ) {
                        $msg = $this->strings['activated_successfully'] . ' <strong>' . esc_html( $plugin['name'] ) . '</strong>';
                        echo '<div id="message" class="updated"><p>' . esc_html( $msg ) . '</p></div>';
                    }
                }
            }
        
            return false;
        }
        

        public function notices() {

            global $current_screen;

            // Remove nag on the install page.
            if ( $this->is_brainforward_page() ) {
                return;
            }

            // Return early if the nag message has been dismissed.
            if ( get_user_meta( get_current_user_id(), 'brainforward_dismissed_notice', true ) ) {
                return;
            }

            $installed_plugins = get_plugins(); // Retrieve a list of all the plugins
            $this->populate_file_path();

            $message             = array(); // Store the messages in an array to be outputted after plugins have looped through.
            $install_link        = false;   // Set to false, change to true in loop if conditions exist, used for action link 'install'.
            $install_link_count  = 0;       // Used to determine plurality of install action link text.
            $activate_link       = false;   // Set to false, change to true in loop if conditions exist, used for action link 'activate'.
            $activate_link_count = 0;       // Used to determine plurality of activate action link text.

            foreach ( $this->plugins as $plugin ) {
                // If the plugin is installed and active, check for minimum version argument before moving forward.
                if ( is_plugin_active_custom( $plugin['file_path'] ) ) {
                    // A minimum version has been specified.
                    if ( isset( $plugin['version'] ) ) {
                        if ( isset( $installed_plugins[$plugin['file_path']]['Version'] ) ) {
                            // If the current version is less than the minimum required version, we display a message.
                            if ( version_compare( $installed_plugins[$plugin['file_path']]['Version'], $plugin['version'], '<' ) ) {
                                if ( current_user_can( 'install_plugins' ) ) {
                                    $message['notice_ask_to_update'][] = $plugin['name'];
                                } else {
                                    $message['notice_cannot_update'][] = $plugin['name'];
                                }
                            }
                        }
                        // Can't find the plugin, so iterate to the next condition.
                        else {
                            continue;
                        }
                    }
                    // No minimum version specified, so iterate over the plugin.
                    else {
                        continue;
                    }
                }

                // Not installed.
                if ( ! isset( $installed_plugins[$plugin['file_path']] ) ) {
                    $install_link = true; // We need to display the 'install' action link.
                    $install_link_count++; // Increment the install link count.
                    if ( current_user_can( 'install_plugins' ) ) {
                        if ( $plugin['required'] ) {
                            $message['notice_can_install_required'][] = $plugin['name'];
                        }
                        // This plugin is only recommended.
                        else {
                            $message['notice_can_install_recommended'][] = $plugin['name'];
                        }
                    }
                    // Need higher privileges to install the plugin.
                    else {
                        $message['notice_cannot_install'][] = $plugin['name'];
                    }
                }
                // Installed but not active.
                elseif ( is_plugin_inactive( $plugin['file_path'] ) ) {
                    $activate_link = true; // We need to display the 'activate' action link.
                    $activate_link_count++; // Increment the activate link count.
                    if ( current_user_can( 'activate_plugins' ) ) {
                        if ( isset( $plugin['required'] ) && $plugin['required'] ) {
                            $message['notice_can_activate_required'][] = $plugin['name'];
                        }
                        // This plugin is only recommended.
                        else {
                            $message['notice_can_activate_recommended'][] = $plugin['name'];
                        }
                    }
                    // Need higher privileges to activate the plugin.
                    else {
                        $message['notice_cannot_activate'][] = $plugin['name'];
                    }
                }
            }

            // If we have notices to display, we move forward.
            if ( ! empty( $message ) ) {
                krsort( $message ); // Sort messages.
                $rendered = ''; // Display all nag messages as strings.

                // If dismissable is false and a message is set, output it now.
                if ( ! $this->dismissable && ! empty( $this->dismiss_msg ) ) {
                    $rendered .= '<p><strong>' . wp_kses_post( $this->dismiss_msg ) . '</strong></p>';
                }

                // Grab all plugin names.
                foreach ( $message as $type => $plugin_groups ) {
                    $linked_plugin_groups = array();

                    // Count number of plugins in each message group to calculate singular/plural message.
                    $count = count( $plugin_groups );

                    // Loop through the plugin names to make the ones pulled from the .org repo linked.
                    foreach ( $plugin_groups as $plugin_group_single_name ) {
                        $external_url = $this->_get_plugin_data_from_name( $plugin_group_single_name, 'external_url' );
                        $source       = $this->_get_plugin_data_from_name( $plugin_group_single_name, 'source' );

                        if ( $external_url && preg_match( '|^http(s)?://|', $external_url ) ) {
                            $linked_plugin_groups[] = '<a href="' . esc_url( $external_url ) . '" title="' . esc_attr( $plugin_group_single_name ) . '" target="_blank">' . $plugin_group_single_name . '</a>';
                        }
                        elseif ( ! $source || preg_match( '|^http://wordpress.org/extend/plugins/|', $source ) ) {
                            $url = add_query_arg(
                                array(
                                    'tab'       => 'plugin-information',
                                    'plugin'    => urlencode( $this->_get_plugin_data_from_name( $plugin_group_single_name ) ),
                                    'TB_iframe' => 'true',
                                    'width'     => '640',
                                    'height'    => '500',
                                ),
                                network_admin_url( 'plugin-install.php' )
                            );

                            $linked_plugin_groups[] = '<a href="' . esc_url( $url ) . '" class="thickbox" title="' . esc_attr( $plugin_group_single_name ) . '">' . $plugin_group_single_name . '</a>';
                        }
                        else {
                            $linked_plugin_groups[] = $plugin_group_single_name; // No hyperlink.
                        }

                        if ( isset( $linked_plugin_groups ) && (array) $linked_plugin_groups ) {
                            $plugin_groups = $linked_plugin_groups;
                        }
                    }

                    $last_plugin = array_pop( $plugin_groups ); // Pop off last name to prep for readability.
                    $imploded    = empty( $plugin_groups ) ? '<em>' . $last_plugin . '</em>' : '<em>' . ( implode( ', ', $plugin_groups ) . '</em> and <em>' . $last_plugin . '</em>' );

                    $rendered .= '<p>' . sprintf( translate_nooped_plural( $this->strings[$type], $count, 'brainforward' ), $imploded, $count ) . '</p>';
                }

                // Setup variables to determine if action links are needed.
                $show_install_link  = $install_link ? '<a href="' . esc_url( add_query_arg( 'page', urlencode( $this->menu ), network_admin_url( 'themes.php' ) ) ) . '">' . translate_nooped_plural( $this->strings['install_link'], $install_link_count, 'brainforward' ) . '</a>' : '';
                $show_activate_link = $activate_link ? '<a href="' . esc_url( add_query_arg( 'page', urlencode( $this->menu ), network_admin_url( 'themes.php' ) ) ) . '">' . translate_nooped_plural( $this->strings['activate_link'], $activate_link_count, 'brainforward' ) . '</a>'  : '';

                // Define all of the action links.
                $action_links = apply_filters(
                    'brainforward_notice_action_links',
                    array(
                        'install'  => ( current_user_can( 'install_plugins' ) )  ? $show_install_link  : '',
                        'activate' => ( current_user_can( 'activate_plugins' ) ) ? $show_activate_link : '',
                        'dismiss'  => $this->dismissable ? '<a class="dismiss-notice" href="' . esc_url( add_query_arg( 'brainforward_plugins-dismiss', 'dismiss_admin_notices' ) ) . '" target="_parent">' . $this->strings['dismiss'] . '</a>' : '',
                    )
                );

                $action_links = array_filter( $action_links ); // Remove any empty array items.
                if ( $action_links ) {
                    $rendered .= '<p>' . implode( ' | ', $action_links ) . '</p>';
                }

                // Register the nag messages and prepare them to be processed.
                $nag_class = version_compare( $this->wp_version, '3.8', '<' ) ? 'updated' : 'update-nag';
                if ( ! empty( $this->strings['nag_type'] ) ) {
                    add_settings_error( 'brainforward', 'brainforward', $rendered, sanitize_html_class( strtolower( $this->strings['nag_type'] ) ) );
                } else {
                    add_settings_error( 'brainforward', 'brainforward', $rendered, $nag_class );
                }
            }

            // Admin options pages already output settings_errors, so this is to avoid duplication.
            if ( 'options-general' !== $current_screen->parent_base ) {
                settings_errors( 'brainforward' );
            }

        }

        public function dismiss() {

            if ( isset( $_GET['brainforward_plugins-dismiss'] ) ) {
                update_user_meta( get_current_user_id(), 'brainforward_dismissed_notice', 1 );
            }

        }

        public function register( $plugin ) {

            if ( ! isset( $plugin['slug'] ) || ! isset( $plugin['name'] ) ) {
                return;
            }

            foreach ( $this->plugins as $registered_plugin ) {
                if ( $plugin['slug'] == $registered_plugin['slug'] ) {
                    return;
                }
            }

            $this->plugins[] = $plugin;

        }

        public function config( $config ) {

            $keys = array( 'default_path', 'has_notices', 'dismissable', 'dismiss_msg', 'menu', 'is_automatic', 'message', 'strings' );

            foreach ( $keys as $key ) {
                if ( isset( $config[$key] ) ) {
                    if ( is_array( $config[$key] ) ) {
                        foreach ( $config[$key] as $subkey => $value ) {
                            $this->{$key}[$subkey] = $value;
                        }
                    } else {
                        $this->$key = $config[$key];
                    }
                }
            }

        }

        public function actions( $install_actions ) {

            // Remove action links on the Brainforward install page.
            if ( $this->is_brainforward_page() ) {
                return false;
            }

            return $install_actions;

        }

        public function flush_plugins_cache() {

            wp_cache_flush();

        }

        public function populate_file_path() {

            // Add file_path key for all plugins.
            foreach ( $this->plugins as $plugin => $values ) {
                $this->plugins[$plugin]['file_path'] = $this->_get_plugin_basename_from_slug( $values['slug'] );
            }

        }

        protected function _get_plugin_basename_from_slug( $slug ) {

            $keys = array_keys( get_plugins() );

            foreach ( $keys as $key ) {
                if ( preg_match( '|^' . $slug .'/|', $key ) ) {
                    return $key;
                }
            }

            return $slug;

        }

        protected function _get_plugin_data_from_name( $name, $data = 'slug' ) {

            foreach ( $this->plugins as $plugin => $values ) {
                if ( $name == $values['name'] && isset( $values[$data] ) ) {
                    return $values[$data];
                }
            }

            return false;

        }

        protected function is_brainforward_page() {

            if ( isset( $_GET['page'] ) && $this->menu === $_GET['page'] ) {
                return true;
            }

            return false;

        }
        
        public function update_dismiss() {

            delete_user_meta( get_current_user_id(), 'brainforward_dismissed_notice' );

        }
        
        public function force_activation() {

            // Set file_path parameter for any installed plugins.
            $this->populate_file_path();

            $installed_plugins = get_plugins();

            foreach ( $this->plugins as $plugin ) {
                // Oops, plugin isn't there so iterate to next condition.
                if ( isset( $plugin['force_activation'] ) && $plugin['force_activation'] && ! isset( $installed_plugins[$plugin['file_path']] ) ) {
                    continue;
                }
                // There we go, activate the plugin.
                elseif ( isset( $plugin['force_activation'] ) && $plugin['force_activation'] && is_plugin_inactive( $plugin['file_path'] ) ) {
                    activate_plugin( $plugin['file_path'] );
                }
            }

        }
        
        public function force_deactivation() {

            // Set file_path parameter for any installed plugins.
            $this->populate_file_path();

            foreach ( $this->plugins as $plugin ) {
                // Only proceed forward if the paramter is set to true and plugin is active.
                if ( isset( $plugin['force_deactivation'] ) && $plugin['force_deactivation'] && is_plugin_active_custom( $plugin['file_path'] ) ) {
                    deactivate_plugins( $plugin['file_path'] );
                }
            }

        }
        
        public static function get_instance() {

            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Brainforward_Plugins ) ) {
                self::$instance = new Brainforward_Plugins();
            }

            return self::$instance;

        }

    }

    // Ensure only one instance of the class is ever invoked.
    $brainforward_plugins = Brainforward_Plugins::get_instance();

}

if ( ! function_exists( 'brainforward_plugins' ) ) {
    function brainforward_plugins( $plugins, $config = array() ) {
        foreach ( $plugins as $plugin ) {
            Brainforward_Plugins::$instance->register( $plugin );
        }
        if ( $config ) {
            Brainforward_Plugins::$instance->config( $config );
        }
    }
}
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

function is_plugin_active_custom( $plugin ) {
    $active_plugins = get_option( 'active_plugins' );
    return in_array( $plugin, $active_plugins );
}


if ( ! class_exists( 'Brainforward_List_Table' ) ) {
    
    class Brainforward_List_Table extends WP_List_Table {
        
        public function __construct() {

            global $status, $page;

            parent::__construct(
                array(
                    'singular' => 'plugin',
                    'plural'   => 'plugins',
                    'ajax'     => false,
                )
            );

        }
        
        protected function _gather_plugin_data() {

            // Load thickbox for plugin links.
            Brainforward_Plugins::$instance->admin_init();
            Brainforward_Plugins::$instance->thickbox();

            // Prep variables for use and grab list of all installed plugins.
            $table_data        = array();
            $i                 = 0;
            $installed_plugins = get_plugins();

            foreach ( Brainforward_Plugins::$instance->plugins as $plugin ) {
                if ( is_plugin_active_custom( $plugin['file_path'] ) ) {
                    continue; // No need to display plugins if they are installed and activated.
                }

                $table_data[$i]['sanitized_plugin'] = $plugin['name'];
                $table_data[$i]['slug']             = $this->_get_plugin_data_from_name( $plugin['name'] );

                $external_url = $this->_get_plugin_data_from_name( $plugin['name'], 'external_url' );
                $source       = $this->_get_plugin_data_from_name( $plugin['name'], 'source' );

                if ( $external_url && preg_match( '|^http(s)?://|', $external_url ) ) {
                    $table_data[$i]['plugin'] = '<strong><a href="' . esc_url( $external_url ) . '" title="' . esc_attr( $plugin['name'] ) . '" target="_blank">' . $plugin['name'] . '</a></strong>';
                }
                elseif ( ! $source || preg_match( '|^http://wordpress.org/extend/plugins/|', $source ) ) {
                    $url = add_query_arg(
                        array(
                           'tab'       => 'plugin-information',
                           'plugin'    => urlencode( $this->_get_plugin_data_from_name( $plugin['name'] ) ),
                           'TB_iframe' => 'true',
                           'width'     => '640',
                           'height'    => '500',
                        ),
                        network_admin_url( 'plugin-install.php' )
                    );

                    $table_data[$i]['plugin'] = '<strong><a href="' . esc_url( $url ) . '" class="thickbox" title="' . esc_attr( $plugin['name'] ) . '">' . $plugin['name'] . '</a></strong>';
                }
                else {
                    $table_data[$i]['plugin'] = '<strong>' . $plugin['name'] . '</strong>'; // No hyperlink.
                }

                if ( isset( $table_data[$i]['plugin'] ) && (array) $table_data[$i]['plugin'] ) {
                    $plugin['name'] = $table_data[$i]['plugin'];
                }

                if ( ! empty( $plugin['source'] ) ) {
                    // The plugin must be from a private repository.
                    if ( preg_match( '|^http(s)?://|', $plugin['source'] ) ) {
                        $table_data[$i]['source'] = __( 'Private Repository', 'brainforward' );
                    // The plugin is pre-packaged with the theme.
                    } else {
                        $table_data[$i]['source'] = __( 'Pre-Packaged', 'brainforward' );
                    }
                }
                // The plugin is from the WordPress repository.
                else {
                    $table_data[$i]['source'] = __( 'WordPress Repository', 'brainforward' );
                }

                $table_data[$i]['type'] = isset( $plugin['required'] ) && $plugin['required'] ? __( 'Required', 'brainforward' ) : __( 'Recommended', 'brainforward' );

                if ( ! isset( $installed_plugins[$plugin['file_path']] ) ) {
                    $table_data[$i]['status'] = sprintf( '%1$s', __( 'Not Installed', 'brainforward' ) );
                } elseif ( is_plugin_inactive( $plugin['file_path'] ) ) {
                    $table_data[$i]['status'] = sprintf( '%1$s', __( 'Installed But Not Activated', 'brainforward' ) );
                }

                $table_data[$i]['file_path'] = $plugin['file_path'];
                $table_data[$i]['url']       = isset( $plugin['source'] ) ? $plugin['source'] : 'repo';

                $i++;
            }

            // Sort plugins by Required/Recommended type and by alphabetical listing within each type.
            $resort = array();
            $req    = array();
            $rec    = array();

            // Grab all the plugin types.
            foreach ( $table_data as $plugin ) {
                $resort[] = $plugin['type'];
            }

            // Sort each plugin by type.
            foreach ( $resort as $type ) {
                if ( 'Required' == $type ) {
                    $req[] = $type;
                } else {
                    $rec[] = $type;
                }
            }

            // Sort alphabetically each plugin type array, merge them and then sort in reverse (lists Required plugins first).
            sort( $req );
            sort( $rec );
            array_merge( $resort, $req, $rec );
            array_multisort( $resort, SORT_DESC, $table_data );

            return $table_data;

        }
        
        protected function _get_plugin_data_from_name( $name, $data = 'slug' ) {

            foreach ( Brainforward_Plugins::$instance->plugins as $plugin => $values ) {
                if ( $name == $values['name'] && isset( $values[$data] ) ) {
                    return $values[$data];
                }
            }

            return false;

        }
        
        public function column_default( $item, $column_name ) {

            switch ( $column_name ) {
                case 'source':
                case 'type':
                case 'status':
                    return $item[$column_name];
            }

        }
        
        public function column_plugin( $item ) {

            $installed_plugins = get_plugins();

            // No need to display any hover links.
            if ( is_plugin_active_custom( $item['file_path'] ) ) {
                $actions = array();
            }

            // We need to display the 'Install' hover link.
            if ( ! isset( $installed_plugins[$item['file_path']] ) ) {
                $actions = array(
                    'install' => sprintf(
                        '<a href="%1$s" title="' . esc_attr__( 'Install', 'brainforward' ) . ' %2$s">' . __( 'Install', 'brainforward' ) . '</a>',
                        esc_url(
						    wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'page'          => urlencode( Brainforward_Plugins::$instance->menu ),
                                        'plugin'        => urlencode( $item['slug'] ),
                                        'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
                                        'plugin_source' => urlencode( $item['url'] ),
                                        'brainforward_plugins-install' => 'install-plugin',
                                    ),
                                    network_admin_url( 'themes.php' )
                                ),
                               'brainforward_plugins-install'
                            )
                        ),
                        $item['sanitized_plugin']
                    ),
                );
            }
            // We need to display the 'Activate' hover link.
            elseif ( is_plugin_inactive( $item['file_path'] ) ) {
                $actions = array(
                    'activate' => sprintf(
                        '<a href="%1$s" title="' . esc_attr__( 'Activate', 'brainforward' ) . ' %2$s">' . __( 'Activate', 'brainforward' ) . '</a>',
                        esc_url(
                            add_query_arg(
                                array(
                                    'page'                 => urlencode( Brainforward_Plugins::$instance->menu ),
                                    'plugin'               => urlencode( $item['slug'] ),
                                    'plugin_name'          => urlencode( $item['sanitized_plugin'] ),
                                    'plugin_source'        => urlencode( $item['url'] ),
                                    'brainforward_plugins-activate'       => 'activate-plugin',
                                    'brainforward_plugins-activate-nonce' => urlencode( wp_create_nonce( 'brainforward_plugins-activate' ) ),
                                ),
                                network_admin_url( 'themes.php' )
                            )
                        ),
                        $item['sanitized_plugin']
                    ),
                );
            }

            return sprintf( '%1$s %2$s', $item['plugin'], $this->row_actions( $actions ) );

        }
        
        public function column_cb( $item ) {

        	$plugin_url = ( 'repo' === $item['url'] ) ? $item['url'] : esc_url( $item['url'] );
            $value = $item['file_path'] . ',' . $plugin_url . ',' . $item['sanitized_plugin'];
            return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" id="%3$s" />', esc_attr( $this->_args['singular'] ), esc_attr( $value ), esc_attr( $item['sanitized_plugin'] ) );

        }
        
        public function no_items() {

            printf( __( 'No plugins to install or activate. <a href="%1$s" title="Return to the Dashboard">Return to the Dashboard</a>', 'brainforward' ), network_admin_url() );
            echo '<style type="text/css">#adminmenu .wp-submenu li.current { display: none !important; }</style>';

        }
        
        public function get_columns() {

            $columns = array(
                'cb'     => '<input type="checkbox" />',
                'plugin' => __( 'Plugin', 'brainforward' ),
                'source' => __( 'Source', 'brainforward' ),
                'type'   => __( 'Type', 'brainforward' ),
                'status' => __( 'Status', 'brainforward' )
            );

            return $columns;

        }
        
        public function get_bulk_actions() {

            $actions = array(
                'brainforward_plugins-bulk-install'  => __( 'Install', 'brainforward' ),
                'brainforward_plugins-bulk-activate' => __( 'Activate', 'brainforward' ),
            );

            return $actions;

        }
        
        public function process_bulk_actions() {

            // Bulk installation process.
            if ( 'brainforward_plugins-bulk-install' === $this->current_action() ) {
                check_admin_referer( 'bulk-' . $this->_args['plural'] );
        
                // Prep variables to be populated.
                $plugins_to_install = array();
                $plugin_installs    = array();
                $plugin_path        = array();
                $plugin_name        = array();
        
                if ( isset( $_GET['plugins'] ) ) {
                    $plugins = explode( ',', stripslashes( $_GET['plugins'] ) );
                } elseif ( isset( $_POST['plugin'] ) ) {
                    $plugins = (array) $_POST['plugin'];
                } else {
                    $plugins = array();
                }
        
                if ( isset( $_POST['plugin'] ) ) {
                    foreach ( $plugins as $plugin_data ) {
                        $plugins_to_install[] = explode( ',', $plugin_data );
                    }
        
                    foreach ( $plugins_to_install as $plugin_data ) {
                        $plugin_installs[] = $plugin_data[0];
                        $plugin_path[]     = $plugin_data[1];
                        $plugin_name[]     = $plugin_data[2];
                    }
                } else {
                    foreach ( $plugins as $key => $value ) {
                        if ( 0 == $key % 3 || 0 == $key ) {
                            $plugins_to_install[] = $value;
                            $plugin_installs[]    = $value;
                        }
                    }
                }
        
                if ( isset( $_GET['plugin_paths'] ) ) {
                    $plugin_paths = explode( ',', stripslashes( $_GET['plugin_paths'] ) );
                } elseif ( isset( $_POST['plugin'] ) ) {
                    $plugin_paths = (array) $plugin_path;
                } else {
                    $plugin_paths = array();
                }
        
                if ( isset( $_GET['plugin_names'] ) ) {
                    $plugin_names = explode( ',', stripslashes( $_GET['plugin_names'] ) );
                } elseif ( isset( $_POST['plugin'] ) ) {
                    $plugin_names = (array) $plugin_name;
                } else {
                    $plugin_names = array();
                }
        
                $i = 0;
                foreach ( $plugin_installs as $key => $plugin ) {
                    if ( preg_match( '|.php$|', $plugin ) ) {
                        unset( $plugin_installs[$key] );
        
                        if ( ! isset( $_GET['plugin_paths'] ) )
                            unset( $plugin_paths[$i] );
        
                        if ( ! isset( $_GET['plugin_names'] ) )
                            unset( $plugin_names[$i] );
                    }
                    $i++;
                }
        
                if ( empty( $plugin_installs ) ) {
                    return false;
                }
        
                $plugin_installs = array_values( $plugin_installs );
                $plugin_paths    = array_values( $plugin_paths );
                $plugin_names    = array_values( $plugin_names );
        
                $plugin_installs = array_map( 'urldecode', $plugin_installs );
                $plugin_paths    = array_map( 'urldecode', $plugin_paths );
                $plugin_names    = array_map( 'urldecode', $plugin_names );
        
                $url = wp_nonce_url(
                    add_query_arg(
                        array(
                            'page'          => urlencode( Brainforward_Plugins::$instance->menu ),
                            'brainforward_plugins-action'  => 'install-selected',
                            'plugins'       => urlencode( implode( ',', $plugins ) ),
                            'plugin_paths'  => urlencode( implode( ',', $plugin_paths ) ),
                            'plugin_names'  => urlencode( implode( ',', $plugin_names ) ),
                        ),
                        network_admin_url( 'themes.php' )
                    ),
                    'bulk-plugins'
                );
                $method = ''; 
                $fields = array( 'action', '_wp_http_referer', '_wpnonce' );
        
                if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
                    return true;
                }
        
                // Check for filesystem credentials and handle errors.
                if ( ! $creds ) {
                    request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
                    return true;
                }
        
                require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api
                require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Need for upgrade classes
        
                // Store all information in arrays since we are processing a bulk installation.
                $api          = array();
                $sources      = array();
                $install_path = array();
        
                $i = 0;
                foreach ( $plugin_installs as $plugin ) {
                    $api[$i] = plugins_api( 'plugin_information', array( 'slug' => $plugin, 'fields' => array( 'sections' => false ) ) ) ? plugins_api( 'plugin_information', array( 'slug' => $plugin, 'fields' => array( 'sections' => false ) ) ) : (object) $api[$i] = 'brainforward_plugins-empty';
                    $i++;
                }
        
                if ( is_wp_error( $api ) ) {
                    wp_die( Brainforward_Plugins::$instance->strings['oops'] . var_dump( $api ) );
                }
        
                $i = 0;
                foreach ( $api as $object ) {
                    $sources[$i] = isset( $object->download_link ) && 'repo' == $plugin_paths[$i] ? $object->download_link : $plugin_paths[$i];
                    $i++;
                }
        
                $url   = esc_url_raw( add_query_arg( array( 'page' => urlencode( Brainforward_Plugins::$instance->menu ) ), network_admin_url( 'themes.php' ) ) );
                $nonce = 'bulk-plugins';
                $names = $plugin_names;
        
                $installer = new TGM_Bulk_Installer( $skin = new TGM_Bulk_Installer_Skin( compact( 'url', 'nonce', 'names' ) ) );
        
                echo '<div class="brainforward_plugins wrap">';
                    if ( version_compare( Brainforward_Plugins::$instance->wp_version, '3.8', '<' ) ) {
                        echo '<div class="notice notice-warning"><p>';
                        _e( 'This theme requires WordPress version 3.8 or higher. Please upgrade your WordPress installation.', 'brainforward' );
                        echo '</p></div>';
                    }
                    echo '<h2>' . esc_html( get_admin_page_title() ) . '</h2>';
                    $installer->bulk_install( $sources );
                echo '</div>';
        
                return true;
            }
        
            // Bulk activation process.
            if ( 'brainforward_plugins-bulk-activate' === $this->current_action() ) {
                check_admin_referer( 'bulk-' . $this->_args['plural'] );
        
                $plugins             = isset( $_POST['plugin'] ) ? (array) $_POST['plugin'] : array();
                $plugins_to_activate = array();
        
                foreach ( $plugins as $i => $plugin ) {
                    $plugins_to_activate[] = explode( ',', $plugin );
                }
        
                foreach ( $plugins_to_activate as $i => $array ) {
                    if ( ! preg_match( '|.php$|', $array[0] ) ) {
                        unset( $plugins_to_activate[$i] );
                    }
                }
        
                if ( empty( $plugins_to_activate ) ) {
                    return;
                }
        
                $plugins      = array();
                $plugin_names = array();
        
                foreach ( $plugins_to_activate as $plugin_string ) {
                    $plugins[]      = $plugin_string[0];
                    $plugin_names[] = $plugin_string[2];
                }
        
                $count       = count( $plugin_names );
                $last_plugin = array_pop( $plugin_names );
                $imploded    = empty( $plugin_names ) ? '<strong>' . $last_plugin . '</strong>' : '<strong>' . ( implode( ', ', $plugin_names ) . '</strong> and <strong>' . $last_plugin . '</strong>.' );
        
                $activate = activate_plugins( $plugins );
        
                if ( is_wp_error( $activate ) ) {
                    echo '<div id="message" class="error"><p>' . $activate->get_error_message() . '</p></div>';
                } else {
                    printf( '<div id="message" class="updated"><p>%1$s %2$s</p></div>', _n( 'The following plugin was activated successfully:', 'The following plugins were activated successfully:', $count, 'brainforward' ), $imploded );
                }
        
                $recent = (array) get_option( 'recently_activated' );
        
                foreach ( $plugins as $plugin => $time ) {
                    if ( isset( $recent[$plugin] ) ) {
                        unset( $recent[$plugin] );
                    }
                }
        
                update_option( 'recently_activated', $recent );
        
                unset( $_POST );
            }
        }
        

        public function prepare_items() {

            $per_page              = 100; // Set it high so we shouldn't have to worry about pagination.
            $columns               = $this->get_columns(); // Get all necessary column information.
            $hidden                = array(); // No columns to hide, but we must set as an array.
            $sortable              = array(); // No reason to make sortable columns.
            $this->_column_headers = array( $columns, $hidden, $sortable ); // Get all necessary column headers.

            // Process our bulk actions here.
            $this->process_bulk_actions();

            // Store all of our plugin data into $items array so WP_List_Table can use it.
            $this->items = $this->_gather_plugin_data();

        }

    }
}



if ( ! class_exists( 'WP_Upgrader' ) && ( isset( $_GET['page'] ) && Brainforward_Plugins::$instance->menu === $_GET['page'] ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    if ( ! class_exists( 'TGM_Bulk_Installer' ) ) {
        
        class TGM_Bulk_Installer extends WP_Upgrader {
            
            public $result;
            
            public $bulk = false;
            
            public function bulk_install( $packages ) {

                // Pass installer skin object and set bulk property to true.
                $this->init();
                $this->bulk = true;

                // Set install strings and automatic activation strings (if config option is set to true).
                $this->install_strings();
                if ( Brainforward_Plugins::$instance->is_automatic ) {
                    $this->activate_strings();
                }

                // Run the header string to notify user that the process has begun.
                $this->skin->header();

                // Connect to the Filesystem.
                $res = $this->fs_connect( array( WP_CONTENT_DIR, WP_PLUGIN_DIR ) );
                if ( ! $res ) {
                    $this->skin->footer();
                    return false;
                }

                // Set the bulk header and prepare results array.
                $this->skin->bulk_header();
                $results = array();

                // Get the total number of packages being processed and iterate as each package is successfully installed.
                $this->update_count   = count( $packages );
                $this->update_current = 0;

                // Loop through each plugin and process the installation.
                foreach ( $packages as $plugin ) {
                    $this->update_current++; // Increment counter.

                    // Do the plugin install.
                    $result = $this->run(
                        array(
                            'package'           => $plugin, // The plugin source.
                            'destination'       => WP_PLUGIN_DIR, // The destination dir.
                            'clear_destination' => false, // Do we want to clear the destination or not?
                            'clear_working'     => true, // Remove original install file.
                            'is_multi'          => true, // Are we processing multiple installs?
                            'hook_extra'        => array( 'plugin' => $plugin, ), // Pass plugin source as extra data.
                        )
                    );

                    // Store installation results in result property.
                    $results[$plugin] = $this->result;

                    // Prevent credentials auth screen from displaying multiple times.
                    if ( false === $result ) {
                        break;
                    }
                }

                // Pass footer skin strings.
                $this->skin->bulk_footer();
                $this->skin->footer();

                // Return our results.
                return $results;

            }

            public function run( $options ) {

                // Default config options.
                $defaults = array(
                    'package'           => '',
                    'destination'       => '',
                    'clear_destination' => false,
                    'clear_working'     => true,
                    'is_multi'          => false,
                    'hook_extra'        => array(),
                );

                // Parse default options with config options from $this->bulk_upgrade and extract them.
                $options = wp_parse_args( $options, $defaults );
                extract( $options );

                // Connect to the Filesystem.
                $res = $this->fs_connect( array( WP_CONTENT_DIR, $destination ) );
                if ( ! $res ) {
                    return false;
                }

                // Return early if there is an error connecting to the Filesystem.
                if ( is_wp_error( $res ) ) {
                    $this->skin->error( $res );
                    return $res;
                }

                // Call $this->header separately if running multiple times.
                if ( ! $is_multi )
                    $this->skin->header();

                // Set strings before the package is installed.
                $this->skin->before();

                // Download the package (this just returns the filename of the file if the package is a local file).
                $download = $this->download_package( $package );
                if ( is_wp_error( $download ) ) {
                    $this->skin->error( $download );
                    $this->skin->after();
                    return $download;
                }

                // Don't accidentally delete a local file.
                $delete_package = ( $download != $package );

                // Unzip file into a temporary working directory.
                $working_dir = $this->unpack_package( $download, $delete_package );
                if ( is_wp_error( $working_dir ) ) {
                    $this->skin->error( $working_dir );
                    $this->skin->after();
                    return $working_dir;
                }

                // Install the package into the working directory with all passed config options.
                $result = $this->install_package(
                    array(
                        'source'            => $working_dir,
                        'destination'       => $destination,
                        'clear_destination' => $clear_destination,
                        'clear_working'     => $clear_working,
                        'hook_extra'        => $hook_extra,
                    )
                );

                // Pass the result of the installation.
                $this->skin->set_result( $result );

                // Set correct strings based on results.
                if ( is_wp_error( $result ) ) {
                    $this->skin->error( $result );
                    $this->skin->feedback( 'process_failed' );
                }
                // The plugin install is successful.
                else {
                    $this->skin->feedback( 'process_success' );
                }

                // Only process the activation of installed plugins if the automatic flag is set to true.
                if ( Brainforward_Plugins::$instance->is_automatic ) {
                    // Flush plugins cache so we can make sure that the installed plugins list is always up to date.
                    wp_cache_flush();

                    // Get the installed plugin file and activate it.
                    $plugin_info = $this->plugin_info( $package );
                    $activate    = activate_plugin( $plugin_info );

                    // Re-populate the file path now that the plugin has been installed and activated.
                    Brainforward_Plugins::$instance->populate_file_path();

                    // Set correct strings based on results.
                    if ( is_wp_error( $activate ) ) {
                        $this->skin->error( $activate );
                        $this->skin->feedback( 'activation_failed' );
                    }
                    // The plugin activation is successful.
                    else {
                        $this->skin->feedback( 'activation_success' );
                    }
                }

                // Flush plugins cache so we can make sure that the installed plugins list is always up to date.
                wp_cache_flush();

                // Set install footer strings.
                $this->skin->after();
                if ( ! $is_multi ) {
                    $this->skin->footer();
                }

                return $result;

            }

            /**
             * Sets the correct install strings for the installer skin to use.
             *
             * @since 2.2.0
             */
            public function install_strings() {

                $this->strings['no_package']          = __( 'Install package not available.', 'brainforward' );
                $this->strings['downloading_package'] = __( 'Downloading install package from <span class="code">%s</span>&#8230;', 'brainforward' );
                $this->strings['unpack_package']      = __( 'Unpacking the package&#8230;', 'brainforward' );
                $this->strings['installing_package']  = __( 'Installing the plugin&#8230;', 'brainforward' );
                $this->strings['process_failed']      = __( 'Plugin install failed.', 'brainforward' );
                $this->strings['process_success']     = __( 'Plugin installed successfully.', 'brainforward' );

            }
            public function activate_strings() {

                $this->strings['activation_failed']  = __( 'Plugin activation failed.', 'brainforward' );
                $this->strings['activation_success'] = __( 'Plugin activated successfully.', 'brainforward' );

            }
            
            public function plugin_info() {

                // Return false if installation result isn't an array or the destination name isn't set.
                if ( ! is_array( $this->result ) ) {
                    return false;
                }

                if ( empty( $this->result['destination_name'] ) ) {
                    return false;
                }

                /// Get the installed plugin file or return false if it isn't set.
                $plugin = get_plugins( '/' . $this->result['destination_name'] );
                if ( empty( $plugin ) ) {
                    return false;
                }

                // Assume the requested plugin is the first in the list.
                $pluginfiles = array_keys( $plugin );

                return $this->result['destination_name'] . '/' . $pluginfiles[0];

            }

        }
    }

    if ( ! class_exists( 'TGM_Bulk_Installer_Skin' ) ) {
        class TGM_Bulk_Installer_Skin extends Bulk_Upgrader_Skin {
            
            public $plugin_info = array();

            
            public $plugin_names = array();

            
            public $i = 0;

            
            public function __construct( $args = array() ) {

                // Parse default and new args.
                $defaults = array( 'url' => '', 'nonce' => '', 'names' => array() );
                $args     = wp_parse_args( $args, $defaults );

                // Set plugin names to $this->plugin_names property.
                $this->plugin_names = $args['names'];

                // Extract the new args.
                parent::__construct( $args );

            }

            public function add_strings() {

                // Automatic activation strings.
                if ( Brainforward_Plugins::$instance->is_automatic ) {
                    $this->upgrader->strings['skin_upgrade_start']        = __( 'The installation and activation process is starting. This process may take a while on some hosts, so please be patient.', 'brainforward' );
                    $this->upgrader->strings['skin_update_successful']    = __( '%1$s installed and activated successfully.', 'brainforward' ) . ' <a onclick="%2$s" href="#" class="hide-if-no-js"><span>' . __( 'Show Details', 'brainforward' ) . '</span><span class="hidden">' . __( 'Hide Details', 'brainforward' ) . '</span>.</a>';
                    $this->upgrader->strings['skin_upgrade_end']          = __( 'All installations and activations have been completed.', 'brainforward' );
                    $this->upgrader->strings['skin_before_update_header'] = __( 'Installing and Activating Plugin %1$s (%2$d/%3$d)', 'brainforward' );
                }
                // Default installation strings.
                else {
                    $this->upgrader->strings['skin_upgrade_start']        = __( 'The installation process is starting. This process may take a while on some hosts, so please be patient.', 'brainforward' );
                    $this->upgrader->strings['skin_update_failed_error']  = __( 'An error occurred while installing %1$s: <strong>%2$s</strong>.', 'brainforward' );
                    $this->upgrader->strings['skin_update_failed']        = __( 'The installation of %1$s failed.', 'brainforward' );
                    $this->upgrader->strings['skin_update_successful']    = __( '%1$s installed successfully.', 'brainforward' ) . ' <a onclick="%2$s" href="#" class="hide-if-no-js"><span>' . __( 'Show Details', 'brainforward' ) . '</span><span class="hidden">' . __( 'Hide Details', 'brainforward' ) . '</span>.</a>';
                    $this->upgrader->strings['skin_upgrade_end']          = __( 'All installations have been completed.', 'brainforward' );
                    $this->upgrader->strings['skin_before_update_header'] = __( 'Installing Plugin %1$s (%2$d/%3$d)', 'brainforward' );
                }

            }

            public function before( $title = '' ) {

                // We are currently in the plugin installation loop, so set to true.
                $this->in_loop = true;

                printf( '<h4>' . $this->upgrader->strings['skin_before_update_header'] . ' <img alt="" src="' . admin_url( 'images/wpspin_light.gif' ) . '" class="hidden waiting-' . $this->upgrader->update_current . '" style="vertical-align:middle;" /></h4>', $this->plugin_names[$this->i], $this->upgrader->update_current, $this->upgrader->update_count );
                echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').show();</script>';
                echo '<div class="update-messages hide-if-js" id="progress-' . esc_attr( $this->upgrader->update_current ) . '"><p>';

                // Flush header output buffer.
                $this->before_flush_output();

            }

            public function after( $title = '' ) {

                // Close install strings.
                echo '</p></div>';

                // Output error strings if an error has occurred.
                if ( $this->error || ! $this->result ) {
                    if ( $this->error ) {
                        echo '<div class="error"><p>' . sprintf( $this->upgrader->strings['skin_update_failed_error'], $this->plugin_names[$this->i], $this->error ) . '</p></div>';
                    } else {
                        echo '<div class="error"><p>' . sprintf( $this->upgrader->strings['skin_update_failed'], $this->plugin_names[$this->i] ) . '</p></div>';
                    }

                    echo '<script type="text/javascript">jQuery(\'#progress-' . esc_js( $this->upgrader->update_current ) . '\').show();</script>';
                }

                // If the result is set and there are no errors, success!
                if ( ! empty( $this->result ) && ! is_wp_error( $this->result ) ) {
                    echo '<div class="updated"><p>' . sprintf( $this->upgrader->strings['skin_update_successful'], $this->plugin_names[$this->i], 'jQuery(\'#progress-' . esc_js( $this->upgrader->update_current ) . '\').toggle();jQuery(\'span\', this).toggle(); return false;' ) . '</p></div>';
                    echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').hide();</script>';
                }

                // Set in_loop and error to false and flush footer output buffer.
                $this->reset();
                $this->after_flush_output();

            }

            public function bulk_footer() {

                // Serve up the string to say installations (and possibly activations) are complete.
                parent::bulk_footer();

                // Flush plugins cache so we can make sure that the installed plugins list is always up to date.
                wp_cache_flush();

                // Display message based on if all plugins are now active or not.
                $complete = array();
                foreach ( Brainforward_Plugins::$instance->plugins as $plugin ) {
                    if ( ! is_plugin_active_custom( $plugin['file_path'] ) ) {
                        echo '<p><a href="' . esc_url( add_query_arg( 'page', urlencode( Brainforward_Plugins::$instance->menu ), network_admin_url( 'themes.php' ) ) ) . '" title="' . esc_attr( Brainforward_Plugins::$instance->strings['return'] ) . '" target="_parent">' . Brainforward_Plugins::$instance->strings['return'] . '</a></p>';
                        $complete[] = $plugin;
                        break;
                    }
                    // Nothing to store.
                    else {
                        $complete[] = '';
                    }
                }

                // Filter out any empty entries.
                $complete = array_filter( $complete );

                // All plugins are active, so we display the complete string and hide the menu to protect users.
                if ( empty( $complete ) ) {
                    echo '<p>' .  sprintf( Brainforward_Plugins::$instance->strings['complete'], '<a href="' . esc_url( network_admin_url() ) . '" title="' . esc_attr__( 'Return to the Dashboard', 'brainforward' ) . '">' . __( 'Return to the Dashboard', 'brainforward' ) . '</a>' ) . '</p>';
                    echo '<style type="text/css">#adminmenu .wp-submenu li.current { display: none !important; }</style>';
                }

            }

            public function before_flush_output() {

                wp_ob_end_flush_all();
                flush();

            }

            public function after_flush_output() {

                wp_ob_end_flush_all();
                flush();
                $this->i++;

            }

        }
    }
}


function brainforward_register_required_plugins() {
    $plugins = array(
        // array(
        //     'name'      => 'Brainforward',
        //     'slug'      => 'brainforward',
        //     'source'    => 'https://wp.brainforward.com/brainforward/dummy-data/brainforward.zip',
        //     'required'  => false,
        // ),
        array(
            'name'      => 'MailChimp for WordPress',
            'slug'      => 'mailchimp-for-wp',
            'required'  => false,
        ),
        array(
            'name'      => 'Classic Editor',
            'slug'      => 'classic-editor',
            'required'  => false,
        ),
        array(
            'name'      => 'One Click Demo Import',
            'slug'      => 'one-click-demo-import',
            'required'  => false,
        ),
        array(
            'name'      => 'Elementor Page Builder',
            'slug'      => 'elementor',
            'required'  => false,
        ),
        array(
            'name'      => 'Contact Form 7',
            'slug'      => 'contact-form-7',
            'required'  => false,
        ),
        array(
            'name'      => 'WooCommerce',
            'slug'      => 'woocommerce',
            'required'  => false,
        ),
    );

    $config = array(
        'id'           => 'brainforward',
        'default_path' => '',
        'menu'         => 'brainforward_plugins-install-plugins',
        'has_notices'  => true,
        'dismissable'  => true,
        'dismiss_msg'  => '',
        'is_automatic' => false,
        'message'      => '',
    );

    brainforward_plugins($plugins, $config);
}


add_action('brainforward_register', 'brainforward_register_required_plugins');