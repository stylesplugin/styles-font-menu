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

	public function get_variant( $variant = false ) {
		if ( isset( $this->variant ) ) {
			return $this->variant;
		}

		if ( empty( $variant ) && isset( $_GET['variant'] ) ) {
			$variant = $_GET['variant'];
		}

		if ( in_array( $variant, (array) $this->variants ) ) {
			$this->variant = array(
				'name' => $variant,
				'url' => $this->files->{$variant},
			);
			return $this->variant;
		}

		// Requested a variant, but none found
		if ( isset( $_GET['variant'] ) ) {
			$variants = implode( '</li><li>', array_keys( (array) $this->variants ) );
			wp_die( 'Variant not found. Variants: <ul><li>' . $variants . '</li></ul>' );
		}

		return $this->get_default_variant();
	}

	public function get_default_variant() {
		if ( isset( $this->variant ) ) {
			return $this->variant;
		}

		if ( in_array( 'regular', (array) $this->variants ) ) {
			$variant = 'regular';
		}else {
			$variant = $this->variants[0];
		}

		$this->variant = array(
			'name' => $variant,
			'url' => $this->files->{$variant},
		);
		return $this->variant;
	}
	
}