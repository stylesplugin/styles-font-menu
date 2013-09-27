<?php

class SFM_Single_Google extends SFM_Single_Standard {

	/**
	 * @var array Variant names
	 */
	protected $variants;

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

	public function get_variant( $variant ) {
		if ( in_array( $variant, (array) $this->variants ) ) {
			return $variant;
		}
		return false;
	}

	public function get_default_variant() {
		if ( isset( $this->default_variant ) ) {
			return $this->default_variant;
		}

		if ( in_array( 'regular', (array) $this->variants ) ) {
			$this->default_variant = 'regular';
		}else {
			$this->default_variant = $this->variants[0];
		}

		return $this->default_variant;
	}
	
}