<?php
// defined('ABSPATH') or die("No script kiddies please!");

// THE AJAX BEGIN //
// enqueue uses an ajax.js script I created. Must be named ajas.js
function slide_ova_ajax_enqueue_script() {
  wp_enqueue_script( 'slide-ova-ajax-handle', SLIDE_OVA_URL . '/js/slide-ova-ajax.js', array( 'jquery' ) );
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
  // $image    = get_post_thumbnail_id($post->ID);
  // set the data
  setup_postdata( $postID );
  // generate the response
  $response = json_encode( array('post' => $postID, 'title' => $title, 'content' => $content, ) );
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

function slideOva() {
  echo '[slide-ova tags="tagname" gallyer_id="ID#"]'
}

// Create The Shortcode [slide-ova] to be used in posts
add_shortcode( 'slide-ova', 'slide_ova_shortcode' );

function slide_ova_shortcode( $atts ) {
  // create short code attributes [slide-ova tags="tagname" gallyer_id="ID#"]
  $a =  shortcode_atts( array('tags' => '', 'gallery_id' => ''), $atts );
  $tags = $a['tags'];
  $galleryID = $a['gallery_id'];
  global $wpdb; // to access the database
  global $post;


  $args = array( 'post_type'=>'slide-ova', 'posts_per_page'=>'-1', 'orderby'=>'menu_order', 'order'=>'ASC', 'tag' => $tags, 'post__in' => array($galleryID) );
  $galleries = get_posts( $args );
  // $tags = get_tags( array('name_like' => "a", 'order' => 'ASC') );
  $post_title = '';
  $post_content = '';
  $slide_url = '';

  // get gallery data for each post
  foreach ( $galleries as $post ) {
    $slide_url = slide_ova_get_meta("slide-ova-url");

    $gallery_modal = "";
    $gallery_modal .= '<div class="modal fade slide-ova-modal-' . $post->ID . ' gallery-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">';
      $gallery_modal .= '<div class="modal-dialog modal-lg">';
        $gallery_modal .= '<div class="modal-content text-center">';

          // $images_args = array(
          //   'post_type'      => 'attachment',
          //   'post_mime_type' => 'image',
          //   'post_status'    => 'inherit',
          //   'posts_per_page' => - 1,
          // );
          // $images = new WP_Query( $images_args );

          // Retrieve the first gallery in the post
          $gallery = get_post_gallery_images( $post );

          $gallery_modal .= '<div id="slide-ova-modal-' . $post->ID . '-' . $post->ID . '" class="slide-container">';

            $gallery_modal .= '<div class="slide slide-image"></div>';

            $gallery_modal .= '<div class="slides-wrapper">';
              $gallery_modal .= '<div class="slides-holder">';

                // Loop through each image in each gallery
                foreach( $gallery as $image ) {
                  $gallery_modal .= '<a href="#" class="slide-preview" id="slide-' . $post->ID . '-' . '">';
                    $gallery_modal .= '<img src="' . $image . '" width="122" height="122" />';
                  $gallery_modal .= '</a>';
                }

              // bread crumbs
              $gallery_modal .= '</div>';
              $gallery_modal .= '<div class="slides-set-bar"><div class="slides-set-selector"></div></div>';
            $gallery_modal .= '</div>';

          $gallery_modal .= '</div>';


        $gallery_modal .= '</div>';
      $gallery_modal .= '</div>';
    $gallery_modal .= '</div>';
  }

  // build the content
  $slide_ova = $gallery_modal;


  // build the content
  $slide_ova .= '<div id="slide-ova-galleries" class="gallery-container">';
    // $slide_ova .= '<div id="gallery-wrapper">';

    // Gallery slider
      $slide_ova .= '<div class="galleries-wrapper">';
        $slide_ova .= '<div class="galleries-holder">';
          $slide_count = 0;

          foreach ( $galleries as $post ) {
            $slide_count++;

            if (strlen($post->post_title) > 40) {
              $this_gallery_title = substr( $post->post_title, 0, 40 ) . '...';
            } else {
              $this_gallery_title = $post->post_title;
            }

            if ( has_post_thumbnail( $post->ID ) ) {
              $this_gallery_preview = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
              $this_gallery_preview = $this_gallery_preview[0];
            } else {
              // nothing
            }

            if ($slide_count == 1) {
              $slide_ova .= '<div class="gallery-col">';
            } elseif ($slide_count % 9 == 0) {
              $slide_ova .= '</div><div class="gallery-col">';
            }
            $slide_ova .= '<a href="#" class="gallery-preview" id="gallery-' . $post->ID . '"  data-toggle="modal" data-target=".slide-ova-modal-' . $post->ID . '">';
              $slide_ova .= '<img src="' . $this_gallery_preview . '" class="gallery_preview_image" width="225" height="225" />';
              $slide_ova .= '<div class="gallery-title"><small>' . $this_gallery_title . '</small></div>';
            $slide_ova .= '</a>';
          }
          $slide_ova .= '</div>';

        // bread crumbs
        $slide_ova .= '</div>';
        $slide_ova .= '<ul class="galleries-set-selectors"></ul>';
      $slide_ova .= '</div>';

    // $slide_ova .= '</div>';
  $slide_ova .= '</div>';

  return $slide_ova;
}