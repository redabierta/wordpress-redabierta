const { __, _x, _n, _nx } = wp.i18n;

(function($){

    "use strict";

    init_image_buttons();
    init_remove_image_buttons();
    init_delete_answer_buttons();
    init_delete_result_buttons();
    mark_correct();
    set_live_title_updates();
    boxes_accordion_toggle();
    stop_propagation();
    
    /**
     * Tab control
     *
     * @since 1.0
     */
    $('#qy-o-main-tab').on('click', function() {
        $('.nav-tab-active').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active').blur();
        $('#qy_o_quiz_extras_meta_box').hide();
        $('#qy_o_quiz_setup_meta_box, #qy_o_quiz_questions_meta_box, #qy_o_quiz_results_meta_box').show();
    });

    $('#qy-o-extras-tab').on('click', function() {
        $('.nav-tab-active').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active').blur();
        $('#qy_o_quiz_setup_meta_box, #qy_o_quiz_questions_meta_box, #qy_o_quiz_results_meta_box').hide();
        $('#qy_o_quiz_extras_meta_box').show();
    });

	/**
     * "Remove image" event
     *
     * @since 1.0
     */
    function init_remove_image_buttons() {
        $('.qy-o-remove-image-button').on('click', function(e) {
            e.preventDefault();
            $(this).hide().prev()
                .val('')
                .prev()
                .addClass('button')
                .html('<span class="dashicons dashicons-format-image"></span>' + __('Image', 'quiz-fox'))
                ;
            return false;
        });
    }
    
    /**
     * Attach actions to the "Publish" button
     *
     * @since 1.0
     */
    $('#publish').on('click', function(e) {
        e.preventDefault();

        // Collect QUESTIONS data into the JSON hidden field.
        let questions = [];
        $('.qy-o-question-box').each(function() {

            let answers = [];
            $(this).find('.qy-o-answer-box').each(function() {
                answers.push({
                    'text': $(this).find('.qy-o-answer-text').val(),
                    'image': $(this).find('.qy-o-answer-image-field').val(),
                    'is_correct': $(this).find('.qy-o-answer-is-correct').prop('checked'),
                    'weight': $(this).find('.qy-o-answer-weight').val(),
                    'number': $(this).find('.qy-o-answer-number').val()
                });
            });

            questions.push({
                'text': $(this).find('.qy-o-question-text').val(),
                'image': $(this).find('.qy-o-question-image-field').val(),
                'use_image_bkg': $(this).find('.qy-o-use-image-bkg').prop('checked'),
                'number': $(this).find('.qy-o-question-number').val(),
                'answers': answers,
                'explanation': $(this).find('.qy-o-answer-explanation').val(),
                'bg_color': $(this).find('.qy-o-question-bg-color').val(),
                'text_color': $(this).find('.qy-o-question-text-color').val()
            });
        });
        
        $('#qy_o_questions_json').val(JSON.stringify(questions));

        // Collect RESULTS data into the JSON hidden field.
        let results = [];
        $('.qy-o-result-box').each(function() {
            results.push({
                'score_range_from': $(this).find('.qy-o-result-score-from').val(),
                'score_range_to': $(this).find('.qy-o-result-score-to').val(),
                'title': $(this).find('.qy-o-result-title-input').val(),
                'description': $(this).find('.qy-o-result-description').val(),
                'image': $(this).find('.qy-o-result-image-field').val(),
                'number': $(this).find('.qy-o-result-number').val()
            });
        });

        $('#qy_o_results_json').val(JSON.stringify(results));

        $('#publish').unbind('click');	
        $('#publish').trigger('click');	
    });

    /**
     * "Add answer" button handler
     *
     * @since 1.0
     */
    $('.qy_o_add_answer_btn').on('click', function(e) {
        e.preventDefault();
        
        let question_box = $(this).closest('.qy-o-question-box');

        // Get last answer number
        let last_answer_number_el = question_box.find('.qy-o-last-answer-number');
        // Increment the last number.
        let new_answer_number = (parseInt(last_answer_number_el.val()) + 1).toString();
        // Update the hidden field containing the last number.
        last_answer_number_el.val(new_answer_number);

        // Get the question containig element.
        let parent_el = question_box.find('.qy-o-answers-box .widefat tbody');

        // Clone the first answer and reset its field values.
        let new_answer = $('#qy-o-question-1 .qy-o-answer-1').clone().attr('class', 'qy-o-answer-box qy-o-answer-' + new_answer_number);
        new_answer.find('.qy-o-remove-image-button').hide().prev().val('').prev().addClass('button').html('<span class="dashicons dashicons-format-image"></span>' + __('Image', 'quiz-fox'));
        new_answer.find('.qy-o-answer-text').attr('name', 'qy-o-answer-text-' + new_answer_number).val('');
        new_answer.find('.qy-o-answer-is-correct').attr('name', 'qy-o-answer-is-correct-' + new_answer_number).prop( "checked", false );
        new_answer.find('.qy-o-answer-weight').attr('name', 'qy-o-answer-weight-' + new_answer_number).val('1');
        new_answer.find('.qy-o-answer-number').val(new_answer_number);

        // ... then append it to the question container.
        new_answer.appendTo(parent_el);

        init_delete_answer_buttons();
        reposition_explanations();
        mark_correct();
        recount_answer_numbers(question_box);
        init_image_buttons();
        init_remove_image_buttons();
        stop_propagation(); 
    });

    /**
     * "Add question" button handler
     *
     * @since 1.0
     */
    $('.qy_o_add_question_btn').on('click', function(e) {
        e.preventDefault();

        // Get last question number
        let last_question_number_el = $(this).closest('#qy_o_quiz_questions_meta_box').find('#qy-o-last-question-number');
        // Increment the last number.
        let new_question_number = (parseInt(last_question_number_el.val()) + 1).toString();
        // Update the hidden field containing the last number.
        last_question_number_el.val(new_question_number);

        // Get the containing element.
        let parent_box_el = $(this).closest('#qy_o_quiz_questions_meta_box .inside').find('#qy-o-quiz-questions-box');

        // Clone the first question and reset its field values.
        let new_question = $('#qy-o-question-1').clone(true);
        new_question.find('.qy-o-question-number').val(new_question_number);
        let new_question_id = 'qy-o-question-' + new_question_number;
        new_question.attr('id', new_question_id);
        new_question.find('.qy-o-question-title .qy-o-number').text(new_question_number);
        new_question.find('.qy-o-question-title .qy-o-title-text').text('');
        new_question.find('.qy-o-remove-image-button').hide().prev().val('').prev().addClass('button').html('<span class="dashicons dashicons-format-image"></span>' + __('Image', 'quiz-fox'));
        new_question.find('.qy-o-question-text').attr('name', 'quiz_question_' + new_question_number);
        new_question.find('.qy-o-question-text').val('');
        new_question.find('.qy-o-answer-explanation').val('');
        new_question.find('.qy-o-last-answer-number').val('2');

        new_question.find('.bg-color-row').remove();
        new_question.find('.text-color-row').remove();

        let bg_color_template = '<tr class="bg-color-row"><th>' + __( 'Background color', 'quiz-fox' ) + '</th><td><input class="qy-o-color qy-o-question-bg-color qy-o-question-bg-color-' + new_question_number + '" type="text" name="qy_o_question_bg_color" value="#ffffff" data-default-color="#ffffff" /></td></tr>';

        let text_color_template = '<tr class="text-color-row"><th>' + __( 'Text color', 'quiz-fox' ) + '</th><td><input class="qy-o-color qy-o-question-text-color qy-o-question-text-color-' + new_question_number + '" type="text" name="qy_o_question_text_color" value="#222222" data-default-color="#222222" /></td></tr>';

        new_question.find('.qy-o-metabox-table .qy-o-question-body').append(bg_color_template);
        new_question.find('.bg-color-row').after(text_color_template);

        new_question.find('.qy-o-question-bg-color-' + new_question_number).wpColorPicker();
        new_question.find('.qy-o-question-text-color-' + new_question_number).wpColorPicker();

        new_question.find('.qy-o-use-image-bkg').prop( "checked", false );

        let j = 0;
        new_question.find('.qy-o-answer-box').each(function() {
            j++;
            if (j > 2) {
                $(this).remove();
            } else {
                $(this).find('.qy-o-answer-text').val('');
                $(this).find('.qy-o-answer-is-correct').prop( "checked", false );
                $(this).find('.qy-o-answer-weight').val('1');
            }
        });

        parent_box_el.find('.qy-o-last-answer-number').val('2');

        new_question.appendTo(parent_box_el);
  
        init_delete_answer_buttons();
        recount_question_numbers();
        mark_correct();
        init_image_buttons();
        init_remove_image_buttons();
        set_live_title_updates();
        show_new_question_box(new_question);
        stop_propagation(); 
    });

    /**
     * "Add result" button handler
     *
     * @since 1.0
     */
    $('#qy_o_quiz_results_meta_box').on('click', '.qy_o_add_result_btn', function(e) {
        e.preventDefault();

        // Get last result number
        let last_result_number_el = $(this).closest('#qy_o_quiz_results_meta_box').find('#qy-o-last-result-number');
        // Increment the last number.
        let new_result_number = (parseInt(last_result_number_el.val()) + 1).toString();
        // Update the hidden field containing the last number.
        last_result_number_el.val(new_result_number);

        // Get the containing element.
        let parent_box_el = $(this).closest('#qy_o_quiz_results_meta_box .inside').find('#qy-o-quiz-results-box');

        // Clone the first result and reset its field values.
        let new_result = $('#qy-o-result-1').clone();
        new_result.find('.qy-o-result-number').val(new_result_number);
        new_result.attr('id', 'qy-o-result-' + new_result_number);
        new_result.find('.qy-o-remove-image-button').hide().prev().val('').prev().addClass('button').html('<span class="dashicons dashicons-format-image"></span>' + __('Image', 'quiz-fox'));
        new_result.find('.qy-o-result-description').attr('name', 'quiz_result_' + new_result_number);
        new_result.find('.qy-o-result-description').val('');
        new_result.find('.qy-o-result-score-from').val('');
        new_result.find('.qy-o-result-score-to').val('');
        new_result.find('.qy-o-result-title-input').val('');
        new_result.find('.qy-o-title-text').text('');

        // ... then append it to the bottom of the container.
        new_result.appendTo(parent_box_el);

        init_delete_result_buttons();
        recount_result_numbers();
        stop_propagation();
        set_live_title_updates();
        init_image_buttons();
        init_remove_image_buttons();
        show_new_result_box(new_result);
        stop_propagation(); 
    });

    /**
     * "Delete question" button handler
     *
     * @since 1.0
     */
    $('.qy-o-delete-question-button').on('click', function() {
        let questions_count = $('.qy-o-question-box').length;
        if (questions_count > 1) {
            let question_box = $(this).closest('.qy-o-question-box');
            let question_number = question_box.find('.qy-o-question-number').val();
            question_box.remove();
            let last_question = $('#qy-o-last-question-number');

            // Fix last number
            last_question.val(questions_count - 1);

            let last_number = parseInt($('#qy-o-last-question-number').val());
            let i;
            for (i = last_number; i > question_number; i--) {
                let question_to_tweak = $('#qy-o-question-' + i.toString());
                new_val = i - 1;
                new_val = new_val.toString();
                question_to_tweak.find('.qy-o-question-number').val(new_val);
                question_to_tweak.attr('id', 'qy-o-question-' + new_val);
            }
            let new_last_number = last_number - 1;
            new_last_number = new_last_number.toString();
            $('#qy-o-last-question-number').val(new_last_number);
            recount_question_numbers();
        } else {
            alert(__('There must be at least one question.', 'quizly'));
        }
    });

    /**
     * Check/uncheck "Weighted score"
     *
     * @since 1.0
     */
    $('#qy-o-weighted-score-enabled').change(function() {
        if (this.checked) {
            $('.qy-o-answer-is-correct').addClass('qy-o-hidden');
            $('.qy-o-answer-weight').removeClass('qy-o-hidden');
            $('#option-answer-explanations-enabled').addClass('qy-o-hidden');
            $('.explanation-box').removeClass('qy-o-row-visible');
        } else {
            $('.qy-o-answer-weight').addClass('qy-o-hidden');
            $('.qy-o-answer-is-correct').removeClass('qy-o-hidden');
            $('#option-answer-explanations-enabled').removeClass('qy-o-hidden');

            let explanations_enabled = $('#qy-o-answer-explanations-enabled').prop('checked');
            if (explanations_enabled) {
                $('.explanation-box').addClass('qy-o-row-visible');
            }
        }
    });

    /**
     * Check/uncheck "Answer explanations"
     *
     * @since 1.0
     */
    $('#qy-o-answer-explanations-enabled').change(function() {
        if (this.checked) {
            $('.explanation-box').addClass('qy-o-row-visible');
            reposition_explanations();
        } else {
            $('.explanation-box').removeClass('qy-o-row-visible');
        }
    });

    /**
     * Check/uncheck "Newsletter enabled"
     *
     * @since 1.0
     */
    $('#qy-o-newsletter-enabled').change(function() {
        if (this.checked) {
            $('.qy-o-newsletter-details').addClass('qy-o-row-visible');
        } else {
            $('.qy-o-newsletter-details').removeClass('qy-o-row-visible');
        }
    });

    /**
     * Check/uncheck "Set image as background"
     *
     * @since 1.0
     */
    $('.qy-o-use-image-bkg').change(function() {
        let color_controls = $(this).closest('.qy-o-question-setup-box').find('.bg-color-row, .text-color-row');
        if (this.checked) {
            color_controls.addClass('qy-o-hidden');
        } else {
            color_controls.removeClass('qy-o-hidden');
        }
    });

    /**
     * Pick a "correct" answer
     *
     * @since 1.0
     */
    function mark_correct() {
        $('.qy-o-answer-is-correct').on('click', function() {
            $(this).closest('.qy-o-answers-box').find('.qy-o-answer-is-correct').each(function() {
                this.checked = false;
            });
            this.checked = true;
        });
    }

    /**
     * Initialize Color Picker
     *
     * @since 1.0
     */
    $('#qy-o-quiz-questions-box').find('.qy-o-question-box').each(function() {
        let number = $(this).find('.qy-o-question-number').val();
        $(this).find('.qy-o-question-bg-color-' + number).wpColorPicker();
        $(this).find('.qy-o-question-text-color-' + number).wpColorPicker();
    });

    /**
     * Live update titles on keystroke
     *
     * @since 1.0
     */
    function set_live_title_updates() {
        $('.qy-o-question-text').on('keyup', function() {
            $(this).closest('.qy-o-question-box').find('.qy-o-title-text').text($(this).val());
        });
        $('.qy-o-result-title-input').on('keyup', function() {
            $(this).closest('.qy-o-result-box').find('.qy-o-title-text').text($(this).val());
        });
    }

    /**
     * Download emails CSV file
     *
     * @since 1.0
     */
    $('#qy-o-download-emails').on('click', function(e) {
        e.preventDefault();

        $.get({
            url: qy_o_ajax_url_csv,
            data: {},
            success: function(res) {
                // Create a download link
                var a = document.createElement('a');
                a.href = res.url;
                a.download = res.basename;
                document.body.append(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(res);
            },
            error: function(err) {
                console.log(err);
            }
        });
    });

    /**
     * Add drag & drop support for the question and the result items
     *
     * @since 1.0
     */
    let is_dragging = false;
    $('#qy-o-quiz-questions-box, #qy-o-quiz-results-box').sortable({
        cancel: 'input, textarea, button',
        revert: true,
        cursor: 'move',
        scroll: true,
        delay: 100,
        start: function() {
            is_dragging = true;
        },
        stop: function() {
            is_dragging = false;
        }
    });
    
    $('#qy-o-quiz-questions-box').unbind('sortupdate');
    $('#qy-o-quiz-questions-box' ).on('sortupdate', function(event, ui) {
        recount_question_numbers();
    });

    $('#qy-o-quiz-results-box').unbind('sortupdate');
    $('#qy-o-quiz-results-box').on('sortupdate', function(event, ui) {
        recount_result_numbers();
    });

    /**
     * Add accordion reveal/hide for the question and the result boxes
     *
     * @since 1.0
     */
    function boxes_accordion_toggle() {
        $( '.qy-o-question-box, .qy-o-result-box', '#wpbody').unbind( 'click' );

        $( '.qy-o-question-box' ).on('click', function(e) {
            let del_btn = $(e.target).hasClass('.qy-o-delete-question-button');
            if ( is_dragging == false && !del_btn ) {
                $('.qy-o-question-inside').not($(this).find('.qy-o-question-inside')).hide();
                $(this).find('.qy-o-question-inside').toggle('fast');  
            }
        });

        $('#wpbody').on('click', '.qy-o-result-box', function(e) {
            let del_btn = $(e.target).hasClass('qy-o-delete-result-button');
            if (is_dragging == false && !del_btn) {
                $('.qy-o-result-inside').not($(this).find('.qy-o-result-inside')).hide();
                $(this).find('.qy-o-result-inside').toggle('fast');
            }        
        });
    }

    function show_new_question_box(el) {               
        $('.qy-o-question-inside').not(el.find('.qy-o-question-inside')).hide();
        el.find('.qy-o-question-inside').show('fast');
    }

    function show_new_result_box(el) {
        $('.qy-o-result-inside').not(el.find('.qy-o-result-inside')).hide();
        el.find('.qy-o-result-inside').show('fast');
    }

    /**
     * Recount and set correct question numbers
     *
     * @since 1.0
     */
    function recount_question_numbers() {
        let counter = 0;
        $('.qy-o-question-box').each(function() {
            counter++;
            $(this).find('.qy-o-question-number').val(counter.toString());
            $(this).find('.qy-o-number').html(counter + ':');
            $(this).attr('id', 'qy-o-question-' + counter);
            if (counter === 1) {
                $(this).attr('id', 'qy-o-question-1');
            }
        });
        $('#qy-o-last-question-number').val(counter);
    }

    /**
     * Recount and set correct answer numbers
     *
     * @since 1.0
     */
    function recount_answer_numbers(question_box) {
        let counter = 0;
        question_box.find('.qy-o-answer-box').each(function() {
            counter++;
            $(this).find('.qy-o-answer-number').val(counter.toString());
            if (counter === 1) {
                $(this).removeClass();
                $(this).addClass('qy-o-answer-box qy-o-answer-1');
            }
        });
        question_box.find('.qy-o-last-answer-number').val(counter);
    }

    /**
     * Recount and set correct result numbers
     *
     * @since 1.0
     */
    function recount_result_numbers() {
        let counter = 0;
        $('.qy-o-result-box').each(function() {
            counter++;
            $(this).find('.qy-o-result-number').val(counter.toString());
            $(this).attr('id', 'qy-o-result-' + counter);
            if (counter === 1) {
                $(this).attr('id', 'qy-o-result-1');
            }
        });
        $('#qy-o-last-result-number').val(counter);
    }

    /**
     * Re-position the explanation element after the last answer
     *
     * @since 1.0
     */
    function reposition_explanations() {
        $('.qy-o-answers-box').each(function() {
            let last_answer = $(this).find('.qy-o-answer-box').last();
            let explanation = $(this).find('.explanation-box');
            explanation.insertAfter(last_answer);
        });
    }

    /**
     * Select/Upload image(s) event
     *
     * @since 1.0
     */
    function init_image_buttons() {
        $('.qy_o_add_image_button').unbind('click');
        init_add_image_button('.qy_o_add_image_button');
    }

    /**
     * 'Add image' button functionality
     *
     * @since 1.0
     */
    function init_add_image_button(selector) {
        $(selector).on('click', function(e) {
            e.preventDefault();
    
            let button = e.target;
            let orig_button = button.closest('.qy_o_add_image_button');

            let custom_uploader = wp.media.frames.file_frame = wp.media({
                title: __('Select image', 'quizly'),
                library: {
                    // uncomment the next line if you want to attach image to the current post
                    // uploadedTo : wp.media.view.settings.post.id, 
                    type: 'image'
                },
                multiple: false,
                // frame: 'post',
                // state: 'insert',
                button: {
                    text: __('Use this image', 'quizly')
                }
            });
            
            custom_uploader.on('select', function() {
                let attachment = custom_uploader.state().get('selection').first().toJSON();
                let image_url;
                let attachment_html;
                
                if (orig_button.classList.contains('larger')) {
                    image_url = attachment.sizes['medium'].url;
                    attachment_html = '<img class="true_pre_image" src="' + image_url + '" style="max-width:200px;display:block;" />';
                } else {
                    image_url = attachment.sizes['thumbnail'].url;
                    attachment_html = '<img class="true_pre_image" src="' + image_url + '" style="max-width:100px;display:block;" />';
                }
                              
                let attachment_id = attachment.id;
                let button_next = $(orig_button).next();
                $(orig_button).removeClass('button'); 
                $(orig_button).html('').html(attachment_html);
                button_next.val(attachment_id);
                button_next.next().show();
            })
            .open();
            
            e.stopPropagation();
        });
    }

    /**
     * "Delete answer" button handler
     *
     * @since 1.0
     */
    function init_delete_answer_buttons() {
        $('.qy-o-delete-answer-button').unbind('click');
        $('.qy-o-delete-answer-button').on('click', function() {
            let question_box = $(this).closest('.qy-o-question-box');
            let answers_count = question_box.find('.qy-o-answer-box').length;
            if (answers_count < 3) {
                alert(__('You need at least two answers.', 'quizly'));
            } else {
                $(this).closest('.qy-o-answer-box').remove();
                recount_answer_numbers(question_box);
            }
        });
    }

    /**
     * "Delete result" button handler
     *
     * @since 1.0
     */
    function init_delete_result_buttons() {
        $('.qy-o-delete-result-button').unbind('click');
        $('.qy-o-delete-result-button').on('click', function() {
            // cache selectors to improve performance
            let results_count = $('.qy-o-result-box').length;
            if (results_count > 1) {
                let result_box = $(this).closest('.qy-o-result-box');
                let result_number = result_box.find('.qy-o-result-number').val();
                result_box.remove();
                let last_number = parseInt($('#qy-o-last-result-number').val());
                let i;
                for (i = last_number; i > result_number; i--) {
                    let result_to_tweak = $('#qy-o-result-' + i.toString());
                    new_val = i - 1;
                    new_val = new_val.toString();
                    result_to_tweak.find('.qy-o-result-number').val(new_val);
                    result_to_tweak.attr('id', 'qy-o-result-' + new_val);
                }
                let new_last_number = last_number - 1;
                new_last_number = new_last_number.toString();
                $('#qy-o-last-result-number').val(new_last_number);
                recount_result_numbers();
            } else {
                alert(__('There must be at least one result.', 'quizly'));
            }
        });
    }

    /**
     * Prevent click propagation to parent elements
     *
     * @since 1.0
     */
    function stop_propagation() {
        $('.qy-o-question-inside, .qy-o-result-inside, .qy-o-delete-question-button, .qy-o-delete-result-button, .qy_o_add_image_button, .qy-o-result-box input, .qy-o-result-box textarea').click(function(e) {
            e.stopPropagation();
        });
    }

})(jQuery);