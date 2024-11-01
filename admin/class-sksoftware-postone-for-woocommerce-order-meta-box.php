<?php

/**
 * Order meta box functionality.
 *
 * @link       https://sk-soft.net
 * @since      1.0.0
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 */

/**
 * Order meta box functionality.
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 * @author     SK Software <office@sk-soft.net>
 */
class Sksoftware_Postone_For_Woocommerce_Order_Meta_Box {
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
	 * Add the meta box.
	 */
	public function add_meta_box() {
		$order = wc_get_order();

		if ( ! $order ) {
			return;
		}

		/** @var WC_Order $order */

		if ( false === $this->order_utilities->get_is_valid_shipping_method( $order ) ) {
			return;
		}

		$screen = class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) && wc_get_container()->get( \Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
			? wc_get_page_screen_id( 'shop-order' )
			: 'shop_order';

		add_meta_box(
			'sksoftware-postone-for-woocommerce',
			'PostOne' . wc_help_tip(
				__(
					'Settings for your PostOne shipment.',
					'sksoftware-postone-for-woocommerce'
				)
			),
			array(
				$this,
				'meta_box_output',
			),
			$screen,
			'normal',
			'low'
		);
	}

	/**
	 * Handle meta box save.
	 *
	 * @param int $post_id
	 */
	public function handle_meta_box_save( $post_id ) {
		$order = wc_get_order( $post_id );

		/** @var WC_Order $order */

		$is_shipment_created = 'yes' === $order->get_meta( '_sksoftware_postone_for_woocommerce_shipment_created' );

		if ( $is_shipment_created ) {
			return;
		}

		$data = array();

		foreach ( $_POST as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( 0 !== strpos( $key, '_sksoftware_postone_for_woocommerce_form_field' ) ) {
				continue;
			}

			$meta_key          = sanitize_key(
				str_replace(
					'_sksoftware_postone_for_woocommerce_form_field_',
					'',
					$key
				)
			);
			$data[ $meta_key ] = sanitize_text_field( $value );
		}

		$order->update_meta_data( '_sksoftware_postone_for_woocommerce_shipment_parameters_override', $data );
		$order->save();
	}

	/**
	 * Output of the meta box.
	 *
	 * @param WP_Post|WC_Order $post_or_order_object
	 */
	public function meta_box_output( $post_or_order_object ) {
		$order = $post_or_order_object instanceof WP_Post ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;

		/** @var WC_Order $order */

		if ( false === $this->order_utilities->get_is_valid_shipping_method( $order ) ) {
			wp_die( esc_html__( 'Order shipping method is not PostOne.', 'sksoftware-postone-for-woocommerce' ) );
		}

		$is_shipment_created = 'yes' === $order->get_meta( '_sksoftware_postone_for_woocommerce_shipment_created' );
		$is_shipment_deleted = 'yes' === $order->get_meta( '_sksoftware_postone_for_woocommerce_shipment_deleted' );


		if ( $this->order_utilities->get_has_shipping_label( $order ) ) {
			$print_label_url = $this->order_utilities->get_print_label_url( $order->get_id() );
		}

		$instance_id        = $this->order_utilities->get_postone_shipping_method_instance( $order );
		$postone_api_client = Sksoftware_Postone_For_Woocommerce_Api_Client::create( $instance_id );

		$data = $postone_api_client->get_shipment_parameters( $order );

		ob_start();

		include plugin_dir_path( __DIR__ ) . 'admin/partials/meta_box_output.php';

		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @param bool   $disabled
	 */
	private function render_form_field(
		$key,
		$value,
		$disabled
	) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$field_type = $this->get_field_type( $key );

		if ( 'choice' === $field_type ) {
			$field_choices = $this->get_field_choices( $key );
		}

		include plugin_dir_path( __DIR__ ) . 'admin/partials/meta_box_form_field.php';
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	private function get_field_type( $key ) {
		if ( in_array( $key, array( 'weight', 'height', 'width', 'length', 'unit_value', 'quantity' ), true ) ) {
			return 'number';
		}

		if ( in_array(
			$key,
			array(
				'product',
				'insurance',
				'unit_value_currency',
				'terms_of_delivery',
				'receiver_country',
				'country_origin',
				'notify_receiver',
			),
			true
		) ) {
			return 'choice';
		}

		return 'text';
	}

	/**
	 * @param string $key
	 *
	 * @return array|string[]
	 */
	private function get_field_choices( $key ) {
		if ( 'product' === $key ) {
			return array(
				'TRC' => 'ONE TRACK',
				'BSC' => 'ONE BASIC',
				'PRM' => 'ONE PREMIUM',
				'EXP' => 'ONE EXPRESS',
				'RTN' => 'ONE RETURN',
				'LTR' => 'ONE LETTER',
				'PAC' => 'ONE PACK',
				'SLC' => 'ONE SELECT',
			);
		}

		if ( 'insurance' === $key ) {
			return array(
				''    => __( 'No insurance', 'sksoftware-postone-for-woocommerce' ),
				'45'  => '45',
				'150' => '150',
				'300' => '300',
			);
		}

		if ( 'unit_value_currency' === $key ) {
			return array(
				'BGN' => 'BGN',
				'EUR' => 'EUR',
				'USD' => 'USD',
				'GBP' => 'GBP',
				'RON' => 'RON',
				'CHF' => 'CHF',
				'JPY' => 'JPY',
				'RUB' => 'RUB',
			);
		}

		if ( 'terms_of_delivery' === $key ) {
			return array(
				'DAP' => __( 'Delivered At Place (named place of destination)', 'sksoftware-postone-for-woocommerce' ),
				'DDP' => __( 'Delivered Duty Paid (named place of destination)', 'sksoftware-postone-for-woocommerce' ),
				'EXW' => __( 'EX Works (named place)', 'sksoftware-postone-for-woocommerce' ),
				'FCA' => __( 'Free Carrier (named place)', 'sksoftware-postone-for-woocommerce' ),
				'FAS' => __( 'Free Alongside Ship (named port of shipment)', 'sksoftware-postone-for-woocommerce' ),
				'FOB' => __( 'Free On Board (named port of shipment)', 'sksoftware-postone-for-woocommerce' ),
				'CFR' => __( 'Cost and Freight (named port of destination)', 'sksoftware-postone-for-woocommerce' ),
				'CIF' => __(
					'Cost, Insurance and Freight (named port of destination)',
					'sksoftware-postone-for-woocommerce'
				),
				'CPT' => __( 'Carriage Paid To (named place of destination)', 'sksoftware-postone-for-woocommerce' ),
				'CIP' => __(
					'Carriage and Insurance Paid То (named place of destination)',
					'sksoftware-postone-for-woocommerce'
				),
				'DAT' => __(
					'Delivered At Terminal (named terminal of destination)',
					'sksoftware-postone-for-woocommerce'
				),
			);
		}

		if ( 'receiver_country' === $key || 'country_origin' === $key ) {
			return WC()->countries->get_countries();
		}

		if ( 'notify_receiver' === $key ) {
			return array(
				'0' => __( 'No', 'sksoftware-postone-for-woocommerce' ),
				'1' => __( 'Yes', 'sksoftware-postone-for-woocommerce' ),
			);
		}

		return array();
	}
}
