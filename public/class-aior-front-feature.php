<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.solwininfotech.com/
 * @since      1.0.0
 *
 * @package    Aior
 * @subpackage Aior/public
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once ABSPATH . 'wp-admin/includes/plugin.php';
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Aior
 * @subpackage Aior/public
 * @author     Solwin Infotech <support@solwininfotech.com>
 */
class Aior_Front_Feature {

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
		add_action( 'aior_comment_form', array( $this, 'soical_share_icons' ), 5, 1 );
		add_action( 'comment_form_logged_in_after', array( $this, 'comment_rating_rating_field' ), 10, 2 );
		add_action( 'comment_form_after_fields', array( $this, 'comment_rating_rating_field' ) );
		add_action( 'comment_post', array( $this, 'comment_rating_save_comment_rating' ) );
		add_filter( 'preprocess_comment', array( $this, 'comment_rating_require_rating' ) );
		add_filter( 'comment_text', array( $this, 'comment_rating_display_rating' ) );
		add_filter( 'the_content', array( $this, 'comment_rating_display_average_rating' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'support_css' ) );
		/** Add WPBakery Page Builder Support. */
		add_action( 'vc_before_init', array( $this, 'aior_add_vc_support' ) );
		/** Add Beaver Page Builder Lite Support. */
		if ( is_plugin_active( 'beaver-builder-lite-version/fl-builder.php' ) ) {
			add_action( 'fl_builder_ui_panel_after_modules', array( $this, 'add_aior_widget' ) );
		}
		/** Add SiteOrigin Page Builder Support. */
		if ( is_plugin_active( 'siteorigin-panels/siteorigin-panels.php' ) ) {
			add_filter( 'siteorigin_panels_widget_dialog_tabs', array( $this, 'siteorigin_panels_add_widgets_dialog_tabs_fun' ), 20 );
			add_filter( 'siteorigin_panels_widgets', array( $this, 'siteorigin_panels_add_recommended_widgets_fun' ) );
		}
		/** Add Fusion Page Builder Support. */
		if ( is_plugin_active( 'fusion-builder/fusion-builder.php' ) ) {
			add_action( 'fusion_builder_before_init', array( $this, 'fusion_element_aior' ) );
		}
		if ( is_plugin_active( 'fusion/fusion-core.php' ) ) {
			add_action( 'init', array( $this, 'fsn_init_aior' ), 12 );
			add_shortcode( 'fsn_aior', array( $this, 'fsn_aior_shortcode' ) );
		}
		if ( is_plugin_active( 'twilio-sms/class-aior-twilio.php' ) ) {
			require_once WP_PLUGIN_DIR . '/twilio-sms/class-aior-twilio-sms.php';
		}
		if ( is_plugin_active( 'direct-bank-transfer/class-aior-dbt.php' ) ) {
			require_once WP_PLUGIN_DIR . '/direct-bank-transfer/class-aior-dbt.php';
		}
		if ( is_plugin_active( 'paypal/class-aior-paypal.php' ) ) {
			require_once WP_PLUGIN_DIR . '/paypal/class-aior-paypal.php';
		}
		if ( is_plugin_active( 'pay-stripe/class-aior-stripe.php' ) ) {
			require_once WP_PLUGIN_DIR . '/pay-stripe/class-aior-stripe.php';
		}
	}

	/**
	 * Support CSS
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function support_css() {
		wp_enqueue_style( 'aior_support_css', plugins_url( 'all-in-one-reservation/admin/css/vc_style.css' ), null, '1.0' );
	}

	/**
	 * Add support to WPBakery Page Builder plugin
	 *
	 * @return void
	 */
	public function aior_add_vc_support() {
		global $wpdb;
		$aior_table_name = $wpdb->prefix . 'posts';
		$post_type       = 'sol_reservation_form';
		$aior_ids        = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title FROM $aior_table_name WHERE post_status='publish' AND post_type='%s' ", $post_type ) ); //phpcs:ignore
		$aior_array      = array( 'Select Reservation Form' );
		if ( ! empty( $aior_ids ) && is_array( $aior_ids ) ) {
			foreach ( $aior_ids as $aior_id ) {
				$aior_array[ $aior_id->post_title ] = $aior_id->ID;
			}
		}
		vc_map(
			array(
				'name'              => esc_html__( 'All In One Reservation', 'all-in-one-reservation' ),
				'base'              => 'reservation_booking',
				'class'             => 'aior_section',
				'category'          => esc_html__( 'Content' ),
				'icon'              => 'aior_icon',
				'admin_enqueue_css' => array( plugins_url() . '/all-in-one-reservation/admin/css/vc_style.css' ),
				'description'       => esc_html__( 'Custom Reservation Forms', 'blog-designer-pro' ),
				'params'            => array(
					array(
						'type'        => 'dropdown',
						'heading'     => esc_html__( 'Select Reservation Form ID', 'blog-designer-pro' ),
						'param_name'  => 'id',
						'value'       => $aior_array,
						'admin_label' => true,
					),
				),
			)
		);
	}

	/**
	 * Beaver Builder Lite
	 *
	 * @return void
	 */
	public function add_aior_widget() {
		?>
		<div id="fl-builder-blocks-bdp-widget" class="fl-builder-blocks-section">
			<span class="fl-builder-blocks-section-title"><?php esc_html_e( 'All in On Reservation', 'all-in-one-reservation' ); ?><i class="fas fa-chevron-down"></i>
			</span>
			<div class="fl-builder-blocks-section-content fl-builder-modules"><span class="fl-builder-block fl-builder-block-module" data-widget="Aior_Appointment_Widget" data-type="widget"><span class="fl-builder-block-title"><?php esc_html_e( 'All in On Reservation', 'all-in-one-reservation' ); ?></span></span></div>
		</div>
		<?php
	}

	/**
	 * Page Builder by SiteOrigin
	 *
	 * @since 1.0.0
	 * @param array $tabs tabs.
	 * @return array $tabs
	 */
	public function siteorigin_panels_add_widgets_dialog_tabs_fun( $tabs ) {
		$tabs['reservation_booking'] = array(
			'title'  => esc_html__( 'All In One Reservation', 'blog-designer-pro' ),
			'filter' => array(
				'groups' => array( 'reservation_booking' ),
			),
		);
		return $tabs;
	}

	/**
	 * Site Origin Panels add recommended widgets
	 *
	 * @since 1.0.0
	 * @param array $widgets widgets.
	 * @return array $widgets
	 */
	public function siteorigin_panels_add_recommended_widgets_fun( $widgets ) {
		foreach ( $widgets as $widget_id => &$widget ) {
			if ( strpos( $widget_id, 'AIOR_Widget_' ) === 0 || strpos( $widget_id, 'aior_appointment_widget' ) !== false ) {
				$widget['groups'][] = 'reservation_booking';
				$widget['icon']     = 'aior_icon';
			}
		}
		return $widgets;
	}

	/**
	 * Fusion Element Builder
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function fusion_element_aior() {
		global $wpdb;
		$aior_table_name = $wpdb->prefix . 'posts';
		$post_type       = 'sol_reservation_form';
		$shortcodes      = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title FROM $aior_table_name WHERE post_status='publish' AND post_type='%s' ", $post_type ) ); //phpcs:ignore
		$aior_layouts    = array();
		if ( $shortcodes ) {
			foreach ( $shortcodes as $shortcode ) {
				$aior_layouts[ $shortcode->post_title ] = $shortcode->ID;
			}
		}
		fusion_builder_map(
			array(
				'name'      => esc_attr__( 'Appoitment Booking', 'all-in-one-reservation' ),
				'shortcode' => 'reservation_booking',
				'icon'      => 'aior_icon',
				'params'    => array(
					array(
						'type'       => 'select',
						'heading'    => esc_attr__( 'Select Reservation Form', 'all-in-one-reservation' ),
						'param_name' => 'id',
						'default'    => '',
						'value'      => $aior_layouts,
					),
				),
			)
		);
	}
	/**
	 * Fusion Page Builder
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function fsn_init_aior() {
		if ( function_exists( 'fsn_map' ) ) {
			global $wpdb;
			$aior_table_name = $wpdb->prefix . 'posts';
			$post_type       = 'sol_reservation_form';
			$shortcodes      = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title FROM $aior_table_name WHERE post_status='publish' AND post_type='%s' ", $post_type ) ); //phpcs:ignore
			$aior_layouts    = array();
			if ( $shortcodes ) {
				foreach ( $shortcodes as $shortcode ) {
					$aior_layouts[ $shortcode->ID ] = $shortcode->post_title;
				}
			}
			fsn_map(
				array(
					'name'          => esc_html__( 'All In One Reservation', 'all-in-one-reservation' ),
					'shortcode_tag' => 'fsn_aior',
					'description'   => esc_html__( 'All in one reservation is a free solution for everyone who is looking for reservation system in their website. All is one resernation plugin provides you with a variety of settings, calendar design, time slots and much more in free.', 'all-in-one-reservation' ),
					'icon'          => 'fsn_aior',
					'params'        => array(
						array(
							'type'       => 'select',
							'param_name' => 'id',
							'label'      => esc_html__( 'Select Reservation Form', 'all-in-one-reservation' ),
							'options'    => $aior_layouts,
						),
					),
				)
			);
		}
	}
	/**
	 * FSN AIOR Shortcode
	 *
	 * @since 1.0.0
	 * @param array  $atts atts.
	 * @param string $content content.
	 * @return html $output
	 */
	public function fsn_aior_shortcode( $atts, $content ) {
		ob_start();
		?>
		<div class="fsn-aior <?php echo esc_attr( fsn_style_params_class( $atts ) ); ?>">
			<?php echo do_shortcode( '[reservation_booking id="' . $atts['id'] . '"]' ); ?>
		</div>
		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Creates Rating Interface.
	 *
	 * @since    1.0.0
	 */
	public function comment_rating_rating_field() {
		wp_nonce_field( 'comment_nonce' );
		echo '<span style="display:none !important">';
		$comment_id = get_comment_ID();
		echo '</span>';
		if ( $comment_id ) {
			$comment = get_comment( $comment_id );
			if ( $comment ) {
				if ( 'sol_reservation_form' === get_post_type( $comment->comment_post_ID ) ) {
					echo '<fieldset class="aior-comments-rating">';
					echo '<span class="aior-rating-container">';
					for ( $i = 5; $i >= 1; $i-- ) {
						echo '<input type="radio" id="rating-' . esc_attr( $i ) . '" name="rating" value="' . esc_attr( $i ) . '">';
						echo '<label for="rating-' . esc_attr( $i ) . '">' . esc_html( $i ) . '</label>';
					}
					echo '<input type="radio" id="rating-0" class="star-cb-clear" name="rating" value="0"><label for="rating-0">0</label>';
					echo '</span>';
					echo '</fieldset>';
				}
			}
		}
	}
	/**
	 * Saves the rating submitted by user.
	 *
	 * @since    1.0.0
	 * @param int $comment_id Comment ID.
	 */
	public function comment_rating_save_comment_rating( $comment_id ) {
		$nonce = ( isset( $_POST['nonce'] ) && ! empty( $_POST['nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ( isset( $_POST['rating'] ) ) && ( '' !== $_POST['rating'] ) && wp_verify_nonce( $nonce, 'on_aior_admin_global_nonce' ) ) {
			$rating = intval( $_POST['rating'] );
		}
		add_comment_meta( $comment_id, 'rating', $rating );
	}
	/**
	 * Make the Rating Required.
	 *
	 * @since    1.0.0
	 * @param string $commentdata Data of the comments.
	 */
	public function comment_rating_require_rating( $commentdata ) {
		$nonce = ( isset( $_POST['nonce'] ) && ! empty( $_POST['nonce'] ) ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( wp_verify_nonce( $nonce, 'on_aior_admin_global_nonce' ) && ! is_admin() && ( ! isset( $_POST['rating'] ) || 0 == intval( $_POST['rating'] ) ) ) {
			/* May use wp_die() to print error if rating not selcted */
			return $commentdata;
		}
	}
	/**
	 * Display the rating after the comment.
	 *
	 * @since    1.0.0
	 * @param string $comment_text Comment Text or Content.
	 */
	public function comment_rating_display_rating( $comment_text ) {
		$rating = get_comment_meta( get_comment_ID(), 'rating', true );
		if ( $rating ) {
			$stars = '<p class="stars">';
			for ( $i = 1; $i <= $rating; $i++ ) {
				$stars .= '<span class="dashicons dashicons-star-filled"></span>';
			}
			$stars       .= '</p>';
			$comment_text = $comment_text . $stars;
			return $comment_text;
		} else {
			return $comment_text;
		}
	}
	/**
	 * Get the Average Rating of post.
	 *
	 * @since    1.0.0
	 * @param int $id Post ID.
	 */
	public static function comment_rating_get_average_ratings( $id ) {
		$comments = get_approved_comments( $id );
		if ( $comments ) {
			$i     = 0;
			$total = 0;
			foreach ( $comments as $comment ) {
				$rate = get_comment_meta( $comment->comment_ID, 'rating', true );
				if ( isset( $rate ) && '' !== $rate ) {
					$i++;
					$total += $rate;
				}
			}
			if ( 0 == $i ) {
				return false;
			} else {
				return round( $total / $i, 1 );
			}
		} else {
			return false;
		}
	}
	/**
	 * Display the average ratings after the comment.
	 *
	 * @since    1.0.0
	 * @param string $content Content of the comment.
	 */
	public function comment_rating_display_average_rating( $content ) {
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : '';
		if ( false === self::comment_rating_get_average_ratings( $post_id ) ) {
			return $content;
		}
		$stars   = '';
		$average = self::comment_rating_get_average_ratings( $post_id );
		for ( $i = 1; $i <= $average + 1; $i++ ) {
			$width = intval( $i - $average > 0 ? 20 - ( ( $i - $average ) * 20 ) : 20 );
			if ( 0 == $width ) {
				continue;
			}
			$stars .= '<span style="overflow:hidden;width:' . $width . 'px" class="dashicons dashicons-star-filled"></span>';
			if ( $i - $average > 0 ) {
				$stars .= '<span style="overflow:hidden; position:relative; left:-' . $width . 'px;" class="dashicons dashicons-star-empty"></span>';
			}
		}
		$custom_content  = '<p class="average-rating">' . esc_html__( "This post's average rating is: ", 'all-in-one-reservation' ) . $average . ' ' . $stars . '</p>';
		$custom_content .= $content;
		return $custom_content;
	}
	/**
	 * Display Social Icons.
	 *
	 * @since    1.0.0
	 * @param int $fid Form ID.
	 */
	public static function soical_share_icons( $fid ) {
		$rf_data                = Aior_Reservation_Form::get_settings( $fid );
		$enable_social_share    = isset( $rf_data['enable_social_share'] ) ? $rf_data['enable_social_share'] : '0';
		$show_social_share_icon = isset( $rf_data['show_social_share_icon'] ) ? $rf_data['show_social_share_icon'] : '';
		if ( $enable_social_share ) {
			if ( ! empty( $show_social_share_icon ) ) {
				$show_ss_icon = maybe_unserialize( $show_social_share_icon );
				if ( ! empty( $show_ss_icon ) ) {

					?>
					<ul class="social-buttons">
						<?php
						foreach ( $show_ss_icon as  $key => $value ) {
							if ( 1 == $value ) {
								?>
								<li class="button__share button__share--facebook"><a href="javascript:void(window.open('https://www.facebook.com/sharer.php?u=' + encodeURIComponent(document.location) + '?t=' + encodeURIComponent(document.title),'_blank'))"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-facebook" viewBox="0 0 24 24"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/></svg><?php esc_html_e( 'Facebook', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 2 == $value ) {
								?>
								<li class="button__share button__share--twitter"><a href="javascript:void(window.open('https://twitter.com/share?url=' + encodeURIComponent(document.location) + '&amp;text=' + encodeURIComponent(document.title) + '&amp;via=fabienb&amp;hashtags=koandesign','_blank'))"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-twitter" viewBox="0 0 24 24"><path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/></svg><?php esc_html_e( 'Twitter', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 3 == $value ) {
								?>
								<li class="button__share button__share--googleplus"><a href="javascript:void(window.open('https://plus.google.com/share?url=' + encodeURIComponent(document.location),'_blank'))"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-google" viewBox="0 0 24 24"><path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z"/></svg><?php esc_html_e( 'Google+ Share', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 4 == $value ) {
								?>
								<!-- optional Twitter username of content author (don’t include “@”) optional Hashtags appended onto the tweet (comma separated. don’t include “#”) -->
								<li class="button__share button__share--linkedin"><a href="javascript:void(window.open('https://www.linkedin.com/shareArticle?url=' + encodeURIComponent(document.location) + '&amp;title=' + encodeURIComponent(document.title),'_blank'))"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-linkedin" viewBox="0 0 24 24"><path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/></svg><?php esc_html_e( 'Linkedin', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 5 == $value ) {
								?>
								<!-- can add &mini=true -->
								<li class="button__share button__share--reddit"><a href="javascript:void(window.open('http://reddit.com/submit?url=' + encodeURIComponent(document.location) + '&amp;title=' + encodeURIComponent(document.title),'_blank'))"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-reddit" viewBox="0 0 24 24"><path d="M6.167 8a.831.831 0 0 0-.83.83c0 .459.372.84.83.831a.831.831 0 0 0 0-1.661zm1.843 3.647c.315 0 1.403-.038 1.976-.611a.232.232 0 0 0 0-.306.213.213 0 0 0-.306 0c-.353.363-1.126.487-1.67.487-.545 0-1.308-.124-1.671-.487a.213.213 0 0 0-.306 0 .213.213 0 0 0 0 .306c.564.563 1.652.61 1.977.61zm.992-2.807c0 .458.373.83.831.83.458 0 .83-.381.83-.83a.831.831 0 0 0-1.66 0z"/><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.828-1.165c-.315 0-.602.124-.812.325-.801-.573-1.9-.945-3.121-.993l.534-2.501 1.738.372a.83.83 0 1 0 .83-.869.83.83 0 0 0-.744.468l-1.938-.41a.203.203 0 0 0-.153.028.186.186 0 0 0-.086.134l-.592 2.788c-1.24.038-2.358.41-3.17.992-.21-.2-.496-.324-.81-.324a1.163 1.163 0 0 0-.478 2.224c-.02.115-.029.23-.029.353 0 1.795 2.091 3.256 4.669 3.256 2.577 0 4.668-1.451 4.668-3.256 0-.114-.01-.238-.029-.353.401-.181.688-.592.688-1.069 0-.65-.525-1.165-1.165-1.165z"/></svg><?php esc_html_e( 'Reddit', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 6 == $value ) {
								?>
								<li class="button__share button__share--digg"><a href="javascript:void(window.open('https://digg.com/submit?url=' + encodeURIComponent(document.location) + '&amp;title=' + encodeURIComponent(document.title),'_blank'))"><svg role="img" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.76 8.16v8.16h3.84v.96h-3.84v1.92H24V8.16h-6.24zm-7.2 0v8.16h3.84v.96h-3.84v1.92h6.24V8.16h-6.24zM3.84 4.8v3.36H0v8.16h6.24V4.8h-2.4zM9.6 8.16H7.2v8.16h2.4V8.16zm12 6.24h-1.44v-4.32h1.44v4.32zm-17.76 0H2.4v-4.32h1.44v4.32zm10.56 0h-1.44v-4.32h1.44v4.32zM9.6 4.8H7.2v2.4h2.4V4.8z"/></svg><?php esc_html_e( 'Digg', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 7 == $value ) {
								?>
								<li class="button__share button__share--tumblr"><a href="javascript:void(window.open('http://www.tumblr.com/share/link?url=' + encodeURIComponent(document.location) + '&amp;name=' + encodeURIComponent(document.title),'_blank'))"> <svg role="img" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M14.563 24c-5.093 0-7.031-3.756-7.031-6.411V9.747H5.116V6.648c3.63-1.313 4.512-4.596 4.71-6.469C9.84.051 9.941 0 9.999 0h3.517v6.114h4.801v3.633h-4.82v7.47c.016 1.001.375 2.371 2.207 2.371h.09c.631-.02 1.486-.205 1.936-.419l1.156 3.425c-.436.636-2.4 1.374-4.156 1.404h-.178l.011.002z"/></svg><?php esc_html_e( 'Tumblr', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 8 == $value ) {
								?>
								<!-- can add &description= -->
								<li class="button__share button__share--stumbleupon"><a href="javascript:void(window.open('http://www.stumbleupon.com/submit?url=' + encodeURIComponent(document.location) + '&amp;title=' + encodeURIComponent(document.title),'_blank'))"><svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M0 10.2A1.8 1.8 0 001.8 12h1.8a8.4 8.4 0 018.4 8.4v1.8a1.8 1.8 0 001.8 1.8h8.4a1.8 1.8 0 001.8-1.8v-1.8C24 9.133 14.867 0 3.6 0H1.8A1.8 1.8 0 000 1.8v8.4z"/></svg><?php esc_html_e( 'StumbleUpon', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 9 == $value ) {
								?>
								<li class="button__share button__share--delicious"><a href="javascript:void(window.open('https://www.deliciousmagazine.co.uk/save?v=5&amp;noui&amp;jump=close&amp;url=' + encodeURIComponent(document.location) + '&amp;title=' + encodeURIComponent(document.title),'_blank'))"><svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12H0v12h12V12zM24 0H12v12h12V0z"/></svg><?php esc_html_e( 'Delicious', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 10 == $value ) {
								?>
								<!-- can add &provider= // [provider] is the Company who is sharing the url -->
								<li class="button__share button__share--evernote"><a href="javascript:void(window.open('http://www.evernote.com/clip.action?url=' + encodeURIComponent(document.location) + '&amp;title=' + encodeURIComponent(document.title),'_blank'))"><svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M8.222 5.393c0 .239-.02.637-.256.895-.257.24-.652.259-.888.259H4.552c-.73 0-1.165 0-1.46.04-.159.02-.356.1-.455.14-.04.019-.04 0-.02-.02L8.38.796c.02-.02.04-.02.02.02-.04.099-.118.298-.138.457-.04.298-.04.736-.04 1.472v2.647zm5.348 17.869c-.67-.438-1.026-1.015-1.164-1.373a2.924 2.924 0 01-.217-1.095 3.007 3.007 0 013-3.004c.493 0 .888.398.888.895a.88.88 0 01-.454.776c-.099.06-.237.1-.336.12-.098.02-.473.06-.65.218-.198.16-.356.418-.356.697 0 .298.118.577.316.776.355.358.829.557 1.342.557a2.436 2.436 0 002.427-2.447c0-1.214-.809-2.29-1.875-2.766-.158-.08-.414-.14-.651-.2a8.04 8.04 0 00-.592-.1c-.829-.1-2.901-.755-3.04-2.605 0 0-.611 2.785-1.835 3.54-.118.06-.276.12-.454.16-.177.04-.374.06-.434.06-1.993.12-4.105-.517-5.565-2.03 0 0-.987-.815-1.5-3.103-.118-.558-.355-1.553-.493-2.488-.06-.338-.08-.597-.099-.836 0-.975.592-1.631 1.342-1.73h4.026c.69 0 1.086-.18 1.342-.42.336-.317.415-.775.415-1.312V1.354C9.05.617 9.703 0 10.669 0h.474c.197 0 .434.02.651.04.158.02.296.06.533.12 1.204.298 1.46 1.532 1.46 1.532s2.27.398 3.415.597c1.085.199 3.77.378 4.282 3.104 1.204 6.487.474 12.775.415 12.775-.849 6.129-5.901 5.83-5.901 5.83a4.1 4.1 0 01-2.428-.736zm4.54-13.034c-.652-.06-1.204.2-1.402.697-.04.1-.079.219-.059.278.02.06.06.08.099.1.237.12.631.179 1.204.239.572.06.967.1 1.223.06.04 0 .08-.02.119-.08.04-.06.02-.18.02-.28-.06-.536-.553-.934-1.204-1.014z"/></svg><?php esc_html_e( 'Evernote', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 11 == $value ) {
								?>
								<li class="button__share button__share--wordpress"><a href="javascript:void(window.open('http://wordpress.com/press-this.php?u=' + encodeURIComponent(document.location) + '&amp;t=' + encodeURIComponent(document.title),'_blank'))"><svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21.469 6.825c.84 1.537 1.318 3.3 1.318 5.175 0 3.979-2.156 7.456-5.363 9.325l3.295-9.527c.615-1.54.82-2.771.82-3.864 0-.405-.026-.78-.07-1.11m-7.981.105c.647-.03 1.232-.105 1.232-.105.582-.075.514-.93-.067-.899 0 0-1.755.135-2.88.135-1.064 0-2.85-.15-2.85-.15-.585-.03-.661.855-.075.885 0 0 .54.061 1.125.09l1.68 4.605-2.37 7.08L5.354 6.9c.649-.03 1.234-.1 1.234-.1.585-.075.516-.93-.065-.896 0 0-1.746.138-2.874.138-.2 0-.438-.008-.69-.015C4.911 3.15 8.235 1.215 12 1.215c2.809 0 5.365 1.072 7.286 2.833-.046-.003-.091-.009-.141-.009-1.06 0-1.812.923-1.812 1.914 0 .89.513 1.643 1.06 2.531.411.72.89 1.643.89 2.977 0 .915-.354 1.994-.821 3.479l-1.075 3.585-3.9-11.61.001.014zM12 22.784c-1.059 0-2.081-.153-3.048-.437l3.237-9.406 3.315 9.087c.024.053.05.101.078.149-1.12.393-2.325.609-3.582.609M1.211 12c0-1.564.336-3.05.935-4.39L7.29 21.709C3.694 19.96 1.212 16.271 1.211 12M12 0C5.385 0 0 5.385 0 12s5.385 12 12 12 12-5.385 12-12S18.615 0 12 0"/></svg><?php esc_html_e( 'WordPress', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 12 == $value ) {
								?>
								<!-- can add &s=[post-desc]&i=[post-img] -->
								<li class="button__share button__share--pocket"><a href="javascript:void(window.open('https://getpocket.com/save?url=' + encodeURIComponent(document.location) + '&amp;title=' + encodeURIComponent(document.title),'_blank'))"><svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.813 10.259l-5.646 5.419c-.32.305-.73.458-1.141.458-.41 0-.821-.153-1.141-.458l-5.646-5.419c-.657-.628-.677-1.671-.049-2.326.63-.657 1.671-.679 2.325-.05l4.511 4.322 4.517-4.322c.66-.631 1.697-.607 2.326.049.631.645.615 1.695-.045 2.326l-.011.001zm5.083-7.546c-.299-.858-1.125-1.436-2.041-1.436H2.179c-.9 0-1.717.564-2.037 1.405-.094.25-.142.511-.142.774v7.245l.084 1.441c.348 3.277 2.047 6.142 4.682 8.139.045.036.094.07.143.105l.03.023c1.411 1.03 2.989 1.728 4.694 2.072.786.158 1.591.24 2.389.24.739 0 1.481-.067 2.209-.204.088-.029.176-.045.264-.06.023 0 .049-.015.074-.029 1.633-.36 3.148-1.036 4.508-2.025l.029-.031.135-.105c2.627-1.995 4.324-4.862 4.686-8.148L24 10.678V3.445c0-.251-.031-.5-.121-.742l.017.01z"/></svg><?php esc_html_e( 'Pocket', 'all-in-one-reservation' ); ?></a></li>
								<?php
							}
							if ( 13 == $value ) {
								?>
								<li class="button__share button__share--pinterest"><a href="javascript:void(window.open('https://pinterest.com/pin/create/bookmarklet/?url=' + encodeURIComponent(document.location) + '&amp;description=' + encodeURIComponent(document.title),'_blank'))"><svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/></svg><?php esc_html_e( 'Pinterest', 'all-in-one-reservation' ); ?></a></li>
								<!-- can add &media=[post-img] &is_video=[is_video] If the content is a video or not -->
								<?php
							}
						}
						?>
					</ul>
					<?php
				}
			}
		}
	}
}
$aior_front_feature = new Aior_Front_Feature();
