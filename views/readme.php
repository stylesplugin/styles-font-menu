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
	.stfm .chosen-results {
		max-height: 400px !important;
	}

	.stfm .sf {
		font-size: 48px;
		height:64px;
		line-height: 64px !important;
		white-space: nowrap;
	}

	.stfm .gf {
		 height:90px; text-indent: -9999px; overflow:hidden;
		 background-repeat: no-repeat;
	}

	.stfm .arial { font-family: Arial, Helvetica, sans-serif; }
	.stfm .bookman { font-family: Bookman, Palatino, Georgia, serif; }
	.stfm .centurygothic { font-family: "Century Gothic", Helvetica, Arial, sans-serif; }
	.stfm .comicsansms { font-family: "Comic Sans MS", Arial, sans-serif; }
	.stfm .courier { font-family: Courier, monospace; }
	.stfm .garamond { font-family: Garamond, Palatino, Georgia, serif; }
	.stfm .georgia { font-family: Georgia, Times, serif; }
	.stfm .helvetica { font-family: Helvetica, Arial, sans-serif; }
	.stfm .lucidagrande { font-family: "Lucida Sans Unicode",Tahoma,Verdana,sans-serif; }
	.stfm .palatino { font-family: Palatino, Georgia, serif; }
	.stfm .tahoma { font-family: Tahoma, Verdana, Helvetica, sans-serif; }
	.stfm .times { font-family: Times, Georgia, serif; }
	.stfm .trebuchetms { font-family: "Trebuchet MS", Tahoma, Helvetica, sans-serif; }
	.stfm .verdana { font-family: Verdana, Tahoma, sans-serif; }
</style>

<script>

	/**
	 * Change heading font-family on menu change event
	 */
	(function($){

		var $headings = $( 'h2,h3', '#styles-font-dropdown-readme' );
		
		$('select.stfm').change( function(){
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