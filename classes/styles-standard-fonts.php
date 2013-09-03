<?php

class Styles_Standard_Fonts extends Styles_Fonts {

	protected $fonts = array( 'Arial' => 'Arial, Helvetica, sans-serif', 'Bookman' => 'Bookman, Palatino, Georgia, serif', 'Century Gothic' => '"Century Gothic", Helvetica, Arial, sans-serif', 'Comic Sans MS' => '"Comic Sans MS", Arial, sans-serif', 'Courier' => 'Courier, monospace', 'Garamond' => 'Garamond, Palatino, Georgia, serif', 'Georgia' => 'Georgia, Times, serif', 'Helvetica' => 'Helvetica, Arial, sans-serif', 'Lucida Grande' => '"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif', 'Palatino' => 'Palatino, Georgia, serif', 'Tahoma' => 'Tahoma, Verdana, Helvetica, sans-serif', 'Times' => 'Times, Georgia, serif', 'Trebuchet MS' => '"Trebuchet MS", Tahoma, Helvetica, sans-serif', 'Verdana' => 'Verdana, Tahoma, sans-serif', );
	protected $families;

	/**
	 * @var array Mult-dimensional array containing necessary font metadata
	 */
	protected $options = array(
		'import_template' => false,
		'fonts' => array(),
	);

	/**
	 * Fires when accessing $this->options from outside the class.
	 */
	public function get_options() {
		if ( !empty( $this->options['fonts'] ) ) { return $this->options; }

		foreach ( (array) $this->fonts as $name => $family ){
			$this->options['fonts'][] = array(
				'font_family' => $family,
				'font_name' => $name,
			);
		}

		return $this->options;
	}

	/**
	 * Fires when accessing $this->families from outside the class.
	 */
	public function get_families() {
		if ( !empty( $this->families ) ) { return $this->families; }

		foreach ( (array) $this->fonts as $family => $value ){
			$this->families[ $family ] = $value;
		}

		return $this->families;
	}

}