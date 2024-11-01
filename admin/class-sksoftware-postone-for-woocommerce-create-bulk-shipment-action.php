<?php
/**
 * Create Postone shipment order action functionality.
 *
 * @link       https://sk-soft.net
 * @since      1.1.0
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 */

/**
 * Create Postone bulk shipment action functionality.
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 * @author     SK Software <office@sk-soft.net>
 */
class Sksoftware_Postone_For_Woocommerce_Create_Bulk_Shipment_Action {
	/**
	 * The order utilities class.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      Sksoftware_Postone_For_Woocommerce_Order_Utilities $order_utilities The order utilities class.
	 */
	protected $order_utilities;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param Sksoftware_Postone_For_Woocommerce_Order_Utilities $order_utilities The order utilities class.
	 */
	public function __construct( $order_utilities ) {
		$this->order_utilities = $order_utilities;
	}

	/**
	 * This filter adds the menu item in orders page.
	 *
	 * @param array $bulk_actions The bulk actions array.
	 *
	 * @return array
	 */
	public function add_action( $bulk_actions ) {
		$bulk_actions['sksoftware_postone_for_woocommerce_create_bulk_shipment'] = __( 'Generate Postone shipments', 'sksoftware-postone-for-woocommerce' );

		return $bulk_actions;
	}

	/**
	 * This action handles the bulk create Postone shipment action.
	 *
	 * @param string $redirect
	 * @param string $action
	 * @param array  $object_ids
	 *
	 * @return string
	 */
	public function handle_action( $redirect, $action, $object_ids ) {
		if ( 'sksoftware_postone_for_woocommerce_create_bulk_shipment' !== $action ) {
			return $redirect;
		}

		$errored_ids = array();
		$created_ids = array();

		// change status of every selected order.
		foreach ( $object_ids as $order_id ) {
			$order = wc_get_order( $order_id );

			// Is shipping method Postone?
			if ( false === $this->order_utilities->get_is_valid_shipping_method( $order ) ) {
				$errored_ids[] = $order_id;

				continue;
			}

			// Shipment exists.
			if ( 'yes' === $order->get_meta( '_sksoftware_postone_for_woocommerce_shipment_created' ) ) {
				$errored_ids[] = $order_id;

				continue;
			}

			$this->order_utilities->create_shipment( $order );

			$created_ids[] = $order_id;
		}

		// do not forget to add query args to URL because we will show notices later.
		return add_query_arg(
			array(
				'bulk_action' => 'sksoftware_postone_bulk_shipments_generated',
				'created_ids' => $created_ids,
				'errored_ids' => $errored_ids,
			),
			$redirect
		);
	}

	/**
	 * This action handles the bulk create Postone shipment action.
	 */
	public function handle_completion() {
		$bulk_action = filter_input( INPUT_GET, 'bulk_action', FILTER_SANITIZE_STRING );

		if ( 'sksoftware_postone_bulk_shipments_generated' !== $bulk_action ) {
			return;
		}

		$created_ids = filter_input( INPUT_GET, 'created_ids', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		$errored_ids = filter_input( INPUT_GET, 'errored_ids', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );

		$created_count = is_countable( $created_ids ) ? count( $created_ids ) : 0;
		$errored_count = is_countable( $errored_ids ) ? count( $errored_ids ) : 0;

		if ( $created_count > 0 ) {
			$order_links = array_map(
				function ( $order_id ) {
					return sprintf(
						'<p>%s <a href="%s" target="_blank">%s</a> / <a href="%s" target="_blank">%s</a></p>',
						__( 'Order', 'sksoftware-postone-for-woocommerce' ),
						admin_url( "post.php?post=$order_id&action=edit" ),
						$order_id,
						$this->order_utilities->get_print_label_url( $order_id ),
						__( 'Print label', 'sksoftware-postone-for-woocommerce' )
					);
				},
				$created_ids
			);

			$translation = _n(
			/* translators: %1$d: number of orders */
				"<strong style='font-size: 16px;'>%1\$d shipment created.</strong>",
				"<strong style='font-size: 16px;'>%1\$d shipments created.</strong>",
				$created_count,
				'sksoftware-postone-for-woocommerce'
			);
			$message = sprintf( $translation, $created_count );

			$this->print_message_html( 'notice', $message, implode( '', $order_links ) );
		}

		if ( $errored_count > 0 ) {
			$order_links = array_map(
				function ( $order_id ) {
					return sprintf(
						'<p>%s <a href="%s" target="_blank">%s</a></p>',
						__( 'Order', 'sksoftware-postone-for-woocommerce' ),
						admin_url( "post.php?post=$order_id&action=edit" ),
						$order_id
					);
				},
				$errored_ids
			);

			$translation = _n(
			/* translators: %1$d: number of orders */
				"<strong style='font-size: 16px;'>%1\$d shipment failed to create.</strong>",
				"<strong style='font-size: 16px;'>%1\$d shipments failed to create.</strong>",
				$errored_count,
				'sksoftware-postone-for-woocommerce'
			);
			$message = sprintf( $translation, $errored_count );

			$this->print_message_html( 'error', $message, implode( '', $order_links ) );
		}
	}

	/**
	 * Print message HTML.
	 *
	 * @param string $type Type of message.
	 * @param string $message HTML message.
	 * @param string $order_links HTML links to orders.
	 *
	 * @return void
	 */
	private function print_message_html( $type, $message, $order_links ) {
		?>
		<div id="message" class="updated <?php echo esc_attr( $type ); ?> is-dismissible">
			<p>
				<?php
				echo wp_kses(
					$message,
					array(
						'strong' => array( 'style' => array() ),
					)
				);
				?>
			</p>
			<?php
			echo wp_kses(
				$order_links,
				array(
					'p' => array(),
					'a' => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			);
			?>
		</div>
		<?php
	}
}
