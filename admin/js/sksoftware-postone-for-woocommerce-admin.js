(function ($) {
	'use strict';
	$(
		function () {
			$( '.sksoftware_postone_for_woocommerce_print_shipment_label' ).attr( 'target', '_blank' );

			$( '[data-toggle="sksoftware-postone-for-woocommerce-shipment-create"]' ).on(
				'click',
				function () {
					$( '#woocommerce-order-actions' ).find( '[name="wc_order_action"]' ).val(
						'sksoftware_postone_for_woocommerce_shipment_create_order_action'
					);
					$( '#woocommerce-order-actions' ).find( 'button.save_order' ).trigger( 'click' );
				}
			);

			$( '[data-toggle="sksoftware-postone-for-woocommerce-shipment-delete"]' ).on(
				'click',
				function () {
					$( '#woocommerce-order-actions' ).find( '[name="wc_order_action"]' ).val(
						'sksoftware_postone_for_woocommerce_shipment_delete_order_action'
					);
					$( '#woocommerce-order-actions' ).find( 'button.save_order' ).trigger( 'click' );
				}
			);

			$( '[data-toggle="sksoftware-postone-for-woocommerce-recalculate-shipping"]' ).on(
				'click',
				function () {
					$( '#woocommerce-order-actions' ).find( '[name="wc_order_action"]' ).val(
						'sksoftware_postone_for_woocommerce_recalculate_shipping_order_action'
					);
					$( '#woocommerce-order-actions' ).find( 'button.save_order' ).trigger( 'click' );
				}
			);

			$( document ).on(
				'click',
				'[data-toggle="sksoftware-postone-for-woocommerce-add-new-row"]',
				function () {
					var table         = $( this ).closest( 'table' );
					var table_body    = $( this ).closest( 'table' ).find( 'tbody' );
					var template      = table_body.data( 'table-row-template' );
					var current_index = table_body.data( 'current-index' );

					if (0 !== current_index && ! current_index) {
						current_index = 0;
					} else {
						current_index++;
					}

					template = template.replaceAll( '%index%', current_index );

					table_body.data( 'current-index', current_index );

					table_body.append( template );

					$( document.body ).trigger( 'wc-enhanced-select-init' );

					table.find( 'tbody.sksoftware-settings-table-sortable' ).sortable( 'refresh' );
				}
			);

			$( document ).on(
				'click',
				'[data-toggle="sksoftware-postone-for-woocommerce-delete-row"]',
				function () {
					$( this ).closest( 'tr' ).remove();
				}
			);

			$( document ).on(
				'wc_backbone_modal_loaded',
				function () {
					$( '.sksoftware-settings-table' ).find( 'tbody.sksoftware-settings-table-sortable' ).sortable(
						{
							items: 'tr',
							cursor: 'move',
							axis: 'y',
							handle: 'td.sksoftware-settings-table-handle',
							scrollSensitivity: 40
						}
					);
				}
			);

			$( document ).on(
				'wc_backbone_modal_loaded',
				function () {
					$( '#sksoftware-postone-for-woocommerce-get-license' ).on(
						'click',
						function () {
							var $errors_container = $( '#sksoftware-postone-for-woocommerce-errors' );
							var $button           = $( this );

							$errors_container.hide();
							$errors_container.html( '' );
							$button.prop( 'disabled', true );

							var data = {
								'action': 'sksoftware_postone_for_woocommerce_start_free_trial_action',
								'email': $( '#admin_email' ).val(),
								'accepted_terms': $( '#accepted_terms' ).is( ':checked' ),
							};
							$.post(
								ajaxurl,
								data,
								function (response) {
									var res = JSON.parse( response );
									$button.prop( 'disabled', true );

									if (res.status === 200) {
										var data1 = {
											'action': 'sksoftware_postone_for_woocommerce_start_free_trial_save_api_key_action',
											'api_key': res.api_key,
										};

										$.post(
											ajaxurl,
											data1,
											function (response) {
												var res1 = JSON.parse( response );

												if (res1.status) {
													window.location.href = window.location.href + '&sksoftware_postone_for_woocommerce_start_free_trial_success=true';
												} else {
													window.location.href = window.location.href + '&sksoftware_postone_for_woocommerce_start_free_trial_success=false';
												}
											}
										);
									} else if (res.status === 422) {
										$errors_container.show();
										$errors_container.append( res.violations[0].title + '<br>' );
									}

									$button.prop( 'disabled', false );
								}
							);
						}
					);
				}
			);

			$( "#sksoftware-postone-for-woocommerce-start-free-trial" ).on(
				'click',
				function (event) {
					event.preventDefault();

					$( this ).WCBackboneModal(
						{
							template: 'sksoftware-postone-for-woocommerce-start-free-trial'
						}
					);
				}
			);
		}
	);
})( jQuery );
