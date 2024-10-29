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
	'id'     => '103',
	'name'   => esc_html__( 'Security', 'all-in-one-reservation' ),
	'title'  => esc_html__( 'Security Settings', 'all-in-one-reservation' ),
	'desc'   => esc_html__( 'reCAPTCHA is a free, accessible CAPTCHA service that helps to block spam on your site.', 'all-in-one-reservation' ),
	'filter' => 'aior_add_security_admin_settings',
);
self::create_tab( $nav_args );
add_filter(
	'aior_add_security_admin_settings',
	function () {
		$rf_data      = Aior_Admin::get_settings();
		$aior_builder = new Aior_Builder();
		?>
	<div class="form-group solrow2">
		<div class="solcol2"><label class="control-label"><?php esc_html_e( 'Site Key (Public Key)', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol2">
			<?php
			$aior_recaptcha_public_key = isset( $rf_data['aior_recaptcha_public_key'] ) && ! empty( $rf_data['aior_recaptcha_public_key'] ) ? $rf_data['aior_recaptcha_public_key'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'id'    => 'aior_recaptcha_public_key',
					'name'  => 'aior_recaptcha_public_key',
					'value' => $aior_recaptcha_public_key,
				)
			);
			?>
			<small><?php esc_html_e( 'This key is required. You can register them at', 'all-in-one-reservation' ); ?> <a href="http://www.google.com/recaptcha/admin/create" target="_blank"><?php esc_html_e( 'Here.', 'all-in-one-reservation' ); ?></a> </small>
		</div>
	</div>
	<div class="form-group solrow2">
		<div class="solcol2"><label class="control-label"><?php esc_html_e( 'Site Key (Private  Key)', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol2">
			<?php
			$aior_recaptcha_private_key = isset( $rf_data['aior_recaptcha_private_key'] ) && ! empty( $rf_data['aior_recaptcha_private_key'] ) ? $rf_data['aior_recaptcha_private_key'] : '';
			$aior_builder->add_field(
				array(
					'type'  => 'text',
					'id'    => 'aior_recaptcha_private_key',
					'name'  => 'aior_recaptcha_private_key',
					'value' => $aior_recaptcha_private_key,
				)
			);
			?>
			<small><?php esc_html_e( 'This private key is required. You can register them at', 'all-in-one-reservation' ); ?> <a href="http://www.google.com/recaptcha/admin/create" target="_blank"><?php esc_html_e( 'Here.', 'all-in-one-reservation' ); ?></a> </small>
		</div>
	</div>
	<div class="form-group solrow2">
		<div class="solcol2"><label class="control-label"><?php esc_html_e( 'Theme', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol2">
			<?php
			$aior_recaptcha_theme = isset( $rf_data['aior_recaptcha_theme'] ) && ! empty( $rf_data['aior_recaptcha_theme'] ) ? $rf_data['aior_recaptcha_theme'] : '';
			$aior_builder->add_field(
				array(
					'type'   => 'select',
					'id'     => 'aior_recaptcha_theme',
					'name'   => 'aior_recaptcha_theme',
					'value'  => $aior_recaptcha_theme,
					'option' => array(
						esc_html__( 'Light', 'all-in-one-reservation' ) => 'light',
						esc_html__( 'Dark', 'all-in-one-reservation' )  => 'dark',
					),
				)
			);
			?>
			<small><?php esc_html_e( 'Choose Theme of reCaptcha form.', 'all-in-one-reservation' ); ?></small>
		</div>
	</div>
	<div class="form-group solrow2">
		<div class="solcol2"><label class="control-label"><?php esc_html_e( 'Language', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol2">
			<?php
			$aior_recaptcha_lang = isset( $rf_data['aior_recaptcha_lang'] ) && ! empty( $rf_data['aior_recaptcha_lang'] ) ? $rf_data['aior_recaptcha_lang'] : '';
			$aior_builder->add_field(
				array(
					'type'   => 'select',
					'id'     => 'aior_recaptcha_lang',
					'name'   => 'aior_recaptcha_lang',
					'value'  => $aior_recaptcha_lang,
					'option' => array(
						esc_html__( 'English', 'all-in-one-reservation' )    => 'en',
						esc_html__( 'Arabic', 'all-in-one-reservation' )     => 'ar',
						esc_html__( 'Bulgarian', 'all-in-one-reservation' )  => 'bg',
						esc_html__( 'Catalan Valencian', 'all-in-one-reservation' ) => 'ca',
						esc_html__( 'Czech', 'all-in-one-reservation' )      => 'cs',
						esc_html__( 'Danish', 'all-in-one-reservation' )     => 'da',
						esc_html__( 'German', 'all-in-one-reservation' )     => 'de',
						esc_html__( 'Greek', 'all-in-one-reservation' )      => 'el',
						esc_html__( 'British English', 'all-in-one-reservation' ) => 'en_gb',
						esc_html__( 'Spanish', 'all-in-one-reservation' )    => 'es',
						esc_html__( 'Persian', 'all-in-one-reservation' )    => 'fa',
						esc_html__( 'French', 'all-in-one-reservation' )     => 'fr',
						esc_html__( 'Canadian French', 'all-in-one-reservation' ) => 'fr_ca',
						esc_html__( 'Hindi', 'all-in-one-reservation' )      => 'hi',
						esc_html__( 'Croatian', 'all-in-one-reservation' )   => 'hr',
						esc_html__( 'Hungarian', 'all-in-one-reservation' )  => 'hu',
						esc_html__( 'Indonesian', 'all-in-one-reservation' ) => 'id',
						esc_html__( 'Italian', 'all-in-one-reservation' )    => 'it',
						esc_html__( 'Hebrew', 'all-in-one-reservation' )     => 'iw',
						esc_html__( 'Jananese', 'all-in-one-reservation' )   => 'ja',
						esc_html__( 'Korean', 'all-in-one-reservation' )     => 'ko',
						esc_html__( 'Lithuanian', 'all-in-one-reservation' ) => 'lt',
						esc_html__( 'Latvian', 'all-in-one-reservation' )    => 'lv',
						esc_html__( 'Dutch', 'all-in-one-reservation' )      => 'nl',
						esc_html__( 'Norwegian', 'all-in-one-reservation' )  => 'no',
						esc_html__( 'Polish', 'all-in-one-reservation' )     => 'pl',
						esc_html__( 'Portuguese', 'all-in-one-reservation' ) => 'pt',
						esc_html__( 'Romanian', 'all-in-one-reservation' )   => 'ro',
						esc_html__( 'Russian', 'all-in-one-reservation' )    => 'ru',
						esc_html__( 'Slovak', 'all-in-one-reservation' )     => 'sk',
						esc_html__( 'Slovene', 'all-in-one-reservation' )    => 'sl',
						esc_html__( 'Serbian', 'all-in-one-reservation' )    => 'sr',
						esc_html__( 'Swedish', 'all-in-one-reservation' )    => 'sv',
						esc_html__( 'Thai', 'all-in-one-reservation' )       => 'th',
						esc_html__( 'Turkish', 'all-in-one-reservation' )    => 'tr',
						esc_html__( 'Ukrainian', 'all-in-one-reservation' )  => 'uk',
						esc_html__( 'Vietnamese', 'all-in-one-reservation' ) => 'vi',
						esc_html__( 'Simplified Chinese', 'all-in-one-reservation' ) => 'zh_cn',
						esc_html__( 'Traditional Chinese', 'all-in-one-reservation' ) => 'zh_tw',
					),
				)
			);
			?>
			<small><?php esc_html_e( 'Choose language of reCaptcha.', 'all-in-one-reservation' ); ?></small>
		</div>
	</div>
	<div class="form-group solrow2">
		<div class="solcol2"><label class="control-label"><?php esc_html_e( 'Spam Verification', 'all-in-one-reservation' ); ?></label></div>
		<div class="solcol2">
			<?php
			$aior_recaptcha_spam_verification = isset( $rf_data['aior_recaptcha_spam_verification'] ) && ! empty( $rf_data['aior_recaptcha_spam_verification'] ) ? $rf_data['aior_recaptcha_spam_verification'] : '';
			$aior_builder->add_field(
				array(
					'type'   => 'select',
					'id'     => 'aior_recaptcha_spam_verification',
					'name'   => 'aior_recaptcha_spam_verification',
					'value'  => $aior_recaptcha_spam_verification,
					'option' => array(
						esc_html__( 'Disable', 'all-in-one-reservation' ) => '0',
						esc_html__( 'Enable', 'all-in-one-reservation' )  => '1',

					),
				)
			);
			?>
			<small><?php esc_html_e( 'Enable/Disable spam verification with reservation form on front-end.', 'all-in-one-reservation' ); ?></small>
		</div>
	</div>
		<?php
	},
	10,
	1
);
