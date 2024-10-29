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

if ( ! defined( 'WPINC' ) ) {
	die;
}
/* Main Appointmetn booking class */
require_once 'class-aior-appointment-booking.php';
/* Appointment Booked listing */
require_once 'class-aior-appointment-list.php';
/* Block support */
require_once 'class-aior-appointment-block.php';
