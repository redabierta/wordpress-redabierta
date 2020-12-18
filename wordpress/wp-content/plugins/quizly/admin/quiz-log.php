<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Override the built-in WP_List_Table class
 *
 * @since 1.0
 */
class Quiz_Log_Table extends WP_List_Table {
    public $screen; //
    
    public function __construct( $args = array() ) {
        $args = array( 
            'singular' => __( 'Entry', 'quizly' ),
            'plural' => __( 'Entries', 'quizly' ),
            'ajax'     => false,
            'screen'   => null, 
        );
        parent::construct( $args );

        $this->screen = get_current_screen(); //
    }

    /**
    * Retrieve log entries from the database
    *
    * @since 1.0
    */
    public static function get_entries( $per_page = 25, $page_number = 1 ) {

        global $wpdb;
    
        $sql = "SELECT {$wpdb->prefix}qy_log.*, {$wpdb->prefix}posts.post_title, {$wpdb->prefix}users.display_name
            FROM {$wpdb->prefix}qy_log 
            inner join {$wpdb->prefix}posts
                on {$wpdb->prefix}posts.ID = {$wpdb->prefix}qy_log.quiz_id
            left join {$wpdb->prefix}users
                on {$wpdb->prefix}users.ID = {$wpdb->prefix}qy_log.user_id";
    
        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
        } else {
            $sql .= " ORDER BY {$wpdb->prefix}qy_log.ID DESC";
        }
    
        $sql .= " LIMIT $per_page";    
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
        
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    /**
    * Delete an entry record.
    *
    * @since 1.0
    */
    public static function delete_entry( $id ) {
        global $wpdb;
    
        $wpdb->delete(
            "{$wpdb->prefix}qy_log",
            [ 'ID' => $id ],
            [ '%d' ]
        );
    }

    /**
    * Returns the count of records in the database.
    *
    * @since 1.0
    */
    public static function record_count() {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}qy_log";
        return $wpdb->get_var( $sql );
    }

    /* Text displayed when no customer data is available */
    public function no_items() {
        _e( 'No entries avaliable.', 'quizly' );
    }

    /**
	 * Render a column when no column specific method exist.
	 *
	 * @since 1.0
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
            case 'ID':
            case 'post_title':
            case 'user_type':
            case 'display_name':
            case 'user_id': 
            case 'score':
            case 'user_email':
			case 'log_date':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @since 1.0
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', esc_attr( $item['ID'] )
		);
    }
    
    /**
	 * Method for name column
	 *
	 * @since 1.0
	 */
	function column_name( $item ) {
		$delete_nonce = wp_create_nonce( 'qy_o_delete_entry' );
		$title = '<strong>' . esc_html( $item['ID'] ) . '</strong>';

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&entry=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
    }
    
    /**
	 * Associative array of columns
	 *
	 * @since 1.0
	 */
	function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
            'post_title' => __( 'Quiz Title', 'quizly' ),
			'user_type' => __( 'User Type', 'quizly' ),
            'display_name' => __( 'User Name', 'quizly' ),
            'score' => __( 'Score', 'quizly' ),
            'user_email' => __( 'Email Address', 'quizly' ),         
            'log_date' => __( 'Date', 'quizly' )
        );

		return $columns;
    }
    
    /**
	 * Columns to make sortable.
	 *
	 * @since 1.0
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'id' => array( 'id', true ),
            'score' => array( 'score', true ),
            'log_date' => array( 'log_date', true )
		);

		return $sortable_columns;
    }
    
    /**
	 * Returns an associative array containing the bulk action
	 *
	 * @since 1.0
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => __( 'Delete', 'quizly' )
		];

		return $actions;
    }

    protected function get_table_classes() {
		return array( 'widefat', 'striped', 'feeds' );
	}
    
    /**
     * Handles data query and filter, sorting, and pagination.
     *
     * @since 1.0
	 */
	public function prepare_items() {
        $this->_column_headers = array( $this->get_columns(), array(), array() );

		// Process bulk action
		$this->process_bulk_action();

		$per_page = $this->get_items_per_page( 'entries_per_page', 5 );
		$current_page = $this->get_pagenum();
        $total_items = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page' => $per_page
		] );
        
        $this->items = self::get_entries( $per_page, $current_page );
    }

    /**
	 * Display the table
	 *
	 * @since 1.0
	 */
	public function display() {
        $singular = null;
        if ( isset( $this->_args['singular'] ) ) {  // expected in PHP 7.4, shows a notice otherwise
            $singular = $this->_args['singular'];
        }

		$this->display_tablenav( 'top' );

		$this->screen->render_screen_reader_content( 'heading_list' );
		?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tbody id="the-list"
                <?php
                if ( $singular ) {
                    echo " data-wp-lists='list:$singular'";
                }
                ?>
                >
                <?php $this->display_rows_or_placeholder(); ?>
            </tbody>

            <tfoot>
            <tr>
                <?php $this->print_column_headers( false ); ?>
            </tr>
            </tfoot>

        </table>
		<?php
		$this->display_tablenav( 'bottom' );
    }
    
    /**
	 * Generate the table navigation above or below the table
	 *
	 * @since 1.0
	 */
	protected function display_tablenav( $which ) {
        if ( 'top' === $which  
            && isset( $this->_args['plural'] ) // expected in PHP 7.4, shows a notice otherwise
        ) { 
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}
		?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <?php if ( $this->has_items() ) : ?>
            <div class="alignleft actions bulkactions">
                <?php $this->bulk_actions( $which ); ?>
            </div>
                <?php
            endif;
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>

            <br class="clear" />
        </div>
		<?php
	}
    
    /**
     * Handles deleting entries record either when the delete link is clicked 
     * or when a group of records is checked 
     * and the delete option is selected from the bulk action.
     *
     * @since 1.0
	 */
    public function process_bulk_action() {

        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Insufficient privileges!', 'quizly' ) );
        }

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'qy_o_delete_entry' ) ) {
				wp_die( __( 'Ah, this is forbidden, I am afraid.', 'quizly' ) );
			}
			else {
				self::delete_entry( absint( $_GET['entry'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_redirect( esc_url_raw( add_query_arg() ) );
				exit();
			}

        }

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_entry( $id );
			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            wp_redirect( esc_url_raw( add_query_arg() ) );
			exit();
        }
	}
}

/**
 * Use the customized WP_List_Table in a 'Log' plugin menu page
 *
 * @since 1.0
 */
class Quiz_Log {

    static $instance;
    private $table;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'qy_o_log_menu' ) );
        add_filter( 'set-screen-option', array( $this, 'qy_o_set_screen' ), 10, 3 );
        add_action( 'init', array( $this, 'qy_o_output_buffer' ) );
    }

    /**
    * Add the "Log" plugin submenu item.
    *
    * @since 1.0
    */
    public function qy_o_log_menu() {

        // global $hook;

        $hook = add_submenu_page(
            'edit.php?post_type=qy_o_quiz',
            __( 'Log', 'quizly' ),
            __( 'Log', 'quizly' ),
            'manage_options',
            'qy-o-entries',
            array( $this, 'qy_o_log_page' )
        );
        
        add_action( "load-$hook", array( $this, 'qy_o_screen_option' ) );
    }

    /**
    * Set screen options for the "Quiz Log" page
    *
    * @since 1.0
    */
    public function qy_o_screen_option() {

        $screen = get_current_screen();
    
        // get out of here if we are not on our settings page
        if( !is_object($screen) )
            return;
    
        $option = 'per_page';
        $args = [
            'label' => __( 'Entries', 'quizly' ),
            'default' => 5,
            'option' => 'entries_per_page'
        ];
    
        add_screen_option( $option, $args );
    
        $this->table = new Quiz_Log_Table();
    }

    /**
    * Render the "Quiz Log" page
    *
    * @since 1.0
    */
    public function qy_o_log_page() {
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php _e( 'Quiz Log', 'quizly' ); ?></h1>
            <button id="qy-o-download-emails" class="page-title-action" title="<?php _e( 'Download a CSV file with submitted email addresses', 'quizly' ); ?>"><span class="dashicons dashicons-download"></span><?php _e( 'Emails CSV', 'quizly' ); ?></button>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-3">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php
                                $this->table->prepare_items();
                                $this->table->display();
                                ?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
    <?php
    }

    /**
    * Set the screen options
    *
    * @since 1.0
    */
    public function qy_o_set_screen( $status, $option, $value ) {
        return $value;
    }

    /**
    * Prevent the "headers already sent" after bulk delete operations.
    *
    * @since 1.0
    */
    public function qy_o_output_buffer() { 
        ob_start();
    }

    /**
    * Singleton instance
    *
    * @since 1.0
    */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
    
}

Quiz_Log::get_instance();  