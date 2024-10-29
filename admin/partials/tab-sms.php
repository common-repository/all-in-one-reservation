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
	'id'     => '31',
	'name'   => esc_html__( 'SMS Notification', 'all-in-one-reservation' ),
	'title'  => esc_html__( 'SMS Notification', 'all-in-one-reservation' ),
	'desc'   => esc_html__( 'SMS Notification settings.', 'all-in-one-reservation' ),
	'filter' => 'aior_add_in_sms_settings',
);
self::create_rs_tab( $nav_args );
add_filter(
	'aior_add_in_sms_settings',
	function () {
		$aior_builder = new Aior_Builder();
		$rf_data      = Aior_Reservation_Form::get_settings();
		$content_1    = 'We have successfully received your reservation request #{reservation_id}, Date: {reservation_date}, Time: {reservation_time_from}-{reservation_time_to}, Seat: {reservation_number_guest}, admin will check your request asap.';
		$content_2    = 'New reservation request #{reservation_id}, Date: {reservation_date}, Time : {reservation_time_from}-{reservation_time_to}';
		$content_3    = 'Reservation request received #{reservation_id}, Date:{reservation_date}, Time: {reservation_time_from}-{reservation_time_to}, Seat: {reservation_number_guest}, admin will check your request asap.';
		$content_4    = 'Your reservation #{reservation_id} is confirmed, Date:{reservation_date},Time: {reservation_time_from}-{reservation_time_to},Guests:{reservation_number_guest}';
		$content_5    = 'Your reservation #{reservation_id} is cancelled';
		?>
	<h3><?php esc_html_e( 'Message Notifications', 'all-in-one-reservation' ); ?></h3>
	<div class="form-group solrow">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Success Message', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol success_msg">
			<?php
			$sms_success_message = isset( $rf_data['sms_success_message'] ) && ! empty( $rf_data['sms_success_message'] ) ? $rf_data['sms_success_message'] : $content_1;
			$aior_builder->add_field(
				array(
					'type'  => 'textarea',
					'id'    => 'sms_success_message',
					'value' => $sms_success_message,
					'max'   => 260,
					'name'  => 'sms_success_message',
				)
			);
			?>

		   
			<small><?php esc_html_e( 'Enter the message to display when a reservation request is made.', 'all-in-one-reservation' ); ?><br><?php esc_html_e( 'Use', 'all-in-one-reservation' ); ?> {first_name},{last_name},{reservation_id} <?php esc_html_e( 'tags for message.', 'all-in-one-reservation' ); ?></small>
		</div>
	</div>

	<h3><?php esc_html_e( 'SMS Notifications', 'all-in-one-reservation' ); ?></h3>
	<div class="form-group solrow">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Admin SMS Notification', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$enable_admin_sms = isset( $rf_data['enable_admin_sms'] ) ? $rf_data['enable_admin_sms'] : '1';
			$aior_builder->add_field(
				array(
					'type'     => 'checkbox',
					'name'     => 'enable_admin_sms',
					'id'       => 'enable_admin_sms',
					'value'    => $enable_admin_sms,
					'selected' => 1,
				)
			);
		?>
		<span><?php esc_html_e( 'Enable/Disable SMS Notification', 'all-in-one-reservation' ); ?></span>
		<small><?php esc_html_e( 'Notify to admin on new reservation arrives from frontend reservation form.', 'all-in-one-reservation' ); ?></small>
		</div>
	</div>
	<div class="form-group solrow admin_mobile_no">
		<div class="solcol"><label class="control-label"><?php esc_html_e( "Admin's Mobile Number", 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$admin_mobile = isset( $rf_data['admin_mobile'] ) && ! empty( $rf_data['admin_mobile'] ) ? $rf_data['admin_mobile'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'name'  => 'admin_mobile',
					'value' => $admin_mobile,
					'id'    => 'admin_mobile',
				)
			);
		?>
		</div>
	</div>
	<h3><?php esc_html_e( 'SMS Shortcode Tags', 'all-in-one-reservation' ); ?></h3>
	<div class="solrow">
		<div class="solcol" style="width:100%">
			<div class="sol_tag_cnt">
				<div class="sol_tag_wrap">
					<span class="tagname">{reservation_id}</span><span class="tagdesk"><?php esc_html_e( 'Reservation id', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{first_name}</span> <span class="tagdesk"><?php esc_html_e( 'User first name', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{last_name}</span> <span class="tagdesk"><?php esc_html_e( 'User last name', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{reservation_date}</span> <span class="tagdesk"><?php esc_html_e( 'Reservation Date', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{reservation_time_from}</span> <span class="tagdesk"><?php esc_html_e( 'Reservation start time', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{reservation_time_to}</span> <span class="tagdesk"><?php esc_html_e( 'Reservation end time', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{reservation_number_guest}</span> <span class="tagdesk"><?php esc_html_e( 'Number of people reserved', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{reservation_phone_no}</span> <span class="tagdesk"><?php esc_html_e( 'Phone number of user', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{reservation_user_email}</span> <span class="tagdesk"><?php esc_html_e( 'Email address of user', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{reservation_user_note}</span> <span class="tagdesk"><?php esc_html_e( 'Reservation note of user', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{cancel_link}</span> <span class="tagdesk"><?php esc_html_e( 'Reservation cancel page link', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{reservation_link}</span> <span class="tagdesk"><?php esc_html_e( 'Reservation page link', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{site_name}</span><span class="tagdesk"><?php esc_html_e( 'Website name', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{home_url}</span><span class="tagdesk"><?php esc_html_e( 'Link of website', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{date}</span><span class="tagdesk"><?php esc_html_e( 'Current date', 'all-in-one-reservation' ); ?></span>
				</div>
				<div class="sol_tag_wrap">
					<span class="tagname">{time}</span><span class="tagdesk"><?php esc_html_e( 'Current time', 'all-in-one-reservation' ); ?></span>
				</div>
			</div>
		</div>
	</div>
	<h3><?php esc_html_e( 'Reservation Request SMS to Admin', 'all-in-one-reservation' ); ?></h3>
	<small><?php esc_html_e( 'This sms send to site administrator when new reservation occurs from frontend reservation form.', 'all-in-one-reservation' ); ?></small>
	<div class="form-group solrow">
		<div class="solcol">
			<label class="control-label"><?php esc_html_e( 'SMS Text', 'all-in-one-reservation' ); ?></label></div>
			<div class="solcol">
				<?php
					$admin_sms_body = isset( $rf_data['admin_sms_body'] ) && ! empty( $rf_data['admin_sms_body'] ) ? $rf_data['admin_sms_body'] : $content_2;
					$aior_builder->add_field(
						array(
							'type'  => 'textarea',
							'id'    => 'admin_sms_body',
							'value' => $admin_sms_body,
							'max'   => 140,
							'name'  => 'admin_sms_body',
						)
					);
				?>
			</div>
		</div>

	<h3><?php esc_html_e( 'Reservation Confirmation SMS to Client', 'all-in-one-reservation' ); ?></h3>
	<small><?php esc_html_e( 'This SMS send to user when site administrator confirm the reservation.', 'all-in-one-reservation' ); ?></small>
   
	<div class="form-group solrow">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'SMS Text', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$confirm_sms_body = isset( $rf_data['confirm_sms_body'] ) && ! empty( $rf_data['confirm_sms_body'] ) ? $rf_data['confirm_sms_body'] : $content_4;
			$aior_builder->add_field(
				array(
					'type'  => 'textarea',
					'id'    => 'confirm_sms_body',
					'value' => $confirm_sms_body,
					'max'   => 170,
					'name'  => 'confirm_sms_body',
				)
			);
		?>
		</div>
	</div>

	<h3><?php esc_html_e( 'Reservation Cancellation SMS to Client', 'all-in-one-reservation' ); ?></h3>
	<small><?php esc_html_e( 'This SMS send to user when site administrator cancel the reservation.', 'all-in-one-reservation' ); ?></small>
	<div class="form-group solrow">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'SMS Text', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$cancel_sms_body = isset( $rf_data['cancel_sms_body'] ) && ! empty( $rf_data['cancel_sms_body'] ) ? $rf_data['cancel_sms_body'] : $content_5;
			$aior_builder->add_field(
				array(
					'type'  => 'textarea',
					'id'    => 'cancel_sms_body',
					'value' => $cancel_sms_body,
					'max'   => 140,
					'name'  => 'cancel_sms_body',
				)
			);
		?>
		</div>
	</div>
		<?php
	},
	10,
	1
);
