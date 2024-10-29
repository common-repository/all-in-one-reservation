<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.solwininfotech.com/
 * @since      1.0.0
 *
 * @package    Aior
 * @subpackage Aior/admin/partials
 */

$nav_args = array(
	'id'     => '2',
	'name'   => esc_html__( 'Time Slot', 'all-in-one-reservation' ),
	'title'  => esc_html__( 'Time Slots Manager', 'all-in-one-reservation' ),
	'desc'   => esc_html__( 'To manage weekly/monthly time slot or particular dates', 'all-in-one-reservation' ),
	'filter' => 'aior_add_in_day_settings',
);
self::create_rs_tab( $nav_args );
add_filter(
	'aior_add_in_day_settings',
	function () {
		$rf_data           = Aior_Reservation_Form::get_settings();
		$aior_builder      = new Aior_Builder();
		$working_days_json = isset( $rf_data['working_days'] ) ? $rf_data['working_days'] : '';
		$working_days      = maybe_unserialize( $working_days_json );
		?>
	<div class="form-group rest_row solrow" style="display: none;">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Monday', 'all-in-one-reservation' ); ?></label></div>
		<div class="form-check solcol">
			<label class="form-check-label">
				<?php
					$monday_off = isset( $working_days['monday']['off'] ) ? $working_days['monday']['off'] : '';
					$aior_builder->add_field(
						array(
							'type'     => 'checkbox',
							'name'     => 'working_days[monday][off]',
							'class'    => 'form-check-input r_closed_days',
							'value'    => $monday_off,
							'selected' => $monday_off,
							'default'  => 1,
						)
					);
					esc_html_e( 'Is Closed', 'all-in-one-reservation' );
				?>
				</label>
			</div>
		<div class="field-group solcol">
			<?php
			$monday_open = isset( $working_days['monday']['open'] ) ? $working_days['monday']['open'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'name'  => 'working_days[monday][open]',
					'class' => 'form-control timepicker h_opening_time',
					'value' => $monday_open,
				)
			);
			?>
			<label class="form-check-label"><?php esc_html_e( 'Set Opening time.', 'all-in-one-reservation' ); ?></label>
		</div>
		<div class="field-group solcol">
			<?php
			$monday_close = isset( $working_days['monday']['close'] ) ? $working_days['monday']['close'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'name'  => 'working_days[monday][close]',
					'class' => 'form-control timepicker h_closing_time',
					'value' => $monday_close,
				)
			);
			?>
			<label class="form-check-label"><?php esc_html_e( 'Set Closing time.', 'all-in-one-reservation' ); ?></label>
		</div>
	</div>
	<div class="form-group rest_row solrow" style="display: none;">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Tuesday', 'all-in-one-reservation' ); ?></label></div>
		<div class="form-check solcol"><label class="form-check-label">
		<?php
			$tuesday_off = isset( $working_days['tuesday']['off'] ) ? $working_days['tuesday']['off'] : '';
			$aior_builder->add_field(
				array(
					'type'     => 'checkbox',
					'name'     => 'working_days[tuesday][off]',
					'class'    => 'form-check-input r_closed_days',
					'value'    => $tuesday_off,
					'selected' => $tuesday_off,
					'default'  => 1,
				)
			);
			esc_html_e( 'Is Closed', 'all-in-one-reservation' );
		?>
		</label>
		</div>
		<div class="field-group solcol">
		<?php
		$tuesday_open = isset( $working_days['tuesday']['open'] ) ? $working_days['tuesday']['open'] : '';
		$aior_builder->add_field(
			array(
				'type'  => 'text',
				'name'  => 'working_days[tuesday][open]',
				'class' => 'form-control timepicker h_opening_time',
				'value' => $tuesday_open,
			)
		);
		?>
		<label class="form-check-label"><?php esc_html_e( 'Set Opening time.', 'all-in-one-reservation' ); ?></label></div>
		<div class="field-group solcol">
		<?php
		$tuesday_close = isset( $working_days['tuesday']['close'] ) ? $working_days['tuesday']['close'] : '';
		$aior_builder->add_field(
			array(
				'type'  => 'text',
				'name'  => 'working_days[tuesday][close]',
				'class' => 'form-control timepicker h_closing_time',
				'value' => $tuesday_close,
			)
		);
		?>
		<label class="form-check-label"><?php esc_html_e( 'Set Closing time.', 'all-in-one-reservation' ); ?></label></div>
	</div>
	<div class="form-group rest_row solrow" style="display: none;">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Wednesday', 'all-in-one-reservation' ); ?></label></div>
		<div class="form-check solcol"><label class="form-check-label">
		<?php
		$wednesday_off = isset( $working_days['wednesday']['off'] ) ? $working_days['wednesday']['off'] : '';
		$aior_builder->add_field(
			array(
				'type'     => 'checkbox',
				'name'     => 'working_days[wednesday][off]',
				'class'    => 'form-check-input r_closed_days',
				'value'    => $wednesday_off,
				'selected' => $wednesday_off,
				'default'  => 1,
			)
		);
			esc_html_e( 'Is Closed', 'all-in-one-reservation' );
		?>
		</label></div>
		<div class="field-group solcol">
		<?php
		$wednesday_open = isset( $working_days['wednesday']['open'] ) ? $working_days['wednesday']['open'] : '';
		$aior_builder->add_field(
			array(
				'type'  => 'text',
				'name'  => 'working_days[wednesday][open]',
				'class' => 'form-control timepicker h_opening_time',
				'value' => $wednesday_open,
			)
		);
		?>
		<label class="form-check-label"><?php esc_html_e( 'Set Opening time.', 'all-in-one-reservation' ); ?></label>
		</div>
		<div class="field-group solcol">
		<?php
		$wednesday_close = isset( $working_days['wednesday']['close'] ) ? $working_days['wednesday']['close'] : '';
		$aior_builder->add_field(
			array(
				'type'  => 'text',
				'name'  => 'working_days[wednesday][close]',
				'class' => 'form-control timepicker h_closing_time',
				'value' => $wednesday_close,
			)
		);
		?>
		<label class="form-check-label"><?php esc_html_e( 'Set Closing time.', 'all-in-one-reservation' ); ?></label></div>
	</div>
	<div class="form-group rest_row solrow" style="display: none;">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Thursday', 'all-in-one-reservation' ); ?></label></div>
		<div class="form-check solcol"><label class="form-check-label">
		<?php
		$thursday_off = isset( $working_days['thursday']['off'] ) ? $working_days['thursday']['off'] : '';
		$aior_builder->add_field(
			array(
				'type'     => 'checkbox',
				'name'     => 'working_days[thursday][off]',
				'class'    => 'form-check-input r_closed_days',
				'value'    => $thursday_off,
				'selected' => $thursday_off,
				'default'  => 1,
			)
		);
			esc_html_e( 'Is Closed', 'all-in-one-reservation' );
		?>
		</label></div>
		<div class="field-group solcol">
		<?php
		$thursday_open = isset( $working_days['thursday']['open'] ) ? $working_days['thursday']['open'] : '';
		$aior_builder->add_field(
			array(
				'type'  => 'text',
				'name'  => 'working_days[thursday][open]',
				'class' => 'form-control timepicker h_opening_time',
				'value' => $thursday_open,
			)
		);
		?>
		<label class="form-check-label"><?php esc_html_e( 'Set Opening time.', 'all-in-one-reservation' ); ?></label></div>
		<div class="field-group solcol">
		<?php
		$thursday_close = isset( $working_days['thursday']['close'] ) ? $working_days['thursday']['close'] : '';
		$aior_builder->add_field(
			array(
				'type'  => 'text',
				'name'  => 'working_days[thursday][close]',
				'class' => 'form-control timepicker h_closing_time',
				'value' => $thursday_close,
			)
		);
		?>
		<label class="form-check-label"><?php esc_html_e( 'Set Closing time.', 'all-in-one-reservation' ); ?></label></div>
	</div>
	<div class="form-group rest_row solrow" style="display: none;">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Friday', 'all-in-one-reservation' ); ?></label></div>        
		<div class="form-check solcol">
			<label class="form-check-label">
			<?php
			$friday_off = isset( $working_days['friday']['off'] ) ? $working_days['friday']['off'] : '';
			$aior_builder->add_field(
				array(
					'type'     => 'checkbox',
					'name'     => 'working_days[friday][off]',
					'class'    => 'form-check-input r_closed_days',
					'value'    => $friday_off,
					'selected' => $friday_off,
					'default'  => 1,
				)
			);
				esc_html_e( 'Is Closed', 'all-in-one-reservation' );
			?>
			</label>
		</div>
		<div class="field-group solcol">
			<?php
			$friday_open = isset( $working_days['friday']['open'] ) ? $working_days['friday']['open'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'name'  => 'working_days[friday][open]',
					'class' => 'form-control timepicker h_opening_time',
					'value' => $friday_open,
				)
			);
			?>
			<label class="form-check-label"><?php esc_html_e( 'Set Opening time.', 'all-in-one-reservation' ); ?></label>
		</div>
		<div class="field-group solcol">
			<?php
			$friday_close = isset( $working_days['friday']['close'] ) ? $working_days['friday']['close'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'name'  => 'working_days[friday][close]',
					'class' => 'form-control timepicker h_closing_time',
					'value' => $friday_close,
				)
			);
			?>
			<label class="form-check-label"><?php esc_html_e( 'Set Closing time.', 'all-in-one-reservation' ); ?></label>
		</div>
	</div>
	<div class="form-group rest_row solrow" style="display: none;">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Saturday', 'all-in-one-reservation' ); ?></label></div>        
		<div class="form-check solcol">
			<label class="form-check-label">
			<?php
			$saturday_off = isset( $working_days['saturday']['off'] ) ? $working_days['saturday']['off'] : '';
			$aior_builder->add_field(
				array(
					'type'     => 'checkbox',
					'name'     => 'working_days[saturday][off]',
					'class'    => 'form-check-input r_closed_days',
					'value'    => $saturday_off,
					'selected' => $saturday_off,
					'default'  => 1,
				)
			);
				esc_html_e( 'Is Closed', 'all-in-one-reservation' );
			?>
			</label>
		</div>
		<div class="field-group solcol">
			<?php
			$saturday_open = isset( $working_days['saturday']['open'] ) ? $working_days['saturday']['open'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'name'  => 'working_days[saturday][open]',
					'class' => 'form-control timepicker h_opening_time',
					'value' => $saturday_open,
				)
			);
			?>
			<label class="form-check-label"><?php esc_html_e( 'Set Opening time.', 'all-in-one-reservation' ); ?></label>
		</div>
		<div class="field-group solcol">
			<?php
			$saturday_close = isset( $working_days['saturday']['close'] ) ? $working_days['saturday']['close'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'name'  => 'working_days[saturday][close]',
					'class' => 'form-control timepicker h_closing_time',
					'value' => $saturday_close,
				)
			);
			?>
			<label class="form-check-label"><?php esc_html_e( 'Set Closing time.', 'all-in-one-reservation' ); ?></label>
		</div>
	</div>
	<div class="form-group rest_row solrow" style="display: none;">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Sunday', 'all-in-one-reservation' ); ?></label></div>        
		<div class="form-check solcol"><label class="form-check-label">
		<?php
		$sunday_off = isset( $working_days['sunday']['off'] ) ? $working_days['sunday']['off'] : '';
		$aior_builder->add_field(
			array(
				'type'     => 'checkbox',
				'name'     => 'working_days[sunday][off]',
				'class'    => 'form-check-input r_closed_days',
				'value'    => $sunday_off,
				'selected' => $sunday_off,
				'default'  => 1,
			)
		);
			esc_html_e( 'Is Closed', 'all-in-one-reservation' );
		?>
		</label></div>
		<div class="field-group solcol">
		<?php
		$sunday_open = isset( $working_days['sunday']['open'] ) ? $working_days['sunday']['open'] : '';
		$aior_builder->add_field(
			array(
				'type'  => 'text',
				'name'  => 'working_days[sunday][open]',
				'class' => 'form-control timepicker h_opening_time',
				'value' => $sunday_open,
			)
		);
		?>
		<label class="form-check-label"><?php esc_html_e( 'Set Opening time.', 'all-in-one-reservation' ); ?></label></div>
		<div class="field-group solcol">
		<?php
		$sunday_close = isset( $working_days['sunday']['close'] ) ? $working_days['sunday']['close'] : '';
		$aior_builder->add_field(
			array(
				'type'  => 'text',
				'name'  => 'working_days[sunday][close]',
				'class' => 'form-control timepicker h_closing_time',
				'value' => $sunday_close,
			)
		);
		?>
		<label class="form-check-label"><?php esc_html_e( 'Set Closing time.', 'all-in-one-reservation' ); ?></label></div>
	</div>
	<div class="form-group" style="display: none;">
		<label class="control-label"><?php esc_html_e( 'Display Closed Days?', 'all-in-one-reservation' ); ?></label>
		<div class="form-check"><label class="form-check-label">
		<?php
		$display_closed_days = isset( $rf_data['display_closed_days'] ) ? $rf_data['display_closed_days'] : '1';
		$aior_builder->add_field(
			array(
				'type'     => 'checkbox',
				'name'     => 'display_closed_days',
				'class'    => 'form-check-input',
				'value'    => $display_closed_days,
				'selected' => $display_closed_days,
				'default'  => 1,
			)
		);
		esc_html_e( 'Display closed days at front side.', 'all-in-one-reservation' );
		?>
		</label></div>
	</div>
		<?php
		apply_filters( 'aior_add_in_slot_settings', $rf_data );
		?>
	<div class="form-group solrow">
		<div class="aior_holiday_settings">
			<label class="control-label"><?php esc_html_e( 'Holiday Setting', 'all-in-one-reservation' ); ?></label>
			<table class="aior_holiday_table">
				<tr class="rest_tbl_holiday_head">
					<th><?php esc_html_e( 'From ', 'all-in-one-reservation' ); ?></th><th><?php esc_html_e( 'To ', 'all-in-one-reservation' ); ?></th><th><?php esc_html_e( 'Remove', 'all-in-one-reservation' ); ?></th>
				</tr>
				<tbody id="ttbody">
					<?php
					if ( isset( $rf_data ) && ! is_array( $rf_data['holiday_dates'] ) ) {
						$rdata = (string) $rf_data['holiday_dates'];
						$s1    = maybe_unserialize( $rdata );
						if ( is_string( $s1 ) ) {
							$holiday_dates = maybe_unserialize( $s1 );
						} else {
							$holiday_dates = $s1;
						}
					}
					if ( isset( $holiday_dates ) ) {
						unset( $holiday_dates['replace_this'] );
					}
					if ( ! empty( $holiday_dates ) ) {
						$i = 0;
						foreach ( $holiday_dates as $holiday ) {
							$holiday_from = $holiday['holidays_date_from'];
							$holiday_to   = $holiday['holidays_date_to'];
							?>
							<tr class="rest_row">
								<td>
								<?php
									$aior_builder->add_field(
										array(
											'type'  => 'text',
											'id'    => 'holiday_dates_' . $i . '_rest_holidays_date_from',
											'name'  => 'holiday_dates[' . $i . '][holidays_date_from]',
											'class' => 'datepick form-control rest_holidays_date_from',
											'value' => $holiday_from,
										)
									);
								?>
															   
								</td>
								<td>
								<?php
									$aior_builder->add_field(
										array(
											'type'  => 'text',
											'id'    => 'holiday_dates_' . $i . '_rest_holidays_date_to',
											'name'  => 'holiday_dates[' . $i . '][holidays_date_to]',
											'class' => 'datepick form-control rest_holidays_date_to',
											'value' => $holiday_to,
										)
									);

								?>
								</td>
								<td align="center" valign="middle" >
									<a class="rest-remove-button" data-id="aior_holiday_table" title="<?php esc_attr_e( 'Remove this holiday dates', 'all-in-one-reservation' ); ?>" href="javascript:;" style="color: #a00 !important;">
										<i class="dashicons dashicons-remove"></i>
									</a>
								</td>
							</tr>
							<?php
							$i++;
						}
					}
					?>
					<tr class="rest_clone">
						<td><input type="text" placeholder="" id="holiday_dates_replace_this_rest_holidays_date_from" class="datepick form-control rest_holidays_date_from" name="holiday_dates[replace_this][holidays_date_from]" value=""></td>
						<td><input type="text" placeholder="" id="holiday_dates_replace_this_rest_holidays_date_to" class="datepick form-control rest_holidays_date_to" name="holiday_dates[replace_this][holidays_date_to]" value=""></td>
						<td align="center" valign="middle" >
							<a class="rest-remove-button" data-id="aior_holiday_table" title="<?php esc_attr_e( 'Remove this holiday dates', 'all-in-one-reservation' ); ?>" href="javascript:;" style="color: #a00 !important;">
								<i class="dashicons dashicons-remove"></i>
							</a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>        
	</div>
	<div class="solrow">
		<button data-id="aior_holiday_table" class="rest_add_holiday_dt button button-primary button-large"><?php esc_html_e( 'Add Holidays', 'all-in-one-reservation' ); ?></button>
		<p class="description"><?php esc_html_e( 'Set date range for holidays.', 'all-in-one-reservation' ); ?></p>
	</div>
		<?php
	},
	10,
	1
);
