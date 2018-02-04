<?php
class PBPC_EDD_Gateway {

	const GATEWAY_ID  = 'pbpc_edd';

	static function init() {
		
		add_filter('edd_payment_gateways', array( __CLASS__, 'register_gateway' ) );

		if ( ! edd_is_gateway_active( self::GATEWAY_ID ) ) {
			return;
		}

		add_action( 'edd_purchase_form_after_user_info', array( __CLASS__, 'instructions_html' ) );
		add_action( 'edd_'.self::GATEWAY_ID.'_cc_form', array( __CLASS__, 'cc_form_html' ) );

		add_filter( 'edd_settings_gateways', array( __CLASS__, 'add_settings' ) );		
		add_filter( 'edd_settings_sections_gateways', array( __CLASS__, 'register_gateway_section' ), 1, 1 );
		add_action( 'edd_gateway_'.self::GATEWAY_ID, array( __CLASS__, 'process_payment' ) );
		
	}	
	
	
	static function add_settings( $settings ) {
		
		$gateway_settings = array(
			
			array(
				'id' => 'pbpc_settings',
				'name' => '<strong>' . __('Send me a postcard Settings', 'pbpc') . '</strong>',
				'desc' => __('Configure the gateway settings', 'pbpc'),
				'type' => 'header'
			),
			array(
				'id' => 'pbpc_postal_address',
				'name' => __('Postal address', 'pbpc'),
				'desc' => __('Where should customers send their postcard?', 'pbpc'),
				'type' => 'textarea',
				'size' => 'regular'
			),
		);

		$settings[ self::GATEWAY_ID ] = $gateway_settings;
		
		return $settings;
		
	}
	
	static function cc_form_html() {
		return;
	}
	
	static function instructions_html() {
		
		$payment_mode = edd_get_chosen_gateway();
		
		if ( $payment_mode != self::GATEWAY_ID ) {
			return;
		}

		?><fieldset>
			<legend><?php _e( 'Send me a postcard', 'pbpc' ); ?> </legend>		
			<p><?php _e( 'You can download your purchase <strong>for free</strong> as soon as your postcard arrives.', 'pbpc' ); ?></p>
			<p><?php _e( 'An e-mail with instructions will be sent after the next step.', 'pbpc' ); ?></p>
		</fieldset><?php

	}
	
	static function process_payment( $purchase_data ) {
		echo 'process!';
		$errors = edd_get_errors();
		
		if ( $errors ) {
			
			edd_send_back_to_checkout('?payment-mode=' . $purchase_data['post_data']['edd-gateway']);
			
		} else {
			
			$payment = array( 
				'price' => $purchase_data['price'], 
				'date' => $purchase_data['date'], 
				'user_email' => $purchase_data['user_email'],
				'purchase_key' => $purchase_data['purchase_key'],
				'currency' => $edd_options['currency'],
				'downloads' => $purchase_data['downloads'],
				'cart_details' => $purchase_data['cart_details'],
				'user_info' => $purchase_data['user_info'],
				'gateway' => self::GATEWAY_ID,
				'status' => 'pending'
			);
	 
			// record the pending payment
			$payment_id = edd_insert_payment($payment);

			self::send_instructions( $purchase_data, $payment_id );
	 
			edd_send_to_success_page();
		}		
	}
	
	static function register_gateway( $gateways ) {
		
		if ( !is_admin() && !PBPC_EDD_Download::is_gateway_allowed_for_basket() ) {
			return $gateways;
		}
		
		$gateways[self::GATEWAY_ID] = array(
			'admin_label' => __('Send me a postcard', 'pbpc'), 
			'checkout_label' => __('Send me a postcard', 'pbpc')
		);
		return $gateways;
	}
	
	static function register_gateway_section( $gateway_sections ) {

		$gateway_sections[self::GATEWAY_ID] = __('Send me a postcard', 'pbpc');

		return $gateway_sections;
		
	}
	
	static function send_instructions( $purchase_data, $payment_id ) {
		
		if ( empty( $purchase_data['user_info'] ) ) {
			return;
		}
		
		$customer = new EDD_Customer( $purchase_data['user_info']['id'] );
		
		$to = $customer->name.' <'.$customer->email.'>';
		$subject = __( 'Send me a postcard instructions', 'pbpc' );
		
		ob_start();
		_e( 'Dear {fullname}', 'pbpc' ); ?>,
		
		<?php _e( 'Thank you for your purchase. I can\'t wait to receive your postcard!', 'pbpc'); ?>
		
		
		<?php _e( 'Please address it to:', 'pbpc' ); ?>
		
		<blockquote><?php 
			echo edd_get_option( 'pbpc_postal_address' ); 
		?></blockquote>
		
		
		<?php _e( 'Make sure to mention <strong>Order ID {payment_id}</strong> and to use sufficient postage.', 'pbpc' ); ?>
		
		
		<?php _e( 'You will receive the following downloads as soon as your postcard arrives:', 'pbpc' ); ?>
		
		<ul><?php
			foreach( $purchase_data['downloads'] as $download_data) {
				$download = edd_get_download( $download_data['id'] );
				?><li><strong><?php
					echo $download->post_title;
				?></strong></li><?php
			}
		?></ul>
		
		<p>{sitename}</p>
		
		<?php
			
		$message = ob_get_clean();
		
		$message = edd_do_email_tags( $message, $payment_id );
		
		$message = apply_filters( 'edd_email_template_wpautop', true ) ? wpautop( $message ) : $message;


		EDD()->emails->send( $to, $subject, $message );
			
		
	}
	
}

PBPC_EDD_Gateway::init();
