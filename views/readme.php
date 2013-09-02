<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php _e('Font Dropdown Menu', 'styles-font-dropdown'); ?></h2>

	<h3>Example output</h3>
	<p>
		<?php do_action( 'styles_fonts_dropdown' ); ?>
	</p>
	<p>
		This submenu can be displayed in your templates with this code:
		<code>&lt;?php do_action( 'styles_fonts_dropdown' ); ?&gt;</code>
	</p>

	<h2>Including in plugins and themes</h2>
	<p>Styles Font Dropdown has been packaged as a plugin only for testing purposes. In real world use, you should put it in your own theme or plugin, then include it with <code>include 'styles-font-dropdown/styles-font-dropdown.php';</code>

	<h2>Select Menu Values</h2>
	<p>
		<strong>Standard Fonts</strong><br/>
		For standard fonts, the option value is set to a font stack, such as <code>Arial, Helvetica, sans-serif</code>, suitable for output as the value of a CSS <code>font-family</code> declaration.
	</p>

	<p>
		<strong>Google Fonts</strong><br/>
		For Google fonts, the option value is set to the URL fragment that can be passed as an <code>@import</code> request. For example, <code>Droid+Sans:regular,700</code>

	<p>This value should be inserted into an <code>@import url(//fonts.googleapis.com/css?family=$value);</code> declaration at the top of your CSS.</p>

	<p>For example, final output for Droid Sans would be <code>@import url(//fonts.googleapis.com/css?family=Droid+Sans:regular,700);</code>

	<h2>Google Fonts API</h2>

	<p>
		<strong>Automatic Updates</strong><br>
		If you <a href="https://code.google.com/apis/console" target="_blank">get a Google Fonts API key</a>, you can enable auto-updates of the Google font list. By default, it caches and updates every 15 days.</p>

	<p>The API key can be set in your local environment, or for all your users. If you enable it only for development, the values will update <code><?php echo str_replace( ABSPATH, '', $this->google_fonts->api_fallback_file ) ?></code> for users who do not have API access.</p>

	<p>
		<strong>Setting your API key</strong><br/>
		Once you <a href="https://code.google.com/apis/console" target="_blank">get a Google Fonts API key</a>, you can set it with <code>add_filter( 'styles_google_font_api', create_function('', "return 'YOUR_KEY_HERE';" ) );</code></p>

	<h2>Changing the cache interval</h2>
	<p>The default is 15 days. You can change the cache interval by setting this filter with a timeout (in seconds): <code>add_filter( 'styles_google_fonts_cache_interval', create_function('', 'return 60*60*24*15;' ) );</code>

	<h2>Changing the font order</h2>
	<p>Google Fonts are ordered by popularity by default. This seemed reasonable, since there are hundreds of fonts, and anything that doesn't appear at top would likely be found with the search field. If you would like change the default ordering, you can use the filter <code>add_filter( 'styles_google_font_sort', create_function( '', 'return "alpha";'));</code></p>

	<p>
		The possible sorting values are:
		<ul style="list-style-type:disc;margin-left:20px;">
			<li><strong>alpha</strong>: Sort the list alphabetically</li>
			<li><strong>date</strong>: Sort the list by date added (most recent font added or updated first)</li>
			<li><strong>popularity</strong>: Sort the list by popularity (most popular family first)</li>
			<li><strong>style</strong>: Sort the list by number of styles available (family with most styles first)</li>
			<li><strong>trending</strong>: Sort the list by families seeing growth in usage (family seeing the most growth first)</li>
		</ul>
	</p>
</div>