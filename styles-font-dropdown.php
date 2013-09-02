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

	const font_api_url = 'https://www.googleapis.com/webfonts/v1/webfonts';

	/**
	 * @var Styles_Google_Fonts Connects to Google Font API
	 */
	var $google_fonts;

	public function __construct() {
		$this->google_fonts = new Styles_Google_Fonts();

		echo '<pre>';
		print_r( $this->google_fonts->families );
		exit;
	}

}

class Styles_Google_Fonts {

	var $cache_interval = 1296000; // 15 days

	/**
	 * @var stdClass Response from Google API listing all fonts
	 */
	private $fonts;

	/**
	 * @var array All font families mentioned in $fonts
	 */
	private $families;

	public function __construct() {
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

		set_transient( 'styles_google_fonts', $this->fonts, $this->$cache_interval );
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