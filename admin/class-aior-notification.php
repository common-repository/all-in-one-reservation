<?php
/**
 * The admin-specific calendar functionality of the plugin.
 *
 * @link       https://www.solwininfotech.com/
 * @since      1.0.0
 *
 * @package    Aior
 * @subpackage Aior/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aior
 * @subpackage Aior/admin
 * @author     Solwin Infotech <support@solwininfotech.com>
 */
class Aior_Notification {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		/* Email when new appointment */
		add_action(
			'aior_rfs_notification',
			function( $args ) {
				$pid        = $args['pid'];
				$fid        = get_post_meta( $pid, 'form_id', true );
				$tid        = get_post_meta( $pid, 'tid', true );
				$sid        = get_post_meta( $pid, 'sid', true );
				$slot       = Aior_Appointment_Booking::get_slot_data( $fid, $tid, $sid );
				$start_time = $slot['start_time'];
				$end_time   = $slot['end_time'];
				$to         = get_post_meta( $pid, 'rf_email', true );
				$data       = array(
					'reservation_id'    => $pid,
					'rf_first_name'     => get_post_meta( $pid, 'rf_first_name', true ),
					'rf_last_name'      => get_post_meta( $pid, 'rf_last_name', true ),
					'rf_email'          => $to,
					'booking_date'      => get_post_meta( $pid, 'tdt', true ),
					'booking_time_from' => $start_time,
					'booking_time_to'   => $end_time,
					'number_guest'      => get_post_meta( $pid, 'sno', true ),
					'phone_no'          => get_post_meta( $pid, 'rf_phone_no', true ),
					'rf_note'           => get_post_meta( $pid, 'rf_note', true ),
				);
				self::send_mail_new_appointment( $fid, $data, $to );

			},
			10,
			1
		);

		/* Email when appointment approved */
		add_action(
			'aior_appointment_approved',
			function( $pid ) {
				$fid        = get_post_meta( $pid, 'form_id', true );
				$tid        = get_post_meta( $pid, 'tid', true );
				$sid        = get_post_meta( $pid, 'sid', true );
				$slot       = Aior_Appointment_Booking::get_slot_data( $fid, $tid, $sid );
				$start_time = $slot['start_time'];
				$end_time   = $slot['end_time'];
				$to         = get_post_meta( $pid, 'rf_email', true );
				$data       = array(
					'reservation_id'    => $pid,
					'rf_first_name'     => get_post_meta( $pid, 'rf_first_name', true ),
					'rf_last_name'      => get_post_meta( $pid, 'rf_last_name', true ),
					'rf_email'          => $to,
					'booking_date'      => get_post_meta( $pid, 'tdt', true ),
					'booking_time_from' => $start_time,
					'booking_time_to'   => $end_time,
					'number_guest'      => get_post_meta( $pid, 'sno', true ),
					'phone_no'          => get_post_meta( $pid, 'rf_phone_no', true ),
					'rf_note'           => get_post_meta( $pid, 'rf_note', true ),
				);
				self::send_mail_approved_appointment( $fid, $data, $to );
			},
			10,
			1
		);
		add_action(
			'aior_appointment_declined',
			function( $pid ) {
				$fid        = get_post_meta( $pid, 'form_id', true );
				$tid        = get_post_meta( $pid, 'tid', true );
				$sid        = get_post_meta( $pid, 'sid', true );
				$slot       = Aior_Appointment_Booking::get_slot_data( $fid, $tid, $sid );
				$start_time = $slot['start_time'];
				$end_time   = $slot['end_time'];
				$to         = get_post_meta( $pid, 'rf_email', true );
				$data       = array(
					'reservation_id'    => $pid,
					'rf_first_name'     => get_post_meta( $pid, 'rf_first_name', true ),
					'rf_last_name'      => get_post_meta( $pid, 'rf_last_name', true ),
					'rf_email'          => $to,
					'booking_date'      => get_post_meta( $pid, 'tdt', true ),
					'booking_time_from' => $start_time,
					'booking_time_to'   => $end_time,
					'number_guest'      => get_post_meta( $pid, 'sno', true ),
					'phone_no'          => get_post_meta( $pid, 'rf_phone_no', true ),
					'rf_note'           => get_post_meta( $pid, 'rf_note', true ),
				);
				self::send_mail_cancellation_appointment( $fid, $data, $to );
			},
			10,
			1
		);

	}
	/**
	 * Get the Settings.
	 *
	 * @since    1.0.0
	 * @param int $fid Form_ID.
	 */
	public function get_settings( $fid ) {
		$notif_success_message     = get_post_meta( $fid, 'notif_success_message', true );
		$enable_admin_notification = get_post_meta( $fid, 'enable_admin_notification', true );
		$admin_email               = get_post_meta( $fid, 'admin_email', true );
		$admin_cc_email            = get_post_meta( $fid, 'admin_cc_email', true );
		$admin_subject             = get_post_meta( $fid, 'admin_subject', true );
		$admin_email_body          = get_post_meta( $fid, 'admin_email_body', true );
		$client_subject            = get_post_meta( $fid, 'client_subject', true );
		$client_email_body         = get_post_meta( $fid, 'client_email_body', true );
		$confirm_subject           = get_post_meta( $fid, 'confirm_subject', true );
		$confirm_email_body        = get_post_meta( $fid, 'confirm_email_body', true );
		$cancel_subject            = get_post_meta( $fid, 'cancel_subject', true );
		$cancel_email_body         = get_post_meta( $fid, 'cancel_email_body', true );
		$array                     = array(
			'success_message'           => $notif_success_message,
			'enable_admin_notification' => $enable_admin_notification,
			'admin_email'               => $admin_email,
			'admin_cc_email'            => $admin_cc_email,
			'admin_subject'             => $admin_subject,
			'admin_email_body'          => $admin_email_body,
			'client_subject'            => $client_subject,
			'client_email_body'         => $client_email_body,
			'confirm_subject'           => $confirm_subject,
			'confirm_email_body'        => $confirm_email_body,
			'cancel_subject'            => $cancel_subject,
			'cancel_email_body'         => $cancel_email_body,
		);
		return $array;
	}
	/**
	 * Replace the Template Variables.
	 *
	 * @since    1.0.0
	 * @param int    $fid Form_ID.
	 * @param string $message Displays Message.
	 * @param array  $data Template Data.
	 */
	public static function template_vars_replacement( $fid, $message, $data ) {
		$rid              = $data['reservation_id'];
		$cancel_page_id   = get_post_meta( $fid, 'cancel_page', true );
		$cancel_page_link = get_permalink( $cancel_page_id ) . '?cancel=true&rid=' . Aior_Core::encrypt( $rid, 'slienceisgold', true );
		$page_link        = '';
		$tags_array       = array(
			'{cancel_link}',
			'{reservation_link}',
			'{first_name}',
			'{last_name}',
			'{reservation_date}',
			'{reservation_time_from}',
			'{reservation_time_to}',
			'{reservation_number_guest}',
			'{reservation_phone_no}',
			'{reservation_id}',
			'{reservation_user_email}',
			'{reservation_user_note}',
			'{blog_url}',
			'{home_url}',
			'{site_name}',
			'{blog_description}',
			'{admin_email}',
			'{date}',
			'{time}',
		);
		$first_name       = isset( $data['rf_first_name'] ) ? $data['rf_first_name'] : '';
		$last_name        = isset( $data['rf_last_name'] ) ? $data['rf_last_name'] : '';
		$user_email       = isset( $data['rf_email'] ) ? $data['rf_email'] : '';
		$user_note        = isset( $data['rf_note'] ) ? $data['rf_note'] : '';
		$to_replace       = array(
			$cancel_page_link,
			$page_link,
			$first_name,
			$last_name,
			$data['booking_date'],
			$data['booking_time_from'],
			$data['booking_time_to'],
			$data['number_guest'],
			$data['phone_no'],
			$data['reservation_id'],
			$user_email,
			$user_note,
			get_option( 'siteurl' ),
			get_option( 'home' ),
			get_option( 'blogname' ),
			get_option( 'blogdescription' ),
			get_option( 'admin_email' ),
			date_i18n( get_option( 'date_format' ) ),
			date_i18n( get_option( 'time_format' ) ),
		);
		return str_replace( $tags_array, $to_replace, $message );
	}
	/**
	 * Replace the Template Variables.
	 *
	 * @since    1.0.0
	 * @param int    $fid Form_ID.
	 * @param array  $data Field names.
	 * @param string $to Displays Message.
	 */
	public function send_mail_new_appointment( $fid, $data, $to ) {
		$user_subject = get_post_meta( $fid, 'client_subject', true );
		$user_message = get_post_meta( $fid, 'client_email_body', true );
		$header       = '';
		$header      .= "MIME-Version: 1.0\r\n";
		$header      .= "Content-Type: text/html; charset=utf-8\r\n";
		$admin_detail = get_user_by( 'email', get_option( 'admin_email' ) );
		$admin_email  = get_post_meta( $fid, 'admin_email', true );
		$header      .= 'From: ' . $admin_detail->display_name . ' <' . $admin_email . ">\r\n";
		$user_subject = $this->template_vars_replacement( $fid, $user_subject, $data );
		$user_message = $this->template_vars_replacement( $fid, $user_message, $data );
		$user_message = apply_filters( 'the_content', $user_message );
		wp_mail( $to, $user_subject, $user_message, $header );
		/* Admin Mail */
		if ( get_post_meta( $fid, 'enable_admin_notification', true ) ) {
			$admin_subject = get_post_meta( $fid, 'admin_subject', true );

			$admin_message  = get_post_meta( $fid, 'admin_email_body', true );
			$admin_email    = get_post_meta( $fid, 'admin_email', true );
			$headers[]      = 'MIME-Version: 1.0';
			$headers[]      = 'Content-Type: text/html; charset=utf-8';
			$headers[]      = 'From: ' . $data['rf_first_name'] . ' <' . $data['rf_email'] . '>';
			$admin_cc_emial = get_post_meta( $fid, 'admin_cc_email', true );
			if ( '' !== $admin_cc_emial ) {
				$headers[] = 'Cc: ' . $admin_cc_emial;
			}
			$admin_subject = $this->template_vars_replacement( $fid, $admin_subject, $data );
			$admin_message = $this->template_vars_replacement( $fid, $admin_message, $data );
			$admin_message = apply_filters( 'the_content', $admin_message );
			wp_mail( $admin_email, $admin_subject, $admin_message, $headers );
		}

	}
	/**
	 * Send Mail to Approved Appointments.
	 *
	 * @since    1.0.0
	 * @param int    $fid Form_ID.
	 * @param array  $data Field names.
	 * @param string $to Displays Message.
	 */
	public function send_mail_approved_appointment( $fid, $data, $to ) {
		$subject = get_post_meta( $fid, 'confirm_subject', true );

		$message      = get_post_meta( $fid, 'confirm_email_body', true );
		$header       = '';
		$header      .= "MIME-Version: 1.0\r\n";
		$header      .= "Content-Type: text/html; charset=utf-8\r\n";
		$admin_detail = get_userdata( get_current_user_id() );
		$name         = isset( $admin_detail->display_name ) ? $admin_detail->display_name : '';
		$email        = $admin_detail->user_email;
		$header      .= 'From: ' . $name . ' <' . $email . ">\r\n";
		$subject      = $this->template_vars_replacement( $fid, $subject, $data );
		$message      = $this->template_vars_replacement( $fid, $message, $data );
		$message      = apply_filters( 'the_content', $message );
		wp_mail( $to, $subject, $message, $header );
	}
	/**
	 * Send Mail to Cancelled Appointments.
	 *
	 * @since    1.0.0
	 * @param int    $fid Form_ID.
	 * @param array  $data Field names.
	 * @param string $to Displays Message.
	 */
	public function send_mail_cancellation_appointment( $fid, $data, $to ) {
		$subject = get_post_meta( $fid, 'cancel_subject', true );

		$message      = get_post_meta( $fid, 'cancel_email_body', true );
		$header       = '';
		$header      .= "MIME-Version: 1.0\r\n";
		$header      .= "Content-Type: text/html; charset=utf-8\r\n";
		$admin_detail = get_userdata( get_current_user_id() );
		$name         = isset( $admin_detail->display_name ) ? $admin_detail->display_name : '';
		$email        = $admin_detail->user_email;
		$header      .= 'From: ' . $name . ' <' . $email . ">\r\n";
		$subject      = $this->template_vars_replacement( $fid, $subject, $data );
		$message      = $this->template_vars_replacement( $fid, $message, $data );
		$message      = apply_filters( 'the_content', $message );
		wp_mail( $to, $subject, $message, $header );
	}
}
$aior_notification = new Aior_Notification();
