<select class="styles-font-dropdown" data-placeholder="Select a Font...">
	<option value=""></option>

	<optgroup label="Standard Fonts">
		<?php foreach ( $this->standard_fonts->families as $name ): ?>
			<option value="<?php echo $name ?>"><?php echo $name ?></option>
		<?php endforeach; ?>
	</optgroup>

	<?php /* Google Fonts loaded by styles-fonts-dropdown.js */ ?>
</select>