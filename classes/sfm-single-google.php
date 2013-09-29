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

	protected $png_url;

	/**
	 * Values for this font that should go into JSON encoded <option> values
	 */
	protected $option_value_whitelist = array( 'family', 'name', 'import_family', 'classname', 'png_url' );

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

	/**
	 * This function can lead to get_remote_ttf
	 * For that reason, it shouldn't be called on init
	 * Right now, it's only called in an AJAX request for a font preview
	 */
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
		$uploads = wp_upload_dir();
		$fonts_directory = $uploads['basedir'] . '/styles-fonts';
		$fonts_directory_url = $uploads['baseurl'] . '/styles-fonts';

		// Variant meta
		$variant = array();
		$variant['name']     = $variant_name;
		$variant['filename'] = $this->get_nicename() . '-' . $variant_name;
		// Todo: detect path; check if file exists in plugin or uploads dir
		$variant['png_path'] = $fonts_directory . '/png/' . $variant['filename'] . '.png';
		$variant['png_url']  = $fonts_directory_url . '/png/' . $variant['filename'] . '.png';;
		$variant['ttf_path'] = $fonts_directory . '/ttf/' . $variant['filename'] . '.ttf';
		$variant['ttf_url']  = $this->files->{$variant_name};

		$this->variant = $variant;

		return $this->variant;
	}

	/**
	 * @return string sanatized font family name for use in file names.
	 */
	public function get_nicename() {
		if ( isset( $this->nicename ) ) {
			return $this->nicename;
		}
		$this->nicename = strtolower( preg_replace( '/[^a-zA-Z0-9]/', '', $this->family ) );
		return $this->nicename;
	}

	/**
	 * @return string path to the cached or downloaded TTF file
	 */
	public function maybe_get_remote_ttf() {
		$variant = $this->get_variant();
		if ( file_exists( $variant['ttf_path'] ) ) {
			return $variant['ttf_path'];
		}else {
			return $this->get_remote_ttf();
		}
	}

	/**
	 * @return string path to the cached TTF file received from remote request.
	 */
	public function get_remote_ttf() {
		// Setup
		$variant = $this->get_variant();
		$dir = dirname( $variant['ttf_path'] );

		// Load filesystem
		if ( !function_exists('WP_Filesystem')) { require ABSPATH . 'wp-admin/includes/file.php'; }
		global $wp_filesystem;
		WP_Filesystem();

		// Create cache directory
		if ( !is_dir( $dir ) && !wp_mkdir_p( $dir ) ) { 
			wp_die( "Please check permissions. Could not create directory $dir" );
		}

		// Cache remote TTF to filesystem
		$ttf_file_path = $wp_filesystem->put_contents(
			$variant['ttf_path'],
			$this->get_remote_ttf_contents(),
			FS_CHMOD_FILE // predefined mode settings for WP files
		);

		// Check file saved
		if ( !$ttf_file_path ) {
			wp_die( "Please check permissions. Could not write font to $dir" );
		}
		
		return $ttf_file_path;
	}

	/**
	 * @return binary The active variant's TTF file contents
	 */
	public function get_remote_ttf_contents() {
		$variant = $this->get_variant();

		if ( empty( $variant['ttf_url'] ) ) {
			wp_die( 'Font URL not set.' );
		}
		
		$response = wp_remote_get( $variant['ttf_url'] );

		if ( is_a( $response, 'WP_Error') ) {
			wp_die( "Attempt to get remote font returned an error.<br/>{$variant['ttf_url']}" );
		}

		return $response['body'];
	}

	/**
	 * @return string URL of image preview PNG for the active variant
	 */
	public function get_png_url() {
		$variant = $this->get_variant();

		if ( file_exists( $variant['png_path'] ) ) {
			return $variant['png_url'];
		}

		return false;
	}
	
}