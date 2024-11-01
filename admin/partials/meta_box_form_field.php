<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing
$field_id   = '_sksoftware-postone-for-woocommerce-form-field-' . str_replace( '_', '-', $key );
$field_name = '_sksoftware_postone_for_woocommerce_form_field_' . $key;
?>
<p class="form-field">
    <label for="<?php echo esc_attr( $field_id ); ?>">
		<?php
		$weight_unit_translation = __( get_option( 'woocommerce_weight_unit' ), 'woocommerce' );
		$length_unit_translation = __( get_option( 'woocommerce_dimension_unit' ), 'woocommerce' );

		$labels = array(
			/* translators: %s: weight unit */
			'weight'                  => sprintf( __( 'Weight (in %s)', 'sksoftware-postone-for-woocommerce' ), $weight_unit_translation ),
			/* translators: %s: dimension unit */
			'height'                  => sprintf( __( 'Height (in %s)', 'sksoftware-postone-for-woocommerce' ), $length_unit_translation ),
			/* translators: %s: dimension unit */
			'width'                   => sprintf( __( 'Width (in %s)', 'sksoftware-postone-for-woocommerce' ), $length_unit_translation ),
			/* translators: %s: dimension unit */
			'length'                  => sprintf( __( 'Length (in %s)', 'sksoftware-postone-for-woocommerce' ), $length_unit_translation ),
			'receiver_name'           => __( 'Receiver Name', 'sksoftware-postone-for-woocommerce' ),
			'receiver_email'          => __( 'Receiver Email', 'sksoftware-postone-for-woocommerce' ),
			'receiver_country'        => __( 'Receiver Country', 'sksoftware-postone-for-woocommerce' ),
			'receiver_region'         => __( 'Receiver Region', 'sksoftware-postone-for-woocommerce' ),
			'receiver_postal_code'    => __( 'Receiver Postal Code', 'sksoftware-postone-for-woocommerce' ),
			'receiver_city'           => __( 'Receiver City', 'sksoftware-postone-for-woocommerce' ),
			'receiver_address_line_1' => __( 'Receiver Address Line 1', 'sksoftware-postone-for-woocommerce' ),
			'receiver_address_line_2' => __( 'Receiver Address Line 2', 'sksoftware-postone-for-woocommerce' ),
			'receiver_address_number' => __( 'Receiver Address Number', 'sksoftware-postone-for-woocommerce' ),
			'receiver_phone'          => __( 'Receiver Phone', 'sksoftware-postone-for-woocommerce' ),
			'receiver_company_name'   => __( 'Receiver Company Name', 'sksoftware-postone-for-woocommerce' ),
			'receiver_vat_number'     => __( 'Receiver Vat Number', 'sksoftware-postone-for-woocommerce' ),
			'product'                 => __( 'Product', 'sksoftware-postone-for-woocommerce' ),
			'content'                 => __( 'Content', 'sksoftware-postone-for-woocommerce' ),
			'quantity'                => __( 'Quantity', 'sksoftware-postone-for-woocommerce' ),
			'country_origin'          => __( 'Sender Country', 'sksoftware-postone-for-woocommerce' ),
			'hs_tariff_code'          => __( 'HS Tariff Code', 'sksoftware-postone-for-woocommerce' ),
			'insurance'               => __( 'Insurance', 'sksoftware-postone-for-woocommerce' ),
			'unit_value'              => __( 'Unit Value', 'sksoftware-postone-for-woocommerce' ),
			'unit_value_currency'     => __( 'Unit Value Currency', 'sksoftware-postone-for-woocommerce' ),
			'terms_of_delivery'       => __( 'Terms Of Delivery', 'sksoftware-postone-for-woocommerce' ),
			'notify_receiver'         => __( 'Notify Receiver', 'sksoftware-postone-for-woocommerce' ),
			'reference_number'        => __( 'Reference Number', 'sksoftware-postone-for-woocommerce' ),
		);

		if ( isset( $labels[ $key ] ) ) {
			echo esc_html( $labels[ $key ] );
		} else {
			echo esc_html__( 'N/A', 'sksoftware-postone-for-woocommerce' );
		}
		?>
    </label>
	<?php
	$should_be_filled_if_customs = __( 'Should be filled if the shipment goes through customs.', 'sksoftware-postone-for-woocommerce' );

	$tips = array(
		'content'          => __( 'Shipment content (items).', 'sksoftware-postone-for-woocommerce' ) . ' ' . $should_be_filled_if_customs,
		'quantity'         => __( 'Shipment item quantity.', 'sksoftware-postone-for-woocommerce' ) . ' ' . $should_be_filled_if_customs,
		'country_origin'   => __( 'Which country does your shipment come from?', 'sksoftware-postone-for-woocommerce' ),
		'hs_tariff_code'   => $should_be_filled_if_customs,
		'notify_receiver'  => __( 'Do you want the receiver of the shipment to be notified for its status by PostOne?', 'sksoftware-postone-for-woocommerce' ),
		'reference_number' => __( 'Reference number should be the ID of the order in you store.', 'sksoftware-postone-for-woocommerce' ),
	);

	if ( isset( $tips[ $key ] ) ) {
		?>
        <span class="woocommerce-help-tip" data-tip="<?php echo esc_attr( $tips[ $key ] ); ?>"></span>
	<?php } ?>
	<?php if ( 'choice' === $field_type ) : ?>
        <select
                class="short <?php echo ( 'receiver_country' === $key || 'country_origin' === $key ) ? 'wc-enhanced-select' : 'select'; ?>"
                name="<?php echo esc_attr( $field_name ); ?>"
                id="<?php echo esc_attr( $field_id ); ?>"
			<?php if ( $disabled ) : ?>
                disabled
			<?php endif ?>

        >
			<?php foreach ( $field_choices as $choice_key => $choice_value ) : ?>
				<?php $is_selected = $value === $choice_key; ?>
                <option value="<?php echo esc_attr( $choice_key ); ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
					<?php echo esc_html__( $choice_value, 'sksoftware-postone-for-woocommerce' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText ?>
                </option>
			<?php endforeach; ?>
        </select>
	<?php else : ?>
        <input
                type="<?php echo esc_attr( $field_type ); ?>"
                class="short"
                name="<?php echo esc_attr( $field_name ); ?>"
                id="<?php echo esc_attr( $field_id ); ?>"
                value="<?php echo esc_attr( (string) $value ); ?>"
			<?php if ( 'number' === $field_type ) : ?>
                step="any"
			<?php endif; ?>
			<?php if ( $disabled ) : ?>
                disabled
			<?php endif; ?>
        >
	<?php endif; ?>
</p>
