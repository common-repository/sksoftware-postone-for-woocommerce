<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://sk-soft.net
 * @since      1.0.0
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sksoftware_Postone_For_Woocommerce
 * @subpackage Sksoftware_Postone_For_Woocommerce/admin
 * @author     SK Software <office@sk-soft.net>
 */
class Sksoftware_Postone_For_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/sksoftware-postone-for-woocommerce-admin.min.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/sksoftware-postone-for-woocommerce-admin.min.js',
			array( 'jquery' ),
			$this->version,
			false
		);

		if (
			class_exists( 'AST_Pro_Actions' )
			&& ! defined( 'SKSOFTWARE_POSTONE_FOR_WOOCOMMERCE_AST_INTEGRATION_DISABLE' )
			&& ! defined( 'SKSOFTWARE_POSTONE_FOR_WOOCOMMERCE_AST_INTEGRATION_DISABLE_PREFILL' )
		) {
			wp_enqueue_script(
				$this->plugin_name . '-ast-integration',
				plugin_dir_url( __FILE__ ) . 'js/sksoftware-postone-for-woocommerce-admin-ast-integration.min.js',
				array( 'jquery' ),
				$this->version,
				false
			);
		}
	}

	/**
	 * Checks if woocommerce is installed and active.
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_check() {
		if ( in_array(
			'woocommerce/woocommerce.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
			true
		) ) {
			return;
		}

		echo '<div class="error">
				<p>' . esc_html__(
			'SKSoftware PostOne for WooCommerce requires WooCommerce to be installed and active.',
			'sksoftware-postone-for-woocommerce'
		) . '</p>
			</div>';
	}

	/**
	 * Checks if woocommerce currency is supported.
	 *
	 * @since    1.0.0
	 */
	public function environment_check() {
		$messages = array();

		if ( false === function_exists( 'get_woocommerce_currency' ) ) {
			return;
		}

		// Currency check.
		if ( ! in_array(
			get_woocommerce_currency(),
			array(
				'BGN',
				'EUR',
				'USD',
				'GBP',
				'RON',
				'CHF',
				'JPY',
				'RUB',
			),
			true
		) ) {
			$messages[] = __(
				'WooCommerce currency is set to one of BGN, EUR, USD, GBP, RON, CHF, JPY, RUB',
				'sksoftware-postone-for-woocommerce'
			);
		}

		if ( ! empty( $messages ) ) {
			/* translators: %s: Error message */
			$prefix    = __(
				'SKSoftware PostOne for WooCommerce requires that %s',
				'sksoftware-postone-for-woocommerce'
			);
			$separator = ' ' . __( 'and', 'sksoftware-postone-for-woocommerce' ) . ' ';

			echo '<div class="error">
				<p>' . esc_html( sprintf( $prefix, implode( $separator, $messages ) ) ) . '</p>
			</div>';
		}
	}

	public function start_free_trial_modal() {
		?>
		<script type="text/template" id="tmpl-sksoftware-postone-for-woocommerce-start-free-trial">
			<div class="wc-backbone-modal">
				<div class="wc-backbone-modal-content">
					<section class="wc-backbone-modal-main" role="main">
						<header class="wc-backbone-modal-header">
							<h1><?php esc_html_e( 'Start free trial', 'sksoftware-postone-for-woocommerce' ); ?></h1>
							<button class="modal-close modal-close-link dashicons dashicons-no-alt">
								<span class="screen-reader-text">Close modal panel</span>
							</button>
						</header>
						<article>
							<div style="display: flex; flex-direction: column; align-items: center; padding-left: 30px; padding-right: 30px;">
								<img
									src="https://sk-soft.net/wp-content/uploads/2021/06/sk-soft-logo-white-background.svg"
									width="160"
									height="90"
									alt="sksoftware logo"
								/>
								<h3>
									<?php
									_e(
										'You are one step away from getting your API key.',
										'sksoftware-postone-for-woocommerce'
									);
									?>
								</h3>
								<p style="text-align: center;">
									<?php
									_e(
										'Please enter your email address and we will fill your API key automatically.',
										'sksoftware-postone-for-woocommerce'
									);
									?>
								</p>
								<div id="sksoftware-postone-for-woocommerce-errors" class="sksoftware-start-free-trial-errors"></div>
								<p class="sksoftware-start-free-trial-form">
									<label for="admin_email" class="sksoftware-start-free-trial-label">
										<?php _e( 'Your email address', 'sksoftware-postone-for-woocommerce' ); ?>
									</label>
									<br>
									<input
										type="text"
										id="admin_email"
										class="sksoftware-start-free-trial-input"
										value="<?php echo get_bloginfo( 'admin_email' ); ?>"
									>
									<br>
									<input name="accepted_terms" type="checkbox" id="accepted_terms">
									<label for="accepted_terms">
										<?php
										echo sprintf(
										/* translators: 1: Privacy policy link 2: Terms & Agreements link 3: License policy link */
											__(
												'I accept the <a target="_blank" href="%1$s">privacy policy</a>, <a target="_blank" href="%2$s">terms & agreements</a> and <a target="_blank" href="%3$s">license policy</a>',
												'sksoftware-postone-for-woocommerce'
											),
											esc_url( 'https://sk-soft.net/privacy-policy/' ),
											esc_url( 'https://sk-soft.net/terms-and-conditions/' ),
											esc_url( 'https://sk-soft.net/license-agreement' )
										);
										?>
									</label>
								</p>
							</div>
						</article>
						<footer>
							<div class="inner">
								<button id="sksoftware-postone-for-woocommerce-get-license" class="button button-primary button-large">
									<?php esc_html_e( 'Get license', 'sksoftware-postone-for-woocommerce' ); ?>
								</button>
							</div>
						</footer>
					</section>
				</div>
			</div>
			<div class="wc-backbone-modal-backdrop modal-close"></div>
		</script>
		<?php
	}

	public function start_free_trial_success() {
		if ( ! isset( $_GET['sksoftware_postone_for_woocommerce_start_free_trial_success'] ) ) {
			return;
		}

		$is_success = 'true' === $_GET['sksoftware_postone_for_woocommerce_start_free_trial_success'];

		$message = __(
			'Your free trial has started successfully. You can now use the plugin for free for 14 days.',
			'sksoftware-postone-for-woocommerce'
		);

		if ( ! $is_success ) {
			$message = __(
				'Your free trial has failed to start. Please try again later.',
				'sksoftware-postone-for-woocommerce'
			);
		}

		$notice_type = $is_success ? 'success' : 'error';

		echo '<div class="notice notice-' . $notice_type . ' is-dismissible">
			  	<p>' . esc_html( $message ) . '</p>
			  </div>';
	}
}
