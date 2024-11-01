<?php

/**
 * Base class for sksoftware postone for woocommerce shipping method.
 *
 * @link       https://sk-soft.net
 * @since      1.0.0
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/includes
 */

/**
 * Base class for sksoftware postone for woocommerce shipping method.
 *
 * @since      1.0.0
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/includes
 * @author     SK Software <office@sk-soft.net>
 */
class Sksoftware_Postone_For_Woocommerce_Shipping_Method extends WC_Shipping_Method {
	/**
	 * Features this method supports. Possible features used by core:
	 * - shipping-zones Shipping zone functionality + instances
	 * - instance-settings Instance settings screens.
	 * - settings Non-instance settings screens. Enabled by default for BW compatibility with methods before instances existed.
	 * - instance-settings-modal Allows the instance settings to be loaded within a modal in the zones UI.
	 *
	 * @var array
	 */
	public $supports = array( 'shipping-zones', 'instance-settings', 'instance-settings-modal', 'settings' );

	/**
	 * @var Sksoftware_Postone_For_Woocommerce_Api_Client
	 */
	private $api_client;

	/**
	 * @param int $instance_id
	 *
	 * @inheritDoc
	 */
	public function __construct( $instance_id = 0 ) {
		parent::__construct( $instance_id );

		$this->id                 = 'sksoftware_postone_for_woocommerce';
		$this->title              = 'PostOne';
		$this->method_title       = 'PostOne';
		$this->method_description = __(
			'Allows customers to receive shipments using PostOne.',
			'sksoftware-postone-for-woocommerce'
		);

		$this->init_form_fields();

		$this->init_settings();

		if ( $instance_id ) {
			$this->init_instance_settings();
		}

		if ( ! empty( $this->get_settings()['title'] ) ) {
			$this->method_title = $this->get_settings()['title'];
		}

		if ( ! empty( $this->get_settings()['description'] ) ) {
			$this->method_description = $this->get_settings()['description'];
		}

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

		$this->api_client = new Sksoftware_Postone_For_Woocommerce_Api_Client( $this->get_settings() );
	}

	/**
	 * @inheritDoc
	 */
	public function init_form_fields() {
		parent::init_form_fields();

		$hs_tariff_code_url = 'https://en.wikipedia.org/wiki/Harmonized_System';

		if ( 'bg_BG' === get_user_locale() ) {
			$hs_tariff_code_url = 'https://bg.wikipedia.org/wiki/%D0%A5%D0%B0%D1%80%D0%BC%D0%BE%D0%BD%D0%B8%D0%B7%D0%B8%D1%80%D0%B0%D0%BD%D0%B0_%D1%81%D0%B8%D1%81%D1%82%D0%B5%D0%BC%D0%B0';
		}

		add_thickbox();

		$this->form_fields = array(
			'api_key'                   => array(
				'title'       => __( 'SK Software API Key', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'text',
				/* translators: 1: SK Software PostOne for WooCommerce plugin link 2: Start free trial link */
				'description' => sprintf(
					__(
						'API Key provided by SK Software Ltd. You can buy a license on %1$s or %2$s',
						'sksoftware-postone-for-woocommerce'
					),
					'<a target="_blank" href="https://sk-soft.net/plugins/postone-for-woocommerce">here</a>',
					'<a href="#" id="sksoftware-postone-for-woocommerce-start-free-trial">start a 14 days free trial</a>.'
				),
				'default'     => '',
			),
			'sender_id'                 => array(
				'title'       => __( 'Sender ID', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'text',
				/* translators: %1$s: PostOne link %2$s: PostOne senders and users link */
				'description' => sprintf(
					__(
						'You can check this value by navigating to %1$s => Settings => Senders and Users => ID or clicking %2$s if you are already signed in.',
						'sksoftware-postone-for-woocommerce'
					),
					'<a href="https://postone.eu" target="_blank">https://postone.eu</a>',
					'<a href="https://postone.eu/senders-and-users" target="_blank">https://postone.eu/senders-and-users</a>'
				),
				'default'     => '',
			),
			'client_id'                 => array(
				'title'       => __( 'Client ID', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'text',
				/* translators: %s: PostOne API link */
				'description' => sprintf(
					__(
						'You can generate API keys from %s.',
						'sksoftware-postone-for-woocommerce'
					),
					'<a href="https://api.postone.eu" target="_blank">https://api.postone.eu</a>'
				) . '<br/>' . __(
					'Login with your PostOne account and click "Create New Client". In the "Name" field type your store name and "Redirect URL" should be the URL of your store.',
					'sksoftware-postone-for-woocommerce'
				),
				'default'     => '',
			),
			'client_secret'             => array(
				'title'       => __( 'Client Secret', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'password',
				/* translators: %s: PostOne API link */
				'description' => sprintf(
					__(
						'You can generate API keys from %s.',
						'sksoftware-postone-for-woocommerce'
					),
					'<a href="https://api.postone.eu" target="_blank">https://api.postone.eu</a>'
				),
				'default'     => '',
			),
			'should_send_tracking_link' => array(
				'title'       => __( 'Send tracking link', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'checkbox',
				'description' => __(
					'This option can send the tracking number of the shipment to the customer. If the shipment you generated has a tracking number, it will automatically send a note to the customer.',
					'sksoftware-postone-for-woocommerce'
				),
				'default'     => false,
			),
			'default_hs_tariff_code'    => array(
				'title'       => __( 'Default HS tariff code', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'text',
				/* translators: %s: HS Tariff code link */
				'description' => __(
					'This field is optional.',
					'sksoftware-postone-for-woocommerce'
				) . '<br>' . sprintf(
					__(
						'Your default HS tariff code for every order. This field is required only if sending outside of the European Union. You can learn more here: %s.',
						'sksoftware-postone-for-woocommerce'
					),
					'<a href="' . $hs_tariff_code_url . '" target="_blank">' . urldecode( $hs_tariff_code_url ) . '</a>'
				),
				'default'     => '',
			),
		);

		$default_product_weight  = null;
		$default_box_height      = null;
		$default_box_width       = null;
		$default_box_length      = null;
		$weight_unit_translation = __( get_option( 'woocommerce_weight_unit' ), 'woocommerce' );
		$length_unit_translation = __( get_option( 'woocommerce_dimension_unit' ), 'woocommerce' );

		switch ( get_option( 'woocommerce_weight_unit' ) ) {
			case 'kg':
				$default_product_weight = '0.250';
				break;
			case 'g':
				$default_product_weight = '250';
				break;
			case 'lbs':
				$default_product_weight = '0.5';
				break;
			case 'oz':
				$default_product_weight = '8';
				break;
		}

		switch ( get_option( 'woocommerce_dimension_unit' ) ) {
			case 'cm':
				$default_box_height = '15';
				$default_box_width  = '15';
				$default_box_length = '15';
				break;
			case 'm':
				$default_box_height = '0.15';
				$default_box_width  = '0.15';
				$default_box_length = '0.15';
				break;
			case 'mm':
				$default_box_height = '150';
				$default_box_width  = '150';
				$default_box_length = '150';
				break;
			case 'in':
				$default_box_height = '6';
				$default_box_width  = '6';
				$default_box_length = '6';
				break;
			case 'yd':
				$default_box_height = '0.16';
				$default_box_width  = '0.16';
				$default_box_length = '0.16';
				break;
		}

		$this->instance_form_fields = array(
			'title'                     => array(
				'title'       => __( 'Title', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'text',
				'description' => __(
					'This controls the title of the shipping method, which the user sees during checkout.',
					'sksoftware-postone-for-woocommerce'
				),
				'default'     => 'PostOne',
			),
			'description'               => array(
				'title'       => __( 'Description', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'text',
				'description' => __(
					'This controls the description of the shipping method, which the user sees during checkout.',
					'sksoftware-postone-for-woocommerce'
				),
			),
			'default_product_weight'    => array(
				/* translators: %s: weight unit */
				'title'       => sprintf(
					__( 'Default product weight in %s', 'sksoftware-postone-for-woocommerce' ),
					$weight_unit_translation
				),
				'type'        => 'decimal',
				'description' => __(
					'Weight to be used for products that are without weight.',
					'sksoftware-postone-for-woocommerce'
				),
				'default'     => $default_product_weight,
			),
			'default_box_height'        => array(
				/* translators: %s: dimension unit */
				'title'       => sprintf(
					__( 'Default box height in %s', 'sksoftware-postone-for-woocommerce' ),
					$length_unit_translation
				),
				'type'        => 'decimal',
				'description' => __( 'Height to be used for shipment volume.', 'sksoftware-postone-for-woocommerce' ),
				'default'     => $default_box_height,
			),
			'default_box_width'         => array(
				/* translators: %s: dimension unit */
				'title'       => sprintf(
					__( 'Default box width in %s', 'sksoftware-postone-for-woocommerce' ),
					$length_unit_translation
				),
				'type'        => 'decimal',
				'description' => __( 'Width to be used for shipment volume.', 'sksoftware-postone-for-woocommerce' ),
				'default'     => $default_box_width,
			),
			'default_box_length'        => array(
				/* translators: %s: dimension unit */
				'title'       => sprintf(
					__( 'Default box length in %s', 'sksoftware-postone-for-woocommerce' ),
					$length_unit_translation
				),
				'type'        => 'decimal',
				'description' => __( 'Length to be used for shipment volume.', 'sksoftware-postone-for-woocommerce' ),
				'default'     => $default_box_length,
			),
			'postone_product'           => array(
				'title'       => __( 'PostOne service', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'select',
				/* translators: %s: PostOne link */
				'description' => sprintf(
					__(
						'Choose which PostOne service to be used. More information about delivery time, pricing and maximum weight of each service can be found in %s.',
						'sksoftware-postone-for-woocommerce'
					),
					'<a href="https://postone.eu">https://postone.eu</a>'
				),
				'default'     => 'TRC',
				'options'     => array(
					'TRC' => 'ONE TRACK',
					'BSC' => 'ONE BASIC',
					'PRM' => 'ONE PREMIUM',
					'EXP' => 'ONE EXPRESS',
					'RTN' => 'ONE RETURN',
					'LTR' => 'ONE LETTER',
					'PAC' => 'ONE PACK',
					'SLC' => 'ONE SELECT',
				),
			),
			'postone_insurance'         => array(
				'title'       => __( 'PostOne insurance', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'select',
				'description' => __(
					'Fill this field if you want your shipment to be insured.',
					'sksoftware-postone-for-woocommerce'
				),
				'default'     => '',
				'options'     => array(
					''    => __( 'No insurance', 'sksoftware-postone-for-woocommerce' ),
					'45'  => '45',
					'150' => '150',
					'300' => '300',
				),
			),
			'merchant_shipping_fee'     => array(
				'title'       => __( 'Merchant shipping fee', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'decimal',
				'description' => __(
					'Used to add the amount you pay for your delivery to go through Econt or another courier to reach PostOne.',
					'sksoftware-postone-for-woocommerce'
				),
				'default'     => '0',
			),
			'default_terms_of_delivery' => array(
				'title'       => __( 'Default terms of delivery', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'select',
				'description' => __(
					'Fill this field if you want your shipment to have default terms of delivery.',
					'sksoftware-postone-for-woocommerce'
				),
				'default'     => 'DAP',
				'options'     => array(
					'DAP' => __(
						'Delivered At Place (named place of destination)',
						'sksoftware-postone-for-woocommerce'
					),
					'DDP' => __(
						'Delivered Duty Paid (named place of destination)',
						'sksoftware-postone-for-woocommerce'
					),
					'EXW' => __( 'EX Works (named place)', 'sksoftware-postone-for-woocommerce' ),
					'FCA' => __( 'Free Carrier (named place)', 'sksoftware-postone-for-woocommerce' ),
					'FAS' => __( 'Free Alongside Ship (named port of shipment)', 'sksoftware-postone-for-woocommerce' ),
					'FOB' => __( 'Free On Board (named port of shipment)', 'sksoftware-postone-for-woocommerce' ),
					'CFR' => __( 'Cost and Freight (named port of destination)', 'sksoftware-postone-for-woocommerce' ),
					'CIF' => __(
						'Cost, Insurance and Freight (named port of destination)',
						'sksoftware-postone-for-woocommerce'
					),
					'CPT' => __(
						'Carriage Paid To (named place of destination)',
						'sksoftware-postone-for-woocommerce'
					),
					'CIP' => __(
						'Carriage and Insurance Paid То (named place of destination)',
						'sksoftware-postone-for-woocommerce'
					),
					'DAT' => __(
						'Delivered At Terminal (named terminal of destination)',
						'sksoftware-postone-for-woocommerce'
					),
				),
			),
			'pricing_override'          => array(
				'title'       => __( 'Pricing override', 'sksoftware-postone-for-woocommerce' ),
				'type'        => 'table',
				'description' => __(
					'This table allows you to change the price according to the conditions set in it. You can learn more about how it works and sample settings for different cases in our documentation.',
					'sksoftware-postone-for-woocommerce'
				),
				'fields'      => array(
					'type'         => array(
						'title'   => __( 'Type', 'sksoftware-postone-for-woocommerce' ),
						'type'    => 'select',
						'options' => array(
							'flat_rate'          => __( 'Flat rate', 'sksoftware-postone-for-woocommerce' ),
							'merchant_all_below' => __(
								'Merchant pays all below',
								'sksoftware-postone-for-woocommerce'
							),
							'merchant_all_above' => __(
								'Merchant pays all above',
								'sksoftware-postone-for-woocommerce'
							),
						),
					),
					'order_amount' => array(
						'title' => __( 'Order amount equal or above', 'sksoftware-postone-for-woocommerce' ),
						'type'  => 'decimal',
					),
					'weight'       => array(
						/* translators: %s: weight unit */
						'title' => sprintf(
							__( 'Weight equal or below in %s', 'sksoftware-postone-for-woocommerce' ),
							$weight_unit_translation
						),
						'type'  => 'decimal',
					),
					'amount'       => array(
						'title' => __( 'Shipping cost equals', 'sksoftware-postone-for-woocommerce' ),
						'type'  => 'decimal',
					),
				),
			),
		);
	}

	/**
	 * Calculate shipping function.
	 *
	 * @access public
	 *
	 * @param mixed $package
	 *
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		$price = $this->api_client->calculate_shipping_for_cart( $package );

		if ( null !== $price ) {
			$rate = array(
				'label' => $this->get_settings()['title'],
				'cost'  => $price,
			);

			$this->add_rate( $rate );
		}
	}

	/**
	 * Validates the data of a field of type table_field.
	 *
	 * @param string $key
	 * @param array  $data
	 *
	 * @return array
	 */
	public function validate_table_field( $key, $data ) {
		$post_data = empty( $data ) ? $this->recursive_sanitize_text_field( $_POST ) : $data; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( false === isset( $post_data['data'] ) ) {
			wp_die( 'An error occurred. Contact your administrator.' );
		}

		$data      = $post_data['data'];
		$field_key = $this->get_field_key( $key );
		$data      = array_filter(
			$data,
			function ( $array_key ) use ( $field_key ) {
				return false !== strpos( $array_key, $field_key );
			},
			ARRAY_FILTER_USE_KEY
		);

		$table_data = array();

		foreach ( $data as $data_key => $data_value ) {
			$sanitized_key = str_replace( $field_key . '_', '', $data_key );

			list( $index, $name ) = explode( '_', $sanitized_key, 2 );

			if ( false === isset( $table_data[ $index ] ) ) {
				$table_data[ $index ] = array();
			}

			$table_data[ $index ][ $name ] = $data_value;
		}

		return $table_data;
	}

	/**
	 * @return bool
	 */
	public function process_admin_options() {
		global $current_section;

		$result = parent::process_admin_options();

		delete_option( 'sksoftware_postone_for_woocommerce_is_authenticated' );
		delete_option( 'sksoftware_postone_for_woocommerce_auth_failed_after_save' );

		$this->api_client = Sksoftware_Postone_For_Woocommerce_Api_Client::create( 0 );
		$this->api_client->authenticate();

		if ( false === $this->api_client->is_authenticated() ) {
			update_option( 'sksoftware_postone_for_woocommerce_auth_failed_after_save', 'yes', 'no' );

			$this->add_error(
				__(
					'Unable to authenticate PostOne. Please check your API keys and try again.',
					'sksoftware-postone-for-woocommerce'
				)
			);

			if ( 'sksoftware_postone_for_woocommerce' === $current_section ) {
				$this->display_errors();
			}
		}

		return $result;
	}

	/**
	 * Generate table HTML.
	 *
	 * @param string $key Field key.
	 * @param array  $data Field data.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_table_html( $key, $data ) {
		$defaults     = array(
			'title'       => '',
			'description' => '',
			'fields'      => '',
		);
		$data         = wp_parse_args( $data, $defaults );
		$table_fields = $data['fields'];
		$table_data   = $this->get_option( $key, array() );
		$row_template = $this->generate_table_row_html( $key, $table_fields, $table_data );

		ob_start();

		include plugin_dir_path( __DIR__ ) . 'admin/partials/settings_table_html.php';

		return ob_get_clean();
	}

	/**
	 * @param string $table_key
	 * @param array  $table_fields
	 * @param array  $table_data
	 * @param null   $index
	 *
	 * @return false|string
	 */
	public function generate_table_row_html( $table_key, $table_fields, $table_data, $index = null ) {
		ob_start();

		include plugin_dir_path( __DIR__ ) . 'admin/partials/settings_table_row_html.php';

		return ob_get_clean();
	}

	/**
	 * @param string $key
	 * @param mixed  $data
	 * @param string $table_key
	 * @param mixed  $table_data
	 * @param int    $index
	 *
	 * @return string
	 */
	public function generate_table_select_html( $key, $data, $table_key, $table_data, $index ) {
		if ( null === $index ) {
			$field_key = $this->get_field_key( $table_key . '_%index%_' . $key );
			$field_id  = $field_key;
		} else {
			$field_key = $this->get_field_key( $table_key . '_' . $index . '_' . $key );
			$field_id  = $field_key;
		}

		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => 'select',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'options'           => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		if ( null === $index ) {
			$value = null;
		} else {
			$value = $table_data[ $index ][ $key ];
		}

		ob_start();

		include plugin_dir_path( __DIR__ ) . 'admin/partials/settings_table_select_html.php';

		return ob_get_clean();
	}

	/**
	 * @param string $key
	 * @param mixed  $data
	 * @param string $table_key
	 * @param mixed  $table_data
	 * @param int    $index
	 *
	 * @return string
	 */
	public function generate_table_decimal_html( $key, $data, $table_key, $table_data, $index ) {
		if ( null === $index ) {
			$field_key = $this->get_field_key( $table_key . '_%index%_' . $key );
			$field_id  = $field_key;
		} else {
			$field_key = $this->get_field_key( $table_key . '_' . $index . '_' . $key );
			$field_id  = $field_key;
		}

		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		if ( null === $index ) {
			$value = null;
		} else {
			$value = $table_data[ $index ][ $key ];
		}

		ob_start();

		include plugin_dir_path( __DIR__ ) . 'admin/partials/settings_table_decimal_html.php';

		return ob_get_clean();
	}

	/**
	 * @return array
	 */
	public function get_settings() {
		if ( ! $this->instance_id ) {
			return $this->settings;
		}

		return array_merge( $this->settings, $this->instance_settings );
	}

	/**
	 * Recursively validate array of texts.
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public function recursive_sanitize_text_field( $array ) {
		foreach ( $array as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = $this->recursive_sanitize_text_field( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
		}

		return $array;
	}
}
