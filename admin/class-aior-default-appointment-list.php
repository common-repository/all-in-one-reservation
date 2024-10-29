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
class Aior_Default_Appointment_List {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'custom_post_type_appointment' ), 0 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box_reseveration' ) );
		add_action( 'manage_reservation_posts_custom_column', array( $this, 'default_appointment_list_custom_column' ), 10, 2 );
		add_filter( 'manage_reservation_posts_columns', array( $this, 'reservation_columns' ) );
		add_filter( 'manage_edit-reservation_sortable_columns', array( $this, 'sortable_reservation_column' ), 10, 2 );
	}
	/**
	 * Register Custom Post Types.
	 *
	 * @since    1.0.0
	 */
	public function custom_post_type_appointment() {
		$labels = array(
			'name'                  => _x( 'Default Appointment', 'Post Type General Name', 'all-in-one-reservation' ),
			'singular_name'         => _x( 'Default Appointment', 'Post Type Singular Name', 'all-in-one-reservation' ),
			'menu_name'             => esc_html__( 'Default Appointment', 'all-in-one-reservation' ),
			'name_admin_bar'        => esc_html__( 'Default Appointment', 'all-in-one-reservation' ),
			'archives'              => esc_html__( 'Default Appointment Archives', 'all-in-one-reservation' ),
			'attributes'            => esc_html__( 'DefaultAppointment Attributes', 'all-in-one-reservation' ),
			'parent_item_colon'     => esc_html__( 'Parent Default Appointment:', 'all-in-one-reservation' ),
			'all_items'             => esc_html__( 'All Default Appointment', 'all-in-one-reservation' ),
			'add_new_item'          => esc_html__( 'Add New Default Appointment', 'all-in-one-reservation' ),
			'add_new'               => esc_html__( 'Add New', 'all-in-one-reservation' ),
			'new_item'              => esc_html__( 'New Default Appointment', 'all-in-one-reservation' ),
			'edit_item'             => esc_html__( 'Edit Default Appointment', 'all-in-one-reservation' ),
			'update_item'           => esc_html__( 'Update Default Appointment', 'all-in-one-reservation' ),
			'view_item'             => esc_html__( 'View Default Appointment', 'all-in-one-reservation' ),
			'view_items'            => esc_html__( 'View Default Appointments', 'all-in-one-reservation' ),
			'search_items'          => esc_html__( 'Search Default Appointment', 'all-in-one-reservation' ),
			'not_found'             => esc_html__( 'No Default Appointment Found', 'all-in-one-reservation' ),
			'not_found_in_trash'    => esc_html__( 'No Default Appointment Found In Trash', 'all-in-one-reservation' ),
			'featured_image'        => esc_html__( 'Featured Image', 'all-in-one-reservation' ),
			'set_featured_image'    => esc_html__( 'Set featured image', 'all-in-one-reservation' ),
			'remove_featured_image' => esc_html__( 'Remove featured image', 'all-in-one-reservation' ),
			'use_featured_image'    => esc_html__( 'Use as featured image', 'all-in-one-reservation' ),
			'insert_into_item'      => esc_html__( 'Insert into Default Appointment', 'all-in-one-reservation' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this Default Appointment', 'all-in-one-reservation' ),
			'items_list'            => esc_html__( 'Default Appointment list', 'all-in-one-reservation' ),
			'items_list_navigation' => esc_html__( 'Default Appointment list navigation', 'all-in-one-reservation' ),
			'filter_items_list'     => esc_html__( 'Filter Default Appointment list', 'all-in-one-reservation' ),
		);
		$args   = array(
			'label'               => esc_html__( 'Default Appointment', 'all-in-one-reservation' ),
			'description'         => esc_html__( 'Summited form date of Default Appointment', 'all-in-one-reservation' ),
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
		register_post_type( 'reservation', $args );
	}
	/**
	 * Displays the Reservation Columns.
	 *
	 * @since    1.0.0
	 * @param array $columns Fetch the columns of table.
	 */
	public function reservation_columns( $columns ) {
		unset( $columns['title'], $columns['date'] );
		$column ['booking_id']      = esc_html__( 'Booking ID', 'all-in-one-reservation' );
		$columns['user_name']       = esc_html__( 'Name', 'all-in-one-reservation' );
		$columns['appoinment_date'] = esc_html__( 'Appointment Date', 'all-in-one-reservation' );
		$columns['appoinment_time'] = esc_html__( 'Time', 'all-in-one-reservation' );
		$columns['booked_date']     = esc_html__( 'Booked Date', 'all-in-one-reservation' );
		$column ['payment']         = esc_html__( 'Payment', 'all-in-one-reservation' );
		return $columns;
	}
	/**
	 * Displays Default Appointment list for Custom Columns.
	 *
	 * @since    1.0.0
	 * @param string $column Fetch the column of table.
	 * @param int    $post_id Fetch the Post ID.
	 */
	public function default_appointment_list_custom_column( $column, $post_id ) {
		$fid       = get_post_meta( $post_id, 'form_id', true );
		$slot_type = get_post_meta( $post_id, 'stp', true );
		$adate     = gmdate( 'l, F j, Y', strtotime( get_post_meta( $post_id, 'aior_booking_date', true ) ) );
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
				$reservation_from_time = get_post_meta( $post_id, 'reservation_from', true );
				$reservation_to_time   = get_post_meta( $post_id, 'reservation_to', true );
				$from_to_time          = $reservation_from_time . ' to ' . $reservation_to_time;
				echo esc_html( $from_to_time );
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
	 * Sort the data as per column selection.
	 *
	 * @since    1.0.0
	 * @param string $columns Fetch the columns of table.
	 */
	public function sortable_reservation_column( $columns ) {
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
	public function reservation_search_join( $join ) {
		global $pagenow, $wpdb;
		$_GET['post_type'] = isset( $_GET['post_type'] ) && ! empty( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';
		if ( is_admin() && 'edit.php' === $pagenow && 'reservation' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
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
	public function reservation_search_where( $where ) {
		global $pagenow, $wpdb;
		$_GET['post_type'] = isset( $_GET['post_type'] ) && ! empty( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';
		if ( is_admin() && 'edit.php' === $pagenow && 'reservation' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
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
		add_meta_box( 'aior_booking_data', esc_html__( 'Booking Data', 'all-in-one-reservation' ), array( $this, 'booking_info' ), 'reservation', 'advanced', 'default', 'post' );
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
		$payment_failed_status  = get_post_meta( $bid, 'payment_failed_status', true );
		$payment_failed_type    = get_post_meta( $bid, 'payment_failed_type', true );
		$payment_failed_code    = get_post_meta( $bid, 'payment_failed_code', true );
		$payment_failed_param   = get_post_meta( $bid, 'payment_failed_param', true );
		$payment_failed_message = get_post_meta( $bid, 'payment_failed_message', true );
		echo '<h3>' . esc_html__( 'Payment Status', 'all-in-one-reservation' ) . '</h3>';
		if ( 'Completed' === $payment ) {
			$paymen = ucfirst( $payment );
			echo '<div class="solrow2">';
			echo '<div class="solcol2"><b>' . esc_html__( 'Payment Status', 'all-in-one-reservation' ) . ':</b>' . esc_html( $paymen ) . '</div>';
			echo '</div>';
		} elseif ( 'failed' === $payment ) {
			echo '<div class="solrow2">';
			if ( $payment ) {
				echo '<div class="solcol2"><b>' . esc_html__( 'Payment Status', 'all-in-one-reservation' ) . ':' . esc_html( $payment ) . '</b></div>';
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
			echo '<div class="solcol2"><b>' . esc_html__( 'Payment Status', 'all-in-one-reservation' ) . ': ' . esc_html__( 'Pending', 'all-in-one-reservation' ) . '</b></div>';
			echo '</div>';
		}
	}
}
$aior_default_appointment_list = new Aior_Default_Appointment_List();
