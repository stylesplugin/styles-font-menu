<div class="wrap styles-font-dropdown-readme">

	<?php screen_icon(); ?>
	<h2><?php _e('Font Dropdown Menu', 'styles-font-dropdown'); ?></h2>

	<h3>Example output</h3>
	<p><?php do_action( 'styles_fonts_dropdown' ); ?></p>

	<?php echo Markdown( file_get_contents( STYLES_FONT_DROPDOWN_DIR . '/readme.md' ) ); ?>

</div>

<style>
	.styles-font-dropdown-readme ul {
		list-style-type: disc;
		margin-left: 30px;
	}
	.example-output {
		display:none;
	}
</style>