jQuery( document ).ready( function( $ ){

	$('select.styles-font-dropdown').stylesFontDropdown();

});

(function( $, styles_google_families ) {
	$.stylesFontDropdown = function(element, options) {

		var plugin = this;
		var defaults = {};
		plugin.settings = {};

		var $element = $(element),
				 element = element;

		plugin.init = function() {
				plugin.settings = $.extend({}, defaults, options);

				plugin.populate_google_fonts();

				$element.chosen({
					"allow_single_deselect": true,
					"inherit_select_classes": true
				});
		}

		plugin.populate_google_fonts = function() {
			console.log( styles_google_families );

			var google_options = "<optgroup class='google-fonts' label='Google Fonts'>";
			$.each( styles_google_families, function( i, name ){
				google_options += "<option value='" + name + "'>" + name + "</option>";
			});
			google_options += "</optgroup>"

			$element.append( google_options ).each( function(){
				var selected = $(this).data('selected');
				$(this).find( 'option[value="' + selected + '"]' ).attr('selected', 'selected');
			} );
		}

		plugin.init();

	}

	$.fn.stylesFontDropdown = function(options) {
		return this.each(function() {
			if (undefined == $(this).data('stylesFontDropdown')) {
				var plugin = new $.stylesFontDropdown(this, options);
				$(this).data('stylesFontDropdown', plugin);
			}
		});
	}

})(jQuery, styles_google_families );