<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    TZ_WC_Ajax_Qty
 * @subpackage TZ_WC_Ajax_Qty/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    TZ_WC_Ajax_Qty
 * @subpackage TZ_WC_Ajax_Qty/includes
 * @author     Andy Markus <andy@themes.zone>
 */
class TZ_WC_Ajax_Qty {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      TZ_WC_Ajax_Qty_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'tz-wc-ajax-qty';
		$this->version = '1.2.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_public_hooks();
        $this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - TZ_WC_Ajax_Qty_Loader. Orchestrates the hooks of the plugin.
	 * - TZ_WC_Ajax_Qty_i18n. Defines internationalization functionality.
	 * - TZ_WC_Ajax_Qty_Admin. Defines all hooks for the admin area.
	 * - TZ_WC_Ajax_Qty_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tz-wc-ajax-qty-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tz-wc-ajax-qty-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tz-wc-ajax-qty-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-tz-wc-ajax-qty-public.php';

		$this->loader = new TZ_WC_Ajax_Qty_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the TZ_WC_Ajax_Qty_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new TZ_WC_Ajax_Qty_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}


    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Tz_Wc_Ajax_Qty_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_filter( 'woocommerce_settings_tabs_array', $plugin_admin, 'add_wc_settings_tab',50 );
        $this->loader->add_action( 'woocommerce_settings_tabs_settings_tab_demo', $plugin_admin, 'settings_tab' );
        $this->loader->add_action( 'woocommerce_update_options_settings_tab_demo', $plugin_admin, 'update_settings' );
        $this->loader->add_action( 'woocommerce_product_options_general_product_data', $plugin_admin, 'product_options_field' );
        $this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'process_product_meta' );
    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new TZ_WC_Ajax_Qty_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'plugins_loaded', $plugin_public, 'replace_buttons' );
		$this->loader->add_filter( 'woocommerce_loop_add_to_cart_link', $plugin_public, 'button_filter', 99, 3 );

		
		if ( is_admin() ) {
			$this->loader->add_action( 'wp_ajax_tz_update_cart_qty', $plugin_public ,'cart_quantity_update' );
			$this->loader->add_action( 'wp_ajax_nopriv_tz_update_cart_qty', $plugin_public ,'cart_quantity_update' );
			$this->loader->add_action( 'wp_ajax_tz_get_qty_form', $plugin_public ,'get_qty_form' );
			$this->loader->add_action( 'wp_ajax_nopriv_tz_get_qty_form', $plugin_public ,'get_qty_form' );
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    TZ_WC_Ajax_Qty_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
