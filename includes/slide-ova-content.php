<?php
// defined('ABSPATH') or die("No script kiddies please!");

// THE AJAX BEGIN //
// enqueue uses an ajax.js script I created. Must be named ajas.js
function ajax_enqueue_script() {
  wp_enqueue_script( 'my-ajax-handle', plugin_dir_url( __FILE__ ) . '/js/ajax.js', array( 'jquery' ) );
}
add_action('wp_enqueue_scripts', 'ajax_enqueue_script');
// Localize script is important for this to work. Must use the default admin-ajax.php which I did not make.
wp_localize_script( 'my-ajax-handle', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
// add actions for logged in users
add_action( 'wp_ajax_the_ajax_hook', 'showVideo' );
// and non logged in users
add_action( 'wp_ajax_nopriv_the_ajax_hook', 'showVideo' );

function showVideo() {
  // get the data to pass to the js
  $postID   = $_POST['postID'];
  $title    = get_the_title($postID);
  $content  = get_post_field('post_content', $postID);
  $video    = get_post_meta( $postID, 'slide-ova-video-url', true );
  // set the data
  setup_postdata( $postID );
  // generate the response
  $response = json_encode( array('post' => $postID, 'title' => $title, 'content' => $content, 'video' => $video ) );
  // response output
  header( "Content-Type: application/json" );
  echo $response;
  // wordpress may print out a spurious zero without this
  die();
}
// THE AJAX END //

// Create The Shortcode [slide-ova] to be used in posts
add_shortcode( 'slide-ova', 'slide_ova_shortcode' );
function slideOva() {
  echo slide_ova_shortcode();
}

function slide_ova_shortcode( $atts = array() ) {
  global $wpdb; // to access the database
  global $post;

  $args = array( 'post_type' => 'slide-ova', 'posts_per_page' => '-1', 'orderby'=>'menu_order', 'order'=>'ASC' );
  $slides = get_posts( $args );

  // get data for most recent post as the default
  foreach ( $slides as $post ) {
    $slide_title = $post->post_title;
    $slide_content = $post->post_content;
    $video_link = slide_ova_get_meta("slide-ova-video-url");

    if (isset($video_link)) {
      $youtube = strpos($video_link, 'youtu');
      $vimeo = strpos($video_link, 'vimeo');

      if (!empty($youtube)) {
        $video_link = explode('.be/', $video_link);
        $slide_video = '<iframe src="http://www.youtube.com/embed/' . $video_link[1] . '?rel=0" frameborder="0" width="640" height="360" allowfullscreen></iframe>';
      } elseif (!empty($vimeo)) {
        $video_link = explode('.com/', $video_link);
        $slide_video = '<iframe src="http://player.vimeo.com/video/' . $video_link[1] . '" frameborder="0" width="640" height="360" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
      } else {
        $slide_video = "<p>Dis' Broke</p>";
      }
    }
  }

  // build the content
  $slide_ova .= '<div id="slide-ova-containa">';
  $slide_ova .= '<div id="slide-ova">';
  // video
  $slide_ova .= '<div class="slide">';
  $slide_ova .= $slide_video;
  $slide_ova .= '</div>';
  // title and description
  $slide_ova .= '<div class="slide-desc">';
  $slide_ova .= '<h2 class="slide-title">' . $slide_title . '</h2>';
  $slide_ova .= '<p>' . $slide_content . '</p>';
  $slide_ova .= '</div>';
  // rotating section
  $slide_ova .= '<div class="slides-wrapper">';
  $slide_ova .= '<div class="slides-holder">';
  foreach ( $slides as $post ) {
    if (strlen($post->post_title) > 40) {
      $slide_title = substr( $post->post_title, 0, 40 ) . '...';
    } else {
      $slide_title = $post->post_title;
    }
    $video_link = slide_ova_get_meta("slide-ova-video-url");

    if (isset($video_link)) {
      $youtube_video = strpos($video_link, 'youtu');
      $vimeo_video = strpos($video_link, 'vimeo');

      if (!empty($youtube_video)) {
        $video_link = explode('.be/', $video_link);
      } elseif (!empty($vimeo_vid)) {
        $video_link = explode('.com/', $video_link);
      }
      $video_id = $video_link[1];
    }

    if ( has_post_thumbnail( $post->ID ) ) {
      $slide_preview = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
      $slide_preview = $slide_preview[0];
    } else {
      $image = 'http://img.youtube.com/vi/' . $video_id . '/default.jpg';
    }

    $slide_ova .= '<a href="#" class="slide-preview" id="slide-' . $post->ID . '">';
    $slide_ova .= '<img src="' . $slide_preview[0] . '" ' . $attributes . ' class="slide_preview_image" width="120" height="90" />';
    $slide_ova .= '<small>' . $slide_title . '</small>';
    $slide_ova .= '</a>';
  }
  // bread crumbs
  $slide_ova .= '</div>';
  $slide_ova .= '<ul class="slides-set-selectors"></ul>';
  $slide_ova .= '</div>';

  $slide_ova .= '</div>';
  $slide_ova .= '</div>';

  return $slide_ova;
}
