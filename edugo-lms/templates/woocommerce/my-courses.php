<?php
/**
 * WooCommerce My Account - My Courses Tab.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();
$enrollment_manager = new Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
$progress_manager = new Edugo_LMS\LMS\Progress\Progress_Manager();
$enrollments = $enrollment_manager->get_user_enrollments( $user_id );
?>

<div class="edugo-wc-my-courses">
    <h2><?php esc_html_e( 'My Courses', 'edugo-lms' ); ?></h2>

    <?php if ( ! empty( $enrollments ) ) : ?>
        <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
            <thead>
                <tr>
                    <th class="woocommerce-orders-table__header"><?php esc_html_e( 'Course', 'edugo-lms' ); ?></th>
                    <th class="woocommerce-orders-table__header"><?php esc_html_e( 'Progress', 'edugo-lms' ); ?></th>
                    <th class="woocommerce-orders-table__header"><?php esc_html_e( 'Status', 'edugo-lms' ); ?></th>
                    <th class="woocommerce-orders-table__header"><?php esc_html_e( 'Actions', 'edugo-lms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $enrollments as $enrollment ) :
                    $course = get_post( $enrollment->course_id );
                    if ( ! $course ) continue;

                    $progress = $progress_manager->get_course_progress( $user_id, $enrollment->course_id );
                    ?>
                    <tr class="woocommerce-orders-table__row">
                        <td class="woocommerce-orders-table__cell" data-title="<?php esc_attr_e( 'Course', 'edugo-lms' ); ?>">
                            <a href="<?php echo esc_url( get_permalink( $course ) ); ?>">
                                <?php echo esc_html( $course->post_title ); ?>
                            </a>
                        </td>
                        <td class="woocommerce-orders-table__cell" data-title="<?php esc_attr_e( 'Progress', 'edugo-lms' ); ?>">
                            <div class="edugo-progress-mini">
                                <div class="edugo-progress-bar-mini" style="background: #e5e5e5; height: 8px; border-radius: 4px; overflow: hidden;">
                                    <div class="edugo-progress-fill" style="background: #7f54b3; height: 100%; width: <?php echo esc_attr( $progress['percentage'] ); ?>%;"></div>
                                </div>
                                <span style="font-size: 12px; color: #666;"><?php echo esc_html( $progress['percentage'] ); ?>%</span>
                            </div>
                        </td>
                        <td class="woocommerce-orders-table__cell" data-title="<?php esc_attr_e( 'Status', 'edugo-lms' ); ?>">
                            <?php if ( $enrollment->status === 'completed' ) : ?>
                                <span class="edugo-badge" style="background: #28a745; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 12px;">
                                    <?php esc_html_e( 'Completed', 'edugo-lms' ); ?>
                                </span>
                            <?php else : ?>
                                <span class="edugo-badge" style="background: #17a2b8; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 12px;">
                                    <?php esc_html_e( 'In Progress', 'edugo-lms' ); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="woocommerce-orders-table__cell" data-title="<?php esc_attr_e( 'Actions', 'edugo-lms' ); ?>">
                            <a href="<?php echo esc_url( get_permalink( $course ) ); ?>" class="woocommerce-button button">
                                <?php
                                if ( $progress['percentage'] >= 100 ) {
                                    esc_html_e( 'Review', 'edugo-lms' );
                                } elseif ( $progress['percentage'] > 0 ) {
                                    esc_html_e( 'Continue', 'edugo-lms' );
                                } else {
                                    esc_html_e( 'Start', 'edugo-lms' );
                                }
                                ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
            <?php esc_html_e( 'You have not enrolled in any courses yet.', 'edugo-lms' ); ?>
            <a class="woocommerce-Button button" href="<?php echo esc_url( get_post_type_archive_link( 'edugo_course' ) ); ?>">
                <?php esc_html_e( 'Browse Courses', 'edugo-lms' ); ?>
            </a>
        </div>
    <?php endif; ?>
</div>
