<select class="styles-font-dropdown" data-placeholder="Select a Font...">

	<option value=""></option>

	<!-- Google Fonts -->
	<?php if ( is_array( $this->google_fonts->families) ): ?>
		<optgroup class="google-fonts" label="Google Fonts">
		<?php foreach ( $this->google_fonts->families as $font ) : ?>
			<option class="gf"><?php echo $font ?></option>
		<?php endforeach; ?>
		</optgroup>
	<?php endif; ?>
</select>