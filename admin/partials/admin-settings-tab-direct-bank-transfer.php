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
	'id'     => '106',
	'name'   => esc_html__( 'Direct Bank Transfer', 'all-in-one-reservation' ),
	'title'  => esc_html__( 'Direct Bank Transfer', 'all-in-one-reservation' ),
	'desc'   => esc_html__( 'Make your payment directly into our bank account. Please use your Booking ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.', 'all-in-one-reservation' ),
	'filter' => 'aior_add_dbt_admin_settings',
);

self::create_tab( $nav_args );
add_filter(
	'aior_add_dbt_admin_settings',
	function () {
		$rf_data      = Aior_Admin::get_settings();
		$aior_builder = new Aior_Builder();
		?>
	<div class="form-group solrow2">
		<div class="solcol2"><label><?php esc_html_e( 'Account Name', 'all-in-one-reservation' ); ?></label> </div>
		<div class="solcol2">
			<?php
			$aior_account_name = isset( $rf_data['aior_account_name'] ) && ! empty( $rf_data['aior_account_name'] ) ? $rf_data['aior_account_name'] : '0';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'id'    => 'aior_account_name',
					'name'  => 'aior_account_name',
					'value' => $aior_account_name,
				)
			);
			?>
		</div>
	</div>
	<div class="form-group solrow2">
		<div class="solcol2"><label><?php esc_html_e( 'Account Number', 'all-in-one-reservation' ); ?></label> </div>
		<div class="solcol2">
			<?php
			$aior_account_no = isset( $rf_data['aior_account_no'] ) && ! empty( $rf_data['aior_account_no'] ) ? $rf_data['aior_account_no'] : '0';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'id'    => 'aior_account_no',
					'name'  => 'aior_account_no',
					'value' => $aior_account_no,
				)
			);
			?>
		</div>
	</div>
	<div class="form-group solrow2">
		<div class="solcol2"><label><?php esc_html_e( 'Bank Name', 'all-in-one-reservation' ); ?></label> </div>
		<div class="solcol2">
			<?php
			$aior_bank_name = isset( $rf_data['aior_bank_name'] ) && ! empty( $rf_data['aior_bank_name'] ) ? $rf_data['aior_bank_name'] : '0';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'id'    => 'aior_bank_name',
					'name'  => 'aior_bank_name',
					'value' => $aior_bank_name,
				)
			);
			?>
		</div>
	</div>
	<div class="form-group solrow2">
		<div class="solcol2"><label><?php esc_html_e( 'IFSC Code', 'all-in-one-reservation' ); ?></label> </div>
		<div class="solcol2">
			<?php
			$aior_ifsc_code = isset( $rf_data['aior_ifsc_code'] ) && ! empty( $rf_data['aior_ifsc_code'] ) ? $rf_data['aior_ifsc_code'] : '0';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'id'    => 'aior_ifsc_code',
					'name'  => 'aior_ifsc_code',
					'value' => $aior_ifsc_code,
				)
			);
			?>
		</div>
	</div>
		<?php wp_nonce_field( 'on_aior_admin_global_nonce', 'aior_admin_global_nonce' ); ?>
		<?php
	},
	10,
	1
);
