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
class Aior_Package {

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
	 */
	public function __construct() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
	}
	/**
	 * Load the Add-On Status.
	 *
	 * @since    1.0.0
	 */
	public function do_load_add_on() {
		$all_add_on = self::combine_add_ons();
		if ( ! empty( $all_add_on ) ) {
			$add_on_json = get_option( 'aior_addon_status' );
			$status      = json_decode( $add_on_json, true );
			if ( ! empty( $status ) ) {
				foreach ( $all_add_on as $key => $value ) {
					if ( in_array( $key, $status ) ) {
						require_once $value;
					}
				}
			}
		}
	}
	/**
	 * Combines the Add-Ons.
	 *
	 * @since    1.0.0
	 */
	public static function combine_add_ons() {
		$add_ons = array();
		$arr_1   = self::get_add_on_list_from( WP_PLUGIN_DIR, 'index' );
		$arr_2   = self::get_add_on_list_from( get_stylesheet_directory() . '/aior-add-on', 'index' );
		if ( ! empty( $arr_1 ) ) {
			foreach ( $arr_1 as  $pfile ) {
				$base             = basename( dirname( $pfile ) );
				$url              = $pfile;
				$add_ons[ $base ] = $url;
			}
		}
		if ( ! empty( $arr_2 ) ) {
			foreach ( $arr_2 as  $tfile ) {
				$base             = basename( dirname( $tfile ) );
				$url              = $tfile;
				$add_ons[ $base ] = $url;
			}
		}
		if ( ! empty( $add_ons ) ) {
			return $add_ons;
		}
	}
	/**
	 * Get the Add-On List.
	 *
	 * @since    1.0.0
	 */
	public static function get_add_on_list() {
		$arr_1     = self::get_add_on_list_from( WP_PLUGIN_DIR . '/all-in-one-reservation', 'json' );
		$arr_3     = $arr_1;
		$file_list = array();
		if ( ! empty( $arr_3 ) ) {
			foreach ( $arr_3 as $file ) {
				if ( file_exists( $file ) ) {
					array_push( $file_list, $file );
				}
			}
		}
		return $file_list;
	}
	/**
	 * Get the Add-On List from file.
	 *
	 * @since    1.0.0
	 * @param string $dir Directory of File.
	 * @param string $type File Type of Add-on.
	 */
	public static function get_add_on_list_from( $dir, $type ) {
		if ( is_dir( $dir ) ) {
			$ffs       = scandir( $dir );
			$ffs       = array( 'paypal', 'pay-stripe', 'direct-bank-transfer', 'twilio-sms' );
			$res       = array();
			$file_list = array();
			if ( count( $ffs ) < 1 ) {
				return;
			}
			foreach ( $ffs as $ff ) {
				if ( is_dir( $dir . '/' . $ff ) ) {
					if ( 'json' === $type ) {
						$add_on_path = $dir . '/' . $ff . '/addon.json';
					} elseif ( 'index' === $type ) {
						$add_on_path = $dir . '/' . $ff . '/index.php';
					}
					array_push( $file_list, $add_on_path );
					$res[] = $ff;
				} else {
					if ( 'json' === $type ) {
						$add_on_path = $dir . '/addon.json';
					} elseif ( 'index' === $type ) {
						$add_on_path = $dir . '/index.php';
					}
					array_push( $file_list, $add_on_path );
				}
			}
			if ( ! empty( $res ) ) {
				$sms_status    = self::get_plugin_status( 'twilio-sms/class-aior-twilio.php' );
				$paypal_status = self::get_plugin_status( 'paypal/class-aior-paypal.php' );
				$stripe_status = self::get_plugin_status( 'pay-stripe/class-aior-stripe.php' );
				$dbt_status    = self::get_plugin_status( 'direct-bank-transfer/class-aior-dbt.php' );
				$new_arr       = array();
				if ( 1 == $sms_status ) {
					$new_arr[] = 'twilio-sms';
				}
				if ( 1 == $paypal_status ) {
					$new_arr[] = 'paypal';
				}
				if ( 1 == $stripe_status ) {
					$new_arr[] = 'pay-stripe';
				}
				if ( 1 == $dbt_status ) {
					$new_arr[] = 'direct-bank-transfer';
				}
				$json = wp_json_encode( $new_arr );
				update_option( 'aior_addon_status', $json );
			}
			if ( ! empty( $file_list ) ) {
				return $file_list;
			}
		}
	}
	/**
	 * Checks that plugin is activated or not.
	 *
	 * @param string $location Location.
	 */
	public static function get_plugin_status( $location = '' ) {

		if ( is_plugin_active( $location ) ) {
			return 1;
		}

		if ( ! file_exists( trailingslashit( WP_PLUGIN_DIR ) . $location ) ) {
			return false;
		}

		if ( is_plugin_inactive( $location ) ) {
			return 2;
		}
	}
	/**
	 * Display the list table page
	 */
	public static function list_table_page() {
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
	 * Provides the Information of Add-On.
	 *
	 * @since    1.0.0
	 */
	public static function info() {
		global $wp_filesystem;
		WP_Filesystem();
		$add_on_list = self::get_add_on_list();
		if ( $add_on_list ) {
			$new_arr = array();
			$i       = 1;
			foreach ( $add_on_list as $file ) {
				$json = $wp_filesystem->get_contents( $file );
				$arr  = json_decode( $json, true );
				array_push( $new_arr, $arr[ 'info_' . $i ] );
				$i++;
			}
			return $new_arr;
		}
	}
}
$aior_package = new Aior_Package();
$aior_package->do_load_add_on();
