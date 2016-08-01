<?php
/*
* Plugin Name: WebGeckos Vertical Tabs
* Version: 1.0
* Plugin URI: http://www.webgeckos.com
* Description: Extending WordPress and Layers with Tabs Widget.
* Author: Danijel Rose
* Author URI: http://www.webgeckos.com/
*
* Requires at least: 4.5
* Tested up to: 4.5.3
*
* Layers Plugin: True
* Layers Required Version: 1.5.4
*
* Text Domain: geckos-kit
* Domain Path: /lang/
*/
/*  Copyright 2016  Danijel Rose  (email : info@webgeckos.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*
    1. ENQUEUE FILES
    2. ENQUEUE SCRIPTS
    3. HOOKS
    4. SHORTCODES
    5. CUSTOM POST TYPES
    6. CUSTOM TAXONOMIES
    7. CUSTOM FIELDS / META BOXES
*/
if ( ! defined( 'ABSPATH' ) ) exit;

/*
    1. ENQUEUE FILES
*/

require_once(plugin_dir_path(__FILE__) . '/widgets/geckos_tabs_widget.php');

/*
    2. ENQUEUE SCRIPTS
*/

function geckos_add_scripts(){
  wp_enqueue_style('geckos-style', plugins_url('/css/geckos-style.css', __FILE__) );
  wp_enqueue_script('geckos-scripts', plugin_dir_url(__FILE__) . 'js/geckos-scripts.js', array('jquery'));
}

function geckos_admin_scripts() {
    wp_enqueue_script( 'wp-color-picker-alpha', plugin_dir_url(__FILE__) . 'js/wp-color-picker-alpha.js', array( 'wp-color-picker' ) );

    wp_enqueue_style( 'wp-color-picker' ); // styles for color picker
}

/*
    3. HOOKS
*/

// hook to enqueue scripts
add_action('wp_enqueue_scripts', 'geckos_add_scripts');

// load custom post types and custom taxonomies
add_action('init', 'geckos_cpt');

// enqueue required scripts for media upload
add_action('admin_enqueue_scripts', 'geckos_admin_scripts');

// hook for adding meta boxes
add_action( 'add_meta_boxes_geckos-tabs', 'geckos_add_meta_box' );

// hook for saving meta box data
add_action( 'save_post_geckos-tabs', 'geckos_save_meta_boxes_data', 10, 2 );

/*
    5. CUSTOM POST TYPES
*/

if ( ! function_exists( 'geckos_cpt' ) ) :

  function geckos_cpt() {

  register_post_type('geckos-tabs', array(
      'labels'        => array(
              'name' => __( 'Vertical Tabs', 'geckos-kit' ),
              'singular_name' => __( 'Vertical Tab', 'geckos-kit' )
          ),
      'public'        => true,
      'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
      'menu_icon'     => 'dashicons-images-alt2',
      'has_archive'   => true,
      'show_in_menu'  => true,
      'taxonomies'    => array( 'ftabs-categories' )
  ));

/*
    6. CUSTOM TAXONOMIES
*/

  register_taxonomy('tabs-categories', 'geckos-tabs', array(
      'labels' =>
          array(
              'name' => __( 'Tabs Categories', 'geckos-kit' ),
              'singular_name' => __( 'Tabs Category', 'geckos-kit' ),
              'add_new_item' => __( 'Add New Tabs Category', 'geckos-kit' ),
              'edit_item' => __( 'Edit Tabs Category', 'geckos-kit' ),
              'update_item' => __( 'Update Tabs Category', 'geckos-kit' )
          ),
      'hierarchical' => true,
      'show_admin_column' => true
  ));

  }
endif;

/*
    7. CUSTOM FIELDS / META BOXES
*/
/**
 * ADD META BOX PRICE FOR PRICING TABLES
 *
 * @param post $post The post object
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
 */
if ( ! function_exists( 'geckos_add_meta_box' ) ) :
  function geckos_add_meta_box( $post ){
      add_meta_box( 'icon', __( 'Icon Selection', 'geckos-kit' ), 'geckos_build_meta_box', 'geckos-tabs', 'side', 'low' );
  }
endif;


/**
 * Build custom field meta box
 *
 * @param post $post The post object
 */
if ( ! function_exists( 'geckos_build_meta_box' ) ) :
  function geckos_build_meta_box( $post ){
      // keeping things save
      wp_nonce_field( basename( __FILE__ ), 'geckos_meta_box_nonce' );

      // retrieve the _geckos_icon current value
      $icon = get_post_meta( $post->ID, '_geckos_icon', true );

      ?>
      <div class='inside'>
          <h4><?php _e( 'Name of the icon', 'geckos-kit' ); ?></h4>
          <p><?php _e( 'Go to <a href="http://fontawesome.io/icons/" target="_blank">http://fontawesome.io/icons/</a> and enter the name of your selected icon into the field below', 'geckos-kit' ); ?></p>
          <p>
              <input type="text" name="icon" value="<?php echo $icon; ?>" />
          </p>
      </div>
      <?php
  }
endif;

/**
 * Store custom field meta box data
 *
 * @param int $post_id The post ID.
 */
if ( ! function_exists( 'geckos_save_meta_boxes_data' ) ) :
  function geckos_save_meta_boxes_data( $post_id ){
      // verify meta box nonce
      if ( !isset( $_POST['geckos_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['geckos_meta_box_nonce'], basename( __FILE__ ) ) ){
          return;
      }
      // Check the user's permissions.
      if ( ! current_user_can( 'edit_post', $post_id ) ){
          return;
      }
      // store custom fields values
      if ( isset( $_REQUEST['icon'] ) ) {
          update_post_meta( $post_id, '_geckos_icon', sanitize_text_field( $_POST['icon'] ) );
      }
  }
endif;
