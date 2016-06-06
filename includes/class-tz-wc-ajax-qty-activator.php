<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    TZ_WC_Ajax_Qty
 * @subpackage TZ_WC_Ajax_Qty/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    TZ_WC_Ajax_Qty
 * @subpackage TZ_WC_Ajax_Qty/includes
 * @author     Andy Markus <andy@themes.zone>
 */
class TZ_WC_Ajax_Qty_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if ( !class_exists( 'WooCommerce' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( __( 'Themes Zone WC Ajax Quantity Plugin requires WooCommerce Plugin to work. Please install WooCommerce plugin before using it', 'tz-wc-ajax-qty' ) );
		}
	}

}
