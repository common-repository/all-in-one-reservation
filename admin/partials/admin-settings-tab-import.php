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
	'id'     => 'aior-import-tab',
	'name'   => esc_html__( 'Import', 'all-in-one-reservation' ),
	'title'  => esc_html__( 'Import Reservation Form', 'all-in-one-reservation' ),
	'desc'   => esc_html__( 'Upload JSON file and import all form.', 'all-in-one-reservation' ),
	'filter' => 'aior_add_import_admin_settings',
);

self::create_tab( $nav_args );
add_filter(
	'aior_add_import_admin_settings',
	function () {
		$rf_data      = Aior_Admin::get_settings();
		$aior_builder = new Aior_Builder();
		wp_enqueue_media();
		?>
	<div class="form-group solrow2">
		<div class="solcol2"><label><?php esc_html_e( 'Upload/Select a file to import forms', 'all-in-one-reservation' ); ?></label> </div>
		<div class="solcol2">
		<?php wp_nonce_field( 'on_aior_admin_global_nonce', 'aior_admin_global_nonce' ); ?>
		<input type='button' class="button-primary" value="<?php esc_attr_e( 'Select a File', 'all-in-one-reservation' ); ?>" id="aior_select_form_for_import"/>
		</div>
	</div>
		<?php
	},
	10,
	1
);
