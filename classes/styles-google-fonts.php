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

	/**
	 * @var array Mult-dimensional array containing necessary font metadata
	 */
	protected $options = array(
		'import_template' => "@import url(//fonts.googleapis.com/css?family=@import_family@);\r",
		'fonts' => array(),
	);

	/**
	 * @var string path to JSON backup of Google API response. In case API fails or is unavailable.
	 */
	protected $api_fallback_file;

	public function __construct() {
		$this->cache_interval = apply_filters( 'styles_google_fonts_cache_interval', 60*60*24*15 ); // 15 days
		$this->api_fallback_file = dirname( dirname( __FILE__ ) ) . '/js/google-fonts-api-fallback.json';
	}

	/**
	 * Fires when accessing $this->fonts from outside the class.
	 */
	public function get_fonts() {
		// If we already processed fonts, return them.
		if ( !empty( $this->fonts ) ) {
			return $this->fonts;
		}

		// If fonts are cached in the transient, return them.
		$this->fonts = get_transient( 'styles_google_fonts' );
		if ( false !== $this->fonts ) {
			return $this->fonts;
		}

		// If no cache, try connecting to Google API
		$this->fonts = $this->remote_get_google_api();

		// If Google API failed, use the fallback file.
		if ( !is_object( $this->fonts ) || !is_array( $this->fonts->items ) ) {
			$this->fonts = $this->get_api_fallback();
			return $this->fonts;
		}

		// API returned some good data. Cache it to the transient
		// and update the fallback file.
		set_transient( 'styles_google_fonts', $this->fonts, $this->cache_interval );
		$this->set_api_fallback();

		return $this->fonts;
	}

	/**
	 * Fires when accessing $this->options from outside the class.
	 */
	public function get_options() {
		if ( !empty( $this->options['fonts'] ) ) { return $this->options; }

		foreach ( (array) $this->get_fonts()->items as $font ){

			// Exclude non-latin fonts
			if ( !in_array('latin', $font->subsets ) ) {
				continue;
			}

			$import_family = str_replace( ' ', '+', $font->family ) . ':' . implode( ',', $font->variants );
			
			$this->options['fonts'][] = array(
				'import_family' => $import_family,
				'font_family' => $font->family,
			);
		}

		return $this->options;
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

	/**
	 * Connect to the remote Google API. Fall back to get_api_fallback on failure.
	 */
	public function remote_get_google_api() {
		// API key must be set with this filter
		$api_key = apply_filters( 'styles_google_font_api', '' );
		
		// Bail if no API key is set
		if ( empty( $api_key ) ) { return $this->get_api_fallback(); }

		// Construct request
		$url = add_query_arg( 'sort', apply_filters( 'styles_google_font_sort', 'popularity' ), self::font_api_url );
		$url = add_query_arg( 'key', $api_key, $url );
		$response = wp_remote_get( $url );

		// If response is an error, use the fallback file
		if ( is_a( $response, 'WP_Error') ) { return $this->get_api_fallback(); }

		return json_decode( $response['body'] );
	}

	/**
	 * If the we don't have a Google API key, or the request fails,
	 * use the contents of this file instead.
	 * 
	 * @todo Rework this and set_api_fallback to use transients and write to disk using WP_Filesystem so we don't have two caching mechanisms going on at once.
	 */
	public function get_api_fallback() {
		$this->fonts = json_decode( file_get_contents( $this->api_fallback_file ) );
		return $this->fonts;
	}

	/**
	 * Save Google Fonts API response to file for cases where we
	 * don't have an API key or the API request fails
	 * 
	 * @todo Write with WP_Filesystem instead of file_put_contents
	 */
	public function set_api_fallback() {
		if ( is_writable( $this->api_fallback_file ) ) {
			file_put_contents( $this->api_fallback_file, json_encode( $this->fonts ) );
		}
	}
	
}