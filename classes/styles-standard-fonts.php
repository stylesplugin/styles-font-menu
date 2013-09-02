<?php

class Styles_Standard_Fonts extends Styles_Fonts {

	protected $fonts = array( 'Arial' => 'Arial, Helvetica, sans-serif', 'Bookman' => 'Bookman, Palatino, Georgia, serif', 'Century Gothic' => '"Century Gothic", Helvetica, Arial, sans-serif', 'Comic Sans MS' => '"Comic Sans MS", Arial, sans-serif', 'Courier' => 'Courier, monospace', 'Garamond' => 'Garamond, Palatino, Georgia, serif', 'Georgia' => 'Georgia, Times, serif', 'Helvetica' => 'Helvetica, Arial, sans-serif', 'Lucida Grande' => '"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana,sans-serif', 'Palatino' => 'Palatino, Georgia, serif', 'Tahoma' => 'Tahoma, Verdana, Helvetica, sans-serif', 'Times' => 'Times, Georgia, serif', 'Trebuchet MS' => '"Trebuchet MS", Tahoma, Helvetica, sans-serif', 'Verdana' => 'Verdana, Tahoma, sans-serif', );
	protected $families;

	/**
	 * Fires when accessing $this->families from outside the class.
	 */
	public function get_families() {
		if ( !empty( $this->families ) ) { return $this->families; }

		foreach ( (array) $this->fonts as $family => $value ){
			$this->families[] = $family;
		}

		return $this->families;
	}

}