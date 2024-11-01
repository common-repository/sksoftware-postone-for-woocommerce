<?php

/**
 * Delete PostOne shipment order action functionality.
 *
 * @link       https://sk-soft.net
 * @since      1.0.0
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 */

/**
 * Delete PostOne shipment order action functionality.
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 * @author     SK Software <office@sk-soft.net>
 */
class Sksoftware_Postone_For_Woocommerce_Delete_Shipment_Order_Action {
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
	 * This filter adds the Delete PostOne shipment action to order actions
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

		$shipment_deleted = $order->get_meta( '_sksoftware_postone_for_woocommerce_shipment_deleted' );

		if ( 'yes' === $shipment_deleted || '' === $shipment_deleted ) {
			return $actions;
		}

		$actions['sksoftware_postone_for_woocommerce_shipment_delete_order_action'] = __(
			'Delete PostOne shipment',
			'sksoftware-postone-for-woocommerce'
		);

		return $actions;
	}

	/**
	 * This action handles the Delete PostOne shipment action.
	 *
	 * @param WC_Order $order
	 */
	public function handle_action( $order ) {
		if ( false === $this->order_utilities->get_is_valid_shipping_method( $order ) ) {
			wp_die( esc_html__( 'Order shipping method must be PostOne.', 'sksoftware-postone-for-woocommerce' ) );
		}

		$instance_id        = $this->order_utilities->get_postone_shipping_method_instance( $order );
		$postone_api_client = Sksoftware_Postone_For_Woocommerce_Api_Client::create( $instance_id );
		$result             = $postone_api_client->delete_shipment( $order );

		if ( ! $result ) {
			wp_die( esc_html__( 'Unable to delete PostOne shipment.', 'sksoftware-postone-for-woocommerce' ) );
		}

		$order->add_order_note( __( 'PostOne shipment deleted.', 'sksoftware-postone-for-woocommerce' ), 0, true );

		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_shipment_number', null );
		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_tracking_number', null );
		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_expect_tracking_number', null );

		/**
		 * This is here for backwards compatibility with version 1.0.5 and down.
		 * Before, labels were stored in a meta field instead of getting them from the API.
		 * This is not needed anymore, but we still need to remove the old data to not clutter the database.
		 */
		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_label', null );
		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_tracking_link', null );

		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_shipment_deleted', 'yes' );
		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_shipment_created', 'no' );
		$order->save();
	}
}
