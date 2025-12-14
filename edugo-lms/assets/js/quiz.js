/**
 * Edugo LMS Quiz Scripts
 *
 * @package Edugo_LMS
 */

(function($) {
    'use strict';

    /**
     * Quiz module
     */
    const EdugoQuiz = {

        quizId: 0,
        timeLimit: 0,
        timeRemaining: 0,
        timerInterval: null,
        startTime: null,
        answers: {},

        /**
         * Initialize
         */
        init: function() {
            this.quizId = $('#edugo-quiz-form').data('quiz-id');
            this.timeLimit = $('#edugo-quiz-form').data('time-limit') * 60; // Convert to seconds
            this.startTime = new Date().toISOString();

            this.bindEvents();

            if (this.timeLimit > 0) {
                this.startTimer();
            }
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            // Answer selection
            $(document).on('change', '.edugo-question-option input', this.handleAnswerChange.bind(this));

            // Navigation
            $(document).on('click', '.edugo-quiz-prev', this.prevQuestion.bind(this));
            $(document).on('click', '.edugo-quiz-next', this.nextQuestion.bind(this));

            // Submit quiz
            $(document).on('submit', '#edugo-quiz-form', this.handleSubmit.bind(this));

            // Question navigation
            $(document).on('click', '.edugo-question-nav-item', this.jumpToQuestion.bind(this));

            // Confirm before leaving
            $(window).on('beforeunload', function(e) {
                if (EdugoQuiz.hasUnsavedAnswers()) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        },

        /**
         * Handle answer change
         */
        handleAnswerChange: function(e) {
            const $input = $(e.target);
            const questionId = $input.closest('.edugo-question').data('question-id');
            const type = $input.attr('type');

            if (type === 'radio') {
                this.answers[questionId] = $input.val();
            } else if (type === 'checkbox') {
                if (!this.answers[questionId]) {
                    this.answers[questionId] = [];
                }

                const value = $input.val();
                const index = this.answers[questionId].indexOf(value);

                if ($input.is(':checked')) {
                    if (index === -1) {
                        this.answers[questionId].push(value);
                    }
                } else {
                    if (index > -1) {
                        this.answers[questionId].splice(index, 1);
                    }
                }
            } else {
                this.answers[questionId] = $input.val();
            }

            // Update navigation
            this.updateQuestionNav(questionId, true);
        },

        /**
         * Start timer
         */
        startTimer: function() {
            this.timeRemaining = this.timeLimit;
            this.updateTimerDisplay();

            this.timerInterval = setInterval(function() {
                EdugoQuiz.timeRemaining--;
                EdugoQuiz.updateTimerDisplay();

                // Warning at 5 minutes
                if (EdugoQuiz.timeRemaining === 300) {
                    EdugoQuiz.showTimerWarning();
                }

                // Auto-submit at 0
                if (EdugoQuiz.timeRemaining <= 0) {
                    clearInterval(EdugoQuiz.timerInterval);
                    EdugoQuiz.autoSubmit();
                }
            }, 1000);
        },

        /**
         * Update timer display
         */
        updateTimerDisplay: function() {
            const minutes = Math.floor(this.timeRemaining / 60);
            const seconds = this.timeRemaining % 60;
            const display = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            $('.edugo-quiz-timer-value').text(display);

            // Add warning class
            if (this.timeRemaining <= 300) {
                $('.edugo-quiz-timer').addClass('warning');
            }

            if (this.timeRemaining <= 60) {
                $('.edugo-quiz-timer').addClass('danger');
            }
        },

        /**
         * Show timer warning
         */
        showTimerWarning: function() {
            if (typeof edugoFrontend !== 'undefined') {
                alert(edugoFrontend.i18n.timeWarning);
            }
        },

        /**
         * Auto-submit on time expiry
         */
        autoSubmit: function() {
            $('#edugo-quiz-form').submit();
        },

        /**
         * Previous question
         */
        prevQuestion: function(e) {
            e.preventDefault();

            const $current = $('.edugo-question.active');
            const $prev = $current.prev('.edugo-question');

            if ($prev.length) {
                $current.removeClass('active');
                $prev.addClass('active');
                this.updateNavigation();
            }
        },

        /**
         * Next question
         */
        nextQuestion: function(e) {
            e.preventDefault();

            const $current = $('.edugo-question.active');
            const $next = $current.next('.edugo-question');

            if ($next.length) {
                $current.removeClass('active');
                $next.addClass('active');
                this.updateNavigation();
            }
        },

        /**
         * Jump to specific question
         */
        jumpToQuestion: function(e) {
            e.preventDefault();

            const $item = $(e.currentTarget);
            const index = $item.data('index');

            $('.edugo-question').removeClass('active');
            $('.edugo-question').eq(index).addClass('active');
            this.updateNavigation();
        },

        /**
         * Update navigation buttons
         */
        updateNavigation: function() {
            const $current = $('.edugo-question.active');
            const $prev = $current.prev('.edugo-question');
            const $next = $current.next('.edugo-question');

            $('.edugo-quiz-prev').prop('disabled', !$prev.length);
            $('.edugo-quiz-next').toggle($next.length > 0);
            $('.edugo-quiz-submit').toggle($next.length === 0);

            // Update question nav
            const currentIndex = $('.edugo-question').index($current);
            $('.edugo-question-nav-item').removeClass('active');
            $('.edugo-question-nav-item').eq(currentIndex).addClass('active');

            // Update progress
            const total = $('.edugo-question').length;
            const progress = ((currentIndex + 1) / total) * 100;
            $('.edugo-quiz-progress-fill').css('width', progress + '%');
            $('.edugo-quiz-progress-text').text(`Question ${currentIndex + 1} of ${total}`);
        },

        /**
         * Update question nav answered state
         */
        updateQuestionNav: function(questionId, answered) {
            const $question = $(`.edugo-question[data-question-id="${questionId}"]`);
            const index = $('.edugo-question').index($question);

            if (answered) {
                $('.edugo-question-nav-item').eq(index).addClass('answered');
            } else {
                $('.edugo-question-nav-item').eq(index).removeClass('answered');
            }
        },

        /**
         * Handle form submit
         */
        handleSubmit: function(e) {
            e.preventDefault();

            // Confirm submission
            if (!confirm(edugoFrontend.i18n.confirmSubmit)) {
                return false;
            }

            const $form = $(e.target);
            const $submitBtn = $form.find('.edugo-quiz-submit');

            // Stop timer
            if (this.timerInterval) {
                clearInterval(this.timerInterval);
            }

            // Calculate time taken
            const timeTaken = this.timeLimit > 0
                ? this.timeLimit - this.timeRemaining
                : Math.floor((new Date() - new Date(this.startTime)) / 1000);

            $submitBtn.prop('disabled', true).text(edugoFrontend.i18n.loading);

            $.ajax({
                url: edugoFrontend.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'edugo_submit_quiz',
                    nonce: edugoFrontend.nonce,
                    quiz_id: this.quizId,
                    answers: this.answers,
                    started_at: this.startTime,
                    time_taken: timeTaken
                },
                success: function(response) {
                    if (response.success) {
                        EdugoQuiz.showResults(response.data);
                    } else {
                        alert(response.data.message || edugoFrontend.i18n.error);
                        $submitBtn.prop('disabled', false).text(edugoFrontend.i18n.submitQuiz);
                    }
                },
                error: function() {
                    alert(edugoFrontend.i18n.error);
                    $submitBtn.prop('disabled', false).text(edugoFrontend.i18n.submitQuiz);
                }
            });

            return false;
        },

        /**
         * Show quiz results
         */
        showResults: function(data) {
            const i18n = edugoFrontend.i18n;
            const $results = $(`
                <div class="edugo-quiz-results">
                    <div class="edugo-quiz-results-header ${data.passed ? 'passed' : 'failed'}">
                        <div class="edugo-results-icon">
                            ${data.passed ? '&#10003;' : '&#10007;'}
                        </div>
                        <h2>${data.passed ? i18n.congratulations : i18n.keepLearning}</h2>
                        <p>${data.message}</p>
                    </div>

                    <div class="edugo-quiz-results-body">
                        <div class="edugo-results-stat">
                            <span class="edugo-results-label">${i18n.yourScore}</span>
                            <span class="edugo-results-value">${data.score}%</span>
                        </div>
                        <div class="edugo-results-stat">
                            <span class="edugo-results-label">${i18n.correctAnswers}</span>
                            <span class="edugo-results-value">${data.correct_answers} / ${data.total_questions}</span>
                        </div>
                        <div class="edugo-results-stat">
                            <span class="edugo-results-label">${i18n.passingGrade}</span>
                            <span class="edugo-results-value">${data.passing_grade}%</span>
                        </div>
                    </div>

                    <div class="edugo-quiz-results-footer">
                        <a href="${window.location.href}" class="edugo-button">${i18n.viewDetails}</a>
                    </div>
                </div>
            `);

            $('#edugo-quiz-form').replaceWith($results);
        },

        /**
         * Check for unsaved answers
         */
        hasUnsavedAnswers: function() {
            return Object.keys(this.answers).length > 0;
        }
    };

    // Initialize on document ready if quiz form exists
    $(document).ready(function() {
        if ($('#edugo-quiz-form').length) {
            EdugoQuiz.init();
        }
    });

})(jQuery);
