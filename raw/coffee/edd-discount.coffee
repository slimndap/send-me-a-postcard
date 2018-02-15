jQuery ->

	$edd_gateway_picker = jQuery 'select#edd-gateway, input.edd-gateway'
	$edd_gateway_picker.change ->
		
		postData = 
			action: 'pbpc_edd_calculate_gateway_discount'
			'payment-mode': jQuery(@).val()
            
		jQuery.ajax
			type: "POST",
			data: postData,
			dataType: "json",
			url: edd_global_vars.ajaxurl,
			success: (discount_response) -> 
				jQuery('#edd_checkout_cart').replaceWith discount_response.html 
		
