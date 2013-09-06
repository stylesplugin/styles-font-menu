<div class="wrap" id="styles-font-dropdown-readme">

	<?php screen_icon(); ?>
	<h2><?php _e('Font Dropdown Menu', 'styles-font-dropdown'); ?></h2>

	<p><a href="#" id="generate-previews">Generate Font Previews</a></p>

	<h3 class="example-output">Example output</h3>
	<p><?php do_action( 'styles_font_menu' ); ?></p>

	<?php echo Markdown( file_get_contents( dirname( dirname( __FILE__ ) ) . '/readme.md' ) ); ?>


</div>

<style>
	#styles-font-dropdown-readme > ul {
		list-style-type: disc;
		margin-left: 30px;
	}
	.styles-font-dropdown .chosen-results {
		max-height: 400px !important;
	}

.styles-font-dropdown .arial, .styles-font-dropdown .bookman, .styles-font-dropdown .centurygothic, .styles-font-dropdown .comicsansms, .styles-font-dropdown .courier, .styles-font-dropdown .garamond, .styles-font-dropdown .georgia, .styles-font-dropdown .helvetica, .styles-font-dropdown .lucidagrande, .styles-font-dropdown .palatino, .styles-font-dropdown .tahoma, .styles-font-dropdown .times, .styles-font-dropdown .trebuchetms, .styles-font-dropdown .verdana {
	font-size: 48px;
	height:64px;
	line-height: 64px !important;
	white-space: nowrap;
}

.styles-font-dropdown .arial { font-family: Arial, Helvetica, sans-serif; }
.styles-font-dropdown .bookman { font-family: Bookman, Palatino, Georgia, serif; }
.styles-font-dropdown .centurygothic { font-family: "Century Gothic", Helvetica, Arial, sans-serif; }
.styles-font-dropdown .comicsansms { font-family: "Comic Sans MS", Arial, sans-serif; }
.styles-font-dropdown .courier { font-family: Courier, monospace; }
.styles-font-dropdown .garamond { font-family: Garamond, Palatino, Georgia, serif; }
.styles-font-dropdown .georgia { font-family: Georgia, Times, serif; }
.styles-font-dropdown .helvetica { font-family: Helvetica, Arial, sans-serif; }
.styles-font-dropdown .lucidagrande { font-family: "Lucida Sans Unicode",Tahoma,Verdana,sans-serif; }
.styles-font-dropdown .palatino { font-family: Palatino, Georgia, serif; }
.styles-font-dropdown .tahoma { font-family: Tahoma, Verdana, Helvetica, sans-serif; }
.styles-font-dropdown .times { font-family: Times, Georgia, serif; }
.styles-font-dropdown .trebuchetms { font-family: "Trebuchet MS", Tahoma, Helvetica, sans-serif; }
.styles-font-dropdown .verdana { font-family: Verdana, Tahoma, sans-serif; }
</style>

<script>

	/**
	 * Change heading font-family on menu change event
	 */
	(function($){

		var $headings = $( 'h2,h3', '#styles-font-dropdown-readme' );
		
		$('select.styles-font-dropdown').change( function(){
			$(this).data('stylesFontDropdown').preview_font_change( $headings );
		});

	})(jQuery);

	/**
	 * Generate Font Previews
	 */
	(function($){
		$('#generate-previews').click( function(){
			var $first = $('optgroup.google-fonts option:first');
			generate_preview( $first );
			return false;
		} );

		// Testing
		// setTimeout( function(){ $('#generate-previews').click(); }, 500 );

		function generate_preview( $option ){
			var name = $option.text();

			$('#generate-previews').after( '<br/>Generating '+ name );

			$.get( document.URL, { "styles-font-preview": name }, function( data, textStatus, jqXHR ){

				var img = $('<img>').attr( 'src', data );

				$('#generate-previews').after( img ).after( '<br/>' );

				$next = $option.next( 'option' );
				if ( $next.length > 0 ) {
					generate_preview( $next );
				}else {
					$('#generate-previews').after( '<br/>Done' );
				}
			} );
		}
	})(jQuery);

	/**
	 * Modify readme.md content:
	 *  - Hide directions on how to get to this page
	 *  - Hide menu screenshot (live demo displayed above)
	 */
	(function($){

		// Remove image of example output
		$('h3.example-output').nextAll('h2').first().remove();
		$('img[src*="example-output.gif"]').remove();

		// Remove directions on how to get to this demo
		var $demo = $('h2:contains(Live Demo)');
		$demo.nextUntil('h2').remove();
		$demo.remove();

	})(jQuery);

</script>