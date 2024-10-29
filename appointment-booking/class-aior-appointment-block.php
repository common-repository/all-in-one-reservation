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
class Aior_Appointment_Block {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'load_block_script' ) );
		add_action( 'admin_print_scripts', array( $this, 'get_booking_appointment' ) );
		add_action( 'manage_posts_extra_tablenav', array( $this, 'export_button_csv_appointment_list' ), 20, 1 );
		add_action( 'init', array( $this, 'export_csv_appointment_list' ) );
	}
	/**
	 * Block JS Loader Script.
	 *
	 * @since    1.0.0
	 */
	public function load_block_script() {
		wp_enqueue_script( 'aior-appointment-block', plugins_url( '/src/admin/aior-appointment-block.js', __FILE__ ), array( 'wp-blocks', 'wp-editor', 'wp-i18n' ), true );
	}
	/**
	 * Get Booking Appointment.
	 *
	 * @since    1.0.0
	 */
	public function get_booking_appointment() {
		$args  = array(
			'numberposts' => -1,
			'post_type'   => 'sol_reservation_form',
		);
		$posts = get_posts( $args );
		if ( $posts ) {
			?>
		<script>
			var aior_reservation_form_obj=[{
			<?php
			foreach ( $posts as $post ) {
				$id    = $post->ID;
				$title = $post->post_title;
				echo "'" . esc_attr( $id ) . "': '" . esc_html( $title ) . "'";
				echo ',';
			}
			?>
				}];
		</script>
			<?php
		}
	}

	/**
	 * CSV Export Button.
	 *
	 * @since 1.0.0
	 * @param string $which Possibly top.
	 */
	public static function export_button_csv_appointment_list( $which ) {
		global $typenow;
		if ( 'sol_appointment_list' === $typenow && 'top' === $which ) {
			?>
			<label><?php esc_html_e( 'Advance Export', 'all-in-one-reservation' ); ?> <input type="checkbox" name="eport_all_sol_appointment_list_fields" value="export_all"></label>
			<input type="submit" name="export_all_sol_appointment_list" class="button button-primary" value="<?php esc_attr_e( 'Export All', 'all-in-one-reservation' ); ?>" />
			<?php
		}
	}
	/**
	 * CSV export appointment list.
	 *
	 * @since    1.0.0
	 */
	public static function export_csv_appointment_list() {
		if ( isset( $_GET['export_all_sol_appointment_list'] ) ) {
			$export_all_fields = isset( $_GET['eport_all_sol_appointment_list_fields'] ) ? sanitize_text_field( wp_unslash( $_GET['eport_all_sol_appointment_list_fields'] ) ) : false;
			$arg               = array(
				'post_type'      => 'sol_appointment_list',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			);

			global $post;
			$arr_post = get_posts( $arg );
			if ( $arr_post ) {
				header( 'Content-type: text/csv' );
				header( 'Content-Disposition: attachment; filename="aior-appointment-list.csv"' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );
				$file = fopen( 'php://output', 'w' );
				if ( $export_all_fields ) {
					$csv_titles = array(
						esc_html__( 'ID', 'all-in-one-reservation' ),
						esc_html__( 'Reservation', 'all-in-one-reservation' ),
						esc_html__( 'Booked Slot', 'all-in-one-reservation' ),
						esc_html__( 'First Name', 'all-in-one-reservation' ),
						esc_html__( 'Last Name', 'all-in-one-reservation' ),
						esc_html__( 'Email', 'all-in-one-reservation' ),
						esc_html__( 'Phone', 'all-in-one-reservation' ),
						esc_html__( 'Note', 'all-in-one-reservation' ),
						esc_html__( 'Status', 'all-in-one-reservation' ),
						esc_html__( 'Form', 'all-in-one-reservation' ),
						esc_html__( 'Start Time', 'all-in-one-reservation' ),
						esc_html__( 'End Time', 'all-in-one-reservation' ),
						esc_html__( 'SID', 'all-in-one-reservation' ),
						esc_html__( 'SNO', 'all-in-one-reservation' ),
						esc_html__( 'STP', 'all-in-one-reservation' ),
						esc_html__( 'TID', 'all-in-one-reservation' ),
						esc_html__( 'TNO', 'all-in-one-reservation' ),
						esc_html__( 'Appointment Date', 'all-in-one-reservation' ),
						esc_html__( 'Price per Slot', 'all-in-one-reservation' ),
					);
				} else {
					$csv_titles = array(
						esc_html__( 'ID', 'all-in-one-reservation' ),
						esc_html__( 'Reservation', 'all-in-one-reservation' ),
						esc_html__( 'Booked Slot', 'all-in-one-reservation' ),
						esc_html__( 'First Name', 'all-in-one-reservation' ),
						esc_html__( 'Last Name', 'all-in-one-reservation' ),
						esc_html__( 'Email', 'all-in-one-reservation' ),
						esc_html__( 'Phone', 'all-in-one-reservation' ),
						esc_html__( 'Note', 'all-in-one-reservation' ),
						esc_html__( 'Status', 'all-in-one-reservation' ),
						esc_html__( 'Start Time', 'all-in-one-reservation' ),
						esc_html__( 'End Time', 'all-in-one-reservation' ),
						esc_html__( 'Appointment Date', 'all-in-one-reservation' ),
						esc_html__( 'Price per Slot', 'all-in-one-reservation' ),
					);
				}
				fputcsv( $file, $csv_titles );
				foreach ( $arr_post as $post_a ) {
					$pid           = $post_a->ID;
					$rf_slot       = get_post_meta( $pid, 'rf_slot', true );
					$rf_first_name = get_post_meta( $pid, 'rf_first_name', true );
					$rf_last_name  = get_post_meta( $pid, 'rf_last_name', true );
					$rf_email      = get_post_meta( $pid, 'rf_email', true );
					$rf_phone_no   = get_post_meta( $pid, 'rf_phone_no', true );
					$rf_note       = get_post_meta( $pid, 'rf_note', true );
					$status        = get_post_meta( $pid, 'status', true );
					$form_id       = get_post_meta( $pid, 'form_id', true );
					$stime         = get_post_meta( $pid, 'stime', true );
					$etime         = get_post_meta( $pid, 'etime', true );
					$sid           = get_post_meta( $pid, 'sid', true );
					$sno           = get_post_meta( $pid, 'sno', true );
					$stp           = get_post_meta( $pid, 'stp', true );
					$tid           = get_post_meta( $pid, 'tid', true );
					$tno           = get_post_meta( $pid, 'tno', true );
					$tdt           = get_post_meta( $pid, 'tdt', true );
					$price         = get_post_meta( $pid, 'price', true );
					if ( $export_all_fields ) {
						fputcsv( $file, array( $pid, get_the_title(), $rf_slot, $rf_first_name, $rf_last_name, $rf_email, $rf_phone_no, $rf_note, $status, $form_id, $stime, $etime, $sid, $sno, $stp, $tid, $tno, $tdt, $price ) );
					} else {
						fputcsv( $file, array( $pid, get_the_title(), $rf_slot, $rf_first_name, $rf_last_name, $rf_email, $rf_phone_no, $rf_note, $status, $stime, $etime, $tdt, $price ) );
					}
				}
				exit();
			}
		}
	}
}
$aior_appointment_block = new Aior_Appointment_Block();
