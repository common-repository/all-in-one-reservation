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
	'id'     => '1',
	'name'   => esc_html__( 'General', 'all-in-one-reservation' ),
	'title'  => esc_html__( 'General Settings', 'all-in-one-reservation' ),
	'desc'   => esc_html__( 'Time, Format, slot etc. settings', 'all-in-one-reservation' ),
	'filter' => 'aior_add_in_general_settings',
);
self::create_rs_tab( $nav_args );
add_filter(
	'aior_add_in_general_settings',
	function () {
		$rf_data      = Aior_Reservation_Form::get_settings();
		$aior_builder = new Aior_Builder();
		?>
	<div class="form-group">
		<label class="control-label"><?php esc_html_e( 'Layout Type', 'all-in-one-reservation' ); ?></label>
		<?php
			$layout_type = isset( $rf_data['layout_type'] ) ? $rf_data['layout_type'] : '';
			$aior_builder->add_field(
				array(
					'type'   => 'select',
					'name'   => 'layout_type',
					'class'  => 'form-control aior_layout_type',
					'option' => array(
						'Design 1' => 'design1',
						'Design 2' => 'design2',
					),
					'value'  => $layout_type,
				)
			);
		?>
		<small><?php esc_html_e( 'Set the layout type as per requirement.', 'all-in-one-reservation' ); ?></small>
	</div>
		<?php wp_nonce_field( 'res_form_nonce_action', 'res_form_nonce' ); ?>
	<div class="form-group">
		<label class="control-label"><?php esc_html_e( 'Booking Type', 'all-in-one-reservation' ); ?></label>
		<?php
			$booking_type = isset( $rf_data['booking_type'] ) ? $rf_data['booking_type'] : '';
			$aior_builder->add_field(
				array(
					'type'   => 'select',
					'name'   => 'booking_type',
					'class'  => 'form-control aior_booking_type',
					'option' => array(
						'Registered Booking' => 'registered',
						'Guest Booking'      => 'guest',
					),
					'value'  => $booking_type,
				)
			);
		?>
		<small><?php esc_html_e( 'Set the booking type as per requirement.', 'all-in-one-reservation' ); ?></small>
	</div>
	<div class="form-group">
		<label class="control-label"><?php esc_html_e( 'Clock Hours', 'all-in-one-reservation' ); ?></label>
		<?php
			$clock_hours = isset( $rf_data['clock_hours'] ) ? $rf_data['clock_hours'] : '';
			$aior_builder->add_field(
				array(
					'type'   => 'select',
					'name'   => 'clock_hours',
					'class'  => 'form-control aior_clock_hours',
					'option' => array(
						'12 Hours' => 'hh:mm p',
						'24 Hours' => 'HH:mm',
					),
					'value'  => $clock_hours,
				)
			);
		?>
		<small><?php esc_html_e( 'Set time format to show in front end side.', 'all-in-one-reservation' ); ?></small>
	</div>
	<div class="form-group solrow" style="display: none;">
		<div class="solcol">
			<label class="control-label"><?php esc_html_e( 'Opening Time', 'all-in-one-reservation' ); ?></label>
			<?php
				$opening_time = isset( $rf_data['opening_time'] ) ? $rf_data['opening_time'] : '';
				$aior_builder->add_field(
					array(
						'type'  => 'text',
						'name'  => 'opening_time',
						'class' => 'form-control timepicker aior_opening_time',
						'value' => $opening_time,
					)
				);
			?>
			<small><?php esc_html_e( 'Set Opening time.', 'all-in-one-reservation' ); ?></small>
		</div>
		<div class="solcol">
			<label class="control-label"><?php esc_html_e( 'Closing Time', 'all-in-one-reservation' ); ?></label>
			<?php
				$closing_time = isset( $rf_data['closing_time'] ) ? $rf_data['closing_time'] : '';
				$aior_builder->add_field(
					array(
						'type'  => 'text',
						'name'  => 'closing_time',
						'class' => 'form-control timepicker aior_closing_time',
						'value' => $closing_time,
					)
				);
			?>
			<small><?php esc_html_e( 'Set closing time.', 'all-in-one-reservation' ); ?></small>
		</div>
	</div>
	<div class="form-group"><label class="control-label"><?php esc_html_e( 'Time Slot (Interval)', 'all-in-one-reservation' ); ?></label>
		<?php
			$time_slot = isset( $rf_data['time_slot'] ) ? $rf_data['time_slot'] : '';
			$aior_builder->add_field(
				array(
					'type'        => 'number',
					'name'        => 'time_slot',
					'class'       => 'form-control aior_time_slot',
					'value'       => $time_slot,
					'default'     => 30,
					'min'         => 1,
					'placeholder' => 30,
				)
			);
		?>
		<small><?php esc_html_e( 'Set time slot ,set it in minutes.', 'all-in-one-reservation' ); ?></small>
	</div>
	<div class="form-group" style="display: none;">
		<label class="control-label"><?php esc_html_e( 'Maximum Guest Number', 'all-in-one-reservation' ); ?></label>
		<?php
			$max_guest = isset( $rf_data['max_guest'] ) ? $rf_data['max_guest'] : '';
			$aior_builder->add_field(
				array(
					'type'        => 'number',
					'name'        => 'max_guest',
					'class'       => 'form-control',
					'value'       => $max_guest,
					'default'     => 10,
					'min'         => 1,
					'placeholder' => 10,
				)
			);
		?>
		<small><?php esc_html_e( 'Set maximum guest number.', 'all-in-one-reservation' ); ?></small>
	</div>
	<div class="solrow" style="display: none;">
		<div class="form-group solcol"><label class="control-label"><?php esc_html_e( 'Number of Days', 'all-in-one-reservation' ); ?></label>
			<?php
				$max_end_days = isset( $rf_data['max_end_days'] ) ? $rf_data['max_end_days'] : '';
				$aior_builder->add_field(
					array(
						'type'        => 'number',
						'name'        => 'max_end_days',
						'class'       => 'form-control',
						'value'       => $max_end_days,
						'min'         => 1,
						'placeholder' => 45,
					)
				);
			?>
			<small><?php esc_html_e( 'Set number of days that guest can make reservation before it.', 'all-in-one-reservation' ); ?></small>
			</div>
		<div class="form-group solcol"><label class="control-label"><?php esc_html_e( 'Included Today', 'all-in-one-reservation' ); ?></label>
			<div class="form-check"><label class="form-check-label">
				<?php
					$include_today = isset( $rf_data['include_today'] ) ? $rf_data['include_today'] : '';

					$aior_builder->add_field(
						array(
							'type'     => 'checkbox',
							'name'     => 'include_today',
							'class'    => 'form-check-input',
							'value'    => $include_today,
							'selected' => $include_today,
							'option'   => array(
								esc_html__( 'Included Today', 'all-in-one-reservation' ) => 1,
							),
							'default'  => 1,
						)
					);
				?>
				</label></div>
		</div>
	</div>
	<div class="form-group"><label class="control-label"><?php esc_html_e( 'Date Format', 'all-in-one-reservation' ); ?></label>
		<?php
			$date_format = isset( $rf_data['date_format'] ) ? $rf_data['date_format'] : '';
			$aior_builder->add_field(
				array(
					'type'   => 'select',
					'name'   => 'date_format',
					'class'  => 'form-control aior_date_format',
					'value'  => $date_format,
					'option' => array(
						esc_html__( 'Select Date Format', 'all-in-one-reservation' ) => '',
						'm/d/yy'        => 'm/d/yy',
						'dd/mm/yy'      => 'dd/mm/yy',
						'm-d-yy'        => 'm-d-yy',
						'm-d-y'         => 'm-d-y',
						'mm-dd-yy'      => 'mm-dd-yy',
						'd M, y'        => 'd M, y',
						'd MM, yy'      => 'd MM, yy',
						'D, dd MM, yy'  => 'D, dd MM, yy',
						'D, dd MM, y'   => 'D, dd MM, y',
						'D, d M, yy'    => 'D, d M, yy',
						'DD, dd MM, yy' => 'DD, dd MM, yy',
					),
				)
			);
		?>
		<small><?php esc_html_e( 'Set date format to show in front end side.', 'all-in-one-reservation' ); ?></small>
	</div>
	<div class="form-group"><label class="control-label"><?php esc_html_e( 'Reservation Page', 'all-in-one-reservation' ); ?></label>
		<?php
			$site_pages_arr    = self::get_page_list_arr();
			$site_page_default = array( esc_html__( 'Select Page', 'all-in-one-reservation' ) => '' );
			$site_pages        = array_merge( $site_page_default, $site_pages_arr );
			$front_page        = isset( $rf_data['front_page'] ) && ! empty( $rf_data['front_page'] ) ? $rf_data['front_page'] : get_option( 'aior_pay' );
			$aior_builder->add_field(
				array(
					'type'    => 'select',
					'name'    => 'front_page',
					'class'   => 'form-control',
					'value'   => $front_page,
					'option'  => $site_pages,
					'defualt' => $front_page,
				)
			);
		?>
		<small><?php esc_html_e( 'Selected page must contain', 'all-in-one-reservation' ); ?> <b>[aior_pay]</b> <?php esc_html_e( 'shortcode to display reservation form.', 'all-in-one-reservation' ); ?></small>
	</div>
	<div class="form-group"><label class="control-label"><?php esc_html_e( 'Cancellation Page', 'all-in-one-reservation' ); ?></label>
		<?php
			$cancel_page = isset( $rf_data['cancel_page'] ) && ! empty( $rf_data['cancel_page'] ) ? $rf_data['cancel_page'] : get_option( 'aior_cancel' );
			$aior_builder->add_field(
				array(
					'type'    => 'select',
					'name'    => 'cancel_page',
					'class'   => 'form-control',
					'value'   => $cancel_page,
					'option'  => $site_pages,
					'defualt' => $cancel_page,
				)
			);
		?>
		<small><?php esc_html_e( 'Selected page must contain', 'all-in-one-reservation' ); ?> <b>[aior_cancel_page]</b> <?php esc_html_e( 'shortcode to display cancel reservation form.', 'all-in-one-reservation' ); ?></small>
	</div>
	<div class="form-group"><label class="control-label"><?php esc_html_e( 'Payment Success Page', 'all-in-one-reservation' ); ?></label>
		<?php
			$payment_success = isset( $rf_data['payment_success'] ) && ! empty( $rf_data['payment_success'] ) ? $rf_data['payment_success'] : get_option( 'aior_payment_success' );
			$aior_builder->add_field(
				array(
					'type'    => 'select',
					'name'    => 'payment_success',
					'class'   => 'form-control',
					'value'   => $payment_success,
					'option'  => $site_pages,
					'defualt' => $payment_success,
				)
			);
		?>
		<small><?php esc_html_e( 'Selected page must contain', 'all-in-one-reservation' ); ?> <b>[aior_pay_success]</b> <?php esc_html_e( 'shortcode to display success payment page.', 'all-in-one-reservation' ); ?></small>
	</div>
	<div class="form-group">
		<label class="control-label"><?php esc_html_e( 'New Booking Action', 'all-in-one-reservation' ); ?></label>
		<?php
			$appointment_action = isset( $rf_data['appointment_action'] ) ? $rf_data['appointment_action'] : '';
			$aior_builder->add_field(
				array(
					'type'   => 'select',
					'name'   => 'appointment_action',
					'class'  => 'form-control',
					'value'  => $appointment_action,
					'option' => array(
						esc_html__( 'Pending', 'all-in-one-reservation' )  => '1',
						esc_html__( 'Approved', 'all-in-one-reservation' ) => '2',
					),
				)
			);
		?>
	</div>
	<div class="form-group"><label class="control-label"><?php esc_html_e( 'Prevent Appointments Before Date', 'all-in-one-reservation' ); ?></label>
		<?php
			$prevent_before_date = isset( $rf_data['prevent_before_date'] ) ? $rf_data['prevent_before_date'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'date',
					'name'  => 'prevent_before_date',
					'class' => 'form-control',
					'value' => $prevent_before_date,
				)
			);
		?>
		<small><?php esc_html_e( 'To prevent appointments from getting booked before a certain date, you can choose that date below.', 'all-in-one-reservation' ); ?></small>
	</div>
	<div class="form-group"><label class="control-label"><?php esc_html_e( 'Prevent Appointments After Date', 'all-in-one-reservation' ); ?></label>
		<?php
			$prevent_after_date = isset( $rf_data['prevent_after_date'] ) ? $rf_data['prevent_after_date'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'date',
					'name'  => 'prevent_after_date',
					'class' => 'form-control',
					'value' => $prevent_after_date,
				)
			);
		?>
		<small><?php esc_html_e( 'To prevent appointments from getting booked after a certain date, you can choose that date below.', 'all-in-one-reservation' ); ?></small>
	</div>
	<div class="form-group"><label class="control-label"><?php esc_html_e( 'Appointment Limit', 'all-in-one-reservation' ); ?></label>
		<?php
			$appointment_limit = isset( $rf_data['appointment_limit'] ) ? $rf_data['appointment_limit'] : 0;
			$aior_builder->add_field(
				array(
					'type'    => 'number',
					'name'    => 'appointment_limit',
					'class'   => 'form-control',
					'value'   => $appointment_limit,
					'default' => 0,
					'min'     => 0,
				)
			);
		?>
		<small><?php esc_html_e( 'Set the appointment limit for the users.', 'all-in-one-reservation' ); ?></small>
	</div>
		<?php
		$is_payment_gateway = Aior_Admin::is_payment_gateway();
		if ( ! empty( $is_payment_gateway ) ) {
			?>
			<div class="form-group"><label class="control-label"><?php esc_html_e( 'Enable Payment', 'all-in-one-reservation' ); ?></label>
			<?php
				$enable_payment = isset( $rf_data['enable_payment'] ) ? $rf_data['enable_payment'] : '0';
				$aior_builder->add_field(
					array(
						'type'   => 'select',
						'name'   => 'enable_payment',
						'id'     => 'enable_payment',
						'class'  => 'form-control',
						'value'  => $enable_payment,
						'option' => array(
							esc_html__( 'Disable', 'all-in-one-reservation' ) => '0',
							esc_html__( 'Enable', 'all-in-one-reservation' ) => '1',
						),
					)
				);
			?>
			</div>
			<div class="form-group payment-gateways"><label class="control-label"><?php esc_html_e( 'Payment Gateway', 'all-in-one-reservation' ); ?></label>
				<?php
				$payment_gateway = isset( $rf_data['payment_gateway'] ) ? $rf_data['payment_gateway'] : '';
				$aior_builder->add_field(
					array(
						'type'   => 'checkbox',
						'name'   => 'payment_gateway',
						'id'     => 'payment_gateway',
						'class'  => 'form-control',
						'value'  => $payment_gateway,
						'option' => $is_payment_gateway,
					)
				);
				?>
			</div>
				<?php
		}
		?>
	<div class="form-group"><label class="control-label"><?php esc_html_e( 'Comment', 'all-in-one-reservation' ); ?></label>
		<?php
			$enable_comment = isset( $rf_data['enable_comment'] ) ? $rf_data['enable_comment'] : '0';
			$aior_builder->add_field(
				array(
					'type'   => 'select',
					'name'   => 'enable_comment',
					'class'  => 'form-control',
					'value'  => $enable_comment,
					'option' => array(
						esc_html__( 'Disable', 'all-in-one-reservation' ) => 0,
						esc_html__( 'Enable', 'all-in-one-reservation' )  => 1,
					),
				)
			);
		?>
	</div>
	<div class="form-group"><label class="control-label"><?php esc_html_e( 'Social Share', 'all-in-one-reservation' ); ?></label>
		<?php
			$enable_social_share = isset( $rf_data['enable_social_share'] ) ? $rf_data['enable_social_share'] : '0';
			$aior_builder->add_field(
				array(
					'type'   => 'select',
					'name'   => 'enable_social_share',
					'class'  => 'form-control',
					'id'     => 'enable_social_share',
					'value'  => $enable_social_share,
					'option' => array(
						esc_html__( 'Disable', 'all-in-one-reservation' ) => 0,
						esc_html__( 'Enable', 'all-in-one-reservation' )  => 1,
					),
				)
			);
		?>
	</div>
		<?php
		if ( $enable_social_share && 1 == $enable_social_share ) {
			?>
		<div class="form-group social_share_icons"><label class="control-label"><?php esc_html_e( 'Show Social Share Icons', 'all-in-one-reservation' ); ?></label>
			<?php
			$show_social_share_icon = isset( $rf_data['show_social_share_icon'] ) ? $rf_data['show_social_share_icon'] : '';
			$aior_builder->add_field(
				array(
					'type'   => 'checkbox',
					'name'   => 'show_social_share_icon',
					'class'  => 'form-control',
					'value'  => $show_social_share_icon,
					'option' => array(
						esc_html__( 'Facebook', 'all-in-one-reservation' )   => 1,
						esc_html__( 'Twitter', 'all-in-one-reservation' )    => 2,
						esc_html__( 'LinkedIn', 'all-in-one-reservation' )   => 4,
						esc_html__( 'Reddit', 'all-in-one-reservation' )     => 5,
						esc_html__( 'Digg', 'all-in-one-reservation' )       => 6,
						esc_html__( 'Tumblr', 'all-in-one-reservation' )     => 7,
						esc_html__( 'StubleUpon', 'all-in-one-reservation' ) => 8,
						esc_html__( 'Delicious', 'all-in-one-reservation' )  => 9,
						esc_html__( 'Evernote', 'all-in-one-reservation' )   => 10,
						esc_html__( 'WordPress', 'all-in-one-reservation' )  => 11,
						esc_html__( 'Pocket', 'all-in-one-reservation' )     => 12,
						esc_html__( 'Pinterest', 'all-in-one-reservation' )  => 13,

					),
				)
			);
			?>
		</div>
			<?php
		}
		?>
		<?php
	},
	10,
	1
);
