<?php
// defined('ABSPATH') or die("No script kiddies please!");

// add columns
add_filter( 'manage_edit-slide-ova_columns', 'my_edit_slide_ova_columns' );

function my_edit_slide_ova_columns( $columns ) {
  $columns = array(
    'cb' => '<input type="checkbox" />',
    'thumbnail' => __( 'Image' ),
    'title' => __( 'Title' ),
    'menu_order' => 'Order',
    'date' => __( 'Date' )
  );

  return $columns;
}

// Add a specific thumbnail size for thumbnail column
function slide_ova_video_thumbnail_size() {
  add_image_size( 'slide-ova-list-image', 142, 80, true );
}
add_action( 'after_setup_theme', 'slide_ova_video_thumbnail_size' );

/* Wordpress can handle the defaults of cb, title, and date */
// add content to new columns
function my_manage_slide_ova_columns( $column, $post_id ) {
  global $post;

  switch( $column ) {
    case 'thumbnail':
      // Get the featured image
      $fImage = get_the_post_thumbnail($post->ID, 'slide-ova-list-image');

      // Show thumbnail if it exists
      if ( !empty($fImage) )
        echo $fImage;
      else
        $video_link = slide_ova_get_meta("slide-ova-video-url");
        if (isset($video_link)) {
          $youtube = strpos($video_link, 'youtu');
          $vimeo = strpos($video_link, 'vimeo');

          if (!empty($youtube)) {
            $video_link = explode('.be/', $video_link);
          } elseif (!empty($vimeo)) {
            $video_link = explode('.com/', $video_link);
          }
          $video_id = $video_link[1];
        }
        echo '<img src="http://img.youtube.com/vi/' . $video_id . '/default.jpg" />';
      break;

    // Display the menu order
    case 'menu_order':
      $order = $post->menu_order;
      echo $order;

      break;

    // Break out of the switch statement for everything else.
    default:
      break;
  }
}
add_action( 'manage_slide-ova_posts_custom_column', 'my_manage_slide_ova_columns', 10, 3 );

// Style columns
function slide_ova_column_widths() {
  echo '<style type="text/css">
    .column-thumbnail { width:142px !important; }
    td.column-thumbnail { padding-bottom:0px !important; }
    .column-menu_order { text-align: left; width:140px !important; }
  </style>';
}
add_action('admin_head', 'slide_ova_column_widths');
