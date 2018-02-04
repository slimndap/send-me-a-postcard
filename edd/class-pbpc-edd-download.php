<?php 
class PBPC_EDD_Download {
	
	static function is_gateway_allowed_for_basket() {
		
		if ( is_admin() ) {
			return true;
		}
		
		$cart_contents = edd_get_cart_contents();
		
		if ( empty( $cart_contents ) ) {
			false;
		}
		
		foreach( $cart_contents as $item ) {
			if ( !self::is_gateway_allowed_for_product( $item['id'] ) ) {
				return false;
			}
		}
		
		return true;
		
	}
	
	static function is_gateway_allowed_for_product( $download_id ) {
		return 1 == get_post_meta( $download_id, 'pbpc_edd_gateway_enabled', true );
	}
	
}