<?php
	// Load Google Fonts and scripts only once and as late as possible
	$this->print_scripts();
?>

<select class="styles-font-dropdown" data-placeholder="Select a Font...">
	<option value=""></option>

	<optgroup label="Standard Fonts">
		<?php foreach ( $this->standard_fonts->families as $name => $font_stack ): ?>
			<option value="<?php echo $font_stack ?>"><?php echo $name ?></option>
		<?php endforeach; ?>
	</optgroup>

	<?php /* 
		Google Fonts loaded by styles-fonts-dropdown.js

		This is done for performance reasons. The list is 600+ fonts.
		In cases where the dropdown is used multiple times on one page,
		outputting the HTML server-side can result in a page of several megabytes.

		This avoids that by outputting the list once in javascript,
		then building the menus with javascript on the client-side.
	*/ ?>
</select>