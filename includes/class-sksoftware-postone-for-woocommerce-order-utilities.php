<?php

/**
 * The file that defines the api client class
 *
 * @link       https://sk-soft.net
 * @since      1.0.0
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/includes
 */

/**
 * The order utilities class.
 *
 * This is used to define common functions used to access
 * order data across the codebase.
 *
 * @since      1.0.0
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/includes
 * @author     SK Software <office@sk-soft.net>
 */
class Sksoftware_Postone_For_Woocommerce_Order_Utilities {
	/**
	 * Checks if an order is chosen to be shipped with PostOne
	 *
	 * @param WC_Order $order
	 *
	 * @return bool
	 */
	public function get_is_valid_shipping_method( $order ) {
		$shipping_methods = $order->get_shipping_methods();

		/** @var WC_Order_Item_Shipping $shipping_method */
		foreach ( $shipping_methods as $shipping_method ) {
			if ( 'sksoftware_postone_for_woocommerce' === $shipping_method->get_method_id() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get shipping method instance
	 *
	 * @param WC_Order $order
	 *
	 * @return string|null
	 */
	public function get_postone_shipping_method_instance( $order ) {
		$shipping_methods = $order->get_shipping_methods();

		/** @var WC_Order_Item_Shipping $shipping_method */
		foreach ( $shipping_methods as $shipping_method ) {
			if ( 'sksoftware_postone_for_woocommerce' === $shipping_method->get_method_id() ) {
				return $shipping_method->get_instance_id();
			}
		}

		return null;
	}

	/**
	 * Get the shipment label URL for an order id.
	 *
	 * @param int $id Order ID.
	 *
	 * @return string
	 */
	public function get_print_label_url( $id ) {
		return wp_nonce_url(
			admin_url( 'admin-ajax.php?action=sksoftware_postone_for_woocommerce_print_shipment_label&order_id=' . $id ),
			'sksoftware_postone_for_woocommerce_print_shipment_label_' . $id
		);
	}

	/**
	 * Determine if the order can have a shipping label based on
	 * if the shipment is created and not deleted.
	 *
	 * @param WC_Order $order
	 *
	 * @return bool
	 */
	public function get_has_shipping_label( $order ) {
		$is_created = 'yes' === $order->get_meta( '_sksoftware_postone_for_woocommerce_shipment_created' );
		$is_deleted = 'yes' === $order->get_meta( '_sksoftware_postone_for_woocommerce_shipment_deleted' );

		return $is_created && false === $is_deleted;
	}

	/**
	 * Create shipment for an order.
	 *
	 * @param WC_Order $order
	 *
	 * @return void
	 */
	public function create_shipment( $order ) {
		$instance_id = $this->get_postone_shipping_method_instance( $order );

		$api_client = Sksoftware_Postone_For_Woocommerce_Api_Client::create( $instance_id );
		$settings   = ( new Sksoftware_Postone_For_Woocommerce_Shipping_Method( $instance_id ) )->get_settings();
		$data       = $api_client->create_shipment( $order );

		$shipment_number        = sanitize_text_field( $data['item_number'] );
		$tracking_number        = sanitize_text_field( $data['tracking_number'] );
		$expect_tracking_number = sanitize_text_field( $data['expect_tracking_number'] );
		$partner_name           = sanitize_text_field( $data['partner_name'] );
		$ast_partner_name       = sanitize_text_field( $data['ast_partner_name'] );
		$tracking_link          = esc_url_raw( $data['tracking_link'] );

		/* translators: %s: Shipment number */
		$order->add_order_note(
			__(
				'PostOne shipment created.',
				'sksoftware-postone-for-woocommerce'
			) . ' ' . sprintf(
				__(
					'Shipment number %s.',
					'sksoftware-postone-for-woocommerce'
				),
				$shipment_number
			),
			0,
			true
		);

		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_shipment_number', $shipment_number );
		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_tracking_number', $tracking_number );
		$order->update_meta_data(
			'_sksoftware_postone_for_woocommerce_expect_tracking_number',
			$expect_tracking_number
		);
		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_partner_name', $partner_name );
		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_ast_partner_name', $ast_partner_name );

		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_shipment_created', 'yes' );
		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_shipment_deleted', 'no' );

		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_tracking_link', $tracking_link );

		if ( 'yes' === $settings['should_send_tracking_link'] && $tracking_number && $tracking_link ) {
			/* translators: %s: Tracking link */
			$note = sprintf( __( 'Your tracking link is: %s', 'sksoftware-postone-for-woocommerce' ), $tracking_link );

			$order->add_order_note( $note, 1, true );
		}

		$order->save();

		do_action( 'sksoftware_postone_for_woocommerce_shipment_created', $order );
	}
}
