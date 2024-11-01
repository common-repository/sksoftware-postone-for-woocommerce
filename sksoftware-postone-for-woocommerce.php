<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://sk-soft.net
 * @since             1.0.0
 * @package           Sksoftware_Postone_For_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:          SKSoftware PostOne for WooCommerce
 * Plugin URI:           https://sk-soft.net/plugins/postone-for-woocommerce/
 * Description:          This plugin integrates PostOne shipping method for WooCommerce.
 * Version:              1.1.1
 * Author:               Simeon Kolev & Martin Shterev from SK Software Ltd.
 * Author URI:           https://sk-soft.net
 * License:              GPL-2.0+
 * License URI:          http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:          sksoftware-postone-for-woocommerce
 * Domain Path:          /languages
 * Requires at least:    4.7
 * Tested up to:         6.5
 * Stable tag:           1.1.1
 * WC requires at least: 3.5.0
 * WC tested up to:      9.0.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) ) {
	require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('SKSOFTWARE_POSTONE_FOR_WOOCOMMERCE_VERSION', '1.1.1');

/**
 * Define constants for the connection to SK Software API
 */
if ( false === defined( 'SKSOFTWARE_POSTONE_FOR_WOOCOMMERCE_API_HOST' ) ) {
	define( 'SKSOFTWARE_POSTONE_FOR_WOOCOMMERCE_API_HOST', 'https://shipping.sk-soft.net' );
}

if ( false === defined( 'SKSOFTWARE_POSTONE_FOR_WOOCOMMERCE_SSL_VERIFY' ) ) {
	define( 'SKSOFTWARE_POSTONE_FOR_WOOCOMMERCE_SSL_VERIFY', true );
}

if ( false === defined( 'SKSOFTWARE_POSTONE_FOR_WOOCOMMERCE_TIMEOUT' ) ) {
	define( 'SKSOFTWARE_POSTONE_FOR_WOOCOMMERCE_TIMEOUT', 15 );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sksoftware-postone-for-woocommerce-activator.php';
	Sksoftware_Postone_For_Woocommerce_Activator::activate();
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sksoftware-postone-for-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function sksoftware_postone_for_woocommerce_run() {
	$plugin = new Sksoftware_Postone_For_Woocommerce();
	$plugin->run();
}

sksoftware_postone_for_woocommerce_run();
