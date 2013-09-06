<?php

if ( !class_exists( 'SFM_Plugin' ) ) :

require_once dirname(__FILE__) . '/sfm-admin.php';
require_once dirname(__FILE__) . '/sfm-font-group.php';
require_once dirname(__FILE__) . '/sfm-font-group-standard.php';
require_once dirname(__FILE__) . '/sfm-font-group-google.php';
require_once dirname(__FILE__) . '/sfm-font-standard.php';
require_once dirname(__FILE__) . '/sfm-font-google.php';

add_action( 'init', 'SFM_Plugin::get_instance', 11 );

/**
 * Controller class
 * Holds instances of models in vars
 * Loads views from views/ directory
 * 
 * Follows the Singleton pattern. @see http://jumping-duck.com/tutorial/wordpress-plugin-structure/
 * @example Access plugin instance with $font_dropdown = SFM_Plugin::get_instance();
 */
class SFM_Plugin {

	/**
	 * @var string The plugin version.
	 */
	var $version = '0.1';

	/**
	 * @var Styles_Font_Menu Instance of the class.
	 */
	protected static $instance = false;

	/**
	 * @var string Class to apply to menu element and prefix to selectors.
	 */
	protected $menu_class = 'sfm';

	/**
	 * @var SFM_Admin Methods for WordPress admin user interface.
	 */
	var $admin;

	/**
	 * @var SFM_Font_Group_Standard Web standard font families and CSS font stacks.
	 */
	var $standard_fonts;

	/**
	 * @var SFM_Font_Group_Google Connects to Google Font API.
	 */
	var $google_fonts;

	/**
	 * @var SFM_Image_Preview Generate image preview of a font.
	 */
	var $font_preview;

	/**
	 * Set with site_url() because we might not be running as a plugin.
	 * 
	 * @var string URL for the styles-font-menu directory.
	 */
	var $plugin_directory;

	/**
	 * Intentionally inaccurate if we're running as a plugin.
	 * 
	 * @var string Plugin basename, only if we're running as a plugin.
	 */
	var $plugin_basename;

	/**
	 * print_scripts() runs as late as possible to avoid processing Google Fonts.
	 * This prevents running multiple times.
	 * 
	 * @var bool Whether we have already registered scripts or not.
	 */
	var $scripts_printed = false;

	/**
	 * Don't use this. Use ::get_instance() instead.
	 */
	public function __construct() {
		if ( !self::$instance ) {
			$message = '<code>' . __CLASS__ . '</code> is a singleton.<br/> Please get an instantiate it with <code>' . __CLASS__ . '::get_instance();</code>';
			wp_die( $message );
		}
	}

	public static function get_instance() {
		if ( !is_a( self::$instance, __CLASS__ ) ) {
			self::$instance = true;
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Initial setup. Called by get_instance.
	 */
	protected function init() {
		$this->plugin_directory = site_url( str_replace( ABSPATH, '', dirname( dirname( __FILE__ ) ) ) );
		$this->plugin_basename = plugin_basename( dirname( dirname( __FILE__ ) ) . '/plugin.php' );

		$this->admin = new SFM_Admin( $this );
		$this->google_fonts = new SFM_Font_Group_Google();
		$this->standard_fonts = new SFM_Font_Group_Standard();

		/**
		 * Output dropdown menu anywhere styles_font_menu action is called.
		 * @example <code>do_action( 'styles_font_menu' );</code>
		 */
		add_action( 'styles_font_menu', array( $this, 'get_view_dropdown' ) );

		/**
		 * Generate an image preview of a font
		 */
		add_action( 'init', array( $this, 'font_preview' ), 12 );
	}

	public function print_scripts() {
		if ( $this->scripts_printed ) { return false; }

		wp_register_script( 'styles-chosen', $this->plugin_directory . '/js/chosen/chosen.jquery.min.js', array( 'jquery' ), $this->version );
		wp_register_script( 'styles-font-menu', $this->plugin_directory . '/js/styles-font-menu.js', array( 'jquery', 'styles-chosen' ), $this->version );
		wp_register_style( 'styles-chosen', $this->plugin_directory . '/js/chosen/chosen.css', array(), $this->version );
		// wp_register_style( 'styles-chosen', $this->plugin_directory . '/js/chosen/chosen.min.css', array(), $this->version );

		// Pass Google Font Families to javascript
		// This saves on bandwidth by outputing them once,
		// then appending them to all <select> elements client-side
		wp_localize_script( 'styles-font-menu', 'styles_google_options', $this->google_fonts->option_values );

		// Output scripts and dependencies
		// Tracks whether dependencies have already been output
		wp_print_scripts( array( 'styles-font-menu' ) );
		wp_print_styles( array( 'styles-chosen' ) );

		$this->scripts_printed = true;
	}

	public function font_preview() {
		if ( !isset( $_GET['styles-font-preview'] ) ) {
			return false;
		}
		if ( !class_exists( 'SFM_Image_Preview') ) {
			require_once dirname( __FILE__ ) . '/sfm-image-preview.php';
		}
		$this->font_preview = new SFM_Image_Preview( $this );
	}

	/**
	 * Display views/dropdown.php
	 */
	public function get_view_dropdown() {
		$this->get_view( 'dropdown' );
	}

	/**
	 * Display any view from the views/ directory.
	 * Allows views to have access to $this
	 */
	public function get_view( $file = 'dropdown' ) {
		$file = dirname( dirname( __FILE__ ) ) . "/views/$file.php";
		if ( file_exists( $file ) ) {
			include $file;
		}
	}
}

endif;