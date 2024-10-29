<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.solwininfotech.com/
 * @since      1.0.0
 *
 * @package    Aior
 * @subpackage Aior/admin/partials
 */

$nav_args = array(
	'id'     => '4',
	'name'   => esc_html__( 'Form', 'all-in-one-reservation' ),
	'title'  => esc_html__( 'Reservation Form Settings', 'all-in-one-reservation' ),
	'desc'   => esc_html__( 'All form fields and Add more fields', 'all-in-one-reservation' ),
	'filter' => 'aior_add_in_security_settings',
);
self::create_rs_tab( $nav_args );
add_filter(
	'aior_add_in_security_settings',
	function() {
		$aior_builder = new Aior_Builder();
		$rf_data      = Aior_Reservation_Form::get_settings();
		$form_json    = isset( $rf_data['form_json'] ) ? $rf_data['form_json'] : '';
		if ( $form_json && '{}' !== $form_json ) {
			$form_json_field = $form_json;
		} else {
			$form_json_field = '{"0":{"0":{"0":{"t":"number","n":"rf_slot","l":"Number of Person","p":"Enter Number of Person","r":"1","m":"You must select number of guest","x":"1","y":"10"}}},"1":{"0":{"0":{"t":"text","n":"rf_first_name","l":"First Name","p":"First Name","r":"1","m":"Please add your fist name"}},"1":{"0":{"t":"text","n":"rf_last_name","l":"Last Name","p":"Last Name","r":"1","m":"Please add your last name"}}},"2":{"0":{"0":{"t":"email","n":"rf_email","l":"Email","p":"Email","r":"1","m":"You must add your email id"}},"1":{"0":{"t":"text","n":"rf_phone_no","l":"Phone No","p":"Phone No.","r":"1","m":"Add your phone number.","x":"","y":""}}},"3":{"0":{"0":{"t":"textarea","n":"rf_note","l":"Notes","p":"Enter your reservation note","r":"","m":""}}}}';
			$form_json_field = apply_filters( 'aior_default_form', $form_json_field );
		}
		$aior_builder->add_field(
			array(
				'type'  => 'textarea',
				'name'  => '',
				'id'    => 'rf_droped',
				'class' => 'hidden',
				'value' => $form_json_field,
			)
		);
		?>
	<div id="rfBuilder">
		<div class="solrow2 form_builder">
				<div class="rcfcol1 solcol2">
					<label class="fbuilder_label"><strong><?php esc_html_e( 'Field', 'all-in-one-reservation' ); ?></strong> </label>
					<ul class="fb_field_bar">
						<li class="sol_fb_row"><a href="javascript:void(0)"><?php esc_html_e( 'Row', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-plus-alt"></span></a></li>
						<li class="sol_fb_text"><a href="javascript:void(0)"><?php esc_html_e( 'Text Field', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-plus-alt"></span></a></li>
						<li class="sol_fb_textarea"><a href="javascript:void(0)"><?php esc_html_e( 'Text Area', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-plus-alt"></span></a></li>
						<li class="sol_fb_select"><a href="javascript:void(0)"><?php esc_html_e( 'Select', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-plus-alt"></span></a></li>
						<li class="sol_fb_radio"><a href="javascript:void(0)"><?php esc_html_e( 'Radio Button', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-plus-alt"></span></a></li>
						<li class="sol_fb_checkbox"><a href="javascript:void(0)"><?php esc_html_e( 'Checkbox', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-plus-alt"></span></a></li>
						<li class="sol_fb_email"><a href="javascript:void(0)"><?php esc_html_e( 'Email', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-plus-alt"></span></a></li>
						<li class="sol_fb_number"><a href="javascript:void(0)"><?php esc_html_e( 'Number', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-plus-alt"></span></a></li>
						<li class="sol_fb_password"><a href="javascript:void(0)"><?php esc_html_e( 'Password', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-plus-alt"></span></a></li>
						<li class="sol_fb_date"><a href="javascript:void(0)"><?php esc_html_e( 'Date', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-calendar"></span></a></li>
						<li class="sol_fb_calendar"><a href="javascript:void(0)"><?php esc_html_e( 'Calendar', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-calendar-alt"></span></a></li>
						<li class="sol_fb_time"><a href="javascript:void(0)"><?php esc_html_e( 'Time', 'all-in-one-reservation' ); ?> <span class="dashicons dashicons-clock"></span></a></li>
						<li class="sol_fb_button" style="display: none;"><a href="javascript:void(0)"><?php esc_html_e( 'Button', 'all-in-one-reservation' ); ?><span class="dashicons dashicons-plus-alt"></span></a></li>
						<li style="display: none;"><center><a style="cursor:pointer;margin:0 10px" class="rfb_export_json">Export JSON</a></center></li>
					</ul>
				</div>
				<div class="rcfcol2 solcol2">
					<label class="fbuilder_label"><strong><?php esc_html_e( 'Properties (Form Builder)', 'all-in-one-reservation' ); ?></strong> </label>
					<div class="sol_fbuilder_area"></div>
				</div>
		</div>
		<div class="form-horizontal">
			<label class="fbuilder_label"><strong><?php esc_html_e( 'Form Preview', 'all-in-one-reservation' ); ?></strong> </label>
			<div class="fbpreview"></div>
			<?php
			$aior_builder->add_field(
				array(
					'type'  => 'textarea',
					'name'  => 'form_json',
					'id'    => 'rf_form_json',
					'class' => 'hidden',
				)
			);
			?>
			<div style="display:none" class="form-group plain_html"></div>
		</div>
	</div>
		<?php
	},
	10,
	1
);
