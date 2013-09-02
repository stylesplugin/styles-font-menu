<div class="wrap styles-font-dropdown-readme">

	<?php screen_icon(); ?>
	<h2><?php _e('Font Dropdown Menu', 'styles-font-dropdown'); ?></h2>

	<h3 class="example-output">Example output</h3>
	<p><?php do_action( 'styles_fonts_dropdown' ); ?></p>

	<?php echo Markdown( file_get_contents( dirname( dirname( __FILE__ ) ) . '/readme.md' ) ); ?>

</div>

<style>
	.styles-font-dropdown-readme ul {
		list-style-type: disc;
		margin-left: 30px;
	}
</style>

<script>
	jQuery(document).ready( function( $ ){
		// Remove image of example output
		$('h3.example-output').nextAll('h2').first().remove();
		$('img[src*="example-output.png"]').remove();

		// Remove directions on how to get to this demo
		var $demo = $('h2:contains(Live Demo)');
		$demo.nextUntil('h2').remove();
		$demo.remove();
	});
</script>