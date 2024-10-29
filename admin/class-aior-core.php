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
class Aior_Core {
	/**
	 * The METHOD for encryption.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $METHOD    The ID of this plugin.
	 */
	protected const METHOD            = 'aes-256-cbc';
	protected const HASHING_ALGORITHM = 'sha256';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}
	/**
	 * Encrypts (but does not authenticate) a message.
	 *
	 * @param string  $message - plaintext message.
	 * @param string  $key - encryption key (raw binary expected).
	 * @param boolean $encode - set to TRUE to return a base64-encoded.
	 * @return string (raw binary)
	 */
	public static function encrypt( $message, $key, $encode = false ) {
		$nonce_size = openssl_cipher_iv_length( self::METHOD );
		$nonce      = openssl_random_pseudo_bytes( $nonce_size );
		$ciphertext = openssl_encrypt(
			$message,
			self::METHOD,
			$key,
			OPENSSL_RAW_DATA,
			$nonce
		);
		/* Naively, we can just concatenate */
		if ( $encode ) {
			return strtr( base64_encode( $nonce . $ciphertext ), '+/=', '._-' );
		}
		return $nonce . $ciphertext;
	}

	/**
	 * Decrypts (but does not verify) a message
	 *
	 * @param string  $message - ciphertext message.
	 * @param string  $key - encryption key (raw binary expected).
	 * @param boolean $encoded - are we expecting an encoded string?.
	 * @return string (raw binary)
	 * @throws Exception Encryption failure.
	 */
	public static function decrypt( $message, $key, $encoded = false ) {
		if ( $encoded ) {
			$message = base64_decode( strtr( $message, '._-', '+/=' ) );
			if ( false == $message ) {
				throw new Exception( 'Encryption failure' );
			}
		}
		$nonce_size = openssl_cipher_iv_length( self::METHOD );
		$nonce      = mb_substr( $message, 0, $nonce_size, '8bit' );
		$ciphertext = mb_substr( $message, $nonce_size, null, '8bit' );
		$plaintext  = openssl_decrypt(
			$ciphertext,
			self::METHOD,
			$key,
			OPENSSL_RAW_DATA,
			$nonce
		);
		return $plaintext;
	}
	/**
	 * Html Keses default.
	 *
	 * @since 1.0
	 * @return array
	 */
	public static function html_kses() {
		$allowed_atts = array(
			'align'      => array(),
			'class'      => array(),
			'type'       => array(),
			'id'         => array(),
			'dir'        => array(),
			'lang'       => array(),
			'style'      => array(),
			'xml:lang'   => array(),
			'src'        => array(),
			'alt'        => array(),
			'href'       => array(),
			'rel'        => array(),
			'rev'        => array(),
			'target'     => array(),
			'novalidate' => array(),
			'type'       => array(),
			'value'      => array(),
			'name'       => array(),
			'tabindex'   => array(),
			'action'     => array(),
			'method'     => array(),
			'for'        => array(),
			'width'      => array(),
			'height'     => array(),
			'data'       => array(),
			'title'      => array(),
		);
		$allowed_tags = wp_kses_allowed_html( 'post' );
		return $allowed_tags;
	}

	/**
	 * Argument for Kses.
	 *
	 * @since    1.0.0
	 * @return  array
	 */
	public static function args_kses() {
		$args_kses = array(
			'div'    => array(
				'class'  => true,
				'id'     => true,
				'style'  => true,
				'script' => true,
			),
			'script' => array(
				'type'    => true,
				'charset' => true,
			),
			'style'  => array(
				'type' => true,
			),
			'iframe' => array(
				'src'          => true,
				'style'        => true,
				'marginwidth'  => true,
				'marginheight' => true,
				'scrolling'    => true,
				'frameborder'  => true,
			),
			'img'    => array(
				'src' => true,
			),
			'a'      => array(
				'href'  => true,
				'class' => true,
			),
			'ul'     => array(
				'class' => true,
				'id'    => true,
				'style' => true,
			),
			'li'     => array(
				'class' => true,
				'id'    => true,
				'style' => true,
			),
			'b'      => array(),
			'br'     => array(),
			'small'  => array(),
			'input'  => array(
				'class'       => true,
				'type'        => true,
				'name'        => true,
				'value'       => true,
				'placeholder' => true,
			),
			'label'  => array(),

		);
		return $args_kses;
	}
}

$aior_core = new Aior_Core();
