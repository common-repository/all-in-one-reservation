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
class Aior_Revenue {
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

	}
	/**
	 * Retrieve Today's Sales Amount.
	 *
	 * @since    1.0.0
	 */
	public static function aior_get_today_sale_amount() {
		$sales = self::aior_get_today_sale();
		if ( ! empty( $sales ) ) {
			$sale_arr = array();
			foreach ( $sales as $sale ) {
				$price             = get_post_meta( $sale, 'price', true );
				$slot              = get_post_meta( $sale, 'rf_slot', true );
				$fprice            = (float) $price * (int) $slot;
				$sale_arr[ $sale ] = $fprice;
			}
			if ( ! empty( $sale_arr ) ) {
				return array_sum( $sale_arr );
			}
		}
	}
	/**
	 * Retrieve Yesterday's Sale Amount.
	 *
	 * @since    1.0.0
	 */
	public static function get_yesterday_sale_amount() {
		$sales = self::get_yesterday_sale();
		if ( ! empty( $sales ) ) {
			$sale_arr = array();
			foreach ( $sales as $sale ) {
				$price             = get_post_meta( $sale, 'price', true );
				$slot              = get_post_meta( $sale, 'rf_slot', true );
				$fprice            = (float) $price * (int) $slot;
				$sale_arr[ $sale ] = $fprice;
			}
			if ( ! empty( $sale_arr ) ) {
				return array_sum( $sale_arr );
			}
		}
	}
	/**
	 * Retrieve Last Month Sale Amount.
	 *
	 * @since    1.0.0
	 */
	public static function get_last_month_sale_amount() {
		$sales = self::get_last_month_sale();
		if ( ! empty( $sales ) ) {
			$sale_arr = array();
			foreach ( $sales as $sale ) {
				$price             = get_post_meta( $sale, 'price', true );
				$slot              = get_post_meta( $sale, 'rf_slot', true );
				$fprice            = (float) $price * (int) $slot;
				$sale_arr[ $sale ] = $fprice;
			}
			if ( ! empty( $sale_arr ) ) {
				return array_sum( $sale_arr );
			}
		}

	}
	/**
	 * Retrieve Current Month Sale Amount.
	 *
	 * @since    1.0.0
	 */
	public static function get_this_month_sale_amount() {
		$sales = self::get_this_month_sale();
		if ( ! empty( $sales ) ) {
			$sale_arr = array();
			foreach ( $sales as $sale ) {
				$price             = get_post_meta( $sale, 'price', true );
				$slot              = get_post_meta( $sale, 'rf_slot', true );
				$fprice            = (float) $price * (int) $slot;
				$sale_arr[ $sale ] = $fprice;
			}
			if ( ! empty( $sale_arr ) ) {
				return array_sum( $sale_arr );
			}
		}

	}
	/**
	 * Retrieve Today's Sales.
	 *
	 * @since    1.0.0
	 */
	public static function aior_get_today_sale() {
		$today       = getdate();
		$args        = array(
			'numberposts' => -1,
			'post_type'   => 'sol_appointment_list',
			'date_query'  => array(
				array(
					'year'  => $today['year'],
					'month' => $today['mon'],
					'day'   => $today['mday'],
				),
			),
		);
		$a_posts     = get_posts( $args );
		$post_id_arr = array();
		foreach ( $a_posts as $apost ) {
			$pid    = $apost->ID;
			$status = get_post_meta( $pid, 'status', true );
			if ( 'approved' === $status ) {
				array_push( $post_id_arr, $pid );
			}
		}
		if ( ! empty( $post_id_arr ) ) {
			return $post_id_arr;
		}
	}
	/**
	 * Retrieve Yesterday's Sales.
	 *
	 * @since    1.0.0
	 */
	public static function get_yesterday_sale() {
		$today       = getdate();
		$back        = 86400 * 1;
		$yday        = getdate( $today[0] - $back );
		$args        = array(
			'numberposts' => -1,
			'post_type'   => 'sol_appointment_list',
			'date_query'  => array(
				array(
					'year'  => $yday['year'],
					'month' => $yday['mon'],
					'day'   => $yday['mday'],
				),
			),
		);
		$a_posts     = get_posts( $args );
		$post_id_arr = array();
		foreach ( $a_posts as $apost ) {
			$pid    = $apost->ID;
			$status = get_post_meta( $pid, 'status', true );
			if ( 'approved' === $status ) {
				array_push( $post_id_arr, $pid );
			}
		}
		if ( ! empty( $post_id_arr ) ) {
			return $post_id_arr;
		}
	}
	/**
	 * Retrieve Percentage Change of Appointments.
	 *
	 * @since    1.0.0
	 * @param int $old Old Amount.
	 * @param int $new New Amount.
	 */
	public static function get_percentage_change( $old, $new ) {
		if ( $new > 0 ) {
			$sale_updown = ( 1 - $old / $new ) * 100;
		} else {
			$sale_updown = 0;
		}
		if ( $sale_updown > 0 ) {
			echo esc_html( $sale_updown . '% ' );
			self::get_percentage_symbol( $old, $new );
		} elseif ( $sale_updown < 0 ) {
			echo esc_html( $sale_updown . '% ' );
			self::get_percentage_symbol( $old, $new );
		} else {
			echo esc_html( '0% ' );
			self::get_percentage_symbol( $old, $new );
		}
	}
	/**
	 * Retrieve Last 30-days Sale Amount.
	 *
	 * @since    1.0.0
	 */
	public static function last_thirty_day_sale() {
		$args        = array(
			'numberposts' => -1,
			'post_type'   => 'sol_appointment_list',
			'date_query'  => array(
				array(
					'after' => '-1 month',
				),
			),
		);
		$a_posts     = get_posts( $args );
		$post_id_arr = array();
		if ( $a_posts ) {
			foreach ( $a_posts as $apost ) {
				$pid    = $apost->ID;
				$status = get_post_meta( $pid, 'status', true );
				if ( 'approved' === $status ) {
					array_push( $post_id_arr, $pid );
				}
			}
			if ( $post_id_arr ) {
				return $post_id_arr;
			}
		}
	}
	/**
	 * Retrieve Last Month Sales.
	 *
	 * @since    1.0.0
	 */
	public static function get_last_month_sale() {
		$post_id_arr = array();
		$month       = (int) current_time( 'm' );
		$year        = (int) current_time( 'Y' );
		--$month;
		$month = gmdate( 'F', mktime( 0, 0, 0, $month, 10 ) );

		$args      = array(
			'posts_per_page' => -1,
			'post_type'      => 'sol_appointment_list',
		);
		$the_query = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$pid           = get_the_ID();
				$status        = get_post_meta( $pid, 'status', true );
				$current_month = get_the_date( 'F' );
				$current_year  = get_the_date( 'Y' );

				if ( $current_year == $year ) {
					if ( $current_month == $month ) {
						if ( 'approved' === $status ) {
							array_push( $post_id_arr, $pid );

						}
					}
				}
			}
		}
		wp_reset_postdata();
		return $post_id_arr;
	}
	/**
	 * Retrieve This Month Sales.
	 *
	 * @since    1.0.0
	 */
	public static function get_this_month_sale() {
		$post_id_arr = array();
		$month       = (int) current_time( 'm' );
		$year        = (int) current_time( 'Y' );
		$month       = gmdate( 'F', mktime( 0, 0, 0, $month, 10 ) );
		$args        = array(
			'posts_per_page' => -1,
			'post_type'      => 'sol_appointment_list',
		);
		$the_query   = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$pid           = get_the_ID();
				$status        = get_post_meta( $pid, 'status', true );
				$current_month = get_the_date( 'F' );
				$current_year  = get_the_date( 'Y' );
				if ( $current_year == $year ) {
					if ( $current_month == $month ) {
						if ( 'approved' === $status ) {
							array_push( $post_id_arr, $pid );

						}
					}
				}
			}
		}
		wp_reset_postdata();
		return $post_id_arr;
	}
	/**
	 * Retrieve Percentage Symbol.
	 *
	 * @since    1.0.0
	 * @param int $old Old Amount.
	 * @param int $new New Amount.
	 */
	public static function get_percentage_symbol( $old, $new ) {
		if ( $new > 0 ) {
			$sale_updown = ( 1 - $old / $new ) * 100;
		} else {
			$sale_updown = 0;
		}
		if ( $sale_updown > 0 ) {
			?>
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#41c592" class="bi bi-caret-up-fill" viewBox="0 0 16 16"><path d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/></svg>
			<?php
		} elseif ( $sale_updown < 0 ) {
			?>
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ee2644" class="bi bi-caret-down-fill" viewBox="0 0 16 16"><path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/></svg>
			<?php
		} else {
			?>
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f2d658" class="bi bi-circle-fill" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8"/></svg>
			<?php
		}
	}
	/**
	 * Get Month Sales.
	 *
	 * @since    1.0.0
	 * @param string $m Monthly Sale.
	 */
	public static function get_a_month_sale( $m ) {
		$post_id_arr = array();
		$year        = (int) current_time( 'Y' );
		$month       = gmdate( 'F', mktime( 0, 0, 0, $m, 10 ) );
		$args        = array(
			'posts_per_page' => -1,
			'post_type'      => 'sol_appointment_list',
		);
		$the_query   = new WP_Query( $args );
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$pid           = get_the_ID();
				$status        = get_post_meta( $pid, 'status', true );
				$current_month = get_the_date( 'F' );
				$current_year  = get_the_date( 'Y' );
				if ( $current_year == $year ) {
					if ( $current_month == $month ) {
						if ( 'approved' === $status ) {
							array_push( $post_id_arr, $pid );
						}
					}
				}
			}
		}
		wp_reset_postdata();
		return $post_id_arr;
	}
	/**
	 * Get Month Sale Amount.
	 *
	 * @since    1.0.0
	 * @param string $m Amount of Monthly Sale.
	 */
	public static function get_a_month_sale_amount( $m ) {
		$sales = self::get_a_month_sale( $m );
		if ( ! empty( $sales ) ) {
			$sale_arr = array();
			foreach ( $sales as $sale ) {
				$price             = get_post_meta( $sale, 'price', true );
				$slot              = get_post_meta( $sale, 'rf_slot', true );
				$fprice            = (float) $price * (int) $slot;
				$sale_arr[ $sale ] = $fprice;
			}
			if ( ! empty( $sale_arr ) ) {
				return array_sum( $sale_arr );
			}
		}
	}
}
