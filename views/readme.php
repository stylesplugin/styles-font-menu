<div class="wrap" id="styles-font-menu-readme">

	<?php screen_icon(); ?>
	<h2><?php _e('Font Dropdown Menu', 'styles-font-menu'); ?></h2>

	<p><a href="#" id="generate-previews">Generate Font Previews</a></p>

	<h3 class="example-output">Example output</h3>
	<p><?php do_action( 'styles_font_menu' ); ?></p>

	<?php echo Markdown( file_get_contents( dirname( dirname( __FILE__ ) ) . '/readme.md' ) ); ?>


</div>

<style>
	#styles-font-menu-readme > ul {
		list-style-type: disc;
		margin-left: 30px;
	}
	.sfm .chosen-results {
		max-height: 400px !important;
	}

	.sfm .sf {
		font-size: 48px;
		height:64px;
		line-height: 64px !important;
		white-space: nowrap;
	}

	.sfm .gf {
		 height:90px; text-indent: -9999px; overflow:hidden;
		 background-repeat: no-repeat;
	}

	.sfm .arial { font-family: Arial, Helvetica, sans-serif; }
	.sfm .bookman { font-family: Bookman, Palatino, Georgia, serif; }
	.sfm .centurygothic { font-family: "Century Gothic", Helvetica, Arial, sans-serif; }
	.sfm .comicsansms { font-family: "Comic Sans MS", Arial, sans-serif; }
	.sfm .courier { font-family: Courier, monospace; }
	.sfm .garamond { font-family: Garamond, Palatino, Georgia, serif; }
	.sfm .georgia { font-family: Georgia, Times, serif; }
	.sfm .helvetica { font-family: Helvetica, Arial, sans-serif; }
	.sfm .lucidagrande { font-family: "Lucida Sans Unicode",Tahoma,Verdana,sans-serif; }
	.sfm .palatino { font-family: Palatino, Georgia, serif; }
	.sfm .tahoma { font-family: Tahoma, Verdana, Helvetica, sans-serif; }
	.sfm .times { font-family: Times, Georgia, serif; }
	.sfm .trebuchetms { font-family: "Trebuchet MS", Tahoma, Helvetica, sans-serif; }
	.sfm .verdana { font-family: Verdana, Tahoma, sans-serif; }
</style>

<script>

	/**
	 * Change heading font-family on menu change event
	 */
	(function($){

		var $headings = $( 'h2,h3', '#styles-font-menu-readme' );
		
		$('select.sfm').change( function(){
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