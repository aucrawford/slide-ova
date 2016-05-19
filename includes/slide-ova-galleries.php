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
  echo slide_ova_shortcode();
}

// Create The Shortcode [slide-ova] to be used in posts
add_shortcode( 'slide-ova', 'slide_ova_shortcode' );

function slide_ova_shortcode( $atts ) {
  $a =  shortcode_atts( array('tags' => ''), $atts );
  $tags = $a['tags'];
  global $wpdb; // to access the database
  global $post;


  $args = array( 'post_type'=>'slide-ova', 'posts_per_page'=>'-1', 'orderby'=>'menu_order', 'order'=>'ASC', 'tag' => $tags, );
  $galleries = get_posts( $args );
  // $tags = get_tags( array('name_like' => "a", 'order' => 'ASC') );
  $post_title = '';
  $post_content = '';
  $slide_url = '';
  $slide_ova = '';

  // get gallery data for each post
  foreach ( $galleries as $post ) {
    // $slide_url = slide_ova_get_meta("slide-ova-url");

    $slide_ova .= '<div class="modal fade slide-ova-gallery-modal slide-ova-modal-' . $post->ID . '" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">';
      $slide_ova .= '<div class="modal-dialog modal-lg">';
        $slide_ova .= '<div class="modal-content text-center">';
          $slide_ova .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';

          $slide_ova .= '<div id="slide-ova-modal-' . $post->ID . '" class="slide-container">';
          
            // $slide_arg = array( 'post_mime_type' => 'image', 'numberposts' => 1, 'post_parent' => $post->ID, 'post_type' => 'attachment' );
            // $first_slide = get_children( $slide_arg );
            $first_slide = get_post_gallery_images($post->ID, 'full')[0];
            $slide_ova .= '<div class="slide-ova-slide"><img src="' . str_replace('-150x150','',$first_slide) . '" /></div>';

            $slide_ova .= '<div class="slides-wrapper">';
              $slide_ova .= '<div class="slides-holder">';

                $slide_count = 0;

                $gallery = get_post_gallery( get_the_ID(), false );
                $ids = explode( ",", $gallery['ids'] );
                foreach( $ids as $id ) {
                  $image_attributes = wp_get_attachment_image_src( $id ); // returns an array
                  if( $image_attributes ) {
                    $slide_count++;

                    if ($slide_count == 1) {
                      $slide_ova .= '<div class="slides-col">';
                    } elseif ($slide_count % 7 == 0) {
                      $slide_ova .= '</div><div class="slides-col">';
                    }

                    $slide_ova .= '<a href="#" class="slide-preview" id="slide-' . $id . '">';
                      $slide_ova .= '<img src="' . $image_attributes[0] . '" width="122" height="122" />';
                    $slide_ova .= '</a>';
                  }
                }
                $slide_ova .= '</div>';

                // Retrieve the first gallery in the post
                // $gallery = get_post_gallery_images( $post );

                // // Loop through each image in each gallery
                // foreach( $gallery as $image ) {
                //   $slide_ova .= '<a href="#" class="slide-preview" id="slide-' . $post->ID . '">';
                //     $slide_ova .= '<img src="' . $image . '" width="122" height="122" />';
                //   $slide_ova .= '</a>';
                // }

              // bread crumbs
              $slide_ova .= '</div>';
              $slide_ova .= '<ul class="slides-set-bar"></ul>';
            $slide_ova .= '</div>';

          $slide_ova .= '</div>';


        $slide_ova .= '</div>';
      $slide_ova .= '</div>';
    $slide_ova .= '</div>';
  }

  // build the content
  // $slide_ova = $gallery_modal;


  // build the content
  $slide_ova .= '<div id="slide-ova-galleries" class="gallery-container">';
    // $slide_ova .= '<div id="gallery-wrapper">';

    // Gallery slider
      $slide_ova .= '<div class="galleries-wrapper">';
        $slide_ova .= '<div class="galleries-holder">';
          $gallery_count = 0;

          foreach ( $galleries as $post ) {
            $gallery_count++;

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

            if ($gallery_count == 1) {
              $slide_ova .= '<div class="galleries-col">';
            } elseif ($gallery_count % 9 == 0) {
              $slide_ova .= '</div><div class="galleries-col">';
            }
            $slide_ova .= '<a href="#" class="gallery-preview" id="gallery-' . $post->ID . '"  data-toggle="modal" data-target=".slide-ova-modal-' . $post->ID . '">';
              $slide_ova .= '<img src="' . $this_gallery_preview . '" class="gallery_preview_image" width="225" height="225" />';
              $slide_ova .= '<div class="gallery-title"><small>' . $this_gallery_title . '</small></div>';
            $slide_ova .= '</a>';
          }
          $slide_ova .= '</div>';

        // bread crumbs
        $slide_ova .= '</div>';
        $slide_ova .= '<ul class="slide-ova-gallery-selectors"></ul>';
      $slide_ova .= '</div>';

    // $slide_ova .= '</div>';
  $slide_ova .= '</div>';

  return $slide_ova;
}