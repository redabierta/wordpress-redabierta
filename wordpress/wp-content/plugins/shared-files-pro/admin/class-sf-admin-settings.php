<?php

class Shared_Files_Settings {

  public function shared_files_add_admin_menu() {
    add_options_page('Shared Files Settings', 'Shared Files', 'manage_options', 'shared-files', array($this, 'settings_page'));
  }

  public function shared_files_settings_init() {

    register_setting('shared-files', 'shared_files_settings');

    add_settings_field(
      'shared-files-icon_set',
      __('Icon set', 'shared-files'),
      array($this, 'icon_set_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-icon_set', 'field_name' => 'icon_set')
    );

    add_settings_section(
      'shared-files_section_general',
      __('General settings', 'shared-files'),
      array($this, 'shared_files_settings_general_section_callback'),
      'shared-files'
    );

    add_settings_field(
      'shared-files-hide_date_from_card',
      __('Hide file date / publish date from card', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-hide_date_from_card', 'field_name' => 'hide_date_from_card')
    );

    add_settings_field(
      'shared-files-hide_file_size_from_card',
      __('Hide file size from card', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-hide_file_size_from_card', 'field_name' => 'hide_file_size_from_card')
    );

    add_settings_field(
      'shared-files-preview_service',
      __('Preview service', 'shared-files'),
      array($this, 'preview_service_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-preview_service', 'field_name' => 'preview_service')
    );

    add_settings_field(
      'shared-files-hide_preview_button',
      __('Hide preview button', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-hide_preview_button', 'field_name' => 'hide_preview_button')
    );

    add_settings_field(
      'shared-files-hide_file_type_icon_from_card',
      __('Hide file type icon from card', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-hide_file_type_icon_from_card', 'field_name' => 'hide_file_type_icon_from_card')
    );

    add_settings_field(
      'shared-files-show_download_button',
      __('Show download button on card', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-show_download_button', 'field_name' => 'show_download_button')
    );
    
    add_settings_field(
      'shared-files-textarea_for_file_description',
      __('Use textarea for file description (instead of rich text editor)', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-textarea_for_file_description', 'field_name' => 'textarea_for_file_description')
    );

    add_settings_field(
      'shared-files-order_by',
      __('Order by', 'shared-files'),
      array($this, 'order_by_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-order_by', 'field_name' => 'order_by')
    );

    add_settings_field(
      'shared-files-order',
      __('Order', 'shared-files'),
      array($this, 'order_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-order', 'field_name' => 'order')
    );

    add_settings_field(
      'shared-files-hide_category_name_from_card',
      __('Hide category name(s) from card', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-hide_category_name_from_card', 'field_name' => 'hide_category_name_from_card')
    );

    add_settings_field(
      'shared-files-only_logged_in_users_can_add_files',
      __('Only logged in users can add files using the front-end uploader', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-only_logged_in_users_can_add_files', 'field_name' => 'only_logged_in_users_can_add_files')
    );

    add_settings_field(
      'shared-files-pagination',
      __('Pagination', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-pagination', 'field_name' => 'pagination', 'placeholder' => '20')
    );


    add_settings_field(
      'shared-files-file_open_method',
      __('File opening method', 'shared-files'),
      array($this, 'file_open_method_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-file_open_method', 'field_name' => 'file_open_method')
    );

    add_settings_field(
      'shared-files-wp_engine_compatibility_mode',
      __('WP Engine compatibility mode', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-wp_engine_compatibility_mode', 'field_name' => 'wp_engine_compatibility_mode')
    );

    add_settings_field(
      'shared-files-wp_location',
      __('WordPress location', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_section_general',
      array('label_for' => 'shared-files-wp_location', 'field_name' => 'wp_location', 'placeholder' => '/some-dir/')
    );

    $tab = 2;

    add_settings_section(
      'shared-files_tab_' . $tab,
      '',
      array($this, 'shared_files_settings_tab_' . $tab . '_callback'),
      'shared-files'
    );

    add_settings_field(
      'shared-files-layout',
      __('Layout', 'shared-files'),
      array($this, 'layout_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-layout', 'field_name' => 'layout')
    );

    add_settings_field(
      'shared-files-card_font',
      __('Card font', 'shared-files'),
      array($this, 'card_font_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-card_font', 'field_name' => 'card_font')
    );

    add_settings_field(
      'shared-files-card_align_elements_vertically',
      __('Align elements vertically and centered (inside card)', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-card_align_elements_vertically', 'field_name' => 'card_align_elements_vertically')
    );

    add_settings_field(
      'shared-files-card_small_font_size',
      __('Small font size on card', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-card_small_font_size', 'field_name' => 'card_small_font_size')
    );

    add_settings_field(
      'shared-files-card_featured_image_as_extra',
      __('Show featured image in addition to file type icon', 'shared-files') . '<div style="font-weight: 400; font-style: italic; font-size: 12px; margin-top: 3px;">' . __('Featured image will be displayed next to file description', 'shared-files') . '</div>',
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-card_featured_image_as_extra', 'field_name' => 'card_featured_image_as_extra')
    );

    add_settings_field(
      'shared-files-card_featured_image_align',
      __('Align featured image', 'shared-files'),
      array($this, 'card_featured_image_align'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-card_featured_image_align', 'field_name' => 'card_featured_image_align')
    );

    add_settings_field(
      'shared-files-card_height',
      __('Card height in pixels', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-card_height', 'field_name' => 'card_height', 'placeholder' => '380')
    );

    add_settings_field(
      'shared-files-card_background',
      __('Card background', 'shared-files'),
      array($this, 'card_background_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-card_background', 'field_name' => 'card_background')
    );

    add_settings_field(
      'shared-files-card_background_custom_color',
      __('Card background, custom color (HEX code)', 'shared-files') . '<div style="font-weight: 400; font-style: italic; font-size: 12px; margin-top: 3px;">' . __('See', 'shared-files') . ' <a href="https://htmlcolorcodes.com/" target="_blank">htmlcolorcodes.com</a></div>',
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-card_background_custom_color', 'field_name' => 'card_background_custom_color', 'placeholder' => '')
    );

    $tab = 3;

    add_settings_section(
      'shared-files_tab_' . $tab,
      '',
      array($this, 'shared_files_settings_tab_' . $tab . '_callback'),
      'shared-files'
    );

    add_settings_field(
      'shared-files-icon_image',
      __('File type: Image', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_image', 'field_name' => 'icon_for_image', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_pdf',
      __('File type: PDF', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_pdf', 'field_name' => 'icon_for_pdf', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_ai',
      __('File type: AI', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_ai', 'field_name' => 'icon_for_ai', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_doc',
      __('File type: Doc', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_doc', 'field_name' => 'icon_for_doc', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_font',
      __('File type: Font', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_font', 'field_name' => 'icon_for_font', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_html',
      __('File type: HTML', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_html', 'field_name' => 'icon_for_html', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_mp3',
      __('File type: MP3', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_mp3', 'field_name' => 'icon_for_mp3', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_video',
      __('File type: Video', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_video', 'field_name' => 'icon_for_video', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_xlsx',
      __('File type: XLSX', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_xlsx', 'field_name' => 'icon_for_xlsx', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_pptx',
      __('File type: PPT(X)', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_pptx', 'field_name' => 'icon_for_pptx', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_zip',
      __('File type: ZIP', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_zip', 'field_name' => 'icon_for_zip', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_indd',
      __('File type: INDD', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_indd', 'field_name' => 'icon_for_indd', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_psd',
      __('File type: PSD', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_psd', 'field_name' => 'icon_for_psd', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_svg',
      __('File type: SVG', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_svg', 'field_name' => 'icon_for_svg', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_other',
      __('File type: Other files', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_other', 'field_name' => 'icon_for_other', 'placeholder' => '', 'wide' => 1)
    );

    add_settings_field(
      'shared-files-icon_youtube',
      __('YouTube-link (External URL)', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-icon_for_youtube', 'field_name' => 'icon_for_youtube', 'placeholder' => '', 'wide' => 1)
    );

    $tab = 4;

    add_settings_section(
      'shared-files_tab_' . $tab,
      '',
      array($this, 'shared_files_settings_tab_' . $tab . '_callback'),
      'shared-files'
    );

    $num = [1];
  
    foreach ($num as $n) {

      $field_title = __('Custom file type', 'shared-files') . ' ' . $n . ': ' . __('extension', 'shared-files');

      add_settings_field(
        'shared-files-custom_' . $n .'_ext',
        $field_title,
        array($this, 'input_render'),
        'shared-files',
        'shared-files_tab_' . $tab,
        array('label_for' => 'shared-files-custom_' . $n .'_ext', 'field_name' => 'custom_' . $n . '_ext', 'placeholder' => '', 'ext' => 1)
      );

      $field_title = __('Custom file type', 'shared-files') . ' ' . $n . ': ' . __('icon file', 'shared-files');
  
      add_settings_field(
        'shared-files-custom_' . $n .'_icon',
        $field_title,
        array($this, 'input_render'),
        'shared-files',
        'shared-files_tab_' . $tab,
        array('label_for' => 'shared-files-custom_' . $n .'_icon', 'field_name' => 'custom_' . $n . '_icon', 'placeholder' => '', 'wide' => 1)
      );
      
    }

    $num = [2, 3, 4, 5, 6];
  
    foreach ($num as $n) {

      $field_title = __('Custom file type', 'shared-files') . ' ' . $n . ': ' . __('extension', 'shared-files');

      add_settings_field(
        'shared-files-custom_' . $n .'_ext',
        $field_title,
        array($this, 'input_render'),
        'shared-files',
        'shared-files_tab_' . $tab,
        array('label_for' => 'shared-files-custom_' . $n .'_ext', 'field_name' => 'custom_' . $n . '_ext', 'placeholder' => '', 'ext' => 1)
      );

      $field_title = __('Custom file type', 'shared-files') . ' ' . $n . ': ' . __('icon file', 'shared-files');
  
      add_settings_field(
        'shared-files-custom_' . $n .'_icon',
        $field_title,
        array($this, 'input_render'),
        'shared-files',
        'shared-files_tab_' . $tab,
        array('label_for' => 'shared-files-custom_' . $n .'_icon', 'field_name' => 'custom_' . $n . '_icon', 'placeholder' => '', 'wide' => 1)
      );
      
    }

    $tab = 5;

    add_settings_section(
      'shared-files_tab_' . $tab,
      '',
      array($this, 'shared_files_settings_tab_' . $tab . '_callback'),
      'shared-files'
    );

    add_settings_field(
      'shared-files-send_email',
      __('Send an email notify when a file is downloaded', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-send_email', 'field_name' => 'send_email')
    );

    add_settings_field(
      'shared-files-recipient_email',
      __('Notification recipient email', 'shared-files'),
      array($this, 'input_render'),
      'shared-files',
      'shared-files_tab_' . $tab,
      array('label_for' => 'shared-files-recipient_email', 'field_name' => 'recipient_email', 'placeholder' => '')
    );


//    $tab = 5;

    add_settings_section(
      'shared-files_section_admin_list',
      '',
      array($this, 'shared_files_settings_admin_list_section_callback'),
      'shared-files'
    );

    add_settings_field(
      'shared-files-hide_limit_downloads',
      __('Hide "Limit downloads"-column', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_admin_list',
      array('label_for' => 'shared-files-hide_limit_downloads', 'field_name' => 'hide_limit_downloads')
    );

    add_settings_field(
      'shared-files-hide_file_added',
      __('Hide "File added"-column', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_admin_list',
      array('label_for' => 'shared-files-hide_file_added', 'field_name' => 'hide_file_added')
    );

    add_settings_field(
      'shared-files-hide_last_access',
      __('Hide "Last access"-column', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_admin_list',
      array('label_for' => 'shared-files-hide_last_access', 'field_name' => 'hide_last_access')
    );

    add_settings_field(
      'shared-files-hide_bandwidth_usage',
      __('Hide "Bandwidth usage"-column', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_admin_list',
      array('label_for' => 'shared-files-hide_bandwidth_usage', 'field_name' => 'hide_bandwidth_usage')
    );

    add_settings_field(
      'shared-files-hide_expiration_date',
      __('Hide "Expiration date"-column', 'shared-files'),
      array($this, 'checkbox_render'),
      'shared-files',
      'shared-files_section_admin_list',
      array('label_for' => 'shared-files-hide_expiration_date', 'field_name' => 'hide_expiration_date')
    );
  }
  
  public function checkbox_render($args) {

    if ($args['field_name']) {
      $options = get_option('shared_files_settings');
      ?>    
      <input type="checkbox" id="shared-files-<?= $args['field_name'] ?>" name="shared_files_settings[<?= $args['field_name'] ?>]" <?= isset($options[$args['field_name']]) ? 'checked="checked"' : ''; ?>>      

      <?php if ($args['field_name'] == 'wp_engine_compatibility_mode'): ?>
        <div class="email-info">
          <?= __('This should be checked if you\'re using WP Engine to host your site.', 'shared-files') ?><br /><br />
          <?= __('When this option is checked, an extra "?" is automatically added to the URLs before the filename like so: <br /><b>/shared-files/123/?this-is-a-file.pdf</b>', 'shared-files') ?><br /><br />
          <?= __('Can also be used with other hosting providers, may help solving 404 errors.', 'shared-files') ?>
        </div>
      <?php endif; ?>

      <?php
        
    }
  }

  public function input_render($args) {

    if ($args['field_name']) {
      $options = get_option('shared_files_settings');
      ?>    

      <?php if ($args['field_name'] == 'card_background_custom_color'): ?>
        # <input type="text" style="width: 100px;" class="input-field <?= isset($args['wide']) ? 'input-field-wide' : '' ?>" id="shared-files-<?= $args['field_name'] ?>" name="shared_files_settings[<?= $args['field_name'] ?>]" value="<?= isset($options[$args['field_name']]) ? $options[$args['field_name']] : ''; ?>" placeholder="<?= $args['placeholder'] ? $args['placeholder'] : '' ?>">
      <?php elseif (isset($args['ext'])): ?>
        filename.<input type="text" class="input-field <?= isset($args['wide']) ? 'input-field-wide' : '' ?>" id="shared-files-<?= $args['field_name'] ?>" name="shared_files_settings[<?= $args['field_name'] ?>]" value="<?= isset($options[$args['field_name']]) ? $options[$args['field_name']] : ''; ?>" placeholder="<?= $args['placeholder'] ? $args['placeholder'] : '' ?>" style="width: 80px;">
      <?php else: ?>
        <input type="text" class="input-field <?= isset($args['wide']) ? 'input-field-wide' : '' ?>" id="shared-files-<?= $args['field_name'] ?>" name="shared_files_settings[<?= $args['field_name'] ?>]" value="<?= isset($options[$args['field_name']]) ? $options[$args['field_name']] : ''; ?>" placeholder="<?= $args['placeholder'] ? $args['placeholder'] : '' ?>">
      <?php endif; ?>

      <?php if ($args['field_name'] == 'wp_location'): ?>
        <div class="email-info">
          <?= __('If you\'re getting 404 from file URLs, it may be necessary to set this to the same directory that your WordPress is installed to. If this is set, the file URLs are formatted like so:<br /><b>/some-dir/shared-files/123/this-is-a-file.pdf</b>', 'shared-files') ?><br /><br />
          <?= __('You should only set this to be the first part of the url, like /some-dir/. This setting may be necessary, if you have installed WordPress in a subdirectory.', 'shared-files') ?>
        </div>
      <?php elseif ($args['field_name'] == 'icon_for_image' || $args['field_name'] == 'custom_1_icon'): ?>
        <p><?= __('e.g. /wp-content/uploads/2019/12/some-fancy-icon.png', 'shared-files') ?></p>
      <?php endif; ?>

      <?php
        
    }
  }
    
  public function shared_files_settings_general_section_callback() {
    echo '<p>' .  __('', 'shared-files') . '</p>';
  }

  public function shared_files_settings_tab_2_callback() {
    echo '</div>';
    echo '<div class="shared-files-settings-tab-2">';

    echo '<h2>' .  __('Layout settings', 'shared-files') . '</h2>';

    echo '<p>' .  __('', 'shared-files') . '</p>';
  }
  
  public function shared_files_settings_tab_3_callback() {
    echo '</div>';
    echo '<div class="shared-files-settings-tab-3">';

    echo '<h2>' .  __('Change default file icons', 'shared-files') . '</h2>';

    echo '<p>' . __('Define alternative icons here. You may add the files to the media library and then copy the URL to the appropriate field below.', 'shared-files') . '</p>';

  }

  public function shared_files_settings_tab_4_callback() {
    echo '</div>';
    echo '<div class="shared-files-settings-tab-4">';

    echo '<h2>' .  __('Custom file types', 'shared-files') . '</h2>';

    echo '<p>' . __('Define extensions and icons for custom file types here. You may add the files to the media library and then copy the URL to the appropriate field below.', 'shared-files') . '</p>';

  }

  public function shared_files_settings_tab_5_callback() {
    echo '</div>';
    echo '<div class="shared-files-settings-tab-5">';

    echo '<h2>' .  __('Email settings', 'shared-files') . '</h2>';

    echo '<p>' . '</p>';

  }

  public function shared_files_settings_admin_list_section_callback() {
    echo '</div>';
    echo '<div class="shared-files-settings-tab-6">';

    echo '<h2>' .  __('Admin list', 'shared-files') . '</h2>';

    echo '<p>' .  __('', 'shared-files') . '</p>';
  }
  
  public function settings_page() {
    ?>

    <form action="options.php" method="post" class="shared-files-settings-form">

      <h1><?= __('Shared Files Settings', 'shared-files') ?></h1>

      <div class="shared-files-settings-tabs-container">
        <ul class="shared-files-settings-tabs">
          <li class="active" data-settings-container="shared-files-settings-tab-1"><span><?= __('General settings', 'shared-files')?></span></li>
          <li data-settings-container="shared-files-settings-tab-2"><span><?= __('Layout', 'shared-files')?></span></li>
          <li data-settings-container="shared-files-settings-tab-3"><span><?= __('File type icons', 'shared-files')?></span></li>
          <li data-settings-container="shared-files-settings-tab-4"><span><?= __('Custom file types', 'shared-files')?></span></li>
          <li data-settings-container="shared-files-settings-tab-5"><span><?= __('Email', 'shared-files')?></span></li>
          <li data-settings-container="shared-files-settings-tab-6"><span><?= __('Admin list & columns', 'shared-files')?></span></li>
          <hr class="clear" />
        </ul>
      </div>

      <div class="shared-files-settings-container">

        <div class="shared-files-settings-tab-1">
          <?php settings_fields('shared-files'); ?>
          <?php do_settings_sections('shared-files'); ?>  
        </div>
        
        <?php submit_button(); ?>
      
      </div>

    </form>
    <?php
  }

  public function layout_render($args) {

    if ($args['field_name']) {
      $options = get_option('shared_files_settings');
      $layout = '';

      if (isset($options[$args['field_name']])) {
        $layout = $options[$args['field_name']];
      }
      ?>    
      <select name="shared_files_settings[<?= $args['field_name'] ?>]">
          <option value=""><?= __('Default list', 'shared-files'); ?></option>
          <option value="2-cards-on-the-same-row" <?= $layout == '2-cards-on-the-same-row' ? 'selected' : ''; ?>><?= __('2 cards on the same row', 'shared-files'); ?></option>
          <option value="3-cards-on-the-same-row" <?= $layout == '3-cards-on-the-same-row' ? 'selected' : ''; ?>><?= __('3 cards on the same row', 'shared-files'); ?></option>
          <option value="4-cards-on-the-same-row" <?= $layout == '4-cards-on-the-same-row' ? 'selected' : ''; ?>><?= __('4 cards on the same row', 'shared-files'); ?></option>
      </select>
      <?php
        
    }
  }

  public function icon_set_render($args) {

    if ($args['field_name']) {
      $options = get_option('shared_files_settings');
      $val = '';

      if (isset($options[$args['field_name']])) {
        $val = $options[$args['field_name']];
      }
      ?>    
      <select name="shared_files_settings[<?= $args['field_name'] ?>]">
          <option value="2020" <?= ($val == '2020' || $val == '') ? 'selected' : ''; ?>>2020</option>
          <option value="2019" <?= $val == '2019' ? 'selected' : ''; ?>>2019</option>
      </select>
      <?php
        
    }
  }
  
  public function file_open_method_render($args) {

    if ($args['field_name']) {
      $options = get_option('shared_files_settings');
      $val = '';

      if (isset($options[$args['field_name']])) {
        $val = $options[$args['field_name']];
      }
      ?>    
      <select name="shared_files_settings[<?= $args['field_name'] ?>]">
          <option value="" <?= $val == '' ? 'selected' : ''; ?>><?= __('Default', 'shared-files'); ?></option>
          <option value="redirect" <?= $val == 'redirect' ? 'selected' : ''; ?>><?= __('Redirect', 'shared-files'); ?></option>
      </select>

      <div class="email-info">
        <?= __('Default method means opening the files using the following url format:', 'shared-files') ?><br />
        <strong>/shared-files/123/this-is-a-file.pdf</strong><br /><br />
        <?= __('Redirect method means that while the file url is at first the same as it is using the default method, the user will be redirected to the actual location on server like so:', 'shared-files') ?><br />
        <strong>/wp-content/uploads/shared-files/this-is-a-file.pdf</strong>
      </div>

      <?php
        
    }
  }

  public function preview_service_render($args) {

    if ($args['field_name']) {
      $options = get_option('shared_files_settings');
      $val = '';

      if (isset($options[$args['field_name']])) {
        $val = $options[$args['field_name']];
      }
      ?>    
      <select name="shared_files_settings[<?= $args['field_name'] ?>]">
          <option value="" <?= $val == '' ? 'selected' : ''; ?>><?= __('Google', 'shared-files'); ?></option>
          <option value="microsoft" <?= $val == 'microsoft' ? 'selected' : ''; ?>><?= __('Microsoft', 'shared-files'); ?></option>
      </select>
      <?php
        
    }
  }

  public function order_by_render($args) {

    if ($args['field_name']) {
      $options = get_option('shared_files_settings');
      $order_by = '';

      if (isset($options[$args['field_name']])) {
        $order_by = $options[$args['field_name']];
      }
      ?>    
      <select name="shared_files_settings[<?= $args['field_name'] ?>]">
          <option value="post_date" <?= $order_by == 'post_date' ? 'selected' : ''; ?>><?= __('File publish date (post date)', 'shared-files'); ?></option>
          <option value="_sf_main_date" <?= $order_by == '_sf_main_date' ? 'selected' : ''; ?>><?= __('File date', 'shared-files'); ?></option>
          <option value="title" <?= $order_by == 'title' ? 'selected' : ''; ?>><?= __('File title', 'shared-files'); ?></option>
      </select>
      <?php
        
    }
  }

  public function order_render($args) {

    if ($args['field_name']) {
      $options = get_option('shared_files_settings');
      $order = '';

      if (isset($options[$args['field_name']])) {
        $order = $options[$args['field_name']];
      }
      ?>
      <select name="shared_files_settings[<?= $args['field_name'] ?>]">
          <option value="DESC" <?= $order == 'DESC' ? 'selected' : ''; ?>><?= __('Descending', 'shared-files'); ?></option>
          <option value="ASC" <?= $order == 'ASC' ? 'selected' : ''; ?>><?= __('Ascending', 'shared-files'); ?></option>
      </select>
      <?php
        
    }
  }

  public function card_background_render($args) {

    if ($args['field_name']) {
      $options = get_option('shared_files_settings');
      $card_background = '';

      if (isset($options[$args['field_name']])) {
        $card_background = $options[$args['field_name']];
      }
      ?>    
      <select name="shared_files_settings[<?= $args['field_name'] ?>]">
        <option value=""><?= __('Transparent', 'shared-files'); ?></option>
        <option value="white" <?= $card_background == 'white' ? 'selected' : ''; ?>><?= __('White', 'shared-files'); ?></option>
        <option value="light_gray" <?= $card_background == 'light_gray' ? 'selected' : ''; ?>><?= __('Light gray', 'shared-files'); ?></option>
        <option value="custom_color" <?= $card_background == 'custom_color' ? 'selected' : ''; ?>><?= __('Custom color', 'shared-files'); ?></option>
      </select>
      <?php
        
    }
  }

  public function card_font_render($args) {

    if ($args['field_name']) {
      $options = get_option('shared_files_settings');
      $card_font = '';

      if (isset($options[$args['field_name']])) {
        $card_font = $options[$args['field_name']];
      }
      ?>    
      <select name="shared_files_settings[<?= $args['field_name'] ?>]">
        <option value=""><?= __('Default', 'shared-files'); ?></option>
        <option value="roboto" <?= $card_font == 'roboto' ? 'selected' : ''; ?>><?= __('Roboto', 'shared-files'); ?></option>
        <option value="ubuntu" <?= $card_font == 'ubuntu' ? 'selected' : ''; ?>><?= __('Ubuntu', 'shared-files'); ?></option>
      </select>
      <?php
        
    }
  }

  public function card_featured_image_align($args) {

    if ($args['field_name']) {
      $options = get_option('shared_files_settings');
      $val = '';

      if (isset($options[$args['field_name']])) {
        $val = $options[$args['field_name']];
      }
      ?>    
      <select name="shared_files_settings[<?= $args['field_name'] ?>]">
        <option value=""><?= __('Right', 'shared-files'); ?></option>
        <option value="left" <?= $val == 'left' ? 'selected' : ''; ?>><?= __('Left', 'shared-files'); ?></option>
      </select>
      <?php
        
    }
  }

}
