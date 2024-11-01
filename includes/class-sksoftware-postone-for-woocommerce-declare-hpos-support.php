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
 * This is used to declare HPOS support.
 *
 * @since      1.0.0
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/includes
 * @author     SK Software <office@sk-soft.net>
 */

class Sksoftware_Postone_For_Woocommerce_Declare_Hpos_Support {
	/**
	 * Declares HPOS support.
	 *
	 * @return void
	 */
	public function declare_hpos_support() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			$file = 'sksoftware-postone-for-woocommerce/sksoftware-postone-for-woocommerce.php';

			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $file, true );
		}
	}
}
