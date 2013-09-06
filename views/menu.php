<?php
	// Load Google Fonts and scripts only once and as late as possible
	$this->print_scripts();
?>

<select class="<?php echo $this->menu_class ?>" data-placeholder="Select a Font...">
	<option value=""></option>

	<optgroup label="Standard Fonts">
		<?php foreach ( $this->standard_fonts->fonts as $font ): ?>
			<option class="sf <?php echo $font->classname ?>" value="<?php esc_attr_e( $font ) ?>"><?php echo $font->name ?></option>
		<?php endforeach; ?>
	</optgroup>

	<?php echo $this->google_fonts->fonts ?>

	<?php /* 
		Google Fonts loaded by styles-font-menu.js

		This is done for performance reasons. The list is 600+ fonts.
		In cases where the dropdown is used multiple times on one page,
		outputting the HTML server-side can result in a page of several megabytes.

		This avoids that by outputting the list once in javascript,
		then building the menus with javascript on the client-side.
	*/ ?>
</select>