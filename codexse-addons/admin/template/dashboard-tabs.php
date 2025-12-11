<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<ul class="codexse-navigation-menu">
    <?php
    $tabs = [
        'codexse-addons' => [
            'label' => __( 'General', 'codexse' ),
            'icon'  => 'info-circle', // Icon for General
        ],
        'codexse-widgets' => [
            'label' => __( 'Widgets', 'codexse' ),
            'icon'  => '3d-cube', // Icon for Widgets
        ],
        'codexse-features' => [
            'label' => __( 'Features', 'codexse' ),
            'icon'  => 'star', // Icon for Features
        ],
    ];

    $current_page = isset( $_GET['page'] ) ? $_GET['page'] : 'codexse';

    foreach ( $tabs as $page => $data ) :
        $active_class = ( $current_page === $page ) ? 'active' : '';
        ?>
        <li>
            <a href="<?php echo esc_url( admin_url( "admin.php?page=$page" ) ); ?>" class="nav-link <?php echo $active_class; ?>">
                <span class="nav-icon"><i class="cx-icon" icon-name="<?php echo esc_attr( $data['icon'] ); ?>"></i></span>
                <?php echo esc_html( $data['label'] ); ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
