<?php
/*
Plugin Name: Styles: Font Menu
Plugin URI: http://github.com/stylesplugin/styles-font-menu
Description: Display an up-to-date menu of Google Fonts. Include it in your own plugins and themes, or install as a plugin for testing and a live demo. Uses the Chosen library to allow menu search and styles.
Version: 0.1
Author: Brainstorm Media
Author URI: http://brainstormmedia.com
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Include this file in your own plugins and themes, 
 * or install it as a stand-alone plugin for testing.
 * 
 * @example include 'styles-font-menu/plugin.php';
 */
if ( !function_exists( 'styles_font_menu_init' ) ) :

function styles_font_menu_init() {

	if ( is_admin() ) {

		/**
		 * Require PHP 5.2.4. Link to WordPress codex article if we need user to upgrade.
		 */
		$required_php_version = '5.2.4';
		$exit_message = esc_html__( "Styles Font Menu requires PHP $required_php_version or newer. <a href='http://wordpress.org/about/requirements/'>Please update.</a>", 'styles-font-menu' );

		if ( version_compare( PHP_VERSION, $php_version_required, '<' ) ) {

			/**
			 * Exit and warn by default. Use the filter to disable exiting,
			 * or add your own behavior and return false.
			 * 
			 * @example add_filter( 'styles_font_menu_include_on_frontend', '__return_false' );
			 */
			if ( apply_filters( 'styles_font_menu_exit_on_php_version_error', true ) ) {
				exit( $exit_message );
			}else {
				return false;
			}

		}
	}

	/**
	 * Only include library in admin by default. Override with the filter
	 * 
	 * @example add_filter( 'styles_font_menu_include_on_frontend', '__return_true' );
	 */
	if ( apply_filters( 'styles_font_menu_include_on_frontend', is_admin() ) ) {
		if ( !class_exists( 'SFM_Plugin' ) ) {
			require_once dirname( __FILE__ ) . '/classes/sfm-plugin.php';
		}
	}

}

if ( did_action( 'init' ) ) {
	styles_font_menu_init();
}else {
	add_action( 'init', 'styles_font_menu_init' );
}


endif;
