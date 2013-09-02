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

if ( !function_exists( 'styles_font_dropdown_init' ) ) :

function styles_font_dropdown_init() {
	if ( is_admin() ) {
		$exit_message = esc_html__( 'Styles Font Dropdown requires PHP 5.2.4 or newer. <a href="http://wordpress.org/about/requirements/">Please update.</a>', 'styles-font-dropdown' );
		if ( version_compare( PHP_VERSION, '5.2.4', '<' ) ) {
			exit( $exit_message );
		}
	}

	// Won't apply if we're not running as a plugin
	if ( !defined( 'STYLES_FONT_DROPDOWN_BASENAME' ) ) define( 'STYLES_FONT_DROPDOWN_BASENAME', plugin_basename( __FILE__ ) );

	if ( !class_exists( 'Styles_Font_Dropdown' ) ) {
		require_once dirname( __FILE__ ) . '/classes/styles-font-dropdown.php';
	}

}
add_action( 'plugins_loaded', 'styles_font_dropdown_init' );

endif;
