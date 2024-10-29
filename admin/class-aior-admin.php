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
class Aior_Admin {
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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'wp_ajax_aoir_reservation_global_ajax', array( $this, 'aoir_reservation_global_ajax' ) );
		add_action( 'wp_ajax_nopriv_aoir_reservation_global_ajax', array( $this, 'aoir_reservation_global_ajax' ) );
		add_action( 'init', array( $this, 'aior_ical_feed' ) );
		require_once 'class-aior-revenue.php';
		require_once 'partials/admin-settings-tab-dashboard.php';
		require_once 'partials/admin-settings-tab-settings.php';
		require_once 'partials/admin-settings-tab-import.php';
		require_once 'partials/admin-settings-tab-security.php';
		require_once 'partials/admin-settings-tab-calendar-feeds.php';
		add_action( 'admin_enqueue_scripts', array( $this, 'load_media_files' ) );
	}
	/**
	 * Includes Media.
	 */
	public function load_media_files() {
		wp_enqueue_media();
	}
	/**
	 * Includes the calendar feeds file.
	 */
	public function aior_ical_feed() {

		if ( isset( $_GET['all_in_one_ical'] ) ) :
			include plugin_dir_path( __DIR__ ) . 'admin/calendar-feeds.php';
			exit;
		endif;

	}

	/**
	 * Creates the tab on Admin.
	 *
	 * @param      array $args       Pass the id of tab.
	 */
	public static function create_tab( $args ) {
		add_action(
			'aior_admin_settings_add_tab_nav',
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
			'aior_admin_settings_add_tab_content',
			function() use ( $args ) {
				$args['title'] = isset( $args['title'] ) ? $args['title'] : '';
				$args['desc']  = isset( $args['desc'] ) ? $args['desc'] : '';
				?>
			<div id="aior-tab-<?php echo esc_attr( $args['id'] ); ?>-content" class="aior-tab-content">
				<h3><?php echo esc_html( $args['title'] ); ?></h3>
				<p><?php echo esc_html( $args['desc'] ); ?></p>
				<?php echo esc_html( apply_filters( $args['filter'], '' ) ); ?>
			</div>
				<?php
			}
		);
	}
	/**
	 * Creates Tab in Reservation Settings.
	 */
	public static function create_reservation_settings() {
		?>
		<div class="aior-tabs-container">
			<ul class="aior-tabs">
			<?php
				do_action( 'aior_admin_settings_add_tab_nav' );
			?>
			</ul>
			<div class="aior-tab-content-container">
			<?php
				do_action( 'aior_admin_settings_add_tab_content' );
			?>
			</div>
		</div>
		<?php
	}
	/**
	 * A golbal method to handle ajax requrest, can hook sperately from remote method: aoir_reservation_global_ajax.
	 *
	 * @since    1.0.0
	 */
	public function aoir_reservation_global_ajax() {
		$status = '';
		if ( isset( $_POST['action'] ) ) {
			$nonce = ( isset( $_POST['nonce'] ) && ! empty( $_POST['nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			if ( wp_verify_nonce( $nonce, 'on_aior_admin_global_nonce' ) ) {
				$act = isset( $_POST['act'] ) ? sanitize_text_field( wp_unslash( $_POST['act'] ) ) : '';
				if ( 'aior_add_on_act' === $act ) {
					$slug  = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
					$todo  = isset( $_POST['todo'] ) ? sanitize_text_field( wp_unslash( $_POST['todo'] ) ) : '';
					$addon = get_option( 'aior_addon_status' ) ? json_decode( get_option( 'aior_addon_status' ), true ) : array();
					if ( 1 == $todo ) {
						$addon[] = $slug;
						if ( $addon ) {
							$addon = array_values( $addon );
							$json  = wp_json_encode( $addon );
							update_option( 'aior_addon_status', $json );
							$status = array(
								'status' => 'success',
								'data'   => '0',
							);
							echo wp_json_encode( $status, JSON_PRETTY_PRINT );
						}
					} else {
						$key = array_search( $slug, $addon );
						if ( false !== $key ) {
							unset( $addon[ $key ] );
							$addon = array_values( $addon );
							$json  = wp_json_encode( $addon );
							update_option( 'aior_addon_status', $json );
							$status = array(
								'status' => 'success',
								'data'   => '1',
							);
							echo wp_json_encode( $status, JSON_PRETTY_PRINT );
						}
					}
				}
				do_action( 'aoir_reservation_global_ajax', $_POST );
			}
		}
		die();
	}
	/**
	 * Create Menu Page.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {
		add_menu_page( esc_html__( 'Reservations Settings', 'all-in-one-reservation' ), esc_html__( 'Reservations', 'all-in-one-reservation' ), 'manage_options', 'aior', array( $this, 'aior_settings_page' ), 'dashicons-calendar-alt', 5 );
		$r_menu = 'aior';
		do_action( 'aior_before_admin_menu', $r_menu );
		add_submenu_page( $r_menu, esc_html__( 'Reservation Forms', 'all-in-one-reservation' ), esc_html__( 'Reservation Forms', 'all-in-one-reservation' ), 'manage_options', 'edit.php?post_type=sol_reservation_form' );
		add_submenu_page( $r_menu, esc_html__( 'Add New Form', 'all-in-one-reservation' ), esc_html__( 'Add New Form', 'all-in-one-reservation' ), 'manage_options', 'post-new.php?post_type=sol_reservation_form' );
		add_submenu_page( $r_menu, esc_html__( 'Add-On', 'all-in-one-reservation' ), esc_html__( 'Add-On', 'all-in-one-reservation' ), 'manage_options', 'aior_add_on', array( $this, 'add_on_list' ) );
		do_action( 'aior_after_admin_menu', $r_menu );
	}

	/**
	 * List the table of Add-On.
	 *
	 * @since    1.0.0
	 */
	public function add_on_list() {
		$add_on_list_table = new Aior_Add_On_List();
		$add_on_list_table->prepare_items();
		?>
			<div class="wrap">
				<h2><?php esc_html_e( 'AIOR Add-on', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-admin-plugins"></span></h2>
				<?php $add_on_list_table->display(); ?>
			</div>
		<?php
	}

	/**
	 * Displays the different setting tabs on Admin.
	 *
	 * @since    1.0.0
	 */
	public function aior_settings_page() {
		self::aior_save_admin_settings();
		?>
		<div class="wrap aior_admin_settings">
			<form action="admin.php?page=aior" method="post">
				<?php
					self::create_reservation_settings();
				?>
				<p></p>
				<?php
					wp_nonce_field( 'aior_save_admin_settings_nonce', 'aior_save_admin_settings_nonce' );
				?>
				<input type="submit" value="<?php esc_html_e( 'Save Settings', 'all-in-one-reservation' ); ?>" class="button button-primary button-large" name="aior_admin_settings_save" id="aior_admin_settings_save">
			</form>
		</div>
		<?php
	}
	/**
	 * Saves the Admin Settings Page.
	 *
	 * @since    1.0.0
	 */
	public static function aior_save_admin_settings() {
		if ( isset( $_POST['aior_admin_settings_save'] ) ) {
			$nonce = ( isset( $_POST['aior_save_admin_settings_nonce'] ) && ! empty( $_POST['aior_save_admin_settings_nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['aior_save_admin_settings_nonce'] ) ) : '';
			if ( wp_verify_nonce( $nonce, 'aior_save_admin_settings_nonce' ) ) {
				$data                                     = array();
				$data['aior_currency']                    = isset( $_POST['aior_currency'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_currency'] ) ) : '';
				$data['aior_currency_symbol']             = isset( $_POST['aior_currency_symbol'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_currency_symbol'] ) ) : '';
				$data['aior_recaptcha_public_key']        = isset( $_POST['aior_recaptcha_public_key'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_recaptcha_public_key'] ) ) : '';
				$data['aior_recaptcha_private_key']       = isset( $_POST['aior_recaptcha_private_key'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_recaptcha_private_key'] ) ) : '';
				$data['aior_recaptcha_theme']             = isset( $_POST['aior_recaptcha_theme'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_recaptcha_theme'] ) ) : '';
				$data['aior_recaptcha_lang']              = isset( $_POST['aior_recaptcha_lang'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_recaptcha_lang'] ) ) : '';
				$data['aior_recaptcha_spam_verification'] = isset( $_POST['aior_recaptcha_spam_verification'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_recaptcha_spam_verification'] ) ) : '';
				$data['aior_twilio_sid']                  = isset( $_POST['aior_twilio_sid'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_twilio_sid'] ) ) : '';
				$data['aior_twilio_token']                = isset( $_POST['aior_twilio_token'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_twilio_token'] ) ) : '';
				$data['aior_twilio_sender_id']            = isset( $_POST['aior_twilio_sender_id'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_twilio_sender_id'] ) ) : '';
				$data['aior_twilio_admin_mobile']         = isset( $_POST['aior_twilio_admin_mobile'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_twilio_admin_mobile'] ) ) : '';
				$data['aior_account_name']                = isset( $_POST['aior_account_name'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_account_name'] ) ) : '';
				$data['aior_account_no']                  = isset( $_POST['aior_account_no'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_account_no'] ) ) : '';
				$data['aior_bank_name']                   = isset( $_POST['aior_bank_name'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_bank_name'] ) ) : '';
				$data['aior_ifsc_code']                   = isset( $_POST['aior_ifsc_code'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_ifsc_code'] ) ) : '';
				$data['aior_paypal_live_mode']            = isset( $_POST['aior_paypal_live_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_paypal_live_mode'] ) ) : '';
				$data['aior_paypal_email_id']             = isset( $_POST['aior_paypal_email_id'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_paypal_email_id'] ) ) : '';
				$data['aior_paypal_site_name']            = isset( $_POST['aior_paypal_site_name'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_paypal_site_name'] ) ) : '';
				$data['aior_paypal_currency']             = isset( $_POST['aior_paypal_currency'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_paypal_currency'] ) ) : '';
				$data['aior_paypal_currency_symbol']      = isset( $_POST['aior_paypal_currency_symbol'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_paypal_currency_symbol'] ) ) : '';
				$data['aior_stripe_live_mode']            = isset( $_POST['aior_stripe_live_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_live_mode'] ) ) : '';
				$data['aior_stripe_test_secret_key']      = isset( $_POST['aior_stripe_test_secret_key'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_test_secret_key'] ) ) : '';
				$data['aior_stripe_test_publishable_key'] = isset( $_POST['aior_stripe_test_publishable_key'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_test_publishable_key'] ) ) : '';
				$data['aior_stripe_live_secret_key']      = isset( $_POST['aior_stripe_live_secret_key'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_live_secret_key'] ) ) : '';
				$data['aior_stripe_live_publishable_key'] = isset( $_POST['aior_stripe_live_publishable_key'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_live_publishable_key'] ) ) : '';
				$data['aior_stripe_site_name']            = isset( $_POST['aior_stripe_site_name'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_site_name'] ) ) : '';
				$data['aior_stripe_currency']             = isset( $_POST['aior_stripe_currency'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_currency'] ) ) : '';
				$data['aior_stripe_currency_symbol']      = isset( $_POST['aior_stripe_currency_symbol'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_currency_symbol'] ) ) : '';
				$data['aior_stripe_image_url']            = isset( $_POST['aior_stripe_image_url'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_image_url'] ) ) : '';
				$data['aior_stripe_payment_form_title']   = isset( $_POST['aior_stripe_payment_form_title'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_payment_form_title'] ) ) : '';
				$data['aior_stripe_checkout_button']      = isset( $_POST['aior_stripe_checkout_button'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_checkout_button'] ) ) : '';
				$data['aior_stripe_body_bg_color']        = isset( $_POST['aior_stripe_body_bg_color'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_body_bg_color'] ) ) : '';
				$data['aior_stripe_body_text_color']      = isset( $_POST['aior_stripe_body_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_body_text_color'] ) ) : '';
				$data['aior_stripe_border_color']         = isset( $_POST['aior_stripe_border_color'] ) ? sanitize_text_field( wp_unslash( $_POST['aior_stripe_border_color'] ) ) : '';
				do_action( 'aior_admin_settings_before_save', $data );
				$json_data = wp_json_encode( $data );
				update_option( 'aior_admin_settings', $json_data );
				do_action( 'aior_admin_settings_after_save', $data );
				?>
				<div class='aior_success_msg'>
					<?php esc_html_e( 'Settings saved successfully.', 'all-in-one-reservation' ); ?>
				</div>
				<?php
			}
		}
	}
	/**
	 * Get the settings data.
	 *
	 * @since    1.0.0
	 */
	public static function get_settings() {
		$empty_array = array(
			'aior_currency'                    => '',
			'aior_currency_symbol'             => '',
			'aior_twilio_sid'                  => '',
			'aior_twilio_token'                => '',
			'aior_twilio_sender_id'            => '',
			'aior_twilio_admin_mobile'         => '',
			'aior_recaptcha_public_key'        => '',
			'aior_recaptcha_private_key'       => '',
			'aior_recaptcha_theme'             => '',
			'aior_recaptcha_lang'              => '',
			'aior_recaptcha_spam_verification' => '',
		);
		$json        = get_option( 'aior_admin_settings', true );
		$arr         = json_decode( $json, true );
		$arr         = array_merge( $empty_array, (array) $arr );
		if ( $arr ) {
			return $arr;
		}
	}

	/**
	 * Run page create method.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function run() {
		$aior_pay_page = get_option( 'aior_pay' );
		if ( empty( $aior_pay_page ) ) {
			$payment_page = array(
				'uid'          => 'aior_pay',
				'post_title'   => esc_html__( 'Reservation Payment', 'all-in-one-reservation' ),
				'post_content' => esc_html__( 'Select payment option from below:', 'all-in-one-reservation' ) .
				'[aior_pay]',
			);
			self::create_page( $payment_page );
		}
		$aior_cancel_page = get_option( 'aior_cancel' );
		update_option( 'test_aior_option', $aior_cancel_page );
		if ( empty( $aior_cancel_page ) ) {
			$cancel_page = array(
				'uid'          => 'aior_cancel',
				'post_title'   => esc_html__( 'Reservation Cancel', 'all-in-one-reservation' ),
				'post_content' => '[aior_cancel_page]',
			);
			self::create_page( $cancel_page );
		}
		$aior_payment_success = get_option( 'aior_payment_success' );
		if ( empty( $aior_payment_success ) ) {
			$payment_page = array(
				'uid'          => 'aior_payment_success',
				'post_title'   => esc_html__( 'Payment Success', 'all-in-one-reservation' ),
				'post_content' => '[aior_pay_success]',
			);
			self::create_page( $payment_page );
		}
	}

	/**
	 * Create pages.
	 *
	 * @since 1.0.0
	 * @param array $args Page arguments.
	 * @return void
	 */
	public static function create_page( $args ) {
		global $user_id;
		$page['post_type']    = 'page';
		$page['post_title']   = $args['post_title'];
		$page['post_content'] = $args['post_content'];
		$page['post_parent']  = 0;
		$page['post_author']  = $user_id;
		$page['post_status']  = 'publish';
		$new_page_id          = wp_insert_post( $page );
		if ( $new_page_id ) {
			update_option( $args['uid'], $new_page_id );
		}
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 *
	 * @param string $hook Retrieve Hooks.
	 */
	public function enqueue_styles( $hook ) {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aior-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jquery-date-range-picker', AIOR_PLUGIN_URL . '/lib/jquery-date-range-picker/daterangepicker.min-admin.css', array(), '0.21.1', 'all' );
		wp_enqueue_style( 'jquery_timepicker_css', AIOR_PLUGIN_URL . '/lib/timepicker/jquery.timepicker.min.css', array(), '1.3.5', 'all' );
		wp_enqueue_style( 'sweetalert2', AIOR_PLUGIN_URL . '/lib/sweetalert2/sweetalert2.min.css', array(), '10.16.6', 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 *
	 * @param string $hook Retrieve Hooks.
	 */
	public function enqueue_scripts( $hook ) {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/aior-admin.js', array( 'jquery', 'jquery-ui-datepicker' ), $this->version, false );
		wp_enqueue_script( 'jquery-date-range-picker', AIOR_PLUGIN_URL . '/lib/jquery-date-range-picker/jquery.daterangepicker.min.js', array( 'jquery', 'moment' ), '0.21.1', true );
		wp_enqueue_script( 'aior-hooks', AIOR_PLUGIN_URL . '/lib/aior-hooks/aior-hooks.js', array( 'jquery', 'underscore' ), '1.0', false );
		wp_enqueue_script( 'jquery-timepicker', AIOR_PLUGIN_URL . '/lib/timepicker/jquery.timepicker.min.js', array( 'jquery' ), '1.3.5', false );
		wp_enqueue_script( 'aior-form-builder', plugin_dir_url( __FILE__ ) . 'js/aior-form-builder.js', array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-droppable', 'wp-color-picker' ), '1.0.0', false );
		wp_localize_script( $this->plugin_name, 'aior_obj', self::translate_string() );
		wp_enqueue_script( 'sweetalert2', AIOR_PLUGIN_URL . '/lib/sweetalert2/sweetalert2.all.min.js', array( 'jquery' ), '10.16.6', false );
		wp_enqueue_script( 'chartjs', AIOR_PLUGIN_URL . '/lib/chart/chart.min.js', array( 'jquery' ), '3.3.2', false );
	}
	/**
	 * Translates the string.
	 *
	 * @since    1.0.0
	 */
	public static function translate_string() {
		$array = array(
			'lang' => array(
				'title' => esc_html__( 'Title', 'all-in-one-reservation' ),
			),
		);
		$array = apply_filters( 'aior_obj_admin', $array );
		return $array;
	}
	/**
	 * Checks that it is payment Gateway or not.
	 *
	 * @since    1.0.0
	 */
	public static function is_payment_gateway() {
		$array = array();
		$array = apply_filters( 'aior_payment_gateway', $array );
		return $array;
	}
}
