<?php
/**
 * Post Types Registration Class.
 *
 * @package Edugo_LMS\LMS
 */

namespace Edugo_LMS\LMS;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Post_Types
 *
 * Registers all custom post types and taxonomies for the LMS.
 *
 * @since 1.0.0
 */
class Post_Types {

    /**
     * Register all custom post types.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_post_types(): void {
        $this->register_course_post_type();
        $this->register_lesson_post_type();
        $this->register_quiz_post_type();
        $this->register_assignment_post_type();
        $this->register_question_post_type();
    }

    /**
     * Register all taxonomies.
     *
     * @since 1.0.0
     * @return void
     */
    public function register_taxonomies(): void {
        $this->register_course_category_taxonomy();
        $this->register_course_tag_taxonomy();
        $this->register_course_level_taxonomy();
        $this->register_question_type_taxonomy();
    }

    /**
     * Register Course post type.
     *
     * @since 1.0.0
     * @return void
     */
    private function register_course_post_type(): void {
        $labels = array(
            'name'                  => _x( 'Courses', 'Post type general name', 'edugo-lms' ),
            'singular_name'         => _x( 'Course', 'Post type singular name', 'edugo-lms' ),
            'menu_name'             => _x( 'Courses', 'Admin Menu text', 'edugo-lms' ),
            'name_admin_bar'        => _x( 'Course', 'Add New on Toolbar', 'edugo-lms' ),
            'add_new'               => __( 'Add New', 'edugo-lms' ),
            'add_new_item'          => __( 'Add New Course', 'edugo-lms' ),
            'new_item'              => __( 'New Course', 'edugo-lms' ),
            'edit_item'             => __( 'Edit Course', 'edugo-lms' ),
            'view_item'             => __( 'View Course', 'edugo-lms' ),
            'all_items'             => __( 'All Courses', 'edugo-lms' ),
            'search_items'          => __( 'Search Courses', 'edugo-lms' ),
            'parent_item_colon'     => __( 'Parent Courses:', 'edugo-lms' ),
            'not_found'             => __( 'No courses found.', 'edugo-lms' ),
            'not_found_in_trash'    => __( 'No courses found in Trash.', 'edugo-lms' ),
            'featured_image'        => _x( 'Course Thumbnail', 'Overrides the "Featured Image"', 'edugo-lms' ),
            'set_featured_image'    => _x( 'Set course thumbnail', 'Overrides "Set featured image"', 'edugo-lms' ),
            'remove_featured_image' => _x( 'Remove course thumbnail', 'Overrides "Remove featured image"', 'edugo-lms' ),
            'use_featured_image'    => _x( 'Use as course thumbnail', 'Overrides "Use as featured image"', 'edugo-lms' ),
            'archives'              => _x( 'Course archives', 'The post type archive label', 'edugo-lms' ),
            'insert_into_item'      => _x( 'Insert into course', 'Overrides "Insert into post"', 'edugo-lms' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this course', 'Overrides "Uploaded to this post"', 'edugo-lms' ),
            'filter_items_list'     => _x( 'Filter courses list', 'Screen reader text', 'edugo-lms' ),
            'items_list_navigation' => _x( 'Courses list navigation', 'Screen reader text', 'edugo-lms' ),
            'items_list'            => _x( 'Courses list', 'Screen reader text', 'edugo-lms' ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'edugo-lms',
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'course', 'with_front' => false ),
            'capability_type'     => 'post',
            'has_archive'         => 'courses',
            'hierarchical'        => false,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-welcome-learn-more',
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
            'show_in_rest'        => true,
            'rest_base'           => 'courses',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        /**
         * Filters the course post type arguments.
         *
         * @since 1.0.0
         * @param array $args Post type arguments.
         */
        $args = apply_filters( 'edugo_course_post_type_args', $args );

        register_post_type( 'edugo_course', $args );
    }

    /**
     * Register Lesson post type.
     *
     * @since 1.0.0
     * @return void
     */
    private function register_lesson_post_type(): void {
        $labels = array(
            'name'                  => _x( 'Lessons', 'Post type general name', 'edugo-lms' ),
            'singular_name'         => _x( 'Lesson', 'Post type singular name', 'edugo-lms' ),
            'menu_name'             => _x( 'Lessons', 'Admin Menu text', 'edugo-lms' ),
            'name_admin_bar'        => _x( 'Lesson', 'Add New on Toolbar', 'edugo-lms' ),
            'add_new'               => __( 'Add New', 'edugo-lms' ),
            'add_new_item'          => __( 'Add New Lesson', 'edugo-lms' ),
            'new_item'              => __( 'New Lesson', 'edugo-lms' ),
            'edit_item'             => __( 'Edit Lesson', 'edugo-lms' ),
            'view_item'             => __( 'View Lesson', 'edugo-lms' ),
            'all_items'             => __( 'All Lessons', 'edugo-lms' ),
            'search_items'          => __( 'Search Lessons', 'edugo-lms' ),
            'not_found'             => __( 'No lessons found.', 'edugo-lms' ),
            'not_found_in_trash'    => __( 'No lessons found in Trash.', 'edugo-lms' ),
            'featured_image'        => _x( 'Lesson Thumbnail', 'Overrides the "Featured Image"', 'edugo-lms' ),
            'set_featured_image'    => _x( 'Set lesson thumbnail', 'Overrides "Set featured image"', 'edugo-lms' ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'edugo-lms',
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'lesson', 'with_front' => false ),
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail' ),
            'show_in_rest'        => true,
            'rest_base'           => 'lessons',
        );

        /**
         * Filters the lesson post type arguments.
         *
         * @since 1.0.0
         * @param array $args Post type arguments.
         */
        $args = apply_filters( 'edugo_lesson_post_type_args', $args );

        register_post_type( 'edugo_lesson', $args );
    }

    /**
     * Register Quiz post type.
     *
     * @since 1.0.0
     * @return void
     */
    private function register_quiz_post_type(): void {
        $labels = array(
            'name'                  => _x( 'Quizzes', 'Post type general name', 'edugo-lms' ),
            'singular_name'         => _x( 'Quiz', 'Post type singular name', 'edugo-lms' ),
            'menu_name'             => _x( 'Quizzes', 'Admin Menu text', 'edugo-lms' ),
            'name_admin_bar'        => _x( 'Quiz', 'Add New on Toolbar', 'edugo-lms' ),
            'add_new'               => __( 'Add New', 'edugo-lms' ),
            'add_new_item'          => __( 'Add New Quiz', 'edugo-lms' ),
            'new_item'              => __( 'New Quiz', 'edugo-lms' ),
            'edit_item'             => __( 'Edit Quiz', 'edugo-lms' ),
            'view_item'             => __( 'View Quiz', 'edugo-lms' ),
            'all_items'             => __( 'All Quizzes', 'edugo-lms' ),
            'search_items'          => __( 'Search Quizzes', 'edugo-lms' ),
            'not_found'             => __( 'No quizzes found.', 'edugo-lms' ),
            'not_found_in_trash'    => __( 'No quizzes found in Trash.', 'edugo-lms' ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'edugo-lms',
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'quiz', 'with_front' => false ),
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array( 'title', 'editor', 'author' ),
            'show_in_rest'        => true,
            'rest_base'           => 'quizzes',
        );

        /**
         * Filters the quiz post type arguments.
         *
         * @since 1.0.0
         * @param array $args Post type arguments.
         */
        $args = apply_filters( 'edugo_quiz_post_type_args', $args );

        register_post_type( 'edugo_quiz', $args );
    }

    /**
     * Register Assignment post type.
     *
     * @since 1.0.0
     * @return void
     */
    private function register_assignment_post_type(): void {
        $labels = array(
            'name'                  => _x( 'Assignments', 'Post type general name', 'edugo-lms' ),
            'singular_name'         => _x( 'Assignment', 'Post type singular name', 'edugo-lms' ),
            'menu_name'             => _x( 'Assignments', 'Admin Menu text', 'edugo-lms' ),
            'name_admin_bar'        => _x( 'Assignment', 'Add New on Toolbar', 'edugo-lms' ),
            'add_new'               => __( 'Add New', 'edugo-lms' ),
            'add_new_item'          => __( 'Add New Assignment', 'edugo-lms' ),
            'new_item'              => __( 'New Assignment', 'edugo-lms' ),
            'edit_item'             => __( 'Edit Assignment', 'edugo-lms' ),
            'view_item'             => __( 'View Assignment', 'edugo-lms' ),
            'all_items'             => __( 'All Assignments', 'edugo-lms' ),
            'search_items'          => __( 'Search Assignments', 'edugo-lms' ),
            'not_found'             => __( 'No assignments found.', 'edugo-lms' ),
            'not_found_in_trash'    => __( 'No assignments found in Trash.', 'edugo-lms' ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'edugo-lms',
            'query_var'           => true,
            'rewrite'             => array( 'slug' => 'assignment', 'with_front' => false ),
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array( 'title', 'editor', 'author' ),
            'show_in_rest'        => true,
            'rest_base'           => 'assignments',
        );

        /**
         * Filters the assignment post type arguments.
         *
         * @since 1.0.0
         * @param array $args Post type arguments.
         */
        $args = apply_filters( 'edugo_assignment_post_type_args', $args );

        register_post_type( 'edugo_assignment', $args );
    }

    /**
     * Register Question post type.
     *
     * @since 1.0.0
     * @return void
     */
    private function register_question_post_type(): void {
        $labels = array(
            'name'                  => _x( 'Questions', 'Post type general name', 'edugo-lms' ),
            'singular_name'         => _x( 'Question', 'Post type singular name', 'edugo-lms' ),
            'menu_name'             => _x( 'Question Bank', 'Admin Menu text', 'edugo-lms' ),
            'add_new'               => __( 'Add New', 'edugo-lms' ),
            'add_new_item'          => __( 'Add New Question', 'edugo-lms' ),
            'new_item'              => __( 'New Question', 'edugo-lms' ),
            'edit_item'             => __( 'Edit Question', 'edugo-lms' ),
            'view_item'             => __( 'View Question', 'edugo-lms' ),
            'all_items'             => __( 'Question Bank', 'edugo-lms' ),
            'search_items'          => __( 'Search Questions', 'edugo-lms' ),
            'not_found'             => __( 'No questions found.', 'edugo-lms' ),
            'not_found_in_trash'    => __( 'No questions found in Trash.', 'edugo-lms' ),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => 'edugo-lms',
            'query_var'           => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'supports'            => array( 'title', 'editor', 'author' ),
            'show_in_rest'        => true,
            'rest_base'           => 'questions',
        );

        /**
         * Filters the question post type arguments.
         *
         * @since 1.0.0
         * @param array $args Post type arguments.
         */
        $args = apply_filters( 'edugo_question_post_type_args', $args );

        register_post_type( 'edugo_question', $args );
    }

    /**
     * Register Course Category taxonomy.
     *
     * @since 1.0.0
     * @return void
     */
    private function register_course_category_taxonomy(): void {
        $labels = array(
            'name'                       => _x( 'Course Categories', 'Taxonomy general name', 'edugo-lms' ),
            'singular_name'              => _x( 'Course Category', 'Taxonomy singular name', 'edugo-lms' ),
            'search_items'               => __( 'Search Categories', 'edugo-lms' ),
            'popular_items'              => __( 'Popular Categories', 'edugo-lms' ),
            'all_items'                  => __( 'All Categories', 'edugo-lms' ),
            'parent_item'                => __( 'Parent Category', 'edugo-lms' ),
            'parent_item_colon'          => __( 'Parent Category:', 'edugo-lms' ),
            'edit_item'                  => __( 'Edit Category', 'edugo-lms' ),
            'update_item'                => __( 'Update Category', 'edugo-lms' ),
            'add_new_item'               => __( 'Add New Category', 'edugo-lms' ),
            'new_item_name'              => __( 'New Category Name', 'edugo-lms' ),
            'separate_items_with_commas' => __( 'Separate categories with commas', 'edugo-lms' ),
            'add_or_remove_items'        => __( 'Add or remove categories', 'edugo-lms' ),
            'choose_from_most_used'      => __( 'Choose from the most used categories', 'edugo-lms' ),
            'not_found'                  => __( 'No categories found.', 'edugo-lms' ),
            'menu_name'                  => __( 'Categories', 'edugo-lms' ),
        );

        $args = array(
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'course-category' ),
            'show_in_rest'          => true,
            'rest_base'             => 'course-categories',
        );

        register_taxonomy( 'edugo_course_category', array( 'edugo_course' ), $args );
    }

    /**
     * Register Course Tag taxonomy.
     *
     * @since 1.0.0
     * @return void
     */
    private function register_course_tag_taxonomy(): void {
        $labels = array(
            'name'                       => _x( 'Course Tags', 'Taxonomy general name', 'edugo-lms' ),
            'singular_name'              => _x( 'Course Tag', 'Taxonomy singular name', 'edugo-lms' ),
            'search_items'               => __( 'Search Tags', 'edugo-lms' ),
            'popular_items'              => __( 'Popular Tags', 'edugo-lms' ),
            'all_items'                  => __( 'All Tags', 'edugo-lms' ),
            'edit_item'                  => __( 'Edit Tag', 'edugo-lms' ),
            'update_item'                => __( 'Update Tag', 'edugo-lms' ),
            'add_new_item'               => __( 'Add New Tag', 'edugo-lms' ),
            'new_item_name'              => __( 'New Tag Name', 'edugo-lms' ),
            'separate_items_with_commas' => __( 'Separate tags with commas', 'edugo-lms' ),
            'add_or_remove_items'        => __( 'Add or remove tags', 'edugo-lms' ),
            'choose_from_most_used'      => __( 'Choose from the most used tags', 'edugo-lms' ),
            'not_found'                  => __( 'No tags found.', 'edugo-lms' ),
            'menu_name'                  => __( 'Tags', 'edugo-lms' ),
        );

        $args = array(
            'hierarchical'          => false,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'course-tag' ),
            'show_in_rest'          => true,
            'rest_base'             => 'course-tags',
        );

        register_taxonomy( 'edugo_course_tag', array( 'edugo_course' ), $args );
    }

    /**
     * Register Course Level taxonomy.
     *
     * @since 1.0.0
     * @return void
     */
    private function register_course_level_taxonomy(): void {
        $labels = array(
            'name'                       => _x( 'Course Levels', 'Taxonomy general name', 'edugo-lms' ),
            'singular_name'              => _x( 'Course Level', 'Taxonomy singular name', 'edugo-lms' ),
            'search_items'               => __( 'Search Levels', 'edugo-lms' ),
            'all_items'                  => __( 'All Levels', 'edugo-lms' ),
            'edit_item'                  => __( 'Edit Level', 'edugo-lms' ),
            'update_item'                => __( 'Update Level', 'edugo-lms' ),
            'add_new_item'               => __( 'Add New Level', 'edugo-lms' ),
            'new_item_name'              => __( 'New Level Name', 'edugo-lms' ),
            'menu_name'                  => __( 'Levels', 'edugo-lms' ),
        );

        $args = array(
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'course-level' ),
            'show_in_rest'          => true,
            'rest_base'             => 'course-levels',
        );

        register_taxonomy( 'edugo_course_level', array( 'edugo_course' ), $args );

        // Insert default levels on first run.
        if ( ! get_option( 'edugo_default_levels_created' ) ) {
            $this->create_default_levels();
        }
    }

    /**
     * Register Question Type taxonomy.
     *
     * @since 1.0.0
     * @return void
     */
    private function register_question_type_taxonomy(): void {
        $labels = array(
            'name'              => _x( 'Question Types', 'Taxonomy general name', 'edugo-lms' ),
            'singular_name'     => _x( 'Question Type', 'Taxonomy singular name', 'edugo-lms' ),
            'search_items'      => __( 'Search Types', 'edugo-lms' ),
            'all_items'         => __( 'All Types', 'edugo-lms' ),
            'edit_item'         => __( 'Edit Type', 'edugo-lms' ),
            'update_item'       => __( 'Update Type', 'edugo-lms' ),
            'add_new_item'      => __( 'Add New Type', 'edugo-lms' ),
            'new_item_name'     => __( 'New Type Name', 'edugo-lms' ),
            'menu_name'         => __( 'Question Types', 'edugo-lms' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => false,
            'rewrite'           => false,
            'show_in_rest'      => true,
        );

        register_taxonomy( 'edugo_question_type', array( 'edugo_question' ), $args );
    }

    /**
     * Create default course levels.
     *
     * @since 1.0.0
     * @return void
     */
    private function create_default_levels(): void {
        $levels = array(
            'beginner'     => __( 'Beginner', 'edugo-lms' ),
            'intermediate' => __( 'Intermediate', 'edugo-lms' ),
            'advanced'     => __( 'Advanced', 'edugo-lms' ),
            'expert'       => __( 'Expert', 'edugo-lms' ),
        );

        foreach ( $levels as $slug => $name ) {
            if ( ! term_exists( $slug, 'edugo_course_level' ) ) {
                wp_insert_term( $name, 'edugo_course_level', array( 'slug' => $slug ) );
            }
        }

        update_option( 'edugo_default_levels_created', true );
    }
}
