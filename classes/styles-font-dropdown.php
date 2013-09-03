<?php

if ( !class_exists( 'Styles_Font_Dropdown' ) ) :

require_once dirname(__FILE__) . '/styles-font-dropdown-admin.php';
require_once dirname(__FILE__) . '/styles-fonts.php';
require_once dirname(__FILE__) . '/styles-standard-fonts.php';
require_once dirname(__FILE__) . '/styles-google-fonts.php';

add_action( 'init', create_function( '', 'new Styles_Font_Dropdown();'), 11 );

class Styles_Font_Dropdown {

	/**
	 * @var string The plugin version.
	 */
	var $version = '0.1';

	/**
	 * @var Styles_Font_Dropdown_Admin Methods for WordPress admin user interface.
	 */
	var $admin;

	/**
	 * @var Styles_Standard_Fonts Web standard font families and CSS font stacks.
	 */
	var $standard_fonts;

	/**
	 * @var Styles_Google_Fonts Connects to Google Font API.
	 */
	var $google_fonts;

	/**
	 * Set with site_url() because we might not be running as a plugin.
	 * 
	 * @var string URL for the styles-font-dropdown directory.
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

	public function __construct() {
		$this->plugin_directory = site_url( str_replace( ABSPATH, '', dirname( dirname( __FILE__ ) ) ) );
		$this->plugin_basename = plugin_basename( dirname( dirname( __FILE__ ) ) . '/plugin.php' );

		$this->admin = new Styles_Font_Dropdown_Admin( $this );
		$this->google_fonts = new Styles_Google_Fonts();
		$this->standard_fonts = new Styles_Standard_Fonts();

		/**
		 * Output dropdown menu anywhere styles_font_dropdown action is called.
		 * @example <code>do_action( 'styles_font_dropdown' );</code>
		 */
		add_action( 'styles_font_dropdown', array( $this, 'get_view_dropdown' ) );

	}

	public function print_scripts() {
		if ( $this->scripts_printed ) { return false; }

		wp_register_script( 'styles-chosen', $this->plugin_directory . '/js/chosen/chosen.jquery.min.js', array( 'jquery' ), $this->version );
		wp_register_script( 'styles-fonts-dropdown', $this->plugin_directory . '/js/styles-fonts-dropdown.js', array( 'jquery', 'styles-chosen' ), $this->version );
		wp_register_style( 'styles-chosen', $this->plugin_directory . '/js/chosen/chosen.min.css', array(), $this->version );

		// Pass Google Font Families to javascript
		// This saves on bandwidth by outputing them once,
		// then appending them to all <select> elements client-side
		wp_localize_script( 'styles-fonts-dropdown', 'styles_google_families', $this->google_fonts->families );

		// Output scripts and dependencies
		// Tracks whether dependencies have already been output
		wp_print_scripts( array( 'styles-fonts-dropdown' ) );
		wp_print_styles( array( 'styles-chosen' ) );

		$this->scripts_printed = true;
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