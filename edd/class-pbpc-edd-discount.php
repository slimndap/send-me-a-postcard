<?php
class PBPC_EDD_Discount {
	
	static function init() {
		add_action( 'init', array( __CLASS__, 'default_gateway_discount' ) );		

		add_action( 'wp_ajax_pbpc_edd_calculate_gateway_discount', array( __CLASS__, 'recalculate_gateway_discount' ) );
		add_action( 'wp_ajax_nopriv_pbpc_edd_calculate_gateway_discount', array( __CLASS__, 'recalculate_gateway_discount' ) );
		
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_script' ) );

	}
	
	static function default_gateway_discount() {

		EDD()->fees->remove_fee( 'pbpc_edd_discount' );
		
		if ( edd_get_cart_total() == 0 ) {
			return;
		}
		
		$gateway = edd_get_default_gateway();
		
		if ( $gateway != PBPC_EDD_Gateway::GATEWAY_ID ) {
			return;
		}

		self::set_gateway_discount();
		
	}
	
	static function enqueue_script() {
		if ( edd_is_checkout() ) {
			wp_enqueue_script( 'pbpc-edd-discount', PBPC_PLUGIN_URL . '/assets/js/edd-discount.js', array( 'jquery' ), PBPC_VERSION );
		}		
	}
	
	static function recalculate_gateway_discount() {
		
		if ( ! empty ( $_REQUEST['action'] ) && $_REQUEST['action'] === 'pbpc_edd_calculate_gateway_discount' ) {
			
			if ( PBPC_EDD_Gateway::GATEWAY_ID == $_REQUEST['gateway'] ) {
				self::set_gateway_discount();			
			} else {
				EDD()->fees->remove_fee( 'pbpc_edd_discount' );
			}
			
			ob_start();
			edd_checkout_cart();
			$cart = ob_get_contents();
			ob_end_clean();

			$response = array(
				'html'  => $cart,
				'total' => html_entity_decode( edd_cart_total( false ), ENT_COMPAT, 'UTF-8' ),
			);

			echo json_encode( $response );

		}

		edd_die();
	}
	
	static function set_gateway_discount() {
		$discount = edd_get_cart_total() * -1;
		$label = __('Send me a postcard discount', 'pbpc' );
		
		EDD()->fees->add_fee( $discount, $label, 'pbpc_edd_discount' );
		
	}
	
}

PBPC_EDD_Discount::init();