<?php
/**
 * Plugin Name: MLR Tabs
 * Plugin URI: http://lillistone.me/2016/04/tab-demo/
 * Description: Easy add extra tabs to pages using shortcode or action hooks.
 * Version: 0.1
 * Author: Matthew Lillistone
 * Author URI: http://lillistone.me
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   MLR Tabs
 * @version   0.1
 * @since     0.1
 * @author    Matthew Lillistone <matthewlillistone.co.uk>
 * @copyright Copyright (c) 2012 - 2013, Matthew Lillistone
 * @link      http://lillistone.me
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */
	
	final class ml_wpsc_info_tabs {
	
		/**
         * Holds the instance of this class.
         *
         * @since  0.1
         * @access private
         * @var    object
         */
        private static $instance;

        /**
         * Stores the directory path for this plugin.
         *
         * @since  0.1
         * @access private
         * @var    string
         */
        private $directory_path;

        /**
         * Stores the directory URI for this plugin.
         *
         * @since  0.1
         * @access private
         * @var    string
         */
        private $directory_uri;
		
		/**
         * Construct
         *
         * @since  0.1
         * @access private
         * @var    string
         */
		public function __construct() {
			
			/* Set the properties needed by the plugin. */
            add_action( 'plugins_loaded', array( $this, 'setup' ), 1 );
			
			/* Load the functions files. */
            add_action( 'plugins_loaded', array( $this, 'includes' ), 3 );
			
			/* Load admin scripts */
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 5 );
				
			/* Enqueue scripts and styles. */
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 15 );
			
		}
		
		/**
         * Defines the directory path and URI for the plugin.
         *
         * @since  0.1
         * @access public
         * @return void
         */
        public function setup() {
			global $wpdb;
                $this->directory_path = trailingslashit( plugin_dir_path( __FILE__ ) );
                $this->directory_uri  = trailingslashit( plugin_dir_url(  __FILE__ ) );

                /* Legacy */
                define( 'MLR_product_tabs_DIR', $this->directory_path );
                define( 'MLR_product_tabs_URI', $this->directory_uri  );
        }
		
		
        /**
         * Loads the initial files needed by the plugin.
         *
         * @since  0.1
         * @access public
         * @return void
         */
        public function includes() {
                require_once( "{$this->directory_path}inc/ml_product_tabs.php" );
                require_once( "{$this->directory_path}inc/ml_product_tabs_custom.php" );
        }

		
		/**
         * Loads the initial files needed by the plugin.
         *
         * @since  0.1
         * @access public
         * @return void
         */
        public function enqueue_admin_scripts() {
				$list = 'enqueued';
				if(!wp_script_is( 'wp-color-picker', $list )) {
					wp_enqueue_script('wp-color-picker');
					wp_enqueue_style('wp-color-picker');
				}
				if(!wp_script_is('jquery-ui-sortable', $list)) {
					wp_enqueue_script( 'jquery-ui-sortable', array('jquery'), true);
				}
				
				wp_enqueue_script('ml-product-admin-js', "{$this->directory_uri}js/ml.producttabs.admin.js", array('jquery'), false, true);
				wp_enqueue_style( 'ml-admin-tabs-css', "{$this->directory_uri}css/admin_tabs.css" );
        }
		
		
		/**
		 * Enqueues scripts and styles on the front end.
		 *
		 * @since 0.1
		 * @access public
		 * @return void
		 */
		 public function enqueue_scripts() {
				$list = 'enqueued';
				(!wp_script_is( 'jquery-ui-slider', $list )) ? wp_enqueue_script( 'jquery-ui-slider', array('jquery'), '0.1', false ) : '';
				
				wp_enqueue_script('ml-product-tabs-js', "{$this->directory_uri}js/ml.product.tabs.js", array('jquery'), false, true);
				wp_enqueue_script('ml-product-imgs-js', "{$this->directory_uri}js/imagesloaded.pkgd.min.js", array('jquery'), false, true); 		
				wp_enqueue_style( 'ml-product-tabs-css', "{$this->directory_uri}css/tabs.css" );
		}
		 
		
		
        /**
         * Returns the instance.
         *
         * @since  0.1
         * @access public
         * @return object
         */
        public static function get_instance() {

                if ( !self::$instance )
                        self::$instance = new self;

                return self::$instance;
        }
}

ml_wpsc_info_tabs::get_instance();
	
?>