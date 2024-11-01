<?php
/**
 * Plugin Name: WP Framework
 * Plugin URI:  http://wpframework.com
 * Description: WP Framework helps designers and developers create custom WordPress themes the WordPress way.
 * Author:      Ptah Dunbar
 * Author URI:  http://ptahdunbar.com
 * Version:     0.1.0-alpha
 * Text Domain: wp-framework
 * Domain Path: /languages/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'WP_Framework' ) ) :
/**
 * Main WP Framework Class
 *
 * Tap tap tap... Is this thing on?
 *
 * @since WP_Framework (0.1)
 */
class WP_Framework {
	/**
	 * @var string WP Framework version of current WP Framework files
	 */
	public $version = '0.1.0-alpha';

	/**
	 * @var string Database version of current WP Framework files
	 */
	public $db_version = 0;

	function __construct() {
		$this->constants();
		$this->setup_globals();
		$this->setup_filters();
	}
	
	function constants() {
		// Define the WP Framework version
		if ( !defined( 'WPF_VERSION' ) )
			define( 'WPF_VERSION', $this->version );
		
		// Define the database version
		if ( !defined( 'WPF_DB_VERSION' ) )
			define( 'WPF_DB_VERSION', $this->db_version );
		
		// Place your custom code (actions/filters) in a file called
		// '/plugins/wpf-custom.php' and it will be loaded before anything else.
		if ( file_exists( WP_PLUGIN_DIR . '/wpf-custom.php' ) )
			require( WP_PLUGIN_DIR . '/wpf-custom.php' );

		// Path and URL
		if ( !defined( 'WPF_PLUGIN_DIR' ) )
			define( 'WPF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		if ( !defined( 'WPF_PLUGIN_URL' ) )
			define( 'WPF_PLUGIN_URL', plugin_dir_url ( __FILE__ ) );
	}

	function setup_globals() {

		// Stores the template for the current page request
		$this->current_template = new stdClass;
		$this->current_template->path = '';
		$this->current_template->slug = '';
		$this->current_template->body_class = '';
	}

	/**
	 * Setup the default filters.
	 *
	 * @since WP_Framework (0.1)
	 * @access private
	 *
	 * @uses add_filter() To add various filters
	 */
	function setup_filters() {
		add_filter( 'template_include', array( $this, 'set_current_template' ) );
		add_filter( 'bp_load_template', array( $this, 'set_current_template' ) );
	}

	function set_current_template( $template ) {
		$this->current_template->path = str_replace( WP_CONTENT_DIR, basename( WP_CONTENT_DIR ), $template );

		// For later injection in the body_class
		$this->current_template->slug = ( false !== stripos( $template, get_stylesheet() ) ) ? str_replace( get_stylesheet_directory(), get_stylesheet(), $template ) : str_replace( get_template_directory(), get_template(), $template );
		$this->current_template->slug = str_replace( '/', '-', str_replace( '.php', '', $this->current_template->slug ) );

		return $template;
	}
}

// "And now for something completely different"
$GLOBALS['wpf'] = new WP_Framework;
endif;

if ( !function_exists( 'wpf_activate' ) ) :
// Activation Function
function wpf_activate() {
	// Force refresh theme roots.
	delete_site_transient( 'theme_roots' );

	do_action( 'wpf_activate' );
}
register_activation_hook( __FILE__, 'wpf_activate' );
endif;

if ( !function_exists( 'wpf_deactivate' ) ) :
// Deactivation Function
function wpf_deactivate() {
	do_action( 'wpf_deactivate' );
}
register_deactivation_hook( __FILE__, 'wpf_deactivate' );
endif;