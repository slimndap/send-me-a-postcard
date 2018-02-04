<?php
class PBPC_EDD {
	
	static function init() {

		self::load_dependencies();
		
	}
	
    private function load_dependencies() {

		require_once dirname( PBPC_PLUGIN_FILE ) . '/edd/class-pbpc-edd-download.php';
	    
		require_once dirname( PBPC_PLUGIN_FILE ) . '/edd/class-pbpc-edd-gateway.php';
	    
		// require_once dirname( PBPC_PLUGIN_FILE ) . '/edd/class-pbpc-edd-discount.php';
	    
		require_once dirname( PBPC_PLUGIN_FILE ) . '/edd/class-pbpc-edd-admin.php';
	    
    }
}

PBPC_EDD::init();