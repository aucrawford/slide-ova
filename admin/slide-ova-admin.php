<?php
// defined('ABSPATH') or die("No script kiddies please!");

// add video meta box
add_action('add_meta_boxes', 'slide_ova_video_meta_box');

function slide_ova_video_meta_box() {
  add_meta_box('slide-ova-video', 'Video Share Link', 'slide_ova_video_box', 'slide-ova', 'normal');
}

function slide_ova_video_box() {
  // Use nonce for verification
  $html =  '<input type="hidden" name="slide_ova_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

  $html .= '<table class="form-table"><tbody><tr>';
  $html .= '<th style="width:25%">';
  $html .= '<label for="slide_ova_video_url">Video</label>';
  $html .= '</th>';
  $html .= '<td>';
  $html .= '<input type="text" id="slide_ova_video_url" name="slide_ova_video_url" value="' . htmlspecialchars(slide_ova_get_meta("slide-ova-video-url")) . '" size="30" />';
  $html .= '<br /> Only accepts a single YouTube or Vimeo share link.';
  $html .= '</td>';
  $html .= '</tr></tbody></table>';
  echo $html;
}

add_action('save_post', 'slide_ova_save_data', 10, 3);
function slide_ova_save_data($post_id, $post) {

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times
  if (!wp_verify_nonce($_POST['slide_ova_noncename'], plugin_basename(__FILE__)))
    return $post->ID;

  if ($post->post_type == 'revision')
    return; //don't store custom data twice

  if (!current_user_can('edit_post', $post->ID))
    return $post->ID;

  // OK, we're authenticated: we need to find and save the data
  // We'll put it into an array to make it easier to loop though.
  $mydata = array();
  $mydata['slide-ova'] = $_POST['slide_ova'];
  $mydata['slide-ova-video-url'] = $_POST['slide_ova_video_url'];
  global $wpdb;
  $mydata['menu-order'] = $wpdb->get_var("SELECT MAX(menu_order)+1 AS menu_order FROM {$wpdb->posts} WHERE post_type='{$post_type}'");

  // Add values of $mydata as custom fields
  foreach ($mydata as $key => $value) { // Let's cycle through the $mydata array!
    update_post_meta($post->ID, $key, $value);
    if (!$value)
      delete_post_meta($post->ID, $key); // Delete if blank
  }
}
