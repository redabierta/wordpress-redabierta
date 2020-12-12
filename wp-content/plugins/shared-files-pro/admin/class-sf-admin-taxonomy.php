<?php

class SharedFilesAdminTaxonomy {

  public function create_shared_files_custom_taxonomy() {

    $labels = array(
      'name' => __('Category', 'shared-files'),
      'singular_name' => __('Category', 'shared-files'),
      'search_items' =>  __('Search Categories', 'shared-files'),
      'all_items' => __('All Categories', 'shared-files'),
      'parent_item' => __('Parent Category', 'shared-files'),
      'parent_item_colon' => __('Parent Category:', 'shared-files'),
      'edit_item' => __('Edit Category', 'shared-files'),
      'update_item' => __('Update Category', 'shared-files'),
      'add_new_item' => __('Add New Category', 'shared-files'),
      'new_item_name' => __('New Type Name', 'shared-files'),
      'menu_name' => __('Categories', 'shared-files'),
    );

    register_taxonomy('shared-file-category', array('shared_file'), array(
      'hierarchical' => true,
      'labels' => $labels,
      'show_ui' => true,
      'show_admin_column' => true,
      'query_var' => true,
      'rewrite' => array('slug' => 'category'),
    ));
  }

  public function register_categories_info_page() {
    add_submenu_page(
      'edit.php?post_type=shared_file',
      __('Categories', 'shared-files'),
      __('Categories', 'shared-files'),
      'manage_options',
      'shared-files-categories-info',
      [$this, 'register_categories_info_page_callback'],
      3
    );
  }

  public function register_categories_info_page_callback() {
    ?>

    <div class="wrap">
      <h1><?= __('Categories', 'shared-files'); ?></h1>

      <?= SharedFilesAdminHelpers::sfProFeatureMarkup() ?>

    </div>
    <?php
  }

  function taxonomy_custom_fields($term) {  
    ?>

    <tr class="form-field">  
      <th scope="row" valign="top">  
      </th>  
      <td>  
        <div class="shared-files-category-description-info"><b><?= __('The description field above can be used to alter the order of the categories in [shared_files_categories]-shortcode.', 'shared-files') ?></b><br /><br /><?= __('If a value is entered, the categories are sorted by that.', 'shared-files') ?></div>
      </td>  
    </tr>  
    
  <?php  
  }  

  public function theme_columns($theme_columns) {

    $new_columns = array(
      'cb' => '<input type="checkbox" />',
      'name' => __('Name'),
      'description' => __('Description'),
      'shortcode' => __('Shortcode', 'shared-files'),
      'slug' => __('Slug'),
      'posts' => __('Posts')
    );

    return $new_columns;
  }

  public function add_shared_file_category_column_content($content, $column_name, $term_id) {

    $term = get_term($term_id, 'shared-file-category');

    switch ($column_name) {
      case 'shortcode':
        $content = '<span class="shared-files-shortcode-admin-list shared-files-shortcode-' . $term->slug . '" title="[shared_files category=' . $term->slug . ']">[shared_files category=' . $term->slug . ']</span>' . 
          '<button class="shared-files-copy shared-files-copy-admin-list" data-clipboard-action="copy" data-clipboard-target=".shared-files-shortcode-' . $term->slug . '">' . __('Copy', 'shared-files') . '</button>';
        break;
      default:
        break;
    }

    return $content;

  }

}
