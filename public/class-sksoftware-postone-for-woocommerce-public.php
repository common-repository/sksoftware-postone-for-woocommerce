<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://sk-soft.net
 * @since      1.0.0
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/public
 * @author     SK Software <office@sk-soft.net>
 */
class Sksoftware_Postone_For_Woocommerce_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sksoftware-postone-for-woocommerce-public.min.js', array( 'jquery' ), $this->version, false );

		// Use WC backbone modals in the plugin.
		wp_enqueue_script( $this->plugin_name . '-backbone-modal', get_site_url() . '/wp-content/plugins/woocommerce/assets/js/admin/backbone-modal.js', array( 'jquery', 'wp-util', 'backbone' ) );
	}

	/**
	 * This action is triggered on checkout update. It triggers recalculation of the shipping price when the payment method changes.
	 *
	 * @param string $query
	 */
	public function woocommerce_checkout_update_order_review( $query ) {
		$packages = WC()->cart->get_shipping_packages();

		foreach ( $packages as $package_key => $package ) {
			$session_key = 'shipping_for_package_' . $package_key;

			WC()->session->__unset( $session_key );
		}

		parse_str( $query, $result );

		WC()->session->set( 'chosen_payment_method', $result['payment_method'] );

		WC()->cart->calculate_shipping();
	}
}
