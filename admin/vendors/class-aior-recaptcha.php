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
class Aior_Recaptcha {
	/**
	 * Signup url.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $signup_url    signup url.
	 */
	private static $signup_url = 'https://www.google.com/recaptcha/admin';
	/**
	 * Verify url.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $site_verify_url    verify url.
	 */
	private static $site_verify_url = 'https://www.google.com/recaptcha/api/siteverify?';
	/**
	 * Secret.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $s_secret    secret.
	 */
	private $s_secret;
	/**
	 * Version.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $_version    version.
	 */
	private static $_version = 'php_1.0';
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$rf_data = Aior_Admin::get_settings();
		$secret  = isset( $rf_data['aior_recaptcha_private_key'] ) && ! empty( $rf_data['aior_recaptcha_private_key'] ) ? $rf_data['aior_recaptcha_private_key'] : '';
		if ( null == $secret || '' == $secret ) {
			die(
				esc_html__( 'To use reCAPTCHA you must get an API key from', 'all-in-one-reservation' ) . " <a href='"
					. esc_url( self::$signup_url ) . "'>" . esc_url( self::$signup_url ) . '</a>'
			);
		}
		$this->s_secret = $secret;
	}
	/**
	 * Encodes the given data into a query string format.
	 *
	 * @param array $data array of string elements to be encoded.
	 *
	 * @return string - encoded request.
	 */
	private function encode_qs( $data ) {
		$req = '';
		foreach ( $data as $key => $value ) {
			$req .= $key . '=' . rawurlencode( stripslashes( $value ) ) . '&';
		}
		// Cut the last '&'.
		$req = substr( $req, 0, strlen( $req ) - 1 );
		return $req;
	}

	/**
	 * Submits an HTTP GET to a reCAPTCHA server.
	 *
	 * @param string $path url path to recaptcha server.
	 * @param array  $data array of parameters to be sent.
	 *
	 * @return string response
	 */
	private function submit_http_get( $path, $data ) {
		$req      = $this->encode_qs( $data );
		$response = wp_remote_get( $path . $req );
		return wp_remote_retrieve_body( $response );
	}
	/**
	 * Calls the reCAPTCHA siteverify API to verify whether the user passes
	 * CAPTCHA test.
	 *
	 * @param string $remote_ip IP address of end user.
	 * @param string $response response string from recaptcha verification.
	 *
	 * @return ReCaptchaResponse
	 */
	public function verify_response( $remote_ip, $response ) {
		// Discard empty solution submissions.
		if ( null == $response || 0 == strlen( $response ) ) {
			$recaptcha_response              = new Aior_Recaptcha_Response();
			$recaptcha_response->success     = false;
			$recaptcha_response->error_codes = 'missing-input';
			return $recaptcha_response;
		}
		$get_response = $this->submit_http_get(
			self::$site_verify_url,
			array(
				'secret'   => $this->s_secret,
				'remoteip' => $remote_ip,
				'v'        => self::$_version,
				'response' => $response,
			)
		);

		$answers            = json_decode( $get_response, true );
		$recaptcha_response = new Aior_Recaptcha_Response();
		if ( trim( $answers['success'] ) == true ) {
			$recaptcha_response->success = true;
		} else {
			$recaptcha_response->success     = false;
			$recaptcha_response->error_codes = $answers['error - codes'];
		}
		return $recaptcha_response;
	}
}

/**
 * Recaptcha functionality of the plugin.
 *
 * @package    Aior
 * @subpackage Aior/admin
 * @author     Solwin Infotech <support@solwininfotech.com>
 */
class Aior_Recaptcha_Response {
	/**
	 * Response status.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $success    Response status.
	 */
	public $success;
	/**
	 * Response status.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $error_codes    Response status.
	 */
	public $error_codes;
}
