<?php
/*
 * Plugin Name: Slide Ova
 * Plugin URI: http://www.theScriptiest.com/slide_ova
 * Description: This pluggin will create a responsive rotator for videos and images.
 * Version: 1.3
 * Author: A. U. Crawford
 * Author URI: http://www.theScriptiest.com/
 * License: MIT
*/

// defined('ABSPATH') or die("No script kiddies please!");
define('SLIDE_OVA_DIR', dirname(__FILE__));
define('SLIDE_OVA_URL', WP_PLUGIN_URL . "/" . basename(SLIDE_OVA_DIR));
define('SLIDE_OVA_VERSION', '2.0');

function slide_ova_get_meta($field) {
  global $post;
  $custom_field = get_post_meta($post->ID, $field, true);
  switch ($field) {
    case 'slide-ova':
      if (preg_match('/\.pdf/', $custom_field)) {
        $pdf_src = urlencode($custom_field);
        $custom_field = "http://docs.google.com/viewer?url=$pdf_src&embedded=true&iframe=true&width=100%&height=100%";
      }
      break;
    default :
      break;
  }
  return $custom_field;
}

// Register Hooks
register_activation_hook(__FILE__, 'slide_ova_activate');
function slide_ova_activate() {
  slide_ova_register();
  // slide_ova_defaults();
  flush_rewrite_rules();
}

// Register Custom Post Type
function slide_ova_register() {
  $labels = array(
    'name' => __('Slide Ova'),
    'singular_name' => __('Gallery'),
    'add_new' => __('Add Gallery'),
    'add_new_item' => __('Add New Gallery'),
    'edit_item' => __('Edit Gallery'),
    'new_item' => __('New Gallery'),
    'view_item' => __('View Gallery'),
    'search_items' => __('Search for Gallery'),
    'not_found' => __('No Gallery found'),
    'not_found_in_trash' => __('No Gallery found in Trash'),
    'parent_item_colon' => '',
    'menu_name' => __('Slide Ova')
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'show_ui' => true,
    'capability_type' => 'post',
    'hierarchical' => true,
    'rewrite' => array('slug' => 'slide-ova'),
    'taxonomies' => array('post_tag'),
    'supports' => array(
        'title',
        'thumbnail',
        'editor',
        'page-attributes',
        'revisions'
    ),
    'menu_position'       => 20,
    'menu_icon' => 'dashicons-format-gallery',
  );

  register_post_type( 'slide-ova', $args );
}
add_action( 'init', 'slide_ova_register');

function place_slide_ova_in_menu($safe_text, $text) {
  if (__('Slide Ova', 'slide_ova_context') !== $text) {
    return $safe_text;
  }
  // We are on the main menu item now. The filter is not needed anymore.
  remove_filter('attribute_escape', 'place_slide_ova_in_menu');

  return __('Slide Ova', 'slide_ova_context');
}

// Register custom JS scripts
function slide_ova_enqueue_scripts() {
  if (!is_admin()) {

    wp_enqueue_script('jquery');
    wp_register_script('slide_ova_scripts', SLIDE_OVA_URL . '/js/slide-ova.js',__FILE__);
    wp_enqueue_script('slide_ova_scripts');

  }
}
add_action('wp_enqueue_scripts', 'slide_ova_enqueue_scripts');

// Add style sheets
function slide_ova_enqueue_styles() {
    wp_enqueue_style('slide_ova_style', SLIDE_OVA_URL . "/css/slide-ova.css", null, null, "screen");
}
add_action('wp_enqueue_scripts', 'slide_ova_enqueue_styles');


// Load in the pages doing everything else!

include("admin/slide-ova-admin.php");

include("includes/slide-ova-galleries.php");
// include("includes/slide-ova-gallery.php");
// include("includes/slide-ova-carousel.php");
