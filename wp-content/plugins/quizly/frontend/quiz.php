<?php

if ( !defined( 'qy_o' ) ) {
    exit;
}

add_filter( 'template_include', 'qy_o_template_include', 1 );
add_action( 'wp_enqueue_scripts', 'qy_o_add_frontend_stylesheet_and_script' );
add_shortcode( 'quizly', 'qy_o_shortcode' );
add_action( 'wp_head', 'qy_o_print_to_head' );

/**
 * Apply a custom quiz template (if such file exists in the active theme) or use a function to render it.
 *
 * @since 1.0
 */
function qy_o_template_include( $template_path ) {
    if ( 'qy_o_quiz' == get_post_type() ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise install content filter
            if ( $theme_file = locate_template( array( 'single-qy-o-quiz.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                add_filter( 'the_content', 'qy_o_display_single_quiz', 20 );
            }
        }
    }
    return $template_path;    
}

/**
 * Display a single quiz
 *
 * @since 1.0
 */
function qy_o_display_single_quiz( $content ) {
    $content = qy_o_render_quiz( get_the_ID() );
    return $content;
}

/**
 * Render a quiz shortcode
 *
 * @since 1.0
 */
function qy_o_shortcode( $atts ) {
    extract( shortcode_atts( array(
		'id' => 0
    ), $atts ) );
    
    if ( $id ) {
        return qy_o_render_quiz( $id, true );
    }
}

/**
 * Render a single quiz on the frontend
 *
 * @since 1.0
 */
function qy_o_render_quiz( $id, $from_shortcode = false ) {

    if ( !empty( $id ) ) {

        $weighted = get_post_meta( $id, 'qy_o_weighted_score_enabled', true );
        $last_question_number = get_post_meta( $id, 'qy_o_last_question_number', true );
        $explanations_enabled = get_post_meta( $id, 'qy_o_answer_explanations_enabled', true );
        $randomize_order_of_answers = get_post_meta( $id, 'qy_o_randomize_answers', true );
        $qy_o_share_buttons_enabled = get_post_meta( $id, 'qy_o_share_buttons_enabled', true );
        $options = get_site_option( 'qy_o_options', array() );
        $qy_o_facebook_app_id = $options['fb_app_id'];
        $show_quiz_title = get_post_meta( $id, 'qy_o_show_title', true );
        $quiz_title = get_the_title( $id );
        $qy_o_newsletter_enabled = get_post_meta( $id , 'qy_o_newsletter_enabled', true );
        $qy_o_newsletter_skippable = get_post_meta( $id , 'qy_o_newsletter_skippable', true );
        $qy_o_autoscroll = get_post_meta( $id , 'qy_o_autoscroll', true );

        if ( $qy_o_newsletter_enabled) {
            $qy_o_newsletter_title_text = esc_html( get_post_meta( $id, 'qy_o_newsletter_title_text', true ) );
            $qy_o_newsletter_description_text = esc_html( get_post_meta( $id, 'qy_o_newsletter_description_text', true ) );
        }

        $qy_o_answer_columns = get_post_meta( $id , 'qy_o_answer_columns', true );

        $weighted_value = $weighted ? '1' : '0';
        $expl_enabled = $explanations_enabled ? '1' : '0';
        $newsletter_enabled = $qy_o_newsletter_enabled ? '1' : '0';
        $autoscroll = $qy_o_autoscroll ? '1' : '0';

        // Get info about the user
        $user_type = ''; // possible values: 'guest', 'logged_user';
        $user_id = null;
        if ( is_user_logged_in() ) {
            $user_type = 'logged_user';
            $user_id = get_current_user_id();
        } else {
            $user_type = 'guest';
        }

        $content = '';

        if ( $from_shortcode ) {
            $content .= '<style>' . esc_html( qy_o_get_custom_css( $id ) ) . '</style>';
        }

        $content .= '<div class="qy-o-quiz-container" data-last-question="' . esc_attr( $last_question_number ) . '" data-weighted="' . esc_attr( $weighted_value ) . '" data-explanations-enabled="' . esc_attr( $expl_enabled ) . '" data-title="' . esc_attr( $quiz_title ) . '" data-fb-app="' . esc_attr( $qy_o_facebook_app_id ) . '" data-id="' . esc_attr( $id ) . '" data-user-type="' . esc_attr( $user_type ) . '" data-user-id="' . esc_attr( $user_id ) . '" data-newsletter-enabled="' . esc_attr( $newsletter_enabled ) . '" data-autoscroll="' . esc_attr( $autoscroll ) . '">';

        if ( $from_shortcode ) { // include post title and featured image (shortcode doesn't show them by default)

            if ( $show_quiz_title ) {
                $content .= '<h2 class="qy-o-quiz-title">' . esc_html( $quiz_title ) . '</h2>';
            }
            $content .= '<p>' . get_the_post_thumbnail( $id, 'full', array( 'class' => 'qy-o-quiz-thumbnail aligncenter' ) ) . '</p>';
        }

        $content .= '<div class="qy-o-description">' . esc_html( get_post_meta( $id, 'quiz_description', true ) ) . '</div>';

        $questions = get_post_meta( $id, 'quiz_questions', true );

        if ( $questions ) {

            foreach( $questions as $question ) {
                $content .= '<div id="qy-o-question-' . esc_attr( $question['number'] ) . '" class="qy-o-question" data-question-number="' . esc_attr( $question['number'] ) . '">';

                    if ( $question['use_image_bkg'] ) {

                        $content .= '<figure class="qy-o-quiz-image qy-o-question-image qy-o-image-bkg">' . wp_get_attachment_image( $question['image'], 'full', '', array( 'class' => 'qy-o-question-thumbnail' ) );
                            $content .= '<div class="qy-o-image-overlay"></div>';
                            $content .= '<h2 class="qy-o-question-title">' . esc_html( $question['text'] ) . '</h2>';
                        $content .= '</figure>';

                    } else {

                        $content .= '<h2 class="qy-o-question-title" style="background-color:' . esc_attr( $question['bg_color'] ) . ';color:' . esc_attr( $question['text_color'] ) . '">' . esc_html( $question['text'] ) . '</h2>';
                        $content .= '<figure class="qy-o-quiz-image qy-o-question-image">' . wp_get_attachment_image( $question['image'], 'full', '', array( 'class' => 'question-thumbnail' ) ) . '</figure>';
                    }

                    $answers = $question['answers'];

                    if ( $randomize_order_of_answers ) {
                        shuffle( $answers );
                    }
                    
                    if ( count( $answers ) > 0 ) {
                        $content .= '<ol class="qy-o-answers-list">';
                        foreach( $answers as $answer ) {

                            $weight = 0;
                            if ( !$weighted ) {
                                $weight = $answer['is_correct'] ? 1 : 0;
                            } else {
                                $weight = $answer['weight'];
                            }

                            $content .= '<li class="qy-o-answer" data-answer-number="' . esc_attr( $answer['number'] ) . '" data-w="' . esc_attr( $weight ) . '">';
                            
                            $answer_image = wp_get_attachment_image( $answer['image'], 'medium_large', '', array( 'class' => 'qy-o-answer-thumbnail' ) );

                            if ( !empty( $answer_image ) ) {
                                $content .= '<a class="qy-o-btn qy-o-answer-btn">';
                                $content .= '<figure class="qy-o-quiz-image qy-o-answer-image">' . $answer_image . '</figure>';
                                $content .= '<p class="qy-o-answer-caption">' . esc_html( $answer['text'] )  . '</p>';
                            } else {
                                $content .= '<a class="qy-o-btn qy-o-answer-btn qy-o-answer-imageless">';
                                $content .='<p class="qy-o-answer-caption">' . esc_html( $answer['text'] )  . '</p>';
                            }
                            $content .= '</a>';
                            
                            $content .= '</li>';
                        }
                        $content .= '</ol>';

                        if ( !$weighted ) {
                            $content .= '<div class="qy-o-question-explanation">';
                                $content .= '<div class="qy-o-question-explanation-inside">';
                                    $content .= '<h3 class="qy-o-explanation-title"></h3>';
                                    $content .= '<p class="qy-o-correct-answer"></p>';
                                    if ($explanations_enabled) {
                                        $content .= '<p class="qy-o-explanation-text">' . esc_html( $question['explanation'] ) . '</p>';
                                    }
                                $content .= '</div>';
                            $content .= '</div>';
                        }
                    }
                    
                $content .= '</div>';
            }
        }

        $results = get_post_meta( $id, 'quiz_results', true );

        foreach( $results as $result ) {
            $content .= '<div id="qy-o-result-' . esc_attr( $result['number'] ) . '" class="qy-o-result" data-score-from="' . esc_attr( $result['score_range_from'] ) . '" data-score-to="' . esc_attr( $result['score_range_to'] ) . '" data-result-number="' . esc_attr( $result['number'] ) . '">';
            
            if ( !$weighted ) {
                $content .= '<p class="qy-o-score-wrapper"></p>';
                $content .= '<h2 class="qy-o-result-title">' . esc_html( $result['title'] ) . '</h2>';
            } else {
                $content .= '<div class="qy-o-score-wrapper"><h2 class="qy-o-result-title">' . esc_html( $result['title'] ) . '</h2></div>';
            }
            
            $content .= '<figure class="qy-o-quiz-image qy-o-result-image alignright">' . wp_get_attachment_image( $result['image'], 'full', '', array( 'class' => 'result-thumbnail' ) ) . '</figure>';
                $content .= '<div class="qy-o-result-description">';
                    $content .= '<p>' . esc_html( $result['description'] ) . '</p>';
                    if ( $qy_o_share_buttons_enabled ) {
                        $content .= '<div class="qy-o-social-buttons-container">';
                            $content .= '<button class="qy-o-btn qy-o-share-button qy-o-share-facebook" data-url="' . get_permalink() . '"><span class="dashicons dashicons-facebook-alt"></span>' . __( 'Share', 'quizly' ) . '</button>';
                            $content .= '<button class="qy-o-btn qy-o-share-button qy-o-share-twitter" data-url="' . get_permalink() . '"><span class="dashicons dashicons-twitter"></span></span>' . __( 'Tweet', 'quizly' ) . '</button>';
                            $content .= '<button class="qy-o-btn qy-o-share-button qy-o-share-link" data-url="' . get_permalink() . '"><span class="dashicons dashicons-admin-links"></span><span class="qy-o-share-link-text">' . __( 'Copy Link', 'quizly' ) . '</span></button>';
                        $content .= '</div>';
                    }
                $content .= '</div>';
            $content .= '</div>';
        }

        if ( $qy_o_newsletter_enabled ) {

            $content .= '<div class="qy-o-newsletter">';
                $content .= '<div class="qy-o-newsletter-inside">';

                    $newsletter_title = $qy_o_newsletter_title_text ? $qy_o_newsletter_title_text : __( 'Sign up to see your result!', 'quizly' );
                    $newsletter_description = $qy_o_newsletter_description_text ? $qy_o_newsletter_description_text : __( 'Get the latest quizzes delivered right to your inbox with our newsletter.', 'quizly' );

                    $content .= '<h3 class="qy-o-newsletter-heading">' . esc_html( $newsletter_title ) . '</h3>';
                    if ( $qy_o_newsletter_skippable ) {
                        $content .= '<span class="dashicons dashicons-no" title="' . __( 'No, thanks!', 'quizly' ) . '"></span>';
                    }
                    $content .= '<p class="qy-o-newsletter-callout">' . esc_html( $newsletter_description ) . '</p>';
                    $content .= '<form action="" method="">';
                        $content .= '<div class="qy-o-newsletter-form-row">';
                            $content .= '<input type="email" size="30" class="qy-o-email" placeholder="' . __( 'Your email address', 'quizly' ) . '"/>';
                            $content .= '<button type="submit" class="qy-o-subscribe">' . __( 'Sign up', 'quizly' ) . '</button>';
                        $content .= '</div>';
                    $content .= '</form>';
                $content .= '</div>';
            $content .= '</div>';

        }

        $content .= '</div>';

        return $content;
    }
}


/**
 * Enqueue frontend CSS and JavaScript files
 *
 * @since 1.0
 */
function qy_o_add_frontend_stylesheet_and_script() {
    // Do not load the styles and script if there isn't a quiz on the page.
    global $post;    
    if ( ( is_single() && 'qy_o_quiz' == get_post_type() ) || ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'quizly') ) ) {
        $options = get_site_option( 'qy_o_options', array() );
        if ( !$options['disable_styles'] ) {
            wp_enqueue_style( 'qy-o-frontend-stylesheet', plugins_url( 'quiz.min.css', __FILE__ ) );
        }
        wp_enqueue_style( 'dashicons' );
        wp_enqueue_script( 'qy-o-frontend-script', plugins_url( 'quiz.min.js', __FILE__ ), array( 'jquery', 'wp-i18n' ), '20200316', true );
        wp_set_script_translations( 'qy-o-frontend-script', 'quizly' );
    }
}


/**
 * Print various stuff in the 'head': 
 * OpenGraph tags, add support fot Twitter widget, custom CSS, generate AJAX urls.
 *
 * @since 1.0
 */
function qy_o_print_to_head() {

    /* Do not load the styles and script if there isn't a quiz on the page. */
    global $post;
    $qy_o_is_single_quiz_post = is_single() && 'qy_o_quiz' == get_post_type();
    $qy_o_has_shortcode =  is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'quizly');

    if ( $qy_o_is_single_quiz_post || $qy_o_has_shortcode ) {
    
        if ( $qy_o_is_single_quiz_post && ! $qy_o_has_shortcode) {  
            $quiz_id = get_the_ID(); ?>

            <!-- Open Graph -->
            <meta property="og:url" content="<?php the_permalink(); ?>" />
            <meta property="og:type" content="article" />
            <meta property="og:title" content="<?php the_title(); ?>" />
            <meta property="og:description" content="<?php echo esc_html( get_post_meta( $quiz_id, 'quiz_description', true ) ); ?>" />
            <?php $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $quiz_id ), 'medium_large' ); ?>
            <meta property="og:image" content="<?php echo esc_url( $large_image_url[0] ); ?>" />

            <!-- Custom CSS -->
            <style>
                <?php echo esc_html( qy_o_get_custom_css( $quiz_id ) ); ?>
            </style>
            
        <?php } ?>

        <!-- Twitter -->
        <script>window.twttr = (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0],
                t = window.twttr || {};
            if (d.getElementById(id)) return t;
            js = d.createElement(s);
            js.id = id;
            js.src = "https://platform.twitter.com/widgets.js";
            fjs.parentNode.insertBefore(js, fjs);

            t._e = [];
            t.ready = function(f) {
                t._e.push(f);
            };

            return t;
            }(document, "script", "twitter-wjs"));
        </script>

        <!-- AJAX urls -->
        <script type="text/javascript">
            const qy_o_ajax_nonce = '<?php echo wp_create_nonce( 'qy_o_ajax' ); ?>';
            const qy_o_ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
        </script>

    <?php 
    }
}

/**
 * Get either global or local (custom) CSS.
 *
 * @since 1.0
 */
function qy_o_get_custom_css( $quiz_id = null ) {
    // Global custom CSS
    $options = get_site_option( 'qy_o_options', array() );
    $custom_css = $options['global_custom_css'];
    
    if ( !empty( $quiz_id ) ) {

        $custom_css .= "\n";

        // Get answer columns number
        $qy_o_answer_columns = get_post_meta( $quiz_id , 'qy_o_answer_columns', true );
        if ( $qy_o_answer_columns == '3' ) { // '2' is by default
            $custom_css .= '.entry-content .qy-o-answers-list li, .qy-o-answers-list li { width: 31.5%; }';
        }

        $custom_css .= "\n";

        // "Local" (for the current quiz) custom CSS
        $custom_css .= esc_html( get_post_meta( $quiz_id, 'qy_o_custom_css', true ) );
    }

    return $custom_css;
}