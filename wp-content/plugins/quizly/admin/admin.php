<?php

if ( !defined( 'qy_o' ) ) {
    exit;
}

require plugin_dir_path( __FILE__ ) . 'quiz-log.php';

add_action( 'admin_init', 'qy_o_admin_init' );
add_action( 'save_post', 'qy_o_save_quiz_fields', 10, 2 );
add_action( 'admin_enqueue_scripts', 'qy_o_add_admin_stylesheet_and_script' );
add_filter( 'manage_edit-qy_o_quiz_columns', 'qy_o_columns', 10, 1 );	
add_filter( 'manage_qy_o_quiz_posts_custom_column', 'qy_o_column_content', 10, 2 );
add_action( 'admin_head', 'qy_o_print_to_admin_head' );
add_action( 'edit_form_after_title', 'qy_o_admin_tabs' );
add_action( 'admin_menu', 'qy_o_settings_menu' );

/**
 * Set tabs for the quiz editor page in the admin
 *
 * @since 1.0
 */
function qy_o_admin_tabs() {
	global $post;
	if ( $post->post_type === 'qy_o_quiz' ) {
		$html = '<div id="qy-o-tabs">';
			$html .= '<h1 class="nav-tab-wrapper">';
				$html .= '<a href="#" id="qy-o-main-tab" class="nav-tab nav-tab-active">' . __( 'Editor', 'quizly' ) . '</a>';
				$html .= '<a href="#" id="qy-o-extras-tab" class="nav-tab">' . __( 'Settings', 'quizly' ) . '</a>';
			$html .= '</h1>';		
		$html .= '</div>';
		echo $html;
	}
}

/**
 * Add actions to the "admin init" hook: options, metaboxes, saves.
 *
 * @since 1.0
 */
function qy_o_admin_init() {

    add_action( 'admin_post_save_qy_o_options', 'qy_o_process_qy_o_options' );

    add_meta_box( 
        'qy_o_quiz_extras_meta_box',
        __( 'Extras', 'quizly' ),
        'qy_o_display_quiz_extras_meta_box',
        'qy_o_quiz', 
        'normal', 
        'high' 
    );
    add_meta_box( 
        'qy_o_quiz_setup_meta_box',
        __( 'Setup', 'quizly' ),
        'qy_o_display_quiz_setup_meta_box',
        'qy_o_quiz', 
        'normal', 
        'high' 
    );
    add_meta_box( 
        'qy_o_quiz_questions_meta_box',
        __( 'Questions', 'quizly' ),
        'qy_o_display_quiz_questions_meta_box',
        'qy_o_quiz', 
        'normal', 
        'high' 
    );
    add_meta_box( 
        'qy_o_quiz_results_meta_box',
        __( 'Results', 'quizly' ),
        'qy_o_display_quiz_results_meta_box',
        'qy_o_quiz', 
        'normal', 
        'high' 
    );
    add_meta_box( 
        'qy_o_quiz_shortcode_meta_box',
        __( 'Shortcode', 'quizly' ),
        'qy_o_display_quiz_shortcode_meta_box',
        'qy_o_quiz', 
        'side', 
        'default'
    );

    add_action( 'admin_post_save_qy_o_entry', 'process_qy_o_entry' );
}

/**
 * Render the "Settings" meta box.
 *
 * @since 1.0
 */
function qy_o_display_quiz_extras_meta_box( $quiz ) {

    $qy_o_randomize_answers = get_post_meta( $quiz->ID, 'qy_o_randomize_answers', true );
    $qy_o_share_buttons_enabled = get_post_meta( $quiz->ID, 'qy_o_share_buttons_enabled', true );
    $qy_o_newsletter_enabled = get_post_meta( $quiz->ID , 'qy_o_newsletter_enabled', true );
    $qy_o_facebook_app_id = get_post_meta( $quiz->ID, 'qy_o_facebook_app_id', true );
    $qy_o_custom_css = esc_html( get_post_meta( $quiz->ID, 'qy_o_custom_css', true ) );
    $qy_o_newsletter_title_text = esc_html( get_post_meta( $quiz->ID, 'qy_o_newsletter_title_text', true ) );
    $qy_o_newsletter_description_text = esc_html( get_post_meta( $quiz->ID, 'qy_o_newsletter_description_text', true ) );
    $qy_o_newsletter_skippable = get_post_meta( $quiz->ID , 'qy_o_newsletter_skippable', true );
    $qy_o_answer_columns = get_post_meta( $quiz->ID , 'qy_o_answer_columns', true );
    $qy_o_autoscroll = get_post_meta( $quiz->ID , 'qy_o_autoscroll', true );

    // Set default values for some of the settings
    $qy_o_new_quiz = get_post_meta( $quiz->ID, 'qy_o_new_quiz', true );
    if ( $qy_o_new_quiz != 'nope' ) {
        $qy_o_share_buttons_enabled = true;
        $qy_o_newsletter_skippable = true;
        $qy_o_answer_columns = '2';
        $qy_o_autoscroll = true;
    }

    ?>
    <table class="qy-o-metabox-table" data-new-quiz="<?php echo $qy_o_new_quiz; ?>">
        <tr>
            <th></th>
            <td>
                <input type="checkbox" name="qy_o_autoscroll" id="qy-o-autoscroll" <?php checked( $qy_o_autoscroll, true, true ); ?> /><label for="qy-o-autoscroll"><?php _e( 'Auto-scroll to the next question?', 'quizly' ); ?></label>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <input type="checkbox" name="qy_o_randomize_answers" id="qy-o-randomize-answers" <?php checked( $qy_o_randomize_answers, true, true ); ?> /><label for="qy-o-randomize-answers"><?php _e( 'Randomize order of choices', 'quizly' ); ?></label>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <input type="checkbox" name="qy_o_share_buttons_enabled" id="qy-o-share-buttons-enabled" <?php checked( $qy_o_share_buttons_enabled, true, true ); ?> /><label for="qy-o-share-buttons-enabled"><?php _e( 'Add share buttons to the quiz result', 'quizly' ); ?></label>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <hr />
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <input type="checkbox" name="qy_o_newsletter_enabled" id="qy-o-newsletter-enabled" <?php checked( $qy_o_newsletter_enabled, true, true ); ?> /><label for="qy-o-newsletter-enabled"><?php _e( 'Ask users for their email address before showing the result', 'quizly' ); ?></label>
            </td>
        </tr>

        <?php $newsletter_details_class = ( $qy_o_newsletter_enabled ) ? ' qy-o-row-visible' : ''; ?>

        <tr class="qy-o-newsletter-details<?php echo $newsletter_details_class ?>">
            <th><?php _e( 'Title', 'quizly' ); ?></th>
            <td>
                <input type="text" size="30" name="qy_o_newsletter_title_text" id="qy-o-newsletter-title-text" placeholder="<?php _e( 'Sign up to see your result!' ) ?>" value="<?php esc_attr_e( $qy_o_newsletter_title_text ); ?>" />
            </td>
        </tr>
        <tr class="qy-o-newsletter-details<?php echo $newsletter_details_class ?>">
            <th><?php _e( 'Description', 'quizly' ); ?></th>
            <td>
                <textarea cols="60" rows="4" name="qy_o_newsletter_description_text" id="qy-o-newsletter-description-text" placeholder="<?php _e( 'Get the latest quizzes delivered right to your inbox with our newsletter.' ) ?>"><?php esc_html_e( $qy_o_newsletter_description_text ); ?></textarea>
            </td>
        </tr>
        <tr class="qy-o-newsletter-details<?php echo $newsletter_details_class ?>">
            <th></th>
            <td>
                <input type="checkbox" name="qy_o_newsletter_skippable" id="qy-o-newsletter-skippable" <?php checked( $qy_o_newsletter_skippable, true, true ); ?> /><label for="qy-o-newsletter-skippable"><?php _e( 'Allow users to skip the subscription form', 'quizly' ); ?></label>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <hr />
            </td>
        </tr>

        <tr class="qy-o-answer-column-option">
            <th><?php _e( 'Columns', 'quizly' ); ?></th>
            <td>
                <input type="radio" id="qy-o-two-cols" name="qy_o_answer_columns" value="2" <?php checked( '2' == $qy_o_answer_columns ); ?>><label for="qy-o-two-cols">2 <?php _e( 'Columns', 'quizly' ); ?></label>
                <input type="radio" id="qy-o-three-cols" name="qy_o_answer_columns" value="3" <?php checked( '3' == $qy_o_answer_columns ); ?>><label for="qy-o-three-cols">3 <?php _e( 'Columns', 'quizly' ); ?></label>
                <p class="description"><?php _e( 'Arrange the answers in how many columns?', 'quizly' ) ?></p>
            </td>
        </tr>  

        <tr>
            <th><?php _e( 'Custom CSS', 'quizly' ); ?></th>
            <td>
                <textarea type="text" cols="80" rows="10" name="qy_o_custom_css" value="" class=""><?php echo esc_html( $qy_o_custom_css ); ?></textarea>
                <p class="description"><?php _e( 'Add styles that are specific for this quiz', 'quizly' ) ?></p>
            </td>
        </tr>  
    </table>
<?php }

/**
 * Render the "Setup" (or "Main") meta box.
 *
 * @since 1.0
 */
function qy_o_display_quiz_setup_meta_box( $quiz ) {
    $qy_o_show_title = get_post_meta( $quiz->ID, 'qy_o_show_title', true );
    $quiz_description = esc_html( get_post_meta( $quiz->ID, 'quiz_description', true ) );
    $qy_o_weighted_score_enabled = get_post_meta( $quiz->ID, 'qy_o_weighted_score_enabled', true );
    $qy_o_answer_explanations_enabled = get_post_meta( $quiz->ID, 'qy_o_answer_explanations_enabled', true );
    ?>

    <table class="qy-o-metabox-table">
        <tr>
            <th></th>
            <td>
                <input type="checkbox" name="qy_o_show_title" id="qy-o-show-title" <?php checked( $qy_o_show_title, true, true ); ?> /><label for="qy-o-show-title"><?php _e( 'Show title', 'quizly' ); ?></label>
            </td>
        </tr>
        <tr>
            <th><?php _e( 'Description', 'quizly' ); ?></th>
            <td>
                <textarea type="text" class="large-text" cols="80" rows="5" name="quiz_description" value=""><?php esc_html_e( $quiz_description ); ?></textarea>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <input type="checkbox" name="qy_o_weighted_score_enabled" id="qy-o-weighted-score-enabled" <?php checked( $qy_o_weighted_score_enabled, true, true ); ?> /><label for="qy-o-weighted-score-enabled"><?php _e( 'Weighted score', 'quizly' ); ?></label>
                <br /><span class="description"><?php _e( 'Assign numeric weight values to answers in place of the "correct-wrong" (Trivia) system. Useful for complex scoring systems or "Personality" quizzes.', 'quizly' ); ?></span>
            </td>
        </tr>
        <?php $explanations_class = ( $qy_o_weighted_score_enabled ) ? 'qy-o-hidden' : ''; ?>
        <tr id="option-answer-explanations-enabled" class="<?php esc_attr_e( $explanations_class ); ?>">
            <th></th>
            <td>
                <input type="checkbox" name="qy_o_answer_explanations_enabled" id="qy-o-answer-explanations-enabled" <?php checked( $qy_o_answer_explanations_enabled, true, true ); ?> /><label for="qy-o-answer-explanations-enabled"><?php _e( 'Answer explanations', 'quizly' ); ?></label>
                <br /><span class="description"><?php _e( 'Provide explanation text for each question, revealed after the user picks an answer.', 'quizly' ); ?></span>
            </td>
        </tr>
    </table>
<?php }


/**
 * Render the "Questions" meta box, container for the quiz questions.
 *
 * @since 1.0
 */
function qy_o_display_quiz_questions_meta_box( $quiz ) { ?>

    <p class="description qy-o-metabox-description">
        <?php _e( 'Add questions & answers. Drag to reorder.', 'quizly' ); ?>
    </p>

    <div id="qy-o-quiz-questions-box">  
        <?php
        $quiz_questions = get_post_meta( $quiz->ID, 'quiz_questions', true );
        $last_question_number = get_post_meta( $quiz->ID, 'qy_o_last_question_number', true );
        $weighted = get_post_meta( $quiz->ID, 'qy_o_weighted_score_enabled', true );
        $explanations_enabled = get_post_meta( $quiz->ID, 'qy_o_answer_explanations_enabled', true );

        if ( empty( $last_question_number ) ) {
            $last_question_number = '1';
        }
        if ( empty( $quiz_questions ) ) {
            echo qy_o_display_question( '', $weighted, $explanations_enabled );
        } else {
            foreach ( $quiz_questions as $question ) {
                echo qy_o_display_question( $question, $weighted, $explanations_enabled );
            }
        } ?>
    </div>
    <div><button class="qy_o_add_question_btn button-secondary"><span class="dashicons dashicons-plus"></span><?php _e( 'Add Question', 'quizly' ); ?></button></div>
    <input type="hidden" name="qy_o_questions_json" id='qy_o_questions_json' />
    <input type="hidden" name="qy_o_last_question_number" id="qy-o-last-question-number" value="<?php esc_attr_e( $last_question_number ); ?>" />
<?php }

/**
 * Render the 'Results" meta box
 *
 * @since 1.0
 */
function qy_o_display_quiz_results_meta_box( $quiz ) { ?>

    <p class="description qy-o-metabox-description">
        <?php _e( 'Add results for the possible score ranges. Drag to reorder.', 'quizly' ); ?>
    </p>

    <div id="qy-o-quiz-results-box">
        <?php
        $quiz_results = get_post_meta( $quiz->ID, 'quiz_results', true );
        $last_result_number = get_post_meta( $quiz->ID, 'qy_o_last_result_number', true );
        $weighted = get_post_meta( $quiz->ID, 'qy_o_weighted_score_enabled', true );

        if ( empty( $last_result_number ) ) {
            $last_result_number = '1';
        }
        if ( empty( $quiz_results ) ) {
            echo qy_o_display_result( '', $weighted );
        } else {
            foreach ( $quiz_results as $result ) {
                echo qy_o_display_result( $result, $weighted );
            }
        } ?>
    </div>
    <div><button class="qy_o_add_result_btn button-secondary"><span class="dashicons dashicons-plus"></span><?php _e( 'Add Result', 'quizly' ); ?></button></div>
    <input type="hidden" name="qy_o_results_json" id='qy_o_results_json' />
    <input type="hidden" name="qy_o_last_result_number" id="qy-o-last-result-number" value="<?php esc_attr_e( $last_result_number ); ?>" />
<?php }

/**
 * Render the quiz "Shortcode" meta box
 *
 * @since 1.0
 */
function qy_o_display_quiz_shortcode_meta_box( $quiz ) {
    echo '<p><input type="text" readonly="readonly" onclick="this.select()" value="[quizly id=&quot;'. esc_attr( $quiz->ID ) . '&quot;]"/></p>';
}

/**
 * Render a single question
 *
 * @since 1.0
 */
function qy_o_display_question( $question, $weighted, $explanations_enabled ) {
    if ( $question === '' ) {
        $question = array(
            'text' => '',
            'image' => '',
            'number' => '1',
            'answers' => [],
            'explanation' => '',
            'bg_color' => '#ffffff',
            'text_color' => '#222222',
            'use_image_bkg' => false
        );
    }

    $output = '<div id="qy-o-question-' . esc_attr( $question['number'] ) . '" class="qy-o-question-box">';

        $output .= '<h3 class="qy-o-question-title"><span class="dashicons dashicons-menu-alt"></span><span class="qy-o-number">' . esc_html( $question['number'] ) . ':</span> <span class="qy-o-title-text">' . esc_html( $question['text'] ) . '</span><span class="dashicons dashicons-trash qy-o-delete-question-button" title="' . __( 'Delete question', 'quizly' ) . '"></span></h3>';

        $output .= '<div class="qy-o-question-inside">';

            $output .= '<p><input type="text" name="qy_o_quiz_question_text" value="' . esc_attr( $question['text'] ) . '" class="qy-o-question-text large-text" /></p>';

            $output .= '<div class="qy-o-question-setup-box">';

                $output .= '<figure class="qy-o-image-upload-field">';
                    $output .= qy_o_image_uploader_field( 'question_image_' . $question['number'], $question['image'], 'question', 200, true );
                    $output .= '<p><input type="checkbox" name="qy_o_use_image_bkg" id="qy-o-use-image-bkg-' . $question['number'] . '" class="qy-o-use-image-bkg" ' . checked( $question['use_image_bkg'], true, false ) . ' ><label for="qy-o-use-image-bkg-' . $question['number'] . '">' . __( 'Set as background', 'quizly' ) . '</label></p>';
                $output .= '</figure>';

                $output .= '<table class="qy-o-metabox-table"><tbody class="qy-o-question-body">';

                    $color_controls_visibility = ( $question['use_image_bkg'] ) ? ' qy-o-hidden' : '';

                    $output .= '<tr class="bg-color-row' . $color_controls_visibility . '">';
                        $output .= '<th>' . __( 'Background color', 'quizly' ) . '</th>';
                        $output .= '<td><input class="qy-o-color qy-o-question-bg-color qy-o-question-bg-color-' . esc_attr( $question['number'] ) .'" type="text" name="qy_o_question_bg_color" value="' . esc_attr( $question['bg_color'] ) . '" data-default-color="#ffffff" /></td>';
                    $output .= '</tr>';

                    $output .= '<tr class="text-color-row' . $color_controls_visibility . '">';
                        $output .= '<th>' . __( 'Text color', 'quizly' ) . '</th>';
                        $output .= '<td><input class="qy-o-color qy-o-question-text-color qy-o-question-text-color-' . esc_attr( $question['number'] ) . '" type="text" name="qy_o_question_text_color" value="' . esc_attr( $question['text_color'] ) . '" data-default-color="#222222" /></td>';
                    $output .= '</tr>';

                $output .= '</tbody></table>'; 
            
            $output .= '</div>';

            $output .= '<h4 class="qy-o-answers-heading">' . __( 'Answers', 'quizly' ) . ':</h4>';

            $output .= '<div class="qy-o-answers-box">';

                $output .= '<table class="widefat">'; 

                    $output .= '<thead>';
                        $output .= '<tr>';
                            $output .= '<th>' . __( 'Image', 'quizly' ) . '</th>';
                            $output .= '<th>' . __( 'Text', 'quizly' ) . '</th>';
                            $output .= '<th>' . __( 'Correct', 'quizly' ) . '</th>';
                            $output .= '<th>' . __( 'Weight', 'quizly' ) . '</th>';
                            $output .= '<th>' . __( 'Delete', 'quizly' ) . '</th>';
                        $output .= '</tr>';
                    $output .= '</thead>';

                    $output .= '<tbody>';

                    $last_answer_number = 0;

                    if ( count( $question['answers'] ) > 0 ) {
                        foreach ( $question['answers'] as $answer ) {
                            $is_even = ( ( ( $last_answer_number + 1 ) % 2 ) == 0) ? true : false;
                            $output .= qy_o_display_answer( $answer, $weighted, $is_even );
                            $last_answer_number++;
                        }
                    } else {
                        for ( $i = 1; $i <= 2; $i++ ) {
                            $answer = array(
                                'text' => '',
                                'image' => '',
                                'is_correct' => false,
                                'weight' => 1,
                                'number' => $i
                            );
                            $is_even = ( ( ( $last_answer_number + 1 ) % 2 ) == 0) ? true : false;
                            $output .= qy_o_display_answer( $answer, $weighted, $is_even );
                            $last_answer_number++;
                        }
                    }

                    $explanation_visibility = ( !$weighted && $explanations_enabled ) ? ' qy-o-row-visible' : '';

                    $output .= '<tr class="explanation-box' . esc_attr( $explanation_visibility ) .'">';
                        $output .= '<td class="explanation-label">' . __( 'Explanation', 'quizly' ) . '</td><td><textarea type="text" cols="80" rows="4" name="qy_o_answer_explanation" class="qy-o-answer-explanation large-text">' . esc_html( $question['explanation'] )  . '</textarea></td>';
                    $output .= '</tr>';

                $output .= '</tbody></table>';

            $output .= '</div>';

            $output .= '<input type="hidden" class="qy-o-question-number" value="' . esc_attr( $question['number'] ) . '" />';
            $output .= '<input type="hidden" class="qy-o-last-answer-number" value="' . esc_attr( $last_answer_number ) . '" />';
            $output .= '<p><a class="qy_o_add_answer_btn">+ ' . __( 'Add Answer', 'quizly' ) .'</a></p>';
        $output .= '</div>';
    $output .= '</div>';

    return $output;
}

/**
 * Render a single answer
 *
 * @since 1.0
 */
function qy_o_display_answer( $answer, $weighted, $is_even ) {
    $alternate = $is_even ? '' : ' alternate';
    $output = '<tr class="qy-o-answer-box qy-o-answer-' . esc_attr( $answer['number'] . $alternate ) . '" >';

        $output .= '<td><figure class="qy-o-image-upload-field">' . qy_o_image_uploader_field( 'answer_image_' . $answer['number'], $answer['image'], 'answer', 100 ) . '</figure></td>';
        $output .= '<td>';
            $output .= '<input type="text" name="qy_o_answer_text" value="' . esc_attr( $answer['text'] ) . '" class="qy-o-answer-text large-text" />';
        $output .= '</td>';
        if ( $weighted ) {
            $output .= '<td><input type="checkbox" name="qy_o_answer_is_correct" class="qy-o-answer-is-correct qy-o-hidden" ' . checked( $answer['is_correct'], true, false ) . ' /></td>';
            $output .= '<td><input type="number" step="1" min="0" max="99" maxlength="2" name="qy_o_answer_weight" value="' . esc_attr( $answer['weight'] ) . '" class="qy-o-answer-weight" /></td>';
        } else {
            $output .= '<td><input type="checkbox" name="qy_o_answer_is_correct" class="qy-o-answer-is-correct" ' . checked( $answer['is_correct'], true, false ) . ' /></td>';
            $output .= '<td><input type="number" step="1" min="0" max="99" maxlength="2" name="qy_o_answer_weight" value="' . esc_attr( $answer['weight'] ) . '" class="qy-o-answer-weight qy-o-hidden" /></td>';
        }
        $output .= '<td><span class="dashicons dashicons-trash qy-o-delete-answer-button" title="' . __( 'Delete answer', 'quizly' ) . '"></span></td>';
        $output .= '<input type="hidden" class="qy-o-answer-number" value="' . esc_attr( $answer['number'] ) . '" />';
    $output .= '</tr>';
    return $output;
}

/**
 * Render a single result
 *
 * @since 1.0
 */
function qy_o_display_result( $result, $weighted ) {
    if ( $result === '' ) {
        $result = array(
            'score_range_from' => '',
            'score_range_to' => '',
            'title' => '',
            'description' => '',
            'image' => '',
            'number' => '1'
        );
    }
    
    $output = '<div id="qy-o-result-' . esc_attr( $result['number'] ) . '" class="qy-o-result-box">';

        $output .= '<h3 class="qy-o-result-title"><span class="dashicons dashicons-menu-alt"></span><span class="qy-o-title-text">' . esc_html( $result['title'] ) . '</span><span class="dashicons dashicons-trash qy-o-delete-result-button" title="' . __( 'Delete result', 'quizly' ) . '"></span></h3>';

        $output .= '<div class="qy-o-result-inside">';

            $output .= '<div class="qy-o-result-flex">';

                $output .= '<table class="qy-o-metabox-table qy-o-result-metabox-table"><tbody class="result-body">';
                    $output .= '<tr>';
                        $output .= '<th>' . __( 'Score', 'quizly' ) . '</th>';
                        $output .= '<td>';
                            $output .= '<input type="number" step="1" min="0" max="999" maxlength="3" name="qy_o_result_score_from" value="' . esc_attr( $result['score_range_from'] ) . '" class="qy-o-result-score-from" placeholder="' . __( 'from', 'quizly' ) . '" /><input type="number" step="1" min="0" max="999" maxlength="3" name="qy_o_result_score_to" value="' . esc_attr( $result['score_range_to'] ) . '" class="qy-o-result-score-to" placeholder="' . __( 'to', 'quizly' ) . '" />';
                        $output .= '</td>';;
                    $output .= '</tr>';
                    $output .= '<tr>';
                        $output .= '<th>' . __( 'Title', 'quizly' ) . '</th>';
                        $output .= '<td>';
                            $output .= '<input type="text" name="qy_o_result_title" value="' . esc_attr( $result['title'] ) . '" class="qy-o-result-title-input large-text" />';
                        $output .= '</td>';
                    $output .= '</tr>';
                    $output .= '<tr>';
                        $output .= '<th>' . __( 'Description', 'quizly' ) . '</th>';
                        $output .= '<td>';
                            $output .= '<textarea type="text" cols="80" rows="7" name="qy_o_result_description" class="qy-o-result-description large-text" />' . $result['description'] . '</textarea>';
                        $output .= '</td>';
                    $output .= '</tr>';
                $output .= '</tbody></table>';

                $output .= '<figure class="qy-o-image-upload-field qy-o-result-image-upload-field">';
                    $output .= qy_o_image_uploader_field( 'result_image_' . $result['number'], $result['image'], 'result', 200 );
                $output .= '</figure>';

            $output .= '</div>';     

        $output .= '</div>';

        $output .= '<input type="hidden" class="qy-o-result-number" value="' . esc_attr( $result['number'] ) . '" />';
    $output .= '</div>';

    return $output;
}

/**
 * Save all quiz fields into the database.
 *
 * @since 1.0
 */
function qy_o_save_quiz_fields( $quiz_id, $quiz ) {

    if ( 'qy_o_quiz' == $quiz->post_type ) {

        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Insufficient privileges!', 'quizly' ) );
        }

        if ( array_key_exists( 'qy_o_questions_json', $_POST ) ) {

            if ( isset( $_POST['qy_o_show_title'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_show_title', true );
            } else {
                update_post_meta( $quiz_id, 'qy_o_show_title', false );
            }

            if ( isset( $_POST['quiz_description'] ) ) {
                update_post_meta( $quiz_id, 'quiz_description', sanitize_textarea_field( $_POST['quiz_description'] ) );
            }

            if ( isset( $_POST['qy_o_last_question_number'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_last_question_number', sanitize_text_field( $_POST['qy_o_last_question_number'] ) );
            }

            if ( isset( $_POST['qy_o_weighted_score_enabled'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_weighted_score_enabled', true );
            } else {
                update_post_meta( $quiz_id, 'qy_o_weighted_score_enabled', false );
            }

            if ( isset( $_POST['qy_o_answer_explanations_enabled'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_answer_explanations_enabled', true );
            } else {
                update_post_meta( $quiz_id, 'qy_o_answer_explanations_enabled', false );
            }

            if ( isset( $_POST['qy_o_randomize_answers'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_randomize_answers', true );
            } else {
                update_post_meta( $quiz_id, 'qy_o_randomize_answers', false );
            }

            if ( isset( $_POST['qy_o_share_buttons_enabled'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_share_buttons_enabled', true );
            } else {
                update_post_meta( $quiz_id, 'qy_o_share_buttons_enabled', false );
            }

            if ( isset( $_POST['qy_o_facebook_app_id'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_facebook_app_id', sanitize_text_field( $_POST['qy_o_facebook_app_id'] ) );
            }

            if ( isset( $_POST['qy_o_custom_css'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_custom_css', sanitize_textarea_field( $_POST['qy_o_custom_css'] ) );
            }

            if ( isset( $_POST['qy_o_newsletter_enabled'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_newsletter_enabled', true );
            } else {
                update_post_meta( $quiz_id, 'qy_o_newsletter_enabled', false );
            }

            if ( isset( $_POST['qy_o_newsletter_title_text'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_newsletter_title_text', sanitize_text_field( $_POST['qy_o_newsletter_title_text'] ) );
            }

            if ( isset( $_POST['qy_o_newsletter_description_text'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_newsletter_description_text', sanitize_textarea_field( $_POST['qy_o_newsletter_description_text'] ) );
            }

            if ( isset( $_POST['qy_o_newsletter_skippable'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_newsletter_skippable', true );
            } else {
                update_post_meta( $quiz_id, 'qy_o_newsletter_skippable', false );
            }

            if ( isset( $_POST['qy_o_answer_columns'] ) ) {
                $qy_o_answer_columns = ( $_POST['qy_o_answer_columns'] == '3' ) ? '3' : '2';
                update_post_meta( $quiz_id, 'qy_o_answer_columns', $qy_o_answer_columns );
            }

            if ( isset( $_POST['qy_o_autoscroll'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_autoscroll', true );
            } else {
                update_post_meta( $quiz_id, 'qy_o_autoscroll', false );
            }

            // Not a "new quiz" after the first save.
            update_post_meta( $quiz_id, 'qy_o_new_quiz', 'nope' );
        
            update_post_meta( $quiz_id, 'quiz_questions', qy_kses_data( json_decode( stripslashes_deep( $_POST['qy_o_questions_json'] ), true ) ) );
        }

        if ( array_key_exists( 'qy_o_results_json', $_POST ) ) {
            if ( isset( $_POST['qy_o_last_result_number'] ) ) {
                update_post_meta( $quiz_id, 'qy_o_last_result_number', sanitize_text_field( $_POST['qy_o_last_result_number'] ) );
            }
            update_post_meta( $quiz_id, 'quiz_results', qy_kses_data( json_decode( stripslashes_deep( $_POST['qy_o_results_json'] ), true ) ) );
        }

        
    }
}

/**
 * Strip evil scripts from the quiz data (JSON)
 *
 * @since 1.0
 */
function qy_kses_data( $data ) {
	$allowed_tags = wp_kses_allowed_html( 'post' );
	$allowed_tags['iframe'] = array( 
        'src' => true, 
        'width' => true, 
        'height' => true, 
        'frameborder' => true 
    );
	
	if ( is_array( $data ) ) {
		forEach( $data as $key => $value ) {
			$data[ $key ] = qy_kses_data( $value );
		}
		return $data;
	}
	
	$data = wp_kses( $data, $allowed_tags );
		
	return $data;
}

/*
* Image upload field
*
* since 1.0
* @param string $name Name of option or name of post custom field.
* @param string $value Optional Attachment ID
* @return string HTML of the Upload Button
*/
function qy_o_image_uploader_field( $name, $value = '', $type = '', $max_width = 100, $larger = false ) {
    $extra_class = $larger ? ' larger' : '';
    $image = ' button' . $extra_class . '"><span class="dashicons dashicons-format-image"></span>' . __( 'Image', 'quizly' );

    $image_size = '';
    if ( $max_width <= 125 ) {
        $image_size = 'thumbnail';
    } elseif ( $max_width <= 250 ) {
        $image_size = 'medium';
    } else {
        $image_size = 'medium_large';
    }

    $display = 'none'; // display state ot the "Remove image" button

    if ( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
        // $image_attributes[0] - image URL
        // $image_attributes[1] - image width
        // $image_attributes[2] - image height
        $image = '"><img src="' . $image_attributes[0] . '" class="qy-o-image-field" style="max-width:' . $max_width . 'px;display:block;" />';
        $display = 'inline-block';
    }

    $output = '
            <a href="#" class="qy_o_add_image_button' . $image . '</a>
            <input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" class="qy-o-' . esc_attr( $type ) . '-image-field" />
            <a href="#" class="qy-o-remove-image-button button button-small" style="display:inline-block;display:' . $display . '" title="' . __( 'Remove image', 'quizly' ) . '"><span class="dashicons dashicons-no-alt"></span></a>
    ';

    return $output;
}

/**
 * Enqueue admin CSS and JavaScript files
 *
 * @since 1.0
 */
function qy_o_add_admin_stylesheet_and_script( $hook ) {
    $screen = get_current_screen();
    if ( is_object( $screen ) && 'qy_o_quiz' == $screen->post_type ) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'qy-o-admin-stylesheet', plugins_url( 'admin.min.css', __FILE__ ) );
        wp_enqueue_script( 'qy-o-admin-script', plugins_url( 'admin.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'wp-color-picker', 'wp-i18n' ), '20200305', true );
        wp_set_script_translations( 'qy-o-admin-script', 'quizly' );     
    }   
}

/**
 * Add quiz-specific columns to the terms table.
 *
 * @since 1.0
 */
function qy_o_columns( $columns ) {

    $_columns = array();
    $_columns['cb'] = '<input type="checkbox" />';
    $_columns['title'] = __( 'Quiz Title', 'column-name', 'quizly' );
    $_columns['id'] = __( 'Shortcode', 'quizly' );
    $_columns['date'] = __( 'Date', 'column-name', 'quizly' );
    
    return $_columns;
}

/**
 * Display a 'Shortcode' column in the terms table.
 *
 * @since 1.0
 */
 function qy_o_column_content( $column_name, $id ) {
    echo '<code>[quizly id="' . intval( $id ) . '"]</code>';
}

/**
 * Generate the AJAX url for CSV file download and print it to the (admin) head.
 *
 * @since 1.0
 */
function qy_o_print_to_admin_head() {
    $screen = get_current_screen();
    if ( is_object( $screen ) && 'qy_o_quiz' == $screen->post_type ) { ?>
        <!-- AJAX url's -->
        <script type="text/javascript">
            const qy_o_ajax_url_csv = '<?php echo admin_url( 'admin-ajax.php?action=csv_pull' ); ?>';
        </script>
    <?php 
    }
}

/**
 * Add a "Settings" plugin submenu
 *
 * @since 1.0
 */
function qy_o_settings_menu() {
    add_submenu_page(
        'edit.php?post_type=qy_o_quiz',
        __( 'Settings', 'quizly' ),
        __( 'Settings', 'quizly' ),
        'manage_options',
        'qy-o-settings',
        'qy_o_settings_page'
    );
}

/**
 * Render the "Settings" page
 *
 * @since 1.0
 */
function qy_o_settings_page() {
    // Retrieve plugin configuration options from database
    $options = qy_o_get_options();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e( 'Quiz Settings', 'quizly' ); ?></h1>
        <hr class="wp-header-end">

        <form method="post" action="admin-post.php">

            <input type="hidden" name="action" value="save_qy_o_options" />
            <!-- Adding security through hidden referrer field -->
            <?php wp_nonce_field( 'qy_o' ); ?>

            <table class="qy-o-metabox-table">

                <tr>
                    <th><?php _e( 'Facebook App ID', 'quizly' ); ?>:</th>
                    <td>
                        <input type="text" name="qy_o_fb_app_id" value="<?php esc_attr_e( $options['fb_app_id'] ); ?>"/>
                        <span class="description"><?php _e( 'Needed for sharing on Facebook.', 'quizly' ); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Disable the plugin styles', 'quizly' ); ?>:</th>
                    <td>
                        <input type="checkbox" name="qy_o_disable_styles" <?php checked( $options['disable_styles'] ); ?>/>
                        <span class="description"><?php _e( 'Turn off the plugin default styles and leave it to your theme to render the quizzes.', 'quizly' ); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><?php _e( 'Custom CSS', 'quizly' ); ?>: </th>
                    <td>
                        <textarea type="text" cols="80" rows="15" name="qy_o_global_custom_css" class=""><?php esc_html_e( $options['global_custom_css'] ); ?></textarea>
                        <p class="description"><?php _e( 'Global styles (for all quizzes). These would be applied independently of the previous option.', 'quizly' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" value="<?php _e( 'Save', '`quizly' ); ?>" class="button-primary"/>
                    </td>
                </tr>
            
            </table>
    
        </form>

    </div>
	<?php
}

/**
 * Get the plugin settings
 *
 * @since 1.0
 */
function qy_o_get_options() {
    $options = get_site_option( 'qy_o_options', array() );

    $new_options['fb_app_id'] = '';
    $new_options['disable_styles'] = false;
    $new_options['global_custom_css'] = '';
	
    $merged_options = wp_parse_args( $options, $new_options ); 

    $compare_options = array_diff_key( $new_options, $options );   
    if ( empty( $options ) || !empty( $compare_options ) ) {
        update_site_option( 'qy_o_options', $merged_options );
    }
    return $merged_options;
}

/**
 * Process and save the plugin settings
 *
 * @since 1.0
 */
function qy_o_process_qy_o_options() {
    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( __( 'Not allowed', 'quizly' ) );
    }
    
    check_admin_referer( 'qy_o' );

    $options = qy_o_get_options();

    if ( isset( $_POST['qy_o_fb_app_id'] ) ) {
        $options['fb_app_id'] = sanitize_text_field( $_POST['qy_o_fb_app_id'] );
    }

    if ( isset( $_POST['qy_o_disable_styles'] ) ) {
        $options['disable_styles'] = true;
    } else {
        $options['disable_styles'] = false;
    }

    if ( isset( $_POST['qy_o_global_custom_css'] ) ) {
        $options['global_custom_css'] = sanitize_textarea_field( $_POST['qy_o_global_custom_css'] );
    }

    update_site_option( 'qy_o_options', $options );

    wp_redirect( admin_url( '/edit.php?post_type=qy_o_quiz&page=qy-o-settings' ) );
    exit;
}