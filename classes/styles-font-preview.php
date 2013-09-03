<?php

class Styles_Font_Preview {

	/**
	 * @var Styles_Font_Dropdown Pointer to parent/wrapper object.
	 */
	var $plugin;

	/**
	 * @var string Absolute path to cached fonts.
	 */
	var $fonts_directory;

	/**
	 * @var string Name of the font we're previewing.
	 */
	var $font_family;

	/**
	 * @var array Display attributes for the preview image and font
	 */
	var $preview_attributes = array(
		'font_size' => 48,
		'font_baseline' => 54, // y-coordinate to place font baseline
		'left_margin' => 5,
		'width' => 300,
		'height' => 75,
		'background_color' => array( 255, 255, 255 ),
		'font_color' => array( 0, 0, 0 ),
	);

	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$uploads = wp_upload_dir();
		$this->fonts_directory = $uploads['basedir'] . '/styles-fonts';

		$this->get_font();

		$this->generate_image();
	}

	public function get_font() {
		if ( empty( $_GET['styles-font-preview'] ) ) {
			wp_die( 'Please specify a font name.');
		}
		$this->font_family = $_GET['styles-font-preview'];

		// Find font in Google Fonts JSON object
		foreach ( $this->plugin->google_fonts->fonts->items as $font ) {
			if ( $this->font_family == $font->family ) {
				$this->font = $font;
				break;
			}
		}

		// Set target variant
		if ( isset( $_GET['variant'] ) ) {
			if ( in_array( $_GET['variant'], (array) $this->font->files ) ) {
				$this->font_variant = $_GET['variant'];
			}else {
				$variants = implode( '</li><li>', array_keys( (array) $this->font->files ) );
				wp_die( 'Variant not found. Variants: <ul><li>' . $variants . '</li></ul>' );
			}
		}else {
			$this->font_variant = 'regular';
		}

		$this->font_url = $this->font->files->{$this->font_variant};

		$nicename = strtolower( preg_replace( '/[^a-zA-Z0-9]/', '', $this->font_family ) );

		$this->font_filename =  "$nicename-{$this->font_variant}.ttf";
		$this->font_path = $this->fonts_directory . '/' . $this->font_filename;

		$this->maybe_get_remote_font();
	}

	public function generate_image() {
		$width = $height = $font_size = $left_margin = $font_baseline = $background_color = $font_color = false;
		extract( $this->preview_attributes, EXTR_IF_EXISTS );
		
		$image = imageCreate($width, $height);

		$background = imageColorAllocate($image, $background_color[0], $background_color[1], $background_color[2]);
		$foreground = imageColorAllocate($image, $font_color[0], $font_color[1], $font_color[2]);

		imagettftext($image, $font_size, 0, $left_margin, $font_baseline, $foreground, $this->font_path, $this->font_family );

		header("Content-type: image/png");
		imagePNG($image);

		exit;
	}

	public function maybe_get_remote_font() {
		if ( file_exists( $this->font_path ) ) {
			return $this->font_path;
		}

		if ( !function_exists('WP_Filesystem')) { require ABSPATH . 'wp-admin/includes/file.php'; }
		global $wp_filesystem; WP_Filesystem();

		if ( !is_dir( $dir ) && !wp_mkdir_p( $dir ) ) { 
			wp_die( "Please check permissions. Could not create directory $dir" );
		}

		$font_file = $wp_filesystem->put_contents( $this->font_path, $this->get_remote_font(), FS_CHMOD_FILE ); // predefined mode settings for WP files

		if ( $font_file ) {
			return $font_file;
		}else {
			wp_die( "Please check permissions. Could not write font to $dir" );
		}
	}

	public function get_remote_font() {
		$response = wp_remote_get( $this->font_url );

		if ( is_a( $response, 'WP_Error') ) {
			wp_die( "Attempt to get remote font returned an error.<br/>{$this->font_url}" );
		}

		return $response['body'];
	}

}