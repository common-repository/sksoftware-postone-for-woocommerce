<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

if ( empty( $data ) ) : ?>
	<div style="margin-top: 12px;"></div>

	<p style="padding-left: 12px; padding-right: 12px; margin-bottom: 12px !important;">
		<?php echo esc_html__( 'Unable to connect to PostOne API.', 'sksoftware-postone-for-woocommerce' ); ?>
	</p>
<?php else : ?>
	<div class="panel-wrap">
		<ul class="wc-tabs">
			<li class="general_options active">
				<a href="#sksoftware_postone_for_woocommerce_general">
					<span><?php echo esc_html__( 'General', 'sksoftware-postone-for-woocommerce' ); ?></span>
				</a>
			</li>
			<li class="receiver_options"
				style="">
				<a href="#sksoftware_postone_for_woocommerce_receiver">
					<span><?php echo esc_html__( 'Receiver', 'sksoftware-postone-for-woocommerce' ); ?></span>
				</a>
			</li>
			<li class="shipping_options">
				<a href="#sksoftware_postone_for_woocommerce_shipping">
					<span><?php echo esc_html__( 'Shipping', 'sksoftware-postone-for-woocommerce' ); ?></span>
				</a>
			</li>
			<li class="other_options">
				<a href="#sksoftware_postone_for_woocommerce_other">
					<span><?php echo esc_html__( 'Other', 'sksoftware-postone-for-woocommerce' ); ?></span>
				</a>
			</li>
		</ul>

		<!-- General -->
		<div id="sksoftware_postone_for_woocommerce_general" class="panel woocommerce_options_panel">
			<div class="options_group">
				<?php
				$this->render_form_field( 'weight', $data['weight'], $is_shipment_created );
				$this->render_form_field( 'height', $data['height'], $is_shipment_created );
				$this->render_form_field( 'width', $data['width'], $is_shipment_created );
				$this->render_form_field( 'length', $data['length'], $is_shipment_created );
				?>
			</div>
		</div>

		<!-- Receiver -->
		<div id="sksoftware_postone_for_woocommerce_receiver" class="panel woocommerce_options_panel hidden">
			<div class="options_group">
				<?php
				$this->render_form_field( 'receiver_name', $data['receiver_name'], $is_shipment_created );
				$this->render_form_field( 'receiver_email', $data['receiver_email'], $is_shipment_created );
				$this->render_form_field( 'receiver_country', $data['receiver_country'], $is_shipment_created );
				$this->render_form_field( 'receiver_region', $data['receiver_region'], $is_shipment_created );
				$this->render_form_field( 'receiver_postal_code', $data['receiver_postal_code'], $is_shipment_created );
				$this->render_form_field( 'receiver_city', $data['receiver_city'], $is_shipment_created );
				$this->render_form_field( 'receiver_address_line_1', $data['receiver_address_line_1'], $is_shipment_created );
				$this->render_form_field( 'receiver_address_line_2', $data['receiver_address_line_2'], $is_shipment_created );
				$this->render_form_field( 'receiver_address_number', $data['receiver_address_number'], $is_shipment_created );
				$this->render_form_field( 'receiver_phone', $data['receiver_phone'], $is_shipment_created );
				$this->render_form_field( 'receiver_company_name', $data['receiver_company_name'], $is_shipment_created );
				$this->render_form_field( 'receiver_vat_number', $data['receiver_vat_number'], $is_shipment_created );
				?>
			</div>
		</div>

		<!-- Shipping -->
		<div id="sksoftware_postone_for_woocommerce_shipping" class="panel woocommerce_options_panel hidden">
			<div class="options_group">
				<?php
				$this->render_form_field( 'product', $data['product'], $is_shipment_created );
				$this->render_form_field( 'content', $data['content'], $is_shipment_created );
				$this->render_form_field( 'quantity', $data['quantity'], $is_shipment_created );
				$this->render_form_field( 'country_origin', $data['country_origin'], $is_shipment_created );
				$this->render_form_field( 'hs_tariff_code', $data['hs_tariff_code'], $is_shipment_created );
				?>
			</div>
		</div>

		<!-- Other -->
		<div id="sksoftware_postone_for_woocommerce_other" class="panel woocommerce_options_panel hidden">
			<div class="options_group">
				<?php
				$this->render_form_field( 'insurance', $data['insurance'], $is_shipment_created );
				$this->render_form_field( 'unit_value', $data['unit_value'], $is_shipment_created );
				$this->render_form_field( 'unit_value_currency', $data['unit_value_currency'], $is_shipment_created );
				$this->render_form_field( 'terms_of_delivery', $data['terms_of_delivery'], $is_shipment_created );
				$this->render_form_field( 'notify_receiver', $data['notify_receiver'], $is_shipment_created );
				$this->render_form_field( 'reference_number', $data['reference_number'], $is_shipment_created );
				?>
			</div>
		</div>

		<div class="clear"></div>
	</div>

	<div style="border-top: 1px solid #dfdfdf; padding: 1.5em 2em; text-align: right; background: #f8f8f8;">
		<?php if ( false === $is_shipment_created ) : ?>
			<button type="button" class="button button-secondary" data-toggle="sksoftware-postone-for-woocommerce-recalculate-shipping">
				<?php echo esc_html__( 'Recalculate shipping', 'sksoftware-postone-for-woocommerce' ); ?>
			</button>

			<button type="button" class="button button-primary" data-toggle="sksoftware-postone-for-woocommerce-shipment-create" style="margin-left: 1.5em;">
				<?php echo esc_html__( 'Create shipment', 'sksoftware-postone-for-woocommerce' ); ?>
			</button>
		<?php endif; ?>

		<?php
		if ( $is_shipment_created && false === $is_shipment_deleted ) :
			$shipment_id      = $order->get_meta( '_sksoftware_postone_for_woocommerce_tracking_number' );
			$ast_partner_name = $order->get_meta( '_sksoftware_postone_for_woocommerce_ast_partner_name' );

			?>
			<button
				type="button"
				class="button-link button-link-delete"
				data-toggle="sksoftware-postone-for-woocommerce-shipment-delete"
				data-shipment-id="<?php echo esc_attr( $shipment_id ); ?>"
				data-ast-partner-name="<?php echo esc_attr( $ast_partner_name ); ?>"
				style="line-height: 2.15384615; min-height: 30px;">
				<?php echo esc_html__( 'Delete shipment', 'sksoftware-postone-for-woocommerce' ); ?>
			</button>

			<a href="<?php echo esc_attr( $print_label_url ); ?>" class="button button-secondary" target="_blank" style="margin-left: 1.5em;">
				<?php echo esc_html__( 'Print label', 'sksoftware-postone-for-woocommerce' ); ?>
			</a>
		<?php endif ?>
	</div>
<?php endif ?>
