<?php
class PBPC_EDD_Taxes {
	
	static function init() {
		add_action( 'wp_ajax_pbpc_edd_calculate_gateway_discount', array( __CLASS__, 'recalculate_taxes' ) );
		add_action( 'wp_ajax_nopriv_pbpc_edd_calculate_gateway_discount', array( __CLASS__, 'recalculate_taxes' ) );
		
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_script' ) );
		
		add_filter( 'edd_use_taxes', array( __CLASS__, 'dont_use_taxes' ) );
		add_filter( 'edd_cart_total', array( __CLASS__, 'change_cart_total' ) );


	}
	
	static function change_cart_total( $cart_total_html ) {
		
		// Leave alone if PBPC gateway is not allowed for this basket.
		if ( !PBPC_EDD_Download::is_gateway_allowed_for_basket() ) {
			return $cart_total_html;
		}
		
		// Disable taxes if no gateway is selected, but PBPC gateway is the default gateway.
		if ( empty( $_REQUEST['payment-mode'] ) && PBPC_EDD_Gateway::GATEWAY_ID == edd_get_default_gateway() ) {
			return '<s>'.$cart_total_html.'</s> '.__('free', 'pbpc');
		}

		// Disable taxes if PBPC is the selected gateway.
		if ( PBPC_EDD_Gateway::GATEWAY_ID == $_REQUEST['payment-mode'] ) {
			return '<s>'.$cart_total_html.'</s> '.__('free', 'pbpc');
		}
		
		return $cart_total_html;
	}
	
	static function dont_use_taxes( $ret ) {

		// Leave alone if use taxes is already false.
		if ( !$ret ) {
			return $ret;
		}

		// Leave alone if PBPC gateway is not allowed for this basket.
		if ( !PBPC_EDD_Download::is_gateway_allowed_for_basket() ) {
			return $ret;
		}
		
		// Disable taxes if no gateway is selected, but PBPC gateway is the default gateway.
		if ( empty( $_REQUEST['payment-mode'] ) && PBPC_EDD_Gateway::GATEWAY_ID == edd_get_default_gateway() ) {
			return false;
		}
		
		// Disable taxes if PBPC is the selected gateway.
		if ( PBPC_EDD_Gateway::GATEWAY_ID == $_REQUEST['payment-mode'] ) {
			return false;
		}
		
		// PBPC is not active. Leave alone.
		return $ret;
		
	}
	
	static function enqueue_script() {
		if ( edd_is_checkout() ) {
			wp_enqueue_script( 'pbpc-edd-discount', PBPC_PLUGIN_URL . '/assets/js/edd-discount.js', array( 'jquery' ), PBPC_VERSION );
		}		
	}
	
	static function recalculate_taxes() {
		
		if ( ! empty ( $_REQUEST['action'] ) && $_REQUEST['action'] === 'pbpc_edd_calculate_gateway_discount' ) {
			
			EDD()->cart->update_cart();
			
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
	
}

PBPC_EDD_Taxes::init();