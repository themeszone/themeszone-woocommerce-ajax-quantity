(function( $ ) {
	'use strict';

	$( window ).load(function() {

		if ( typeof tz_call === 'undefined' ) {
			return false;
		}

		$('.tz-qty-cont').live('click', function(){

			var qty_input = $(this).parent().find('input[name="quantity"]');
			if ( $(qty_input).prop('disabled') ) return;
			var qty_step = parseFloat($(qty_input).attr('step'));
			var qty_min = parseFloat($(qty_input).attr('min'));
			var qty_max = parseFloat($(qty_input).attr('max'));


			if ( $(this).hasClass('minus') ){
				var vl = parseFloat($(qty_input).val());
				vl = ( (vl - qty_step) < qty_min ) ? qty_min : (vl - qty_step);
				$(qty_input).val(vl);
			} else if ( $(this).hasClass('plus') ) {
				var vl = parseFloat($(qty_input).val());
				vl = ( (vl + qty_step) > qty_max ) ? qty_max : (vl + qty_step);
				$(qty_input).val(vl);
			}
			$(qty_input).trigger('change');

		});

		var tz_qty_query = {};
		var tz_qty_o_sender;

		$( document.body ).on('after_adding_to_cart', function(env, sender, data){

			tz_qty_o_sender = sender;
			tz_qty_query = {
				'action': 'tz_get_qty_form',
				'tz_qty_get_form_data': data      // We pass php values differently!
			};

			var qty_form = $(sender).parents('li.product').find('.tz-qty-container');
			var pos = $(sender).parents('li.product').find('a.button[data-tz_qty_ajax="true"]');
			var vv = $(sender).parents('li.product').find('a.added_to_cart');
			if ( pos.length && qty_form.length ) {
				pos.hide();
				vv.remove();
				qty_form.find('input.qty').prop( "disabled", true );
				qty_form.removeClass('hidden');
				qty_form.show();
            }

		});

		$ ( document.body ).on('added_to_cart', function(env, fragments, cart_hash, $thisbutton){

			if ($thisbutton == tz_qty_o_sender){
				$.post(tz_call.ajax_call_path, tz_qty_query, function(response, status) {
					if ( status == 'success' ) {

						var qty_form = $thisbutton.parents('li.product').find('.tz-qty-container');
						var h_field = $thisbutton.parents('li.product').find('input[data-product_id]');
						$(h_field).data('cart_id', response.cart_id);
						var inp = qty_form.find('input.qty');
						$(inp).val(response.qty);
						$(inp).prop( "disabled", false );

					} else {

					}
				});
			}

		});

		$( document.body ).on('adding_to_cart', function(env, obj, data){
			$( document.body ).trigger('after_adding_to_cart', [ obj, data ] );
		});

		$( '.tz-qty-container .quantity input.qty' ).live( 'change', function() {

			if ( $(this).prop('disabled') ) return;

			var inputattrs = $(this).parents('li.product').find('input[data-product_id]');

			var args = {};

			$.each( inputattrs.data(), function( key, value ) {
				args[key] = value;
			});

			args['qty'] = parseFloat($(this).val());
			var cur_qty = args['qty'];

			var buy_but = $(this).parents('li.product').find('a.add_to_cart_button');
			var qty_form = $(this).parents('li.product').find('div.tz-qty-container');



			var data = {
				'action': 'tz_update_cart_qty',
				'tz_cart_update_args': args      // We pass php values differently!
			};



			// We can also pass the url value separately from ajaxurl for front end AJAX implementations
			$.post(wc_add_to_cart_params.ajax_url, data, function(response) {

				var fragments = response.fragments;
				var cart_hash = response.cart_hash;

				// Block fragments class
				if ( fragments ) {
					$.each( fragments, function( key ) {
						$( key ).addClass( 'updating' );
					});
				}

				var this_page = window.location.toString();

				// Block widgets and fragments
				$( '.shop_table.cart, .updating, .cart_totals' ).fadeTo( '400', '0.6' ).block({
					message: null,
					overlayCSS: {
						opacity: 0.6
					}
				});

				// Replace fragments
				if ( fragments ) {
					$.each( fragments, function( key, value ) {
						$( key ).replaceWith( value );
					});
				}

				if ( cur_qty <= 0 ) {
					$(qty_form).hide();
					$(buy_but).removeClass('added');
					$(buy_but).removeClass('hidden');
					$(buy_but).show();
				}

				// Unblock
				$( '.widget_shopping_cart, .updating' ).stop( true ).css( 'opacity', '1' ).unblock();

				// Cart page elements
				$( '.shop_table.cart' ).load( this_page + ' .shop_table.cart:eq(0) > *', function() {

					$( '.shop_table.cart' ).stop( true ).css( 'opacity', '1' ).unblock();

					$( document.body ).trigger( 'cart_page_refreshed' );
				});

				$( '.cart_totals' ).load( this_page + ' .cart_totals:eq(0) > *', function() {
					$( '.cart_totals' ).stop( true ).css( 'opacity', '1' ).unblock();
				});

				// Trigger event so themes can refresh other areas
				$( document.body ).trigger( 'cart_qty_update', [ fragments, cart_hash ] );

			});


		});

		$( document.body ).on('removed_from_cart', function( e, fragments, data, args) {
           	var removed_product = args.context.dataset.product_id;
           	var product_removed_button = $('[data-product_id="'+removed_product+'"]')
		   	if ( product_removed_button.length && product_removed_button[0].hasAttribute('data-tz_qty_ajax') ) {
           		var product_container = product_removed_button.parents('li.product');
           		if ( product_container.length ) {
           			var inp = product_container.find('.tz-qty-container .quantity input.qty');
                    inp.val(0);
                    $(inp).trigger('change');
				}
			}
		});

	});

})( jQuery );
