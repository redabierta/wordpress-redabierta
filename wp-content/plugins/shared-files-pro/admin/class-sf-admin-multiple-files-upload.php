<?php

class SharedFilesAdminMultipleFilesUpload {

  public function file_upload($request) {

    if (isset($_POST) && isset($_POST['_sf_insert_multiple_files'])) {

      $num = sizeof($_FILES['_sf_files']['name']);
      
      for ($i = 0; $i < $num; $i++) {

        $filename = $_FILES['_sf_files']['name'][$i];
  
        if (isset($_FILES['_sf_files']['tmp_name'][$i]) && $_FILES['_sf_files']['tmp_name'][$i]) {

          if (!isset($_POST['secret_code']) || !wp_verify_nonce($_POST['secret_code'], 'sf_insert_multiple_files')) {
            wp_die('Error in processing form data.');
          }
  
          $new_post = array( 'post_type'    => 'shared_file',
                              'post_status'  => 'publish',
                              'post_title'   => '', 
                              'post_content' => '');
          
          $id = wp_insert_post($new_post);

          update_post_meta($id, '_sf_description', '');

          if (isset($_POST['shared-file-category']) && $_POST['shared-file-category']) {
            wp_set_object_terms($id, $_POST['shared-file-category'], 'shared-file-category');
          }
  
          // Get the file type of the upload
          $arr_file_type = wp_check_filetype(basename($_FILES['_sf_files']['name'][$i]));
          $uploaded_type = $arr_file_type['type'];
  
          add_filter('upload_dir', [ $this, 'set_upload_dir' ]);
  
          // Use the WordPress API to upload the file
          $upload = wp_upload_bits($_FILES['_sf_files']['name'][$i], null, file_get_contents($_FILES['_sf_files']['tmp_name'][$i]));
  
          if (isset($upload['error']) && $upload['error']) {
            wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
          }
  
          remove_filter('upload_dir', [$this, 'set_upload_dir']);
  
          add_post_meta($id, '_sf_file', $upload);
          update_post_meta($id, '_sf_file', $upload);
  
          $filename = substr(strrchr($upload['file'], "/"), 1);
  
          update_post_meta($id, '_sf_filename', $filename);
          update_post_meta($id, '_sf_filesize', $_FILES['_sf_files']['size'][$i]);
          update_post_meta($id, '_sf_load_cnt', 0);
          update_post_meta($id, '_sf_bandwidth_usage', 0);
          update_post_meta($id, '_sf_file_added', current_time('Y-m-d H:i:s'));
  
          $my_post = array(
            'ID'           => $id,
            'post_title'   => $filename,
          );
  
          wp_update_post($my_post);
                  
        } else {
          
          $error_msg = __('A file was not successfully uploaded. Please note the maximum file size.', 'shared_files');
          wp_die($error_msg);
          
        }
      } 
      
      wp_redirect(get_admin_url(null, 'edit.php?post_type=shared_file&_sf_success=1'));
      exit;

    }


    return $request;
  }

  public function add_multiple_files_view() {

    $screen = get_current_screen();
            
    if ($screen->id == 'edit-shared_file' && $screen->action == '') {

      $taxonomy_slug = 'shared-file-category';

      ?>
      <script>
      jQuery('.post-type-shared_file .wp-header-end').after(function() {
  
          let sf_button_markup = '<div class="shared-files-multiple-files-upload-container">' + 
                              '<h2><?= __('Add multiple files', 'shared-files') ?></h2>' + 
                              '<form method="post" action="<?= get_home_url() ?>" enctype="multipart/form-data">' + 
                              '<input type="file" name="_sf_files[]" multiple required><br /><br />' + 
                              '<label>Category:</label>' + 

                              `<?php
                              if (get_taxonomy($taxonomy_slug)) {
                          
                                wp_dropdown_categories([
                                  'show_option_all' => ' ',
                                  'hide_empty' => 0,
                                  'hierarchical' => 1,
                                  'show_count' => 1,
                                  'orderby' => 'name',
                                  'name' => $taxonomy_slug,
                                  'value_field' => 'slug',
                                  'taxonomy' => $taxonomy_slug,
                                ]);
                                
                              }
                              ?><br /><br />` + 
                              `<?php wp_nonce_field('sf_insert_multiple_files', 'secret_code') ?>` + 
                              '<input type="hidden" name="_sf_insert_multiple_files" value="1" />' + 
                              '<input type="submit" name="submit" value="Upload">' + 
                              '</form>' + 
                              '</div>';
  
          return sf_button_markup;
  
      });

      jQuery('.post-type-shared_file .page-title-action').after(function() {
        return '<a href="" class="page-title-action shared-files-add-multiple-files">Add multiple files</div>';
      });

      </script>


      <?php if (isset($_GET) && isset($_GET['_sf_success']) && !isset($_GET['ids'])): ?>
        <script>
        jQuery('.post-type-shared_file .wp-header-end').after(function() {
            let sf_ok_markup = '<div class="shared-files-multiple-files-added-container"><div class="shared-files-multiple-files-added"><?= __('File(s) successfully added.', 'shared-files') ?></div></div>';
            return sf_ok_markup;
        });
        </script>
      <?php endif; ?>

      <?php
    }


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
