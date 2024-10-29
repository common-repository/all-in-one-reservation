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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once ABSPATH . 'wp-admin/includes/plugin.php';

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
class Aior_Reservation_Form {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'custom_post_type_sol_reservation_form' ), 0 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post_sol_reservation_form', array( $this, 'on_settings_save_or_update' ), 10, 3 );
		if ( is_admin() ) {
			add_action( 'aior_reservation_form_save', array( $this, 'aior_save_settings' ), 10, 3 );
		}
		add_filter( 'manage_sol_reservation_form_posts_columns', array( $this, 'set_custom_edit_sol_reservation_form_columns' ) );
		add_action( 'manage_sol_reservation_form_posts_custom_column', array( $this, 'custom_sol_reservation_form_column' ), 10, 2 );

		/* Put together all settings tabs  */
		require_once 'partials/tab-general.php';
		require_once 'partials/tab-day-settings.php';
		require_once 'partials/tab-notification.php';
		if ( is_plugin_active( 'twilio-sms/class-aior-twilio.php' ) ) {
			require_once 'partials/tab-sms.php';
		}
		require_once 'partials/tab-form.php';
		add_action( 'aior_comment_form', array( $this, 'show_comment_form' ), 10, 1 );
	}
	/**
	 * Displays the Comment Form after calendar.
	 *
	 * @since    1.0.0
	 * @param int $fid Form ID.
	 */
	public function show_comment_form( $fid ) {
		$rf_data        = self::get_settings( $fid );
		$enable_comment = isset( $rf_data['enable_comment'] ) ? $rf_data['enable_comment'] : '0';
		if ( $enable_comment ) {
			self::get_comments( $fid );
		}
	}
	/**
	 * List of comments.
	 *
	 * @since    1.0.0
	 * @param int $fid Form ID.
	 */
	public static function get_comments( $fid ) { ?>
		<form class="aior_comments" method="post">
			<h3 class="aior_comment_heading"><?php esc_html_e( 'Write a Reply or Comment', 'all-in-one-reservation' ); ?></h3>
			<p class="aior_comment_field">
				<label for="comment"><?php esc_html_e( 'Add Comments', 'all-in-one-reservation' ); ?>&nbsp;</label>
				<textarea id="aior_comment" name="aior_comment" cols="45" rows="8" required=""></textarea>
			</p>
			<p class="form-submit">
				<input name="submit_aior_comment" type="submit" id="submit_aior_comment" class="submit" value="Send" data-form-id="<?php echo esc_attr( $fid ); ?>">
			</p>
		</form>
		<?php
		self::get_custom_comment( $fid );
	}
	/**
	 * List of Custom Comments.
	 *
	 * @since    1.0.0
	 * @param int $fid Form ID.
	 */
	public static function get_custom_comment( $fid ) {
		$aior_comment_count = get_comments_number( $fid );
		?>
		<div id="comments" class="comments-area default-max-width <?php echo get_option( 'show_avatars' ) ? 'show-avatars' : ''; ?>">
			<h2 class="comments-title">
			<?php
			if ( '1' === $aior_comment_count ) :
				esc_html_e( '1 comment', 'all-in-one-reservation' );
				else :
					printf(
						/* translators: %s: search term */
						esc_html( _nx( '%s comment', '%s comments', $aior_comment_count, 'Comments title', 'all-in-one-reservation' ) ),
						esc_html( number_format_i18n( $aior_comment_count ) )
					);
				endif;
				?>
			</h2><!-- .comments-title -->
			<ol class="comment-list">
				<?php
				$args         = array(
					'post_id' => $fid,
				);
				$all_comments = get_comments( $args );
				wp_list_comments(
					array(
						'avatar_size' => 60,
						'style'       => 'ol',
						'short_ping'  => true,
					),
					$all_comments
				);
				?>
			</ol><!-- .comment-list -->
			<?php
			the_comments_pagination(
				array(
					'before_page_number' => esc_html__( 'Page', 'all-in-one-reservation' ) . ' ',
					'mid_size'           => 0,
					'prev_text'          => sprintf(
						'%s <span class="nav-prev-text">%s</span>',
						is_rtl() ? '&rarr;' : '&larr;',
						esc_html__( 'Older comments', 'all-in-one-reservation' )
					),
					'next_text'          => sprintf(
						'<span class="nav-next-text">%s</span> %s',
						esc_html__( 'Newer comments', 'all-in-one-reservation' ),
						is_rtl() ? '&larr;' : '&rarr;'
					),
				)
			);
			if ( ! comments_open( $fid ) ) :
				?>
				<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'all-in-one-reservation' ); ?></p>
				<?php
			endif;
			?>
		</div><!-- #comments -->
		<?php
	}
	/**
	 * Set the custom column.
	 *
	 * @since    1.0.0
	 * @param array $columns Fetch the columns.
	 */
	public function set_custom_edit_sol_reservation_form_columns( $columns ) {
		unset( $columns['author'] );
		$columns['reservation_booking_scode'] = esc_html__( 'Shortcode', 'all-in-one-reservation' );
		return $columns;
	}
	/**
	 * Set Field Type to Custom Column.
	 *
	 * @since    1.0.0
	 * @param string $column Columns Name.
	 * @param int    $post_id Post ID.
	 */
	public function custom_sol_reservation_form_column( $column, $post_id ) {
		switch ( $column ) {
			case 'reservation_booking_scode':
				echo '<input type="text" onclick="this.select()" class="copy_shortcode" title="Copy Shortcode" value="[reservation_booking id=\'' . esc_attr( $post_id ) . '\']" readonly>';
				break;
		}
	}

	/**
	 * Register Cutsom Post Type.
	 */
	public function custom_post_type_sol_reservation_form() {
		$rf_data        = self::get_settings();
		$enable_comment = isset( $rf_data['enable_comment'] ) ? $rf_data['enable_comment'] : '0';
		if ( $enable_comment ) {
			$supports = array( 'title', 'comments' );
		} else {
			$supports = array( 'title' );
		}
		$labels = array(
			'name'                  => _x( 'Reservation Forms', 'Post Type General Name', 'all-in-one-reservation' ),
			'singular_name'         => _x( 'Reservation Form', 'Post Type Singular Name', 'all-in-one-reservation' ),
			'menu_name'             => esc_html__( 'Reservation Forms', 'all-in-one-reservation' ),
			'name_admin_bar'        => esc_html__( 'Reservation Forms', 'all-in-one-reservation' ),
			'archives'              => esc_html__( 'Reservation Forms Archives', 'all-in-one-reservation' ),
			'attributes'            => esc_html__( 'Form Attributes', 'all-in-one-reservation' ),
			'parent_item_colon'     => esc_html__( 'Parent Item:', 'all-in-one-reservation' ),
			'all_items'             => esc_html__( 'All Reservation Forms', 'all-in-one-reservation' ),
			'add_new_item'          => esc_html__( 'Add New Reservation Form', 'all-in-one-reservation' ),
			'add_new'               => esc_html__( 'Add New Reservation Form', 'all-in-one-reservation' ),
			'new_item'              => esc_html__( 'New Reservation Form', 'all-in-one-reservation' ),
			'edit_item'             => esc_html__( 'Edit Reservation Form', 'all-in-one-reservation' ),
			'update_item'           => esc_html__( 'Update Reservation Form', 'all-in-one-reservation' ),
			'view_item'             => esc_html__( 'View Reservation Form', 'all-in-one-reservation' ),
			'view_items'            => esc_html__( 'View Reservation Forms', 'all-in-one-reservation' ),
			'search_items'          => esc_html__( 'Search Reservation Form', 'all-in-one-reservation' ),
			'not_found'             => esc_html__( 'Not found', 'all-in-one-reservation' ),
			'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'all-in-one-reservation' ),
			'featured_image'        => esc_html__( 'Featured Image', 'all-in-one-reservation' ),
			'set_featured_image'    => esc_html__( 'Set featured image', 'all-in-one-reservation' ),
			'remove_featured_image' => esc_html__( 'Remove featured image', 'all-in-one-reservation' ),
			'use_featured_image'    => esc_html__( 'Use as featured image', 'all-in-one-reservation' ),
			'insert_into_item'      => esc_html__( 'Insert into item', 'all-in-one-reservation' ),
			'uploaded_to_this_item' => esc_html__( 'Uploaded to this item', 'all-in-one-reservation' ),
			'items_list'            => esc_html__( 'Calendar list', 'all-in-one-reservation' ),
			'items_list_navigation' => esc_html__( 'Items list navigation', 'all-in-one-reservation' ),
			'filter_items_list'     => esc_html__( 'Filter items list', 'all-in-one-reservation' ),
		);
		$args   = array(
			'label'               => esc_html__( 'Reservation Form', 'all-in-one-reservation' ),
			'description'         => esc_html__( 'Reservation Forms/Event/Calendar', 'all-in-one-reservation' ),
			'labels'              => $labels,
			'supports'            => $supports,
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-calendar-alt',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);
		register_post_type( 'sol_reservation_form', $args );
	}
	/**
	 * Add Meta Box to Reservation Settings.
	 */
	public function add_meta_box() {
		add_meta_box( 'reservation_settings', esc_html__( 'Reservation Settings', 'all-in-one-reservation' ), array( $this, 'create_reservation_settings' ), 'sol_reservation_form', 'advanced', 'default' );
	}
	/**
	 * Creates the tabs.
	 *
	 * @since    1.0.0
	 * @param array $args Arguments for the tabs.
	 */
	public static function create_rs_tab( $args ) {
		add_action(
			'aior_add_tab_nav',
			function() use ( $args ) {
				?>
			<li class="aior-tab">
				<a href="javascript:void(0)" id="aior-tab-<?php echo esc_attr( $args['id'] ); ?>">
					<?php echo esc_html( $args['name'] ); ?>
				</a>
			</li>
				<?php
			}
		);
		add_action(
			'aior_add_tab_content',
			function() use ( $args ) {
				?>
			<div id="aior-tab-<?php echo esc_attr( $args['id'] ); ?>-content" class="aior-tab-content">
				<h3><?php echo esc_html( $args['title'] ); ?></h3>
				<p>
				<?php
				if ( isset( $args['desc'] ) ) {
					echo esc_html( $args['desc'] );
				} else {
					echo '';
				}
				?>
				</p>
				<?php echo esc_html( apply_filters( $args['filter'], '' ) ); ?>
			</div>
				<?php
			}
		);
	}
	/**
	 * Creates the new Reservation Settings.
	 *
	 * @since    1.0.0
	 */
	public function create_reservation_settings() {
		?>
		<div class="aior-tabs-container">
			<ul class="aior-tabs">
			<?php
				do_action( 'aior_add_tab_nav' );
			?>
			</ul>
			<div class="aior-tab-content-container">
			<?php
				do_action( 'aior_add_tab_content' );
			?>
			</div>
		</div>
		<?php
	}
	/**
	 * Save the Reservation Forms Settings.
	 *
	 * @since    1.0.0
	 * @param int    $post_id Post ID.
	 * @param string $post Details of Post.
	 * @param string $update Update the Post Details.
	 */
	public function aior_save_settings( $post_id, $post, $update ) {
		if ( ! empty( $_POST ) ) {
			$nonce = ( isset( $_POST['res_form_nonce'] ) && ! empty( $_POST['res_form_nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['res_form_nonce'] ) ) : '';
			if ( wp_verify_nonce( $nonce, 'res_form_nonce_action' ) ) {
				$booking_type           = isset( $_POST['booking_type'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_type'] ) ) : '';
				$layout_type            = isset( $_POST['layout_type'] ) ? sanitize_text_field( wp_unslash( $_POST['layout_type'] ) ) : '';
				$clock_hours            = isset( $_POST['clock_hours'] ) ? sanitize_text_field( wp_unslash( $_POST['clock_hours'] ) ) : '';
				$opening_time           = isset( $_POST['opening_time'] ) ? sanitize_text_field( wp_unslash( $_POST['opening_time'] ) ) : '';
				$closing_time           = isset( $_POST['closing_time'] ) ? sanitize_text_field( wp_unslash( $_POST['closing_time'] ) ) : '';
				$time_slot              = isset( $_POST['time_slot'] ) ? sanitize_text_field( wp_unslash( $_POST['time_slot'] ) ) : '';
				$max_guest              = isset( $_POST['max_guest'] ) ? sanitize_text_field( wp_unslash( $_POST['max_guest'] ) ) : '';
				$max_end_days           = isset( $_POST['max_end_days'] ) ? sanitize_text_field( wp_unslash( $_POST['max_end_days'] ) ) : '';
				$include_today          = isset( $_POST['include_today'] ) ? sanitize_text_field( wp_unslash( $_POST['include_today'] ) ) : '';
				$date_format            = isset( $_POST['date_format'] ) ? sanitize_text_field( wp_unslash( $_POST['date_format'] ) ) : '';
				$front_page             = isset( $_POST['front_page'] ) ? sanitize_text_field( wp_unslash( $_POST['front_page'] ) ) : '';
				$cancel_page            = isset( $_POST['cancel_page'] ) ? sanitize_text_field( wp_unslash( $_POST['cancel_page'] ) ) : '';
				$payment_success        = isset( $_POST['payment_success'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_success'] ) ) : '';
				$prevent_before_date    = isset( $_POST['prevent_before_date'] ) ? sanitize_text_field( wp_unslash( $_POST['prevent_before_date'] ) ) : '';
				$prevent_after_date     = isset( $_POST['prevent_after_date'] ) ? sanitize_text_field( wp_unslash( $_POST['prevent_after_date'] ) ) : '';
				$appointment_limit      = isset( $_POST['appointment_limit'] ) ? sanitize_text_field( wp_unslash( $_POST['appointment_limit'] ) ) : '';
				$appointment_action     = isset( $_POST['appointment_action'] ) ? sanitize_text_field( wp_unslash( $_POST['appointment_action'] ) ) : '';
				$enable_payment         = isset( $_POST['enable_payment'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_payment'] ) ) : '';
				$payment_gateway        = isset( $_POST['payment_gateway'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['payment_gateway'] ) ) : '';
				$enable_comment         = isset( $_POST['enable_comment'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_comment'] ) ) : '';
				$enable_social_share    = isset( $_POST['enable_social_share'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_social_share'] ) ) : '';
				$show_social_share_icon = isset( $_POST['show_social_share_icon'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['show_social_share_icon'] ) ) : '';

				if ( isset( $_POST['holiday_dates'] ) && ! empty( $_POST['holiday_dates'] ) ) {
					foreach ( $_POST['holiday_dates'] as $key => $holiday_date ) {
						$holiday_dates[ $key ] = array_map( 'sanitize_text_field', wp_unslash( $holiday_date ) );
					}
				}

				/* Save Message and Notifications */
				$notif_success_message     = isset( $_POST['notif_success_message'] ) ? sanitize_text_field( wp_unslash( $_POST['notif_success_message'] ) ) : '';
				$enable_admin_notification = isset( $_POST['enable_admin_notification'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_admin_notification'] ) ) : '';
				$admin_email               = isset( $_POST['admin_email'] ) ? sanitize_email( wp_unslash( $_POST['admin_email'] ) ) : '';
				$admin_cc_email            = isset( $_POST['admin_cc_email'] ) ? sanitize_email( wp_unslash( $_POST['admin_cc_email'] ) ) : '';
				$admin_subject             = isset( $_POST['admin_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['admin_subject'] ) ) : '';
				$admin_email_body          = isset( $_POST['admin_email_body'] ) ? wp_kses_post( wp_unslash( $_POST['admin_email_body'] ) ) : '';
				$client_subject            = isset( $_POST['client_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['client_subject'] ) ) : '';
				$client_email_body         = isset( $_POST['client_email_body'] ) ? wp_kses_post( wp_unslash( $_POST['client_email_body'] ) ) : '';
				$confirm_subject           = isset( $_POST['confirm_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['confirm_subject'] ) ) : '';
				$confirm_email_body        = isset( $_POST['confirm_email_body'] ) ? wp_kses_post( wp_unslash( $_POST['confirm_email_body'] ) ) : '';
				$cancel_subject            = isset( $_POST['cancel_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['cancel_subject'] ) ) : '';
				$cancel_email_body         = isset( $_POST['cancel_email_body'] ) ? wp_kses_post( wp_unslash( $_POST['cancel_email_body'] ) ) : '';
				$calendar_theme            = isset( $_POST['calendar_theme'] ) ? sanitize_text_field( wp_unslash( $_POST['calendar_theme'] ) ) : '1';
				$booking_form_theme        = isset( $_POST['booking_form_theme'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_form_theme'] ) ) : '4';

				/** Save sms and notification */
				$sms_success_message = isset( $_POST['sms_success_message'] ) ? sanitize_text_field( wp_unslash( $_POST['sms_success_message'] ) ) : '';

				$enable_admin_sms = isset( $_POST['enable_admin_sms'] ) ? sanitize_text_field( wp_unslash( $_POST['enable_admin_sms'] ) ) : '1';
				$admin_mobile     = isset( $_POST['admin_mobile'] ) ? sanitize_text_field( wp_unslash( $_POST['admin_mobile'] ) ) : '';
				$admin_sms_body   = isset( $_POST['admin_sms_body'] ) ? wp_kses_post( wp_unslash( $_POST['admin_sms_body'] ) ) : '';
				$confirm_sms_body = isset( $_POST['confirm_sms_body'] ) ? wp_kses_post( wp_unslash( $_POST['confirm_sms_body'] ) ) : '';
				$cancel_sms_body  = isset( $_POST['cancel_sms_body'] ) ? wp_kses_post( wp_unslash( $_POST['cancel_sms_body'] ) ) : '';

				update_post_meta( $post_id, 'sms_success_message', $sms_success_message );
				update_post_meta( $post_id, 'enable_admin_sms', $enable_admin_sms );
				update_post_meta( $post_id, 'admin_mobile', $admin_mobile );
				update_post_meta( $post_id, 'admin_sms_body', $admin_sms_body );
				update_post_meta( $post_id, 'confirm_sms_body', $confirm_sms_body );
				update_post_meta( $post_id, 'cancel_sms_body', $cancel_sms_body );

				/* Saving Boooking Form JSON */
				$form_json = isset( $_POST['form_json'] ) ? wp_kses_post( $_POST['form_json'] ) : '';

				update_post_meta( $post_id, 'booking_type', $booking_type );
				update_post_meta( $post_id, 'layout_type', $layout_type );
				update_post_meta( $post_id, 'clock_hours', $clock_hours );
				update_post_meta( $post_id, 'opening_time', $opening_time );
				update_post_meta( $post_id, 'closing_time', $closing_time );
				update_post_meta( $post_id, 'time_slot', $time_slot );
				update_post_meta( $post_id, 'max_guest', $max_guest );
				update_post_meta( $post_id, 'max_end_days', $max_end_days );
				update_post_meta( $post_id, 'include_today', $include_today );
				update_post_meta( $post_id, 'date_format', $date_format );
				update_post_meta( $post_id, 'front_page', $front_page );
				update_post_meta( $post_id, 'cancel_page', $cancel_page );
				update_post_meta( $post_id, 'payment_success', $payment_success );

				update_post_meta( $post_id, 'prevent_before_date', $prevent_before_date );
				update_post_meta( $post_id, 'prevent_after_date', $prevent_after_date );
				update_post_meta( $post_id, 'appointment_limit', $appointment_limit );
				update_post_meta( $post_id, 'appointment_action', $appointment_action );
				update_post_meta( $post_id, 'enable_payment', $enable_payment );
				update_post_meta( $post_id, 'payment_gateway', $payment_gateway );
				update_post_meta( $post_id, 'enable_comment', $enable_comment );
				update_post_meta( $post_id, 'enable_social_share', $enable_social_share );
				update_post_meta( $post_id, 'show_social_share_icon', $show_social_share_icon );
				update_post_meta( $post_id, 'holiday_dates', $holiday_dates );

				update_post_meta( $post_id, 'notif_success_message', $notif_success_message );
				update_post_meta( $post_id, 'enable_admin_notification', $enable_admin_notification );
				update_post_meta( $post_id, 'admin_email', $admin_email );
				update_post_meta( $post_id, 'admin_cc_email', $admin_cc_email );
				update_post_meta( $post_id, 'admin_subject', $admin_subject );
				update_post_meta( $post_id, 'admin_email_body', $admin_email_body );
				update_post_meta( $post_id, 'client_subject', $client_subject );
				update_post_meta( $post_id, 'client_email_body', $client_email_body );
				update_post_meta( $post_id, 'confirm_subject', $confirm_subject );
				update_post_meta( $post_id, 'confirm_email_body', $confirm_email_body );
				update_post_meta( $post_id, 'cancel_subject', $cancel_subject );
				update_post_meta( $post_id, 'cancel_email_body', $cancel_email_body );
				update_post_meta( $post_id, 'form_json', $form_json );
				update_post_meta( $post_id, 'calendar_theme', $calendar_theme );
				update_post_meta( $post_id, 'booking_form_theme', $booking_form_theme );

				do_action( 'aior_save_rf_settings', $post_id );
			}
		}
	}
	/**
	 * Displays the Comment Form after calendar.
	 *
	 * @since    1.0.0
	 * @param int $post_id Post ID.
	 */
	public static function get_settings( $post_id = '' ) {
		if ( ! $post_id || 'undefined' == $post_id ) {
			$post_id = get_the_id();
		}
		if ( $post_id ) {
			$rf_data = get_post_meta( $post_id );
			unset( $rf_data['_edit_last'] );
			unset( $rf_data['_edit_lock'] );
			$rf_data_arr = array();
			foreach ( $rf_data as $key => $val ) {
				$rf_data_arr[ $key ] = $val[0];
			}
			if ( ! empty( $rf_data_arr ) ) {
				return $rf_data_arr;
			}
		} else {
			return false;
		}
	}
	/**
	 * Save or Update the Settings.
	 *
	 * @since    1.0.0
	 * @param int    $post_id Post ID.
	 * @param string $post Details of Post.
	 * @param string $update Update the Post Details.
	 */
	public function on_settings_save_or_update( $post_id, $post, $update ) {
		if ( isset( $post ) ) {
			if ( 'sol_reservation_form' === $post->post_type ) {
				do_action( 'aior_reservation_form_save', $post_id, $post, $update );
			}
		}
	}
	/**
	 * Get the list of page.
	 *
	 * @since    1.0.0
	 */
	public function get_page_list_arr() {
		$args     = array(
			'post_type'   => 'page',
			'post_status' => 'publish',
		);
		$wppages  = get_pages( $args );
		$page_arr = array();
		if ( $wppages ) {
			foreach ( $wppages as $wppage ) {
				$id                 = $wppage->ID;
				$title              = $wppage->post_title;
				$page_arr[ $title ] = $id;
			}
			if ( ! empty( $page_arr ) ) {
				return $page_arr;
			}
		}
	}
}
$aior_reservation_form = new Aior_Reservation_Form();
