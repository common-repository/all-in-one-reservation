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
class Aior_Reservation_List {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'custom_post_type_reservation' ), 0 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box_reseveration' ) );
		add_action( 'manage_posts_extra_tablenav', array( $this, 'export_button_csv_reservation_form' ), 20, 1 );
		add_action( 'init', array( $this, 'export_csv_reservation_form' ) );
		add_action( 'aoir_reservation_global_ajax', array( $this, 'import_json_reservation_from_ajax' ), 10, 1 );
	}

	/**
	 * Reservation List Custom Post Type.
	 *
	 * @since    1.0.0
	 */
	public function custom_post_type_reservation() {
		$labels = array(
			'name'                  => _x( 'Reservations', 'Post Type General Name', 'all-in-one-reservation' ),
			'singular_name'         => _x( 'Reservation', 'Post Type Singular Name', 'all-in-one-reservation' ),
			'menu_name'             => esc_html__( 'Reservation', 'all-in-one-reservation' ),
			'name_admin_bar'        => esc_html__( 'Reservation', 'all-in-one-reservation' ),
			'archives'              => esc_html__( 'Reservation Archives', 'all-in-one-reservation' ),
			'attributes'            => esc_html__( 'Reservation Attributes', 'all-in-one-reservation' ),
			'parent_item_colon'     => esc_html__( 'Parent Reservation:', 'all-in-one-reservation' ),
			'all_items'             => esc_html__( 'All Reservations', 'all-in-one-reservation' ),
			'add_new_item'          => esc_html__( 'Add New Reservation', 'all-in-one-reservation' ),
			'add_new'               => esc_html__( 'Add New', 'all-in-one-reservation' ),
			'new_item'              => esc_html__( 'New Reservation', 'all-in-one-reservation' ),
			'edit_item'             => esc_html__( 'Edit Reservation', 'all-in-one-reservation' ),
			'update_item'           => esc_html__( 'Update Reservation', 'all-in-one-reservation' ),
			'view_item'             => esc_html__( 'View Reservation', 'all-in-one-reservation' ),
			'view_items'            => esc_html__( 'View Reservations', 'all-in-one-reservation' ),
			'search_items'          => esc_html__( 'Search Reservation', 'all-in-one-reservation' ),
			'not_found'             => esc_html__( 'Not found', 'all-in-one-reservation' ),
			'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'all-in-one-reservation' ),
			'featured_image'        => esc_html__( 'Featured Image', 'all-in-one-reservation' ),
			'set_featured_image'    => esc_html__( 'Set featured image', 'all-in-one-reservation' ),
			'remove_featured_image' => esc_html__( 'Remove featured image', 'all-in-one-reservation' ),
			'use_featured_image'    => esc_html__( 'Use as featured image', 'all-in-one-reservation' ),
			'insert_into_item'      => esc_html__( 'Insert into Reservation', 'all-in-one-reservation' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this Reservation', 'all-in-one-reservation' ),
			'items_list'            => esc_html__( 'Reservations list', 'all-in-one-reservation' ),
			'items_list_navigation' => esc_html__( 'Reservations list navigation', 'all-in-one-reservation' ),
			'filter_items_list'     => esc_html__( 'Filter Reservations list', 'all-in-one-reservation' ),
		);
		$args   = array(
			'label'               => esc_html__( 'Reservation', 'all-in-one-reservation' ),
			'description'         => esc_html__( 'Summited form date of Reservation', 'all-in-one-reservation' ),
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
	 * Add Meta Box in Reservation List.
	 *
	 * @since    1.0.0
	 */
	public function add_meta_box_reseveration() {
		add_meta_box( 'aior_booking_data', esc_html__( 'Booking Data', 'all-in-one-reservation' ), array( $this, 'booking_info' ), 'reservation', 'advanced', 'default', 'post' );
	}
	/**
	 * Booking Information.
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
		wp_nonce_field( 'aior_save_admin_settings_nonce', 'aior_save_admin_settings_nonce' );
	}
	/**
	 * CSV Export Button.
	 *
	 * @since 1.0.0
	 * @param string $which Possibly top.
	 */
	public static function export_button_csv_reservation_form( $which ) {
		global $typenow;
		if ( 'sol_reservation_form' === $typenow && 'top' === $which ) {
			?>
			<input type="submit" name="export_all_sol_reservation_form" class="button button-primary" value="<?php esc_attr_e( 'Export All', 'all-in-one-reservation' ); ?>" />
			<?php
		}
	}
	/**
	 * JSON export all appointment form data.
	 *
	 * @since    1.0.0
	 */
	public static function export_csv_reservation_form() {
		global $post;
		if ( isset( $_GET['export_all_sol_reservation_form'] ) ) {
			$arg      = array(
				'post_type'      => 'sol_reservation_form',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			);
			$arr_post = get_posts( $arg );
			if ( $arr_post ) {
				header( 'Content-type: application/json' );
				header( 'Content-Disposition: attachment; filename="aior-appointment-forms.json"' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );
				$array_for_json = array();
				foreach ( $arr_post as $post_a ) {
					setup_postdata( $post_a );
					$pid         = $post_a->ID;
					$title       = $post_a->post_title;
					$meta        = get_post_meta( $pid, null, true );
					$export_data = array(
						'export_id' => $pid,
						'title'     => $title,
						'meta'      => $meta,
					);
					array_push( $array_for_json, $export_data );
				}
				$json = wp_json_encode( $array_for_json );
				echo $json;
				exit();
			}
		}
	}

	/**
	 * Get require import request.
	 *
	 * @since    1.0.0
	 * @param array $data Pass the data.
	 */
	public static function import_json_reservation_from_ajax( $data ) {
		$act = $data['act'];
		if ( 'import_json_reservation_from_ajax' === $act ) {
			$aid  = $data['aid'];
			$file = wp_get_attachment_url( $aid );
			if ( $file ) {
				$return   = false;
				$contents = file( $file );
				$string   = implode( $contents );
				$arr      = json_decode( $string );
				if ( ! empty( $arr ) ) {
					foreach ( $arr as $key => $iform ) {
						if ( property_exists( $iform, 'export_id' ) ) {
							$exported_id = $iform->export_id;
							$title       = $iform->title;
							$meta        = $iform->meta;
							$postarr     = array(
								'post_title'  => $title . '-exported',
								'post_status' => 'publish',
								'post_type'   => 'sol_reservation_form',
							);

							$pid = wp_insert_post( $postarr );
							if ( $pid ) {
								foreach ( $meta as $meta_key => $meta_value ) {
									if ( is_array( $meta_value[0] ) ) {
										$mv = unserialize( $meta_value[0] );
									} else {
										$mv = $meta_value[0];
									}
									if ( 'slot_montly_list' === $meta_key ) {
										$nvm = unserialize( $mv );
										$mv  = $nvm;
									}
									update_post_meta( $pid, $meta_key, $mv );
									$return = true;
								}
							}
						}
					}
				}
				if ( $return ) {
					esc_html_e( 'All forms imported.', 'all-in-one-reservation' );
				}
			}
		}
	}
}
$aior_reservation_list = new Aior_Reservation_List();
