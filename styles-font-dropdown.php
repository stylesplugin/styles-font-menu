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

class Styles_Font_Dropdown {

	/**
	 * @var Styles_Google_Fonts Connects to Google Font API
	 */
	var $google_fonts;

	var $version = '1.0';

	public function __construct() {
		$this->google_fonts = new Styles_Google_Fonts();

		/**
		 * Output dropdown menu anywhere styles_fonts_dropdown action is called.
		 * @example <code>do_action( 'styles_fonts_dropdown' );</code>
		 */
		add_action( 'styles_fonts_dropdown', array( $this, 'get_dropdown' ) );

		// Testing only
		add_action( 'admin_init', array( $this, 'test_dropdown_action' ), 11 );

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

class Styles_Google_Fonts {

	const font_api_url = 'https://www.googleapis.com/webfonts/v1/webfonts';

	/**
	 * @example Override with <code>add_filter( 'styles_google_fonts_cache_interval', function(){ return 60*60*24*1; } );</code>
	 * @var int Seconds before cache expires. Defaults to 15 days.
	 */
	var $cache_interval;

	/**
	 * @var stdClass Response from Google API listing all fonts
	 */
	private $fonts;

	/**
	 * @var array All font families mentioned in $fonts
	 */
	private $families;

	public function __construct() {
		$this->cache_interval = apply_filters( 'styles_google_fonts_cache_interval', 60*60*24*15 ); // 15 days

		$this->get_fonts();
	}

	/**
	 * If client tries to access variables directly, pass to get() method
	 */
	public function __get( $target ) {
		return $this->get( $target );
	}

	/**
	 * If a get_XXX method exists for a variable, use it.
	 * Otherwise, return the variable value
	 */
	public function get( $target = 'fonts' ) {
		$method = 'get_' . $target;
		if ( method_exists( __CLASS__, $method ) ) {
			return $this->$method();
		}else if ( isset( $this->$target ) ){
			return $this->$target;
		}else {
			return false;
		}
	}

	public function get_fonts() {
		// Return from cache if available
		$this->fonts = get_transient( 'styles_google_fonts' );
		if ( false !==  $this->fonts ) { return $this->fonts; }

		// Bail if no API key is set
		$api_key = apply_filters( 'styles_google_font_api', '' );
		if ( empty( $api_key ) ) { return false; }

		// Construct request
		$url = add_query_arg( 'sort', apply_filters( 'styles_google_font_sort', 'popularity' ), self::font_api_url );
		$url = add_query_arg( 'key', $api_key, $url );
		$response = wp_remote_get( $url );

		if ( is_a( $response, 'WP_Error') ) { return false; }

		$fonts = json_decode( $response['body'] );

		if ( !is_array( $fonts->items ) ) {
			// @todo check to see if this messes up the caching above
			$this->fonts = null;
		}

		set_transient( 'styles_google_fonts', $this->fonts, $this->cache_interval );
		return $this->fonts;
	}

	public function get_families() {
		if ( !empty( $this->families ) ) { return $this->families; }

		foreach ( (array) $this->fonts->items as $font ){
			$this->families[] = $font->family;
		}

		return $this->families;
	}


}