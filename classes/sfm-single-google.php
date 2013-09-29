<?php

class SFM_Single_Google extends SFM_Single_Standard {

	/**
	 * @var array Variant names
	 */
	protected $variants;

	/**
	 * @var array Info on active variant, for image previews
	 */
	protected $variant;

	/**
	 * @var string Key for variant to use if none set
	 */
	protected $default_variant;

	/**
	 * @var array URLs to TTF files with variants as array keys.
	 */
	protected $files;

	/**
	 * @string Variation of name for insertion into @import CSS string.
	 */
	protected $import_family;

	/**
	 * Values for this font that should go into JSON encoded <option> values
	 */
	protected $option_value_whitelist = array( 'family', 'name', 'import_family', 'classname' );

	/**
	 * @var array Options to pass to javascript
	 */
	protected $options;

	public function __construct( $args = array() ) {
		parent::__construct( $args );

		$this->variants = $args['variants'];
		$this->files = $args['files'];
		$this->import_family = $this->get_import_family();
	}

	public function get_import_family() {
		return str_replace( ' ', '+', $this->family ) . ':' . implode( ',', $this->variants );
	}

	public function get_variant( $variant_request = false ) {
		if ( isset( $this->variant ) ) {
			return $this->variant;
		}

		if ( empty( $variant_request ) && isset( $_GET['variant'] ) ) {
			$variant_request = $_GET['variant'];
		}

		if ( empty( $variant_request ) ) {
			// No variant requested. Give default.
			if ( in_array( 'regular', (array) $this->variants ) ) {
				$variant_name = 'regular';
			}else {
				$variant_name = $this->variants[0];
			}
		}else if ( in_array( $variant_request, (array) $this->variants ) ) {
			// Variant requested and found
			$variant_name = $variant;
		}

		if ( !$variant_name ) {
			// Requested a variant, but none found
			$variants = implode( '</li><li>', array_keys( (array) $this->variants ) );
			wp_die( 'Variant not found. Variants: <ul><li>' . $variants . '</li></ul>' );
		}

		// Paths
		// Todo: detect path; check if file exists in plugin or uploads dir
		$uploads = wp_upload_dir();
		$fonts_directory = $uploads['basedir'] . '/styles-fonts';
		$fonts_directory_url = $uploads['baseurl'] . '/styles-fonts';

		// Variant meta
		$variant = array();
		$variant['name']     = $variant_name;
		$variant['filename'] = $this->get_nicename() . '-' . $variant_name;
		$variant['png_path'] = $fonts_directory . '/png/' . $variant['filename'] . '.png';
		$variant['png_url']  = $fonts_directory_url . '/png/' . $variant['filename'] . '.png';;
		$variant['ttf_path'] = $fonts_directory . '/ttf/' . $variant['filename'] . '.ttf';
		$variant['ttf_url']  = $this->files->{$variant_name};

		$this->variant = $variant;

		return $this->variant;
	}

	public function get_nicename() {
		if ( isset( $this->nicename ) ) {
			return $this->nicename;
		}
		$this->nicename = strtolower( preg_replace( '/[^a-zA-Z0-9]/', '', $this->family ) );
		return $this->nicename;
	}
	
}