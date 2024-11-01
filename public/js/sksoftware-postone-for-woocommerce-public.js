(function ($) {
	'use strict';

	$(
		function () {
			$( document ).on(
				'change',
				'[name="payment_method"]',
				function () {
					$( 'body' ).trigger( 'update_checkout', {update_shipping_method: true} );
				}
			);
		}
	);

})( jQuery );
