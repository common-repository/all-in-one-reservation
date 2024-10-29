<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.solwininfotech.com/
 * @since             1.0.0
 * @package           Aior
 *
 * @wordpress-plugin
 * Plugin Name:       All in One Reservation
 * Plugin URI:        https://www.solwininfotech.com/product/wordpress-plugins/all-in-one-reservation/
 * Description:       A powerful reservation and bookings plugin to book your restaurant/cafeteria table, seat bookings and appointment bookings. It is an all in one reservation solution plugin with the features of weekly hours management, holiday settings and much more.
 * Version:           1.0.2
 * Author:            Solwin Infotech
 * Author URI:        https://www.solwininfotech.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aior
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if ( ! defined( 'AIOR_PLUGIN_PATH' ) ) {
	define( 'AIOR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'AIOR_PLUGIN_URL' ) ) {
	define( 'AIOR_PLUGIN_URL', plugins_url() . '/' . basename( plugin_dir_path( __FILE__ ) ) );
}
if ( ! defined( 'AIOR_PLUGIN_BASE' ) ) {
	define( 'AIOR_PLUGIN_BASE', plugin_basename( __FILE__ ) );
}
/**
 * Allow JSON file to upload.
 *
 * @param array $types contains all uploadable file types.
 */
function aior_add_upload_mimes( $types ) {
	$types['json'] = 'application/json';
	return $types;
}
add_filter( 'upload_mimes', 'aior_add_upload_mimes' );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AIOR_VERSION', '1.0.2' );
$secure_hash = md5( 'aior_ical_feed_' . get_site_url() );
define( 'AIORICAL_SECURE_HASH', $secure_hash );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aior-activator.php
 */
function activate_aior() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aior-activator.php';
	Aior_Activator::activate();
	Aior_Admin::run();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aior-deactivator.php
 */
function deactivate_aior() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aior-deactivator.php';
	Aior_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_aior' );
register_deactivation_hook( __FILE__, 'deactivate_aior' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'admin/class-aior-core.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-aior.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-aior-reservation-form.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-aior-reservation-list.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-aior-builder.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-aior-notification.php';
require plugin_dir_path( __FILE__ ) . 'public/class-aior-front-feature.php';
require plugin_dir_path( __FILE__ ) . 'appointment-booking/class-aior-appointment-block.php';
require plugin_dir_path( __FILE__ ) . 'appointment-booking/class-aior-appointment-booking.php';
require plugin_dir_path( __FILE__ ) . 'appointment-booking/class-aior-appointment-list.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-aior-add-on-list.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-aior-package.php';
require_once plugin_dir_path( __FILE__ ) . 'appointment-booking/class-aior-appointment-widget.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_aior() {
	$plugin = new Aior();
	$plugin->run();
}
run_aior();
