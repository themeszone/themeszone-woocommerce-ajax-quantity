<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://themes.zone
 * @since             1.2.0
 * @package           TZ_WC_Ajax_Qty
 *
 * @wordpress-plugin
 * Plugin Name:       Themes Zone WC Ajax Quantity
 * Plugin URI:        https://themes.zone/themes-zone-woocommerce-ajax-quantity-plugin/
 * Description:       Plugin adds a quantity filed on product listing page when customer clicks on add to cart button, thus allowing customers to change product quantity without having to go to the shopping cart to change the number of products.
 * Version:           1.2.0
 * Author:            Themes Zone
 * Author URI:        https://themes.zone/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tz-wc-ajax-qty
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tz-wc-ajax-qty-activator.php
 */
function activate_tz_wc_ajax_qty() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tz-wc-ajax-qty-activator.php';
	TZ_WC_Ajax_Qty_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tz-wc-ajax-qty-deactivator.php
 */
function deactivate_tz_wc_ajax_qty() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tz-wc-ajax-qty-deactivator.php';
	TZ_WC_Ajax_Qty_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tz_wc_ajax_qty' );
register_deactivation_hook( __FILE__, 'deactivate_tz_wc_ajax_qty' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tz-wc-ajax-qty.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tz_wc_ajax_qty() {

	$plugin = new TZ_WC_Ajax_Qty();
	$plugin->run();

}
run_tz_wc_ajax_qty();
