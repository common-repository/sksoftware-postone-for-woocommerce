/**
 * This file provides AST integration with our plugin.
 * It is called only if AST integration is not disabled by config.
 *
 * It will automatically prepopulate the AST fields for easier tracking creation.
 *
 * @package sksoftware-postone-for-woocommerce
 */

(function ($) {
	'use strict';
	$(
		function () {
			const $delete_button = $( '[data-toggle="sksoftware-postone-for-woocommerce-shipment-delete"]' );

			if ($delete_button.length === 0) {
				// Shipment not yet created.
				return;
			}

			const tracking_number  = $delete_button.data( 'shipment-id' );
			const ast_partner_name = $delete_button.data( 'ast-partner-name' );

			// Popup mode support.
			const callback = (mutationList, observer) => {
				for (const mutation of mutationList) {
					if ('childList' !== mutation.type) {
						continue;
					}

					for (const node of mutation.addedNodes) {
						if (node.classList && node.classList.contains( 'trackingpopup_wrapper' )) {
							$( '.trackingpopup_wrapper input[name="tracking_number"]' ).val( tracking_number );
							const $select = $( '.trackingpopup_wrapper select#tracking_provider' );

							$select.val( ast_partner_name );
							$select.trigger( 'change' );
						}
					}
				}
			};

			const observer = new MutationObserver( callback );
			observer.observe( document.body, { attributes: false, childList: true, subtree: false } );

			// Legacy mode support.
			if ($( '#woocommerce-advanced-shipment-tracking' ).length > 0) {
				$( '.button-show-tracking-form' ).on(
					'click',
					function () {
						$( '#woocommerce-advanced-shipment-tracking input[name="tracking_number"]' ).val( tracking_number );
						const $select = $( '#woocommerce-advanced-shipment-tracking select#tracking_provider' );

						$select.val( ast_partner_name );
						$select.trigger( 'change' );
					}
				);
			}

		}
	);
})( jQuery );
