const { __, _x, _n, _nx } = wp.i18n;

(function($){

    "use strict";

    let score = 0;
    let all_questions_count = 0;
    let unanswered_questions_count = 0;
    let og_data = {};
    
    $('.qy-o-answer-btn').hover(
        function() { // mouseenter
            let this_answer_number = parseInt($(this).closest('.qy-o-answer').attr('data-answer-number'));
            let answers_list = $(this).closest('.answers-list');
            answers_list.find('.qy-o-answer').each(function() {
                let answer_number = parseInt($(this).attr('data-answer-number'));
                if (answer_number != this_answer_number) {
                    $(this).css('opacity', '0.55');
                }
            });
        }, 
        function() { //mouseleave
            let hovered_answer = $(this);
            hovered_answer.closest('.qy-o-answers-list').find('.qy-o-answer').each(function() {
                $(this).css('opacity', '1');
            });
        }
    );
    
    $('.qy-o-answer-btn').click(function(e) {
        e.preventDefault();

        let question = $(this).closest('.qy-o-question');
        let container = question.closest('.qy-o-quiz-container');
        
        let weighted = container.attr('data-weighted');
        weighted = (weighted == '1') ? true : false;

        let explanations_enabled = container.attr('data-explanations-enabled');
        explanations_enabled = (explanations_enabled == '1') ? true : false;

        all_questions_count = container.find('.qy-o-question').length;

        if (unanswered_questions_count === 0) {
            unanswered_questions_count = all_questions_count;
        }

        let last_question_number = parseInt(container.attr('data-last-question'));
        let question_number = parseInt(question.attr('data-question-number'));
        let answer = $(this).closest('.qy-o-answer');
        let weight = parseInt(answer.attr('data-w'));
        let is_correct = false;
        let correct_answer_text = '';
        
        let is_last_question = (last_question_number == question_number) ? true : false;

        if (!weighted) {
            if ( weight === 1 ) {
                answer.addClass('qy-o-answer-correct');
                is_correct = true;
            } else {
                answer.addClass('qy-o-answer-wrong');
            }
        } else {
            answer.addClass('qy-o-answer-weighted');
        }
 
        let question_title = question.find('.qy-o-quiz-title');
        let thumbnail = question.find('.qy-o-question-thumbnail');
        let answers_list = question.find('.qy-o-answers-list');

        // Assemble OG data
        if (jQuery.isEmptyObject(og_data)) {
            og_data['title'] = container.find('.qy-o-quiz-title').text();
            og_data['desc'] = container.find('.qy-o-description').text();
            og_data['thumbnail'] = container.find('.qy-o-quiz-thumbnail').attr('src');
        }

        // Get autoscroll setting
        let autoscroll = container.attr('data-autoscroll');
        autoscroll = (autoscroll == '1') ? true : false;

        let explanation;
        let explanation_inside;
        let explanation_title = null;
        let correct_answer_el;

        if (!weighted) {
            explanation = question.find('.qy-o-question-explanation');
            explanation_inside = explanation.find('.qy-o-question-explanation-inside');
            explanation_title = explanation.find('.qy-o-explanation-title');
            correct_answer_el = explanation.find('.qy-o-correct-answer');
        }
        
        // Dim the already answered question
        question_title.css('opacity', '0.4');
        thumbnail.css('opacity', '0.4');
        answers_list.css('opacity', '0.6');

        if (!weighted) {
            question.find('.qy-o-answer').each(function() {
                let weight = parseInt($(this).attr('data-w'));
                if (weight === 1) {
                    correct_answer_text = $(this).find('.qy-o-answer-caption').html();
                }
            })
        }

        if (!weighted) {
            if (is_correct) {
                explanation_inside.addClass('qy-o-correct');
                explanation_title.html(__('¡Correcto!', 'quizly')).addClass('qy-o-correct');
                score += weight;
            } else {
                explanation_inside.addClass('qy-o-wrong');
                explanation_title.html(__('¡Incorrecto!', 'quizly')).addClass('qy-o-wrong');
            }
            correct_answer_el.html('<span class="dashicons dashicons-yes"></span>' + correct_answer_text);
        } else {
            score += weight;
        }

        answers_list.each(function() {
            $(this).find('.qy-o-answer-btn').unbind('click');
        });

        unanswered_questions_count -= 1;

        // Show the explanation
        if (!weighted) {
            explanation.show();
        }

        // Is this the last question ("last" = "at the bottom" in this case)?
        if (is_last_question) {
            if (unanswered_questions_count > 0) {
                setTimeout(() => {
                    alert(__('It seems there are some unanswered questions. You need to take care of them in order to see the final result.', 'quizly'));
                }, 300);
                
            } else {
                setTimeout(() => {
                    qy_o_end_quiz(300, weighted, explanation_title, container);
                }, 1000);
                
            }
        } else if (unanswered_questions_count === 0) {
            qy_o_end_quiz(300, weighted, explanation_title, container);
        } else {
            if (!weighted) {
                qy_o_scroll_to_next(explanation_title, autoscroll, 200);
            } else { 
                qy_o_scroll_to_next(question.next(), autoscroll, 400, 200);
            }
        }     
    });

    // Facebook share
    $('.qy-o-share-facebook').click(function(e) {
        let url_to_share = $(this).attr('data-url');
        let quiz_title = $(this).closest('.qy-o-quiz-container').attr('data-title');
        FB.ui({
            method: 'share',
            href: url_to_share,
            quote: quiz_title
        }, function(response){});
    });

    // Twitter share
    $('.qy-o-share-twitter').click(function(e) {
        let url_to_share = $(this).attr('data-url');
        let quiz_title = $(this).closest('.qy-o-quiz-container').attr('data-title');
        quiz_title = quiz_title.replace(' ', '%20');
        let url = 'https://twitter.com/intent/tweet?text=' + quiz_title + '%0A' + url_to_share;
        window.open(url, '', 'width=640,height=480');
    });

    // Copy Link
    $('.qy-o-share-link').on('click', function(e) {
        let current_url = $(location).attr("href");
        $('body').append('<input type="text" id="qy-o-copy-url" />');
        let temp_input = $('#qy-o-copy-url');
        temp_input.val(current_url);
        temp_input.select();
        document.execCommand('copy');
        temp_input.remove();
        $(this).find('.qy-o-share-link-text').text(__('Copied!', 'quizly'));
    });

    /**
     * * * * FUNCTIONS
     *
     * @since 1.0
     */

    function qy_o_scroll_to_next(el, autoscroll, speed = 400, delay = 300) {
        if (autoscroll) {
            let new_position = el.offset();
            setTimeout(function() {
                $('html, body').stop().animate({ scrollTop: (new_position.top - 96) }, speed);
            }, delay);
        }     
    }

    function qy_o_show_result(timeout, weighted, container) {
        let appr_result_number = 0;

        $('.qy-o-result').each(function() {
            let range_from = parseInt($(this).attr('data-score-from'));
            let range_to = parseInt($(this).attr('data-score-to'));
            if (range_from <= score && range_to >= score ) {
                appr_result_number = parseInt($(this).attr('data-result-number'));
            }
        });

        if (appr_result_number > 0) {
            let appr_result = $('#qy-o-result-' + appr_result_number.toString());
                    
            let score_final = score;
            let score_el = '';

            if (!weighted) {
                score_el = '<span class="qy-o-score-text">' + __( `¡Acertaste ${score_final} de ${all_questions_count} preguntas!`, 'quizly' ) + '</span>';
            }

            score_el += '<span class="qy-o-retake-quiz"><span class="dashicons dashicons-image-rotate"></span>' + __('Rehacer quiz', 'quizly') + '</span>';

            if (!weighted) {
                appr_result.find('.qy-o-score-wrapper').append(score_el);
            } else {
                appr_result.find('.qy-o-result-title').after(score_el);
            }

            // Retake quiz
            $('.qy-o-retake-quiz').on('click', function() {
                // Scroll to the quiz start and reload
                let start_position = $('#qy-o-question-1').offset();
                $('html, body').stop().animate({ scrollTop: start_position.top - 96 }, 200, function() {
                    location.reload();
                });
            });

            appr_result.show();

            // Scroll to the result
            let new_position = appr_result.offset();
            setTimeout(() => {
                $('html, body').stop().animate({scrollTop: (new_position.top - 96)}, 400);
                // Save result
                qy_o_save_result(container);
            }, timeout);
            
        } else {
            console.log('Ugh... cannot find an appropriate result :O');
        }
    }

    // Get the Facebook app ID
    let fb_app_id = $('.qy-o-quiz-container').attr('data-fb-app');

    // Facebook SDK
    $.ajaxSetup({ cache: true });
    $.getScript('https://connect.facebook.net/en_US/sdk.js', function(){
        FB.init({
            appId: fb_app_id,
            version: 'v2.7' // or v2.1, v2.2, v2.3, ...
        });     
        $('#loginbutton,#feedbutton').removeAttr('disabled');
        FB.getLoginStatus(function() {
            console.log('Facebook SDK: Status updated.');
        });
    });

    // AJAX save result to the log table
    function qy_o_save_result(container) {
        let quiz_id = container.attr('data-id');
        let user_type = container.attr('data-user-type');
        let user_id = container.attr('data-user-id');
        let newsletter_el = container.find('.qy-o-newsletter');
        let email_input_val = newsletter_el.find('.qy-o-email').val();
        let newsletter_enabled = container.attr('data-newsletter-enabled');
        newsletter_enabled = (newsletter_enabled == '1') ? true : false;

        let user_email = '';
        if (newsletter_enabled && email_input_val) {
            user_email = email_input_val;
        }

        let data = {
                    _ajax_nonce: qy_o_ajax_nonce,
                    action: 'qy_o_save_result_ajax',
                    quiz_id: parseInt(quiz_id),
                    user_type: user_type,
                    user_id: parseInt(user_id),
                    score: score,
                    user_email: user_email
                };

        $.post({
            url: qy_o_ajax_url,
            data: data,
            success: function(res) {},
            error: function(error) {
                console.log(error) ;
            }
        });
    }

    function qy_o_show_newsletter_form(timeout, weighted = false, explanation_title = null, container) {
        let newsletter_el = container.find('.qy-o-newsletter');
        let email_input = newsletter_el.find('.qy-o-email');
        let submit_btn = newsletter_el.find('.qy-o-subscribe');

        // Init button to close the newsletter form
        let skip_button = $('.qy-o-newsletter .dashicons-no');
        if (skip_button) { // should be empty if not enabled in the quiz options.
            skip_button.on('click', function(e) {
                // Show result and hide email form.
                qy_o_show_result(timeout, weighted, container);
                newsletter_el.hide();
            });
        }

        submit_btn.on('click', function(el) {
            el.preventDefault();
            // Is it a valid email address?
            email_input = newsletter_el.find('.qy-o-email');
            let email_input_val = email_input.val();
            if (is_email(email_input_val)) {
                set_cookie("qy_o_has_email", "yes", 60);
                // Show result and hide email form.
                qy_o_show_result(timeout, weighted, container);
                newsletter_el.hide();
                
            } else {
                email_input.val( __('Please enter a valid email address!', 'quiz_fox')).addClass('qy-o-input-invalid');
            }
        });

        email_input.on('focus', function() {
            $(this).val('');
            $(this).removeClass('qy-o-input-invalid');
        });

        // Scroll to the subscribe form
        newsletter_el.show();
        let newsletter_position = newsletter_el.offset();
        if (explanation_title) {
            let explnation_position = explanation_title.offset();
            $('html, body').stop().animate({scrollTop: (explnation_position.top - 96)}, 400);
            setTimeout(function() {
                $('html, body').stop().animate({scrollTop: (newsletter_position.top - 96)}, 400);
            }, 2000);
        } else {
            $('html, body').stop().animate({scrollTop: (newsletter_position.top - 96)}, 400);
        }
    }

    function qy_o_end_quiz(timeout, weighted = false, explanation_title = null, container) {
        let user_type = container.attr('data-user-type');
        // If the user is a guest (not logged-in) check if they've already provided an email address
        let has_email;
        if (user_type == 'guest') {
            has_email = get_cookie("qy_o_has_email");
        }

        let is_newsletter_enabled = container.attr('data-newsletter-enabled');
        is_newsletter_enabled = (is_newsletter_enabled == '1') ? true : false;

        if (user_type == 'guest' && has_email != "yes" && is_newsletter_enabled) {
            qy_o_show_newsletter_form(timeout, weighted, explanation_title, container);
        } else {
            qy_o_show_result(timeout, weighted, container);
        }
    }

    function is_email(email) {
        let regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if(!regex.test(email)) {
            return false;
        } else {
            return true;
        }
    }

    function set_cookie(cname, cvalue, exdays) {
        let d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        let expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function get_cookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

})(jQuery);