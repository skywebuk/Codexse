<?php
/**
 * Instructor Dashboard - Earnings Tab.
 *
 * @package Edugo_LMS
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$instructor_id = get_current_user_id();

global $wpdb;
$earnings_table = $wpdb->prefix . 'edugo_earnings';

// Get earnings summary.
$total_earnings = (float) $wpdb->get_var(
    $wpdb->prepare(
        "SELECT SUM(commission_amount) FROM {$earnings_table} WHERE instructor_id = %d",
        $instructor_id
    )
);

$pending_earnings = (float) $wpdb->get_var(
    $wpdb->prepare(
        "SELECT SUM(commission_amount) FROM {$earnings_table} WHERE instructor_id = %d AND status = 'pending'",
        $instructor_id
    )
);

$withdrawn_earnings = (float) $wpdb->get_var(
    $wpdb->prepare(
        "SELECT SUM(commission_amount) FROM {$earnings_table} WHERE instructor_id = %d AND status = 'completed'",
        $instructor_id
    )
);

// Get recent earnings.
$recent_earnings = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$earnings_table} WHERE instructor_id = %d ORDER BY created_at DESC LIMIT 20",
        $instructor_id
    )
);
?>

<div class="edugo-dashboard-header">
    <h2><?php esc_html_e( 'My Earnings', 'edugo-lms' ); ?></h2>
</div>

<div class="edugo-earnings-summary">
    <div class="edugo-earnings-card">
        <span class="edugo-earnings-label"><?php esc_html_e( 'Total Earnings', 'edugo-lms' ); ?></span>
        <span class="edugo-earnings-value"><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( $total_earnings ) ); ?></span>
    </div>

    <div class="edugo-earnings-card">
        <span class="edugo-earnings-label"><?php esc_html_e( 'Available Balance', 'edugo-lms' ); ?></span>
        <span class="edugo-earnings-value edugo-text-success"><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( $pending_earnings ) ); ?></span>
    </div>

    <div class="edugo-earnings-card">
        <span class="edugo-earnings-label"><?php esc_html_e( 'Withdrawn', 'edugo-lms' ); ?></span>
        <span class="edugo-earnings-value"><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( $withdrawn_earnings ) ); ?></span>
    </div>
</div>

<?php
$minimum_withdrawal = (float) get_option( 'edugo_minimum_withdrawal', 50 );
if ( $pending_earnings >= $minimum_withdrawal ) :
    ?>
    <div class="edugo-withdrawal-cta">
        <p>
            <?php
            printf(
                /* translators: %s: Available balance */
                esc_html__( 'You have %s available for withdrawal.', 'edugo-lms' ),
                '<strong>' . esc_html( Edugo_LMS\Helpers\Helper::format_price( $pending_earnings ) ) . '</strong>'
            );
            ?>
        </p>
        <a href="<?php echo esc_url( add_query_arg( 'tab', 'withdrawals' ) ); ?>" class="edugo-button edugo-button-primary">
            <?php esc_html_e( 'Request Withdrawal', 'edugo-lms' ); ?>
        </a>
    </div>
<?php endif; ?>

<div class="edugo-earnings-history">
    <h3><?php esc_html_e( 'Earnings History', 'edugo-lms' ); ?></h3>

    <?php if ( ! empty( $recent_earnings ) ) : ?>
        <table class="edugo-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Date', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Course', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Order Total', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Commission', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Your Earnings', 'edugo-lms' ); ?></th>
                    <th><?php esc_html_e( 'Status', 'edugo-lms' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $recent_earnings as $earning ) :
                    $course = get_post( $earning->course_id );
                    ?>
                    <tr>
                        <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $earning->created_at ) ) ); ?></td>
                        <td>
                            <?php if ( $course ) : ?>
                                <?php echo esc_html( $course->post_title ); ?>
                            <?php else : ?>
                                <span class="edugo-text-muted"><?php esc_html_e( 'Deleted', 'edugo-lms' ); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( (float) $earning->order_total ) ); ?></td>
                        <td><?php echo esc_html( $earning->commission_rate ); ?>%</td>
                        <td><strong><?php echo esc_html( Edugo_LMS\Helpers\Helper::format_price( (float) $earning->commission_amount ) ); ?></strong></td>
                        <td>
                            <?php
                            $status_labels = array(
                                'pending'   => __( 'Pending', 'edugo-lms' ),
                                'completed' => __( 'Paid', 'edugo-lms' ),
                            );
                            $status_class = $earning->status === 'completed' ? 'success' : 'warning';
                            ?>
                            <span class="edugo-badge edugo-badge-<?php echo esc_attr( $status_class ); ?>">
                                <?php echo esc_html( $status_labels[ $earning->status ] ?? $earning->status ); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="edugo-empty-state edugo-empty-state-small">
            <p><?php esc_html_e( 'No earnings yet. Once students purchase your courses, your earnings will appear here.', 'edugo-lms' ); ?></p>
        </div>
    <?php endif; ?>
</div>
