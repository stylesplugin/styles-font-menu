<?php

class SFM_Image_Preview {

	/**
	 * @var string $_GET key that triggers this class to run
	 */
	protected $action_key = 'styles-font-preview';

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
		'font_baseline' => 64, // y-coordinate to place font baseline
		'left_margin' => 5,
		'width' => 500,
		'height' => 90,
		'background_color' => array( 255, 255, 255 ),
		'font_color' => array( 0, 0, 0 ),
	);

	public function __construct() {
		$uploads = wp_upload_dir();
		$this->fonts_directory = $uploads['basedir'] . '/styles-fonts';
		$this->fonts_directory_url = $uploads['baseurl'] . '/styles-fonts';

		if ( isset( $_GET[ $this->action_key ] ) ) {
			$this->get_font();
			$this->get_image();
		}

		exit;
	}

	public function get_font() {
		if ( empty( $_GET[ $this->action_key ] ) ) {
			wp_die( 'Please specify a font name.');
		}
		$this->font_family = $_GET[ $this->action_key ];

		$plugin = SFM_Plugin::get_instance();

		$this->font = $plugin->google_fonts->get_font_by_name( $this->font_family );
		if ( !$this->font ) {
			wp_die( 'Font not found: ' . $this->font_family );
		}

		$this->get_font_variant();

		$this->font_url = $this->font->files->{$this->font_variant};

		$nicename = strtolower( preg_replace( '/[^a-zA-Z0-9]/', '', $this->font_family ) );

		$this->font_filename =  "$nicename-{$this->font_variant}.ttf";
		$this->font_ttf_path = $this->fonts_directory . '/ttf/' . $this->font_filename;

		// Variant removed from png names
		$this->font_png_path = $this->fonts_directory .  "/png/$nicename.png";
		$this->font_png_url = $this->fonts_directory_url . "/png/$nicename.png";

		$this->maybe_get_remote_font();
	}

	public function get_font_variant() {
		if ( isset( $this->font_variant ) ) {
			return $this->font_variant;
		}
		$variant_request = ( isset( $_GET['variant'] ) ) ? $_GET['variant'] : false;

		if ( $variant_request ) {
			// Validate requested variant
			if ( $this->font->get_variant( $variant_request ) ) {
				$this->font_variant = $this->font->get_variant( $variant_request );
			}else {
				$variants = implode( '</li><li>', array_keys( (array) $this->font->variants ) );
				wp_die( 'Variant not found. Variants: <ul><li>' . $variants . '</li></ul>' );
			}
		}else {
			// Get default variant
			$this->font_variant = $this->font->default_variant;
		}

		return $this->font_variant;
	}

	public function get_image() {
		if ( file_exists( $this->font_png_path ) ) {
			exit( $this->font_png_url );
		}

		$this->generate_image();
	}

	public function generate_image() {
		$width = $height = $font_size = $left_margin = $font_baseline = $background_color = $font_color = false;
		extract( $this->preview_attributes, EXTR_IF_EXISTS );
		
		$image = imageCreate($width, $height);

		$background = imageColorAllocate($image, $background_color[0], $background_color[1], $background_color[2]);
		$foreground = imageColorAllocate($image, $font_color[0], $font_color[1], $font_color[2]);

		imagettftext($image, $font_size, 0, $left_margin, $font_baseline, $foreground, $this->font_ttf_path, $this->font_family );

		ob_start();
		imagePNG($image);
		$image = ob_get_clean();

		// Save image file
		$dir = dirname( $this->font_png_path );

		if ( !is_dir( $dir ) && !wp_mkdir_p( $dir ) ) { 
			wp_die( "Please check permissions. Could not create directory $dir" );
		}

		if ( !function_exists('WP_Filesystem')) { require ABSPATH . 'wp-admin/includes/file.php'; }
		global $wp_filesystem; WP_Filesystem();
		$image_file = $wp_filesystem->put_contents( $this->font_png_path, $image, FS_CHMOD_FILE ); // predefined mode settings for WP files

		if ( !$image_file ) {
			wp_die( "Please check permissions. Could not write image to $dir" );
		}

		// header("Content-type: image/png");
		// echo $image;

		echo $this->font_png_url;

		exit;
	}

	public function maybe_get_remote_font() {
		if ( file_exists( $this->font_ttf_path ) ) {
			return $this->font_ttf_path;
		}

		$dir = dirname( $this->font_ttf_path );

		if ( !function_exists('WP_Filesystem')) { require ABSPATH . 'wp-admin/includes/file.php'; }
		global $wp_filesystem; WP_Filesystem();

		if ( !is_dir( $dir ) && !wp_mkdir_p( $dir ) ) { 
			wp_die( "Please check permissions. Could not create directory $dir" );
		}

		$font_file = $wp_filesystem->put_contents( $this->font_ttf_path, $this->get_remote_font(), FS_CHMOD_FILE ); // predefined mode settings for WP files

		if ( $font_file ) {
			return $font_file;
		}else {
			wp_die( "Please check permissions. Could not write font to $dir" );
		}
	}

	public function get_remote_font() {
		if ( empty( $this->font_url ) ) {
			wp_die( 'Font URL not set.' );
		}
		
		$response = wp_remote_get( $this->font_url );

		if ( is_a( $response, 'WP_Error') ) {
			wp_die( "Attempt to get remote font returned an error.<br/>{$this->font_url}" );
		}

		return $response['body'];
	}

}