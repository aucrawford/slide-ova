<?php
// defined('ABSPATH') or die("No script kiddies please!");

  /////////////////////////////////
 // Create the Gallery Listings //
/////////////////////////////////

// add columns for galleries
function slide_ova_galleries_columns( $columns ) {
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'thumbnail' => __( 'Image' ),
    'title' => __( 'Title' ),
    'tags' => __( 'Tags' ),
    'menu_order' => 'Order',
    'date' => __( 'Date' )
  );
  return $columns;
}
add_filter( 'manage_edit-slide-ova_columns', 'slide_ova_galleries_columns' );


// Add a specific thumbnail size for thumbnail column
function slide_ova_thumbnail_size() {
  add_image_size( 'slide-ova-list-image', 142, 80, true );
}
add_action( 'after_setup_theme', 'slide_ova_thumbnail_size' );

/* Wordpress can handle the defaults of cb, title, and date */
// add content to new columns
function manage_slide_ova_columns( $column, $post_id ) {
  global $post;

  switch( $column ) {
    // Featured Image
    case 'thumbnail':
      $fImage = get_the_post_thumbnail($post->ID, 'slide-ova-list-image');

      if ( !empty($fImage) ) {
        echo $fImage;
      } else {
        // nothing
      }
      break;

    // Tag
    case 'tags':
      $tags = get_tags( array('name_like' => "a", 'order' => 'ASC') );
      foreach ( (array) $tags as $tag ) {
        echo $tag->name . " ";
      }
      break;

    // Menu Order
    case 'menu_order':
      $order = $post->menu_order;
      echo $order;

      break;

    // Break out of the switch statement for everything else.
    default:
      break;
  }
}
add_action( 'manage_slide-ova_posts_custom_column', 'manage_slide_ova_columns', 10, 3 );

// Style columns
function slide_ova_column_widths() {
  echo  '<style type="text/css">
          .column-thumbnail { width:142px !important; }
          td.column-thumbnail { padding-bottom:0px !important; }
          .column-menu_order { text-align: left; width:140px !important; }
        </style>';
}
add_action('admin_head', 'slide_ova_column_widths');

  //////////////////////
 // Save the Gallery //
//////////////////////

add_action('save_post', 'slide_ova_save_data', 10, 3);
function slide_ova_save_data($post_id, $post) {

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times
  if ( !isset($_POST['slide_ova_noncename']) || !wp_verify_nonce($_POST['slide_ova_noncename'], plugin_basename(__FILE__) )) {
    return $post->ID;
  }

  if ($post->post_type == 'revision') {
    return; //don't store custom data twice
  }

  if (!current_user_can('edit_post', $post->ID)) {
    return $post->ID;
  }

  // OK, we're authenticated: we need to find and save the data
  // We'll put it into an array to make it easier to loop though.
  $mydata = array();
  $mydata['slide-ova'] = $_POST['slide_ova'];
  global $wpdb;
  $mydata['menu-order'] = $wpdb->get_var("SELECT MAX(menu_order)+1 AS menu_order FROM {$wpdb->posts} WHERE post_type='{$post_type}'");

  // Add values of $mydata as custom fields
  foreach ($mydata as $key => $value) { // Let's cycle through the $mydata array!
    update_post_meta($post->ID, $key, $value);
    if (!$value)
      delete_post_meta($post->ID, $key); // Delete if blank
  }
}
