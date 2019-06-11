<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://themes.zone/
 * @since      1.0.0
 *
 * @package    Tz_Wc_Ajax_Qty
 * @subpackage Tz_Wc_Ajax_Qty/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tz_Wc_Ajax_Qty
 * @subpackage Tz_Wc_Ajax_Qty/admin
 * @author     Themes Zone <content@themes.zone>
 */
class Tz_Wc_Ajax_Qty_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tz_Wc_Ajax_Qty_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tz_Wc_Ajax_Qty_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tz-wc-ajax-qty-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tz_Wc_Ajax_Qty_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tz_Wc_Ajax_Qty_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tz-wc-ajax-qty-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function add_wc_settings_tab( $settings_tabs ){
        $settings_tabs['settings_tab_demo'] = esc_html__( 'Themes Zone Ajax Quantity', 'tz-wc-ajax-qty' );
        return $settings_tabs;
    }

    public function settings_tab() {
        woocommerce_admin_fields( $this->get_settings() );
    }

    private function get_settings(){
        $settings = array(
            'section_title' => array(
                'name'     => esc_html__( 'Global Product Listing Quantity Ajax Settings', 'tz-wc-ajax-qty' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'tz_ajax_qty_section_title'
            ),

            array(
                'title'    => esc_html__( 'Enable Quantity Field Globally', 'tz-wc-ajax-qty' ),
                'desc'     => esc_html__( 'Enable Quantity Field on Product Listing for all non variational products ', 'tz-wc-ajax-qty' ),
                'id'       => 'tz_wc_ajax_qty_global',
                'default'  => 'no',
                'type'     => 'checkbox',
                'desc_tip' => esc_html__( 'Checking this field will turn quantity field for all non variational products', 'tz-wc-ajax-qty' ),
            ),

            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'tz_ajax_qty_section_end'
            )
        );
        return apply_filters( 'tz_ajax_qty_section_settings', $settings );
    }

    function update_settings() {
        woocommerce_update_options( $this->get_settings() );
    }

    function product_options_field(){
        global $post;

        echo '<div class="options_group show_if_simple show_if_downloadable show_if_virtual hidden">';

        woocommerce_wp_checkbox(
            array(
                'id'          => '_tz_qty_box_enabled',
                'label'       => esc_html__( 'Enable Ajax Quantity Box', 'tz-wc-ajax-qty' ),
                'description' => esc_html__( 'Check this box if you want to enable Ajax Quantity field for this product.', 'tz-wc-ajax-qty' ),
                'value'       => wc_bool_to_string( get_post_meta($post->ID, '_tz_qty_box_enabled', true) )
            )
        );

        echo '</div>';

    }


    function process_product_meta( $post_id ) {
        $product_qty_field_enabled = $_POST['_tz_qty_box_enabled'];
        update_post_meta($post_id, '_tz_qty_box_enabled', esc_attr($product_qty_field_enabled));
    }

}
