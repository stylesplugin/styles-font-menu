<?php

if ( !class_exists( 'Styles_Font_Dropdown' ) ) :

require_once dirname(__FILE__) . '/styles-fonts.php';
require_once dirname(__FILE__) . '/styles-standard-fonts.php';
require_once dirname(__FILE__) . '/styles-google-fonts.php';

add_action( 'init', create_function( '', 'new Styles_Font_Dropdown();'), 11 );

class Styles_Font_Dropdown {

	/**
	 * @var Styles_Standard_Fonts Web standard font families and CSS font stacks
	 */
	var $standard_fonts;

	/**
	 * @var Styles_Google_Fonts Connects to Google Font API
	 */
	var $google_fonts;

	var $version = '0.1';

	/**
	 * Set with site_url() because we might not be running as a plugin
	 * 
	 * @var string URL for the styles-font-dropdown directory
	 */
	var $plugin_directory;

	/**
	 * Intentionally inaccurate if we're running as a plugin.
	 * 
	 * @var string Plugin basename, only if we're running as a plugin.
	 */
	var $plugin_basename;

	/**
	 * print_scripts() runs as late as possible to avoid processing Google Fonts
	 * This prevents running multiple times
	 * @var bool Whether we have already registered scripts or not.
	 */
	var $scripts_printed = false;

	/**
	 * @var string Slug for readme at /wp-admin/plugins.php?page=$readme_page_slug
	 */
	var $readme_page_slug = 'styles-font-dropdown';

	public function __construct() {
		$this->plugin_directory = site_url( str_replace( ABSPATH, '', dirname( dirname( __FILE__ ) ) ) );
		$this->plugin_basename = plugin_basename( dirname( dirname( __FILE__ ) ) . '/plugin.php' );

		$this->google_fonts = new Styles_Google_Fonts();
		$this->standard_fonts = new Styles_Standard_Fonts();

		/**
		 * Output dropdown menu anywhere styles_font_dropdown action is called.
		 * @example <code>do_action( 'styles_font_dropdown' );</code>
		 */
		add_action( 'styles_font_dropdown', array( $this, 'get_dropdown' ) );

		// Readme page
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_readme_page' ) );

	}

	/**
	 * Add additional links to the plugin row
	 * If we're not running as a plugin, this won't do anything,
	 * because plugin_basename won't match any active plugin path.
	 */
	public function plugin_row_meta( $meta, $basename ) {
		if ( $basename == $this->plugin_basename ) {
			$meta[] = '<a href="' . network_admin_url( 'plugins.php?page=' . $this->readme_page_slug ) . '">How to use this plugin</a>';
		}
		return $meta;
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
	 * Display readme and working example in WordPress admin
	 * Does not add a menu item
	 * @link /wp-admin/plugins.php?page=styles-font-dropdown
	 */
	public function add_readme_page() {
		add_submenu_page( null, 'Font Dropdown Menu', 'Font Dropdown Menu', 'manage_options', $this->readme_page_slug, array( $this, 'readme_page' ) );
	}

	/**
	 * Display views/readme.php, which modifies readme.md to show a working example.
	 */
	public function readme_page() {
		if ( !function_exists( 'Markdown' ) ) {
			require_once dirname( __FILE__ ) . '/markdown/markdown.php';
		}
		$this->get_view( 'readme' );
	}

	/**
	 * Display views/dropdown.php
	 */
	public function get_dropdown() {
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