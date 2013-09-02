<select class="styles-font-dropdown" data-placeholder="Select a Font...">
	<option value=""></option>

	<optgroup label="Standard Fonts">
		<?php foreach ( $this->standard_fonts->families as $name => $font_stack ): ?>
			<option value="<?php echo $font_stack ?>"><?php echo $name ?></option>
		<?php endforeach; ?>
	</optgroup>

	<?php /* Google Fonts loaded by styles-fonts-dropdown.js */ ?>
</select>