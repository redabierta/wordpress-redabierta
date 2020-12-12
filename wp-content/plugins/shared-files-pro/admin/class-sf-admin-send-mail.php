<?php

class SharedFilesAdminSendMail {

  public static function file_load_send_email($post_id, $post) {

    $post_title = get_the_title($post_id);
    $s = get_option('shared_files_settings');

    if (isset($s['send_email']) && isset($s['recipient_email']) && is_email($s['recipient_email']) && is_object($post) && $post->post_type == 'shared_file') {
  
      $headers = ['Content-Type: text/html; charset=UTF-8'];  
      $subject = 'File downloaded: ' . $post_title;

      $body_html = '';
      $body_html .= '<html><head><title></title></head><body>';
      $body_html .= '<h3 style="color: #000;">File was downloaded: ' . $post_title . '</h3>';
      $body_html .= '<p style="color: #bbb;">-- <br />This email was sent by Shared Files Pro</p>';
      $body_html .= '</body></html>';
  
      $resp = wp_mail($s['recipient_email'], $subject, $body_html, $headers);

    }
    
  }

  public static function file_limit_send_email($post_id, $post) {

    $post_title = get_the_title($post_id);
    $s = get_option('shared_files_settings');

    if (isset($s['recipient_email']) && is_email($s['recipient_email']) && $post->post_type == 'shared_file') {

      $headers = ['Content-Type: text/html; charset=UTF-8'];
      $subject = 'Download limit reached for ' . $post_title;

      $body_html = '';
      $body_html .= '<html><head><title></title></head><body>';
      $body_html .= '<h3 style="color: #000;">Download limit reached for ' . $post_title . '</h3>';
      $body_html .= '<p style="color: #bbb;">-- <br />This email was sent by Shared Files Pro</p>';
      $body_html .= '</body></html>';
  
      $resp = wp_mail($s['recipient_email'], $subject, $body_html, $headers);
            
    }
    
  }

  public function file_expired_send_email() {

    $s = get_option('shared_files_settings');

    if (isset($s['recipient_email']) && is_email($s['recipient_email'])) {
  
      $wpb_all_query_all_files = new WP_Query(array(
        'post_type' => 'shared_file',
        'post_status' => 'publish',
        'posts_per_page' => -1,

        'meta_query' => array(
          'relation' => 'AND',
            array(
              'key' => '_sf_expiration_date',
              'compare' => 'EXISTS'
            ),
        )        
      ));
  
      if (isset($wpb_all_query_all_files) && $wpb_all_query_all_files->have_posts()):
        while ($wpb_all_query_all_files->have_posts()): $wpb_all_query_all_files->the_post();
  
          $id = get_the_id();
          $c = get_post_custom($id);
  
          $filename = $c['_sf_filename'][0];
          $post_title = get_the_title($id) . ' / ' . $filename;
  
          $expiration_date = get_post_meta($id, '_sf_expiration_date', true);
          $expiration_date_formatted = '';
          $expiration_date_alert = 0;
  
          if ($expiration_date instanceof DateTime) {
            $dt_now = new DateTime('now');
              
            if ($expiration_date <= $dt_now) {

              $headers = ['Content-Type: text/html; charset=UTF-8'];
              $subject = 'File expired: ' . $post_title;
        
              $body_html = '';
              $body_html .= '<html><head><title></title></head><body>';
              $body_html .= '<h3 style="color: #000;">File expired: ' . $post_title . '</h3>';
              $body_html .= '<p style="color: #bbb;">-- <br />This email was sent by Shared Files Pro</p>';
              $body_html .= '</body></html>';
          
              $resp = wp_mail($s['recipient_email'], $subject, $body_html, $headers);
  
            }
              
          }
  
        endwhile;
      endif;
      
    }
    
  }

}
