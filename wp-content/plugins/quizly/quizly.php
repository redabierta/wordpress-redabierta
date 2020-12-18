<?php
/*
  Plugin Name: Quizly
  Plugin URI: https://oniwp.com/
  Description: Intuitive and powerful quiz builder
  Author: Oni
  Version: 1.0.2
  Author URI: https://oniwp.com/
  Text Domain: quizly
  Domain Path: /languages
 */

define( 'qy_o', 1 );

register_activation_hook( __FILE__, 'qy_o_activation' );
register_deactivation_hook( __FILE__, 'qy_o_deactivation' );
add_action( 'plugins_loaded', 'qy_o_setup' );

/**
 * Setup the plugin.
 *
 * @since 1.0
 */
function qy_o_setup() {

    load_plugin_textdomain( 'quizly', false, 'quizly/languages' );

    // Includes
    if ( is_admin() ) {
        require plugin_dir_path( __FILE__ ) . 'admin/admin.php';
    } else {
        require plugin_dir_path( __FILE__ ) . 'frontend/quiz.php';
    }

    add_action( 'init', 'qy_o_add_quiz_post_type' );
    add_action( 'wp_ajax_qy_o_save_result_ajax', 'qy_o_save_result_ajax' );
    add_action( 'wp_ajax_nopriv_qy_o_save_result_ajax', 'qy_o_save_result_ajax' );
    add_action( 'wp_ajax_csv_pull', 'qy_o_download_emails_csv' );   
}

/**
 * Register the "quiz" post type 
 *
 * @since 1.0
 */
function qy_o_add_quiz_post_type() {
    register_post_type( 'qy_o_quiz', array(
        'labels' => array(
            'name' => __( 'Quizly', 'quizly' ),
            'singular_name' => __( 'Quiz', 'quizly' ),
            'all_items' => __('All Quizzes','quizly'),
            'add_new' => __( 'Add New', 'quizly' ),
            'add_new_item' => __( 'Add New Quiz', 'quizly' ),
            'edit' => __( 'Edit', 'quizly' ),
            'edit_item' => __( 'Edit Quiz', 'quizly' ),
            'new_item' => __( 'New Quiz', 'quizly' ),
            'view' => __( 'View', 'quizly' ),
            'view_item' => __( 'View Quiz', 'quizly' ),
            'search_items' => __( 'Search Quizzes', 'quizly' ),
            'not_found' => __( 'No Quizzes found', 'quizly' ),
            'not_found_in_trash' => __( 'No Quizzes found in Trash', 'quizly' ),
            'parent' => __( 'Parent Quiz Review', 'quizly' )
        ),
        'public' => false,
        'menu_position' => 101,
        'supports' => array( 'title', 'thumbnail' ),
        'hierarchical' => false,
        'menu_icon' => 'dashicons-forms',
        'has_archive' => false,
        'rewrite' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'query_var' => true,
        'can_export' => true,
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'capability_type' => 'post'
    ));

    flush_rewrite_rules();
}

/**
 * Plugin activation
 *
 * @since 1.0
 */
function qy_o_activation() {
    global $wpdb;
    qy_o_create_table( $wpdb->get_blog_prefix() );
}

/**
 * Plugin deactivation
 *
 * @since 1.0
 */
function qy_o_deactivation() {
    flush_rewrite_rules();
}

/**
 * Create a db table to store the quiz log
 *
 * @since 1.0
 */
function qy_o_create_table( $prefix ) {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $creation_query = 'CREATE TABLE IF NOT EXISTS ' .
        $prefix . "qy_log (
            `ID` int NOT NULL AUTO_INCREMENT,
            `quiz_id` int(20) NOT NULL,
            `user_type` varchar(20) NOT NULL DEFAULT 'guest',
            `user_id` int(10) DEFAULT NULL,
            `score` int(10) NOT NULL DEFAULT 0,
            `user_email` varchar(200) DEFAULT NULL,
            `log_date` date DEFAULT CURDATE(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $creation_query );  
}

/**
 * Save quiz plays in the log table
 *
 * @since 1.0
 */
function qy_o_save_result_ajax() {
    check_ajax_referer( 'qy_o_ajax', '_ajax_nonce' );

    if ( isset( $_POST['quiz_id'] ) ) {
        global $wpdb;

        // Prepare the query for saving result into the database
        $table = $wpdb->prefix . "qy_log";
        $data = array(
            'quiz_id' => absint( $_POST['quiz_id'] ),
            'user_type' => ( $_POST['user_type'] === 'logged_user' ) ? 'logged_user' : 'guest',
            'user_id' => absint( $_POST['user_id'] ),
            'score' => absint( $_POST['score'] ),
            'user_email' => sanitize_email( $_POST['user_email'] )
        );

        $format = array('%d', '%s', '%d', '%d', '%s');
        $wpdb->insert( $table, $data, $format );
    }
    wp_die();
}

/**
 * Download a CSV file with user email addresses.
 *
 * @since 1.0
 */
function qy_o_download_emails_csv() {

    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( __( 'Insufficient privileges!', 'quizly' ) );
    }

    global $wpdb;
    $file = 'emails';

    $results = $wpdb->get_results( "
        SELECT {$wpdb->prefix}qy_log.user_type, {$wpdb->prefix}users.user_nicename, {$wpdb->prefix}qy_log.user_email as email_address
        FROM {$wpdb->prefix}qy_log
        LEFT JOIN {$wpdb->prefix}users
            on {$wpdb->prefix}users.ID = {$wpdb->prefix}qy_log.user_id
        WHERE {$wpdb->prefix}qy_log.user_email <> ''
        GROUP BY {$wpdb->prefix}qy_log.user_email
    ", ARRAY_A );

    if ( empty( $results ) ) {
        return;
    }

    $csv_output = '"' . implode( '","', array_keys( $results[0] ) ) . '"' . "\n";

    foreach ( $results as $row ) {
        $csv_output .= '"' . implode( '","', $row ) . '"' . "\n";
    }

    $csv_output .= "\n";

    $upload_dir = wp_get_upload_dir();
    $basename = $file . "_" . date( "Y-m-d_H-i", time() ) . '.csv';
    $filepath = $upload_dir['path'] . '/' . $basename;
    $file_handler = fopen( $filepath, "w" );
    fwrite( $file_handler, $csv_output );
    fclose( $file_handler );

    if ( file_exists( $filepath ) ) {
        $response = array(
            'url' => $upload_dir['url'] . '/' . $basename,
            'basename' => $basename
        );

        wp_send_json( $response );
    }
    wp_die();
}