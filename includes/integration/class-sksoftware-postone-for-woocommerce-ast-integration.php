<?php

/**
 * The core plugin class.
 *
 * This class is used to integrate Advanced Shipment Tracking for WooCommerce with our plugin.
 *
 * @since      1.0.0
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/includes/integration
 * @author     SK Software <office@sk-soft.net>
 */
class Sksoftware_Postone_For_Woocommerce_AST_Integration {
	/**
	 * This function adds tracking info to AST plugin.
	 *
	 * @param WC_Order $order WooCommerce order.
	 *
	 * @return void
	 * @since    1.0.0
	 */
	public function add_tracking_information_to_ast( $order ) {
		if ( defined( 'SKSOFTWARE_POSTONE_FOR_WOOCOMMERCE_AST_INTEGRATION_DISABLE' ) ) {
			return;
		}

		if ( defined( 'SKSOFTWARE_POSTONE_FOR_WOOCOMMERCE_AST_INTEGRATION_DISABLE_AUTOGENERATE' ) ) {
			return;
		}

		if ( ! class_exists( 'AST_Pro_Actions' ) ) {
			return;
		}

		if ( ! function_exists( 'ast_insert_tracking_number' ) ) {
			return;
		}

		$tracking_number   = $order->get_meta( '_sksoftware_postone_for_woocommerce_tracking_number' );
		$tracking_provider = $order->get_meta( '_sksoftware_postone_for_woocommerce_ast_partner_name' );

		if ( ! $tracking_provider ) {
			return;
		}

		$tracking_info_exist           = tracking_info_exist( $order->get_id(), $tracking_number );
		$restrict_adding_same_tracking = get_option( 'restrict_adding_same_tracking', 1 );

		if ( $tracking_info_exist && $restrict_adding_same_tracking ) {
			return;
		}

		ast_insert_tracking_number(
			$order->get_id(),
			wc_clean( $tracking_number ),
			$tracking_provider,
			gmdate( 'Y-m-d' )
		);
	}

	/**
	 * This function adds tracking info to AST plugin.
	 *
	 * @param WC_Order $order WooCommerce order.
	 *
	 * @return void
	 * @since    1.0.0
	 */
	public function remove_tracking_information_from_ast( $order ) {
		if ( ! class_exists( 'AST_Pro_Actions' ) ) {
			return;
		}

		if ( ! function_exists( 'ast_get_tracking_items' ) ) {
			return;
		}

		if ( ! function_exists( 'tracking_info_exist' ) ) {
			return;
		}

		$order_id = $order->get_id();
		$ast      = AST_Pro_Actions::get_instance();

		foreach ( ast_get_tracking_items( $order_id ) as $item ) {
			$ast->delete_tracking_item( $order_id, $item['tracking_id'] );
		}
	}
}
