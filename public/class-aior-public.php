<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.solwininfotech.com/
 * @since      1.0.0
 *
 * @package    Aior
 * @subpackage Aior/public
 */

if ( ! isset( $_SESSION ) ) {
	session_start();
}

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Aior
 * @subpackage Aior/public
 * @author     Solwin Infotech <support@solwininfotech.com>
 */
class Aior_Public {


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		add_shortcode( 'reservation_booking', array( $this, 'reservation_booking_shortcode' ) );
		add_action( 'wp_ajax_aoir_reservation_form_submit', array( $this, 'aoir_reservation_form_submit' ) );
		add_action( 'wp_ajax_nopriv_aoir_reservation_form_submit', array( $this, 'aoir_reservation_form_submit' ) );
		add_action( 'wp_head', array( $this, 'recaptcha_script' ) );
		add_shortcode( 'aior_pay', array( $this, 'payment_page' ) );
		add_shortcode( 'aior_pay_success', array( $this, 'success_payment_page' ) );
		add_action( 'aior_payment_gateway_default', array( $this, 'default_payment_modes' ) );
	}

	/**
	 * Recaptcha Script.
	 *
	 * @since    1.0.0
	 */
	public function recaptcha_script() {
		$rf_data     = Aior_Admin::get_settings();
		$public_key  = isset( $rf_data['aior_recaptcha_public_key'] ) && ! empty( $rf_data['aior_recaptcha_public_key'] ) ? $rf_data['aior_recaptcha_public_key'] : '';
		$private_key = isset( $rf_data['aior_recaptcha_private_key'] ) && ! empty( $rf_data['aior_recaptcha_private_key'] ) ? $rf_data['aior_recaptcha_private_key'] : '';
		$theme       = isset( $rf_data['aior_recaptcha_theme'] ) && ! empty( $rf_data['aior_recaptcha_theme'] ) ? $rf_data['aior_recaptcha_theme'] : '';
		$ln          = isset( $rf_data['aior_recaptcha_lang'] ) && ! empty( $rf_data['aior_recaptcha_lang'] ) ? $rf_data['aior_recaptcha_lang'] : '';
		$reg_form    = isset( $rf_data['aior_recaptcha_spam_verification'] ) && ! empty( $rf_data['aior_recaptcha_spam_verification'] ) ? $rf_data['aior_recaptcha_spam_verification'] : '';
		if ( ! empty( $public_key ) && ! empty( $private_key ) && $reg_form ) {
			require_once AIOR_PLUGIN_PATH . '/admin/vendors/class-aior-recaptcha.php';
			wp_enqueue_script( 'recaptcha-js', 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit', $this->version, true );
			wp_localize_script(
				'recaptcha-js',
				'aior_recaptcha',
				array(
					'sitekey' => esc_html( $public_key ),
					'theme'   => esc_html( $theme ),
					'hl'      => esc_html( $ln ),
				)
			);
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aior-public.css', array( 'dashicons' ), $this->version, 'all' );
		wp_enqueue_style( 'jquery-date-range-picker', AIOR_PLUGIN_URL . '/lib/jquery-date-range-picker/daterangepicker.min.css', array(), '0.21.1', 'all' );
		wp_enqueue_style( 'jquery_timepicker_css', AIOR_PLUGIN_URL . '/lib/timepicker/jquery.timepicker.min.css', array(), '1.3.5', 'all' );
		wp_enqueue_style( 'sweetalert2', AIOR_PLUGIN_URL . '/lib/sweetalert2/sweetalert2.min.css', array(), '10.16.6', 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/aior-public.js', array( 'jquery', 'moment', 'jquery-ui-datepicker' ), $this->version, false );
		wp_enqueue_script( 'jquery-date-range-picker', AIOR_PLUGIN_URL . '/lib/jquery-date-range-picker/jquery.daterangepicker.js', array( 'jquery' ), '0.21.1', true );
		wp_enqueue_script( 'jquery-timepicker', AIOR_PLUGIN_URL . '/lib/timepicker/jquery.timepicker.min.js', array( 'jquery' ), '1.3.5', false );
		wp_localize_script( $this->plugin_name, 'aior_obj', self::translate_string() );
		wp_enqueue_script( 'aior-hooks', AIOR_PLUGIN_URL . '/lib/aior-hooks/aior-hooks.js', array( 'jquery', 'underscore' ), '1.0', false );
		wp_enqueue_script( 'sweetalert2', AIOR_PLUGIN_URL . '/lib/sweetalert2/sweetalert2.all.min.js', array( 'jquery' ), '10.16.6', false );
	}
	/**
	 * Reservation Booking Shortcode.
	 *
	 * @since    1.0.0
	 * @param array $atts Attributes.
	 */
	public function reservation_booking_shortcode( $atts ) {
		$args = shortcode_atts(
			array(
				'id'   => 'error',
				'slug' => 'error',
			),
			$atts
		);
		$id   = $args['id'];
		if ( 'error' !== $id ) {
			ob_start();
			self::get_form( $id );
			$o = ob_get_contents();
			ob_end_clean();
			$o = apply_filters( 'aior_shortcode', $o, $id );
			return $o;
		}
	}
	/**
	 * Get the Form.
	 *
	 * @since    1.0.0
	 * @param int $id Form ID.
	 */
	public static function get_form( $id ) {
		$form_json     = get_post_meta( $id, 'form_json', true );
		$booking_type  = get_post_meta( $id, 'booking_type', true );
		$form_array    = json_decode( $form_json, true );
		$aior_builder  = new Aior_Builder();
		$pay_link      = '';
		$is_pay_active = '';
		if ( $form_array ) {
			do_action( 'aior_before_reservation_form' );
			if ( self::is_payment_enable( $id ) ) {
				$pay_page_id = get_post_meta( $id, 'aior_pay', true );
				if ( ! $pay_page_id ) {
					$pay_page_id = get_option( 'aior_pay', true );
				}
				$pay_link      = get_permalink( $pay_page_id );
				$is_pay_active = $pay_page_id;
			}
			echo '<form action="' . esc_attr( $pay_link ) . '" id="aior_reservation_form_' . esc_attr( $id ) . '" class="aior_reservation_form aior_fomr_style_1" method="post">';
			foreach ( $form_array as $row ) {
				echo '<div class="solrow">';
				foreach ( $row as $column ) {
					echo '<div class="solcol">';
					$j = 0;
					foreach ( $column as $form_field ) {
						if ( is_user_logged_in() && 'registered' === $booking_type && 'rf_email' === $form_field['n'] || is_user_logged_in() && 'guest' === $booking_type && 'rf_email' === $form_field['n'] ) {
							$current_user    = wp_get_current_user();
							$form_field['v'] = $current_user->user_email;
						}

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
						do_action( 'aior_before_reservation_form_field', $j );
						?>
						<div class="form-group">
							<label class="control-label"><?php echo esc_html( $label ); ?></label>
							<?php
							$attr = ! empty( $message ) ? array( 'data-err-msg' => $message ) : '';
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
						do_action( 'aior_after_reservation_form_field', $j );
						$j++;
					}
					echo '</div>';
				}
				echo '</div>';
			}
			$rf_data     = Aior_Admin::get_settings();
			$public_key  = isset( $rf_data['aior_recaptcha_public_key'] ) && ! empty( $rf_data['aior_recaptcha_public_key'] ) ? $rf_data['aior_recaptcha_public_key'] : '';
			$private_key = isset( $rf_data['aior_recaptcha_private_key'] ) && ! empty( $rf_data['aior_recaptcha_private_key'] ) ? $rf_data['aior_recaptcha_private_key'] : '';
			$theme       = isset( $rf_data['aior_recaptcha_theme'] ) && ! empty( $rf_data['aior_recaptcha_theme'] ) ? $rf_data['aior_recaptcha_theme'] : '';
			$ln          = isset( $rf_data['aior_recaptcha_lang'] ) && ! empty( $rf_data['aior_recaptcha_lang'] ) ? $rf_data['aior_recaptcha_lang'] : '';
			$reg_form    = isset( $rf_data['aior_recaptcha_spam_verification'] ) && ! empty( $rf_data['aior_recaptcha_spam_verification'] ) ? $rf_data['aior_recaptcha_spam_verification'] : '';
			if ( ! empty( $public_key ) && ! empty( $private_key ) && $reg_form ) {
				echo '<div id="aior_reservation_captcha"></div>';
			}
			echo '<input type="hidden" name="reservation_form_id" value="' . esc_attr( $id ) . '">';
			echo '<input type="hidden" name="action" value="aoir_reservation_form_submit">';
			wp_nonce_field( 'on_aior_rform_submit_nonce', 'aior_rform_submit_nonce' );
			do_action( 'aior_before_reservation_form_submit', $id );
			echo '<div class="solrow"><div class="solcol">';
			echo '<input type="submit" name="aior_rf_submit_' . esc_attr( $id ) . '" value="' . esc_attr__( 'Book Now', 'all-in-one-reservation' ) . '" class="restBooknow aior_book_now" data-submit-id="' . esc_attr( $id ) . '" data-redirect="' . esc_attr( $is_pay_active ) . '">';
			echo '</div></div>';

			echo '</form>';
			$nonce = ( isset( $_POST['nonce'] ) && ! empty( $_POST['nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			if ( ! isset( $_POST['action'] ) && wp_verify_nonce( $nonce, 'on_aior_admin_global_nonce' ) ) {
				do_action( 'aior_comment_form', $id );
			}
			do_action( 'aior_after_reservation_form', $id );
		}
	}
	/**
	 * Reservation Form Submit.
	 *
	 * @since    1.0.0
	 */
	public function aoir_reservation_form_submit() {
		$status = '';
		if ( isset( $_POST['aior_rform_submit_nonce'] ) ) {
			$nonce = ( isset( $_POST['aior_rform_submit_nonce'] ) && ! empty( $_POST['aior_rform_submit_nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['aior_rform_submit_nonce'] ) ) : '';
			if ( wp_verify_nonce( $nonce, 'on_aior_rform_submit_nonce' ) ) {
				$status = self::save_submit_form_data();
			} else {
				$status = array(
					'status'  => 'error',
					'message' => esc_html_e( 'Invalid nonce', 'all-in-one-reservation' ),
					'data'    => 'null',
				);
			}
			echo wp_json_encode( $status, JSON_PRETTY_PRINT );
		}
		die();
	}
	/**
	 * Save the Form Data in Database.
	 *
	 * @since    1.0.0
	 */
	public static function save_submit_form_data() {
		if ( isset( $_POST['aior_rform_submit_nonce'] ) ) {
			$nonce = ( isset( $_POST['aior_rform_submit_nonce'] ) && ! empty( $_POST['aior_rform_submit_nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['aior_rform_submit_nonce'] ) ) : '';
			if ( wp_verify_nonce( $nonce, 'on_aior_rform_submit_nonce' ) ) {
				$form                        = array();
				$fid                         = isset( $_POST['reservation_form_id'] ) ? sanitize_text_field( wp_unslash( $_POST['reservation_form_id'] ) ) : false;
				$form['rf_first_name']       = isset( $_POST['rf_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['rf_first_name'] ) ) : '';
				$form['rf_last_name']        = isset( $_POST['rf_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['rf_last_name'] ) ) : '';
				$form['rf_slot']             = isset( $_POST['rf_slot'] ) ? sanitize_text_field( wp_unslash( $_POST['rf_slot'] ) ) : '';
				$form['rf_email']            = isset( $_POST['rf_email'] ) ? sanitize_email( wp_unslash( $_POST['rf_email'] ) ) : '';
				$form['rf_note']             = isset( $_POST['rf_note'] ) ? wp_kses_post( wp_unslash( $_POST['rf_note'] ) ) : '';
				$form['reservation_form_id'] = isset( $_POST['reservation_form_id'] ) ? sanitize_text_field( wp_unslash( $_POST['reservation_form_id'] ) ) : '';
				$form['tno']                 = isset( $_POST['tno'] ) ? sanitize_text_field( wp_unslash( $_POST['tno'] ) ) : '';
				$form['tdt']                 = isset( $_POST['tdt'] ) ? sanitize_text_field( wp_unslash( $_POST['tdt'] ) ) : '';
				$form['stp']                 = isset( $_POST['stp'] ) ? sanitize_text_field( wp_unslash( $_POST['stp'] ) ) : '';
				$form['pri']                 = isset( $_POST['pri'] ) ? sanitize_text_field( wp_unslash( $_POST['pri'] ) ) : '';
				$form['aior_post_type']      = isset( $_POST['aior_post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_post_type'] ) ) : '';
				$form['response_in']         = isset( $_POST['response_in'] ) ? sanitize_text_field( wp_unslash( $_POST['response_in'] ) ) : '';
				$form['fid']                 = $fid;
				$tid                         = isset( $_POST['tid'] ) ? sanitize_text_field( wp_unslash( $_POST['tid'] ) ) : false;
				$form['tid']                 = $tid;
				$sid                         = isset( $_POST['sid'] ) ? sanitize_text_field( wp_unslash( $_POST['sid'] ) ) : false;
				$form['sid']                 = $sid;
				$aior_post_type              = isset( $_POST['aior_post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_post_type'] ) ) : 'reservation';
				$form['aior_post_type']      = $aior_post_type;
				$response_in                 = isset( $_POST['response_in'] ) ? sanitize_text_field( wp_unslash( $_POST['response_in'] ) ) : '';
				$form['response_in']         = $response_in;
				$message                     = '';
				$rf_phone_no                 = isset( $_POST['rf_phone_no'] ) ? sanitize_text_field( wp_unslash( $_POST['rf_phone_no'] ) ) : '';
				$form['rf_phone_no']         = $rf_phone_no;
				if ( ! empty( $rf_phone_no ) ) {
					$replace_data        = str_replace( '+', '', $rf_phone_no );
					$form['rf_phone_no'] = '+' . $replace_data;
				}
				if ( $fid ) {
					$form_json  = get_post_meta( $fid, 'form_json', true );
					$form_array = json_decode( $form_json, true );
					$args_a     = array( 'data' => $form );
					do_action( 'aior_rfs_before_save', $args_a );
					if ( $form_array ) {
						$new_post               = array(
							'post_title'   => 'Reservation #' . uniqid(),
							'post_content' => '',
							'post_status'  => 'publish',
							'post_type'    => $aior_post_type,
						);
						$booking_id             = wp_insert_post( $new_post );
						$_SESSION['booking_id'] = $booking_id;
						if ( $booking_id ) {
							foreach ( $form_array as $row ) {
								foreach ( $row as $column ) {
									foreach ( $column as $form_field ) {
										$key   = array_key_exists( 'n', $form_field ) ? $form_field['n'] : '';
										$value = $form[ $key ];
										update_post_meta( $booking_id, $key, $value );
									}
								}
							}
							$rf_data            = Aior_Reservation_Form::get_settings( $fid );
							$appointment_action = isset( $rf_data['appointment_action'] ) ? $rf_data['appointment_action'] : 1;
							$start_time         = isset( $start_time ) ? $start_time : '';
							$end_time           = isset( $end_time ) ? $end_time : '';
							$slot_data          = Aior_Appointment_Booking::get_slot_data( $fid, $tid, $sid );
							$start_time         = $slot_data['start_time'];
							$end_time           = $slot_data['end_time'];
							update_post_meta( $booking_id, 'status', 'pending' );
							update_post_meta( $booking_id, 'form_id', (int) $fid );
							update_post_meta( $booking_id, 'stime', $start_time );
							update_post_meta( $booking_id, 'etime', $end_time );
							update_post_meta( $booking_id, 'pid', (int) $booking_id );
							$args_b = array(
								'data' => $form,
								'pid'  => $booking_id,
							);
							do_action( 'aior_rfs_meta_save', $args_b );
							if ( ! self::is_payment_enable( $fid ) ) {
								if ( 1 == $appointment_action ) {
									$appointment_status = 'pending';
									$message_tag        = array( '{first_name}', '{last_name}', '{reservation_id}' );
									$message_val        = array( $form['rf_first_name'], $form['rf_last_name'], $booking_id );
									$message            = get_post_meta( $fid, 'notif_success_message', true );
									$message            = str_replace( $message_tag, $message_val, $message );
								} elseif ( 2 == $appointment_action ) {
									$appointment_status = 'approved';
									$message_tag        = array( '{first_name}', '{last_name}', '{reservation_id}' );
									$message_val        = array( $form['rf_first_name'], $form['rf_last_name'], $booking_id );
									$message            = 'Thank you {first_name}, We have successfully received your reservation request #{reservation_id}. Your booking has been approved. We will send you updates to the email address you provided.';
									$message            = str_replace( $message_tag, $message_val, $message );
								}
								update_post_meta( $booking_id, 'status', $appointment_status );
								$status = array(
									'status'     => 'success',
									'message'    => $message,
									'data'       => $fid,
									'ri'         => $response_in,
									'booking_id' => $booking_id,
								);
								return $status;
							}
							if ( ! self::is_payment_enable( $fid ) ) {
								$data = Aior_Appointment_Booking::get_booking_data( $booking_id );
								if ( 1 == $appointment_action ) {
									$appointment_status = 'pending';
									$message_tag        = array( '{first_name}', '{last_name}', '{reservation_id}' );
									$message_val        = array( $form['rf_first_name'], $form['rf_last_name'], $booking_id );
									$message            = get_post_meta( $fid, 'notif_success_message', true );
									$message            = str_replace( $message_tag, $message_val, $message );
								} elseif ( 2 == $appointment_action ) {
									$appointment_status = 'approved';
									$message_tag        = array( '{first_name}', '{last_name}', '{reservation_id}' );
									$message_val        = array( $form['rf_first_name'], $form['rf_last_name'], $booking_id );
									$message            = get_post_meta( $fid, 'confirm_email_body', true );
								}
								do_action( 'aior_rfs_notification', $args_b );
								update_post_meta( $booking_id, 'status', $appointment_status );
							}
							if ( ! $message ) {
								if ( 1 == $appointment_action ) {
									$message = get_post_meta( $fid, 'notif_success_message', true );
								} elseif ( 2 == $appointment_action ) {
									$message = get_post_meta( $fid, 'confirm_email_body', true );
								}
							}
							$status = array(
								'status'     => 'success',
								'message'    => $message,
								'data'       => $fid,
								'ri'         => $response_in,
								'booking_id' => $booking_id,
							);
							return $status;
						}
					} else {
						$status = array(
							'status'     => 'error',
							'message'    => esc_html_e( 'Booking ID unable to fetch.', 'all-in-one-reservation' ),
							'data'       => 'null',
							'booking_id' => false,
						);
						return $status;
					}
				} else {
					$status = array(
						'status'     => 'error',
						'message'    => esc_html_e( 'Form ID unable to fetch.', 'all-in-one-reservation' ),
						'data'       => 'null',
						'booking_id' => false,
					);
					return $status;
				}
			}
		}
	}
	/**
	 * Default Payment Modes.
	 *
	 * @since    1.0.0
	 * @param string $hook_payment_gateway Hook for Payment Gateway.
	 * @param int    $fid Form ID.
	 */
	public static function default_payment_modes( $hook_payment_gateway, $fid ) {
		$appointment_action     = get_post_meta( $fid, 'appointment_action', true );
		$payment_success_page   = get_option( 'aior_payment_success' );
		$succes_url             = get_permalink( $payment_success_page );
		$status                 = self::save_submit_form_data();
		$rf_data                = Aior_Admin::get_settings();
		$aior_currency_symbol   = isset( $rf_data['aior_currency_symbol'] ) && ! empty( $rf_data['aior_currency_symbol'] ) ? $rf_data['aior_currency_symbol'] : '$';
		$_SESSION['booking_id'] = $status['booking_id'];
		if ( ! empty( $status ) ) {
			if ( $status['booking_id'] ) {
				$order_id = $status['booking_id'];
				$price    = get_post_meta( $order_id, 'price', true );
				if ( empty( $price ) ) {
					$price = 1;
				}
				if ( 3 != $appointment_action ) {
					echo '<li class="aior_payment_gateway_cod_pay">';
					echo '<button class="aior_pay_accordion">' . esc_html__( 'Cash On Delivery (COD)', 'all-in-one-reservation' ) . '</button>';
					echo '<div class="aior_pay_panel">';
					echo '<form method="post" id="aior-cod-pay-form" class="aior-cod-pay-form" action="' . esc_url( $succes_url ) . '">';
					echo '<img class="cod_image" src=' . esc_url( plugin_dir_url( __FILE__ ) . 'src/cod.png' ) . '>';
					echo '<div class="form-row">';
					echo '<input type="hidden" name="booking_id" id="booking_id" value="' . esc_attr( $order_id ) . '">';
					echo '<label>TOTAL AMOUNT: </label><b> ' . esc_html( $aior_currency_symbol ) . ' ' . esc_html( $price ) . '</b>';
					echo '</form>';
					echo '<input type="submit" class="aior_cod_pay_btn" name="aior_cod_pay_btn" id="aior_cod_pay_btn" value="PAY NOW">';
					echo '</form>';
					echo '</div>';
					echo '</li>';
				}
			}
		}
	}
	/**
	 * Renders the Payment Gateways.
	 *
	 * @since    1.0.0
	 * @param int    $fid Form ID.
	 * @param string $form Form Data.
	 */
	public static function render_payment_gateway( $fid, $form ) {
		$enable_payment     = get_post_meta( $fid, 'enable_payment', true );
		$payment_gateway    = get_post_meta( $fid, 'payment_gateway', true );
		$is_payment_gateway = Aior_Admin::is_payment_gateway();
		if ( 1 == $enable_payment ) {
			echo '<ul class="aior_paymentg_list">';
			$hook_payment_gateway = 'aior_payment_gateway_default';
			self::default_payment_modes( $hook_payment_gateway, $fid );
			if ( $payment_gateway ) {
				foreach ( $payment_gateway as $value ) {
					$hook_payment_gateway = 'aior_payment_gateway_' . $value;
					echo '<li class="' . esc_attr( $hook_payment_gateway ) . '">';
					$pg_name = array_search( $value, $is_payment_gateway );
					echo '<button class="aior_pay_accordion">' . esc_html( $pg_name ) . '</button>';
					echo '<div class="aior_pay_panel">';
					do_action( $hook_payment_gateway, $fid, $form );
					echo '</div>';
					echo '</li>';
				}
			}
			echo '</ul>';
		}
	}
	/**
	 * Is Payment Enable or Not.
	 *
	 * @since    1.0.0
	 * @param int $fid Form ID.
	 */
	public static function is_payment_enable( $fid ) {
		$enable_payment  = get_post_meta( $fid, 'enable_payment', true );
		$payment_gateway = get_post_meta( $fid, 'payment_gateway', true );
		if ( 1 == $enable_payment ) {
			return true;
		}
	}
	/**
	 * Payment Page.
	 *
	 * @since    1.0.0
	 */
	public function payment_page() {
		ob_start();
		if ( isset( $_POST['aior_payment_submitted'] ) ) {
			do_action( 'aior_payment_gateway_page' );
		} else {
			$status = '';
			if ( isset( $_POST['aior_rform_submit_nonce'] ) ) {
				$fid   = isset( $_POST['reservation_form_id'] ) ? sanitize_text_field( wp_unslash( $_POST['reservation_form_id'] ) ) : false;
				$nonce = isset( $_POST['aior_rform_submit_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_rform_submit_nonce'] ) ) : '';
				if ( wp_verify_nonce( $nonce, 'on_aior_rform_submit_nonce' ) ) {
					/* First thing first: list all payment Gateway */
					$form            = isset( $_POST ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST ) ) : '';
					$enable_payment  = get_post_meta( $fid, 'enable_payment', true );
					$payment_gateway = get_post_meta( $fid, 'payment_gateway', true );
					if ( $enable_payment ) {
						self::render_payment_gateway( $fid, $form );
					}
				} else {
					$status = array(
						'status'  => 'error',
						'message' => esc_html_e( 'Invalid nonce', 'all-in-one-reservation' ),
						'data'    => 'null',
					);
				}
			}
		}
		$o = ob_get_contents();
		ob_end_clean();
		return $o;
	}
	/**
	 * Success Payment Page.
	 *
	 * @since    1.0.0
	 */
	public function success_payment_page() {
		ob_start();
		if ( ! empty( $_GET['item_number'] ) && ! empty( $_GET['tx'] ) && ! empty( $_GET['amt'] ) && ! empty( $_GET['cc'] ) && ! empty( $_GET['st'] ) ) {
			// Get transaction information from URL.
			$item_number    = isset( $_GET['item_number'] ) ? sanitize_text_field( wp_unslash( $_GET['item_number'] ) ) : '';
			$txn_id         = isset( $_GET['tx'] ) ? sanitize_text_field( wp_unslash( $_GET['tx'] ) ) : '';
			$payment_gross  = isset( $_GET['amt'] ) ? sanitize_text_field( wp_unslash( $_GET['amt'] ) ) : '';
			$currency_code  = isset( $_GET['cc'] ) ? sanitize_text_field( wp_unslash( $_GET['cc'] ) ) : '';
			$payment_status = isset( $_GET['st'] ) ? sanitize_text_field( wp_unslash( $_GET['st'] ) ) : '';
			$rf_data        = Aior_Admin::get_settings();
			$product_name   = isset( $rf_data['aior_paypal_site_name'] ) && ! empty( $rf_data['aior_paypal_site_name'] ) ? $rf_data['aior_paypal_site_name'] : '';
			$product_price  = get_post_meta( $item_number, 'price', true );

			update_post_meta( $item_number, 'item_number', (int) $item_number );
			update_post_meta( $item_number, 'txn_id', (int) $txn_id );
			update_post_meta( $item_number, 'payment_amt', sanitize_text_field( $payment_gross ) );
			update_post_meta( $item_number, 'currency_code', sanitize_text_field( $currency_code ) );
			update_post_meta( $item_number, 'payment', sanitize_text_field( $payment_status ) );
			update_post_meta( $item_number, 'pay_method', 'PayPal' );
			$args_b = array(
				'data' => '',
				'pid'  => $item_number,
			);
			do_action( 'aior_rfs_notification', $args_b );
		} else {
			$rf_data           = Aior_Admin::get_settings();
			$booking_id        = isset( $_POST['booking_id'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_id'] ) ) : '';
			$aior_account_name = isset( $rf_data['aior_account_name'] ) ? $rf_data['aior_account_name'] : '';
			$aior_account_no   = isset( $rf_data['aior_account_no'] ) ? $rf_data['aior_account_no'] : '';
			$aior_bank_name    = isset( $rf_data['aior_bank_name'] ) ? $rf_data['aior_bank_name'] : '';
			$aior_ifsc_code    = isset( $rf_data['aior_ifsc_code'] ) ? $rf_data['aior_ifsc_code'] : '';
			$nonce             = ( isset( $_POST['aior_dbt_pay_nonce'] ) && ! empty( $_POST['aior_dbt_pay_nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['aior_dbt_pay_nonce'] ) ) : '';
			if ( wp_verify_nonce( $nonce, 'aior_dbt_pay_nonce' ) && isset( $_POST['aior_dbt_pay_btn'] ) && ( ! empty( $aior_account_name ) && ! empty( $aior_account_no ) && ! empty( $aior_bank_name ) && ! empty( $aior_ifsc_code ) ) ) {
				update_post_meta( $booking_id, 'payment_mode', 'direct_bank_transfer' );
				update_post_meta( $booking_id, 'payment', 'completed' );
				update_post_meta( $booking_id, 'pay_method', 'Direct Bank Transfer' );
				?>
				<div class="container">
					<div class="status">
						<h3 class="success">
							<?php
							echo esc_html( $aior_account_name );
							esc_html_e( ' , We are glad to inform you that we have confirmed your payment.', 'all-in-one-reservation' );
							?>
						</h3>
						<h4><?php esc_html_e( 'Payment Information', 'all-in-one-reservation' ); ?></h4>
						<p><b><?php esc_html_e( 'Reference Number:', 'all-in-one-reservation' ); ?></b> <?php echo esc_html( $booking_id ); ?></p>
						<p><b><?php esc_html_e( 'Account Name:', 'all-in-one-reservation' ); ?></b> <?php echo esc_html( $aior_account_name ); ?></p>
						<p><b><?php esc_html_e( 'Account Number:', 'all-in-one-reservation' ); ?></b> <?php echo esc_html( $aior_account_no ); ?></p>
						<p><b><?php esc_html_e( 'Bank Name:', 'all-in-one-reservation' ); ?></b> <?php echo esc_html( $aior_bank_name ); ?></p>
						<h5><b><?php esc_html_e( 'For Further Communication, Contact to Admin.', 'all-in-one-reservation' ); ?></b></h5>
					</div>
				</div>
				<?php
				$args_b = array(
					'data' => '',
					'pid'  => $booking_id,
				);
				do_action( 'aior_rfs_notification', $args_b );
			} elseif ( isset( $_POST['aior_cod_pay_btn'] ) ) {
				$booking_id = isset( $_POST['booking_id'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_id'] ) ) : '';
				?>
				<h3 class="success"><?php esc_html_e( 'We’re glad to inform you that we have received your booking. Once your payment will done, than we will proceed further.', 'all-in-one-reservation' ); ?></h3>
				<?php
				update_post_meta( $booking_id, 'payment_mode', 'cash_on_delivery' );
				update_post_meta( $booking_id, 'payment', 'completed' );
				update_post_meta( $booking_id, 'pay_method', 'Cash On Delivery' );
				$args_b = array(
					'data' => '',
					'pid'  => $booking_id,
				);
				do_action( 'aior_rfs_notification', $args_b );
			} else {
				$payment_cancel_page = get_option( 'aior_cancel' );
				$cancel_url          = get_permalink( $payment_cancel_page );
				wp_redirect( $cancel_url );
				exit();
			}
		}
		?>
		<div class="container">
			<div class="status">
				<?php if ( ! empty( $item_number ) ) { ?>
					<h3 class="success"><?php esc_html_e( 'We’re glad to inform you that we have confirmed your payment.', 'all-in-one-reservation' ); ?></h3>
					<h4><?php esc_html_e( 'Payment Information', 'all-in-one-reservation' ); ?></h4>
					<p><b><?php esc_html_e( 'Reference Number:', 'all-in-one-reservation' ); ?></b> <?php echo esc_html( $item_number ); ?></p>
					<p><b><?php esc_html_e( 'Transcation ID:', 'all-in-one-reservation' ); ?></b> <?php echo esc_html( $txn_id ); ?></p>
					<p><b><?php esc_html_e( 'Paid Amount:', 'all-in-one-reservation' ); ?></b> <?php echo esc_html( $payment_gross . ' ' . $currency_code ); ?></p>
					<p><b><?php esc_html_e( 'Payment Status:', 'all-in-one-reservation' ); ?></b> <?php echo esc_html( $payment_status ); ?></p>
					<h4><?php esc_html_e( 'Product Information', 'all-in-one-reservation' ); ?></h4>
					<p><b><?php esc_html_e( 'Name:', 'all-in-one-reservation' ); ?></b> <?php echo esc_html( $product_name ); ?></p>
					<p><b><?php esc_html_e( 'Price:', 'all-in-one-reservation' ); ?></b> <?php echo esc_html( $product_price ); ?></p>
				<?php } ?>
			</div>
		</div>
		<?php
		session_destroy();
		$o = ob_get_contents();
		ob_end_clean();
		return $o;
	}
	/**
	 * Translates the String.
	 *
	 * @since    1.0.0
	 */
	public static function translate_string() {
		$array = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'lang'    => array(
				1 => esc_html__( 'Please Wait!', 'all-in-one-reservation' ),
			),
		);
		$array = apply_filters( 'aior_obj', $array );
		return $array;
	}
}
