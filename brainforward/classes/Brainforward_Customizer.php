<?php

class Brainforward_Customizer {

    public function __construct() {
        add_action( 'customize_register', [$this, 'customize_register'] );
        add_action( 'customize_preview_init', [$this, 'customize_preview_js'] );
        add_action( 'wp_head', [$this, 'header_output']);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_google_fonts'));
    }

    public function customize_register( $wp_customize ) {
        $this->set_customizer_settings( $wp_customize );
    }

    private function set_customizer_settings( $wp_customize ) {
        $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
        $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

        // Light Logo Setting
        $wp_customize->add_setting('brainforward_light_logo', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control(new WP_Customize_Image_Control(
            $wp_customize,
            'brainforward_light_logo',
            array(
                'label'    => __('Light Logo', 'brainforward'),
                'section'  => 'title_tagline', // You can change this to your custom section if needed
                'settings' => 'brainforward_light_logo',
                'priority' => 9,
            )
        ));

        // Navbar Logo Width Setting
        $wp_customize->add_setting('navbar_logo_width_setting', array(
            'default'           => 160,
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('navbar_logo_width_control', array(
            'label'       => __('Logo Width', 'brainforward'),
            'section'     => 'title_tagline', // Make sure this section exists
            'settings'    => 'navbar_logo_width_setting',
            'type'        => 'number', // No need for input_type here
            'description' => __('Set the width of the Navbar logo in pixels.', 'brainforward'),
        ));


        // Add a new panel for theme options
        $wp_customize->add_panel( 'brainforward_theme_options_panel',
            array(
            'title'       => __( 'Theme Options', 'brainforward' ),
            'capability'  => 'edit_theme_options',
            'priority'    => 0,
            )
        );

        // Section for Typography Settings
        $wp_customize->add_section( 'brainforward_typography_section',
            array(
            'title'       => __( 'Typography', 'brainforward' ),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainforward_theme_options_panel',
            )
        );

        // Add a setting for Body Font Family
        $wp_customize->add_setting('body_font_family_setting', array(
            'default'           => 'Roboto', // Default font
            'sanitize_callback' => 'sanitize_text_field',
        ));

        // Add a control for Body Font Family
        $wp_customize->add_control('body_font_family_control', array(
            'label'   => __('Body Font Family', 'brainforward'),
            'section' => 'brainforward_typography_section',
            'settings'    => 'body_font_family_setting',
            'type'    => 'text',
            'choices' => Brainforward_Functions::get_popular_google_fonts(),
            'description' => __('Enter the font family name here. For example: "Nata Sans"', 'brainforward'),
        ));

        // Add a setting for Body Font Weight
        $wp_customize->add_setting('body_font_weight_setting', array(
            'default'           => '',  // Default semicolon-separated font weights
            'sanitize_callback' => array($this, 'sanitize_font_weight'),  // Sanitize using the class method
        ));

        // Add a control for Body Font Weight
        $wp_customize->add_control('body_font_weight_control', array(
            'label'       => __('Body Font Weight (semicolon-separated)', 'brainforward'),
            'section'     => 'brainforward_typography_section',
            'settings'    => 'body_font_weight_setting',
            'type'        => 'text',
            'description' => __('Enter one or more font weights as needed, separated by semicolons (e.g., 300;400;500;600).', 'brainforward'),
        ));

        // Add a setting for Body Font Size
        $wp_customize->add_setting('body_font_size_setting', array(
            'default'           => '16px',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        // Add a control for Body Font Size
        $wp_customize->add_control('body_font_size_control', array(
            'label'       => __('Body Font Size (px)', 'brainforward'),
            'section'     => 'brainforward_typography_section',
            'settings'    => 'body_font_size_setting',
            'type'        => 'text',
            'description' => __('Enter a value in px units, e.g., 16px', 'brainforward'),
        ));

        // Add a setting for Heading Font Family
        $wp_customize->add_setting('heading_font_family_setting', array(
            'default'           => 'Bebas Neue', // Default heading font
            'sanitize_callback' => 'sanitize_text_field',
        ));

        // Add a control for Heading Font Family
        $wp_customize->add_control('heading_font_family_control', array(
            'label'   => __('Heading Font Family', 'brainforward'),
            'section' => 'brainforward_typography_section',
            'settings'    => 'heading_font_family_setting',
            'type'    => 'text',
            'choices' => Brainforward_Functions::get_popular_google_fonts(),
            'description' => __('Enter the font family name here. For example: "Nata Sans"', 'brainforward'),
        ));

        // Add a setting for Heading Font Weight
        $wp_customize->add_setting('heading_font_weight_setting', array(
            'default'           => '',  // Default semicolon-separated font weights
            'sanitize_callback' => array($this, 'sanitize_font_weight'),  // Sanitize using the class method
        ));

        // Add a control for Heading Font Weight
        $wp_customize->add_control('heading_font_weight_control', array(
            'label'       => __('Heading Font Weight (semicolon-separated)', 'brainforward'),
            'section'     => 'brainforward_typography_section',
            'settings'    => 'heading_font_weight_setting',
            'type'        => 'text',
            'description' => __('Enter one or more font weights as needed, separated by semicolons (e.g., 600;700;800;900).', 'brainforward'),
        ));

        // Section for General Settings
        $wp_customize->add_section( 'brainforward_color_settings',
            array(
            'title'       => __( 'Color scheme', 'brainforward' ),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainforward_theme_options_panel',
            )
        );
        $wp_customize->add_setting( 'accent_color',
            array(
            'default'    => '#228F99',
            'type'       => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport'  => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
            ) 
        );      
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize,
            'brainforward_accent_color',
            array(
            'label'      => __( 'Accent Color', 'brainforward' ),
            'settings'   => 'accent_color',
            'section'    => 'brainforward_color_settings',
            ) 
        ) );
        $wp_customize->add_setting( 'primary_color',
            array(
            'default'    => '#0e1317',
            'type'       => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport'  => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
            ) 
        );    
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize,
            'brainforward_primary_color',
            array(
            'label'      => __( 'Primary Color', 'brainforward' ),
            'settings'   => 'primary_color',
            'section'    => 'brainforward_color_settings',
            ) 
        ) );
        $wp_customize->add_setting( 'gray_color',
            array(
            'default'    => '#f6f6f6',
            'type'       => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport'  => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
            ) 
        );    
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize,
            'brainforward_gray_color',
            array(
            'label'      => __( 'Gray Color', 'brainforward' ),
            'settings'   => 'gray_color',
            'section'    => 'brainforward_color_settings',
            ) 
        ) );
        $wp_customize->add_setting( 'text_color',
            array(
            'default'    => '#696969',
            'type'       => 'theme_mod',
            'capability' => 'edit_theme_options',
            'transport'  => 'refresh',
            'sanitize_callback' => 'sanitize_hex_color',
            ) 
        );      
        $wp_customize->add_control( new WP_Customize_Color_Control(
            $wp_customize,
            'brainforward_text_color',
            array(
            'label'      => __( 'Text Color', 'brainforward' ),
            'settings'   => 'text_color',
            'section'    => 'brainforward_color_settings',
            ) 
        ) );
        
        // Add a new section for Navbar Settings
        $wp_customize->add_section('brainforward_navbar_section',
        array(
            'title'       => __('Navbar Settings', 'brainforward'),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainforward_theme_options_panel',
        )
        );
        
        if ( class_exists( 'Elementor\Plugin' ) ) {
            // Add control for Elementor Template for Navbar
            $wp_customize->add_setting( 'navbar_elementor_template_setting', array(
                'default'           => 'default',
                'sanitize_callback' => 'sanitize_text_field',
            ));
        
            $wp_customize->add_control( 'navbar_elementor_template_control', array(
                'label'       => __( 'Elementor Template for Navbar', 'brainforward' ),
                'settings'    => 'navbar_elementor_template_setting',
                'section'     => 'brainforward_navbar_section', // Ensure you have a section defined for the navbar
                'type'        => 'select',
                'choices'     => Brainforward_Functions::get_post_title_array('elementor_library'), // Retrieves Elementor library templates
                'description' => __( 'Select an Elementor template for your Navbar.', 'brainforward' ),
            ));
        }
        

        // Add the custom heading control for Navbar Options
        $wp_customize->add_control(new WP_Customize_Heading_Control(
        $wp_customize,
        'brainforward_navbar_options_heading',
        array(
            'label'    => __('Navbar Options', 'brainforward'),
            'section'  => 'brainforward_navbar_section',
            'settings' => array(), // No setting needed for this control
            'type'     => 'heading',
        )
        ));

        // Transparent Menu Style Selector Setting
        $wp_customize->add_setting('transparent_menu_setting',
        array(
            'default'           => 'normal',
            'sanitize_callback' => 'sanitize_key', // Use sanitize_key for a select control
        )
        );

        $wp_customize->add_control('transparent_menu_control',
        array(
            'label'       => __('Transparent Menu Style', 'brainforward'),
            'settings'    => 'transparent_menu_setting',
            'section'     => 'brainforward_navbar_section',
            'type'        => 'select',
            'choices'     => array(
                'normal'  => __('Normal', 'brainforward'),
                'light'   => __('Light', 'brainforward'),
                'dark'    => __('Dark', 'brainforward'),
            ),
            'description' => __('Select the style for the transparent Navbar.', 'brainforward'),
        )
        );
        // Sticky Menu Setting
        $wp_customize->add_setting('sticky_menu_setting', array(
            'default'           => 'enabled',
            'sanitize_callback' => 'sanitize_key', // Sanitize the selected option
        ));

        // Sticky Menu Control
        $wp_customize->add_control('sticky_menu_control', array(
            'label'       => __('Sticky Menu', 'brainforward'),
            'settings'    => 'sticky_menu_setting',
            'section'     => 'brainforward_navbar_section',
            'type'        => 'select',
            'choices'     => array(
                'enabled'  => __('Enabled', 'brainforward'),
                'disabled' => __('Disabled', 'brainforward'),
            ),
            'description' => __('Enable or disable the sticky navbar on scroll.', 'brainforward'),
        ));

        // Add the custom heading control for Navbar Actions
        $wp_customize->add_control(new WP_Customize_Heading_Control(
            $wp_customize,
            'brainforward_navbar_actions_heading',
            array(
                'label'    => __('Navbar Actions', 'brainforward'),
                'section'  => 'brainforward_navbar_section',
                'settings' => array(), // No setting needed for this control
                'type'     => 'heading',
            )
        ));

        if (class_exists('WooCommerce')) {
            // Navbar Cart Icon Setting
            $wp_customize->add_setting('navbar_cart_setting',
                array(
                    'default'           => 'hide',
                    'sanitize_callback' => 'sanitize_key', // Use sanitize_key for a select control
                )
            );

            $wp_customize->add_control('navbar_cart_control',
                array(
                    'label'       => __('Cart Icon', 'brainforward'),
                    'settings'    => 'navbar_cart_setting',
                    'section'     => 'brainforward_navbar_section',
                    'type'        => 'select',
                    'choices'     => array(
                        'show' => __('Show', 'brainforward'),
                        'hide' => __('Hide', 'brainforward'),
                    ),
                    'description' => __('Show or hide the WooCommerce cart icon in the Navbar.', 'brainforward'),
                )
            );
        }

        // Navbar Search Icon Setting
        $wp_customize->add_setting('navbar_search_setting',
            array(
                'default'           => 'show',
                'sanitize_callback' => 'sanitize_key', // Use sanitize_key for a select control
            )
        );

        $wp_customize->add_control('navbar_search_control',
            array(
                'label'       => __('Search Icon', 'brainforward'),
                'settings'    => 'navbar_search_setting',
                'section'     => 'brainforward_navbar_section',
                'type'        => 'select',
                'choices'     => array(
                    'show' => __('Show', 'brainforward'),
                    'hide' => __('Hide', 'brainforward'),
                ),
                'description' => __('Show or hide the search icon in the Navbar.', 'brainforward'),
            )
        );

        // Navbar Sidebar Toggle Setting
        $wp_customize->add_setting('navbar_sidebar_setting',
            array(
                'default'           => 'hide',
                'sanitize_callback' => 'sanitize_key', // Use sanitize_key for a select control
            )
        );

        $wp_customize->add_control('navbar_sidebar_control',
            array(
                'label'       => __('Sidebar Toggle', 'brainforward'),
                'settings'    => 'navbar_sidebar_setting',
                'section'     => 'brainforward_navbar_section',
                'type'        => 'select',
                'choices'     => array(
                    'show' => __('Show', 'brainforward'),
                    'hide' => __('Hide', 'brainforward'),
                ),
                'description' => __('Show or hide the sidebar toggle icon in the Navbar.', 'brainforward'),
            )
        );

        // Navbar Button Setting
        $wp_customize->add_setting('navbar_button_setting',
            array(
                'default'           => 'hide',
                'sanitize_callback' => 'sanitize_key', // Use sanitize_key for a select control
            )
        );

        $wp_customize->add_control('navbar_button_control',
            array(
                'label'       => __('Navbar Button', 'brainforward'),
                'settings'    => 'navbar_button_setting',
                'section'     => 'brainforward_navbar_section',
                'type'        => 'select',
                'choices'     => array(
                    'show' => __('Show', 'brainforward'),
                    'hide' => __('Hide', 'brainforward'),
                ),
                'description' => __('Show or hide the button in the Navbar.', 'brainforward'),
            )
        );

        // Navbar Button Text Setting
        $wp_customize->add_setting('navbar_button_text_setting',
            array(
                'default'           => __('Get Started', 'brainforward'),
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control('navbar_button_text_control',
            array(
                'label'       => __('Button Text', 'brainforward'),
                'settings'    => 'navbar_button_text_setting',
                'section'     => 'brainforward_navbar_section',
                'type'        => 'text',
                'description' => __('Set the text for the Navbar button.', 'brainforward'),
            )
        );

        // Navbar Button Link Setting
        $wp_customize->add_setting('navbar_button_link_setting',
            array(
                'default'           => home_url(),
                'sanitize_callback' => 'esc_url_raw', // Sanitize the URL input
            )
        );

        $wp_customize->add_control('navbar_button_link_control',
            array(
                'label'       => __('Button Link', 'brainforward'),
                'settings'    => 'navbar_button_link_setting',
                'section'     => 'brainforward_navbar_section',
                'type'        => 'url',
                'description' => __('Set the link for the Navbar button.', 'brainforward'),
            )
        );



        // Add the custom heading control for Navbar Dimensions
        $wp_customize->add_control(new WP_Customize_Heading_Control(
        $wp_customize,
        'brainforward_navbar_dimensions_heading',
        array(
            'label'    => __('Navbar Dimensions', 'brainforward'),
            'section'  => 'brainforward_navbar_section',
            'settings' => array(), // No setting needed for this control
            'type'     => 'heading',
        )
        ));

        // Navbar Width Setting
        $wp_customize->add_setting('navbar_width_setting',
            array(
                'default'           => 1600, // Set default value as needed
                'sanitize_callback' => 'absint', // Use absint to ensure the value is a positive integer
            )
        );

        $wp_customize->add_control('navbar_width_control',
            array(
                'label'       => __('Navbar Width', 'brainforward'),
                'settings'    => 'navbar_width_setting',
                'section'     => 'brainforward_navbar_section',
                'type'        => 'number',
                'input_type'  => 'text', // Use 'text' to allow entering numeric values
                'description' => __('Set the width of the Navbar in pixels.', 'brainforward'),
            )
        );

        // Navbar Height Setting
        $wp_customize->add_setting( 'navbar_height_setting',
            array(
                'default'           => 100, // Set default value as needed
                'sanitize_callback' => 'absint', // Use absint to ensure the value is a positive integer
            )
        );

        $wp_customize->add_control( 'navbar_height_control',
            array(
                'label'       => __( 'Navbar Height', 'brainforward' ),
                'settings'    => 'navbar_height_setting',
                'section'     => 'brainforward_navbar_section',
                'type'        => 'number',
                'input_type'  => 'text', // Use 'text' to allow entering numeric values
                'description' => __( 'Set the height of the Navbar in pixels.', 'brainforward' ),
            )
        );


        // Add the custom heading control for Navbar Dimensions
        $wp_customize->add_control(new WP_Customize_Heading_Control(
            $wp_customize,
            'brainforward_top_bar_heading',
            array(
                'label'    => __('Topbar', 'brainforward'),
                'section'  => 'brainforward_navbar_section',
                'settings' => array(), // No setting needed for this control
                'type'     => 'heading',
            )
        ));
        // Navbar Style Selector Setting (Offer Toggle)
        $wp_customize->add_setting( 'navbar_offer_setting',
            array(
                'default'           => 'hide',
                'sanitize_callback' => 'sanitize_key',
            )
        );

        $wp_customize->add_control( 'navbar_offer_setting',
            array(
                'label'       => __( 'Offer', 'brainforward' ),
                'settings'    => 'navbar_offer_setting',
                'section'     => 'brainforward_navbar_section',
                'type'        => 'select',
                'choices'     => array(
                    'show' => __( 'Show', 'brainforward' ),
                    'hide' => __( 'Hide', 'brainforward' ),
                ),
                'description' => __( 'Select an option to hide or show the offer text.', 'brainforward' ),
            )
        );

        // Navbar Offer Text Setting
        $wp_customize->add_setting( 'navbar_offer_text',
            array(
                'default' => __('🧠 Special Offer: Get 50% off your first course - Enroll today!', 'brainforward'),
                'sanitize_callback' => 'wp_kses_post',
            )
        );


        $wp_customize->add_control( 'navbar_offer_text',
            array(
                'label'       => __( 'Offer Text', 'brainforward' ),
                'settings'    => 'navbar_offer_text',
                'section'     => 'brainforward_navbar_section',
                'type'        => 'text',
                'description' => __( 'Enter the text to display in the offer bar.', 'brainforward' ),
            )
        );



        // Add a new section for Navbar Settings
        $wp_customize->add_section( 'brainforward_header_section',
            array(
                'title'       => __( 'Header Settings', 'brainforward' ),
                'capability'  => 'edit_theme_options',
                'panel'       => 'brainforward_theme_options_panel',
            )
            );

        if ( class_exists( 'Elementor\Plugin' ) ) {
            // Add control for Elementor Template
            $wp_customize->add_setting( 'header_elementor_template_setting',
            array(
                'default'           => 'default',
                'sanitize_callback' => 'sanitize_text_field',
            )
            );

            $wp_customize->add_control( 'header_elementor_template_control',
            array(
                'label'      => __( 'Elementor Template for Header', 'brainforward' ),
                'settings'   => 'header_elementor_template_setting',
                'section'    => 'brainforward_header_section',
                'type'       => 'select',
                'choices'    => Brainforward_Functions::get_post_title_array('elementor_library'), // Call this function to get Elementor library templates
                'description' => __( 'Select an Elementor template for your Header.', 'brainforward' ),
            )
            );
        }


        // Add Background Image control
        $wp_customize->add_setting('header_bg_image', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'header_bg_image', array(
            'label'    => __('Header Background Image', 'brainforward'),
            'section'  => 'brainforward_header_section',
            'settings' => 'header_bg_image',
        )));

        // Navbar Background Color Setting
        $wp_customize->add_setting('header_bg_color', array(
            'default'           => '#0e1317', // Set default value as needed
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_bg_color', array(
            'label'      => __('Header Background Color', 'brainforward'),
            'section'    => 'brainforward_header_section',
            'settings'   => 'header_bg_color',
            'description' => __('Choose a color for the header background.', 'brainforward'),
        )));


        // Control for Loader Width
        $wp_customize->add_setting('header_color_opacity', array(
            'default'           => 90, // Set default width in percentage
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('header_color_opacity', array(
            'label'       => __('Background Opacity', 'brainforward'),
            'description' => __('Set the opacity of the Background Color in percentage.', 'brainforward'),
            'section'     => 'brainforward_header_section',
            'type'        => 'range',
            'input_attrs' => array(
                'min'  => 1,
                'max'  => 99,
                'step' => 1,
            ),
        ));

        // Header Text Color Setting
        $wp_customize->add_setting('header_text_color', array(
            'default'           => '#ffffff', // Set default value as needed
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_text_color', array(
            'label'      => __('Header Text Color', 'brainforward'),
            'section'    => 'brainforward_header_section',
            'settings'   => 'header_text_color',
            'description' => __('Choose a color for the header text.', 'brainforward'),
        )));

        $wp_customize->add_setting('header_text_align', array(
        'default'           => 'center',
        'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('header_text_align', array(
        'label'         => __('Text Align', 'brainforward'),
        'settings'      => 'header_text_align',
        'section'       => 'brainforward_header_section',
        'type'          => 'select',
        'choices'       => array(
            'left'   => __('Left', 'brainforward'),
            'center' => __('Center', 'brainforward'),
            'right'  => __('Right', 'brainforward')
        ),
        'description'   => __('Change the alignment of the header text and title.', 'brainforward'),
        ));

        // Navbar Style Selector Setting
        $wp_customize->add_setting( 'header_scroll_arrow',
           array(
               'default'           => 'hide',
               'sanitize_callback' => 'sanitize_key', // Use sanitize_key for a select control
           )
       );

       $wp_customize->add_control( 'header_scroll_arrow',
           array(
               'label'       => __( 'Scroll Down Arrow', 'brainforward' ),
               'settings'    => 'header_scroll_arrow',
               'section'     => 'brainforward_header_section',
               'type'        => 'select',
               'choices'     => array(
                   'show' => __( 'Show', 'brainforward' ),
                   'hide' => __( 'Hide', 'brainforward' ),
               ),
               'description' => __( 'Select an option to hide or show the scroll down arrow.', 'brainforward' ),
           )
       );



        // Blog Section
        $wp_customize->add_section('brainforward_blog_section', array(
            'title' => __('Blog Settings', 'brainforward'),
            'capability' => 'edit_theme_options',
            'panel'      => 'brainforward_theme_options_panel',
        ));

        // Read More Text Setting
        $wp_customize->add_setting('blog_list_page_title', array(
            'default'           => __('Blog List', 'brainforward'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('blog_list_page_title', array(
            'label'       => __('Blog Page Title', 'brainforward'),
            'settings'    => 'blog_list_page_title',
            'section'     => 'brainforward_blog_section',
            'type'        => 'text',
            'description' => __('Enter the text to display on the blog page title.', 'brainforward'),
        ));


        // Sidebar Hide/Show
        $wp_customize->add_setting('blog_sidebar_display', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key', // Use sanitize_key for a select control
        ));

        $wp_customize->add_control('blog_sidebar_display', array(
            'label'       => __('Display Sidebar', 'brainforward'),
            'section'     => 'brainforward_blog_section',
            'settings'    => 'blog_sidebar_display',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Select whether to show or hide the sidebar.', 'brainforward'),
        ));

        // Feature Image Display Setting
        $wp_customize->add_setting('blog_thumb_settings', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_thumb_settings', array(
            'label'       => __('Display Featured Image', 'brainforward'),
            'settings'    => 'blog_thumb_settings',
            'section'     => 'brainforward_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the featured image on blog posts.', 'brainforward'),
        ));

        // Feature Image Size Setting
        $wp_customize->add_setting('blog_thumb_size', array(
            'default'           => 'large',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_thumb_size', array(
            'label'       => __('Featured Image Size', 'brainforward'),
            'settings'    => 'blog_thumb_size',
            'section'     => 'brainforward_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'thumbnail' => __('Thumbnail', 'brainforward'),
                'medium'    => __('Medium', 'brainforward'),
                'large'     => __('Large', 'brainforward'),
                'full'      => __('Full Size', 'brainforward'),
            ),
            'description' => __('Select the size of the featured image displayed on blog posts.', 'brainforward'),
        ));


        // Author Display Setting
        $wp_customize->add_setting('blog_show_author', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_author', array(
            'label'       => __('Display Author', 'brainforward'),
            'settings'    => 'blog_show_author',
            'section'     => 'brainforward_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the author information on blog posts.', 'brainforward'),
        ));

        // Date Display Setting
        $wp_customize->add_setting('blog_show_date', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_date', array(
            'label'       => __('Display Date', 'brainforward'),
            'settings'    => 'blog_show_date',
            'section'     => 'brainforward_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the post date on blog posts.', 'brainforward'),
        ));

        // Comment Count Display Setting
        $wp_customize->add_setting('blog_show_comment_count', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_comment_count', array(
            'label'       => __('Display Comment Count', 'brainforward'),
            'settings'    => 'blog_show_comment_count',
            'section'     => 'brainforward_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the comment count on blog posts.', 'brainforward'),
        ));

        // Tags Display Setting
        $wp_customize->add_setting('blog_show_tags', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_tags', array(
            'label'       => __('Display Tags', 'brainforward'),
            'settings'    => 'blog_show_tags',
            'section'     => 'brainforward_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the tags on blog posts.', 'brainforward'),
        ));

        // Category Display Setting
        $wp_customize->add_setting('blog_show_category', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_category', array(
            'label'       => __('Display Categories', 'brainforward'),
            'settings'    => 'blog_show_category',
            'section'     => 'brainforward_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the categories on blog posts.', 'brainforward'),
        ));


        // Title Display Setting
        $wp_customize->add_setting('blog_show_title', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_title', array(
            'label'       => __('Display Title', 'brainforward'),
            'settings'    => 'blog_show_title',
            'section'     => 'brainforward_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the post title on blog posts.', 'brainforward'),
        ));

        // Excerpt Display Setting
        $wp_customize->add_setting('blog_show_excerpt', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_excerpt', array(
            'label'       => __('Display Excerpt', 'brainforward'),
            'settings'    => 'blog_show_excerpt',
            'section'     => 'brainforward_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the excerpt on blog posts.', 'brainforward'),
        ));

        // Excerpt Length Setting
        $wp_customize->add_setting('blog_excerpt_length', array(
            'default'           => 30,
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('blog_excerpt_length', array(
            'label'       => __('Excerpt Length', 'brainforward'),
            'settings'    => 'blog_excerpt_length',
            'section'     => 'brainforward_blog_section',
            'type'        => 'number',
            'description' => __('Set the number of words for the blog post excerpt.', 'brainforward'),
        ));

        // Read More Button Display Setting
        $wp_customize->add_setting('blog_read_more', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_read_more', array(
            'label'       => __('Display Read More Button', 'brainforward'),
            'settings'    => 'blog_read_more',
            'section'     => 'brainforward_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the Read More button on blog posts.', 'brainforward'),
        ));

        // Read More Text Setting
        $wp_customize->add_setting('blog_read_more_text', array(
            'default'           => __('Read More', 'brainforward'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('blog_read_more_text', array(
            'label'       => __('Read More Button Text', 'brainforward'),
            'settings'    => 'blog_read_more_text',
            'section'     => 'brainforward_blog_section',
            'type'        => 'text',
            'description' => __('Enter the text to display on the Read More button.', 'brainforward'),
        ));

    





        // Blog Section
        $wp_customize->add_section('brainforward_blog_details_section', array(
            'title' => __('Blog Details Settings', 'brainforward'),
            'capability' => 'edit_theme_options',
            'panel'      => 'brainforward_theme_options_panel',
        ));


        // Sidebar Hide/Show
        $wp_customize->add_setting('blog_details_sidebar_display', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key', // Use sanitize_key for a select control
        ));

        $wp_customize->add_control('blog_details_sidebar_display', array(
            'label'       => __('Display Sidebar', 'brainforward'),
            'section'     => 'brainforward_blog_details_section',
            'settings'    => 'blog_details_sidebar_display',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Select whether to show or hide the sidebar.', 'brainforward'),
        ));

        // Author Display Setting
        $wp_customize->add_setting('blog_details_show_author', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_author', array(
            'label'       => __('Display Author', 'brainforward'),
            'settings'    => 'blog_details_show_author',
            'section'     => 'brainforward_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the author information on blog posts.', 'brainforward'),
        ));

        // Date Display Setting
        $wp_customize->add_setting('blog_details_show_date', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_date', array(
            'label'       => __('Display Date', 'brainforward'),
            'settings'    => 'blog_details_show_date',
            'section'     => 'brainforward_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the post date on blog posts.', 'brainforward'),
        ));

        // Comment Count Display Setting
        $wp_customize->add_setting('blog_details_show_comment_count', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_comment_count', array(
            'label'       => __('Display Comment Count', 'brainforward'),
            'settings'    => 'blog_details_show_comment_count',
            'section'     => 'brainforward_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the comment count on blog posts.', 'brainforward'),
        ));

        // Category Display Setting
        $wp_customize->add_setting('blog_details_show_category', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_category', array(
            'label'       => __('Display Categories', 'brainforward'),
            'settings'    => 'blog_details_show_category',
            'section'     => 'brainforward_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the categories on blog posts.', 'brainforward'),
        ));

        // Tags Display Setting
        $wp_customize->add_setting('blog_details_show_tags', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_tags', array(
            'label'       => __('Display Tags', 'brainforward'),
            'settings'    => 'blog_details_show_tags',
            'section'     => 'brainforward_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the tags on blog posts.', 'brainforward'),
        ));
        // Social Share Display Setting
        $wp_customize->add_setting('blog_show_social_share', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_social_share', array(
            'label'       => __('Display Social Share Buttons', 'brainforward'),
            'settings'    => 'blog_show_social_share',
            'section'     => 'brainforward_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide social share buttons on blog details.', 'brainforward'),
        ));

        // Next and Previous Post Navigation Setting
        $wp_customize->add_setting('blog_details_show_post_navigation', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_post_navigation', array(
            'label'       => __('Display Post Navigation', 'brainforward'),
            'settings'    => 'blog_details_show_post_navigation',
            'section'     => 'brainforward_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the navigation to the next and previous posts on blog details.', 'brainforward'),
        ));



        $wp_customize->add_section('brainforward_woocommerce_section', array(
            'title'       => __('WooCommerce Settings', 'brainforward'),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainforward_theme_options_panel',
        ));

       // Next and Previous Post Navigation Setting
        $wp_customize->add_setting('woocommerce_page_header', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('woocommerce_page_header', array(
            'label'       => __('Display Page Header', 'brainforward'),
            'settings'    => 'woocommerce_page_header',
            'section'     => 'brainforward_woocommerce_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the page header that displays the page title and subtitle on WooCommerce pages.', 'brainforward'),
        ));

        // Display Sale Badge
        $wp_customize->add_setting('woocommerce_sale_badge', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('woocommerce_sale_badge', array(
            'label'       => __('Display Sale Badge', 'brainforward'),
            'section'     => 'brainforward_woocommerce_section',
            'settings'    => 'woocommerce_sale_badge',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the sale badge on products.', 'brainforward'),
        ));


        // Wishlist Button Show/Hide
        $wp_customize->add_setting('woocommerce_wishlist_button', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('woocommerce_wishlist_button', array(
            'label'       => __('Display Wishlist Button', 'brainforward'),
            'section'     => 'brainforward_woocommerce_section',
            'settings'    => 'woocommerce_wishlist_button',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
            'description' => __('Choose whether to show or hide the wishlist button on product pages.', 'brainforward'),
        ));



        

        $wp_customize->add_section('brainforward_footer_section', array(
            'title'       => __('Footer Settings', 'brainforward'),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainforward_theme_options_panel',
        ));    

        if ( class_exists( 'Elementor\Plugin' ) ) {
            // Add control for Elementor Template
            $wp_customize->add_setting('footer_elementor_template_setting', array(
            'default'           => 'default',
            'sanitize_callback' => 'sanitize_text_field',
            ));
            $wp_customize->add_control('footer_elementor_template_control', array(
            'label'         => __('Elementor Template for Footer', 'brainforward'),
            'settings'      => 'footer_elementor_template_setting',
            'section'       => 'brainforward_footer_section',
            'type'          => 'select',
            'choices'       => Brainforward_Functions::get_post_title_array('elementor_library'), // Ensure this function is defined to get Elementor library templates
            'description'   => __('Select an Elementor template for your footer.', 'brainforward'),
            ));
        }

         // Add a text control for copyrights text in the Footer section

        // Control for Copyrights Text
        $wp_customize->add_setting('copyright_text_setting', array(
            'default'           => __('©2025 All rights reserved. Powered by Brainforward', 'brainforward'),
            'sanitize_callback' => 'sanitize_textarea_field',
        ));

        $wp_customize->add_control('copyright_text_setting', array(
            'label'    => __('Copyrights Text', 'brainforward'),
            'section'  => 'brainforward_footer_section',
            'settings'   => 'copyright_text_setting',
            'type'     => 'textarea',
        ));
        
        // Scroll Up Button Selector Setting
        $wp_customize->add_setting( 'scroll_up_settings',
            array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key', // Use sanitize_key for a select control
            )
        );

        $wp_customize->add_control( 'scroll_up_settings',
            array(
            'label'      => __( 'Scroll Up Button', 'brainforward' ),
            'settings'   => 'scroll_up_settings',
            'section'    => 'brainforward_footer_section',
            'type'       => 'select',
            'choices'    => array(
                    'show' => __( 'Show', 'brainforward' ),
                    'hide'  => __( 'Hide', 'brainforward' ),
            ),
            'description' => __( 'Select a option to hide or show scroll to up button.', 'brainforward' ),
            )
        );

        // Add controls for 404 page settings
        $wp_customize->add_section('brainforward_404_section', array(
            'title'      => __('404 Page', 'brainforward'),
            'capability' => 'edit_theme_options',
            'panel'      => 'brainforward_theme_options_panel',
        ));

        if ( class_exists( 'Elementor\Plugin' ) ) {
            // Add control for Elementor Template
            $wp_customize->add_setting( '404_elementor_template_setting',
            array(
                'default'           => 'default',
                'sanitize_callback' => 'sanitize_text_field',
            )
            );

            $wp_customize->add_control( '404_elementor_template_control',
            array(
                'label'      => __( 'Elementor Template for 404', 'brainforward' ),
                'settings'   => '404_elementor_template_setting',
                'section'    => 'brainforward_404_section',
                'type'       => 'select',
                'choices'    => Brainforward_Functions::get_post_title_array('elementor_library'), // Call this function to get Elementor library templates
                'description' => __( 'Select an Elementor template for your 404 page.', 'brainforward' ),
            )
            );
        }

        // Control for 404 Image
        $wp_customize->add_setting('brainforward_404_image', array(
            'default'           => get_theme_file_uri('assets/images/404.png'),
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'brainforward_404_image', array(
            'label'    => __('404 Image', 'brainforward'),
            'section'  => 'brainforward_404_section',
            'settings' => 'brainforward_404_image',
        )));

        // Control for Page Title
        $wp_customize->add_setting('brainforward_404_title', array(
            'default'           => __('Oops... Page Not Found!', 'brainforward'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('brainforward_404_title', array(
            'label'    => __('Page Title', 'brainforward'),
            'section'  => 'brainforward_404_section',
            'type'     => 'text',
        ));

        // Control for Page Description
        $wp_customize->add_setting('brainforward_404_description', array(
            'default'           => __('Please return to the site\'s homepage. It looks like nothing was found at this location. Get in touch to discuss your employee needs today. Please give us a call, drop us an email.', 'brainforward'),
            'sanitize_callback' => 'sanitize_textarea_field',
        ));

        $wp_customize->add_control('brainforward_404_description', array(
            'label'    => __('Page Description', 'brainforward'),
            'section'  => 'brainforward_404_section',
            'type'     => 'textarea',
        ));

        // Control for Page Title
        $wp_customize->add_setting('brainforward_404_button_text', array(
            'default'           => __('Back to Home', 'brainforward'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('brainforward_404_button_text', array(
            'label'    => __('Button Text', 'brainforward'),
            'section'  => 'brainforward_404_section',
            'type'     => 'text',
        ));

        // Add controls for loader settings
        $wp_customize->add_section('brainforward_loader_section', array(
            'title'      => __('Page Loader', 'brainforward'),
            'capability' => 'edit_theme_options',
            'panel'      => 'brainforward_theme_options_panel',
        ));
        
        // Control for Loader Visibility (Enable/Disable)
        $wp_customize->add_setting('brainforward_loader_visibility', array( // Updated variable name to '_visibility'
            'default'           => 'show', // Set default to "show"
            'sanitize_callback' => 'sanitize_key',
        ));
        
        $wp_customize->add_control('brainforward_loader_visibility', array( // Updated variable name to '_visibility'
            'label'    => __('Loader Visibility', 'brainforward'),
            'section'  => 'brainforward_loader_section',
            'type'     => 'select',
            'choices'  => array(
                'show' => __('Show', 'brainforward'),
                'hide' => __('Hide', 'brainforward'),
            ),
        ));


        // Control for Loader Type (Enable/Disable)
        $wp_customize->add_setting('brainforward_loader_type', array( // Updated variable name to 'type'
            'default'           => 'loader', // Set default to "show"
            'sanitize_callback' => 'sanitize_key',
        ));
        
        $wp_customize->add_control('brainforward_loader_type', array( // Updated variable name to '_type'
            'label'    => __('Loader Type', 'brainforward'),
            'section'  => 'brainforward_loader_section',
            'type'     => 'select',
            'choices'  => array(
                'text' => __('Text', 'brainforward'),
                'loader' => __('Loader', 'brainforward'),
                'template' => __('Template', 'brainforward'),
            ),
        ));
        
        
        // Loader Text Setting
        $wp_customize->add_setting( 'loader_text_setting',
            array(
                'default'           => __('Loading', 'brainforward'),
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control( 'loader_text_control',
            array(
                'label'       => __( 'Loader Text', 'brainforward' ),
                'section'     => 'brainforward_loader_section',
                'settings'    => 'loader_text_setting',
                'type'        => 'text',
                'description' => __( 'Enter the text to display in the loader (max 10 characters).', 'brainforward' ),
            )
        );


        if ( class_exists( 'Elementor\Plugin' ) ) {
            // Add control for Elementor Template for Navbar (Preloader)
            $wp_customize->add_setting( 'loader_template_settings', array(
                'default'           => 'default',
                'sanitize_callback' => 'sanitize_text_field',
            ));
        
            $wp_customize->add_control( 'loader_template_control', array(
                'label'       => __( 'Elementor Template for Preloader', 'brainforward' ),
                'settings'    => 'loader_template_settings',
                'section'     => 'brainforward_loader_section', // Ensure you have a section defined for the navbar
                'type'        => 'select',
                'choices'     => Brainforward_Functions::get_post_title_array('elementor_library'), // Retrieves Elementor library templates
                'description' => __( 'Select an Elementor template to be used as the preloader. This will control the visual appearance of the preloader before the page fully loads.', 'brainforward' ),
            ));
        }
        

        // Adding the "Additional Scripts" section under the Additional CSS section
        $wp_customize->add_section('brainforward_additional_scripts_section', array(
            'title'       => __('Additional Scripts', 'brainforward'),
            'capability'  => 'edit_theme_options',
            'description' => __('Add custom scripts to be included in the theme\'s script file.', 'brainforward'),
            'section'     => 'custom_css', // Parent section set to Additional CSS
            'priority'    => 999,
        ));

        // API & Integration Settings Section
        $wp_customize->add_section('brainforward_api_section', array(
            'title'       => __('API & Integrations', 'brainforward'),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainforward_theme_options_panel',
            'description' => __('Configure API keys and third-party integrations.', 'brainforward'),
        ));

        // SMS API Token Setting
        $wp_customize->add_setting('sms_api_token', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('sms_api_token', array(
            'label'       => __('SMS API Token', 'brainforward'),
            'section'     => 'brainforward_api_section',
            'type'        => 'text',
            'description' => __('Enter your SSL Wireless SMS API token.', 'brainforward'),
        ));

        // SMS Sender ID Setting
        $wp_customize->add_setting('sms_sender_id', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('sms_sender_id', array(
            'label'       => __('SMS Sender ID', 'brainforward'),
            'section'     => 'brainforward_api_section',
            'type'        => 'text',
            'description' => __('Enter your SMS sender ID (SID).', 'brainforward'),
        ));

        // Google Tag Manager ID Setting
        $wp_customize->add_setting('gtm_container_id', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('gtm_container_id', array(
            'label'       => __('Google Tag Manager Container ID', 'brainforward'),
            'section'     => 'brainforward_api_section',
            'type'        => 'text',
            'description' => __('Enter your GTM Container ID (e.g., GTM-XXXXXXX).', 'brainforward'),
        ));

        // Google Analytics ID Setting
        $wp_customize->add_setting('ga_measurement_id', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('ga_measurement_id', array(
            'label'       => __('Google Analytics Measurement ID', 'brainforward'),
            'section'     => 'brainforward_api_section',
            'type'        => 'text',
            'description' => __('Enter your GA4 Measurement ID (e.g., G-XXXXXXXXXX).', 'brainforward'),
        ));

        // Google Site Verification Setting
        $wp_customize->add_setting('google_site_verification', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('google_site_verification', array(
            'label'       => __('Google Site Verification Code', 'brainforward'),
            'section'     => 'brainforward_api_section',
            'type'        => 'text',
            'description' => __('Enter your Google site verification code.', 'brainforward'),
        ));

    }

    public function customize_preview_js() {
        wp_enqueue_script(
            'brainforward_customizer',
            BRAINFORWARD_JS_URI . '/customizer.js',
            ['customize-preview'],
            BRAINFORWARD_THEME_VERSION,
            true
        );
    }

    public function header_output(){
        $body_fonts = get_theme_mod('body_font_family_setting', 'Roboto');
        $heading_fonts = get_theme_mod('heading_font_family_setting', 'Bebas Neue');
        ?>
        <!--Customizer CSS--> 
        <style type="text/css">
           :root {
            <?php 
                self::generate_custom_property('--navbar-height', 'navbar_height_setting', 'px'); 
                self::generate_color_property('--primary-color', 'primary_color'); 
                self::generate_color_property('--gray-color', 'gray_color'); 
                self::generate_color_property('--text-color', 'text_color'); 
                self::generate_color_property('--accent-color', 'accent_color'); 
                if (isset($body_fonts) && $body_fonts != 'default') { 
                    self::generate_custom_property('--body-font-family', 'body_font_family_setting'); 
                } 
                if (isset($heading_fonts) && $heading_fonts != 'default') { 
                    self::generate_custom_property('--heading-font-family', 'heading_font_family_setting'); 
                } 
            ?>
           }
            <?php 
                self::generate_css('body', 'font-size', 'body_font_size_setting');
                self::generate_css('.site-header', 'background-image', 'header_bg_image', 'url("', '")' ); 
                self::generate_css('.site-header:before, .single_post-thumbnail::after', 'background', 'header_bg_color' ); 
                self::generate_css('.site-header:before, .single_post-thumbnail::after', 'opacity', 'header_color_opacity', '.', '' ); 
                self::generate_css('.site-header .site-header__title,.site-header .site-header__description', 'color', 'header_text_color' ); 
                self::generate_css('.site-header', 'text-align', 'header_text_align' );
                self::generate_css('.main__navbar .container.wide', 'max-width', 'navbar_width_setting', '', 'px' ); 
                self::generate_css('.main__navbar .custom-logo-link img', 'width', 'navbar_logo_width_setting', '', 'px' ); 
            ?>

        </style> 
        <!--/Customizer CSS-->
        <?php
      }


    // Public method for sanitizing font weight input
    public function sanitize_font_weight( $value ) {
        // Define valid font weights
        $valid_font_weights = array( '100', '200', '300', '400', '500', '600', '700', '800', '900' );
        
        // Split the input by semicolons into an array
        $weights = array_map('trim', explode( ';', $value ));

        // Filter out invalid font weights
        $weights = array_filter( $weights, function( $item ) use ( $valid_font_weights ) {
            return in_array( $item, $valid_font_weights );
        });

        // Return a semicolon-separated list of valid font weights
        return implode( ';', $weights );
    }
    
    public static function generate_css( $selector, $style, $mod_name, $prefix = '', $postfix = '', $echo = true ) {
        $return = '';
        $mod = get_theme_mod( $mod_name );
        if ( isset( $mod ) && !empty( $mod ) && '' !== $mod ) {
            $return = sprintf( '%s { %s:%s; }',
                $selector,
                $style,
                $prefix . $mod . $postfix
            );
            if ( $echo ) {
                echo wp_kses_post($return);
            }
        }
        return $return;
    } 
    
    public static function generate_custom_property($property, $mod_name, $postfix='', $echo=true) {
        $return = '';
        $mod    = get_theme_mod($mod_name);
        if (!empty($mod)) {
            $return = sprintf('%s:%s%s;',
                $property,
                $mod,
                $postfix
            );
            if ($echo) {
                echo wp_kses_post($return);
            }
        }
        return $return;
    }

    public static function hex_to_rgb($hex) {
        $hex = ltrim($hex, '#');
    
        if (strlen($hex) === 6) {
            list($r, $g, $b) = sscanf($hex, '%02x%02x%02x');
        } elseif (strlen($hex) === 3) {
            list($r, $g, $b) = sscanf($hex, '%1x%1x%1x');
            $r = $r * 17;
            $g = $g * 17;
            $b = $b * 17;
        } else {
            return null; // Invalid hex code
        }
    
        return [$r, $g, $b];
    }
    
    public static function generate_color_property($property, $mod_name, $postfix = '', $echo = true) {
        $return = '';
        $mod    = get_theme_mod($mod_name);
        if (!empty($mod)) {
            // Directly output as hex value
            $return = sprintf('%s: %s%s;', 
                $property,
                $mod,
                $postfix
            );
            if ($echo) {
                echo wp_kses_post($return);
            }
        }
        return $return;
    }


    public function enqueue_google_fonts() {
        // Get the selected fonts from the Customizer
        $body_fonts      = get_theme_mod('body_font_family_setting', 'Roboto');
        $body_weight     = trim(get_theme_mod('body_font_weight_setting', ''));
        $heading_fonts   = get_theme_mod('heading_font_family_setting', 'Bebas Neue');
        $heading_weight  = trim(get_theme_mod('heading_font_weight_setting', ''));

        // Build weight part only if not empty
        $body_fonts_w    = $body_weight ? ':wght@' . $body_weight : '';
        $heading_fonts_w = $heading_weight ? ':wght@' . $heading_weight : '';

        // Enqueue body font
        if (!empty($body_fonts) && $body_fonts !== 'default') {
            wp_enqueue_style(
                'google-body-fonts',
                'https://fonts.googleapis.com/css2?family=' . urlencode($body_fonts) . $body_fonts_w . '&display=swap',
                [],
                null
            );
        }

        // Enqueue heading font
        if (!empty($heading_fonts) && $heading_fonts !== 'default') {
            wp_enqueue_style(
                'google-heading-fonts',
                'https://fonts.googleapis.com/css2?family=' . urlencode($heading_fonts) . $heading_fonts_w . '&display=swap',
                [],
                null
            );
        }
    }

    
}

new Brainforward_Customizer();