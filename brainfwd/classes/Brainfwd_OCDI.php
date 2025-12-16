<?php

class Brainfwd_OCDI {

    public function __construct() {
        add_filter('pt-ocdi/import_files', [$this, 'import_files']);
        add_action('pt-ocdi/after_import', [$this, 'after_import_setup']);
    }

    public function import_files() {
        return [
            [
                'import_file_name'  => esc_html__('Backpack Landing Page', 'brainfwd'),
                'local_import_file' => 'https://wp.brainforward.com/brainforward/dummy-data/home-1/content.xml',
                'local_import_widget_file'  => 'https://wp.brainforward.com/brainforward/dummy-data/home-1/widget.wie',
                'local_import_customizer_file'  => 'https://wp.brainforward.com/brainforward/dummy-data/home-1/customizer.dat',
                'import_preview_image_url'  => get_template_directory_uri() . '/lib/dummy-data/images/home-1.png',
                'preview_url'   => 'http://wp.brainforward.com/brainforward/',
            ],
        ];
    }

    public function after_import_setup($selected_import) {
        // Assign menus to their locations.
        $main_menu = get_term_by('name', 'Mainmenu', 'nav_menu');
        set_theme_mod('nav_menu_locations', [
            'primary_menu' => $main_menu->term_id
        ]);
        // Assign front page and posts page (blog page).
        $front_page_id = get_page_by_title('Home');
        $blog_page_id  = get_page_by_title('Blog');
        update_option('show_on_front', 'page');
        update_option('page_on_front', $front_page_id->ID);
        update_option('page_for_posts', $blog_page_id->ID);
    }

    /*-- Mainmenu-Demo-Content --*/
    public static function mainmenu_demo_content() {
        if (!current_user_can('edit_theme_options')) {
            return;
        }
        printf(
            wp_kses('<a class="select_menu_link" href="%s">%s</a>', wp_kses_allowed_html('post')),
            esc_url(admin_url('nav-menus.php')),
            esc_html__('Setup a menu', 'brainfwd')
        );
    }
}

// Initialize the class
new Brainfwd_OCDI();
