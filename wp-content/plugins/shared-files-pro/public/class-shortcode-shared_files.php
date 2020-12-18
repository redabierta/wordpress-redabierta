<?php
  
class ShortcodeSharedFiles {

  /**
   * Search view embeddable via shortcode.
   *
   * @since    1.0.0
   */
  public static function shared_files($atts = [], $content = null, $tag = '') {

    $post_id = get_the_id();

    // normalize attribute keys, lowercase
    $atts = array_change_key_case( (array) $atts, CASE_LOWER);
    $s = get_option('shared_files_settings');

    $limit_posts = 0;

    if (isset($atts['limit'])) {
      $limit_posts = (int) $atts['limit'];
    }

    $meta_query_hide_not_public = array('relation' => 'OR');

    $meta_query_hide_not_public[] = array(
			'key'		  => '_sf_not_public',
			'compare'	=> '=',
			'value'   => ''
		);

    $meta_query_hide_not_public[] = array(
			'key'		  => '_sf_not_public',
			'compare'	=> 'NOT EXISTS',
		);

//    $meta_query_hide_not_public = array();

    $html = '';

    if (isset($atts['file_upload']) && (is_user_logged_in() || !isset($s['only_logged_in_users_can_add_files']))) {

      if (isset($atts['only_uploaded_files'])) {
    
        $meta_query_hide_not_public = array('relation' => 'AND');
  
        $meta_query_hide_not_public[] = array(
    			'key'		  => '_sf_embed_post_id',
    			'compare'	=> '=',
    			'value'   => $post_id
    		);
  
        $meta_query_hide_not_public[] = array(
    			'key'		  => '_sf_not_public',
    			'compare' => 'EXISTS'
    		);

      } else {

        $meta_query_hide_not_public = array('relation' => 'OR');
  
        $meta_query_hide_not_public[] = array(
    			'key'		  => '_sf_embed_post_id',
    			'compare'	=> '=',
    			'value'   => $post_id
    		);

        $meta_query_hide_not_public[] = array(
    			'key'		  => '_sf_not_public',
    			'compare'	=> '=',
    			'value'   => ''
    		);
  
        $meta_query_hide_not_public[] = array(
    			'key'		  => '_sf_not_public',
    			'compare' => 'NOT EXISTS'
    		);
    		
      }

      $html .= SharedFilesFileUpload::fileUploadMarkup($atts);
    }

    if (isset($atts['file_id'])) {

      $file_id = (int) $atts['file_id'];
    
      $html .= SharedFilesHelpers::initLayout($s);
      
      $wpb_all_query = new WP_Query(array(
        'post_type' => 'shared_file',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'p' => $file_id
      ));
  
      $filetypes = SharedFilesHelpers::getFiletypes();
      $external_filetypes = SharedFilesHelpers::getExternalFiletypes();
    
      $html .= '<div id="shared-files-search">';
      $html .= '<ul id="myList">';
  
      if ($wpb_all_query->have_posts()):
        while ($wpb_all_query->have_posts()): $wpb_all_query->the_post();
  
          $id = get_the_id();
          $c = get_post_custom($id);
  
          $external_url = isset($c['_sf_external_url']) ? $c['_sf_external_url'][0] : '';
          $filetype = '';
  
          $imagefile = SharedFilesHelpers::getImageFile($id, $external_url);
          $hide_description = isset($atts['hide_description']) ? $atts['hide_description'] : '';
        
          $html .= SharedFilesPublicHelpers::fileListItem($c, $imagefile, $hide_description, 2);
  
        endwhile;
        
        wp_reset_postdata();
        
      else:
        $html .= '<div class="sf_error">' . __('File not found', 'shared-files') . '</div>';
      endif;
  
      $html .= '</ul>';
    
      $html .= '</div>';

      return $html;

    } else {
  
      $layout = '';
      
      $tag_slug = '';
      
      if (isset($_GET['sf_tag'])) {
        $tag_slug = $_GET['sf_tag'];
      }
      
      if (isset($atts['layout'])) {
        $layout = $atts['layout'];
      } elseif (isset($s['layout']) && $s['layout']) {
        $layout = $s['layout'];
      }
    
      $html .= SharedFilesHelpers::initLayout($s);
      
      $type = 'basic';
      
      if (isset($atts['category'])) {
        $type = 'category';
      }
      
      $html .= '<div class="shared-files-container shared-files-type-' . $type . ' ' . ($layout ? 'shared-files-' . $layout : '') . '">';  
      $html .= '<div id="shared-files-search">';

      if (!isset($atts['hide_search'])) {
        
        $html .= '<div class="shared-files-search-form-container"><form class="shared-files-ajax-form"><div class="shared-files-search-input-container"><input type="text" id="search-files" class="shared-files-search-files" placeholder="' . __('Search files...', 'shared-files') . '" value="" /></div>';
      
        if (wp_count_terms('shared-file-category') > 1) {

          $html .= '<div class="shared-files-category-select-container">';
          
          if (isset($atts['category'])) {

            $cat = get_term_by('slug', $atts['category'], 'shared-file-category');

            if ($cat) {
              $terms = get_terms(array(
                'taxonomy'   => 'shared-file-category',
                'hide_empty' => true,
                'parent' => $cat->term_id
              ));
            }
          
            if (!isset($atts['hide_category_dropdown']) && sizeof($terms) > 0) {

              $html .= '<select name="sf_category" class="shared-files-category-select select_v2" onchange="document.location.href=\'./?c=\' + this.options[this.selectedIndex].value">';
        
              $html .= '<option value="all_files">' . __('Choose category', 'shared-files') . '</option>';
        
              foreach ($terms as $term) {
                $html .= '<option value="' . $term->slug . '" ' . (isset($_GET['c'] ) && $_GET['c'] == $term->slug ? 'selected' : '') . '>'. $term->name .'</option>';
              }
        
              $html .= '</select>';
              
            }
          
          } elseif (!isset($atts['only_uploaded_files']) && !isset($atts['hide_category_dropdown'])) {

            $cat_selected = isset($_GET['sf_category']) ? $_GET['sf_category'] : 0;
            
            $args = array(
                      'taxonomy' => 'shared-file-category',
                      'name' => 'sf_category',
                      'show_option_all' => __('Choose category', 'shared-files'),
                      'hierarchical' => true,
                      'class' => 'shared-files-category-select select_v2',
                      'echo' => false,
                      'value_field' => 'slug',
                      'selected' => $cat_selected
                    );
                          
            $html .= wp_dropdown_categories($args);
            
          }
          
          $html .= '</div>';

        }
    
        $html .= '<hr class="clear" /></form></div>';

      }

      if (isset($atts['category'])) {
        $term_slug = sanitize_title($atts['category']);

        $posts_per_page = isset($s['pagination']) && $s['pagination'] ? (int) $s['pagination'] : 20;
        $paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;     

        if ($limit_posts) {
          $posts_per_page = $limit_posts;
        }
  
        $wpb_all_query = new WP_Query(array(
          'post_type' => 'shared_file',
          'post_status' => 'publish',

          'posts_per_page' => $posts_per_page,
          'paged' => $paged,

          'tag' => $tag_slug,

          'tax_query' => array(
            array(
              'taxonomy' => 'shared-file-category',
              'field' => 'slug',
              'terms' => $term_slug,
              'include_children' => true
            )
          ),

          'orderby' => SharedFilesHelpers::getOrderBy($atts),
          'order' => SharedFilesHelpers::getOrder($atts),
          'meta_key' => SharedFilesHelpers::getMetaKey($atts),

          'meta_query' => $meta_query_hide_not_public,
        ));

        $wpb_all_query_all_files = new WP_Query(array(
          'post_type' => 'shared_file',
          'post_status' => 'publish',
          'posts_per_page' => -1,
          'tax_query' => array(
            array (
              'taxonomy' => 'shared-file-category',
              'field' => 'slug',
              'terms' => $term_slug,
              'include_children' => true
            )
          ),

          'orderby' => SharedFilesHelpers::getOrderBy($atts),
          'order' => SharedFilesHelpers::getOrder($atts),
          'meta_key' => SharedFilesHelpers::getMetaKey($atts),

          'meta_query' => $meta_query_hide_not_public,
        ));

      } else if (isset($_GET['sf_category']) && $_GET['sf_category'] != '0') {
  
        $term_slug = sanitize_title($_GET['sf_category']);
  
        $wpb_all_query = new WP_Query(array(
          'post_type' => 'shared_file',
          'post_status' => 'publish',
          'posts_per_page' => -1,

          'tag' => $tag_slug,

          'tax_query' => array(
            array(
              'taxonomy' => 'shared-file-category',
              'field' => 'slug',
              'terms' => $term_slug,
              'include_children' => true
            )
          ),

          'orderby' => SharedFilesHelpers::getOrderBy($atts),
          'order' => SharedFilesHelpers::getOrder($atts),
          'meta_key' => SharedFilesHelpers::getMetaKey($atts),

          'meta_query' => $meta_query_hide_not_public,
        ));

        $wpb_all_query_all_files = new WP_Query(array(
          'post_type' => 'shared_file',
          'post_status' => 'publish',
          'posts_per_page' => -1,
          'tax_query' => array(
            array(
              'taxonomy' => 'shared-file-category',
              'field' => 'slug',
              'terms' => $term_slug,
              'include_children' => true
            )
          ),

          'orderby' => SharedFilesHelpers::getOrderBy($atts),
          'order' => SharedFilesHelpers::getOrder($atts),
          'meta_key' => SharedFilesHelpers::getMetaKey($atts),

          'meta_query' => $meta_query_hide_not_public,
        ));
          
      } else if (isset($_GET['c']) && $_GET['c'] != 'all_files') {
  
        $term_slug = sanitize_title($_GET['c']);
  
        $wpb_all_query = new WP_Query(array(
          'post_type' => 'shared_file',
          'post_status' => 'publish',
          'posts_per_page' => -1,

          'tag' => $tag_slug,

          'tax_query' => array(
            array(
              'taxonomy' => 'shared-file-category',
              'field' => 'slug',
              'terms' => $term_slug,
              'include_children' => true
            )
          ),

          'orderby' => SharedFilesHelpers::getOrderBy($atts),
          'order' => SharedFilesHelpers::getOrder($atts),
          'meta_key' => SharedFilesHelpers::getMetaKey($atts),

          'meta_query' => $meta_query_hide_not_public,
        ));

        $wpb_all_query_all_files = new WP_Query(array(
          'post_type' => 'shared_file',
          'post_status' => 'publish',
          'posts_per_page' => -1,
          'tax_query' => array(
            array(
              'taxonomy' => 'shared-file-category',
              'field' => 'slug',
              'terms' => $term_slug,
              'include_children' => true
            )
          ),

          'orderby' => SharedFilesHelpers::getOrderBy($atts),
          'order' => SharedFilesHelpers::getOrder($atts),
          'meta_key' => SharedFilesHelpers::getMetaKey($atts),

          'meta_query' => $meta_query_hide_not_public,
        ));
  
      } else {
        
        $paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;     
        $posts_per_page = isset($s['pagination']) && $s['pagination'] ? (int) $s['pagination'] : 20;
      
        if ($limit_posts) {
          $posts_per_page = $limit_posts;
        }
        
        $wpb_all_query = new WP_Query(array(
          'post_type' => 'shared_file',
          'post_status' => 'publish',
          'posts_per_page' => $posts_per_page,

          'tag' => $tag_slug,

          'orderby' => SharedFilesHelpers::getOrderBy($atts),
          'order' => SharedFilesHelpers::getOrder($atts),
          'meta_key' => SharedFilesHelpers::getMetaKey($atts),

          'meta_query' => $meta_query_hide_not_public,
        ));

        $wpb_all_query_all_files = new WP_Query(array(
          'post_type' => 'shared_file',
          'post_status' => 'publish',
          'posts_per_page' => -1,

          'orderby' => SharedFilesHelpers::getOrderBy($atts),
          'order' => SharedFilesHelpers::getOrder($atts),
          'meta_key' => SharedFilesHelpers::getMetaKey($atts),

          'meta_query' => $meta_query_hide_not_public,
        ));

      }

      $filetypes = SharedFilesHelpers::getFiletypes();
      $external_filetypes = SharedFilesHelpers::getExternalFiletypes();
  
      $html .= '<div id="shared-files-files-found"></div>';
      $html .= '<span id="shared-files-one-file-found">' . __('file found.', 'shared-files') . '</span><span id="shared-files-more-than-one-file-found">' . __('files found.', 'shared-files') . '</span>';

      $hide_description = isset($atts['hide_description']) ? $atts['hide_description'] : '';

      if ($tag_slug) {
        $html .= SharedFilesHelpers::tagTitleMarkup($tag_slug, 'shared-files-non-ajax', $hide_description);
      }

      if ($wpb_all_query->have_posts()):

        $html .= '<ul id="myList" class="shared-files-ajax-list">';

        while ($wpb_all_query->have_posts()): $wpb_all_query->the_post();
  
          $id = get_the_id();
          $c = get_post_custom($id);
  
          $external_url = isset($c['_sf_external_url']) ? $c['_sf_external_url'][0] : '';
          $filetype = '';
  
          $imagefile = SharedFilesHelpers::getImageFile($id, $external_url);

          if (isset($atts['category']) || isset($atts['file_upload'])) {
            $html .= SharedFilesPublicHelpers::fileListItem($c, $imagefile, $hide_description, 0);
          } else {
            $html .= SharedFilesPublicHelpers::fileListItem($c, $imagefile, $hide_description, 1);
          }

        endwhile;

        $html .= '</ul>';

      else:
      
        $html .= '<div class="shared-files-files-not-found">' . __('No published files found. You should add files first from WP admin: Shared Files / File Management.', 'shared-files') . '</div>';
        
      endif;

      $html .= '<ul id="shared-files-all-files">';
  
      if (isset($wpb_all_query_all_files) && $wpb_all_query_all_files->have_posts()):
        while ($wpb_all_query_all_files->have_posts()): $wpb_all_query_all_files->the_post();
  
          $id = get_the_id();
          $c = get_post_custom($id);
  
          $external_url = isset($c['_sf_external_url']) ? $c['_sf_external_url'][0] : '';
          $filetype = '';
          $hide_description = isset($atts['hide_description']) ? $atts['hide_description'] : '';
  
          $imagefile = SharedFilesHelpers::getImageFile($id, $external_url);

          $html .= SharedFilesPublicHelpers::fileListItem($c, $imagefile, $hide_description);
  
        endwhile;
      endif;
  
      $html .= '</ul>';

      $pagination_args = array(
          'base'         => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
          'total'        => $wpb_all_query->max_num_pages,
          'current'      => max(1, get_query_var('paged')),
          'format'       => '?paged=%#%',
          'show_all'     => true,
          'type'         => 'plain',
          'prev_next'    => false,
          'add_args'     => false,
          'add_fragment' => '',
        );

      if (!$limit_posts) {

        $html .= '<div id="shared-files-pagination" class="shared-files-pagination">';
  
        if (paginate_links($pagination_args)) {
          $html .= '<span class="shared-files-more-files">' . __('Browse more files:', 'shared-files') . '</span>' .
          paginate_links($pagination_args);
        }
  
        $html .= '</div>';
        
      }
  
      $html .= '<div id="shared-files-nothing-found">';
      $html .= __('No files found.', 'shared-files');
      $html .= '</div>';
  
      $html .= '</div></div><hr class="clear" />';

      wp_reset_postdata();
  
      return $html;
    }
  }

}
