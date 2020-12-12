<?php
  
class ShortcodeSharedFilesSearch {

  public static function shared_files_search($atts = [], $content = null, $tag = '') {

    // normalize attribute keys, lowercase
    $atts = array_change_key_case( (array) $atts, CASE_LOWER);
    $s = get_option('shared_files_settings');

    $layout = '';
    
    if (isset($atts['layout'])) {
      $layout = $atts['layout'];
    } elseif (isset($s['layout']) && $s['layout']) {
      $layout = $s['layout'];
    }
  
    $html = '';

    $html .= SharedFilesHelpers::initLayout($s);
    
    if (isset($atts['not_sorted_by_categories'])) {

      $html .= '<div class="shared-files-container ' . ($layout ? 'shared-files-' . $layout : '') . '">';  
      $html .= '<div id="shared-files-search">';

      $html .= '<div class="shared-files-search-form-container shared-files-search-form-container-all-files"><div class="shared-files-search-input-container"><input type="text" id="search-files" class="shared-files-search-files-v2" placeholder="' . __('Search files...', 'shared-files') . '"></div>';
      
      $html .= '<hr class="clear" /></div>';

      $filetypes = SharedFilesHelpers::getFiletypes();
      $external_filetypes = SharedFilesHelpers::getExternalFiletypes();
  
      $html .= '<div id="shared-files-files-found"></div>';
      $html .= '<span id="shared-files-one-file-found">' . __('file found.', 'shared-files') . '</span><span id="shared-files-more-than-one-file-found">' . __('files found.', 'shared-files') . '</span>';

      $html .= '<div id="shared-files-nothing-found">';
      $html .= __('No files found.', 'shared-files');
      $html .= '</div>';

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

      $wpb_all_query_all_files = new WP_Query(array(
        'post_type' => 'shared_file',
        'post_status' => 'publish',
        'posts_per_page' => -1,

        'orderby' => SharedFilesHelpers::getOrderBy($atts),
        'order' => SharedFilesHelpers::getOrder($atts),
        'meta_key' => SharedFilesHelpers::getMetaKey($atts),

        'meta_query' => $meta_query_hide_not_public,
      ));

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

    } else {

      // Sorted by category

      $html .= '<div class="shared-files-container ' . ($layout ? 'shared-files-' . $layout : '') . '">';  
      $html .= '<div id="shared-files-search">';

      $html .= '<div class="shared-files-search-form-container shared-files-search-form-container-all-files"><div class="shared-files-search-input-container"><input type="text" id="search-files" class="shared-files-search-all-files" placeholder="' . __('Search files...', 'shared-files') . '"></div>';
      
      $html .= '<hr class="clear" /></div>';

      $filetypes = SharedFilesHelpers::getFiletypes();
      $external_filetypes = SharedFilesHelpers::getExternalFiletypes();
  
      $html .= '<div id="shared-files-files-found"></div>';
      $html .= '<span id="shared-files-one-file-found">' . __('file found.', 'shared-files') . '</span><span id="shared-files-more-than-one-file-found">' . __('files found.', 'shared-files') . '</span>';

      $html .= '<div id="shared-files-nothing-found">';
      $html .= __('No files found.', 'shared-files');
      $html .= '</div>';
      
      $html .= '<ul class="shared-files-all-files-and-categories">';

      if (wp_count_terms('shared-file-category') > 1) {

        $terms = get_terms(array(
          'taxonomy'   => 'shared-file-category',
          'hide_empty' => true,
        ));
  
        foreach ($terms as $term) {
          $html .= '<li>';
          $html .= '<h4>' . $term->name . '</h4>';

          $term_slug = $term->slug;
    
          $wpb_all_query = new WP_Query(array(
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
            
          ));

          if (isset($wpb_all_query) && $wpb_all_query->have_posts()):

            $html .= '<ul class="shared-files-in-category">';

            while ($wpb_all_query->have_posts()): $wpb_all_query->the_post();
      
              $id = get_the_id();
              $c = get_post_custom($id);
      
              $external_url = isset($c['_sf_external_url']) ? $c['_sf_external_url'][0] : '';
              $filetype = '';
      
              $imagefile = SharedFilesHelpers::getImageFile($id, $external_url);
              $hide_description = isset($atts['hide_description']) ? $atts['hide_description'] : '';
      
              $html .= SharedFilesPublicHelpers::fileListItem($c, $imagefile, $hide_description);
      
            endwhile;

            $html .= '</ul>';

          endif;

          $html .= '</li>';

        }

      }

      $html .= '</ul>';
      
    }
  
    $html .= '</div></div>';

    wp_reset_postdata();

    return $html;

  }

}
