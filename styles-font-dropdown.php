<?php
/*
Plugin Name: Styles: Font Dropdown
Plugin URI: http://stylesplugin.com
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

add_action( 'admin_init', create_function( '', 'new Styles_Font_Dropdown();') );

require_once dirname( __FILE__ ) . '/classes/styles-fonts.php';
require_once dirname( __FILE__ ) . '/classes/styles-standard-fonts.php';
require_once dirname( __FILE__ ) . '/classes/styles-google-fonts.php';

class Styles_Font_Dropdown {

	/**
	 * @var Styles_Google_Fonts Connects to Google Font API
	 */
	var $google_fonts;

	var $version = '1.0';

	public function __construct() {
		$this->google_fonts = new Styles_Google_Fonts();
		$this->standard_fonts = new Styles_Standard_Fonts();
		$this->register_scripts();

		/**
		 * Output dropdown menu anywhere styles_fonts_dropdown action is called.
		 * @example <code>do_action( 'styles_fonts_dropdown' );</code>
		 */
		add_action( 'styles_fonts_dropdown', array( $this, 'get_dropdown' ) );

		// Testing only
		add_action( 'admin_init', array( $this, 'test_dropdown_action' ), 11 );
	}

	public function register_scripts() {
		wp_register_script( 'styles-chosen', plugins_url( 'js/chosen/chosen.jquery.min.js', __FILE__ ), array( 'jquery' ), $this->version );
		wp_register_script( 'styles-fonts-dropdown', plugins_url( 'js/styles-fonts-dropdown.js', __FILE__ ), array( 'jquery', 'styles-chosen' ), $this->version );
		wp_register_style( 'styles-chosen', plugins_url( 'js/chosen/chosen.min.css', __FILE__ ), array(), $this->version );

		// Pass Google Font Families to javascript
		// This saves on bandwidth by outputing them once,
		// then appending them to all <select> elements client-side
		wp_localize_script( 'styles-fonts-dropdown', 'styles_google_families', $this->google_fonts->families );
	}

	/**
	 * Make sure the output action works. Testing only.
	 */
	public function test_dropdown_action() {
		do_action( 'styles_fonts_dropdown' );
	}

	public function get_dropdown() {
		$this->get_view( 'dropdown' );
	}

	public function get_view( $file = 'dropdown' ) {
		// Ensure dependencies have been output by now.
		wp_print_scripts( array( 'styles-fonts-dropdown' ) );
		wp_print_styles( array( 'styles-chosen' ) );

		$file = dirname( __FILE__ ) . "/views/$file.php";
		if ( file_exists( $file ) ) {
			include $file;
		}
	}
}