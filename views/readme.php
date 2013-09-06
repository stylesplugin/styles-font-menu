<div class="wrap" id="styles-font-dropdown-readme">

	<?php screen_icon(); ?>
	<h2><?php _e('Font Dropdown Menu', 'styles-font-dropdown'); ?></h2>

	<h3 class="example-output">Example output</h3>
	<p><?php do_action( 'styles_font_menu' ); ?></p>

	<?php echo Markdown( file_get_contents( dirname( dirname( __FILE__ ) ) . '/readme.md' ) ); ?>

</div>

<style>
	#styles-font-dropdown-readme > ul {
		list-style-type: disc;
		margin-left: 30px;
	}
</style>

<script>

	/**
	 * Change heading font-family on menu change event
	 */
	(function($){

		var $headings = $( 'h2,h3', '#styles-font-dropdown-readme' );
		
		$('select.styles-font-dropdown').change( function(){
			$(this).data('stylesFontDropdown').preview_font_change( $headings );
		});

	})(jQuery);

	/**
	 * Modify readme.md content:
	 *  - Hide directions on how to get to this page
	 *  - Hide menu screenshot (live demo displayed above)
	 */
	(function($){

		// Remove image of example output
		$('h3.example-output').nextAll('h2').first().remove();
		$('img[src*="example-output.gif"]').remove();

		// Remove directions on how to get to this demo
		var $demo = $('h2:contains(Live Demo)');
		$demo.nextUntil('h2').remove();
		$demo.remove();

	})(jQuery);

</script>