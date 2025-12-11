<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Codexse_Addons_Features {

    private static $instance = null;

    /**
     * Singleton instance.
     *
     * @return self
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     * Add hooks and initialize features.
     */
    private function __construct() {
        add_action( 'elementor/init', [ $this, 'init_features' ] );
    }

    /**
     * Initialize features by loading required files.
     */
    public function init_features() {
        $features_list = $this->get_features_list();
        foreach ( $features_list as $key => $option ) {
            $features_title = isset( $option['title'] ) ? sanitize_text_field( $option['title'] ) : '';
            if ( empty( $features_title ) ) {
                continue; // Skip if title is empty
            }

            $features_file_name = strtolower( str_replace( ' ', '_', $features_title ) );
            $features_file_path = CODEXSE_FEATURES_DIR . $features_file_name . '.php';

            // Check if the feature is enabled and the file exists
            if ( ! file_exists( $features_file_path ) || Codexse_Addons_Functions::get_features_status( 'codexse_' . $features_file_name ) !== 'on' ) {
                continue;
            }

            require_once $features_file_path;
        }
    }

    /**
     * Get the list of features.
     *
     * @return array
     */
    private function get_features_list() {
        $features_list = [
            'scroll_effect' => [
                'title' => 'Scroll Effect',
            ],
            'floating_effect' => [
                'title' => 'Floating Effect',
            ],
        ];

        /**
         * Filter the features list to allow additional features.
         *
         * @param array $features_list
         */
        return apply_filters( 'codexse_features_list', $features_list );
    }
}

// Initialize the class instance
Codexse_Addons_Features::instance();
