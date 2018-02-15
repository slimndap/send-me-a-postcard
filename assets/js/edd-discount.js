'use strict';

(function () {
  jQuery(function () {
    var $edd_gateway_picker;
    $edd_gateway_picker = jQuery('select#edd-gateway, input.edd-gateway');
    return $edd_gateway_picker.change(function () {
      var postData;
      postData = {
        action: 'pbpc_edd_calculate_gateway_discount',
        'payment-mode': jQuery(this).val()
      };
      return jQuery.ajax({
        type: "POST",
        data: postData,
        dataType: "json",
        url: edd_global_vars.ajaxurl,
        success: function success(discount_response) {
          return jQuery('#edd_checkout_cart').replaceWith(discount_response.html);
        }
      });
    });
  });
}).call(undefined);
