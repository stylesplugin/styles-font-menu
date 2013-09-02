<?php
/*
Plugin Name: Styles: Font Dropdown
Plugin URI: http://github.com/stylesplugin/styles-font-dropdown
Description: Display a drop-down of Google Fonts with previews.
Version: 1.0
Author: Brainstorm Media
Author URI: http://brainstormmedia.com
*/

/**
 * Copyright (c) 2013 Brainstorm Media. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

add_action( 'admin_menu', create_function( '', 'new Styles_Font_Dropdown();') );

define( 'STYLES_FONT_DROPDOWN_DIR', dirname( __FILE__ ) );

if ( !class_exists( 'Styles_Font_Dropdown' ) ):

require_once STYLES_FONT_DROPDOWN_DIR . '/classes/styles-fonts.php';
require_once STYLES_FONT_DROPDOWN_DIR . '/classes/styles-standard-fonts.php';
require_once STYLES_FONT_DROPDOWN_DIR . '/classes/styles-google-fonts.php';

class Styles_Font_Dropdown {

	/**
	 * @var Styles_Standard_Fonts Web standard font families and CSS font stacks
	 */
	var $standard_fonts;

	/**
	 * @var Styles_Google_Fonts Connects to Google Font API
	 */
	var $google_fonts;

	var $version = '1.0';

	/**
	 * register_scripts() runs as late as possible to avoid processing Google Fonts
	 * This prevents running multiple times
	 */
	var $scripts_registered = false;

	var $readme_page_slug = 'styles-font-dropdown';

	public function __construct() {
		$this->google_fonts = new Styles_Google_Fonts();
		$this->standard_fonts = new Styles_Standard_Fonts();

		/**
		 * Output dropdown menu anywhere styles_fonts_dropdown action is called.
		 * @example <code>do_action( 'styles_fonts_dropdown' );</code>
		 */
		add_action( 'styles_fonts_dropdown', array( $this, 'get_dropdown' ) );

		// Example page
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'add_readme_page' ), 11 );

	}

	/**
	 * Add additional links to the plugin row
	 */
	public function plugin_row_meta( $meta, $basename ) {
		if ( $basename == plugin_basename( __FILE__ ) ) {
			$meta[] = '<a href="' . network_admin_url( 'plugins.php?page=' . $this->readme_page_slug ) . '">How to use this plugin</a>';
		}
		return $meta;
	}

	public function register_scripts() {
		if ( $this->scripts_registered ) { return false; }

		wp_register_script( 'styles-chosen', plugins_url( 'js/chosen/chosen.jquery.min.js', __FILE__ ), array( 'jquery' ), $this->version );
		wp_register_script( 'styles-fonts-dropdown', plugins_url( 'js/styles-fonts-dropdown.js', __FILE__ ), array( 'jquery', 'styles-chosen' ), $this->version );
		wp_register_style( 'styles-chosen', plugins_url( 'js/chosen/chosen.min.css', __FILE__ ), array(), $this->version );

		// Pass Google Font Families to javascript
		// This saves on bandwidth by outputing them once,
		// then appending them to all <select> elements client-side
		wp_localize_script( 'styles-fonts-dropdown', 'styles_google_families', $this->google_fonts->families );

		$this->scripts_registered = true;
	}

	/**
	 * Make sure the output action works. Testing only.
	 */
	public function add_readme_page() {
		add_submenu_page( null, 'Font Dropdown Menu', 'Font Dropdown Menu', 'manage_options', $this->readme_page_slug, array( $this, 'readme_page' ) );
	}

	public function readme_page() {
		if ( !function_exists( 'Markdown' ) ) {
			require_once STYLES_FONT_DROPDOWN_DIR . '/classes/markdown/markdown.php';
		}
		$this->get_view( 'readme' );
	}

	public function get_dropdown() {
		$this->get_view( 'dropdown' );
	}

	public function get_view( $file = 'dropdown' ) {
		// Load Google Fonts and scripts as late as possible
		$this->register_scripts();
		wp_print_scripts( array( 'styles-fonts-dropdown' ) );
		wp_print_styles( array( 'styles-chosen' ) );

		$file = STYLES_FONT_DROPDOWN_DIR . "/views/$file.php";
		if ( file_exists( $file ) ) {
			include $file;
		}
	}
}

endif;