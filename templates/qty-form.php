<?php global $cart_item_info, $product, $form_visible, $link; ?>
<div class="tz-qty-container <?php echo ( $form_visible ) ? '' : 'hidden'; ?>">
	<span class="tz-qty-cont minus">-</span>
	<?php
	$cur_qty = isset($_POST['quantity']) ? wc_stock_amount( $_POST['quantity'] ): wc_stock_amount ( $cart_item_info['qty']) ;
	woocommerce_quantity_input( array(
		'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 0, $product ),
		'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
		'input_value' => ( isset( $cur_qty ) ? $cur_qty : 1 ),

	) );
	echo sprintf( '<input type="hidden" data-product_id="%s" data-product_sku="%s"  data-cart_id="%s" class="%s hidden button product_type_simple">', esc_attr( $product->get_id() ), esc_attr( $product->get_sku() ),  esc_attr($cart_item_info['key']) ,esc_attr( $link['class'] ) );
	?>
	<span class="tz-qty-cont plus">+</span>
</div>