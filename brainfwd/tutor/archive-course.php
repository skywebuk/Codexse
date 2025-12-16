<?php
/**
 * Template for displaying courses
 *
 * @package Tutor\Templates
 * @subpackage CourseArchive
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.5.8
 */

use TUTOR\Input;

tutor_utils()->tutor_custom_header();

get_template_part('templates/jumbotron');
?>

<section class="single_page_area section-padding">
    <div class="container">
        <?php
        // Sanitize GET input for course filtering
        $get = isset( $_GET['course_filter'] ) ? Input::sanitize_array( $_GET ) : array(); // phpcs:ignore

        // If course_filter parameter exists, modify query accordingly
        if ( isset( $get['course_filter'] ) ) {
            $filter = ( new \Tutor\Course_Filter( false ) )->load_listing( $get, true );
            query_posts( $filter );
        }

        // Load the Tutor LMS archive course template with merged parameters
        tutor_load_template(
            'archive-course-init',
            array_merge(
                $get,
                array(
                    'course_filter'     => (bool) tutor_utils()->get_option( 'course_archive_filter', false ),
                    'supported_filters' => tutor_utils()->get_option( 'supported_course_filters', array() ),
                    'loop_content_only' => false,
                )
            )
        );
        ?>
    </div>
</section>

<?php
tutor_utils()->tutor_custom_footer();
