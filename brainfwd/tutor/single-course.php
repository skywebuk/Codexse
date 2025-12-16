<?php
/**
 * Template for displaying single course
 *
 * @package Tutor\Templates
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.0.0
 */

$course_id     = get_the_ID();
$course_rating = tutor_utils()->get_course_rating( $course_id );
$is_enrolled   = tutor_utils()->is_enrolled( $course_id, get_current_user_id() );

// Prepare the nav items.
$course_nav_item = apply_filters( 'tutor_course/single/nav_items', tutor_utils()->course_nav_items(), $course_id );
$is_public       = \TUTOR\Course_List::is_public( $course_id );
$is_mobile       = wp_is_mobile();

$enrollment_box_position = tutor_utils()->get_option( 'enrollment_box_position_in_mobile', 'bottom' );
if ( '-1' === $enrollment_box_position ) {
	$enrollment_box_position = 'bottom';
}
$student_must_login_to_view_course = tutor_utils()->get_option( 'student_must_login_to_view_course' );

tutor_utils()->tutor_custom_header();

if ( ! is_user_logged_in() && ! $is_public && $student_must_login_to_view_course ) {
	tutor_load_template( 'login' );
	tutor_utils()->tutor_custom_footer();
	return;
}
$has_video = apply_filters( 'tutor_course_has_video', tutor_utils()->has_video_in_single(), $course_id );

$header_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
if (empty($header_image)) {
    $header_image = get_theme_mod('header_bg_image');
}
$post_format = get_post_format();
$format_label = $post_format ? get_post_format_string($post_format) : __('Course', 'brainfwd');
?>
<div class="single_post-thumbnail" style="background-image: url('<?php echo esc_url($header_image); ?>');"></div>
<div class="container wide">
    <div class="single_post-thumbnail__overlay">
        <div class="single_post-thumbnail__meta">
            <span class="post-type"><?php echo esc_html($format_label); ?></span>
            <h1 class="post-title mb-0"><?php the_title(); ?></h1>
        </div>
    </div>
</div>
<div class="post-details section-padding-bottom pt-5">
	<div class="container">
		<div class="tutor-row tutor-gx-xl-5">
			<main class="tutor-col-xl-8">
				<?php $has_video ? tutor_course_video() : get_tutor_course_thumbnail(); ?>
				<?php do_action( 'tutor_course/single/before/inner-wrap' ); ?>

				<?php if ( $is_mobile && 'top' === $enrollment_box_position ) : ?>
					<div class="tutor-mt-32">
						<?php tutor_load_template( 'single.course.course-entry-box' ); ?>
					</div>
				<?php endif; ?>

				<div class="tutor-course-details-tab tutor-mt-32">
					<?php if ( is_array( $course_nav_item ) && count( $course_nav_item ) > 1 ) : ?>
						<div class="tutor-is-sticky">
							<?php tutor_load_template( 'single.course.enrolled.nav', array( 'course_nav_item' => $course_nav_item ) ); ?>
						</div>
					<?php endif; ?>
					<div class="tutor-tab tutor-pt-24">
						<?php foreach ( $course_nav_item as $key => $subpage ) : ?>
							<div id="tutor-course-details-tab-<?php echo esc_attr( $key ); ?>" class="tutor-tab-item<?php echo 'info' == $key ? ' is-active' : ''; ?>">
								<?php
									do_action( 'tutor_course/single/tab/' . $key . '/before' );

									$method = $subpage['method'];
								if ( is_string( $method ) ) {
									$method();
								} else {
									$_object = $method[0];
									$_method = $method[1];
									$_object->$_method( get_the_ID() );
								}

									do_action( 'tutor_course/single/tab/' . $key . '/after' );
								?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php do_action( 'tutor_course/single/after/inner-wrap' ); ?>
			</main>

			<aside class="tutor-col-xl-4">
				<?php $sidebar_attr = apply_filters( 'tutor_course_details_sidebar_attr', '' ); ?>
				<div class="tutor-single-course-sidebar tutor-mt-40 tutor-mt-xl-0" <?php echo esc_attr( $sidebar_attr ); ?> >
					<?php do_action( 'tutor_course/single/before/sidebar' ); ?>

					<?php if ( ( $is_mobile && 'bottom' === $enrollment_box_position ) || ! $is_mobile ) : ?>
						<?php tutor_load_template( 'single.course.course-entry-box' ); ?>
					<?php endif ?>

					<div class="tutor-single-course-sidebar-more tutor-mt-24">
						<?php tutor_course_instructors_html(); ?>
						<?php tutor_course_requirements_html(); ?>
						<?php tutor_course_tags_html(); ?>
						<?php tutor_course_target_audience_html(); ?>
					</div>

					<?php do_action( 'tutor_course/single/after/sidebar' ); ?>
				</div>
			</aside>
		</div>
	</div>
</div>


<?php
tutor_utils()->tutor_custom_footer();
