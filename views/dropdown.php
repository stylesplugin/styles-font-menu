<?php
	// Load Google Fonts and scripts only once and as late as possible
	$this->print_scripts();
?>

<select class="styles-font-dropdown" data-placeholder="Select a Font...">
	<option value=""></option>

	<optgroup label="Standard Fonts">
		<?php foreach ( $this->standard_fonts->options['fonts'] as $font ): ?>
			<?php $class = strtolower( preg_replace( '/[^a-zA-Z0-9]/', '', $font['font_name'] ) ); ?>
			<option class="<?php echo $class ?>" value="<?php echo esc_attr( json_encode($font) ) ?>"><?php echo $font['font_name'] ?></option>
		<?php endforeach; ?>
	</optgroup>

	<?php /* 
		Google Fonts loaded by styles-font-dropdown.js

		This is done for performance reasons. The list is 600+ fonts.
		In cases where the dropdown is used multiple times on one page,
		outputting the HTML server-side can result in a page of several megabytes.

		This avoids that by outputting the list once in javascript,
		then building the menus with javascript on the client-side.
	*/ ?>
</select>