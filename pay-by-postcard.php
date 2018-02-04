<?php
/**
 * Plugin Name:     Pay By Postcard
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Let customers pay you by sending a postcard.
 * Author:          Jeroen Schmit
 * Author URI:      https://slimndap.com
 * Text Domain:     pay-by-postcard
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Pay_By_Postcard
 */

class PBPC {

	/** Refers to a single instance of this class. */
	private static $instance = null;
	
	/**
     * Creates or returns an instance of this class.
     *
     * @return  Foo A single instance of this class.
     */
   	public static function get_instance() {
 
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
 
        return self::$instance;
 
    }
    
    /**
     * Initializes the plugin by setting localization, filters, and administration functions.
     */
     private function __construct() {
		self::define_constants();
		self::load_dependencies();
    }
    
    private function define_constants() {
	    
		define( 'PBPC_VERSION', '0.1.0' );
		define( 'PBPC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'PBPC_PLUGIN_URL', plugins_url( '', __FILE__ ) );
		define( 'PBPC_PLUGIN_FILE', __FILE__ );
	    
    }
    
    private function load_dependencies() {

		require_once dirname( PBPC_PLUGIN_FILE ) . '/edd/class-pbpc-edd.php';
	    
    }
}

PBPC::get_instance();