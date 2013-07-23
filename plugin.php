<?php
/*
Plugin Name: Woocommerce Category Best Seller Widget
Plugin URI: http://steveostudios.tv/woocommerce-category-best-sellers-widget
Description: Widget that displays a specified number of best sellers in the same category.
Version: 1.0
Author: Steve Stone
Author URI: http://steveostudios.tv
Author Email: steveostudios@gmail.com
Text Domain: woocommerce-category-best-seller-locale
Domain Path: /lang/
Network: false
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2012 Steve Stone (steveostudios@gmail.com)

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

class Woocommerce_Category_Best_Seller extends WP_Widget {

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		parent::__construct(
			'woocommerce-category-best-seller',
			__( 'Woocommerce Category Best Sellers', 'woocommerce_category_best_seller_locale' ),
			array(
				'classname'		=>	'woocommerce-category-best-seller',
				'description'	=>	__( 'Widget that displays a specified number of best sellers in the same category.', 'woocommerce_category_best_seller_locale' )
			)
		);
		
		$this->init_plugin_constants();

		// Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );

	} // end constructor

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$title = empty($instance['title']) ? '' : apply_filters('title', $instance['title']);  
    $category_id = empty($instance['category_id']) ? '' : apply_filters('category_id', $instance['category_id']);  
    $display_count = empty($instance['display_count']) ? '' : apply_filters('display_count', $instance['display_count']);
    
    // Start of My Code
    global $post;
    $terms = get_the_terms( $post->ID, 'product_cat' );
    $best_sellers = array();
    $best_seller_categories = array();
    $this_id = array($post->ID);
    foreach ($terms as $term) {
      if (term_is_ancestor_of($category_id, $term, 'product_cat')) {
        $product_cat_id = $term->term_id;
        $product_cat_name = $term->name;
        $product_cat_slug = $term->slug;
     
        $args = array( 
          'post_type' => 'product', 
          'posts_per_page' => $display_count,
          'post_status' 	 => 'publish',
          'product_cat' => $product_cat_slug, 
          'post__not_in' => $this_id,
          'meta_key' 		 => 'total_sales',
          'orderby' 		 => 'meta_value_num',
        );
        $partner_products = new WP_Query( $args );
        
        if ( $partner_products->have_posts() ) {
          $best_seller_categories[$product_cat_id] = array(
            'name' => $product_cat_name,
            'slug' => $product_cat_slug
          );

          $best_sellers[$product_cat_id] = array();
          global $product;
          while ( $partner_products->have_posts() ) {
            $partner_products->the_post();
            
            $img = null;
            if (has_post_thumbnail( $partner_products->post->ID )) {
              $img = get_the_post_thumbnail($partner_products->post->ID, 'small'); 
            }
            
            $best_sellers[$product_cat_id][$partner_products->post->ID] = array(
              'name' => $partner_products->post->post_title,
              'img' => $img,
              'price' => get_post_meta( $partner_products->post->ID, '_regular_price', true),
              'sale_price' => get_post_meta( $partner_products->post->ID, '_sale_price', true)
            );
          }
        }
      }
    }
    // End of My Code

		include( plugin_dir_path( __FILE__ ) . '/views/widget.php' );

	} // end widget

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The previous instance of values before the update.
	 * @param	array	old_instance	The new instance of values to be generated via the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
      
    $instance['title'] = strip_tags(stripslashes($new_instance['title']));  
    $instance['category_id'] = strip_tags(stripslashes($new_instance['category_id']));  
    $instance['display_count'] = strip_tags(stripslashes($new_instance['display_count']));

		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
			  'title' => '',
			  'category_id' => 13,
			  'display_count' => 5
			)
		);
		
    $args = array(
			'hierarchical'       => 1,
			'show_option_none'   => '',
			'hide_empty'         => 0,
			'orderby'                  => 'name',
			'taxonomy'           => 'product_cat'
		);
  	$categories = get_categories($args);
		
		$new_instance = $instance;

		$title = strip_tags(stripslashes($new_instance['title'])); 
		$category_id = strip_tags(stripslashes($new_instance['category_id']));
		$display_count = strip_tags(stripslashes($new_instance['display_count']));

		// Display the admin form
		include( plugin_dir_path(__FILE__) . '/views/admin.php' );

	} // end form

	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/
	
	private function init_plugin_constants() { 
 
    if(!defined('PLUGIN_LOCALE')) { 
      define('PLUGIN_LOCALE', 'woocommerce_category_best_seller_locale'); 
    } // end if 
 
    if(!defined('PLUGIN_NAME')) { 
      define('PLUGIN_NAME', 'Woocommerce Category Best Sellers'); 
    } // end if 
 
    if(!defined('PLUGIN_SLUG')) { 
      define('PLUGIN_SLUG', 'Woocommerce-Category-Best-Sellers'); 
    } // end if 
   
  }

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {
		load_plugin_textdomain( 'woocommerce_category_best_seller_locale', false, plugin_dir_path( __FILE__ ) . '/lang/' );
	}
	
	/**
	 * Fired when the plugin is activated.
	 *
	 * @param		boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
		// TODO define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// TODO define deactivation functionality here
	}

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {
		wp_enqueue_style( 'woocommerce_category_best_seller_styles', plugins_url( 'woocommerce_category_best_seller/css/admin.css' ) );
	}

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {
		wp_enqueue_script( 'woocommerce_category_best_seller_script', plugins_url( 'woocommerce_category_best_seller/js/admin.js' ), array('jquery') );
	}

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {
		wp_enqueue_style( 'woocommerce_category_best_seller_styles', plugins_url( 'woocommerce_category_best_seller/css/widget.css' ) );
	}

	/**
	 * Registers and enqueues widget-specific scripts.
	 */
	public function register_widget_scripts() {
		wp_enqueue_script( 'woocommerce_category_best_seller_script', plugins_url( 'woocommerce_category_best_seller/js/widget.js' ), array('jquery') );
	}

} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("Woocommerce_Category_Best_Seller");' ) );
