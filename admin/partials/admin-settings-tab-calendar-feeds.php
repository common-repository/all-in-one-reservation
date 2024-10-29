<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://www.solwininfotech.com/
 * @since      1.0.0
 *
 * @package    Aior
 * @subpackage Aior/admin
 */

$nav_args = array(
	'id'     => '105',
	'name'   => esc_html__( 'Calendar Feeds', 'all-in-one-reservation' ),
	'title'  => esc_html__( 'Calendar Feeds', 'all-in-one-reservation' ),
	'desc'   => esc_html__( 'Use the following URLs to either download a static feed (not auto-updating) or paste the URL into your favorite calendar app (Google Calendar, Apple Calendar, etc.) as a subscription to load a read-only auto-updating appointment feed.', 'all-in-one-reservation' ),
	'filter' => 'aior_add_calendar_feeds_admin_settings',
);

self::create_tab( $nav_args );
add_filter(
	'aior_add_calendar_feeds_admin_settings',
	function () {
		$rf_data      = Aior_Admin::get_settings();
		$aior_builder = new Aior_Builder();
		?>
	<div class="form-group solrow2">
		<div class="solcol2"><label><?php esc_html_e( 'All Appointments', 'all-in-one-reservation' ); ?></label> </div>
		<div class="solcol2">
		<?php wp_nonce_field( 'on_aior_admin_global_nonce', 'aior_admin_global_nonce' ); ?>
		<input type='text' readonly="readonly" value="<?php echo esc_url( get_site_url() ); ?>/?all_in_one_ical&sh=<?php echo esc_attr( AIORICAL_SECURE_HASH ); ?>" id="aior_calendar_feeds" style=" width:60%; "/>
		</div>
	</div>
		<?php
	},
	10,
	1
);
