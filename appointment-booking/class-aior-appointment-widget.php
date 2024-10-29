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
if ( ! class_exists( 'Aior_Appointment_Widget' ) ) {

	/**
	 * Extend WP_Widget
	 *
	 * @since    1.0.0
	 */
	class Aior_Appointment_Widget extends WP_Widget {
		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			$options = array(
				'classname'   => 'aior_appointment_widget',
				'description' => esc_html__( 'To show reservation fomr or calendar', 'all-in-one-reservation' ),
			);
			parent::__construct(
				'aior_appointment_widget',
				esc_html__( 'AIOR : Appointment Widget', 'all-in-one-reservation' ),
				$options
			);
		}
		/**
		 * Widget.
		 *
		 * @since    1.0.0
		 * @param array $args Arguments.
		 * @param array $instance Instance.
		 */
		public function widget( $args, $instance ) {
			echo esc_html( $args['before_widget'] );
			echo esc_html( $args['before_title'] . apply_filters( 'widget_title', esc_html__( 'Appointment Calendar', 'all-in-one-reservation' ) ) . $args['after_title'] );
			if ( $instance['title'] ) {
				echo '<h3 class="aior_appointment_title">' . esc_html( $instance['title'] ) . '</h3>';
			}
			if ( $instance['appointment_id'] ) {
				$scode = $instance['appointment_id'];
				echo do_shortcode( '[reservation_booking id="' . $scode . '"]' );

			}
			echo esc_html( $args['after_widget'] );

		}
		/**
		 * Widget Form.
		 *
		 * @since    1.0.0
		 * @param array $instance Instance.
		 */
		public function form( $instance ) {
			$instance       = wp_parse_args(
				(array) $instance,
				array(
					'title'          => '',
					'appointment_id' => '',
				)
			);
			$title          = $instance['title'];
			$appointment_id = $instance['appointment_id'];
			$args           = array(
				'numberposts' => -1,
				'post_type'   => 'sol_reservation_form',
			);
			$posts          = get_posts( $args );
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'all-in-one-reservation' ); ?>: 
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
					name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"  value="<?php echo esc_attr( $title ); ?>">
				</label>
			</p>
			<?php
			if ( $posts ) {
				?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Appointment', 'all-in-one-reservation' ); ?>:
						<select class='widefat' id="<?php echo esc_attr( $this->get_field_id( 'appointment_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'appointment_id' ) ); ?>" type="text">
							<?php
							foreach ( $posts as $post ) {
								$id    = $post->ID;
								$title = $post->post_title;
								echo '<option value="' . esc_attr( $id ) . '" ';
								echo ( $appointment_id == $id ) ? 'selected' : '';
								echo '>' . esc_html( $title ) . '</option>';
							}
							?>
						</select>
					</label>
				</p>
				<?php
			} else {
				esc_html_e( 'Add appointment form then add widget.', 'all-in-one-reservation' );
			}
		}
	}
}
add_action( 'widgets_init', 'aior_appointment_register_custom_widget' );

if ( ! function_exists( 'aior_appointment_register_custom_widget' ) ) {
	/**
	 * Register Custom Widget.
	 *
	 * @since    1.0.0
	 */
	function aior_appointment_register_custom_widget() {
		register_widget( 'Aior_Appointment_Widget' );
	}
}
