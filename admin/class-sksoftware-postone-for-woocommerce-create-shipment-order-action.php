<?php

/**
 * Create PostOne shipment order action functionality.
 *
 * @link       https://sk-soft.net
 * @since      1.0.0
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 */

/**
 * Create PostOne shipment order action functionality.
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 * @author     SK Software <office@sk-soft.net>
 */
class Sksoftware_Postone_For_Woocommerce_Create_Shipment_Order_Action {
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
	 * This filter adds the Create PostOne shipment action to order actions
	 * which have postone as shipping method.
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	public function add_action( $actions ) {
		$order = wc_get_order();

		if ( false === $this->order_utilities->get_is_valid_shipping_method( $order ) ) {
			return $actions;
		}

		if ( 'yes' === $order->get_meta( '_sksoftware_postone_for_woocommerce_shipment_created' ) ) {
			return $actions;
		}

		$actions['sksoftware_postone_for_woocommerce_shipment_create_order_action'] = __(
			'Create PostOne shipment',
			'sksoftware-postone-for-woocommerce'
		);

		return $actions;
	}

	/**
	 * This action handles the Create PostOne shipment action.
	 *
	 * @param WC_Order $order
	 */
	public function handle_action( $order ) {
		if ( false === $this->order_utilities->get_is_valid_shipping_method( $order ) ) {
			wp_die( esc_html__( 'Order shipping method must be PostOne.', 'sksoftware-postone-for-woocommerce' ) );
		}

		$this->order_utilities->create_shipment( $order );
	}
}
