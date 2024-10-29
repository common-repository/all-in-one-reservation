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

global $wpdb;
if ( ! isset( $_GET['sh'] ) || isset( $_GET['sh'] ) && AIORICAL_SECURE_HASH !== $_GET['sh'] ) {
	wp_die( '<strong>Calendar Feed Requirements:</strong><br>The AIOR calendar feeds now require a secure hash to access. Please take a look at your "Appointments > Calendar Feeds" page for the updated feed URLs.' );
}

header( 'Content-type: text/calendar; charset=utf-8' );
header( 'Content-Disposition: attachment; filename=aior-appointment-feed-' . AIOR_VERSION . '.ics' );

if ( isset( $_GET['calendar'] ) ) :
	$calendar_id = sanitize_text_field( wp_unslash( $_GET['calendar'] ) );
else :
	$calendar_id = false;
endif;

$appts_in_this_timeslot = array();
$table_name             = $wpdb->prefix . 'posts';
$sql                    = $wpdb->prepare( "SELECT * FROM %s WHERE post_type = 'sol_appointment_list' AND ( post_status = 'publish' OR post_status = 'future')", $table_name );

foreach ( $sql as $data ) {
	$start_time                                    = get_post_meta( $data->ID, 'stime', true );
	$end_time                                      = get_post_meta( $data->ID, 'etime', true );
	$timeslot                                      = get_post_meta( $data->ID, 'rf_slot', true );
	$user_id                                       = $data->post_author;
	$appointments_array[ $data->ID ]['post_id']    = $data->ID;
	$appointments_array[ $data->ID ]['start_time'] = $start_time;
	$appointments_array[ $data->ID ]['end_time']   = $end_time;
	$appointments_array[ $data->ID ]['timeslot']   = $timeslot;
	$appointments_array[ $data->ID ]['status']     = $data->post_status;
	$appointments_array[ $data->ID ]['user']       = $user_id;
	$appts_in_this_timeslot[]                      = $data->ID;
}

?>BEGIN:VCALENDAR<?php echo "\r\n"; ?>
VERSION:2.0<?php echo "\r\n"; ?>
PRODID:-//aior.io//AIOR Calendar<?php echo "\r\n"; ?>
CALSCALE:GREGORIAN<?php echo "\r\n"; ?>
<?php
if ( ! empty( $appts_in_this_timeslot ) ) :

	foreach ( $appts_in_this_timeslot as $appt_id ) :

		$first_name = get_post_meta( $appt_id, 'rf_first_name', true );
		$last_name  = get_post_meta( $appt_id, 'rf_last_name', true );
		$full_name  = $first_name . ( $last_name ? ' ' . $last_name : '' );
		$email_id   = get_post_meta( $appt_id, 'rf_email', true );

		if ( ! $full_name ) :

			// Customer Information.
			$user_id = $appointments_array[ $appt_id ]['user'];
			if ( $user_id ) :
				$user_info    = get_userdata( $user_id );
				$display_name = $user_info->$display_name;
				if ( ! empty( $user_info ) ) :
					$email = $user_info->user_email;
				else :
					$display_name = esc_html__( '[No name]', 'all-in-one-reservation' );
					$email        = esc_html__( '[No email]', 'all-in-one-reservation' );
				endif;
			else :
				$display_name = esc_html__( '[No name]', 'all-in-one-reservation' );
				$email        = esc_html__( '[No email]', 'all-in-one-reservation' );
			endif;

		else :

			$display_name = $full_name;
			$email        = $email_id;

		endif;

		$display_name = clean_calendar_string( $display_name );
		$email        = clean_calendar_string( $email );

		// Appointment Information.
		if ( isset( $appt_id ) ) :
			$start_time = get_post_meta( $appt_id, 'stime', true );
			$end_time   = get_post_meta( $appt_id, 'etime', true );
			$timeslot   = get_post_meta( $appt_id, 'rf_slot', true );
			$phone_no   = get_post_meta( $appt_id, 'rf_phone_no', true );
			$notes      = get_post_meta( $appt_id, 'rf_note', true );
			$tdt_date   = get_post_meta( $appt_id, 'tdt', true );
			$start_date = strtotime( $tdt_date );
			$aior_start = gmdate( 'H:i:s', strtotime( $start_time ) );
			$starts     = gmdate( 'Y-m-d', strtotime( get_gmt_from_date( $tdt_date ) ) );

			$end_t    = gmdate( 'H:i:s', strtotime( $end_time ) );
			$end_date = gmdate( 'Y-m-d', strtotime( get_gmt_from_date( $tdt_date ) ) );

			$start_date_time = $starts . gmdate( 'H:i:s', strtotime( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $starts . ' ' . $aior_start ) ) ) ) );
			$end_date_time   = $end_date . gmdate( 'H:i:s', strtotime( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $end_date . ' ' . $end_t ) ) ) ) );

			$formatted_start_date = gmdate( 'Ymd\THis', strtotime( $start_date_time ) );
			$formatted_end_date   = gmdate( 'Ymd\THis', strtotime( $end_date_time ) );
			$description          = str_replace( "\r\n", "\\n\\n", $notes );
			?>
			BEGIN:VEVENT<?php echo "\r\n"; ?>
DTSTAMP:<?php echo esc_html( $formatted_start_date ); ?>Z<?php echo "\r\n"; ?>
			<?php
			if ( $formatted_start_date ) :
				?>
						DTSTART;TZID=Asia/Kolkata:<?php echo esc_html( $formatted_start_date ); ?>Z<?php echo "\r\n"; ?>
DTEND;TZID=Asia/Kolkata:<?php echo esc_html( $formatted_end_date ); ?>Z<?php echo "\r\n"; ?>
				<?php
else :
	?>
	DTSTART;VALUE=DATE:<?php echo esc_html( $formatted_start_date ); ?><?php echo "\r\n"; ?>
DTEND;VALUE=DATE:<?php echo esc_html( $formatted_end_date ); ?><?php echo "\r\n"; ?>
							<?php
endif;
?>
SUMMARY:<?php echo esc_html( $display_name ); ?><?php echo "\r\n"; ?><?php echo ( esc_html( $description ) ? 'DESCRIPTION:' . esc_html( $description ) . "\r\n" : '' ); ?>
UID:aior-appointment-<?php echo esc_html( $appt_id ); ?><?php echo "\r\n"; ?>
END:VEVENT<?php echo "\r\n"; ?>
			<?php

		endif;

	endforeach;

endif;

?>
END:VCALENDAR
<?php
/**
 * Clean the calendar string.
 *
 * @param bool $string Pass the boolean values.
 */
function clean_calendar_string( $string = false ) {

	if ( $string ) :

		preg_match_all( '/<\!--([\\s\\S]*?)-->/', $string, $matches );
		if ( isset( $matches[0] ) && ! empty( $matches[0] ) ) :
			foreach ( $matches[0] as $match ) :
				$string = str_replace( $match, '', $string );
			endforeach;
		endif;

		if ( function_exists( 'mb_convert_encoding' ) ) :
			$string = mb_convert_encoding( $string, 'UTF-8' );
		else :
			$string = htmlspecialchars_decode( utf8_decode( htmlentities( $string, ENT_COMPAT, 'utf-8', false ) ) );
		endif;

		return preg_replace( '/([\,\;])/', '\\$1', $string );

	else :

		return false;

	endif;

}
