<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'woocommerce_before_add_to_cart_quantity', 'wwccs_dynamic_atts_variation' );
  
function wwccs_dynamic_atts_variation() {
   global $product;
   if ( ! $product->is_type( 'variable' ) ) return;
    wc_enqueue_js( " 
      $('input.variation_id').change(function(){
         if( $(this).val() && $(this).val() > 0  ) {   
            $('form.variations_form').find('.variations select').each( function( index, el ){
               var current_select_id = $(el).attr('id');
               var current_select_val = $(el).find('option:selected').text();
               $('.woocommerce-product-attributes-item--attribute_'+current_select_id+' td p').text(current_select_val);
            });
         } 
      });
   " );
}
