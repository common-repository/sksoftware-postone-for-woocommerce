<?php

/**
 * Print PostOne shipment label order action functionality.
 *
 * @link       https://sk-soft.net
 * @since      1.0.0
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 */

/**
 * Print PostOne shipment label order action functionality.
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 * @author     SK Software <office@sk-soft.net>
 */
class Sksoftware_Postone_For_Woocommerce_Print_Shipment_Label_Order_Action {
	/**
	 * The order utilities class.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sksoftware_Postone_For_Woocommerce_Order_Utilities $order_utilities The order utilities class.
	 */
	protected $order_utilities;

	/**
	 * @param Sksoftware_Postone_For_Woocommerce_Order_Utilities $order_utilities The order utilities class.
	 */
	public function __construct( $order_utilities ) {
		$this->order_utilities = $order_utilities;
	}

	/**
	 * This filter adds the Print PostOne shipment label action to order actions
	 * which have postone as shipping method.
	 *
	 * @param array    $actions
	 * @param WC_Order $order
	 *
	 * @return array
	 */
	public function add_action( $actions, $order ) {
		if ( false === $this->order_utilities->get_is_valid_shipping_method( $order ) ) {
			return $actions;
		}

		if ( empty( $this->order_utilities->get_has_shipping_label( $order ) ) ) {
			return $actions;
		}

		$actions['sksoftware_postone_for_woocommerce_print_shipment_label'] = array(
			'url'    => wp_nonce_url(
				admin_url( 'admin-ajax.php?action=sksoftware_postone_for_woocommerce_print_shipment_label&order_id=' . $order->get_id() ),
				'sksoftware_postone_for_woocommerce_print_shipment_label'
			),
			'name'   => __( 'Print PostOne shipment label', 'sksoftware-postone-for-woocommerce' ),
			'action' => 'sksoftware_postone_for_woocommerce_print_shipment_label',
		);

		return $actions;
	}

	/**
	 * This action handles the Print PostOne shipment label action.
	 */
	public function handle_action() {
		$order_id = filter_input( INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT );

		check_ajax_referer( 'sksoftware_postone_for_woocommerce_print_shipment_label_' . $order_id, true );

		if ( ! $order_id ) {
			wp_die( esc_html__( 'Order ID is missing.', 'sksoftware-postone-for-woocommerce' ) );
		}

		$order = wc_get_order( $order_id );

		if ( false === $this->order_utilities->get_is_valid_shipping_method( $order ) ) {
			wp_die( esc_html__( 'Order shipping method must be Postone.', 'sksoftware-postone-for-woocommerce' ) );
		}

		if ( false === $this->order_utilities->get_has_shipping_label( $order ) ) {
			wp_die( esc_html__( 'Order must have a shipping label.', 'sksoftware-postone-for-woocommerce' ) );
		}

		$instance_id       = $this->order_utilities->get_postone_shipping_method_instance( $order );
		$postone_api_client = Sksoftware_Postone_For_Woocommerce_Api_Client::create( $instance_id );

		$response = $postone_api_client->print_shipment_label( $order );

		if ( 'application/pdf' !== $response['headers']['content-type'] || 200 !== $response['response']['code'] ) {
			wp_die( esc_html__( 'Failed to print shipment label.', 'sksoftware-postone-for-woocommerce' ) );
		}

		header( 'Content-type:' . $response['headers']['content-type'] );
		header( 'Content-Disposition:inline' );

		echo $response['body']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		die;
	}
}
