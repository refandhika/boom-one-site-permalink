<?php
/**
* Plugin Name: 	Boombastis - One Site Permalink
* Plugin URI: 	https://www.boombastis.com
* Description: 	Unify site name in all pages when using multiple site.
* Version: 		1.0.0
* Author: 		Refa Andhika
* Author URI: 	https://www.boombastis.com
* License: 		Private
* License URI: 	https://www.boombastis.com
*
*/

/**
* Main function
*/

foreach( [ 'post', 'page', 'post_type', 'term', 'author' ] as $type ){
    add_filter( $type . '_link', function ( $url, $post_id, $sample ) use ( $type ){
        return apply_filters( 'wpse_link', $url, $post_id, $sample, $type );
    }, 9999, 3 );
}
add_filter( 'wpse_link', function(  $url, $post_id, $sample, $type ){
	$options = get_option('bosp_options');

	if(isset($options['bosp_main_site'])):
		$patt = '/(\w+)\/(w+)/i';
		$repl = $options['bosp_main_site'].'/${2}';
		return preg_replace($patt, $repl, $url);
	else:
		return $url;
	endif;

}, 10, 4 );

/**
* Init settings menu
*/
function bosp_setting_init() {
	register_setting('general_bosp', 'bosp_options');

	add_settings_section('bosp_first_section', '', 'bosp_main_callback', 'general_bosp');

	add_settings_field('bosp_mainsite', 'Main Site', 'bosp_mainsite_callback', 'general_bosp', 'bosp_first_section');
}
add_action( 'admin_init', 'bosp_setting_init' );

function create_bosp_settings_page() {
	$page_title = 'Boombastis - One Site Permalink Setting';
	$menu_title = 'Boom - One Site Permalink';
	$capability = 'manage_options';
	$slug = 'bosp';
	$callback = 'bosp_setting_page_content';

	add_submenu_page('options-general.php', $page_title, $menu_title, $capability, $slug, $callback);
}
add_action( 'admin_menu', 'create_bosp_settings_page' );

function bosp_setting_page_content() { 

	if ( !current_user_can('manage_options') ) :
		return;
	endif;

	if ( isset( $_GET['setting-updated'] ) ) :
		add_settings_error( 'bosp_messages', 'bosp_messages', __( 'Setting Saved', 'general_bosp'), 'updated' );
	endif;

	settings_errors( 'bosp_messages' );
	?>
	<div class="wrap">
		<h2>Boombastis - One Site Permalink</h2>
		<form action="options.php" method="post">
			<?php 
				settings_fields('general_bosp');
				do_settings_sections('general_bosp');
				submit_button('Save Settings');
			?>
		</form>
	</div>
<?php
}

/**
* Callback for desktop section
*/

function bosp_main_callback($args) {
	switch ($args['id']) {
		case 'bosp_first_section':
			echo 'Main site used for primary site.';
			break;
	}
}

function bosp_mainsite_callback($args) {
	$options = get_option('bosp_options');
	?>
	<input name="bosp_options[bosp_main_site]" id="bosp_main_site" text="text" size="50" value="<?php echo isset( $options['bosp_main_site'] ) ? esc_attr($options['bosp_main_site']) : '';?>"/>
	<?php 
}

/*EOF*/