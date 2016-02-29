<?php
// defined('ABSPATH') or die("No script kiddies please!");

// THE AJAX BEGIN //
// enqueue uses an ajax.js script I created. Must be named ajas.js
function slide_ova_ajax_enqueue_script() {
  wp_enqueue_script( 'slide-ova-ajax-handle', plugin_dir_url( __FILE__ ) . '/js/ajax.js', array( 'jquery' ) );
  // Localize script is important for this to work. Must use the default admin-ajax.php which I did not make.
  wp_localize_script( 'slide-ova-ajax-handle', 'slide_ova_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('wp_enqueue_scripts', 'slide_ova_ajax_enqueue_script');

// if ( function_exists( 'add_theme_support' ) ) {
//   add_theme_support( 'post-thumbnails' );
//   set_post_thumbnail_size( 125, 125 );
// }
// add_theme_support( 'post-thumbnails' );

function showSlide() {
  // get the data to pass to the js
  $postID   = $_POST['postID'];
  $title    = get_the_title($postID);
  $content  = get_post_field('post_content', $postID);
  $video    = get_post_meta( $postID, 'slide-ova-video-url', true );
  // $image    = get_post_thumbnail_id($post->ID);
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
// add actions for logged in users
add_action( 'wp_ajax_the_ajax_hook', 'showSlide' );
// and non logged in users
add_action( 'wp_ajax_nopriv_the_ajax_hook', 'showSlide' );

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
    $slide_video_link = slide_ova_get_meta("slide-ova-video-url");

    if ( get_post_meta( get_the_ID(), 'thumb', true ) ) {
      $slide_image = '<a href="' + the_permalink() + '" rel="bookmark"><img class="thumb" src="' + esc_url( get_post_meta( get_the_ID(), 'thumb', true ) ) + '" alt="' + the_title_attribute() + '" /></a>';
    }

    if (isset($slide_video_link) && !empty($slide_video_link)) {
      $youtube = strpos($slide_video_link, 'youtu');
      $vimeo = strpos($slide_video_link, 'vimeo');

      if (!empty($youtube)) {
        $slide_video_link = explode('.be/', $slide_video_link);
        $slide_media = '<iframe src="http://www.youtube.com/embed/' . $slide_video_link[1] . '?rel=0" frameborder="0" allowfullscreen></iframe>';
      } elseif (!empty($vimeo)) {
        $slide_video_link = explode('.com/', $slide_video_link);
        $slide_media = '<div class="slide slide-video"><iframe src="http://player.vimeo.com/video/' . $slide_video_link[1] . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>';
      } else {
        $slide_media = "<p>Dis' Broke</p>";
      }
    } else {
      $slide_media = '<div class="slide slide-image">' + $slide_image + '</div>';
    }
  }

  // build the content
  $slide_ova .= '<div id="slide-ova-containa">';
  $slide_ova .= '<div id="slide-ova">';
  // video
  $slide_ova .= $slide_media;
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
      $this_slide_title = substr( $post->post_title, 0, 40 ) . '...';
    } else {
      $this_slide_title = $post->post_title;
    }
    $video_link = slide_ova_get_meta("slide-ova-video-url");

    if (isset($video_link)) {
      $youtube_video = strpos($video_link, 'youtu');
      $vimeo_video = strpos($video_link, 'vimeo');

      if (!empty($youtube_video)) {
        $video_link = explode('.be/', $video_link);
      } elseif (!empty($vimeo_video)) {
        $video_link = explode('.com/', $video_link);
      }
      $video_id = $video_link[1];
    }

    if ( has_post_thumbnail( $post->ID ) ) {
      $this_slide_preview = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
      $this_slide_preview = $this_slide_preview[0];
    } else {
      $this_slide_preview = 'http://img.youtube.com/vi/' . $video_id . '/default.jpg';
    }

    $slide_ova .= '<a href="#" class="slide-preview" id="slide-' . $post->ID . '">';
    $slide_ova .= '<img src="' . $this_slide_preview . '" ' . $attributes . ' class="slide_preview_image" width="120" height="90" />';
    $slide_ova .= '<small>' . $this_slide_title . '</small>';
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
