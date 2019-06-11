<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    TZ_WC_Ajax_Qty
 * @subpackage TZ_WC_Ajax_Qty/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    TZ_WC_Ajax_Qty
 * @subpackage TZ_WC_Ajax_Qty/public
 * @author     Andy Markus <andy@themes.zone>
 */
class TZ_WC_Ajax_Qty_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in TZ_WC_Ajax_Qty_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The TZ_WC_Ajax_Qty_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tz-wc-ajax-qty-public.css', array(), $this->version, 'all' );


	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in TZ_WC_Ajax_Qty_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The TZ_WC_Ajax_Qty_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tz-wc-ajax-qty-public.js', array( 'jquery' ), $this->version, false );
		$data_array = array(
			'ajax_call_path' => admin_url( 'admin-ajax.php' )
		);

		wp_localize_script( $this->plugin_name, 'tz_call', $data_array );

	}

	public function get_template($template_name){
		$path = dirname(plugin_dir_path(__DIR__)).'/'.dirname( plugin_basename( __DIR__ ) )  . '/templates/'.$template_name;
		if ( file_exists( $path ) )
			include ( $path );
	}

	public function get_qty_form(){

		if ( isset($_POST['tz_qty_get_form_data']) )
			extract($_POST['tz_qty_get_form_data']);
		else die();

		ob_start();

		if ( isset($product_id) ) {
			global $product, $cart_item_info;
			$product = wc_get_product($product_id);

			if ($product && !$product->is_sold_individually() && $product->is_purchasable() && $this->product_is_in_cart($product_id)) {
				$cart_item_info = $this->product_is_in_cart($product_id);
				$data = array(
					'qty' => $cart_item_info['qty'],
					'cart_id' => $cart_item_info['key'],
					'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
				);

				wp_send_json($data);


			} else {

				// If there was an error adding to the cart, redirect to the product page to show any errors
				$data = array(
					'error' => true,
					'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
				);

				wp_send_json($data);

			}

		}

		die();


	}

	public function cart_quantity_update(){

		if ( isset($_POST['tz_cart_update_args']) ) extract($_POST['tz_cart_update_args']); else die();

		if (isset($product_id)) {

			ob_start();

			$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($product_id));
			$quantity = empty($qty) ? 0 : wc_stock_amount($qty);
			$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
			$product_status = get_post_status($product_id);
			$cart_item_key = WC()->cart->find_product_in_cart($cart_id);

			if ($passed_validation && WC()->cart->set_quantity($cart_item_key, $quantity) && 'publish' === $product_status) {
				do_action('woocommerce_ajax_added_to_cart', $product_id);
				WC_AJAX::get_refreshed_fragments();
			} else {
				$data = array(
					'error' => true,
					'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
				);

				wp_send_json($data);

			}

		}

		return;

	}

	public function product_is_in_cart($product_id){

		foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];

			if( $product_id == $_product->get_id() ) {
				return array('key'=>$cart_item_key, 'qty' => $values["quantity"]);
			}
		}

		return null;

	}

	public function custom_template_loop_add_to_cart( $args = array() ) {
		global $product;
		if ( $product &&
            ( ( get_option( 'tz_wc_ajax_qty_global' ) === 'yes' ) ||
              ( get_post_meta( $product->get_id(), '_tz_qty_box_enabled', true ) === 'yes' )  ||
              ( apply_filters( 'tz_wc_qty_ajax_filter', $product ) === true ) )
        ) {
			$defaults = array(
				'quantity' => 1,
				'class'    => implode( ' ', array_filter( array(
						'button',
                        'product_type_' . $product->get_type(),
                        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                        $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
				) ) ),
                'attributes' => array(
                    'data-product_id'  => $product->get_id(),
                    'data-product_sku' => $product->get_sku(),
                    'aria-label'       => $product->add_to_cart_description(),
                    'rel'              => 'nofollow',
                ),
			);

            $args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

            if ( isset( $args['attributes']['aria-label'] ) ) {
                $args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
            }

			global $cart_item_info, $form_visible;

			$form_visible = false;

            $prod_id = $product->get_id();

			$cart_item_info = $this->product_is_in_cart($prod_id);

			if ( $this->product_is_in_cart($product->get_id()) ) $args['class'] .= ' hidden ';

			wc_get_template('loop/add-to-cart.php', $args);

			if ( $product->is_purchasable() && !$product->is_sold_individually() ) {
				if ( $this->product_is_in_cart($product->get_id()) )
					$form_visible = true;
			}
			$this->get_template('qty-form.php');

		} elseif ( $product ) {
            woocommerce_template_loop_add_to_cart();
        }
	}

	public function replace_buttons() {

		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
        add_action( 'woocommerce_after_shop_loop_item', [$this, 'custom_template_loop_add_to_cart'], 10);

	}

	public function button_filter( $link, $product, $args ){

        if ( ( get_option( 'tz_wc_ajax_qty_global' ) === 'yes' ) ||
            ( get_post_meta( $product->get_id(), '_tz_qty_box_enabled', true ) === 'yes' )  ||
            ( apply_filters( 'tz_wc_qty_ajax_filter', $product ) === true ) ) {
            $link = str_replace( 'data-product_id="'.$product->get_id().'"', 'data-product_id="'.$product->get_id().'" data-tz_qty_ajax="true"', $link );
        }
        return $link;
    }

}
