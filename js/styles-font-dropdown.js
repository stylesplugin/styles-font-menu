jQuery( document ).ready( function( $ ){

	// Add Google Fonts and Chosen to select elements
	$('select.styles-font-dropdown').stylesFontDropdown();

});

(function( $, styles_google_options ) {

	/**
	 * Build Google Fonts option list only once
	 */
	var google_options = "<optgroup class='google-fonts' label='Google Fonts'>";
	for (var i=0; i < styles_google_options.fonts.length; i++){
		google_options += "<option value='" + JSON.stringify( styles_google_options.fonts[i] ) + "'>" + styles_google_options.fonts[i].font_family + "</option>";
	}
	google_options += "</optgroup>";

	/**
	 * Define jQuery plugin to act on and attach to select elements
	 */
	$.stylesFontDropdown = function(element, options) {

		var plugin = this,
				$element = $(element);

		/**
		 * Default settings. Override by passing object to stylesFontDropdown()
		 */
		var defaults = {
					"chosen_settings": {
						"allow_single_deselect": true,
						"inherit_select_classes": true
					}
				};

		plugin.settings = {};

		plugin.init = function() {
				plugin.settings = $.extend({}, defaults, options);

				plugin.populate_google_fonts();

				$element.chosen( plugin.settings.chosen_settings );
		};

		plugin.populate_google_fonts = function() {
			$element.append( google_options ).each( function(){
				// If a selected option is set in <option data-selected="XXX">, select it.
				// @todo Not sure why this is here. Carried over from old Styles text selector. Check back when connecting to database.
				var selected = $(this).data('selected');
				$(this).find( 'option[value="' + selected + '"]' ).attr('selected', 'selected');
			} );

		};

		plugin.init();

	};

	/**
	 * Attach this plugin instance to the target elements
	 * Access later with $('select.styles-font-dropdown').data('stylesFontDropdown');
	 */
	$.fn.stylesFontDropdown = function(options) {
		return this.each(function() {
			if (undefined === $(this).data('stylesFontDropdown')) {
				var plugin = new $.stylesFontDropdown(this, options);
				$(this).data('stylesFontDropdown', plugin);
			}
		});
	};

})( jQuery, styles_google_options );