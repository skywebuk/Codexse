/**
 * Edugo LMS Frontend Scripts
 *
 * @package Edugo_LMS
 */

(function($) {
    'use strict';

    /**
     * Frontend module
     */
    const EdugoFrontend = {

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initProgressTracking();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Mark lesson complete
            $(document).on('click', '.edugo-complete-lesson', this.markLessonComplete);

            // Enroll in course
            $(document).on('click', '.edugo-enroll-btn', this.enrollInCourse);

            // Course tabs
            $(document).on('click', '.edugo-tab-link', this.handleTabs);

            // Accordion
            $(document).on('click', '.edugo-accordion-header', this.toggleAccordion);

            // Rating stars
            $(document).on('click', '.edugo-rating-input', this.handleRating);

            // Filter courses
            $(document).on('change', '.edugo-course-filter', this.filterCourses);
        },

        /**
         * Mark lesson as complete
         */
        markLessonComplete: function(e) {
            e.preventDefault();

            const $button = $(this);
            const lessonId = $button.data('lesson-id');

            if ($button.hasClass('loading')) {
                return;
            }

            $button.addClass('loading').text(edugoFrontend.i18n.loading);

            $.ajax({
                url: edugoFrontend.restUrl + 'progress/lesson/' + lessonId + '/complete',
                type: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', edugoFrontend.restNonce);
                },
                success: function(response) {
                    if (response.success) {
                        $button
                            .removeClass('loading')
                            .addClass('completed')
                            .text(edugoFrontend.i18n.lessonCompleted);

                        // Update progress bar
                        if (response.course_progress) {
                            EdugoFrontend.updateProgressBar(response.course_progress.percentage);
                        }

                        // Show next lesson if available
                        EdugoFrontend.showNextLesson();
                    }
                },
                error: function(xhr) {
                    $button.removeClass('loading').text('Mark Complete');
                    EdugoFrontend.showNotice('error', xhr.responseJSON?.message || edugoFrontend.i18n.error);
                }
            });
        },

        /**
         * Enroll in free course
         */
        enrollInCourse: function(e) {
            e.preventDefault();

            const $button = $(this);
            const courseId = $button.data('course-id');

            if ($button.hasClass('loading')) {
                return;
            }

            $button.addClass('loading').text(edugoFrontend.i18n.loading);

            $.ajax({
                url: edugoFrontend.restUrl + 'enroll',
                type: 'POST',
                data: JSON.stringify({ course_id: courseId }),
                contentType: 'application/json',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', edugoFrontend.restNonce);
                },
                success: function(response) {
                    if (response.success) {
                        $button
                            .removeClass('loading edugo-enroll-btn')
                            .addClass('edugo-start-course')
                            .text('Start Course')
                            .attr('href', window.location.href);

                        EdugoFrontend.showNotice('success', response.message);

                        // Reload page after short delay
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    $button.removeClass('loading').text('Enroll Now');
                    EdugoFrontend.showNotice('error', xhr.responseJSON?.message || edugoFrontend.i18n.error);
                }
            });
        },

        /**
         * Handle tab switching
         */
        handleTabs: function(e) {
            e.preventDefault();

            const $link = $(this);
            const target = $link.attr('href');

            // Update active states
            $link.closest('.edugo-tabs-nav').find('.edugo-tab-link').removeClass('active');
            $link.addClass('active');

            // Show target panel
            $(target).siblings('.edugo-tab-panel').removeClass('active');
            $(target).addClass('active');
        },

        /**
         * Toggle accordion
         */
        toggleAccordion: function(e) {
            e.preventDefault();

            const $header = $(this);
            const $item = $header.closest('.edugo-accordion-item');
            const $content = $item.find('.edugo-accordion-content');

            $item.toggleClass('active');
            $content.slideToggle(200);
        },

        /**
         * Handle rating selection
         */
        handleRating: function() {
            const $input = $(this);
            const value = $input.val();

            $input.closest('.edugo-rating-stars')
                .find('.edugo-rating-input')
                .removeClass('selected');

            $input.addClass('selected')
                .prevAll('.edugo-rating-input')
                .addClass('selected');
        },

        /**
         * Filter courses
         */
        filterCourses: function() {
            const category = $('.edugo-filter-category').val();
            const level = $('.edugo-filter-level').val();
            const sort = $('.edugo-filter-sort').val();

            const params = new URLSearchParams(window.location.search);

            if (category) params.set('category', category);
            else params.delete('category');

            if (level) params.set('level', level);
            else params.delete('level');

            if (sort) params.set('sort', sort);
            else params.delete('sort');

            window.location.search = params.toString();
        },

        /**
         * Initialize progress tracking
         */
        initProgressTracking: function() {
            if (!$('.edugo-lesson-content').length) {
                return;
            }

            let timeSpent = 0;
            const lessonId = $('.edugo-lesson-content').data('lesson-id');

            // Track time every 30 seconds
            setInterval(function() {
                timeSpent += 30;

                // Save progress every 2 minutes
                if (timeSpent % 120 === 0) {
                    EdugoFrontend.saveTimeSpent(lessonId, 120);
                }
            }, 30000);

            // Save on page unload
            $(window).on('beforeunload', function() {
                if (timeSpent > 0) {
                    EdugoFrontend.saveTimeSpent(lessonId, timeSpent % 120);
                }
            });
        },

        /**
         * Save time spent on lesson
         */
        saveTimeSpent: function(lessonId, seconds) {
            navigator.sendBeacon(
                edugoFrontend.ajaxUrl,
                new URLSearchParams({
                    action: 'edugo_save_time_spent',
                    nonce: edugoFrontend.nonce,
                    lesson_id: lessonId,
                    seconds: seconds
                })
            );
        },

        /**
         * Update progress bar
         */
        updateProgressBar: function(percentage) {
            $('.edugo-course-progress .edugo-progress-fill').css('width', percentage + '%');
            $('.edugo-course-progress .edugo-progress-text').text(percentage + '% Complete');
        },

        /**
         * Show next lesson
         */
        showNextLesson: function() {
            const $nextLesson = $('.edugo-lesson-item.current').next('.edugo-lesson-item');

            if ($nextLesson.length) {
                // Highlight next lesson
                $nextLesson.addClass('next-up');

                // Show next button
                $('.edugo-next-lesson').show();
            }
        },

        /**
         * Show notice
         */
        showNotice: function(type, message) {
            const $notice = $(`
                <div class="edugo-notice edugo-notice-${type}">
                    <p>${message}</p>
                </div>
            `);

            $('body').append($notice);

            setTimeout(function() {
                $notice.addClass('show');
            }, 100);

            setTimeout(function() {
                $notice.removeClass('show');
                setTimeout(function() {
                    $notice.remove();
                }, 300);
            }, 4000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        EdugoFrontend.init();
    });

})(jQuery);
