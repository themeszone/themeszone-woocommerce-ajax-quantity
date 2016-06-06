<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    TZ_WC_Ajax_Qty
 * @subpackage TZ_WC_Ajax_Qty/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    TZ_WC_Ajax_Qty
 * @subpackage TZ_WC_Ajax_Qty/includes
 * @author     Andy Markus <andy@themes.zone>
 */
class TZ_WC_Ajax_Qty_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'tz-wc-ajax-qty',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
