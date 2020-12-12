<?php

class SharedFilesFileUpload {
  
  public static function fileUploadMarkup($atts) {

    $html = '';

    $post_id = get_the_id();
    $post_title = get_the_title();

    if (isset($_POST) && isset($_POST['shared-files-upload'])) {
      $html .= '<div class="shared-files-upload-complete">' . __('File successfully uploaded.', 'shared-files') . '</div>';      
    } elseif (isset($_GET) && isset($_GET['_sf_delete_file']) && isset($_GET['sc'])) {
      $html .= '<div class="shared-files-file-deleted">' . __('File successfully deleted.', 'shared-files') . '</div>';      
    }

    $html .= '<div class="sf-public-file-upload-container">';
    $html .= '<form method="post" enctype="multipart/form-data">';

    $html .= wp_nonce_field('sf_insert_file', 'secret_code');
    
    $html .= '<input name="shared-files-upload" value="1" type="hidden" />';
    $html .= '<input name="_sf_embed_post_id" value="' . $post_id . '" type="hidden" />';
    $html .= '<input name="_sf_embed_post_title" value="' . $post_title . '" type="hidden" />';

    $html .= '<input type="file" id="sf_file" name="_sf_file" value="" size="25" required /><br />';

    if (isset($atts['category'])) {
      
      $category_slug = sanitize_title_with_dashes($atts['category']);
      $html .= '<input name="shared-file-category" type="hidden" value="' . $category_slug . '" />';
      
    } else {

      $taxonomy_slug = 'shared-file-category';
  
      if (get_taxonomy($taxonomy_slug)) {
  
        $html .= wp_dropdown_categories([
          'show_option_all' => ' ',
          'hide_empty' => 0,
          'hierarchical' => 1,
          'show_count' => 1,
          'orderby' => 'name',
          'name' => $taxonomy_slug,
          'value_field' => 'slug',
          'taxonomy' => $taxonomy_slug,
          'echo' => 0,
          'class' => 'select_v2',
          'show_option_all' => __('Choose category', 'shared-files')
        ]);
        
      }
      
    }

    $html .= '<span>' . __('Description', 'shared-files') . '</span>';
    $html .= '<textarea name="_sf_description"></textarea>';

    $html .= '</br><input type="submit" value="Subir" class="sf-public-file-upload-submit" />';

    $html .= '</form>';
    $html .= '</div>';
    
    return $html;

  }

  public function file_upload($request) {

    if (isset($_GET) && isset($_GET['_sf_delete_file'])) {

      $user = wp_get_current_user();

      if (!isset($_GET['sc']) || !wp_verify_nonce($_GET['sc'], 'sf_delete_file_' . $user->ID)) {
        wp_die('Error in processing form data.');
      }

      $file_id = (int) $_GET['_sf_delete_file'];
      $file = get_post($file_id);
      $post_type = get_post_type($file_id);
      $c = get_post_custom($file_id);

      if ($file && $user->ID == $c['_sf_user_id'][0] && $post_type == 'shared_file') {
        wp_delete_post($file_id);
      }

    }

    if (isset($_POST) && isset($_POST['shared-files-upload']) && isset($_FILES) && isset($_FILES['_sf_file']['name'])) {

      if (isset($_FILES['_sf_file']['tmp_name']) && $_FILES['_sf_file']['tmp_name']) {

        if (!isset($_POST['secret_code']) || !wp_verify_nonce($_POST['secret_code'], 'sf_insert_file')) {
          wp_die('Error in processing form data.');
        }

        $new_post = array( 'post_type'    => 'shared_file',
                            'post_status'  => 'publish',
                            'post_title'   => '', 
                            'post_content' => '');
        
        $id = wp_insert_post($new_post);
  
        update_post_meta($id, '_sf_frontend_uploader', 1);
        update_post_meta($id, '_sf_not_public', 1);
        update_post_meta($id, '_sf_embed_post_id', $_POST['_sf_embed_post_id']);
        update_post_meta($id, '_sf_embed_post_title', $_POST['_sf_embed_post_title']);

        if (isset($_POST['shared-file-category'])) {
          $cat_slug = $_POST['shared-file-category'];
          $cat = get_term_by('slug', $cat_slug, 'shared-file-category');

          if ($cat) {
            wp_set_object_terms($id, $cat->term_id, 'shared-file-category');
          }
        }

        if (is_user_logged_in()) {
          $user = wp_get_current_user();
          update_post_meta($id, '_sf_user_id', $user->ID);
        }
  
        if (isset($_POST['_sf_description']) && $_POST['_sf_description']) {
          $description = wp_strip_all_tags( balanceTags(wp_kses_post($_POST['_sf_description']), 1) );
          update_post_meta($id, '_sf_description', $description);
        } else {
          update_post_meta($id, '_sf_description', '');
        }

        // Get the file type of the upload
        $arr_file_type = wp_check_filetype(basename($_FILES['_sf_file']['name']));
        $uploaded_type = $arr_file_type['type'];

        add_filter('upload_dir', [ $this, 'set_upload_dir' ]);

        // Use the WordPress API to upload the file
        $upload = wp_upload_bits($_FILES['_sf_file']['name'], null, file_get_contents($_FILES['_sf_file']['tmp_name']));

        if (isset($upload['error']) && $upload['error']) {
          wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
        }

        remove_filter('upload_dir', [$this, 'set_upload_dir']);

        add_post_meta($id, '_sf_file', $upload);
        update_post_meta($id, '_sf_file', $upload);

        $filename = substr(strrchr($upload['file'], "/"), 1);

        update_post_meta($id, '_sf_filename', $filename);
        update_post_meta($id, '_sf_filesize', $_FILES['_sf_file']['size']);
        update_post_meta($id, '_sf_load_cnt', 0);
        update_post_meta($id, '_sf_bandwidth_usage', 0);
        update_post_meta($id, '_sf_file_added', current_time('Y-m-d H:i:s'));

        $my_post = array(
          'ID'           => $id,
          'post_title'   => $filename,
        );

        wp_update_post($my_post);
      
      } else {
        
        $error_msg = __('File was not successfully uploaded. Please note the maximum file size.', 'shared_files');
        wp_die($error_msg);
        
      }

    }
    
    return $request;
  }

  /**
   * Set the custom upload directory.
   *
   * @since    1.0.0
   */
  public function set_upload_dir($dir) {

    return array(
      'path'   => $dir['basedir'] . '/shared-files',
      'url'    => $dir['baseurl'] . '/shared-files',
      'subdir' => '/shared-files',
    ) + $dir;
  }
    
}
