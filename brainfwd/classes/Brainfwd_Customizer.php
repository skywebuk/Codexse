<?php

class Brainfwd_Customizer {

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
        $wp_customize->add_setting('brainfwd_light_logo', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control(new WP_Customize_Image_Control(
            $wp_customize,
            'brainfwd_light_logo',
            array(
                'label'    => __('Light Logo', 'brainfwd'),
                'section'  => 'title_tagline', // You can change this to your custom section if needed
                'settings' => 'brainfwd_light_logo',
                'priority' => 9,
            )
        ));

        // Navbar Logo Width Setting
        $wp_customize->add_setting('navbar_logo_width_setting', array(
            'default'           => 160,
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('navbar_logo_width_control', array(
            'label'       => __('Logo Width', 'brainfwd'),
            'section'     => 'title_tagline', // Make sure this section exists
            'settings'    => 'navbar_logo_width_setting',
            'type'        => 'number', // No need for input_type here
            'description' => __('Set the width of the Navbar logo in pixels.', 'brainfwd'),
        ));


        // Add a new panel for theme options
        $wp_customize->add_panel( 'brainfwd_theme_options_panel',
            array(
            'title'       => __( 'Theme Options', 'brainfwd' ),
            'capability'  => 'edit_theme_options',
            'priority'    => 0,
            )
        );

        // Section for Typography Settings
        $wp_customize->add_section( 'brainfwd_typography_section',
            array(
            'title'       => __( 'Typography', 'brainfwd' ),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainfwd_theme_options_panel',
            )
        );

        // Add a setting for Body Font Family
        $wp_customize->add_setting('body_font_family_setting', array(
            'default'           => 'Roboto', // Default font
            'sanitize_callback' => 'sanitize_text_field',
        ));

        // Add a control for Body Font Family
        $wp_customize->add_control('body_font_family_control', array(
            'label'   => __('Body Font Family', 'brainfwd'),
            'section' => 'brainfwd_typography_section',
            'settings'    => 'body_font_family_setting',
            'type'    => 'text',
            'choices' => Brainfwd_Functions::get_popular_google_fonts(),
            'description' => __('Enter the font family name here. For example: "Nata Sans"', 'brainfwd'),
        ));

        // Add a setting for Body Font Weight
        $wp_customize->add_setting('body_font_weight_setting', array(
            'default'           => '',  // Default semicolon-separated font weights
            'sanitize_callback' => array($this, 'sanitize_font_weight'),  // Sanitize using the class method
        ));

        // Add a control for Body Font Weight
        $wp_customize->add_control('body_font_weight_control', array(
            'label'       => __('Body Font Weight (semicolon-separated)', 'brainfwd'),
            'section'     => 'brainfwd_typography_section',
            'settings'    => 'body_font_weight_setting',
            'type'        => 'text',
            'description' => __('Enter one or more font weights as needed, separated by semicolons (e.g., 300;400;500;600).', 'brainfwd'),
        ));

        // Add a setting for Body Font Size
        $wp_customize->add_setting('body_font_size_setting', array(
            'default'           => '16px',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        // Add a control for Body Font Size
        $wp_customize->add_control('body_font_size_control', array(
            'label'       => __('Body Font Size (px)', 'brainfwd'),
            'section'     => 'brainfwd_typography_section',
            'settings'    => 'body_font_size_setting',
            'type'        => 'text',
            'description' => __('Enter a value in px units, e.g., 16px', 'brainfwd'),
        ));

        // Add a setting for Heading Font Family
        $wp_customize->add_setting('heading_font_family_setting', array(
            'default'           => 'Bebas Neue', // Default heading font
            'sanitize_callback' => 'sanitize_text_field',
        ));

        // Add a control for Heading Font Family
        $wp_customize->add_control('heading_font_family_control', array(
            'label'   => __('Heading Font Family', 'brainfwd'),
            'section' => 'brainfwd_typography_section',
            'settings'    => 'heading_font_family_setting',
            'type'    => 'text',
            'choices' => Brainfwd_Functions::get_popular_google_fonts(),
            'description' => __('Enter the font family name here. For example: "Nata Sans"', 'brainfwd'),
        ));

        // Add a setting for Heading Font Weight
        $wp_customize->add_setting('heading_font_weight_setting', array(
            'default'           => '',  // Default semicolon-separated font weights
            'sanitize_callback' => array($this, 'sanitize_font_weight'),  // Sanitize using the class method
        ));

        // Add a control for Heading Font Weight
        $wp_customize->add_control('heading_font_weight_control', array(
            'label'       => __('Heading Font Weight (semicolon-separated)', 'brainfwd'),
            'section'     => 'brainfwd_typography_section',
            'settings'    => 'heading_font_weight_setting',
            'type'        => 'text',
            'description' => __('Enter one or more font weights as needed, separated by semicolons (e.g., 600;700;800;900).', 'brainfwd'),
        ));

        // Section for General Settings
        $wp_customize->add_section( 'brainfwd_color_settings',
            array(
            'title'       => __( 'Color scheme', 'brainfwd' ),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainfwd_theme_options_panel',
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
            'brainfwd_accent_color',
            array(
            'label'      => __( 'Accent Color', 'brainfwd' ),
            'settings'   => 'accent_color',
            'section'    => 'brainfwd_color_settings',
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
            'brainfwd_primary_color',
            array(
            'label'      => __( 'Primary Color', 'brainfwd' ),
            'settings'   => 'primary_color',
            'section'    => 'brainfwd_color_settings',
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
            'brainfwd_gray_color',
            array(
            'label'      => __( 'Gray Color', 'brainfwd' ),
            'settings'   => 'gray_color',
            'section'    => 'brainfwd_color_settings',
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
            'brainfwd_text_color',
            array(
            'label'      => __( 'Text Color', 'brainfwd' ),
            'settings'   => 'text_color',
            'section'    => 'brainfwd_color_settings',
            ) 
        ) );
        
        // Add a new section for Navbar Settings
        $wp_customize->add_section('brainfwd_navbar_section',
        array(
            'title'       => __('Navbar Settings', 'brainfwd'),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainfwd_theme_options_panel',
        )
        );
        
        if ( class_exists( 'Elementor\Plugin' ) ) {
            // Add control for Elementor Template for Navbar
            $wp_customize->add_setting( 'navbar_elementor_template_setting', array(
                'default'           => 'default',
                'sanitize_callback' => 'sanitize_text_field',
            ));
        
            $wp_customize->add_control( 'navbar_elementor_template_control', array(
                'label'       => __( 'Elementor Template for Navbar', 'brainfwd' ),
                'settings'    => 'navbar_elementor_template_setting',
                'section'     => 'brainfwd_navbar_section', // Ensure you have a section defined for the navbar
                'type'        => 'select',
                'choices'     => Brainfwd_Functions::get_post_title_array('elementor_library'), // Retrieves Elementor library templates
                'description' => __( 'Select an Elementor template for your Navbar.', 'brainfwd' ),
            ));
        }
        

        // Add the custom heading control for Navbar Options
        $wp_customize->add_control(new WP_Customize_Heading_Control(
        $wp_customize,
        'brainfwd_navbar_options_heading',
        array(
            'label'    => __('Navbar Options', 'brainfwd'),
            'section'  => 'brainfwd_navbar_section',
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
            'label'       => __('Transparent Menu Style', 'brainfwd'),
            'settings'    => 'transparent_menu_setting',
            'section'     => 'brainfwd_navbar_section',
            'type'        => 'select',
            'choices'     => array(
                'normal'  => __('Normal', 'brainfwd'),
                'light'   => __('Light', 'brainfwd'),
                'dark'    => __('Dark', 'brainfwd'),
            ),
            'description' => __('Select the style for the transparent Navbar.', 'brainfwd'),
        )
        );
        // Sticky Menu Setting
        $wp_customize->add_setting('sticky_menu_setting', array(
            'default'           => 'enabled',
            'sanitize_callback' => 'sanitize_key', // Sanitize the selected option
        ));

        // Sticky Menu Control
        $wp_customize->add_control('sticky_menu_control', array(
            'label'       => __('Sticky Menu', 'brainfwd'),
            'settings'    => 'sticky_menu_setting',
            'section'     => 'brainfwd_navbar_section',
            'type'        => 'select',
            'choices'     => array(
                'enabled'  => __('Enabled', 'brainfwd'),
                'disabled' => __('Disabled', 'brainfwd'),
            ),
            'description' => __('Enable or disable the sticky navbar on scroll.', 'brainfwd'),
        ));

        // Add the custom heading control for Navbar Actions
        $wp_customize->add_control(new WP_Customize_Heading_Control(
            $wp_customize,
            'brainfwd_navbar_actions_heading',
            array(
                'label'    => __('Navbar Actions', 'brainfwd'),
                'section'  => 'brainfwd_navbar_section',
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
                    'label'       => __('Cart Icon', 'brainfwd'),
                    'settings'    => 'navbar_cart_setting',
                    'section'     => 'brainfwd_navbar_section',
                    'type'        => 'select',
                    'choices'     => array(
                        'show' => __('Show', 'brainfwd'),
                        'hide' => __('Hide', 'brainfwd'),
                    ),
                    'description' => __('Show or hide the WooCommerce cart icon in the Navbar.', 'brainfwd'),
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
                'label'       => __('Search Icon', 'brainfwd'),
                'settings'    => 'navbar_search_setting',
                'section'     => 'brainfwd_navbar_section',
                'type'        => 'select',
                'choices'     => array(
                    'show' => __('Show', 'brainfwd'),
                    'hide' => __('Hide', 'brainfwd'),
                ),
                'description' => __('Show or hide the search icon in the Navbar.', 'brainfwd'),
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
                'label'       => __('Sidebar Toggle', 'brainfwd'),
                'settings'    => 'navbar_sidebar_setting',
                'section'     => 'brainfwd_navbar_section',
                'type'        => 'select',
                'choices'     => array(
                    'show' => __('Show', 'brainfwd'),
                    'hide' => __('Hide', 'brainfwd'),
                ),
                'description' => __('Show or hide the sidebar toggle icon in the Navbar.', 'brainfwd'),
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
                'label'       => __('Navbar Button', 'brainfwd'),
                'settings'    => 'navbar_button_setting',
                'section'     => 'brainfwd_navbar_section',
                'type'        => 'select',
                'choices'     => array(
                    'show' => __('Show', 'brainfwd'),
                    'hide' => __('Hide', 'brainfwd'),
                ),
                'description' => __('Show or hide the button in the Navbar.', 'brainfwd'),
            )
        );

        // Navbar Button Text Setting
        $wp_customize->add_setting('navbar_button_text_setting',
            array(
                'default'           => __('Get Started', 'brainfwd'),
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control('navbar_button_text_control',
            array(
                'label'       => __('Button Text', 'brainfwd'),
                'settings'    => 'navbar_button_text_setting',
                'section'     => 'brainfwd_navbar_section',
                'type'        => 'text',
                'description' => __('Set the text for the Navbar button.', 'brainfwd'),
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
                'label'       => __('Button Link', 'brainfwd'),
                'settings'    => 'navbar_button_link_setting',
                'section'     => 'brainfwd_navbar_section',
                'type'        => 'url',
                'description' => __('Set the link for the Navbar button.', 'brainfwd'),
            )
        );



        // Add the custom heading control for Navbar Dimensions
        $wp_customize->add_control(new WP_Customize_Heading_Control(
        $wp_customize,
        'brainfwd_navbar_dimensions_heading',
        array(
            'label'    => __('Navbar Dimensions', 'brainfwd'),
            'section'  => 'brainfwd_navbar_section',
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
                'label'       => __('Navbar Width', 'brainfwd'),
                'settings'    => 'navbar_width_setting',
                'section'     => 'brainfwd_navbar_section',
                'type'        => 'number',
                'input_type'  => 'text', // Use 'text' to allow entering numeric values
                'description' => __('Set the width of the Navbar in pixels.', 'brainfwd'),
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
                'label'       => __( 'Navbar Height', 'brainfwd' ),
                'settings'    => 'navbar_height_setting',
                'section'     => 'brainfwd_navbar_section',
                'type'        => 'number',
                'input_type'  => 'text', // Use 'text' to allow entering numeric values
                'description' => __( 'Set the height of the Navbar in pixels.', 'brainfwd' ),
            )
        );


        // Add the custom heading control for Navbar Dimensions
        $wp_customize->add_control(new WP_Customize_Heading_Control(
            $wp_customize,
            'brainfwd_top_bar_heading',
            array(
                'label'    => __('Topbar', 'brainfwd'),
                'section'  => 'brainfwd_navbar_section',
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
                'label'       => __( 'Offer', 'brainfwd' ),
                'settings'    => 'navbar_offer_setting',
                'section'     => 'brainfwd_navbar_section',
                'type'        => 'select',
                'choices'     => array(
                    'show' => __( 'Show', 'brainfwd' ),
                    'hide' => __( 'Hide', 'brainfwd' ),
                ),
                'description' => __( 'Select an option to hide or show the offer text.', 'brainfwd' ),
            )
        );

        // Navbar Offer Text Setting
        $wp_customize->add_setting( 'navbar_offer_text',
            array(
                'default' => __('🧠 Special Offer: Get 50% off your first course - Enroll today!', 'brainfwd'),
                'sanitize_callback' => 'wp_kses_post',
            )
        );


        $wp_customize->add_control( 'navbar_offer_text',
            array(
                'label'       => __( 'Offer Text', 'brainfwd' ),
                'settings'    => 'navbar_offer_text',
                'section'     => 'brainfwd_navbar_section',
                'type'        => 'text',
                'description' => __( 'Enter the text to display in the offer bar.', 'brainfwd' ),
            )
        );



        // Add a new section for Navbar Settings
        $wp_customize->add_section( 'brainfwd_header_section',
            array(
                'title'       => __( 'Header Settings', 'brainfwd' ),
                'capability'  => 'edit_theme_options',
                'panel'       => 'brainfwd_theme_options_panel',
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
                'label'      => __( 'Elementor Template for Header', 'brainfwd' ),
                'settings'   => 'header_elementor_template_setting',
                'section'    => 'brainfwd_header_section',
                'type'       => 'select',
                'choices'    => Brainfwd_Functions::get_post_title_array('elementor_library'), // Call this function to get Elementor library templates
                'description' => __( 'Select an Elementor template for your Header.', 'brainfwd' ),
            )
            );
        }


        // Add Background Image control
        $wp_customize->add_setting('header_bg_image', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'header_bg_image', array(
            'label'    => __('Header Background Image', 'brainfwd'),
            'section'  => 'brainfwd_header_section',
            'settings' => 'header_bg_image',
        )));

        // Navbar Background Color Setting
        $wp_customize->add_setting('header_bg_color', array(
            'default'           => '#0e1317', // Set default value as needed
            'sanitize_callback' => 'sanitize_hex_color',
        ));

        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_bg_color', array(
            'label'      => __('Header Background Color', 'brainfwd'),
            'section'    => 'brainfwd_header_section',
            'settings'   => 'header_bg_color',
            'description' => __('Choose a color for the header background.', 'brainfwd'),
        )));


        // Control for Loader Width
        $wp_customize->add_setting('header_color_opacity', array(
            'default'           => 90, // Set default width in percentage
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('header_color_opacity', array(
            'label'       => __('Background Opacity', 'brainfwd'),
            'description' => __('Set the opacity of the Background Color in percentage.', 'brainfwd'),
            'section'     => 'brainfwd_header_section',
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
            'label'      => __('Header Text Color', 'brainfwd'),
            'section'    => 'brainfwd_header_section',
            'settings'   => 'header_text_color',
            'description' => __('Choose a color for the header text.', 'brainfwd'),
        )));

        $wp_customize->add_setting('header_text_align', array(
        'default'           => 'center',
        'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control('header_text_align', array(
        'label'         => __('Text Align', 'brainfwd'),
        'settings'      => 'header_text_align',
        'section'       => 'brainfwd_header_section',
        'type'          => 'select',
        'choices'       => array(
            'left'   => __('Left', 'brainfwd'),
            'center' => __('Center', 'brainfwd'),
            'right'  => __('Right', 'brainfwd')
        ),
        'description'   => __('Change the alignment of the header text and title.', 'brainfwd'),
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
               'label'       => __( 'Scroll Down Arrow', 'brainfwd' ),
               'settings'    => 'header_scroll_arrow',
               'section'     => 'brainfwd_header_section',
               'type'        => 'select',
               'choices'     => array(
                   'show' => __( 'Show', 'brainfwd' ),
                   'hide' => __( 'Hide', 'brainfwd' ),
               ),
               'description' => __( 'Select an option to hide or show the scroll down arrow.', 'brainfwd' ),
           )
       );



        // Blog Section
        $wp_customize->add_section('brainfwd_blog_section', array(
            'title' => __('Blog Settings', 'brainfwd'),
            'capability' => 'edit_theme_options',
            'panel'      => 'brainfwd_theme_options_panel',
        ));

        // Read More Text Setting
        $wp_customize->add_setting('blog_list_page_title', array(
            'default'           => __('Blog List', 'brainfwd'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('blog_list_page_title', array(
            'label'       => __('Blog Page Title', 'brainfwd'),
            'settings'    => 'blog_list_page_title',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'text',
            'description' => __('Enter the text to display on the blog page title.', 'brainfwd'),
        ));


        // Sidebar Hide/Show
        $wp_customize->add_setting('blog_sidebar_display', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key', // Use sanitize_key for a select control
        ));

        $wp_customize->add_control('blog_sidebar_display', array(
            'label'       => __('Display Sidebar', 'brainfwd'),
            'section'     => 'brainfwd_blog_section',
            'settings'    => 'blog_sidebar_display',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Select whether to show or hide the sidebar.', 'brainfwd'),
        ));

        // Feature Image Display Setting
        $wp_customize->add_setting('blog_thumb_settings', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_thumb_settings', array(
            'label'       => __('Display Featured Image', 'brainfwd'),
            'settings'    => 'blog_thumb_settings',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the featured image on blog posts.', 'brainfwd'),
        ));

        // Feature Image Size Setting
        $wp_customize->add_setting('blog_thumb_size', array(
            'default'           => 'large',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_thumb_size', array(
            'label'       => __('Featured Image Size', 'brainfwd'),
            'settings'    => 'blog_thumb_size',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'thumbnail' => __('Thumbnail', 'brainfwd'),
                'medium'    => __('Medium', 'brainfwd'),
                'large'     => __('Large', 'brainfwd'),
                'full'      => __('Full Size', 'brainfwd'),
            ),
            'description' => __('Select the size of the featured image displayed on blog posts.', 'brainfwd'),
        ));


        // Author Display Setting
        $wp_customize->add_setting('blog_show_author', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_author', array(
            'label'       => __('Display Author', 'brainfwd'),
            'settings'    => 'blog_show_author',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the author information on blog posts.', 'brainfwd'),
        ));

        // Date Display Setting
        $wp_customize->add_setting('blog_show_date', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_date', array(
            'label'       => __('Display Date', 'brainfwd'),
            'settings'    => 'blog_show_date',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the post date on blog posts.', 'brainfwd'),
        ));

        // Comment Count Display Setting
        $wp_customize->add_setting('blog_show_comment_count', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_comment_count', array(
            'label'       => __('Display Comment Count', 'brainfwd'),
            'settings'    => 'blog_show_comment_count',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the comment count on blog posts.', 'brainfwd'),
        ));

        // Tags Display Setting
        $wp_customize->add_setting('blog_show_tags', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_tags', array(
            'label'       => __('Display Tags', 'brainfwd'),
            'settings'    => 'blog_show_tags',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the tags on blog posts.', 'brainfwd'),
        ));

        // Category Display Setting
        $wp_customize->add_setting('blog_show_category', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_category', array(
            'label'       => __('Display Categories', 'brainfwd'),
            'settings'    => 'blog_show_category',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the categories on blog posts.', 'brainfwd'),
        ));


        // Title Display Setting
        $wp_customize->add_setting('blog_show_title', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_title', array(
            'label'       => __('Display Title', 'brainfwd'),
            'settings'    => 'blog_show_title',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the post title on blog posts.', 'brainfwd'),
        ));

        // Excerpt Display Setting
        $wp_customize->add_setting('blog_show_excerpt', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_excerpt', array(
            'label'       => __('Display Excerpt', 'brainfwd'),
            'settings'    => 'blog_show_excerpt',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the excerpt on blog posts.', 'brainfwd'),
        ));

        // Excerpt Length Setting
        $wp_customize->add_setting('blog_excerpt_length', array(
            'default'           => 30,
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('blog_excerpt_length', array(
            'label'       => __('Excerpt Length', 'brainfwd'),
            'settings'    => 'blog_excerpt_length',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'number',
            'description' => __('Set the number of words for the blog post excerpt.', 'brainfwd'),
        ));

        // Read More Button Display Setting
        $wp_customize->add_setting('blog_read_more', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_read_more', array(
            'label'       => __('Display Read More Button', 'brainfwd'),
            'settings'    => 'blog_read_more',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the Read More button on blog posts.', 'brainfwd'),
        ));

        // Read More Text Setting
        $wp_customize->add_setting('blog_read_more_text', array(
            'default'           => __('Read More', 'brainfwd'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('blog_read_more_text', array(
            'label'       => __('Read More Button Text', 'brainfwd'),
            'settings'    => 'blog_read_more_text',
            'section'     => 'brainfwd_blog_section',
            'type'        => 'text',
            'description' => __('Enter the text to display on the Read More button.', 'brainfwd'),
        ));

    





        // Blog Section
        $wp_customize->add_section('brainfwd_blog_details_section', array(
            'title' => __('Blog Details Settings', 'brainfwd'),
            'capability' => 'edit_theme_options',
            'panel'      => 'brainfwd_theme_options_panel',
        ));


        // Sidebar Hide/Show
        $wp_customize->add_setting('blog_details_sidebar_display', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key', // Use sanitize_key for a select control
        ));

        $wp_customize->add_control('blog_details_sidebar_display', array(
            'label'       => __('Display Sidebar', 'brainfwd'),
            'section'     => 'brainfwd_blog_details_section',
            'settings'    => 'blog_details_sidebar_display',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Select whether to show or hide the sidebar.', 'brainfwd'),
        ));

        // Author Display Setting
        $wp_customize->add_setting('blog_details_show_author', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_author', array(
            'label'       => __('Display Author', 'brainfwd'),
            'settings'    => 'blog_details_show_author',
            'section'     => 'brainfwd_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the author information on blog posts.', 'brainfwd'),
        ));

        // Date Display Setting
        $wp_customize->add_setting('blog_details_show_date', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_date', array(
            'label'       => __('Display Date', 'brainfwd'),
            'settings'    => 'blog_details_show_date',
            'section'     => 'brainfwd_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the post date on blog posts.', 'brainfwd'),
        ));

        // Comment Count Display Setting
        $wp_customize->add_setting('blog_details_show_comment_count', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_comment_count', array(
            'label'       => __('Display Comment Count', 'brainfwd'),
            'settings'    => 'blog_details_show_comment_count',
            'section'     => 'brainfwd_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the comment count on blog posts.', 'brainfwd'),
        ));

        // Category Display Setting
        $wp_customize->add_setting('blog_details_show_category', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_category', array(
            'label'       => __('Display Categories', 'brainfwd'),
            'settings'    => 'blog_details_show_category',
            'section'     => 'brainfwd_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the categories on blog posts.', 'brainfwd'),
        ));

        // Tags Display Setting
        $wp_customize->add_setting('blog_details_show_tags', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_tags', array(
            'label'       => __('Display Tags', 'brainfwd'),
            'settings'    => 'blog_details_show_tags',
            'section'     => 'brainfwd_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the tags on blog posts.', 'brainfwd'),
        ));
        // Social Share Display Setting
        $wp_customize->add_setting('blog_show_social_share', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_show_social_share', array(
            'label'       => __('Display Social Share Buttons', 'brainfwd'),
            'settings'    => 'blog_show_social_share',
            'section'     => 'brainfwd_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide social share buttons on blog details.', 'brainfwd'),
        ));

        // Next and Previous Post Navigation Setting
        $wp_customize->add_setting('blog_details_show_post_navigation', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('blog_details_show_post_navigation', array(
            'label'       => __('Display Post Navigation', 'brainfwd'),
            'settings'    => 'blog_details_show_post_navigation',
            'section'     => 'brainfwd_blog_details_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the navigation to the next and previous posts on blog details.', 'brainfwd'),
        ));



        $wp_customize->add_section('brainfwd_woocommerce_section', array(
            'title'       => __('WooCommerce Settings', 'brainfwd'),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainfwd_theme_options_panel',
        ));

       // Next and Previous Post Navigation Setting
        $wp_customize->add_setting('woocommerce_page_header', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('woocommerce_page_header', array(
            'label'       => __('Display Page Header', 'brainfwd'),
            'settings'    => 'woocommerce_page_header',
            'section'     => 'brainfwd_woocommerce_section',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the page header that displays the page title and subtitle on WooCommerce pages.', 'brainfwd'),
        ));

        // Display Sale Badge
        $wp_customize->add_setting('woocommerce_sale_badge', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('woocommerce_sale_badge', array(
            'label'       => __('Display Sale Badge', 'brainfwd'),
            'section'     => 'brainfwd_woocommerce_section',
            'settings'    => 'woocommerce_sale_badge',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the sale badge on products.', 'brainfwd'),
        ));


        // Wishlist Button Show/Hide
        $wp_customize->add_setting('woocommerce_wishlist_button', array(
            'default'           => 'show',
            'sanitize_callback' => 'sanitize_key',
        ));

        $wp_customize->add_control('woocommerce_wishlist_button', array(
            'label'       => __('Display Wishlist Button', 'brainfwd'),
            'section'     => 'brainfwd_woocommerce_section',
            'settings'    => 'woocommerce_wishlist_button',
            'type'        => 'select',
            'choices'     => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
            'description' => __('Choose whether to show or hide the wishlist button on product pages.', 'brainfwd'),
        ));



        

        $wp_customize->add_section('brainfwd_footer_section', array(
            'title'       => __('Footer Settings', 'brainfwd'),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainfwd_theme_options_panel',
        ));    

        if ( class_exists( 'Elementor\Plugin' ) ) {
            // Add control for Elementor Template
            $wp_customize->add_setting('footer_elementor_template_setting', array(
            'default'           => 'default',
            'sanitize_callback' => 'sanitize_text_field',
            ));
            $wp_customize->add_control('footer_elementor_template_control', array(
            'label'         => __('Elementor Template for Footer', 'brainfwd'),
            'settings'      => 'footer_elementor_template_setting',
            'section'       => 'brainfwd_footer_section',
            'type'          => 'select',
            'choices'       => Brainfwd_Functions::get_post_title_array('elementor_library'), // Ensure this function is defined to get Elementor library templates
            'description'   => __('Select an Elementor template for your footer.', 'brainfwd'),
            ));
        }

         // Add a text control for copyrights text in the Footer section

        // Control for Copyrights Text
        $wp_customize->add_setting('copyright_text_setting', array(
            'default'           => __('©2025 All rights reserved. Powered by Brainforward', 'brainfwd'),
            'sanitize_callback' => 'sanitize_textarea_field',
        ));

        $wp_customize->add_control('copyright_text_setting', array(
            'label'    => __('Copyrights Text', 'brainfwd'),
            'section'  => 'brainfwd_footer_section',
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
            'label'      => __( 'Scroll Up Button', 'brainfwd' ),
            'settings'   => 'scroll_up_settings',
            'section'    => 'brainfwd_footer_section',
            'type'       => 'select',
            'choices'    => array(
                    'show' => __( 'Show', 'brainfwd' ),
                    'hide'  => __( 'Hide', 'brainfwd' ),
            ),
            'description' => __( 'Select a option to hide or show scroll to up button.', 'brainfwd' ),
            )
        );

        // Add controls for 404 page settings
        $wp_customize->add_section('brainfwd_404_section', array(
            'title'      => __('404 Page', 'brainfwd'),
            'capability' => 'edit_theme_options',
            'panel'      => 'brainfwd_theme_options_panel',
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
                'label'      => __( 'Elementor Template for 404', 'brainfwd' ),
                'settings'   => '404_elementor_template_setting',
                'section'    => 'brainfwd_404_section',
                'type'       => 'select',
                'choices'    => Brainfwd_Functions::get_post_title_array('elementor_library'), // Call this function to get Elementor library templates
                'description' => __( 'Select an Elementor template for your 404 page.', 'brainfwd' ),
            )
            );
        }

        // Control for 404 Image
        $wp_customize->add_setting('brainfwd_404_image', array(
            'default'           => get_theme_file_uri('assets/images/404.png'),
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'brainfwd_404_image', array(
            'label'    => __('404 Image', 'brainfwd'),
            'section'  => 'brainfwd_404_section',
            'settings' => 'brainfwd_404_image',
        )));

        // Control for Page Title
        $wp_customize->add_setting('brainfwd_404_title', array(
            'default'           => __('Oops... Page Not Found!', 'brainfwd'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('brainfwd_404_title', array(
            'label'    => __('Page Title', 'brainfwd'),
            'section'  => 'brainfwd_404_section',
            'type'     => 'text',
        ));

        // Control for Page Description
        $wp_customize->add_setting('brainfwd_404_description', array(
            'default'           => __('Please return to the site\'s homepage. It looks like nothing was found at this location. Get in touch to discuss your employee needs today. Please give us a call, drop us an email.', 'brainfwd'),
            'sanitize_callback' => 'sanitize_textarea_field',
        ));

        $wp_customize->add_control('brainfwd_404_description', array(
            'label'    => __('Page Description', 'brainfwd'),
            'section'  => 'brainfwd_404_section',
            'type'     => 'textarea',
        ));

        // Control for Page Title
        $wp_customize->add_setting('brainfwd_404_button_text', array(
            'default'           => __('Back to Home', 'brainfwd'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('brainfwd_404_button_text', array(
            'label'    => __('Button Text', 'brainfwd'),
            'section'  => 'brainfwd_404_section',
            'type'     => 'text',
        ));

        // Add controls for loader settings
        $wp_customize->add_section('brainfwd_loader_section', array(
            'title'      => __('Page Loader', 'brainfwd'),
            'capability' => 'edit_theme_options',
            'panel'      => 'brainfwd_theme_options_panel',
        ));
        
        // Control for Loader Visibility (Enable/Disable)
        $wp_customize->add_setting('brainfwd_loader_visibility', array( // Updated variable name to '_visibility'
            'default'           => 'show', // Set default to "show"
            'sanitize_callback' => 'sanitize_key',
        ));
        
        $wp_customize->add_control('brainfwd_loader_visibility', array( // Updated variable name to '_visibility'
            'label'    => __('Loader Visibility', 'brainfwd'),
            'section'  => 'brainfwd_loader_section',
            'type'     => 'select',
            'choices'  => array(
                'show' => __('Show', 'brainfwd'),
                'hide' => __('Hide', 'brainfwd'),
            ),
        ));


        // Control for Loader Type (Enable/Disable)
        $wp_customize->add_setting('brainfwd_loader_type', array( // Updated variable name to 'type'
            'default'           => 'loader', // Set default to "show"
            'sanitize_callback' => 'sanitize_key',
        ));
        
        $wp_customize->add_control('brainfwd_loader_type', array( // Updated variable name to '_type'
            'label'    => __('Loader Type', 'brainfwd'),
            'section'  => 'brainfwd_loader_section',
            'type'     => 'select',
            'choices'  => array(
                'text' => __('Text', 'brainfwd'),
                'loader' => __('Loader', 'brainfwd'),
                'template' => __('Template', 'brainfwd'),
            ),
        ));
        
        
        // Loader Text Setting
        $wp_customize->add_setting( 'loader_text_setting',
            array(
                'default'           => __('Loading', 'brainfwd'),
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control( 'loader_text_control',
            array(
                'label'       => __( 'Loader Text', 'brainfwd' ),
                'section'     => 'brainfwd_loader_section',
                'settings'    => 'loader_text_setting',
                'type'        => 'text',
                'description' => __( 'Enter the text to display in the loader (max 10 characters).', 'brainfwd' ),
            )
        );


        if ( class_exists( 'Elementor\Plugin' ) ) {
            // Add control for Elementor Template for Navbar (Preloader)
            $wp_customize->add_setting( 'loader_template_settings', array(
                'default'           => 'default',
                'sanitize_callback' => 'sanitize_text_field',
            ));
        
            $wp_customize->add_control( 'loader_template_control', array(
                'label'       => __( 'Elementor Template for Preloader', 'brainfwd' ),
                'settings'    => 'loader_template_settings',
                'section'     => 'brainfwd_loader_section', // Ensure you have a section defined for the navbar
                'type'        => 'select',
                'choices'     => Brainfwd_Functions::get_post_title_array('elementor_library'), // Retrieves Elementor library templates
                'description' => __( 'Select an Elementor template to be used as the preloader. This will control the visual appearance of the preloader before the page fully loads.', 'brainfwd' ),
            ));
        }
        

        // Adding the "Additional Scripts" section under the Additional CSS section
        $wp_customize->add_section('brainfwd_additional_scripts_section', array(
            'title'       => __('Additional Scripts', 'brainfwd'),
            'capability'  => 'edit_theme_options',
            'description' => __('Add custom scripts to be included in the theme\'s script file.', 'brainfwd'),
            'section'     => 'custom_css', // Parent section set to Additional CSS
            'priority'    => 999,
        ));

        // API & Integration Settings Section
        $wp_customize->add_section('brainfwd_api_section', array(
            'title'       => __('API & Integrations', 'brainfwd'),
            'capability'  => 'edit_theme_options',
            'panel'       => 'brainfwd_theme_options_panel',
            'description' => __('Configure API keys and third-party integrations.', 'brainfwd'),
        ));

        // SMS API Token Setting
        $wp_customize->add_setting('sms_api_token', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('sms_api_token', array(
            'label'       => __('SMS API Token', 'brainfwd'),
            'section'     => 'brainfwd_api_section',
            'type'        => 'text',
            'description' => __('Enter your SSL Wireless SMS API token.', 'brainfwd'),
        ));

        // SMS Sender ID Setting
        $wp_customize->add_setting('sms_sender_id', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('sms_sender_id', array(
            'label'       => __('SMS Sender ID', 'brainfwd'),
            'section'     => 'brainfwd_api_section',
            'type'        => 'text',
            'description' => __('Enter your SMS sender ID (SID).', 'brainfwd'),
        ));

        // Google Tag Manager ID Setting
        $wp_customize->add_setting('gtm_container_id', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('gtm_container_id', array(
            'label'       => __('Google Tag Manager Container ID', 'brainfwd'),
            'section'     => 'brainfwd_api_section',
            'type'        => 'text',
            'description' => __('Enter your GTM Container ID (e.g., GTM-XXXXXXX).', 'brainfwd'),
        ));

        // Google Analytics ID Setting
        $wp_customize->add_setting('ga_measurement_id', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('ga_measurement_id', array(
            'label'       => __('Google Analytics Measurement ID', 'brainfwd'),
            'section'     => 'brainfwd_api_section',
            'type'        => 'text',
            'description' => __('Enter your GA4 Measurement ID (e.g., G-XXXXXXXXXX).', 'brainfwd'),
        ));

        // Google Site Verification Setting
        $wp_customize->add_setting('google_site_verification', array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('google_site_verification', array(
            'label'       => __('Google Site Verification Code', 'brainfwd'),
            'section'     => 'brainfwd_api_section',
            'type'        => 'text',
            'description' => __('Enter your Google site verification code.', 'brainfwd'),
        ));

    }

    public function customize_preview_js() {
        wp_enqueue_script(
            'brainfwd_customizer',
            BRAINFWD_JS_URI . '/customizer.js',
            ['customize-preview'],
            BRAINFWD_THEME_VERSION,
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

new Brainfwd_Customizer();