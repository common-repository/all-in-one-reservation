<?php
/**
 * The admin-specific functionality of the plugin.
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
class Aior_Appointment_Booking {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_filter( 'aior_add_in_slot_settings', array( $this, 'slot_builder_area' ), 10, 1 );
		add_filter( 'aior_default_form', array( $this, 'default_form' ), 10, 1 );
		/* Admin Style */
		add_action( 'admin_enqueue_scripts', array( $this, 'appointment_booking_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'appointment_booking_scripts' ) );
		/* Public Style */
		add_action( 'wp_enqueue_scripts', array( $this, 'appointment_booking_styles_public' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'appointment_booking_scripts_public' ) );

		add_action( 'aior_save_rf_settings', array( $this, 'aior_save_settings' ), 10, 1 );
		/* Add for translation accesibl from js */
		add_filter(
			'aior_obj_admin',
			function( $array ) {
				foreach ( $array['lang'] as $k => $v ) {
					$new [ $k ] = $v;
				}
				$new   ['close']          = esc_html__( 'Remove', 'all-in-one-reservation' );
				$new   ['title']          = esc_html__( 'Task Title', 'all-in-one-reservation' );
				$new   ['desc']           = esc_html__( 'Description', 'all-in-one-reservation' );
				$new   ['start_time']     = esc_html__( 'Start Time', 'all-in-one-reservation' );
				$new   ['end_time']       = esc_html__( 'End Time', 'all-in-one-reservation' );
				$new   ['interval']       = esc_html__( 'Interval in Minute', 'all-in-one-reservation' );
				$new   ['space']          = esc_html__( 'Available Space', 'all-in-one-reservation' );
				$new   ['build']          = esc_html__( 'Build Slots', 'all-in-one-reservation' );
				$new   ['add_slot_range'] = esc_html__( 'Add Date Slot', 'all-in-one-reservation' );
				$new   ['enable']         = esc_html__( 'Enable', 'all-in-one-reservation' );
				$new   ['disable']        = esc_html__( 'Disable', 'all-in-one-reservation' );
				$new   ['start_date']     = esc_html__( 'Start Date', 'all-in-one-reservation' );
				$new   ['end_date']       = esc_html__( 'End Date', 'all-in-one-reservation' );
				$new   ['duration']       = esc_html__( 'Work Duration in Minute', 'all-in-one-reservation' );
				$new   ['price']          = esc_html__( 'Price', 'all-in-one-reservation' );
				$array['lang']            = $new;
				return $array;
			},
			10,
			1
		);
		add_action( 'aoir_reservation_global_ajax', array( $this, 'generate_day_slot' ) );
		add_action( 'aoir_reservation_global_ajax', array( $this, 'generate_montly_slot' ) );
		add_action( 'aoir_reservation_global_ajax', array( $this, 'remove_montly_task' ) );
		add_action( 'aoir_reservation_global_ajax', array( $this, 'get_appointment_slot_ajax' ) );
		add_action( 'aoir_reservation_global_ajax', array( $this, 'get_appointment_form_ajax' ) );
		add_filter( 'aior_shortcode', array( $this, 'appointment_calendar' ), 10, 2 );
		add_action( 'aior_rfs_before_save', array( $this, 'do_before_save_fts_form' ), 10, 1 );
		add_action( 'aior_rfs_meta_save', array( $this, 'do_save_rfs_form_meta' ), 10, 1 );
		add_filter(
			'aior_new_appointment_notification',
			function() {
				return get_option( 'aior_new_appointment_notification' );
			},
			10,
			1
		);
		add_action(
			'current_screen',
			function() {
				$current_screen = get_current_screen();
				if ( 'sol_appointment_list' === $current_screen->post_type ) {
					update_option( 'aior_new_appointment_notification', '' );
				}
			}
		);
		add_action( 'admin_init', array( $this, 'theme_and_style' ) );
	}

	/**
	 * Calendar.
	 *
	 * @since    1.0.0
	 */
	public function theme_and_style() {
		$nav_args = array(
			'id'     => '32',
			'name'   => esc_html__( 'Style', 'all-in-one-reservation' ),
			'title'  => esc_html__( 'Calendar Theme and Style', 'all-in-one-reservation' ),
			'filter' => 'aior_add_in_theme_and_style',
		);
		Aior_Reservation_Form::create_rs_tab( $nav_args );
		add_filter(
			'aior_add_in_theme_and_style',
			function () {
				$aior_builder = new Aior_Builder();
				$rf_data      = Aior_Reservation_Form::get_settings();
				?>
			<div class="form-group">
				<div class=""><label class="control-label"><?php esc_html_e( 'Select Calendar Theme', 'all-in-one-reservation' ); ?></label></div>
				<div class="thumb_radio">
					<?php
					$theme          = array(
						1  => array(
							'title' => esc_html__( 'Style One', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/c1.jpg',
						),
						2  => array(
							'title' => esc_html__( 'Style Two', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/c2.jpg',
						),
						3  => array(
							'title' => esc_html__( 'Style Three', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/c3.jpg',
						),
						4  => array(
							'title' => esc_html__( 'Style Four', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/c4.jpg',
						),
						5  => array(
							'title' => esc_html__( 'Style Five', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/c5.png',
						),
						6  => array(
							'title' => esc_html__( 'Style Six', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/c6.png',
						),
						7  => array(
							'title' => esc_html__( 'Style Seven', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/c7.png',
						),
						8  => array(
							'title' => esc_html__( 'Style Eight', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/c8.png',
						),
						9  => array(
							'title' => esc_html__( 'Style Nine', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/c9.png',
						),
						10 => array(
							'title' => esc_html__( 'Style Ten', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/c10.png',
						),
					);
					$calendar_theme = isset( $rf_data['calendar_theme'] ) && ! empty( $rf_data['calendar_theme'] ) ? $rf_data['calendar_theme'] : '0';
					$aior_builder->add_field(
						array(
							'type'   => 'radio_image',
							'id'     => 'calendar_theme',
							'name'   => 'calendar_theme',
							'value'  => $calendar_theme,
							'option' => $theme,
							'width'  => 150,
							'height' => 175,

						)
					);
					?>
				</div>
			</div>
			<div class="form-group">
				<div class=""><label class="control-label"><?php esc_html_e( 'Select Form Theme', 'all-in-one-reservation' ); ?></label></div>
				<div class="aior_form_theme">
					<?php
					$ftheme             = array(
						1  => array(
							'title' => esc_html__( 'Dark', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/f1.png',
						),
						2  => array(
							'title' => esc_html__( 'Minimal', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/f2.png',
						),
						3  => array(
							'title' => esc_html__( 'Borderless', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/f3.png',
						),
						4  => array(
							'title' => esc_html__( 'Bootstrap 4', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/f4.png',
						),
						5  => array(
							'title' => esc_html__( 'Material UI', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/f5.png',
						),
						6  => array(
							'title' => esc_html__( 'WordPress Admin', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/f6.png',
						),
						7  => array(
							'title' => esc_html__( 'Bulma', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/f7.png',
						),
						8  => array(
							'title' => esc_html__( 'Default', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/f8.png',
						),
						9  => array(
							'title' => esc_html__( 'Dark Theme 1', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/dark1.png',
						),
						10 => array(
							'title' => esc_html__( 'Dark Theme 2', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/dark2.png',
						),
						11 => array(
							'title' => esc_html__( 'Dark Theme 3', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/dark3.png',
						),
						12 => array(
							'title' => esc_html__( 'Light Theme 1', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/light1.png',
						),
						13 => array(
							'title' => esc_html__( 'Light Theme 2', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/light2.png',
						),
						14 => array(
							'title' => esc_html__( 'Light Theme 3', 'all-in-one-reservation' ),
							'thumb' => plugin_dir_url( __FILE__ ) . '/src/thumb/light3.png',
						),
					);
					$booking_form_theme = isset( $rf_data['booking_form_theme'] ) && ! empty( $rf_data['booking_form_theme'] ) ? $rf_data['booking_form_theme'] : '4';
					$aior_builder->add_field(
						array(
							'type'   => 'radio_image',
							'id'     => 'booking_form_theme',
							'name'   => 'booking_form_theme',
							'value'  => $booking_form_theme,
							'option' => $ftheme,
							'width'  => 150,
							'height' => 175,
						)
					);
					?>
				</div>
			</div>
				<?php
			}
		);
	}
	/**
	 * Verfiy data before final submit.
	 *
	 * @since    1.0.0
	 * @param array $args Arguments for data.
	 */
	public function do_before_save_fts_form( $args ) {
		$data = $args['data'];
	}
	/**
	 * Save Form Meta.
	 *
	 * @since    1.0.0
	 * @param    array $args arguments.
	 */
	public function do_save_rfs_form_meta( $args ) {
		$data      = $args['data'];
		$pid       = $args['pid'];
		$fid       = $data['reservation_form_id'];
		$sid       = $data['sid'];
		$pri       = $data['pri'];
		$sslot     = (int) $data['rf_slot'];
		$slot      = 'slot_booked_' . $sid;
		$sdata     = (int) get_post_meta( $fid, $slot, true );
		$count     = $sdata + $sslot;
		$slot_type = $data['stp'];
		$anan      = (int) get_option( 'aior_new_appointment_notification' );
		$anan      = $anan++;
		update_post_meta( $pid, 'sid', (int) $sid );
		update_post_meta( $pid, 'sno', sanitize_text_field( $sslot ) );
		update_post_meta( $pid, 'stp', sanitize_text_field( $slot_type ) );
		update_post_meta( $pid, 'tid', (int) $data['tid'] );
		update_post_meta( $pid, 'tno', sanitize_text_field( $data['tno'] ) );
		update_post_meta( $pid, 'tdt', sanitize_text_field( $data['tdt'] ) );
		update_post_meta( $pid, 'price', sanitize_text_field( $data['pri'] ) );
		update_post_meta( $fid, $slot, (int) $count );
		update_option( 'aior_new_appointment_notification', (int) $anan );
	}
	/**
	 * Get available slot.
	 *
	 * @since    1.0.0
	 * @param    int    $pid post id.
	 * @param    int    $tid task id.
	 * @param    int    $sid slot id.
	 * @param    string $type slot type.
	 * @return $available
	 */
	public static function get_available_slot( $pid, $tid, $sid, $type ) {
		$total_slot = '';
		if ( 'month' === $type ) {
			$task = get_post_meta( $pid, 'slotmtask_' . $tid, true );
			if ( $task ) {
				foreach ( $task as $ts ) {
					if ( $ts['q'] === $sid ) {
						$total_slot = (int) $ts['r'];
					}
				}
			}
		} else {
			$day  = $type; /* this is day name */
			$task = get_post_meta( $pid, 'slot_weekly', true );
			if ( $task ) {
				$slot = $task[ $day ][ $tid ];
				foreach ( $slot as $ts ) {
					if ( $ts['q'] === $sid ) {
						$total_slot = (int) $ts['s'];
					}
				}
			}
		}
		$booked_slot = (int) get_post_meta( $pid, 'slot_booked_' . $sid, true );
		$available   = $total_slot - $booked_slot;
		return $available;
	}
	/**
	 * Get custom date range.
	 *
	 * @since    1.0.0
	 * @param    array $slot_monthly slot montly.
	 * @return $month_range
	 */
	public static function get_custom_date_range( $slot_monthly ) {
		$list_date = array();
		if ( $slot_monthly ) {
			foreach ( $slot_monthly as $task_id => $task ) {
				$start_d = isset( $task['s'] ) ? $task['s'] : '';
				$end_d   = isset( $task['e'] ) ? $task['e'] : '';
				$ssdate  = strtotime( $start_d );
				$end_d   = strtotime( $end_d );
				for ( $j = $ssdate;$j <= $end_d;$j += ( 86400 ) ) {
					$alldates = gmdate( 'Y-m-d', $j );
					array_push( $list_date, $alldates );
				}
				$month_range = array_unique( $list_date );
			}
			return $month_range;
		}
	}
	/**
	 * Get between dates.
	 *
	 * @since    1.0.0
	 * @param    string $start_date Start Date.
	 * @param    string $end_date End Date.
	 * @return $rang_array
	 */
	public static function get_between_dates( $start_date, $end_date ) {
		$rang_array = array();

		$start_date = strtotime( $start_date );
		$end_date   = strtotime( $end_date );

		for ( $current_date = $start_date; $current_date <= $end_date; $current_date += ( 86400 ) ) {
			$date         = gmdate( 'Y-m-d', $current_date );
			$rang_array[] = $date;
		}

		return $rang_array;
	}
	/**
	 * Render Calendar.
	 *
	 * @since    1.0.0
	 * @param    int $o output.
	 * @param    int $id id.
	 */
	public function appointment_calendar( $o, $id ) {
		ob_start();
		global $wpdb;
		$slot_type           = get_post_meta( $id, 'slot_type', true );
		$booking_type        = get_post_meta( $id, 'booking_type', true );
		$layout_type         = get_post_meta( $id, 'layout_type', true );
		$reservation_type    = get_post_meta( $id, 'reservation_type', true );
		$slot_weekly         = get_post_meta( $id, 'slot_weekly', true );
		$slot_monthly        = get_post_meta( $id, 'slot_montly_list', true );
		$calendar_theme      = get_post_meta( $id, 'calendar_theme', true );
		$booking_form_theme  = get_post_meta( $id, 'booking_form_theme', true );
		$appointment_limit   = get_post_meta( $id, 'appointment_limit', true );
		$prevent_before_date = get_post_meta( $id, 'prevent_before_date', true );
		$holiday_dates       = get_post_meta( $id, 'holiday_dates', true );
		$prevent_after_date  = get_post_meta( $id, 'prevent_after_date', true );
		$comments            = get_post_meta( $id, 'enable_comment', true );
		$date_range          = self::get_custom_date_range( $slot_monthly );
		$i                   = 0;
		$is_user_logged_in   = is_user_logged_in();
		$current_user_id      = get_current_user_id();
		$author_id            = get_post_field( 'post_author', $id );
		$author_name          = get_the_author_meta( 'display_name', $author_id );
		$comment_author_email = get_option( 'admin_email' );
		$comment_author_url   = get_option( 'siteurl' );
		if ( $is_user_logged_in ) {
			$user_count          = isset( $user_count ) ? $user_count : 0;
			$booked_current_user = $is_user_logged_in ? wp_get_current_user() : false;
			$user_id             = $booked_current_user->ID;
			$sql                 = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type = 'sol_appointment_list' AND post_status = 'publish' AND post_author =  %d ", $user_id ) );
			$count               = count( $sql );
			if ( $count > 0 ) {
				$user_count = $count;
			}
		} else {
			$user_count = 0;
		}
		wp_nonce_field( 'on_aior_admin_global_nonce', 'aior_admin_global_nonce' );
		if ( isset( $comments ) && 1 == $comments ) {
			$comment = isset( $_POST['aior_comment'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_comment'] ) ) : "";
			if( isset( $_POST['submit_aior_comment'] ) ) {
				$update_data = array(
					'comment_content'      => wp_strip_all_tags( $comment ),
					'comment_approved'     => '1',
					'comment_post_ID'      => esc_attr( $id ),
					'comment_type'         => 'comment',
					'user_id'              => esc_attr( $current_user_id ),
					'comment_author'       => esc_attr( $author_name ),
					'comment_author_email' => esc_attr( $comment_author_email ),
					'comment_author_url'   => esc_url( $comment_author_url ),
				);
				wp_insert_comment( $update_data );
			}
		}
		?>
		<input type="hidden" class="aior_abpid" value="<?php echo esc_attr( $id ); ?>">
		<input type="hidden" id="aior_slot_type" value="<?php echo esc_attr( $slot_type ); ?>">
		<input type="hidden" id="aior_booking_type" value="<?php echo esc_attr( $booking_type ); ?>">
		<input type="hidden" id="aior_reservation_type" value="<?php echo esc_attr( $reservation_type ); ?>">
		<input type="hidden" id="aior_login_page" value="<?php echo esc_url( site_url() . '/wp-login.php' ); ?>">
		<input type="hidden" id="appointment_limit" value="<?php echo esc_attr( $appointment_limit ); ?>">
		<input type="hidden" id="prevent_before_date" value="<?php echo esc_attr( $prevent_before_date ); ?>">
		<input type="hidden" id="holiday_dates" value="<?php print_r( $holiday_dates ); //phpcs:ignore ?>">
		<input type="hidden" id="prevent_after_date" value="<?php echo esc_attr( $prevent_after_date ); ?>">
		<input type="hidden" id="user_count" value="<?php echo esc_attr( $user_count ); ?>">
		<input type="hidden" id="is_user_login" value="<?php echo esc_attr( $is_user_logged_in ); ?>">
		<input type="hidden" id="aior_layout_type" value="<?php echo esc_attr( $layout_type ); ?>">
		<input type="hidden" id="aior-ab-calendar-<?php echo esc_attr( $id ); ?>">
		<div id="aior-ab-c-cnt" class="aior_cftheme_<?php echo esc_attr( $calendar_theme ); ?>">
			<?php if ( 'design2' === $layout_type ) { ?>
				<select name="service_name" id="service_name">
					<option value="All">Select any Service</option>
				<?php
				if ( 1 == $slot_type ) {
					foreach ( $slot_weekly as $slots ) {
						foreach ( $slots as $slot ) {
							?>
							<option value="<?php echo esc_attr( $slot[0]['t'] ); ?>"><?php echo esc_html( $slot[0]['t'] ); ?></option>
							<?php
						}
					}
				} else {
					foreach ( $slot_monthly as $slot ) {
						?>
						<option value="<?php echo esc_attr( $slot['t'] ); ?>"><?php echo esc_html( $slot['t'] ); ?></option>
						<?php
					}
				}
				?>
				</select>
			<?php } ?>
		</div>
		<?php
		$theme = array(
			1  => 'dark.min.css',
			2  => 'minimal.min.css',
			3  => 'borderless.min.css',
			4  => 'bootstrap-4.min.css',
			5  => 'material-ui.min.css',
			6  => 'wordpress-admin.min.css',
			7  => 'bulma.min.css',
			8  => 'default.min.css',
			9  => 'dark-theme-1.min.css',
			10 => 'dark-theme-2.min.css',
			11 => 'dark-theme-3.min.css',
			12 => 'light-theme-1.min.css',
			13 => 'light-theme-2.min.css',
			14 => 'light-theme-3.min.css',
		);
		wp_enqueue_style( 'sweetalert2-theme', esc_url( AIOR_PLUGIN_URL . '/lib/sweetalert2/themes/' . $theme[ $booking_form_theme ] ) );
		?>
		<script>
		jQuery(document).ready(function(){
			(function(){
				var today = new Date();
				var enableDate = [
				<?php
				if ( ! empty( $date_range ) ) {
					foreach ( $date_range as $enable_date ) {
						echo '"' . esc_html( $enable_date ) . '",';
					}
				}
				?>
				];
				localStorage.setItem("service_name", "");
				var holiday_dates = $('#holiday_dates').val();
				var prevent_before_date = $('#prevent_before_date').val();
				var start_date = '';
				var prevent_after_date = $('#prevent_after_date').val();
				var end_date = '';
				if( prevent_before_date !== null && prevent_before_date !== '' ) {
					start_date = prevent_before_date;
				} else {
					start_date = new Date();
				}
				if( prevent_after_date !== null && prevent_after_date !== '' ) {
					end_date = prevent_after_date;
				} else {
					end_date = false;
				}
				$('#aior-ab-calendar-<?php echo esc_attr( $id ); ?>').dateRangePicker({
					container  : '#aior-ab-c-cnt',
					startDate  : start_date,
					endDate    : end_date,
					inline     : true,
					alwaysOpen : true,
					singleMonth: true,
					singleDate : true,
					autoUpdateInput: true,
					<?php
					if ( 1 == $slot_type ) {
						echo 'beforeShowDay:off_days,';
					} else {
						echo 'beforeShowDay:off_custom,';
					}
					?>
				});
				function off_custom(date) {
					if(date){
						nd = moment(date).format('YYYY-MM-DD');
						holiday_array = [];
						<?php
						if ( ! empty( $holiday_dates['replace_this'] ) ) {
							unset( $holiday_dates['replace_this'] );
						}
						$final_array   = array();
						$holiday_array = '';
						if ( ! empty( $holiday_dates ) ) {
							foreach ( $holiday_dates as $v => $date ) {
								$holiday_date_from = date_create( $date['holidays_date_from'] );
								$holiday_date_to   = date_create( $date['holidays_date_to'] );
								$holiday_date_from = date_format( $holiday_date_from, 'Y-m-d' );
								$holiday_date_to   = date_format( $holiday_date_to, 'Y-m-d' );
								$dates             = self::get_between_dates( $holiday_date_from, $holiday_date_to );
								$final_array       = array_merge( $final_array, $dates );
								$holiday_array     = wp_json_encode( $final_array );
							}
							echo "var holiday_str = '{$holiday_array}', holiday_array = JSON.parse(holiday_str); \n";
						}
						?>
						if( enableDate.includes(nd) && ( $.inArray( nd, holiday_array ) === -1 )){
							return [true,"",'<?php esc_html_e( 'Available', 'all-in-one-reservation' ); ?>']
						} else {
							<?php if ( $holiday_dates ) { ?>
								if ( $.inArray( nd, holiday_array ) !== -1 ) {
							<?php } else { ?>
								if ( date.getDate() === 32) {
							<?php } ?>
								var valid = !( $.inArray( nd, holiday_array ) !== -1 );
								var _class = '';
								var _tooltip = valid ? '' : 'Holiday';
								return [valid,_class,_tooltip];
							} else {
								return [false,"",""]
							}
						}
					}
				};
				function off_days(date) {
					nd = moment(date).format('YYYY-MM-DD');
					holiday_array = [];
					<?php
					if ( ! empty( $holiday_dates['replace_this'] ) ) {
						unset( $holiday_dates['replace_this'] );
					}
						$final_array   = array();
						$holiday_array = '';
					if ( ! empty( $holiday_dates ) ) {
						foreach ( $holiday_dates as $v => $date ) {
							$holiday_date_from = date_create( $date['holidays_date_from'] );
							$holiday_date_to   = date_create( $date['holidays_date_to'] );
							$holiday_date_from = date_format( $holiday_date_from, 'Y-m-d' );
							$holiday_date_to   = date_format( $holiday_date_to, 'Y-m-d' );
							$dates             = self::get_between_dates( $holiday_date_from, $holiday_date_to );
							$final_array       = array_merge( $final_array, $dates );
							$holiday_array     = wp_json_encode( $final_array );
						}
						echo "var holiday_str = '{$holiday_array}', holiday_array = JSON.parse(holiday_str); \n";
					}
					?>
					if (
						(
						<?php
						$week = array(
							'sunday'    => '0',
							'monday'    => '1',
							'tuesday'   => '2',
							'wednesday' => '3',
							'thursday'  => '4',
							'friday'    => '5',
							'saturday'  => '6',
						);
						$i    = 0;
						if ( $slot_weekly ) {
							foreach ( $slot_weekly as $day => $v ) {
								if ( $i > 0 ) {
									echo ' || ';
								}
								echo 'date.getDay() === ' . esc_html( $week[ $day ] );
								$i++;
							}
						} else {
							echo 'date.getDay() === 7';
						}
						?>
						) && ( $.inArray( nd, holiday_array ) === -1 )
					){
						return [true,"",""]
					} else {
						<?php if ( $holiday_dates ) { ?>
								if ( $.inArray( nd, holiday_array ) !== -1 ) {
						<?php } else { ?>
								if ( date.getDate() === 32) {
						<?php } ?>
							var valid = !( $.inArray( nd, holiday_array ) !== -1 );
							var _class = '';
							var _tooltip = valid ? '' : 'Holiday';
							return [valid,_class,_tooltip];
						} else {
							return [false,"date-closed",""];
						}
					}
				};
				$('#service_name').on("change", function() {
					var current_service_name = $(this).val();
					localStorage.setItem("service_name", current_service_name);
				});
				/* Ajax Call */
				$('#aior-ab-c-cnt .month-wrapper').on('click','.day', function(){
					if($(this).hasClass('invalid')){}
					else{
						$(".sol_ap_s_date").remove();
						var weekday       = ["sunday","monday","tuesday","wednesday","thursday","friday","saturday"];
						var container     = $(this).parent().parent();
							selected_day  = Number($(this).attr('time'));
							objday        = new Date(selected_day);
							selected_date = Number($(this).attr('time'));
							objdate       = new Date(selected_date);
							sdate         = objdate.toDateString();
							slot_weekly   = $(this).attr( 'slot_weekly' );
							abcid         = $('.aior_abpid').attr('value');
							nonce         = $('#aior_admin_global_nonce').val();
							stype         = $('#aior_slot_type').val();
							fdate         = moment(selected_date).format('YYYY-MM-DD');
							service_name  = localStorage.getItem("service_name");

						$.ajax({
							type    : 'POST',
							dataType: 'html',
							url     : aior_obj.ajaxurl,
							data    : {  
								action       : 'aoir_reservation_global_ajax',
								act          : 'get_day_appointment_data',
								slot_type    : stype,
								selected_date: sdate,
								slot_weekly  : slot_weekly,
								selected_day : weekday[objday.getDay()],
								post_id      : abcid,
								service_name : service_name,
								nonce        : nonce
							},
							beforeSend:function(){Swal.fire({allowOutsideClick:false,showConfirmButton:false,didOpen:()=>{Swal.showLoading()}})},
							success:function(res){
								swal.close();
								if(res){
									ad  = JSON.parse(res);
									h   = '';
									h  += '<tr class="sol_ap_s_date"><td colspan="7"><div class="so_ap_s_cnt">';
									if( res === 'null' || res === '[]' ) {
										h  += '<header><strong>Sorry, No Slots Found.</strong></header>';
									} else {
										h  += '<header>Available Appointments <strong>'+sdate+'</strong></header>';
										h  += '<div class="solrow solarowhead">';
										h  += '<div class="solcol solacol_1">Service</div>';
										h  += '<div class="solcol solacol_2">Time</div>';
										h  += '<div class="solcol solacol_3">Space</div>';
										h  += '<div class="solcol solacol_4">Price</div>';
										h  += '<div class="solcol solacol_5">Book</div>';
										h  += '</div>';
									}
									if( 1==stype ){
										for(k in ad){
											for(i=0;i<ad[k].length;i++){
												title  = ad[k][i].t;
												desc   = ad[k][i].d;
												stime  = ad[k][i].st;
												etime  = ad[k][i].et;
												space  = ad[k][i].s;
												price  = ad[k][i].p;
												sid    = ad[k][i].q;
												tid    = k;
												tno    = i;
												aspace = ad[k][i].u;
												h += '<div class="solrow">';
												h += '<div class="solcol solacol_1"><span class="stitle tooltip">'+title+'<br><span class="tooltiptext">'+desc+'</span></span></div>';
												h += '<div class="solcol solacol_2"><span class="stime">'+stime+'</span><span class="setme">-</span><span class="etime">'+etime+'</span></div>';
												h += '<div class="solcol solacol_3"><span class="sspace">'+aspace+'</span></div>';
												h += '<div class="solcol solacol_4"><span class="sprice">'+price+'</span></div>';
												h += '<div class="solcol solacol_5"><a data-sid="'+sid+'" data-space="'+aspace+'" data-abcid="'+abcid+'" data-taskid="'+k+'" data-taskno="'+i+'" data-taskdate="'+fdate+'" data-price="'+price+'" href="javascript:void(0)" class="book-apppointment-now">Book Now</a></div>';
												h += '</div>';
											}    
										}
									} else{
										for(j=0;j<ad.length;j++){
											tid   = ad[j]['tid'];
											sdate = ad[j]['task'];
											slot  = ad[j]['slot'];
											title = sdate.t;
											desc  = sdate.d;
											for(l=0;l<slot.length;l++){
												sid    = slot[l].q;
												stime  = slot[l].s;
												etime  = slot[l].e;
												space  = slot[l].r;
												price  = slot[l].p;
												aspace = slot[l].u;
												h += '<div class="solrow">';
												h += '<div class="solcol solacol_1"><span class="stitle tooltip">'+title+'<span class="tooltiptext">'+desc+'</span></span></div>';
												h += '<div class="solcol solacol_2"><span class="stime">'+stime+'</span><span class="setme">-</span><span class="etime">'+etime+'</span></div>';
												h += '<div class="solcol solacol_3"><span class="sspace">'+aspace+'</span></div>';
												h += '<div class="solcol solacol_4"><span class="sprice">'+price+'</span></div>';
												h += '<div class="solcol solacol_5"><a data-sid="'+sid+'" data-space="'+aspace+'" data-abcid="'+abcid+'" data-taskid="'+tid    +'" data-taskno="'+l+'"  data-taskdate="'+fdate+'" data-price="'+price+'"  href="javascript:void(0)" class="book-apppointment-now">Book Now</a></div>';
												h += '</div>';
											}
										}
									}
									h+='</div></td></tr>';
									container.after(h);
								}
							}
						});
					}
				});
			}(jQuery))
		});
		</script>
		<div class="aior_payment_gateways">
			<?php
			do_action( 'aior_after_appointment_calendar', $id );
			do_action( 'aior_comment_form', $id );
			?>
		</div>
		<?php
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	/**
	 * Get appointment from ajax.
	 *
	 * @since    1.0.0
	 * @param    array $data data array.
	 */
	public function get_appointment_form_ajax( $data ) {
		$act = $data['act'];
		if ( 'aior_get_appointment_form' === $act ) {
			$id = $data['abcid'];
			Aior_Public::get_form( $id );
		}
	}

	/**
	 * Get appointment slot ajax.
	 *
	 * @since    1.0.0
	 * @param    array $data data array.
	 */
	public function get_appointment_slot_ajax( $data ) {
		$act = $data['act'];
		if ( 'get_day_appointment_data' == $act ) {
			$postid       = isset( $data['post_id'] ) ? $data['post_id'] : '';
			$slot_type    = isset( $data['slot_type'] ) ? $data['slot_type'] : '';
			$slot_weekly  = get_post_meta( $postid, 'slot_weekly', true );
			$sdate        = isset( $data['selected_date'] ) ? $data['selected_date'] : '';
			$service_name = isset( $data['service_name'] ) ? $data['service_name'] : '';
			$layout_type  = get_post_meta( $postid, 'layout_type', true );
			$new_data 	  = array();
			if ( 1 == $slot_type ) {
				$selected_day  = isset( $data['selected_day'] ) ? $data['selected_day'] : '';
				$all_slot_data = $slot_weekly[ $selected_day ];
				if ( ! empty( $all_slot_data ) ) {
					foreach ( $all_slot_data as $task_id => $slot ) {
						$i = 0;
						if ( 'design2' === $layout_type && ! empty( $service_name ) && $slot[0]['t'] === $service_name ) {
							$slot_count = count( $slot );
							for ( $i = 0; $i < $slot_count;$i++ ) {
								$islot                            = $slot[ $i ];
								$q                                = $islot['q'];
								$t                                = $islot['t'];
								$d                                = $islot['d'];
								$st                               = $islot['st'];
								$et                               = $islot['et'];
								$s                                = $islot['s'];
								$p                                = $islot['p'];
								$new_data[ $task_id ][ $i ]['q']  = $q;
								$new_data[ $task_id ][ $i ]['t']  = $t;
								$new_data[ $task_id ][ $i ]['d']  = $d;
								$new_data[ $task_id ][ $i ]['st'] = $st;
								$new_data[ $task_id ][ $i ]['et'] = $et;
								$new_data[ $task_id ][ $i ]['s']  = $s;
								$new_data[ $task_id ][ $i ]['p']  = $p;
								$u                                = self::get_available_slot( $postid, $task_id, $q, $selected_day );
								$new_data[ $task_id ][ $i ]['u']  = $u;
							}
						} elseif ( ( 'design1' === $layout_type && empty( $service_name ) || 'design2' === $layout_type && empty( $service_name ) ) || 'All' === $service_name ) {
							$slot_count = count( $slot );
							for ( $i = 0; $i < $slot_count;$i++ ) {
								$islot                            = $slot[ $i ];
								$q                                = $islot['q'];
								$t                                = $islot['t'];
								$d                                = $islot['d'];
								$st                               = $islot['st'];
								$et                               = $islot['et'];
								$s                                = $islot['s'];
								$p                                = $islot['p'];
								$new_data[ $task_id ][ $i ]['q']  = $q;
								$new_data[ $task_id ][ $i ]['t']  = $t;
								$new_data[ $task_id ][ $i ]['d']  = $d;
								$new_data[ $task_id ][ $i ]['st'] = $st;
								$new_data[ $task_id ][ $i ]['et'] = $et;
								$new_data[ $task_id ][ $i ]['s']  = $s;
								$new_data[ $task_id ][ $i ]['p']  = $p;
								$u                                = self::get_available_slot( $postid, $task_id, $q, $selected_day );
								$new_data[ $task_id ][ $i ]['u']  = $u;
							}
						}
					}
					echo wp_json_encode( $new_data );
				}
			} else {
				$new_data = array(
					'post_id'      => $postid,
					'date'         => $sdate,
					'service_name' => $service_name,
				);
				self::get_montly_task_for_ajax( $new_data );
			}
		}
	}
	/**
	 * Get Monthly Task for ajax.
	 *
	 * @since    1.0.0
	 * @param    array $data data array.
	 */
	public static function get_montly_task_for_ajax( $data ) {
		$pid           = $data['post_id'];
		$sdate         = $data['date'];
		$service_name  = $data['service_name'];
		$slot_monthly  = get_post_meta( $pid, 'slot_montly_list', true );
		$layout_type   = get_post_meta( $pid, 'layout_type', true );
		$all_slot_data = array();
		$timestamp     = array();
		$i             = 0;
		foreach ( $slot_monthly as $task_id => $task ) {
			$start_d         = isset( $task['s'] ) ? $task['s'] : '';
			$end_d           = isset( $task['e'] ) ? $task['e'] : '';
			$title           = isset( $task['t'] ) ? $task['t'] : '';
			$desc            = isset( $task['d'] ) ? $task['d'] : '';
			$price           = isset( $task['p'] ) ? $task['p'] : '';
			$slotmtask       = 'slotmtask_' . $task_id;
			$task_detais     = get_post_meta( $pid, $slotmtask, true );
			$tsselected_date = strtotime( $sdate );
			$timestamp_start = strtotime( $start_d );
			$timestamp_end   = strtotime( $end_d );
			if ( 'design2' === $layout_type && ! empty( $service_name ) && $task['t'] === $service_name ) {
				$slot_count = count( $task_detais );
				for ( $i = 0; $i < $slot_count; $i++ ) {
					$islot           = $task_detais[ $i ];
					$q               = $islot['q'];
					$s               = $islot['s'];
					$e               = $islot['e'];
					$r               = $islot['r'];
					$p               = $islot['p'];
					$ntsk[ $i ]['q'] = $q;
					$ntsk[ $i ]['s'] = $s;
					$ntsk[ $i ]['e'] = $e;
					$ntsk[ $i ]['r'] = $r;
					$ntsk[ $i ]['p'] = $p;
					$u               = self::get_available_slot( $pid, $task_id, $q, 'month' );
					$ntsk[ $i ]['u'] = $u;
				}

				$temp = array(
					'tid'  => $task_id,
					'task' => $task,
					'slot' => $ntsk,
				);
				if ( $tsselected_date >= $timestamp_start && $tsselected_date <= $timestamp_end ) {
					array_push( $all_slot_data, $temp );
				}
				$i++;
			} elseif ( ( 'design1' === $layout_type && empty( $service_name ) || 'design2' === $layout_type && empty( $service_name ) ) || 'All' === $service_name ) {
				$slot_count = count( $task_detais );
				for ( $i = 0; $i < $slot_count; $i++ ) {
					$islot           = $task_detais[ $i ];
					$q               = $islot['q'];
					$s               = $islot['s'];
					$e               = $islot['e'];
					$r               = $islot['r'];
					$p               = $islot['p'];
					$ntsk[ $i ]['q'] = $q;
					$ntsk[ $i ]['s'] = $s;
					$ntsk[ $i ]['e'] = $e;
					$ntsk[ $i ]['r'] = $r;
					$ntsk[ $i ]['p'] = $p;
					$u               = self::get_available_slot( $pid, $task_id, $q, 'month' );
					$ntsk[ $i ]['u'] = $u;
				}

				$temp = array(
					'tid'  => $task_id,
					'task' => $task,
					'slot' => $ntsk,
				);
				if ( $tsselected_date >= $timestamp_start && $tsselected_date <= $timestamp_end ) {
					array_push( $all_slot_data, $temp );
				}
				$i++;
			}
		}
		echo wp_json_encode( $all_slot_data );
	}
	/**
	 * Save settings.
	 *
	 * @since    1.0.0
	 * @param    int $post_id post id.
	 */
	public function aior_save_settings( $post_id ) {
		$nonce = ( isset( $_POST['res_form_nonce'] ) && ! empty( $_POST['res_form_nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['res_form_nonce'] ) ) : '';
		if ( wp_verify_nonce( $nonce, 'res_form_nonce_action' ) ) {
			$slot_type = isset( $_POST['slot_type'] ) ? sanitize_text_field( wp_unslash( $_POST['slot_type'] ) ) : '';
			if ( isset( $_POST['slot_weekly'] ) && ! empty( $_POST['slot_weekly'] ) ) {
				foreach ( $_POST['slot_weekly'] as $task_id => $tasks ) {
					foreach ( $tasks as $ttask_id => $ttask ) {
						foreach ( $ttask as $tttask_id => $tttask ) {
							$slot_weekly[ $task_id ][ $ttask_id ][ $tttask_id ] = array_map( 'sanitize_text_field', wp_unslash( $tttask ) );
						}
					}
				}
			}
			if ( isset( $_POST['slot_monthly'] ) && ! empty( $_POST['slot_monthly'] ) ) {
				foreach ( $_POST['slot_monthly'] as $task_id => $tasks ) {
					foreach ( $tasks as $ttask_id => $ttask ) {
						$slot_monthly[ $task_id ][ $ttask_id ] = array_map( 'sanitize_text_field', wp_unslash( $ttask ) );
					}
				}
			}
			update_post_meta( $post_id, 'slot_type', $slot_type );
			update_post_meta( $post_id, 'slot_weekly', $slot_weekly );
			$slot_montly_list = array();
			if ( ! empty( $slot_monthly ) ) {
				$task_id = 0;
				foreach ( $slot_monthly as $task_id => $tasks ) {
					foreach ( $tasks as $task ) {
						$sd                           = $task['sd'];
						$ed                           = $task['ed'];
						$t                            = $task['t'];
						$d                            = $task['d'];
						$slot_montly_list[ $task_id ] = array(
							's' => $sd,
							'e' => $ed,
							't' => $t,
							'd' => $d,
						);
					}
					self::update_task_in_meta( $post_id, $tasks, $task_id );
				}
				update_post_meta( $post_id, 'slot_montly_list', $slot_montly_list );
			}
		}
	}

	/**
	 * Update task in meta.
	 *
	 * @since    1.0.0
	 * @param    int   $post_id post id.
	 * @param    array $tasks tasks.
	 * @param    int   $task_id task id.
	 */
	public static function update_task_in_meta( $post_id, $tasks, $task_id ) {
		$k         = 0;
		$task_meta = 'slotmtask_' . $task_id;
		foreach ( $tasks as $task ) {
			/* Divide data */
			$q                = $task['q'];
			$st               = $task['st'];
			$et               = $task['et'];
			$s                = $task['s'];
			$p                = $task['p'];
			$task_value[ $k ] = array(
				'q' => $q,
				's' => $st,
				'e' => $et,
				'r' => $s,
				'p' => $p,
			);
			$k++;
		}
		$task_meta = 'slotmtask_' . $task_id;
		update_post_meta( $post_id, $task_meta, $task_value );
	}
	/**
	 * Generate day slot.
	 *
	 * @since    1.0.0
	 * @param    array $data post id.
	 */
	public function generate_day_slot( $data ) {
		$act = $data['act'];
		if ( 'aior_generate_day_slot' === $act ) {
			$start    = $data['start'];
			$end      = $data['end'];
			$duration = $data['duration'];
			$interval = $data['interval'];
			$slots    = self::split_time( $start, $end, $duration, $interval );
			$i        = 0;
			foreach ( $slots as $k => $v ) {
				$slot[ $i ] = array( $k, $v );
				$i++;
			}
			if ( ! empty( $slot ) ) {
				echo wp_json_encode( $slot ); /*if required may use JSON_PRETTY_PRINT */
			}
		}
	}
	/**
	 * Generate Monthly Slot.
	 *
	 * @since    1.0.0
	 * @param    array $data data array.
	 */
	public function generate_montly_slot( $data ) {
		$act = $data['act'];
		if ( 'aior_generate_montly_slot' === $act ) {
			$start    = $data['start'];
			$end      = $data['end'];
			$duration = $data['duration'];
			$interval = $data['interval'];
			$slots    = self::split_time( $start, $end, $duration, $interval );
			$i        = 0;
			foreach ( $slots as $k => $v ) {
				$slot[ $i ] = array( $k, $v );
				$i++;
			}
			if ( ! empty( $slot ) ) {
				echo wp_json_encode( $slot ); /*if required may use JSON_PRETTY_PRINT */
			}
		}
	}
	/**
	 * Remove Monthly Task.
	 *
	 * @since    1.0.0
	 * @param    array $data data array.
	 */
	public function remove_montly_task( $data ) {
		$act = $data['act'];
		if ( 'aior_remove_montly_task' === $act ) {
			$pid    = $data['pid'];
			$target = $data['target'];
			$tg                = 'slotmtask_' . $target;
			$slot_monthly_list = get_post_meta( $pid, 'slot_montly_list', true );
			if ( $pid ) {
				unset( $slot_monthly_list[ $target ] );
				update_post_meta( $pid, 'slot_montly_list', $slot_monthly_list );
				delete_post_meta( $pid, $tg );
			}
			echo 'removed';
		}
	}
	/**
	 * Slot Builder Area.
	 *
	 * @since    1.0.0
	 */
	public static function slot_builder_area() {
		$aior_builder = new Aior_Builder();
		$rf_data      = Aior_Reservation_Form::get_settings();
		wp_nonce_field( 'on_aior_admin_global_nonce', 'aior_admin_global_nonce' );
		$pid = get_the_ID();
		?>
			<input type="hidden" id="possible_post_id" value="<?php esc_attr( $pid ); ?>">
			<div class="form-group aior_slot_type">
				<label class="control-label"><?php esc_html_e( 'Select Slot Type', 'all-in-one-reservation' ); ?></label>
				<?php
					$slot_type = isset( $rf_data['slot_type'] ) ? $rf_data['slot_type'] : '1';
					$aior_builder->add_field(
						array(
							'type'     => 'radio',
							'name'     => 'slot_type',
							'class'    => 'form-check-input',
							'value'    => $slot_type,
							'selected' => $slot_type,
							'default'  => 1,
							'option'   => array(
								esc_html__( 'Weekly', 'all-in-one-reservation' ) => 1,
								esc_html__( 'Monthly', 'all-in-one-reservation' ) => 2,
							),
						)
					);
				?>
			</div>
			<div class="slot_weekly_cnt">
				<h3><?php esc_html_e( 'Weekly Time Slot', 'all-in-one-reservation' ); ?></h3>
				<div class="slot_weekly">
					<div class="form-group rest_row solrow2">
						<div class="solcol2"><label class="control-label aior_weekly_slot"><label class="control-label"><?php esc_html_e( 'Monday', 'all-in-one-reservation' ); ?></label><ul><?php self::get_day_task( 'monday' ); ?></ul><a class="aior_add_weekly_slot button button-primary button-large" href="javascript:void(0)" data-day="monday"><?php esc_html_e( 'Add Task', 'all-in-one-reservation' ); ?></a></div>
						<div class="solcol2"><label class="control-label aior_weekly_slot"><label class="control-label"><?php esc_html_e( 'Tuesday', 'all-in-one-reservation' ); ?></label><ul><?php self::get_day_task( 'tuesday' ); ?></ul><a class="aior_add_weekly_slot button button-primary button-large" href="javascript:void(0)" data-day="tuesday"><?php esc_html_e( 'Add Task', 'all-in-one-reservation' ); ?></a> </div>
						<div class="solcol2"><label class="control-label aior_weekly_slot"><label class="control-label"><?php esc_html_e( 'Wednesday', 'all-in-one-reservation' ); ?></label><ul><?php self::get_day_task( 'wednesday' ); ?></ul><a class="aior_add_weekly_slot button button-primary button-large" href="javascript:void(0)" data-day="wednesday"><?php esc_html_e( 'Add Task', 'all-in-one-reservation' ); ?></a> </div>
						<div class="solcol2"><label class="control-label aior_weekly_slot"><label class="control-label"><?php esc_html_e( 'Thursday', 'all-in-one-reservation' ); ?></label><ul><?php self::get_day_task( 'thursday' ); ?></ul><a class="aior_add_weekly_slot button button-primary button-large" href="javascript:void(0)" data-day="thursday"><?php esc_html_e( 'Add Task', 'all-in-one-reservation' ); ?></a> </div>
						<div class="solcol2"><label class="control-label aior_weekly_slot"><label class="control-label"><?php esc_html_e( 'Friday', 'all-in-one-reservation' ); ?></label><ul><?php self::get_day_task( 'friday' ); ?></ul><a class="aior_add_weekly_slot button button-primary button-large" href="javascript:void(0)" data-day="friday"><?php esc_html_e( 'Add Task', 'all-in-one-reservation' ); ?></a> </div>
						<div class="solcol2"><label class="control-label aior_weekly_slot"><label class="control-label"><?php esc_html_e( 'Saturday', 'all-in-one-reservation' ); ?></label><ul><?php self::get_day_task( 'saturday' ); ?></ul><a class="aior_add_weekly_slot button button-primary button-large" href="javascript:void(0)" data-day="saturday"><?php esc_html_e( 'Add Task', 'all-in-one-reservation' ); ?></a> </div>
						<div class="solcol2"><label class="control-label aior_weekly_slot"><label class="control-label"><?php esc_html_e( 'Sunday', 'all-in-one-reservation' ); ?></label><ul><?php self::get_day_task( 'sunday' ); ?></ul><a class="aior_add_weekly_slot button button-primary button-large" href="javascript:void(0)" data-day="sunday"><?php esc_html_e( 'Add Task', 'all-in-one-reservation' ); ?></a> </div>
					</div>
				</div>
			</div>
			<div class="slot_monthly_cnt aior_cftheme_2">
				<h3><?php esc_html_e( 'Monthly Time Slot', 'all-in-one-reservation' ); ?></h3>
				<div class="slot_monthly">
					<ul>
					<?php
						self::get_montly_task();
					?>
					</ul>
					<a class="aior_add_monthly_slot button button-primary button-large" href="javascript:void(0)"><?php esc_html_e( 'Add Custom Slot', 'all-in-one-reservation' ); ?></a>
				</div>
			</div>
		<?php
	}
	/**
	 * Get day task.
	 *
	 * @since    1.0.0
	 * @param    string $cday day.
	 */
	public static function get_day_task( $cday ) {
		$slot_weekly = get_post_meta( get_the_ID(), 'slot_weekly', true );
		$slot_weekly = isset( $slot_weekly ) && is_array( $slot_weekly ) ? $slot_weekly : '';
		$args_kses = Aior_Core::args_kses();
		if ( $slot_weekly ) {
			foreach ( $slot_weekly as $day_name => $day ) {
				if ( $day_name === $cday ) {
					foreach ( $day as $task_id => $tasks ) {
						if ( $tasks ) {
							$k = 0;
							foreach ( $tasks as $task ) {
								if ( 'monday' === $day_name || 'tuesday' === $day_name || 'wednesday' === $day_name || 'thursday' === $day_name || 'friday' === $day_name || 'saturday' === $day_name || 'sunday' === $day_name ) {
									$sid   = isset( $task['q'] ) ? $task['q'] : '';
									$title = isset( $task['t'] ) ? $task['t'] : '';
									$desc  = isset( $task['d'] ) ? $task['d'] : '';
									$from  = isset( $task['st'] ) ? $task['st'] : '';
									$to    = isset( $task['et'] ) ? $task['et'] : '';
									$space = isset( $task['s'] ) ? $task['s'] : '';
									$price = isset( $task['p'] ) ? $task['p'] : '';
									$h     = '<li class="slot_weekly">';
									$h    .= '<b>' . esc_html( $title ) . '</b><br>';
									$h    .= '<small>' . esc_html( $desc ) . '</small><br>';
									$h    .= '<input class="slot_id" type="hidden" name="slot_weekly[' . esc_attr( $day_name ) . '][' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][q]" value="' . esc_attr( $sid ) . '">';
									$h    .= '<input class="slot_title" type="hidden" name="slot_weekly[' . esc_attr( $day_name ) . '][' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][t]" value="' . esc_attr( $title ) . '">';
									$h    .= '<input class="slot_desc" type="hidden" name="slot_weekly[' . esc_attr( $day_name ) . '][' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][d]" value="' . esc_attr( $desc ) . '">';
									$h    .= '<div class="solrow">';
									$h    .= '<div class="solcol"><label>' . esc_html__( 'Start Time', 'all-in-one-reservation' ) . '</label><input class="slot_start timepicker" type="text" name="slot_weekly[' . esc_attr( $day_name ) . '][' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][st]" placeholder="' . esc_attr__( 'Start Time', 'all-in-one-reservation' ) . '" value="' . esc_attr( $from ) . '"></div>';
									$h    .= '<div class="solcol"><label>' . esc_html__( 'End Time', 'all-in-one-reservation' ) . '</label><input class="slot_end timepicker" type="text" name="slot_weekly[' . esc_attr( $day_name ) . '][' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][et]" placeholder="' . esc_attr__( 'End Time', 'all-in-one-reservation' ) . '" value="' . esc_attr( $to ) . '"></div>';
									$h    .= '</div>';
									$h    .= '<label>Available Space</label><input class="slot_space" type="text" name="slot_weekly[' . esc_attr( $day_name ) . '][' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][s]" placeholder="' . esc_attr__( 'Available Space', 'all-in-one-reservation' ) . '" value="' . esc_attr( $space ) . '">';
									$h    .= '<label>Price</label><input class="slot_price" type="text" name="slot_weekly[' . esc_attr( $day_name ) . '][' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][p]" placeholder="' . esc_attr__( 'Price', 'all-in-one-reservation' ) . '" value="' . esc_attr( $price ) . '">';
									$h    .= '<a class="button aior_slot_close">Close</a>';
									$h    .= '</li>';
								}
								echo wp_kses($h, $args_kses);
								$k++;
							}
						}
					}
				}
			}
		}
	}
	/**
	 * Get montly task.
	 *
	 * @since    1.0.0
	 */
	public static function get_montly_task() {
		$pid          = get_the_ID();
		$slot_monthly = get_post_meta( $pid, 'slot_montly_list', true );

		if ( $slot_monthly ) {
			foreach ( $slot_monthly as $task_id => $task ) {
				$start_d     = isset( $task['s'] ) ? $task['s'] : '';
				$end_d       = isset( $task['e'] ) ? $task['e'] : '';
				$title       = isset( $task['t'] ) ? $task['t'] : '';
				$desc        = isset( $task['d'] ) ? $task['d'] : '';
				$slotmtask   = 'slotmtask_' . $task_id;
				$task_detais = get_post_meta( $pid, $slotmtask, true );
				$k           = 0;
				echo '<li class="slot_monthly">';
				echo '<header>';
				echo '<h3>' . esc_html( $title ) . ' : <small>' . esc_html( $desc ) . '</small></h3>';
				echo esc_html( $start_d . ' to ' . $end_d );
				echo '<a data-pid="' . esc_attr( $pid ) . '" data-target="' . esc_attr( $task_id ) . '" href="javascript:void(0)" class="slot_m_remove"><span class="dashicons dashicons-trash"></span></a>';
				echo '</header>';

				if ( ! is_array( $task_detais ) ) {
					$task_detais = unserialize( $task_detais );
				}

				foreach ( $task_detais as $detail ) {
					$slot_id = isset( $detail['q'] ) ? $detail['q'] : '';
					$from    = isset( $detail['s'] ) ? $detail['s'] : '';
					$to      = isset( $detail['e'] ) ? $detail['e'] : '';
					$space   = isset( $detail['r'] ) ? $detail['r'] : '';
					$price   = isset( $detail['p'] ) ? $detail['p'] : '';
					echo '<div class="sm_slot">';
						echo '<div class="solrow">';
							echo '<div class="solcol" style="display:none">';
							echo '<b>' . esc_html( $title ) . '</b><br>';
							echo '<small>' . esc_html( $desc ) . '</small><br>';
							echo esc_html( $start_d . ' to ' . $end_d );
							echo '<input type="hidden" name="slot_monthly[' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][q]" value="' . esc_attr( $slot_id ) . '">';
							echo '<input type="hidden" name="slot_monthly[' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][sd]" value="' . esc_attr( $start_d ) . '">';
							echo '<input type="hidden" name="slot_monthly[' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][ed]" value="' . esc_attr( $end_d ) . '">';
							echo '<input class="slot_title" type="hidden" name="slot_monthly[' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][t]" value="' . esc_attr( $title ) . '">';
							echo '<input class="slot_desc" type="hidden" name="slot_monthly[' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][d]" value="' . esc_attr( $desc ) . '">';
							echo '</div>';

							echo '<div class="solcol"><label>Start Time</label><input class="slot_start timepicker" type="text" name="slot_monthly[' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][st]" placeholder="' . esc_attr__( 'Start Time', 'all-in-one-reservation' ) . '" value="' . esc_attr( $from ) . '"></div>';
							echo '<div class="solcol"><label>End Time</label><input class="slot_end timepicker" type="text" name="slot_monthly[' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][et]" placeholder="' . esc_attr__( 'End Time', 'all-in-one-reservation' ) . '" value="' . esc_attr( $to ) . '"></div>';

								echo '<div class="solcol">';
							echo '<label>' . esc_html__( 'Available Space', 'all-in-one-reservation' ) . '</label><input class="slot_space" type="text" name="slot_monthly[' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][s]" placeholder="' . esc_attr__( 'Available Space', 'all-in-one-reservation' ) . '" value="' . esc_attr( $space ) . '">';
							echo '</div>';

							echo '<div class="solcol">';
							echo '<label>' . esc_html__( 'Price', 'all-in-one-reservation' ) . '</label><input class="slot_price" type="text" name="slot_monthly[' . esc_attr( $task_id ) . '][' . esc_attr( $k ) . '][p]" placeholder="' . esc_attr__( 'Price', 'all-in-one-reservation' ) . '" value="' . esc_attr( $price ) . '">';
							echo '</div>';

							echo '<div class="solcol">';
							echo '<a class="button aior_slot_close_m">' . esc_html__( 'Close', 'all-in-one-reservation' ) . '</a>';
							echo '</div>';

						echo '</div>';
					echo '</div>';
					$k++;
				}
				echo '</li>';
			}
		}
	}
	/**
	 * Get the slot form.
	 *
	 * @since    1.0.0
	 * @param    string $start_time start time.
	 * @param    string $end_time start time.
	 * @param    string $duration start time.
	 * @param    string $interval start time.
	 * @return $return
	 */
	public static function split_time( $start_time, $end_time, $duration, $interval ) {
		$post_id    = get_the_ID();
		$clock_hour = get_post_meta( $post_id, 'clock_hour', true );
		$js_format  = $clock_hour ? $clock_hour : 'hh:mm p';       /* format need to get from settings */
		if ( 'hh:mm p' === $js_format ) {
			$format = 'h:i a';
		} else {
			$format = 'H:i';
		}
		$return     = array();                 // Define output.
		$start_time = strtotime( $start_time );  // Get Timestamp.
		$end_time   = strtotime( $end_time );    // Get Timestamp.
		$add_mins   = $duration * 60;
		$interval   = $interval * 60;
		while ( $start_time <= $end_time ) {
			$from            = gmdate( $format, $start_time );
			$to              = gmdate( $format, $start_time + $add_mins );
			$return[ $from ] = $to;
			$start_time     += $interval;
			$start_time     += $add_mins; // Endtime check.
		}
		return $return;
	}
	/**
	 * Check is Weekly.
	 *
	 * @since    1.0.0
	 * @param    string $fid fid.
	 * @return boolean
	 */
	public static function is_weekly( $fid ) {
		$slot_type = get_post_meta( $fid, 'slot_type', true );
		if ( 1 == $slot_type ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Check is Weekly.
	 *
	 * @since    1.0.0
	 * @param    string $fid fid.
	 * @param    string $tid tid.
	 * @param    string $sid sid.
	 * @return boolean
	 */
	public static function get_slot_data( $fid, $tid, $sid ) {
		$single_slot = false;
		if ( self::is_weekly( $fid ) ) {
			$task = get_post_meta( $fid, 'slot_weekly', true );
			if ( $task ) {
				$days = array( 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday' );
				foreach ( $days as $day ) {
					if ( array_key_exists( $day, $task ) ) {
						$weekday = $task[ $day ];
						if ( array_key_exists( $tid, $weekday ) ) {
							$slot = $weekday[ $tid ];
							foreach ( $slot as $ts ) {
								if ( $ts['q'] === $sid ) {
									$single_slot = array(
										'slot'       => $ts['q'],
										'title'      => $ts['t'],
										'desc'       => $ts['d'],
										'start_time' => $ts['st'],
										'end_time'   => $ts['et'],
										'space'      => $ts['s'],
										'price'      => $ts['p'],
									);
								}
							}
						}
					}
				}
			}
		} else {
			$monthly = get_post_meta( $fid, 'slot_montly_list', true );
			$task    = get_post_meta( $fid, 'slotmtask_' . $tid, true );
			$title   = '';
			$desc    = '';
			if ( $monthly ) {
				foreach ( $monthly as $key => $value ) {
					if ( $key === $tid ) {
						$title = $value['t'];
						$desc  = $value['d'];
					}
				}
			}
			if ( $task ) {
				foreach ( $task as $ts ) {
					if ( $ts['q'] === $sid ) {
						$single_slot = array(
							'slot'       => $ts['q'],
							'title'      => $title,
							'desc'       => $desc,
							'start_time' => $ts['s'],
							'end_time'   => $ts['e'],
							'space'      => $ts['r'],
							'price'      => $ts['p'],
						);
					}
				}
			}
		}
		return $single_slot;
	}
	/**
	 * Get Booking Data through ID.
	 *
	 * @since    1.0.0
	 * @param    int $bid Booking ID.
	 */
	public static function get_booking_data( $bid ) {
		$first_name        = get_post_meta( $bid, 'rf_first_name', true );
		$last_name         = get_post_meta( $bid, 'rf_last_name', true );
		$email             = get_post_meta( $bid, 'rf_email', true );
		$phone             = get_post_meta( $bid, 'rf_phone_no', true );
		$note              = get_post_meta( $bid, 'rf_note', true );
		$status            = get_post_meta( $bid, 'status', true );
		$fid               = get_post_meta( $bid, 'form_id', true );
		$sid               = get_post_meta( $bid, 'sid', true );
		$tid               = get_post_meta( $bid, 'tid', true );
		$date              = get_post_meta( $bid, 'tdt', true );
		$price             = get_post_meta( $bid, 'price', true );
		$guest             = get_post_meta( $bid, 'rf_slot', true );
		$slot              = self::get_slot_data( $fid, $tid, $sid );
		$booking_time_from = $slot['start_time'];
		$booking_time_to   = $slot['end_time'];
		$data              = array(
			'reservation_id'    => $bid,
			'rf_first_name'     => $first_name,
			'rf_last_name'      => $last_name,
			'rf_email'          => $email,
			'phone_no'          => $phone,
			'rf_note'           => $note,
			'number_guest'      => $guest,
			'booking_date'      => $date,
			'booking_time_from' => $booking_time_from,
			'booking_time_to'   => $booking_time_to,
			'price'             => $price,
			'status'            => $status,
			'sid'               => $sid,
			'tid'               => $tid,
		);
		return $data;
	}
	/**
	 * Default Form JSON.
	 *
	 * @since    1.0.0
	 */
	public function default_form() {
		return '{"0":{"0":{"0":{"t":"number","n":"rf_slot","l":"Number of Person","p":"","r":"1","c":"","m":"You must select number of person you want to book","x":"1","y":""}}},"1":{"0":{"0":{"t":"text","n":"rf_first_name","l":"First Name","p":"First Name","r":"1","c":"","m":"Please add your fist name"}},"1":{"0":{"t":"text","n":"rf_last_name","l":"Last Name","p":"Last Name","r":"1","c":"","m":"Please add your last name"}}},"2":{"0":{"0":{"t":"email","n":"rf_email","l":"Email","p":"Email","r":"1","c":"","m":"You must add your email id"}},"1":{"0":{"t":"text","n":"rf_phone_no","l":"Phone No","p":"Phone No.","r":"1","c":"","m":"Add your phone number.","x":"","y":""}}},"3":{"0":{"0":{"t":"textarea","n":"rf_note","l":"Note","p":"Enter your reservation note","r":"","c":"","m":""}}}}';
	}
	/**
	 * Register Style.
	 *
	 * @since    1.0.0
	 */
	public function appointment_booking_styles() {
		wp_register_style( 'aior-appointment-booking', plugin_dir_url( __FILE__ ) . '/src/admin/appointment-booking.css', null, '1.0' );
		wp_enqueue_style( 'aior-appointment-booking' );
	}
	/**
	 * Register Script.
	 *
	 * @since    1.0.0
	 */
	public function appointment_booking_scripts() {
		wp_register_script( 'aior-appointment-booking', plugin_dir_url( __FILE__ ) . '/src/admin/appointment-booking.js', array( 'aior-hooks' ), '1.0', true );
		wp_enqueue_script( 'aior-appointment-booking' );
	}
	/**
	 * Register Style.
	 *
	 * @since    1.0.0
	 */
	public function appointment_booking_styles_public() {
		wp_register_style( 'aior-appointment-booking-public', plugin_dir_url( __FILE__ ) . '/src/public/appointment-booking-public.css', null, '1.0' );
		wp_enqueue_style( 'aior-appointment-booking-public' );
	}
	/**
	 * Register Script.
	 *
	 * @since    1.0.0
	 */
	public function appointment_booking_scripts_public() {
		wp_register_script( 'aior-appointment-booking-public', plugin_dir_url( __FILE__ ) . '/src/public/appointment-booking-public.js', array( 'aior-hooks' ), '1.0', true );
		wp_enqueue_script( 'aior-appointment-booking-public' );
	}
}
$aior_appointment_booking = new Aior_Appointment_Booking();
require plugin_dir_path( __FILE__ ) . 'class-aior-appointment-widget.php';
