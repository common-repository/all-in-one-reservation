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
	'id'     => '3',
	'name'   => esc_html__( 'Email Notification', 'all-in-one-reservation' ),
	'title'  => esc_html__( 'Email Notification', 'all-in-one-reservation' ),
	'desc'   => esc_html__( 'Email & Notification settings.', 'all-in-one-reservation' ),
	'filter' => 'aior_add_in_notification_settings',
);
self::create_rs_tab( $nav_args );
add_filter(
	'aior_add_in_notification_settings',
	function () {
		$aior_builder = new Aior_Builder();
		$rf_data      = Aior_Reservation_Form::get_settings();
		$content_1    = 'Thank you <strong>{first_name},</strong><br><br>We have successfully received your reservation request <strong>#{reservation_id}</strong>. Your booking is awaiting to be confirmed from site administrator. We will send you updates to the email address you provided.';
		$content_2    = 'Hi {site_name},<br><br>We have one new reservation request with reservation id <strong>#{reservation_id}</strong>.<br><h2>Reservation Details</h2><br>---------------------------------<br><strong>Reservation Date</strong> : {reservation_date}<br><strong>Reservation Time</strong> : {reservation_time_from} to {reservation_time_to}<br><strong>Number of Guests</strong> : {reservation_time_to}<br><strong>Phone No.</strong> : {reservation_phone_no}<br><strong>Name</strong> : {first_name} {last_name}<br><strong>Email</strong> : {reservation_user_email}<br><strong>Note</strong> : {reservation_user_note}<br><br>Thanks,<br>{site_name}';
		$content_3    = 'Hi {first_name},<br><br>We have received your reservation request <strong>#{reservation_id}</strong>.<br><br><h2>Your Reservation Details</h2><br>---------------------------------<br><strong>Reservation Date</strong> : {reservation_date}<br><strong>Reservation Time</strong> : {reservation_time_from} to {reservation_time_to}<br><strong>Number of Guests</strong> : {reservation_number_guest}<br><strong>Phone No</strong> : {reservation_phone_no}<br><br>{site_name} administrator will check your request asap and once it confirmed from our side then you will get your reservation confirmation via email from us.<br><br>Thanks,<br>{site_name}';
		$content_4    = 'Hi <strong>{first_name}</strong>,<br><br>We have confirmed your reservation <strong>#{reservation_id}</strong>.<br><h2>Your Reservation Details</h2><br>---------------------------------<br><strong>Reservation Date</strong> : {reservation_date}<br><strong>Reservation Time</strong> : {reservation_time_from} to {reservation_time_to}<br><strong>Number of Guests</strong> : {reservation_number_guest}<br><strong>Phone No.</strong> : {reservation_phone_no}<br>If you are willing to cancel this reservation then please click on below link.<br><strong>{cancel_link}</strong><br><br>Thanks,<br>{site_name}';
		$content_5    = 'Hi <strong>{first_name}</strong>,<br><br>We have cancelled your reservation with reservation <strong>#{reservation_id}</strong>.<br><h2>Your Reservation Details</h2><br>---------------------------------<br><strong>Reservation Date</strong> : {reservation_date}<br><strong>Reservation Time</strong> : {reservation_time_from} to {reservation_time_to}<br><strong>Number of Guests</strong> : {reservation_number_guest}<br><strong>Phone No.</strong> : {reservation_phone_no}<br>If you are willing to add new reservation then please go through below link<br><strong>{reservation_link}</strong><br><br>Thanks,<br>{site_name}';
		?>
	<h3><?php esc_html_e( 'Message Notifications', 'all-in-one-reservation' ); ?></h3>
	<div class="form-group solrow">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Success Message', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
			<?php
			$success_message = isset( $rf_data['notif_success_message'] ) && ! empty( $rf_data['notif_success_message'] ) ? $rf_data['notif_success_message'] : $content_1;
			$settings        = array(
				'wpautop'       => true,
				'media_buttons' => false,
				'textarea_name' => 'notif_success_message',
				'textarea_rows' => 10,
				'tabindex'      => '',
				'editor_css'    => '',
				'editor_class'  => '',
				'teeny'         => false,
				'dfw'           => false,
				'tinymce'       => true,
				'quicktags'     => true,
			);
			$aior_builder->add_field(
				array(
					'type'     => 'wp_editor',
					'id'       => 'notif_success_message',
					'value'    => $success_message,
					'settings' => $settings,

				)
			);
			?>
			<small><?php esc_html_e( 'Enter the message to display when a reservation request is made.', 'all-in-one-reservation' ); ?><br><?php esc_html_e( 'Use', 'all-in-one-reservation' ); ?> {first_name},{last_name},{reservation_id} <?php esc_html_e( 'tags for message.', 'all-in-one-reservation' ); ?></small>
		</div>
	</div>

	<h3><?php esc_html_e( 'Email Notifications', 'all-in-one-reservation' ); ?></h3>
	<div class="form-group solrow aior_enable_email">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Admin Email Notification', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$enable_admin_notification = isset( $rf_data['enable_admin_notification'] ) ? $rf_data['enable_admin_notification'] : 1;
			$aior_builder->add_field(
				array(
					'type'     => 'checkbox',
					'name'     => 'enable_admin_notification',
					'id'       => 'enable_admin_notification',
					'value'    => $enable_admin_notification,
					'selected' => 1,
					'default'  => 1,
				)
			);
		?>
		<span><?php esc_html_e( 'Enable/Disable Email Notification', 'all-in-one-reservation' ); ?></span>
		<small><?php esc_html_e( 'Notify to admin on new reservation arrives from frontend reservation form.', 'all-in-one-reservation' ); ?></small>
		</div>
	</div>
	<div class="form-group solrow admin_email_id">
		<div class="solcol"><label class="control-label"><?php esc_html_e( "Admin's Email Id", 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$admin_email = isset( $rf_data['admin_email'] ) && ! empty( $rf_data['admin_email'] ) ? $rf_data['admin_email'] : get_option( 'admin_email' );
			$aior_builder->add_field(
				array(
					'type'     => 'email',
					'name'     => 'admin_email',
					'value'    => $admin_email,
					'required' => true,
				)
			);
		?>
		</div>
	</div>
	<div class="form-group solrow admin_email_cc_id">
		<div class="solcol"><label class="control-label"><?php esc_html_e( "Admin's CC Email Id", 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$admin_cc_email = isset( $rf_data['admin_cc_email'] ) ? $rf_data['admin_cc_email'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'email',
					'name'  => 'admin_cc_email',
					'value' => $admin_cc_email,
				)
			);
		?>
		</div>
	</div>
	<h3><?php esc_html_e( 'Email Shortcode Tags', 'all-in-one-reservation' ); ?></h3>
	<div class="solrow">
		<div class="solcol" style="width: 100%">
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

	<h3><?php esc_html_e( 'Reservation Request Email to Admin', 'all-in-one-reservation' ); ?></h3>
	<small><?php esc_html_e( 'This email send to site administrator when new reservation occurs from frontend reservation form.', 'all-in-one-reservation' ); ?></small>
	<div class="form-group solrow email_request">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Subject', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$admin_subject = isset( $rf_data['admin_subject'] ) && ! empty( $rf_data['admin_subject'] ) ? $rf_data['admin_subject'] : 'New Reservation arrived  #{reservation_id}';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'name'  => 'admin_subject',
					'value' => $admin_subject,
				)
			);
		?>
		</div>
	</div>
	<div class="form-group solrow">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Email Body', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$admin_email_body = isset( $rf_data['admin_email_body'] ) && ! empty( $rf_data['admin_email_body'] ) ? $rf_data['admin_email_body'] : $content_2;
			$settings         = array(
				'wpautop'       => true,
				'media_buttons' => false,
				'textarea_name' => 'admin_email_body',
				'textarea_rows' => 10,
				'tabindex'      => '',
				'editor_css'    => '',
				'editor_class'  => '',
				'teeny'         => false,
				'dfw'           => false,
				'tinymce'       => true,
				'quicktags'     => true,
			);
			$aior_builder->add_field(
				array(
					'type'     => 'wp_editor',
					'id'       => 'admin_email_body',
					'value'    => $admin_email_body,
					'settings' => $settings,
				)
			);
		?>
		</div>
	</div>

	<h3><?php esc_html_e( 'Reservation Request Confirmation Email to Client', 'all-in-one-reservation' ); ?></h3>
	<small><?php esc_html_e( 'This email send to user when reservation made by user.', 'all-in-one-reservation' ); ?></small>
	<div class="form-group solrow email_request">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Subject', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$client_subject = isset( $rf_data['client_subject'] ) && ! empty( $rf_data['client_subject'] ) ? $rf_data['client_subject'] : '{site_name} - Reservation Request Received #{reservation_id}';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'name'  => 'client_subject',
					'value' => $client_subject,
				)
			);
		?>
		</div>
	</div>
	<div class="form-group solrow">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Email Body', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$client_email_body = isset( $rf_data['client_email_body'] ) && ! empty( $rf_data['client_email_body'] ) ? $rf_data['client_email_body'] : $content_3;
			$settings          = array(
				'wpautop'       => true,
				'media_buttons' => false,
				'textarea_name' => 'client_email_body',
				'textarea_rows' => 10,
				'tabindex'      => '',
				'editor_css'    => '',
				'editor_class'  => '',
				'teeny'         => false,
				'dfw'           => false,
				'tinymce'       => true,
				'quicktags'     => true,
			);
			$aior_builder->add_field(
				array(
					'type'     => 'wp_editor',
					'id'       => 'client_email_body',
					'value'    => $client_email_body,
					'settings' => $settings,
				)
			);
		?>
		</div>
	</div>


	<h3><?php esc_html_e( 'Reservation Confirmation Email to Client', 'all-in-one-reservation' ); ?></h3>
	<small><?php esc_html_e( 'This email send to user when site administrator confirm the reservation.', 'all-in-one-reservation' ); ?></small>
	<div class="form-group solrow email_request">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Subject', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$confirm_subject = isset( $rf_data['confirm_subject'] ) && ! empty( $rf_data['confirm_subject'] ) ? $rf_data['confirm_subject'] : '{site_name} -Reservation Request Confirmed #{reservation_id}';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'name'  => 'confirm_subject',
					'value' => $confirm_subject,
				)
			);
		?>
		</div>
	</div>
	<div class="form-group solrow">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Email Body', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$confirm_email_body = isset( $rf_data['confirm_email_body'] ) && ! empty( $rf_data['confirm_email_body'] ) ? $rf_data['confirm_email_body'] : $content_4;
			$settings           = array(
				'wpautop'       => true,
				'media_buttons' => false,
				'textarea_name' => 'confirm_email_body',
				'textarea_rows' => 10,
				'tabindex'      => '',
				'editor_css'    => '',
				'editor_class'  => '',
				'teeny'         => false,
				'dfw'           => false,
				'tinymce'       => true,
				'quicktags'     => true,
			);
			$aior_builder->add_field(
				array(
					'type'     => 'wp_editor',
					'id'       => 'confirm_email_body',
					'value'    => $confirm_email_body,
					'settings' => $settings,
				)
			);
		?>
		</div>
	</div>

	<h3><?php esc_html_e( 'Reservation Cancellation Email to Client', 'all-in-one-reservation' ); ?></h3>
	<small><?php esc_html_e( 'This email send to user when site administrator cancel the reservation.', 'all-in-one-reservation' ); ?></small>
	<div class="form-group solrow email_request">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Subject', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$cancel_subject = isset( $rf_data['cancel_subject'] ) && ! empty( $rf_data['cancel_subject'] ) ? $rf_data['cancel_subject'] : '{site_name} - Reservation Request Cancelled #{reservation_id}';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'name'  => 'cancel_subject',
					'value' => $cancel_subject,
				)
			);
		?>
		</div>
	</div>
	<div class="form-group solrow">
		<div class="solcol"><label class="control-label"><?php esc_html_e( 'Email Body', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol">
		<?php
			$cancel_email_body = isset( $rf_data['cancel_email_body'] ) && ! empty( $rf_data['cancel_email_body'] ) ? $rf_data['cancel_email_body'] : $content_5;
			$settings          = array(
				'wpautop'       => true,
				'media_buttons' => false,
				'textarea_name' => 'cancel_email_body',
				'textarea_rows' => 10,
				'tabindex'      => '',
				'editor_css'    => '',
				'editor_class'  => '',
				'teeny'         => false,
				'dfw'           => false,
				'tinymce'       => true,
				'quicktags'     => true,
			);
			$aior_builder->add_field(
				array(
					'type'     => 'wp_editor',
					'id'       => 'cancel_email_body',
					'value'    => $cancel_email_body,
					'settings' => $settings,
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
