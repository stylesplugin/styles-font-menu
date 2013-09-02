<?php

class Styles_Google_Fonts extends Styles_Fonts {

	const font_api_url = 'https://www.googleapis.com/webfonts/v1/webfonts';

	/**
	 * @example Override with <code>add_filter( 'styles_google_fonts_cache_interval', function(){ return 60*60*24*1; } );</code>
	 * @var int Seconds before cache expires. Defaults to 15 days.
	 */
	var $cache_interval;

	/**
	 * @var stdClass Response from Google API listing all fonts
	 */
	protected $fonts;

	/**
	 * @var array All font families mentioned in $fonts
	 */
	protected $families;

	public function __construct() {
		$this->cache_interval = apply_filters( 'styles_google_fonts_cache_interval', 60*60*24*15 ); // 15 days
	}

	/**
	 * Fires when accessing $this->fonts from outside the class.
	 */
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

	/**
	 * Fires when accessing $this->families from outside the class.
	 */
	public function get_families() {
		if ( !empty( $this->families ) ) { return $this->families; }

		foreach ( (array) $this->get_fonts()->items as $font ){
			$variants = str_replace( ' ', '+', $font->family ) . ':' . implode( ',', $font->variants );
			$this->families[ $font->family ] = $variants;
		}

		return $this->families;
	}
	
}