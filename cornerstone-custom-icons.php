<?php
/*
Plugin Name: Cornerstone Custom Icons
Description: Add custom icon fonts and SVGs to the built in Cornerstone and Pro elements controls
Version:     0.2.2
Author:      Michael Bourne
Author URI:  https://michaelbourne.ca
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
Text Domain: cornerstone-custom-icons
Domain Path: /languages
*/
/**
Cornerstone Custom Icons is a plugin for WordPress that enables you to add custom icon fonts to the built in Cornerstone controls.
Copyright (c) 2018 Michael Bourne.

The Cornerstone Custom Icons Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>

You can contact me at michael@michaelbourne.ca
**/

defined( 'CCICONS_ROOT' ) or define( 'CCICONS_ROOT', dirname( __FILE__ ) );
defined( 'CCICONS_URI' ) or define( 'CCICONS_URI', plugin_dir_url( __FILE__ ) );
defined( 'CCICONS_VERSION' ) or define( 'CCICONS_VERSION', '0.2.1' );
defined( 'CCICONS_UPLOAD' ) or define( 'CCICONS_UPLOAD', 'cornerstone_icons_files' );

if ( ! class_exists( 'CCIcons' ) ) {

	class CCIcons {

		/**
		 * Core singleton class
		 *
		 * @var self - pattern realization
		 */
		private static $_instance;

		/**
		 * Prefix for plugin
		 *
		 * @var $prefix
		 */
		private $prefix;

		/**
		 * Path download folder
		 *
		 * @var $upload_dir
		 */
		public $upload_dir;

		/**
		 * URL download folder
		 *
		 * @var $upload_url
		 */
		private $upload_url;

		/**
		 * uploads folder name
		 *
		 * @var $upload_dir_single
		 */
		private $upload_dir_single;

		/**
		 * Prefix for custom icons
		 *
		 * @var $prefix_icon
		 */
		private $prefix_icon;

		/**
		 * Constructor.
		 */
		private function __construct() {

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			// merge css
			include_once( CCICONS_ROOT . '/includes/merge.css.php' );

			// save font class
			include_once( CCICONS_ROOT . '/includes/save.font.php' );

			// add menu item
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

			add_action( 'admin_init', array( $this, 'admin_init' ) );

			// add admin styles and scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// for front end
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 999999 );
			add_action( 'wp_enqueue_scripts_clean', array( $this, 'enqueue_scripts' ), 10 );

			// add icon css to footer for builder support
			add_action( 'wp_print_footer_scripts', array( $this, 'insert_footer_css' ) );

			// load icons
			add_action( 'cornerstone_config_common_font-icons', array( $this, 'icons_filters' ), 999 );
			add_filter( 'cornerstone_config_common_font-icons', array( $this, 'icons_filters' ), 20 );

			$upload = wp_upload_dir();

			// main variables
			$this->prefix = 'cci_';
			$this->prefix_icon = 'cci-';
			$this->upload_dir  = $upload['basedir'] . '/' . CCICONS_UPLOAD;
			$this->upload_url  = $upload['baseurl'] . '/' . CCICONS_UPLOAD;
			//$this->upload_dir_single = str_replace( get_option('siteurl'), '', $this->upload_url );

			// SSL fix because WordPress core function wp_upload_dir() doesn't check protocol.
			if ( is_ssl() ) $this->upload_url = str_replace( 'http://', 'https://', $this->upload_url );

			// load translations
			add_action( 'plugins_loaded', array( $this, 'cci_load_textdomain' ) );
		}

		/**
		 * Get the instance of CCIcons Plugins
		 *
		 * @return self
		 */
		public static function getInstance() {

			if ( ! ( self::$_instance instanceof self ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;

		}

		/**
		 * @param mixed $instance
		 */
		public static function setInstance( $instance ) {

			self::$_instance = $instance;

		}

		/**
		 * Init main functions (for hook admin_init)
		 */
		public function admin_init() {

			$this->settings_init();

			$saveFont = new SaveFont_CCIcons();
			$saveFont->init();

		}

		/**
		 * Internationalization
		 */
		public function cci_load_textdomain() {
			load_plugin_textdomain( 'cornerstone-custom-icons', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
		}

		/**
		 * Add new pages to admin
		 */
		public function add_admin_menu() {

			add_submenu_page( 
				'x-addons-home',
				__( 'Cornerstone Custom Icons', 'cornerstone-custom-icons' ), 
				__( 'Custom Icons', 'cornerstone-custom-icons' ), 
				'manage_options', 
				'cornerstone_icons_settings', 
				array(
					$this,
					'options_page',
				)
			);

		}

		/**
		 * Render all options
		 */
		public function options_page() {

			include_once 'includes/template.options.page.php';

		}


		/**
		 * CCIcons settings init
		 */
		protected function settings_init() {

			register_setting( $this->prefix . 'fontellos_page', $this->prefix . 'cornerstone_icons_settings' );
			add_settings_section( $this->prefix . 'fontellos_pluginPage_section', '', '', $this->prefix . 'fontellos_page' );

		}


		/**
		 * Admin enqueue scripts
		 */
		public function admin_enqueue_scripts() {

			wp_enqueue_style( 'admin_fontellos_css', CCICONS_URI . 'assets/css/cornerstone-custom-icons.css', array(), CCICONS_VERSION );

			wp_enqueue_script( 'cornerstone-custom-icons', CCICONS_URI . 'assets/js/cornerstone-custom-icons.js', array(
				'jquery',
			), CCICONS_VERSION, true );

			if ( is_admin() ) {
				$cci_script = array(
					'ajaxurl'    => admin_url( 'admin-ajax.php' ),
					'plugin_url' => CCICONS_URI,
					'exist'         => __( "This font file already exists. Make sure you're giving it a unique name!", 'cornerstone-custom-icons' ), 
					'failedopen'    => __( 'Failed to open the ZIP archive. If you uploaded a valid ZIP file, your host may be blocking this PHP function. Please get in touch with them.', 'cornerstone-custom-icons' ), 
					'failedextract' => __( 'Failed to extract the ZIP archive. Your host may be blocking this PHP function. Please get in touch with them.', 'cornerstone-custom-icons' ), 
					'emptyfile'     => __( 'Your browser failed to upload the file. Please try again.', 'cornerstone-custom-icons' ), 
					'regen'         => __( 'Custom Icon CSS file has been regenerated.', 'cornerstone-custom-icons' ), 
					'delete'        => __( 'Are you sure you want to delete this font?', 'cornerstone-custom-icons' ), 
					'updatefailed'  => __( 'Plugin failed to update the WP options table.', 'cornerstone-custom-icons' ), 
				);
				wp_localize_script( 'cornerstone-custom-icons', 'CC_ICONS', $cci_script );
			}

		}

		/**
		 * Enqueue scripts
		 */
		public function enqueue_scripts() {

			if ( file_exists( $this->upload_dir . '/merged-icons-font.css' ) ) {

				$modtime = @filemtime( $this->upload_dir . '/merged-icons-font.css' );
				if(!$modtime){ $modtime = mt_rand(); }
				wp_enqueue_style( 'cci-icon-fonts', esc_url( $this->upload_url . '/merged-icons-font.css' ), false, $modtime );
			}

		}

		/**
		 * Add custom font CSS to footer so it actually works in the builder
		 * - to do: get this working with the builder boilerplate action
		 */
		public function insert_footer_css() {

			if( current_user_can( 'manage_options' ) ){

				if ( file_exists( $this->upload_dir . '/merged-icons-font.css' ) ) {

					$modtime = @filemtime( $this->upload_dir . '/merged-icons-font.css' );
					if(!$modtime){ $modtime = mt_rand(); }
					echo '<link rel="stylesheet" type="text/css" href="' . $this->upload_url . '/merged-icons-font.css?ver=' . $modtime . '">';
				}
			}
		}


		/**
		 * @param Get font info
		 *
		 * @return array
		 */
		public function get_config_font( $file_name ) {

			$file_config = glob( $this->upload_dir . '/' . $file_name . '/*/*' );
			$data        = array();
			$css_folder  = '';

			foreach ( $file_config as $key => $file ) {

				if ( strpos( $file, 'config.json' ) !== false ) {
					$file_info               = json_decode( file_get_contents( $file ) );
					$data['name']            = trim($file_info->name);
					$data['icons']           = $file_info->glyphs;
					$data['css_prefix_text'] = $file_info->css_prefix_text;
				}

				if ( is_string( $file ) && strpos( $file, 'css' ) !== false ) {
					$file_part          = explode( CCICONS_UPLOAD . '/', $file );
					$data['css_folder'] = $file;
					$css_folder         = $file_part[1];
				}

				if ( is_string( $file ) && strpos( $file, 'font' ) !== false ) {
					$file_part        = explode( CCICONS_UPLOAD . '/', $file );
					//$data['font_url'] = content_url() . $file_part[1];
					$data['font_url'] = $file_part[1];
				}

			}

			if ( empty( $data['name'] ) ) {
				$data['name'] = 'font' . mt_rand();
				$data['nameempty'] = true;
				$data['css_root']  = $data['css_folder'] . '/fontello.css';
				$data['css_url']   = $this->upload_url . $css_folder . '/fontello.css';
			} else {
				$data['css_root']  = $data['css_folder'] . '/' . $data['name'] . '.css';
				$data['css_url']   = $this->upload_url . $css_folder . '/' . $data['name'] . '.css';
			}

			$data['file_name'] = $file_name;

			return $data;

		}

		/**
		 * Add new icons to Cornerstone
		 *
		 * @param $config
		 *
		 * @return array
		 */
		public function icons_filters( $config ) {

			$options = get_option( 'cc_icons_fonts' );

			$icons = array();
			$icons_reverse = array();
			if ( empty( $options ) ) {
				return $config;
			}
			foreach ( $options as $key => $font ) {
				if ( $font['status'] == '1' ) {

					$font_data = json_decode($font['data'],true);

					$new_icons = $this->parse_css( $font_data['css_root'], $font_data['name'] );
					if ( !empty($new_icons) && is_array( $new_icons ) ) {
						$icons = array_merge( $new_icons, $icons );
					}

					$new_icons_reverse = $this->parse_css_reverse( $font_data['css_root'], $font_data['name'] );
					if ( !empty($new_icons_reverse) && is_array( $new_icons_reverse ) ) {
						$icons_reverse = array_merge($new_icons_reverse, $icons_reverse);
					}

				}
			}


			if( array_key_exists('icons', $config) && is_array($config['icons'])){
					$config['icons'] = $icons + $config['icons'];

					if( array_key_exists('solid', $config) && is_array($config['solid'])){
						$config['solid'] = $icons_reverse + $config['solid'];
					}
			} else {
				$config = array_merge($icons, $config);
			}

			return $config;
		}


		/**
		 * Parse CSS to get icons.
		 *
		 * @param $css_file
		 *
		 * @return array
		 */
		protected function parse_css( $css_file, $name ) {

			$icons = array();
			if ( ! file_exists( $css_file ) ) {
				return null;
			}
			$css_source = file_get_contents( $css_file );

			preg_match_all( "/\.\w*?\-(.*?):\w*?\s*?{?\s*?{\s*?\w*?:\s*?\'\\\\?(\w*?)\'.*?}/", $css_source, $matches, PREG_SET_ORDER, 0 );
			foreach ( $matches as $match ) {
				$icons[ $name . '-' . $match[1] ] = $match[2];
			}

			return $icons;

		}


		/**
		 * Parse CSS to get icon reverse alias.
		 *
		 * @param $css_file
		 *
		 * @return array
		 */
		protected function parse_css_reverse( $css_file, $name ) {

			$icons = array();
			if ( ! file_exists( $css_file ) ) {
				return null;
			}
			$css_source = file_get_contents( $css_file );

			preg_match_all( "/\.\w*?\-(.*?):\w*?\s*?{?\s*?{\s*?\w*?:\s*?\'\\\\?(\w*?)\'.*?}/", $css_source, $matches, PREG_SET_ORDER, 0 );
			foreach ( $matches as $match ) {
				$icons[] = $name . '-' . $match[1];
			}

			return $icons;

		}

		/**
		 * remove folder (recursive)
		 *
		 * @param $dir
		 */
		protected function rrmdir( $dir ) {

			if ( is_dir( $dir ) ) {
				$objects = scandir( $dir );
				foreach ( $objects as $object ) {
					if ( $object != "." && $object != ".." ) {
						if ( is_dir( $dir . "/" . $object ) ) {
							$this->rrmdir( $dir . "/" . $object );
						} else {
							unlink( $dir . "/" . $object );
						}
					}
				}
				rmdir( $dir );
			}

		}

		/**
		 * @param        $name
		 * @param bool   $default
		 * @param string $type
		 *
		 * @return bool|string
		 */
		protected function getRequest( $name, $default = false, $type = 'POST' ) {

			$TYPE = ( strtolower( $type ) == 'post' ) ? $_POST : $_GET;
			if ( ! empty( $TYPE[ $name ] ) ) {
				return sanitize_text_field( $TYPE[ $name ] );
			}

			return $default;

		}

	}

	CCIcons::getInstance();

	/**
	 * Main manager
	 */
	function cc_icons_manager() {
		return CCIcons::getInstance();
	}

}