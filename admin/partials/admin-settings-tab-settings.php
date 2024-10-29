<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://www.solwininfotech.com/
 * @since      1.0.0
 *
 * @package    Aior
 * @subpackage Aior/admin
 */

$nav_args = array(
	'id'     => '101',
	'name'   => esc_html__( 'Settings', 'all-in-one-reservation' ),
	'title'  => esc_html__( 'Basic Settings', 'all-in-one-reservation' ),
	'desc'   => esc_html__( 'Set the currency and its symbol to use in the site.', 'all-in-one-reservation' ),
	'filter' => 'aior_add_common_admin_settings',
);
self::create_tab( $nav_args );
add_filter(
	'aior_add_common_admin_settings',
	function () {
		$rf_data                           = Aior_Admin::get_settings();
		$aior_builder                      = new Aior_Builder();
		$aior_new_appointment_notification = get_option( 'aior_new_appointment_notification' );
		if ( ! $aior_new_appointment_notification ) {
			$aior_new_appointment_notification = 0;
		}
		?>
	<div class="form-group solrow2">
			<div class="solcol2"><label class="control-label"><?php esc_html_e( 'Currency', 'all-in-one-reservation' ); ?></label></div>
			<div class="solcol2">
				<?php
				$aior_currency = isset( $rf_data['aior_currency'] ) && ! empty( $rf_data['aior_currency'] ) ? $rf_data['aior_currency'] : 'USD';
				$aior_builder->add_field(
					array(
						'type'  => 'text',
						'id'    => 'aior_currency',
						'name'  => 'aior_currency',
						'value' => $aior_currency,
					)
				);
				?>
				<small><?php esc_html_e( 'Specify a currency using its', 'all-in-one-reservation' ); ?> <a href="https://www.iban.com/currency-codes" target="new"><?php esc_html_e( '3-letter ISO code', 'all-in-one-reservation' ); ?></a>. <?php esc_html_e( 'Defaults to USD if left blank.', 'all-in-one-reservation' ); ?></small>
			</div>
		</div>
		<div class="form-group solrow2">
			<div class="solcol2"><label class="control-label"><?php esc_html_e( 'Currency Symbol', 'all-in-one-reservation' ); ?></label></div>
			<div class="solcol2">
				<?php
				$aior_currency_symbol = isset( $rf_data['aior_currency_symbol'] ) && ! empty( $rf_data['aior_currency_symbol'] ) ? $rf_data['aior_currency_symbol'] : '$';
				$aior_builder->add_field(
					array(
						'type'  => 'text',
						'id'    => 'aior_currency_symbol',
						'name'  => 'aior_currency_symbol',
						'value' => $aior_currency_symbol,
					)
				);
				?>
				<small><?php esc_html_e( 'Specify a currency symbol using its', 'all-in-one-reservation' ); ?> <a href="https://www.eurochange.co.uk/travel-money/world-currency-abbreviations-symbols-and-codes-travel-money" target="new"><?php esc_html_e( 'Currency Symbol', 'all-in-one-reservation' ); ?></a>. <?php esc_html_e( 'Defaults to $ if left blank.', 'all-in-one-reservation' ); ?></small>
			</div>
		</div>
		<?php

	},
	10,
	1
);
