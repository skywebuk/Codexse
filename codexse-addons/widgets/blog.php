<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Codexse_Addons_Elementor_Widget_Blog extends Widget_Base {

    // Get widget name
    public function get_name() {
        return 'codexse_blog';
    }

    // Get widget title
    public function get_title() {
        return __( 'Blog', 'codexse-addons' );
    }

    // Get widget icon
    public function get_icon() {
        return 'cx-addons-icon eicon-posts-grid';
    }

    // Get widget categories
    public function get_categories() {
        return [ 'codexse-addons' ]; // Ensure this category is registered in Elementor
    }

    // Get widget keywords
    public function get_keywords() {
        return [ 'blog','codexse','post','articles','writer','author','news','content','updates','stories','publication','diary','journal','writing','editorial','read','reading','media','headline','insights','narrative' ];
    }

    // Get style dependencies
    public function get_style_depends() {
        return [ 'codexse-blog' ]; // Enqueue your custom CSS file
    }

    // Register widget controls
    protected function _register_controls() {
        $this->blog_control_settings();
        $this->blog_item_style_settings();
        $this->blog_item_title_settings();
	}

    public function blog_control_settings(){
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Blog Settings', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        
        // Titles Control
        $this->add_control(
            'titles',
            [
                'label'       => __( 'Titles', 'codexse-addons' ),
                'type'        => Controls_Manager::SELECT2,
                'multiple'    => true,
                'options'     => $this->get_blog_titles(),
                'description' => __( 'Select titles to display posts from.', 'codexse-addons' ),
            ]
        );

        // Category Control
        $this->add_control(
            'category',
            [
                'label'       => __( 'Category', 'codexse-addons' ),
                'type'        => Controls_Manager::SELECT2,
                'multiple'    => true,
                'options'     => $this->get_blog_categories(),
                'description' => __( 'Select categories to display posts from.', 'codexse-addons' ),
            ]
        );

        // Tags Control
        $this->add_control(
            'tags',
            [
                'label'       => __( 'Tags', 'codexse-addons' ),
                'type'        => Controls_Manager::SELECT2,
                'multiple'    => true,
                'options'     => $this->get_blog_tags(),
                'description' => __( 'Select tags to filter posts.', 'codexse-addons' ),
            ]
        );

        // Number of Posts Control
        $this->add_control(
            'posts_per_page',
            [
                'label'   => __( 'Number of Posts', 'codexse-addons' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 5,
                'min'     => 1,
                'max'     => 20,
            ]
        );

        // Post Style Control
        $this->add_control(
            'post_style',
            [
                'label'   => __( 'Post Style', 'codexse-addons' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'style1',
                'options' => [
                    'style1' => esc_html__( 'Style One', 'codexse-addons' ),
                    'style2' => esc_html__( 'Style Two', 'codexse-addons' ),
                    'style3' => esc_html__( 'Style Three', 'codexse-addons' ),
                    'style4' => esc_html__( 'Style Four', 'codexse-addons' ),
                    'style5' => esc_html__( 'Style Five', 'codexse-addons' ),
                    'style6' => esc_html__( 'Style Six', 'codexse-addons' ),
                ],
            ]
        );
        
        // Titles Control
        $this->add_control(
            'grids',
            [
                'label'       => __( 'Grid', 'codexse-addons' ),
                'type'        => Controls_Manager::SELECT,
                'options'     => [
                    '1'   => __( 'One', 'codexse-addons' ),
                    '2'   => __( 'Two', 'codexse-addons' ),
                    '3' => __( 'Three', 'codexse-addons' ),
                    '4'  => __( 'Four', 'codexse-addons' ),
                    '5'  => __( 'Five', 'codexse-addons' ),
                    '6'  => __( 'Six', 'codexse-addons' ),
                    '7'  => __( 'Seven', 'codexse-addons' ),
                    '8'  => __( 'Eight', 'codexse-addons' ),
                ],
                'default' => '3',
                'description' => __( 'Choose how many items to display in a single row. This option controls the grid layout for your widget.', 'codexse-addons' ),
            ]
        );
        $this->end_controls_section();
    }

    public function blog_item_style_settings(){

        // Feature Style tab section
        $this->start_controls_section(
            'blog_style_one_section',
            [
                'label' => __( 'Blog Item', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'post_style' => 'style1',
                ]
            ]
        );
        
        $this->start_controls_tabs('blog_style_one_tabs');
        $this->start_controls_tab( 'blog_style_one_tab',
			[
				'label' => __( 'Normal', 'codexse-addons' ),
			]
		);

        $this->add_responsive_control(
            'blog_style_one_height',
            [
                'label' => __( 'Height', 'codexse-addons' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .blog-box-one' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'blog_style_one_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .blog-box-one' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'blog_style_one_background',
                'label' => __( 'Background', 'codexse-addons' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .blog-box-one .blog-overlay',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'blog_style_one_border',
                'label' => __( 'Border', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .blog-box-one',
            ]
        );

        $this->add_responsive_control(
            'blog_style_one_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .blog-box-one' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'blog_style_one_box_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .blog-box-one',
            ]
        );

        
        $this->add_control(
			'blog_style_one_box_transform',
			[
				'label' => __( 'Transform', 'codexse-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .blog-box-one' => 'transform: {{VALUE}}',
				],
			]
		);
        
		$this->add_control(
			'blog_style_one_box_transition',
			[
				'label' => __( 'Transition Duration', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .blog-box-one' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
		$this->end_controls_tab();

             
        // Hover Style tab Start
        $this->start_controls_tab(
            'blog_style_one_box_hover',
            [
                'label' => __( 'Hover', 'codexse-addons' ),
            ]
        );
              
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'blog_style_one_hover_background',
                'label' => __( 'Background', 'codexse-addons' ),
                'types' => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .blog-box-one .blog-overlay:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'blog_style_one_border_hover',
                'label' => __( 'Border', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .blog-box-one:hover',
            ]
        );
        $this->add_responsive_control(
            'blog_style_one_hover_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .blog-box-one:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'blog_style_one_box_hover_shadow',
                'label' => __( 'Box Shadow', 'codexse-addons' ),
                'selector' => '{{WRAPPER}} .blog-box-one:hover',
            ]
        );
        $this->add_control(
			'blog_style_one_box_hover_transform',
			[
				'label' => __( 'Transform', 'codexse-addons' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'translateY(0)',
				'selectors' => [
					'{{WRAPPER}} .blog-box-one:hover' => 'transform: {{VALUE}}',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end        
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section(); // Feature Box section style end

    }

    public function blog_item_title_settings(){

        // Feature Style tab section
        $this->start_controls_section(
            'blog_title_section',
            [
                'label' => __( 'Title', 'codexse-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->start_controls_tabs('blog_title_tabs');
        
        $this->start_controls_tab( 'blog_title_tab_normal',
			[
				'label' => __( 'Normal', 'codexse-addons' ),
			]
		);        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'blog_title_typography',
                'selector' => '{{WRAPPER}} .blog-box-one .blog-title a',
            ]
        );
        $this->add_control(
            'blog_title_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blog-box-one .blog-title a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'blog_title_margin',
            [
                'label' => __( 'Margin', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .blog-box-one .blog-title a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
        
        $this->add_responsive_control(
            'blog_title_padding',
            [
                'label' => __( 'Padding', 'codexse-addons' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .blog-box-one .blog-title a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' =>'before',
            ]
        );
                
		$this->add_control(
			'blog_title_transition',
			[
				'label' => __( 'Transition Duration', 'codexse-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .blog-box-one .blog-title a' => 'transition-duration: {{SIZE}}s',
				],
			]
		);
        
        $this->end_controls_tab(); // Hover Style tab end
         
        $this->start_controls_tab( 'blog_title_hover_tab',
			[
				'label' => __( 'Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'blog_title_hover_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blog-box-one .blog-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->start_controls_tab( 'blog_hover_title_tab',
			[
				'label' => __( 'Box Hover', 'codexse-addons' ),
			]
		);        
        
        $this->add_control(
            'blog_hover_title_color',
            [
                'label' => __( 'Color', 'codexse-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .blog-box-one:hover .blog-title a' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_tab(); // Hover Style tab end
        $this->end_controls_tabs();// Box Style tabs end  
        $this->end_controls_section();
    }

    
    protected function render() {
        $settings = $this->get_settings_for_display();

        // Get selected posts, categories, and tags
        $titles = isset( $settings['titles'] ) ? $settings['titles'] : [];
        $categories = isset( $settings['category'] ) ? $settings['category'] : [];
        $tags = isset( $settings['tags'] ) ? $settings['tags'] : [];
        $posts_per_page = isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : 5;
        $post_style = isset( $settings['post_style'] ) ? $settings['post_style'] : 'style1';

        switch ($settings['grids']) {
            case "1":
                $column = "row-cols-1";
                break;
            case "2":
                $column = "row-cols-1 row-cols-sm-2";
                break;
            case "4":
                $column = "row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4";
                break;
            case "5":
                $column = "row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5";
                break;
            case "6":
                $column = "row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6";
                break;
            case "7":
                $column = "row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-7";
                break;
            case "8":
                $column = "row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-8";
                break;
            default:
                $column = "row-cols-1 row-cols-sm-2 row-cols-md-3";
        }

        // Query parameters for posts
        $args = [
            'post_type'      => 'post', // Can be customized to allow other post types
            'posts_per_page' => $posts_per_page,
            'post_status'    => 'publish',
        ];

        if ( ! empty( $titles ) ) {
            $args['post__in'] = $titles; // Filter by selected post titles
        }

        if ( ! empty( $categories ) ) {
            $args['category__in'] = $categories; // Filter by selected categories
        }

        if ( ! empty( $tags ) ) {
            $args['tag__in'] = $tags; // Filter by selected tags
        }

        // Get the posts based on the query arguments
        $query = new WP_Query( $args );

        $count = 1;

        // Check if there are any posts to display
        if ( $query->have_posts() ) {
            echo '<div class="blog-posts">';
            echo '<div class="row g-4 '.esc_attr($column).' ">';
            while ( $query->have_posts() ) {
                $query->the_post();
                
                echo '<div class="col">';
                    // Render posts based on selected post style
                    if ( $post_style === 'style1' ) {
                        echo $this->render_blog_content_one(get_the_ID());
                    } elseif ( $post_style === 'style2' ) {
                        echo $this->render_blog_content_two(get_the_ID());
                    }elseif ( $post_style === 'style3' ) {
                        echo $this->render_blog_content_three(get_the_ID());
                    }elseif ( $post_style === 'style4' ) {
                        echo $this->render_blog_content_four(get_the_ID());
                    }elseif ( $post_style === 'style5' ) {
                        echo $this->render_blog_content_five(get_the_ID(), $count);
                        $count++;
                    } elseif ( $post_style === 'style6' ) {
                        echo $this->render_blog_content_six(get_the_ID());
                    }

                echo '</div>';

            }
            echo '</div>';
            echo '</div>';
        } else {
            echo '<p>' . __( 'No posts found.', 'codexse-addons' ) . '</p>';
        }

        // Reset post data
        wp_reset_postdata();
    }

    public function get_blog_categories() {
        $categories = get_categories( array( 'hide_empty' => false ) );
        $category_options = [];
        foreach ( $categories as $category ) {
            $category_options[ $category->term_id ] = $category->name;
        }
        return $category_options;
    }

    public function get_blog_tags() {
        $tags = get_tags( array( 'hide_empty' => false ) );
        $tag_options = [];
        foreach ( $tags as $tag ) {
            $tag_options[ $tag->term_id ] = $tag->name;
        }
        return $tag_options;
    }

    public function get_blog_titles() {
        $posts_query = new WP_Query( [
            'post_type'      => 'post',
            'posts_per_page' => -1, // Get all posts
            'post_status'    => 'publish',
        ] );

        $titles = [];
        while ( $posts_query->have_posts() ) {
            $posts_query->the_post();
            $titles[get_the_ID()] = get_the_title();
        }
        wp_reset_postdata();

        return $titles;
    }
    
    public function render_blog_content_one( $post_id ) {
        // Fetch post details
        $post = get_post( $post_id );
        
        // If post is not found, return nothing
        if ( ! $post ) {
            return '';
        }
    
        // Get post categories
        $categories = get_the_category( $post_id );
        $category_name = ! empty( $categories ) ? $categories[0]->name : 'No category';
        $category_link = ! empty( $categories ) ? get_category_link( $categories[0]->term_id ) : '#';
    
        // Get post author
        $author_name = get_the_author_meta( 'display_name', $post->post_author );
        $author_link = get_author_posts_url( $post->post_author );
    
        // Get post date
        $post_date = get_the_date( 'F j, Y', $post_id );
    
        // Get feature image URL
        $feature_image_url = get_the_post_thumbnail_url( $post_id, 'large' );
    
        // Construct the blog box content with background image
        $blog_content = '
        <div class="blog-box blog-box-one">
            <div class="blog-thumbnail" style="background-image: url(' . esc_url( $feature_image_url ) . '); "></div>
            <a href="' . esc_url( get_permalink( $post_id ) ) . '" class="blog-overlay"></a>
            <div class="blog-content">
                <a href="' . esc_url( $category_link ) . '" class="blog-category">' . esc_html( $category_name ) . '</a>
                <h3 class="blog-title">
                    <a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( $post->post_title ) . '</a>
                </h3>
                <ul class="blog-meta">
                    <li><i class="ri-user-line"></i> <a href="' . esc_url( $author_link ) . '" class="blog-author">' . esc_html( $author_name ) . '</a></li>
                    <li><i class="ri-time-line"></i> ' . esc_html( $post_date ) . '</li>
                </ul>
            </div>
        </div>';
    
        return $blog_content;
    }    
    
    public function render_blog_content_two( $post_id ) {
        // Fetch post details
        $post = get_post( $post_id );
        
        // If post is not found, return nothing
        if ( ! $post ) {
            return '';
        }
    
        // Get post categories
        $categories = get_the_category( $post_id );
        $category_name = ! empty( $categories ) ? $categories[0]->name : 'No category';
        $category_link = ! empty( $categories ) ? get_category_link( $categories[0]->term_id ) : '#';
    
        // Get post author
        $author_name = get_the_author_meta( 'display_name', $post->post_author );
        $author_link = get_author_posts_url( $post->post_author );
    
        // Get post date
        $post_date = get_the_date( 'F j, Y', $post_id );
    
        // Get post excerpt
        $post_excerpt = wp_trim_words( $post->post_content, 22, '&nbsp;[...]' );
    
        // Construct the blog box content
        $blog_content = '
        <div class="blog-box blog-box-two">
            <figure class="blog-image"><a href="' . esc_url( get_permalink( $post_id ) ) . '" >' . get_the_post_thumbnail( $post_id, 'large' ) . '</a>
            <a href="' . esc_url( $category_link ) . '" class="blog-category">' . esc_html( $category_name ) . '</a></figure>
            <div class="blog-content">
                <h3 class="blog-title"><a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( $post->post_title ) . '</a></h3>
                <ul class="blog-meta">
                    <li><i class="ri-user-line"></i> By <a href="' . esc_url( $author_link ) . '" class="blog-author">' . esc_html( $author_name ) . '</a></li>
                    <li><i class="ri-time-line"></i> ' . esc_html( $post_date ) . '</li>
                </ul>
                <div class="blog-text">' . esc_html( $post_excerpt ) . '</div>
            </div>
        </div>';
    
        return $blog_content;
    }

    public function render_blog_content_three( $post_id ) {
        // Fetch post details
        $post = get_post( $post_id );
        // If post is not found, return nothing
        if ( ! $post ) {
            return '';
        }
        // Get post categories
        $categories = get_the_category( $post_id );
        $category_name = ! empty( $categories ) ? $categories[0]->name : 'No category';
        $category_link = ! empty( $categories ) ? get_category_link( $categories[0]->term_id ) : '#';
        // Get post author
        $author_name = get_the_author_meta( 'display_name', $post->post_author );
        $author_link = get_author_posts_url( $post->post_author );
        // Get post date
        $post_date = get_the_date( 'F j, Y', $post_id );
        // Get post excerpt
        $post_excerpt = wp_trim_words( $post->post_content, 22, ' &nbsp;[...]' );

        // Get feature image URL
        $feature_image_url = get_the_post_thumbnail_url( $post_id, 'large' );

        // Construct the blog box content
        $blog_content = '
        <div class="blog-box blog-box-three">
            <a href="' . esc_url( get_permalink( $post_id ) ) . '" class="blog-image" style="background-image: url(' . esc_url( $feature_image_url ) . '); " ></a>
            <div class="blog-content">
                <h3 class="blog-title"><a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( $post->post_title ) . '</a></h3>
                <ul class="blog-meta">
                    <li><i class="ri-user-line"></i> By <a href="' . esc_url( $author_link ) . '" class="blog-author">' . esc_html( $author_name ) . '</a></li>
                    <li><i class="ri-time-line"></i> ' . esc_html( $post_date ) . '</li>
                </ul>
            </div>
        </div>';
        return $blog_content;
    }
    
    public function render_blog_content_four( $post_id ) {
        // Fetch post details
        $post = get_post( $post_id );
        
        // If post is not found, return nothing
        if ( ! $post ) {
            return '';
        }
    
        // Get post categories
        $categories = get_the_category( $post_id );
        $category_name = ! empty( $categories ) ? $categories[0]->name : 'No category';
        $category_link = ! empty( $categories ) ? get_category_link( $categories[0]->term_id ) : '#';
    
        // Get post author
        $author_name = get_the_author_meta( 'display_name', $post->post_author );
        $author_link = get_author_posts_url( $post->post_author );
    
        // Get post date
        $post_date = get_the_date( 'F j, Y', $post_id );
    
        // Get post excerpt
        $post_excerpt = wp_trim_words( $post->post_content, 22, '&nbsp;[...]' );
    
        // Construct the blog box content
        $blog_content = '
        <div class="blog-box blog-box-four">
            <figure class="blog-image"><a href="' . esc_url( get_permalink( $post_id ) ) . '" >' . get_the_post_thumbnail( $post_id, 'large' ) . '</a></figure>
            <div class="blog-content">
                <h3 class="blog-title"><a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( $post->post_title ) . '</a></h3>
                <ul class="blog-meta">
                    <li><i class="ri-time-line"></i> ' . esc_html( $post_date ) . '</li>
                </ul>
            </div>
        </div>';
    
        return $blog_content;
    }

    public function render_blog_content_five( $post_id, $count ) {
        // Validate the post ID
        $post_id = intval( $post_id );
    
        // Fetch post details
        $post = get_post( $post_id );
    
        // If post is not found, return nothing
        if ( ! $post ) {
            return '';
        }
    
        // Get post categories
        $categories = get_the_category( $post_id );
        $category_name = ! empty( $categories ) ? esc_html( $categories[0]->name ) : esc_html__( 'No category', 'your-textdomain' );
        $category_link = ! empty( $categories ) ? esc_url( get_category_link( $categories[0]->term_id ) ) : '#';
    
        // Get post author
        $author_name = esc_html( get_the_author_meta( 'display_name', $post->post_author ) );
        $author_link = esc_url( get_author_posts_url( $post->post_author ) );
    
        // Get post date
        $post_date = esc_html( get_the_date( 'F j, Y', $post_id ) );
    
        // Get post excerpt
        $post_excerpt = esc_html( wp_trim_words( $post->post_content, 22, '&nbsp;[...]' ) );
    
        // Get post thumbnail or fallback
        $post_thumbnail = get_the_post_thumbnail( $post_id, 'large' );
        if ( empty( $post_thumbnail ) ) {
            $post_thumbnail = '<img src="' . esc_url( get_template_directory_uri() . '/assets/images/default-thumbnail.jpg' ) . '" alt="' . esc_attr( $post->post_title ) . '">';
        }
    
        // Construct the blog box content
        $blog_content = '
        <div class="blog-box blog-box-five">
            <figure class="blog-image">
                <span class="count">' . esc_html( $count ) . '</span>
                <a href="' . esc_url( get_permalink( $post_id ) ) . '">' . $post_thumbnail . '</a>
            </figure>
            <div class="blog-content">
                <h3 class="blog-title">
                    <a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( $post->post_title ) . '</a>
                </h3>
                <ul class="blog-meta">
                    <li><i class="ri-user-line"></i> By <a href="' . esc_url( $author_link ) . '" class="blog-author">' . esc_html( $author_name ) . '</a></li>
                    <li><i class="ri-time-line"></i> ' . esc_html( $post_date ) . '</li>
                </ul>
            </div>
        </div>';
    
        return $blog_content;
    }


    public function render_blog_content_six( $post_id ) {
        // Fetch post details
        $post = get_post( $post_id );
        
        // If post is not found, return nothing
        if ( ! $post ) {
            return '';
        }
    
        // Get post categories
        $categories = get_the_category( $post_id );
        $category_name = ! empty( $categories ) ? $categories[0]->name : 'No category';
        $category_link = ! empty( $categories ) ? get_category_link( $categories[0]->term_id ) : '#';
    
        // Get post author
        $author_name = get_the_author_meta( 'display_name', $post->post_author );
        $author_link = get_author_posts_url( $post->post_author );
    
        // Get post date
        $post_date = get_the_date( 'F j, Y', $post_id );
    
        // Get post excerpt
        $post_excerpt = wp_trim_words( $post->post_content, 22, '&nbsp;[...]' );
    
        // Construct the blog box content
        $blog_content = '
        <div class="blog-box blog-box-six">
            <figure class="blog-image"><a href="' . esc_url( get_permalink( $post_id ) ) . '" >' . get_the_post_thumbnail( $post_id, 'large' ) . '</a></figure>
            <div class="blog-content">
                <h3 class="blog-title"><a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( $post->post_title ) . '</a></h3>
                <ul class="blog-meta">
                    <li><i class="ri-user-line"></i> By <a href="' . esc_url( $author_link ) . '" class="blog-author">' . esc_html( $author_name ) . '</a></li>
                    <li><i class="ri-time-line"></i> ' . esc_html( $post_date ) . '</li>
                </ul>
                <div class="blog-text">' . esc_html( $post_excerpt ) . '</div>
            </div>
        </div>';
    
        return $blog_content;
    }
    

    
}
