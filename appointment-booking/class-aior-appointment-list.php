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
class Aior_Appointment_List {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'custom_post_type_appointment' ), 0 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box_reseveration' ) );
		add_action(
			'aior_before_admin_menu',
			function ( $r_menu ) {
				$notification_count = apply_filters( 'aior_new_appointment_notification', '' );
				add_submenu_page(
					$r_menu,
					esc_html__( 'Appointment List', 'all-in-one-reservation' ),
					$notification_count ? sprintf( esc_html__( 'Appointment List', 'all-in-one-reservation' ) . '<span class="awaiting-mod">%d</span>', $notification_count ) : esc_html__( 'Appointment List', 'all-in-one-reservation' ),
					'manage_options',
					'edit.php?post_type=sol_appointment_list'
				);
			}
		);

		add_action( 'manage_sol_appointment_list_posts_custom_column', array( $this, 'appointment_list_custom_column' ), 10, 2 );
		add_filter( 'manage_sol_appointment_list_posts_columns', array( $this, 'sol_appointment_list_columns' ) );
		add_filter( 'manage_edit-sol_appointment_list_sortable_columns', array( $this, 'sortable_appointment_list_column' ), 10, 2 );
		add_action( 'aoir_reservation_global_ajax', array( $this, 'get_booked_appointment_action_ajax' ) );
		add_shortcode( 'aior_cancel_page', array( $this, 'cancel_reservation' ) );
		add_filter( 'posts_where', array( $this, 'sol_appointment_list_search_where' ), 10, 2 );
		add_filter( 'posts_join', array( $this, 'sol_appointment_list_search_join' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'aior_remove_bulk_actions' ), 10, 2 );
		add_action( 'bulk_actions-edit-sol_appointment_list', array( $this, 'aior_remove_default_bulk_actions' ) );
	}

	/**
	 * Remove Quick Edit from Row Actions.
	 *
	 * @since    1.0.0
	 * @param array $bulk_array Actions.
	 */
	public function aior_remove_default_bulk_actions( $bulk_array ) {
		unset( $bulk_array['edit'] );
		return $bulk_array;
	}
	/**
	 * Remove Quick Edit from Row Actions.
	 *
	 * @since    1.0.0
	 * @param array $actions Actions.
	 * @param array $post Post.
	 */
	public function aior_remove_bulk_actions( $actions, $post ) {
		if ( 'sol_appointment_list' == $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}
	/**
	 * Get Booked Appointment Action for Ajax.
	 *
	 * @since    1.0.0
	 * @param array $data Appointment Data.
	 */
	public function get_booked_appointment_action_ajax( $data ) {
		$act = $data['act'];
		if ( 'do_booked_appointment_act' === $act ) {
			$todo   = $data['todo'];
			$pid    = $data['pid'];
			$status = '';
			if ( 'approve' === $todo ) {
				update_post_meta( $pid, 'status', 'approved' );
				$status = array(
					'status'  => 'success',
					'message' => esc_html__( 'Appointment approved.', 'all-in-one-reservation' ),
					'data'    => $pid,
					'act'     => 'approved',
				);
				echo wp_json_encode( $status );
				do_action( 'aior_appointment_approved', $pid );
			} elseif ( 'decline' === $todo ) {
				update_post_meta( $pid, 'status', 'declined' );
				$status = array(
					'status'  => 'success',
					'message' => esc_html__( 'Appointment declined.', 'all-in-one-reservation' ),
					'data'    => $pid,
					'act'     => 'declined',
				);
				echo wp_json_encode( $status );
				do_action( 'aior_appointment_declined', $pid );
			}
		}
	}
	/**
	 * Cancel The Reservation.
	 *
	 * @since    1.0.0
	 */
	public function cancel_reservation() {
		if ( isset( $_GET['cancel'] ) ) {
			if ( isset( $_GET['rid'] ) ) {
				$cancel = isset( $_GET['cancel'] ) ? sanitize_text_field( wp_unslash( $_GET['cancel'] ) ) : '';
				$rid    = isset( $_GET['rid'] ) ? sanitize_text_field( wp_unslash( $_GET['rid'] ) ) : '';
				if ( $cancel && $rid ) {
					$rid         = Aior_Core::decrypt( $rid, 'slienceisgold', true );
					$is_canceled = get_post_meta( $rid, 'status', true );
					$fid         = get_post_meta( $rid, 'form_id', true );
					$data        = Aior_Appointment_Booking::get_booking_data( $rid );
					$message     = get_post_meta( $fid, 'cancel_email_body', true );
					$message     = Aior_Notification::template_vars_replacement( $fid, $message, $data );
					if ( 'canceled' !== $is_canceled ) {
						update_post_meta( $rid, 'status', 'canceled' );
						do_action( 'aior_appointment_declined', $rid );
						return $message;
					} else {
						return $message;
					}
				}
			}
		}
	}
	/**
	 * Register the Custom Post Type.
	 *
	 * @since    1.0.0
	 */
	public function custom_post_type_appointment() {
		$labels = array(
			'name'                  => _x( 'Appointment', 'Post Type General Name', 'all-in-one-reservation' ),
			'singular_name'         => _x( 'Appointment', 'Post Type Singular Name', 'all-in-one-reservation' ),
			'menu_name'             => esc_html__( 'Appointment', 'all-in-one-reservation' ),
			'name_admin_bar'        => esc_html__( 'Appointment', 'all-in-one-reservation' ),
			'archives'              => esc_html__( 'Appointment Archives', 'all-in-one-reservation' ),
			'attributes'            => esc_html__( 'Appointment Attributes', 'all-in-one-reservation' ),
			'parent_item_colon'     => esc_html__( 'Parent Appointment:', 'all-in-one-reservation' ),
			'all_items'             => esc_html__( 'All Appointment', 'all-in-one-reservation' ),
			'add_new_item'          => esc_html__( 'Add New Appointment', 'all-in-one-reservation' ),
			'add_new'               => esc_html__( 'Add New', 'all-in-one-reservation' ),
			'new_item'              => esc_html__( 'New Appointment', 'all-in-one-reservation' ),
			'edit_item'             => esc_html__( 'Edit Appointment', 'all-in-one-reservation' ),
			'update_item'           => esc_html__( 'Update Appointment', 'all-in-one-reservation' ),
			'view_item'             => esc_html__( 'View Appointment', 'all-in-one-reservation' ),
			'view_items'            => esc_html__( 'View Appointments', 'all-in-one-reservation' ),
			'search_items'          => esc_html__( 'Search Appointment', 'all-in-one-reservation' ),
			'not_found'             => esc_html__( 'No Appointment Found', 'all-in-one-reservation' ),
			'not_found_in_trash'    => esc_html__( 'No Appointment Found In Trash', 'all-in-one-reservation' ),
			'featured_image'        => esc_html__( 'Featured Image', 'all-in-one-reservation' ),
			'set_featured_image'    => esc_html__( 'Set featured image', 'all-in-one-reservation' ),
			'remove_featured_image' => esc_html__( 'Remove featured image', 'all-in-one-reservation' ),
			'use_featured_image'    => esc_html__( 'Use as featured image', 'all-in-one-reservation' ),
			'insert_into_item'      => esc_html__( 'Insert into Appointment', 'all-in-one-reservation' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this Appointment', 'all-in-one-reservation' ),
			'items_list'            => esc_html__( 'Appointment list', 'all-in-one-reservation' ),
			'items_list_navigation' => esc_html__( 'Appointment list navigation', 'all-in-one-reservation' ),
			'filter_items_list'     => esc_html__( 'Filter Appointment list', 'all-in-one-reservation' ),
		);
		$args   = array(
			'label'               => esc_html__( 'Appointment', 'all-in-one-reservation' ),
			'description'         => esc_html__( 'Summited form date of Appointment', 'all-in-one-reservation' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-cart',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'capabilities'        => array(
				'create_posts' => false,
			),
			'map_meta_cap'        => true,

		);
		register_post_type( 'sol_appointment_list', $args );
	}
	/**
	 * List Appointment Columns.
	 *
	 * @since    1.0.0
	 * @param array $columns Columns of Appointment.
	 */
	public function sol_appointment_list_columns( $columns ) {
		unset( $columns['title'], $columns['date'] );
		$column ['booking_id']      = esc_html__( 'Booking ID', 'all-in-one-reservation' );
		$columns['user_name']       = esc_html__( 'Name', 'all-in-one-reservation' );
		$columns['appoinment_date'] = esc_html__( 'Appointment Date', 'all-in-one-reservation' );
		$columns['appoinment_time'] = esc_html__( 'Time', 'all-in-one-reservation' );
		$columns['booked_date']     = esc_html__( 'Booked Date', 'all-in-one-reservation' );
		$columns['status']          = esc_html__( 'Status', 'all-in-one-reservation' );
		$columns['action']          = esc_html__( 'Action', 'all-in-one-reservation' );
		$column ['payment']         = esc_html__( 'Payment', 'all-in-one-reservation' );
		return $columns;
	}
	/**
	 * List of Appointment Custom Columns.
	 *
	 * @since    1.0.0
	 * @param string $column Column Name.
	 * @param int    $post_id Post ID.
	 */
	public function appointment_list_custom_column( $column, $post_id ) {
		$fid       = get_post_meta( $post_id, 'form_id', true );
		$slot_type = get_post_meta( $post_id, 'stp', true );
		$adate     = gmdate( 'l, F j, Y', strtotime( get_post_meta( $post_id, 'tdt', true ) ) );
		switch ( $column ) {
			case 'booking_id':
				echo esc_html( $post_id );
				break;
			case 'user_name':
				$first_name   = get_post_meta( $post_id, 'rf_first_name', true );
				$last_name    = get_post_meta( $post_id, 'rf_last_name', true );
				$user_name    = $first_name . ' ' . $last_name;
				$email        = get_post_meta( $post_id, 'rf_email', true );
				$phone_number = get_post_meta( $post_id, 'rf_phone_no', true );
				echo esc_html( $user_name ) . '<br><small>' . esc_html( $email ) . '</small><br>';
				echo '<small>' . esc_html__( 'Phone:', 'all-in-one-reservation' ) . esc_html( $phone_number ) . '</small>';
				break;
			case 'appoinment_date':
				echo esc_html( $adate );
				echo '<br>';
				break;
			case 'booked_date':
				echo get_the_date( 'F j, Y', $post_id );
				break;
			case 'appoinment_time':
				$day = strtolower( gmdate( 'l', strtotime( $adate ) ) );
				echo esc_html( self::get_slot_time( $slot_type, $post_id, $fid, $day ) );
				break;
			case 'status':
				$status = get_post_meta( $post_id, 'status', true );
				echo '<span class="aior_a_status_' . esc_attr( $status ) . '">';
				if ( 'pending' === $status ) {
					echo '<span class="dashicons dashicons-warning"></span> ';
				} elseif ( 'declined' === $status ) {
					echo '<span class="dashicons dashicons-dismiss"></span> ';
				} elseif ( 'approved' === $status ) {
					echo '<span class="dashicons dashicons-yes-alt"></span> ';
				}
				echo esc_html( ucfirst( $status ) );
				echo '</span>';
				break;
			case 'action':
				$status = get_post_meta( $post_id, 'status', true );
				wp_nonce_field( 'on_aior_admin_global_nonce', 'aior_admin_global_nonce' );
				if ( 'pending' === $status ) {
					echo '<button type="button" class="button button-primary booke-appointment-act approve-btn" data-pid="' . esc_attr( $post_id ) . '" data-act="approve">' . esc_html__( 'Approve', 'all-in-one-reservation' ) . '</button>&nbsp;&nbsp;&nbsp;';
					echo '<button type="button" class="button button-primary booke-appointment-act deny-btn" data-pid="' . esc_attr( $post_id ) . '" data-act="decline">' . esc_html__( 'Decline', 'all-in-one-reservation' ) . '</button>';
				} elseif ( 'approved' === $status ) {
					echo '<button type="button" class="button button-primary booke-appointment-act deny-btn" data-pid="' . esc_attr( $post_id ) . '" data-act="decline">' . esc_html__( 'Decline', 'all-in-one-reservation' ) . '</button>';
				} elseif ( 'declined' === $status ) {
					echo '<button type="button" class="button button-primary booke-appointment-act approve-btn" data-pid="' . esc_attr( $post_id ) . '" data-act="approve">' . esc_html__( 'Approve', 'all-in-one-reservation' ) . '</button>&nbsp;&nbsp;&nbsp;';
				}
				break;
			case 'payment':
				$payment_status = get_post_meta( $post_id, 'payment', true );
				echo esc_html( $payment_status );
				break;
		}
	}
	/**
	 * Get the slot time.
	 *
	 * @since    1.0.0
	 * @param string $slot_type Slot Type.
	 * @param string $aid Appointment ID.
	 * @param int    $fid Form ID.
	 * @param string $day Day.
	 */
	public static function get_slot_time( $slot_type, $aid, $fid, $day ) {
		$tid        = get_post_meta( $aid, 'tid', true );
		$sid        = get_post_meta( $aid, 'sid', true );
		$slot_data  = Aior_Appointment_Booking::get_slot_data( $fid, $tid, $sid );
		$start_time = isset( $slot_data['start_time'] ) ? $slot_data['start_time'] : '';
		$end_time   = isset( $slot_data['end_time'] ) ? $slot_data['end_time'] : '';
		if ( ! $start_time ) {
			$start_time = get_post_meta( $aid, 'stime', true );
		}
		if ( ! $end_time ) {
			$end_time = get_post_meta( $aid, 'etime', true );
		}
		return $start_time . ' ' . esc_html__( 'to', 'all-in-one-reservation' ) . ' ' . $end_time;
	}
	/**
	 * Sort the appointment list columns.
	 *
	 * @since    1.0.0
	 * @param array $columns Column Names.
	 */
	public function sortable_appointment_list_column( $columns ) {
		$columns['user_name'] = 'user_name';
		$columns['email']     = 'email';
		return $columns;
	}
	/**
	 * Joins the search with other tables.
	 *
	 * @since    1.0.0
	 * @param string $join Do the left join with other table to get proper output.
	 */
	public function sol_appointment_list_search_join( $join ) {
		global $pagenow, $wpdb;
		$_GET['post_type'] = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';
		if ( is_admin() && 'edit.php' === $pagenow && 'sol_appointment_list' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
			$join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
		}
		return $join;
	}
	/**
	 * Search the reservation data.
	 *
	 * @since    1.0.0
	 * @param string $where Condition.
	 */
	public function sol_appointment_list_search_where( $where ) {
		global $pagenow, $wpdb;
		$_GET['post_type'] = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';
		if ( is_admin() && 'edit.php' === $pagenow && 'sol_appointment_list' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
			$where  = preg_replace( '/\(\s*' . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/", '(' . $wpdb->posts . '.post_title LIKE $1) OR (' . $wpdb->postmeta . '.meta_value LIKE $1)', $where );
			$where .= " GROUP BY {$wpdb->posts}.id";
		}
		return $where;
	}
	/**
	 * Add the Meta Box for Reservation Post Type.
	 *
	 * @since    1.0.0
	 */
	public function add_meta_box_reseveration() {
		add_meta_box( 'aior_booking_data', esc_html__( 'Booking Data', 'all-in-one-reservation' ), array( $this, 'booking_info' ), 'sol_appointment_list', 'advanced', 'default', 'post' );
	}
	/**
	 * Stores the Booking Information.
	 *
	 * @since    1.0.0
	 */
	public function booking_info() {
		$bid          = get_the_id();
		$fid          = get_post_meta( $bid, 'form_id', true );
		$form_json    = get_post_meta( $fid, 'form_json', true );
		$form_array   = json_decode( $form_json, true );
		$aior_builder = new Aior_Builder();
		if ( $form_array ) {
			foreach ( $form_array as $row ) {
				echo '<div class="solrow2">';
				foreach ( $row as $column ) {
					echo '<div class="solcol2">';
					foreach ( $column as $form_field ) {
						$type        = array_key_exists( 't', $form_field ) ? $form_field['t'] : '';
						$name        = array_key_exists( 'n', $form_field ) ? $form_field['n'] : '';
						$label       = array_key_exists( 'l', $form_field ) ? $form_field['l'] : '';
						$placeholder = array_key_exists( 'p', $form_field ) ? $form_field['p'] : '';
						$required    = array_key_exists( 'r', $form_field ) ? $form_field['r'] : '';
						$message     = array_key_exists( 'm', $form_field ) ? $form_field['m'] : '';
						$options     = array_key_exists( 'o', $form_field ) ? $form_field['o'] : '';
						$value       = array_key_exists( 'v', $form_field ) ? $form_field['v'] : '';
						$min         = array_key_exists( 'x', $form_field ) ? $form_field['x'] : '';
						$max         = array_key_exists( 'y', $form_field ) ? $form_field['y'] : '';
						?>
						<div class="form-group">
							<label class="control-label"><?php echo esc_html( $label ); ?></label>
							<?php
							$attr  = ! empty( $message ) ? array( 'data-err-msg' => $message ) : '';
							$value = get_post_meta( $bid, $name, true ) ? get_post_meta( $bid, $name, true ) : $value;
							$aior_builder->add_field(
								array(
									'type'        => $type,
									'name'        => $name,
									'class'       => 'aior_fomr_filed ' . $name,
									'id'          => $name,
									'value'       => $value,
									'option'      => $options,
									'placeholder' => $placeholder,
									'required'    => $required,
									'min'         => $min,
									'max'         => $max,
									'attr'        => $attr,
								)
							);
							?>
						</div>
						<?php
					}
					echo '</div>';
				}
				echo '</div>';
			}
		}
		$payment                = get_post_meta( $bid, 'payment', true );
		$payment_method         = get_post_meta( $bid, 'pay_method', true );
		$payment_failed_status  = get_post_meta( $bid, 'payment_failed_status', true );
		$payment_failed_type    = get_post_meta( $bid, 'payment_failed_type', true );
		$payment_failed_code    = get_post_meta( $bid, 'payment_failed_code', true );
		$payment_failed_param   = get_post_meta( $bid, 'payment_failed_param', true );
		$payment_failed_message = get_post_meta( $bid, 'payment_failed_message', true );
		echo '<h3>' . esc_html__( 'Payment Status', 'all-in-one-reservation' ) . '</h3>';
		if ( 'Completed' === $payment ) {
			$paymen = ucfirst( $payment );
			echo '<div class="solrow2">';
			echo '<div class="solcol2"><b>' . esc_html__( 'Payment Status', 'all-in-one-reservation' ) . ': ' . esc_html( $paymen ) . '</b><br>';
			echo '<b>' . esc_html__( 'Payment Method', 'all-in-one-reservation' ) . ': ' . esc_html( $payment_method ) . '</b></div>';
			echo '</div>';
		} elseif ( 'failed' === $payment ) {
			echo '<div class="solrow2">';
			if ( $payment ) {
				echo '<div class="solcol2"><b>' . esc_html__( 'Payment Status', 'all-in-one-reservation' ) . ':' . esc_html( $payment ) . '</b><br>';
				echo '<b>' . esc_html__( 'Payment Method', 'all-in-one-reservation' ) . ': ' . esc_html( $payment_method ) . '</b></div>';
			}
			if ( $payment_failed_status ) {
				echo '<div class="solcol2"><b>' . esc_html__( 'Error Status', 'all-in-one-reservation' ) . ':' . esc_html( $payment_failed_status ) . '</b></div>';
			}
			if ( $payment_failed_type ) {
				echo '<div class="solcol2"><b>' . esc_html__( 'Error Type', 'all-in-one-reservation' ) . ':</b>' . esc_html( $payment_failed_type ) . '</div>';
			}
			if ( $payment_failed_code ) {
				echo '<div class="solcol2"><b>' . esc_html__( 'Error Code', 'all-in-one-reservation' ) . ':</b>' . esc_html( $payment_failed_code ) . '</div>';
			}
			if ( $payment_failed_param ) {
				echo '<div class="solcol2"><b>' . esc_html__( 'Error Param', 'all-in-one-reservation' ) . ':</b>' . esc_html( $payment_failed_param ) . '</div>';
			}
			if ( $payment_failed_message ) {
				echo '<div class="solcol2"><b>' . esc_html__( 'Error Message', 'all-in-one-reservation' ) . ':</b>' . esc_html( $payment_failed_message ) . '</div>';
			}
			echo '</div>';
		} else {
			update_post_meta( $bid, 'payment', 'pending' );
			echo '<div class="solrow2">';
			echo '<div class="solcol2"><b>' . esc_html__( 'Payment Status', 'all-in-one-reservation' ) . ': ' . esc_html__( 'Pending', 'all-in-one-reservation' ) . '</b><br>';
			echo '<b>' . esc_html__( 'Payment Method', 'all-in-one-reservation' ) . ': ' . esc_html( $payment_method ) . '</b></div>';
			echo '</div>';
		}
		wp_nonce_field( 'aior_save_admin_settings_nonce', 'aior_save_admin_settings_nonce' );
	}
}
$aior_appointment_list = new Aior_Appointment_List();
