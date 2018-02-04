<?php
class PBPC_EDD_Admin {
	
	static function init() {
		
		if ( ! edd_is_gateway_active( PBPC_EDD_Gateway::GATEWAY_ID ) ) {
			return;
		}

		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post', array( __CLASS__, 'meta_box_save' ), 10, 2 );
	}
	
	static function add_meta_box() {
		
		add_meta_box( 
			'pbpc_edd', 
			__( 'Send me a postcard', 'pbpc' ),
			array( __CLASS__, 'render_meta_box' ), 
			'download', 
			'normal', 
			'high' 
		);

	}
	
	static function meta_box_save( $post_id, $post ) {
		
		if ( 
			! isset( $_POST['pbpc_edd_gateway_enabled_nonce'] ) || 
			! wp_verify_nonce( $_POST['pbpc_edd_gateway_enabled_nonce'] ) 
		) {
			return;
		}
		
		if ( 
			( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || 
			( defined( 'DOING_AJAX') && DOING_AJAX ) || 
			isset( $_REQUEST['bulk_edit'] ) 
		) {
			return;
		}
		
		if ( isset( $post->post_type ) && 'revision' == $post->post_type ) {
			return;
		}
		
		if ( ! current_user_can( 'edit_product', $post_id ) ) {
			return;
		}
		
		update_post_meta( $post_id, 'pbpc_edd_gateway_enabled', !empty( $_POST['pbpc_edd_gateway_enabled'] ) );
	}
	
	static function render_meta_box() {
		
		wp_nonce_field( -1, 'pbpc_edd_gateway_enabled_nonce', true, true );
		
		?><p>
			<label for="pbpc_edd_gateway_enabled">
				<input type="checkbox" name="pbpc_edd_gateway_enabled" id="pbpc_edd_gateway_enabled" value="1" <?php
					checked( 1, get_post_meta( get_the_id(), 'pbpc_edd_gateway_enabled', true ), true ); ?>>
				<?php  printf( __( 'Customers can pay for this %s by sending me a postcard.', 'pbpc'), edd_get_label_singular() ); ?>
			</label>
		</p><?php
		
	}
}

PBPC_EDD_Admin::init();