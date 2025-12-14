<?php
/**
 * Admin Settings Template.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tabs = array(
    'general'      => __( 'General', 'edugo-lms' ),
    'monetization' => __( 'Monetization', 'edugo-lms' ),
    'quiz'         => __( 'Quiz', 'edugo-lms' ),
    'certificate'  => __( 'Certificate', 'edugo-lms' ),
    'email'        => __( 'Email', 'edugo-lms' ),
    'pages'        => __( 'Pages', 'edugo-lms' ),
);

// Handle form submission.
if ( isset( $_POST['edugo_save_settings'] ) && check_admin_referer( 'edugo_settings_nonce' ) ) {
    // Save settings based on active tab.
    switch ( $active_tab ) {
        case 'general':
            update_option( 'edugo_course_approval_required', isset( $_POST['edugo_course_approval_required'] ) ? 'yes' : 'no' );
            update_option( 'edugo_progress_calculation', sanitize_text_field( wp_unslash( $_POST['edugo_progress_calculation'] ?? 'lesson_based' ) ) );
            update_option( 'edugo_drip_content_enabled', isset( $_POST['edugo_drip_content_enabled'] ) ? 'yes' : 'no' );
            break;

        case 'monetization':
            update_option( 'edugo_instructor_commission', absint( $_POST['edugo_instructor_commission'] ?? 70 ) );
            update_option( 'edugo_minimum_withdrawal', absint( $_POST['edugo_minimum_withdrawal'] ?? 50 ) );
            update_option( 'edugo_currency', sanitize_text_field( wp_unslash( $_POST['edugo_currency'] ?? 'USD' ) ) );
            break;

        case 'quiz':
            update_option( 'edugo_quiz_passing_grade', absint( $_POST['edugo_quiz_passing_grade'] ?? 60 ) );
            update_option( 'edugo_quiz_retake_limit', absint( $_POST['edugo_quiz_retake_limit'] ?? 3 ) );
            break;

        case 'certificate':
            update_option( 'edugo_certificate_enabled', isset( $_POST['edugo_certificate_enabled'] ) ? 'yes' : 'no' );
            update_option( 'edugo_certificate_template', wp_kses_post( wp_unslash( $_POST['edugo_certificate_template'] ?? '' ) ) );
            break;

        case 'email':
            update_option( 'edugo_email_notifications', isset( $_POST['edugo_email_notifications'] ) ? 'yes' : 'no' );
            update_option( 'edugo_admin_email', sanitize_email( wp_unslash( $_POST['edugo_admin_email'] ?? '' ) ) );
            break;

        case 'pages':
            update_option( 'edugo_student_dashboard_page', absint( $_POST['edugo_student_dashboard_page'] ?? 0 ) );
            update_option( 'edugo_instructor_dashboard_page', absint( $_POST['edugo_instructor_dashboard_page'] ?? 0 ) );
            update_option( 'edugo_courses_page', absint( $_POST['edugo_courses_page'] ?? 0 ) );
            break;
    }

    echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved successfully.', 'edugo-lms' ) . '</p></div>';
}
?>

<div class="wrap edugo-admin-wrap">
    <h1><?php esc_html_e( 'Edugo LMS Settings', 'edugo-lms' ); ?></h1>

    <nav class="nav-tab-wrapper">
        <?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
            <a href="<?php echo esc_url( add_query_arg( 'tab', $tab_key, admin_url( 'admin.php?page=edugo-settings' ) ) ); ?>"
               class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html( $tab_label ); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <form method="post" action="">
        <?php wp_nonce_field( 'edugo_settings_nonce' ); ?>

        <table class="form-table">
            <?php
            switch ( $active_tab ) :
                case 'general':
                    ?>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Course Approval Required', 'edugo-lms' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="edugo_course_approval_required"
                                       value="yes" <?php checked( get_option( 'edugo_course_approval_required' ), 'yes' ); ?>>
                                <?php esc_html_e( 'Require admin approval for instructor courses', 'edugo-lms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Progress Calculation', 'edugo-lms' ); ?></th>
                        <td>
                            <select name="edugo_progress_calculation">
                                <option value="lesson_based" <?php selected( get_option( 'edugo_progress_calculation' ), 'lesson_based' ); ?>>
                                    <?php esc_html_e( 'Lesson Based', 'edugo-lms' ); ?>
                                </option>
                                <option value="quiz_based" <?php selected( get_option( 'edugo_progress_calculation' ), 'quiz_based' ); ?>>
                                    <?php esc_html_e( 'Quiz Based', 'edugo-lms' ); ?>
                                </option>
                                <option value="combined" <?php selected( get_option( 'edugo_progress_calculation' ), 'combined' ); ?>>
                                    <?php esc_html_e( 'Combined (Lessons + Quizzes)', 'edugo-lms' ); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Drip Content', 'edugo-lms' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="edugo_drip_content_enabled"
                                       value="yes" <?php checked( get_option( 'edugo_drip_content_enabled' ), 'yes' ); ?>>
                                <?php esc_html_e( 'Enable drip content scheduling', 'edugo-lms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <?php
                    break;

                case 'monetization':
                    ?>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Instructor Commission (%)', 'edugo-lms' ); ?></th>
                        <td>
                            <input type="number" name="edugo_instructor_commission" min="0" max="100"
                                   value="<?php echo esc_attr( get_option( 'edugo_instructor_commission', 70 ) ); ?>">
                            <p class="description"><?php esc_html_e( 'Percentage of sale that goes to the instructor.', 'edugo-lms' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Minimum Withdrawal Amount', 'edugo-lms' ); ?></th>
                        <td>
                            <input type="number" name="edugo_minimum_withdrawal" min="0"
                                   value="<?php echo esc_attr( get_option( 'edugo_minimum_withdrawal', 50 ) ); ?>">
                        </td>
                    </tr>
                    <?php
                    break;

                case 'quiz':
                    ?>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Default Passing Grade (%)', 'edugo-lms' ); ?></th>
                        <td>
                            <input type="number" name="edugo_quiz_passing_grade" min="0" max="100"
                                   value="<?php echo esc_attr( get_option( 'edugo_quiz_passing_grade', 60 ) ); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Default Retake Limit', 'edugo-lms' ); ?></th>
                        <td>
                            <input type="number" name="edugo_quiz_retake_limit" min="0"
                                   value="<?php echo esc_attr( get_option( 'edugo_quiz_retake_limit', 3 ) ); ?>">
                            <p class="description"><?php esc_html_e( 'Set to 0 for unlimited retakes.', 'edugo-lms' ); ?></p>
                        </td>
                    </tr>
                    <?php
                    break;

                case 'certificate':
                    ?>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable Certificates', 'edugo-lms' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="edugo_certificate_enabled"
                                       value="yes" <?php checked( get_option( 'edugo_certificate_enabled' ), 'yes' ); ?>>
                                <?php esc_html_e( 'Enable course completion certificates', 'edugo-lms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <?php
                    break;

                case 'email':
                    ?>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Email Notifications', 'edugo-lms' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="edugo_email_notifications"
                                       value="yes" <?php checked( get_option( 'edugo_email_notifications' ), 'yes' ); ?>>
                                <?php esc_html_e( 'Enable email notifications', 'edugo-lms' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Admin Email', 'edugo-lms' ); ?></th>
                        <td>
                            <input type="email" name="edugo_admin_email" class="regular-text"
                                   value="<?php echo esc_attr( get_option( 'edugo_admin_email', get_option( 'admin_email' ) ) ); ?>">
                        </td>
                    </tr>
                    <?php
                    break;

                case 'pages':
                    $pages = get_pages();
                    ?>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Student Dashboard Page', 'edugo-lms' ); ?></th>
                        <td>
                            <select name="edugo_student_dashboard_page">
                                <option value=""><?php esc_html_e( 'Select a page', 'edugo-lms' ); ?></option>
                                <?php foreach ( $pages as $page ) : ?>
                                    <option value="<?php echo esc_attr( $page->ID ); ?>"
                                            <?php selected( get_option( 'edugo_student_dashboard_page' ), $page->ID ); ?>>
                                        <?php echo esc_html( $page->post_title ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Instructor Dashboard Page', 'edugo-lms' ); ?></th>
                        <td>
                            <select name="edugo_instructor_dashboard_page">
                                <option value=""><?php esc_html_e( 'Select a page', 'edugo-lms' ); ?></option>
                                <?php foreach ( $pages as $page ) : ?>
                                    <option value="<?php echo esc_attr( $page->ID ); ?>"
                                            <?php selected( get_option( 'edugo_instructor_dashboard_page' ), $page->ID ); ?>>
                                        <?php echo esc_html( $page->post_title ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Courses Page', 'edugo-lms' ); ?></th>
                        <td>
                            <select name="edugo_courses_page">
                                <option value=""><?php esc_html_e( 'Select a page', 'edugo-lms' ); ?></option>
                                <?php foreach ( $pages as $page ) : ?>
                                    <option value="<?php echo esc_attr( $page->ID ); ?>"
                                            <?php selected( get_option( 'edugo_courses_page' ), $page->ID ); ?>>
                                        <?php echo esc_html( $page->post_title ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                    break;
            endswitch;
            ?>
        </table>

        <p class="submit">
            <input type="submit" name="edugo_save_settings" class="button-primary"
                   value="<?php esc_attr_e( 'Save Settings', 'edugo-lms' ); ?>">
        </p>
    </form>
</div>
