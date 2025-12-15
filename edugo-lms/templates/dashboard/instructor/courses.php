<?php
/**
 * Instructor Dashboard - Courses Tab.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$instructor_id = get_current_user_id();

$courses = get_posts( array(
    'post_type'      => 'edugo_course',
    'author'         => $instructor_id,
    'posts_per_page' => -1,
    'post_status'    => array( 'publish', 'draft', 'pending' ),
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

$enrollment_manager = new Edugo_LMS\LMS\Enrollment\Enrollment_Manager();
?>

<div class="edugo-dashboard-header">
    <h2><?php esc_html_e( 'My Courses', 'edugo-lms' ); ?></h2>
    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=edugo_course' ) ); ?>" class="edugo-button edugo-button-primary">
        <span class="dashicons dashicons-plus-alt2"></span>
        <?php esc_html_e( 'Create Course', 'edugo-lms' ); ?>
    </a>
</div>

<?php if ( ! empty( $courses ) ) : ?>
    <div class="edugo-courses-table-wrapper">
        <table class="edugo-table edugo-courses-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Course', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Students', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Price', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Actions', 'edugo-lms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $courses as $course ) :
                    $student_count = $enrollment_manager->get_enrollment_count( $course->ID );
                    $is_free = get_post_meta( $course->ID, '_edugo_is_free', true ) === 'yes';
                    $price = get_post_meta( $course->ID, '_edugo_price', true );
                    ?>
                    <tr>
                        <td>
                            <div class="edugo-course-info">
                                <?php if ( has_post_thumbnail( $course ) ) : ?>
                                    <div class="edugo-course-thumb">
                                        <?php echo get_the_post_thumbnail( $course, 'thumbnail' ); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="edugo-course-title-wrap">
                                    <a href="<?php echo esc_url( get_permalink( $course ) ); ?>" class="edugo-course-title">
                                        <?php echo esc_html( $course->post_title ); ?>
                                    </a>
                                    <span class="edugo-course-date">
                                        <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $course->post_date ) ) ); ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="edugo-student-count"><?php echo esc_html( $student_count ); ?></span>
                        </td>
                        <td>
                            <?php if ( $is_free ) : ?>
                                <span class="edugo-free-badge"><?php esc_html_e( 'Free', 'edugo-lms' ); ?></span>
                            <?php elseif ( $price ) : ?>
                                <span class="edugo-price"><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( (float) $price ) ); ?></span>
                            <?php else : ?>
                                <span class="edugo-text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $status_labels = array(
                                'publish' => __( 'Published', 'edugo-lms' ),
                                'draft'   => __( 'Draft', 'edugo-lms' ),
                                'pending' => __( 'Pending Review', 'edugo-lms' ),
                            );
                            $status_class = $course->post_status;
                            ?>
                            <span class="edugo-status edugo-status-<?php echo esc_attr( $status_class ); ?>">
                                <?php echo esc_html( $status_labels[ $course->post_status ] ?? $course->post_status ); ?>
                            </span>
                        </td>
                        <td>
                            <div class="edugo-actions">
                                <a href="<?php echo esc_url( get_edit_post_link( $course ) ); ?>" class="edugo-action-btn" title="<?php esc_attr_e( 'Edit', 'edugo-lms' ); ?>">
                                    <span class="dashicons dashicons-edit"></span>
                                </a>
                                <a href="<?php echo esc_url( get_permalink( $course ) ); ?>" class="edugo-action-btn" title="<?php esc_attr_e( 'View', 'edugo-lms' ); ?>">
                                    <span class="dashicons dashicons-visibility"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <div class="edugo-empty-state">
        <div class="edugo-empty-icon">
            <span class="dashicons dashicons-welcome-learn-more"></span>
        </div>
        <h3><?php esc_html_e( 'No Courses Yet', 'edugo-lms' ); ?></h3>
        <p><?php esc_html_e( 'Create your first course and start teaching today!', 'edugo-lms' ); ?></p>
        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=edugo_course' ) ); ?>" class="edugo-button edugo-button-primary">
            <?php esc_html_e( 'Create Course', 'edugo-lms' ); ?>
        </a>
    </div>
<?php endif; ?>
