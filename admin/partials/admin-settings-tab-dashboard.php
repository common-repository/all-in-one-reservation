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
	'id'     => '100',
	'name'   => esc_html__( 'Dashboard', 'all-in-one-reservation' ),
	'filter' => 'aior_add_dashboard_admin_settings',
);
self::create_tab( $nav_args );
add_filter(
	'aior_add_dashboard_admin_settings',
	function () {
		$rf_data                           = Aior_Admin::get_settings();
		$aior_builder                      = new Aior_Builder();
		$aior_currency_symbol              = isset( $rf_data['aior_currency_symbol'] ) && ! empty( $rf_data['aior_currency_symbol'] ) ? $rf_data['aior_currency_symbol'] : '$';
		$aior_new_appointment_notification = get_option( 'aior_new_appointment_notification' );
		$sale_month_till_count             = ( 0 !== Aior_Revenue::last_thirty_day_sale() ) ? count( (array) Aior_Revenue::last_thirty_day_sale() ) : '0';
		$sale_prev_month_count             = ( 0 !== Aior_Revenue::get_last_month_sale() ) ? count( Aior_Revenue::get_last_month_sale() ) : '0';
		$sale_this_month_count             = ( 0 !== Aior_Revenue::get_this_month_sale() ) ? count( Aior_Revenue::get_this_month_sale() ) : '0';

		if ( ! $aior_new_appointment_notification ) {
			$aior_new_appointment_notification = 0;
		}
		?>
	<div class="aior-dashboard">
		<?php
		$month_1    = Aior_Revenue::get_a_month_sale_amount( 1 );
		$month_2    = Aior_Revenue::get_a_month_sale_amount( 2 );
		$month_3    = Aior_Revenue::get_a_month_sale_amount( 3 );
		$month_4    = Aior_Revenue::get_a_month_sale_amount( 4 );
		$month_5    = Aior_Revenue::get_a_month_sale_amount( 5 );
		$month_6    = Aior_Revenue::get_a_month_sale_amount( 6 );
		$month_7    = Aior_Revenue::get_a_month_sale_amount( 7 );
		$month_8    = Aior_Revenue::get_a_month_sale_amount( 8 );
		$month_9    = Aior_Revenue::get_a_month_sale_amount( 9 );
		$month_10   = Aior_Revenue::get_a_month_sale_amount( 10 );
		$month_11   = Aior_Revenue::get_a_month_sale_amount( 11 );
		$month_12   = Aior_Revenue::get_a_month_sale_amount( 12 );
		$chart_data = $month_1 . ',' . $month_2 . ',' . $month_3 . ',' . $month_4 . ',' . $month_5 . ',' . $month_6 . ',' . $month_7 . ',' . $month_8 . ',' . $month_9 . ',' . $month_10 . ',' . $month_11 . ',' . $month_12;
		?>
			<div class="solrow2">
				<div class="solcol2">
					<div class="airo-m-dash-w-s">
						<h4><?php esc_html_e( 'Previous Month Revenue', 'all-in-one-reservation' ); ?></h4>
						<?php if( empty( Aior_Revenue::get_last_month_sale_amount() ) ) { ?>
							<p><?php echo esc_html( $aior_currency_symbol ) . ' 0'; ?></p>
						<?php } else { ?>
							<p><?php echo esc_html( $aior_currency_symbol ) . esc_html( Aior_Revenue::get_last_month_sale_amount() ); ?></p>
						<?php } ?>
					</div>
				</div>
				<div class="solcol2">
					<div class="airo-m-dash-w-s">
						<h4><?php esc_html_e( 'This Month Revenue', 'all-in-one-reservation' ); ?></h4>
						<p>
							<?php
							if( empty( Aior_Revenue::get_this_month_sale_amount() ) ) {
								echo esc_html( $aior_currency_symbol ) . ' 0';
							} else {
								echo esc_html( $aior_currency_symbol ) . esc_html( Aior_Revenue::get_this_month_sale_amount() );
								Aior_Revenue::get_percentage_symbol( Aior_Revenue::get_last_month_sale_amount(), Aior_Revenue::get_this_month_sale_amount() );
							}
							?>
						</p>
					</div>
				</div>
				<div class="solcol2">
					<div class="airo-m-dash-w-s">
						<h4><?php esc_html_e( 'All Forms', 'all-in-one-reservation' ); ?></h4>
						<p>
						<a class="button button-primary button-large" href="<?php echo esc_url( get_admin_url() ); ?>edit.php?post_type=sol_reservation_form"><?php esc_html_e( 'List Forms', 'all-in-one-reservation' ); ?></a>
						</p>
					</div>
				</div>
				<div class="solcol2">
					<div class="airo-m-dash-w-s">
						<h4><?php esc_html_e( 'Create New Form', 'all-in-one-reservation' ); ?></h4>
						<p>
						<a class="button button-primary button-large" href="<?php echo esc_url( get_admin_url() ); ?>post-new.php?post_type=sol_reservation_form"><?php esc_html_e( 'Create New Form', 'all-in-one-reservation' ); ?></a>
						</p>
					</div>
				</div>
				<div class="solcol2"></div>
			</div>
			<div class="solrow2">
				<div class="solcol2">
					<div class="aior-revenue-chart-cnt">
						<canvas id="aior-revenue-chart"></canvas>
						<script>
						var ctx = jQuery('#aior-revenue-chart');
						var myChart = new Chart(ctx,{
							type: 'line',
							data: {
								labels: ['January','February','March','April','May','June','July','August','September','October','November','December'],
								datasets: [{
									label: 'Revenue',data: [<?php echo esc_html( $chart_data ); ?>],
									fill: false,borderColor:'#000',cubicInterpolationMode: 'monotone',
									tension: 0.4,
									borderWidth: 4
								}]
							},
							options: {
								responsive: true,
								plugins: {title: {display: true,},
								},
									interaction: {intersect: false,},
								scales: {y: {beginAtZero: true}}
							}
						});
						</script>
					</div>
				</div>
			</div>
		<div class="solrow2">
			<div class="solcol2">
				<div class="airo-m-dash-w">
					<h3><?php esc_html_e( 'New Appointments', 'all-in-one-reservation' ); ?></h3>
					<p>
					<?php esc_html_e( 'New appointment waiting for approval', 'all-in-one-reservation' ); ?> <span class="aior-bubble" aria-hidden="true"><?php echo esc_html( $aior_new_appointment_notification ); ?></span>
					</p>
					<p>
					<a class="button button-primary button-large" href="<?php echo esc_url( get_admin_url() ); ?>edit.php?post_type=sol_appointment_list"><?php esc_html_e( 'Take Action', 'all-in-one-reservation' ); ?></a>
					</p>
					<p>
					<small><?php esc_html_e( 'Take an action like, appointment approve, decline or check old appointments.', 'all-in-one-reservation' ); ?></small>
					</p>
				</div>               
			</div>
			<div class="solcol2">
				<?php
					$new_booking = Aior_Revenue::aior_get_today_sale();
				if ( $new_booking ) {
					$today_sale = count( $new_booking );
				} else {
					$today_sale = 0;
				}
					$y_day_booking = Aior_Revenue::get_yesterday_sale();
				if ( $y_day_booking ) {
					$y_sale = count( $y_day_booking );
				} else {
					$y_sale = 0;
				}
				?>
				<div class="airo-m-dash-w">
					<h3><?php esc_html_e( 'Today Sales', 'all-in-one-reservation' ); ?>
					<small style="float:right"> <?php Aior_Revenue::get_percentage_change( $y_sale, $today_sale ); ?> </small>
					</h3>
					<p> 
						<?php esc_html_e( 'New Sale', 'all-in-one-reservation' ); ?> <span class="aior-bubble" aria-hidden="true"><?php echo esc_html( $today_sale ); ?></span>
					</p>
					<p> 
						<?php esc_html_e( 'Total Yesterday Sale', 'all-in-one-reservation' ); ?> <span class="aior-bubble" aria-hidden="true"><?php echo esc_html( $y_sale ); ?></span>
					</p>
					<p>
						<?php esc_html_e( 'Yesterday Revenue', 'all-in-one-reservation' ); ?>
						<span class="aior-bubble" aria-hidden="true"><?php echo esc_html( $aior_currency_symbol ) . esc_html( Aior_Revenue::get_yesterday_sale_amount() ); ?> </span>
					</p>
					<p>
						<?php
						esc_html_e( 'Today Revenue', 'all-in-one-reservation' );
						?>
						<span class="aior-bubble" aria-hidden="true">
							<?php
							echo esc_html( $aior_currency_symbol ) . esc_html( Aior_Revenue::aior_get_today_sale_amount() );
							Aior_Revenue::get_percentage_symbol( Aior_Revenue::get_yesterday_sale_amount(), Aior_Revenue::aior_get_today_sale_amount() );
							?>
						</span>
					</p>
				</div>
			</div>
			<div class="solcol2">
				<div class="airo-m-dash-w">
					<h3><?php esc_html_e( 'Monthly Sales', 'all-in-one-reservation' ); ?>
					<small style="float:right"> <?php Aior_Revenue::get_percentage_change( $sale_prev_month_count, $sale_this_month_count ); ?> </small>
					</h3>
					<p><?php esc_html_e( 'Last 30 day sale', 'all-in-one-reservation' ); ?> <span class="aior-bubble" aria-hidden="true"> <?php echo esc_html( $sale_month_till_count ); ?> </span></p>
					<p><?php esc_html_e( 'Previous Month sale', 'all-in-one-reservation' ); ?> <span class="aior-bubble" aria-hidden="true"> <?php echo esc_html( $sale_prev_month_count ); ?> </span></p>
					<p><?php esc_html_e( 'This Month sale', 'all-in-one-reservation' ); ?> <span class="aior-bubble" aria-hidden="true"> <?php echo esc_html( $sale_this_month_count ); ?> </span></p>
					<p> 
						<?php
						esc_html_e( 'Previous Month Revenue', 'all-in-one-reservation' );
						?>
						<span class="aior-bubble" aria-hidden="true"><?php echo esc_html( $aior_currency_symbol ) . esc_html( Aior_Revenue::get_last_month_sale_amount() ); ?> </span>
					</p>
					<p>
						<?php
						esc_html_e( 'This Month Revenue', 'all-in-one-reservation' );
						?>
						<span class="aior-bubble" aria-hidden="true">
						<?php
						echo esc_html( $aior_currency_symbol ) . esc_html( Aior_Revenue::get_this_month_sale_amount() );
						Aior_Revenue::get_percentage_symbol( Aior_Revenue::get_last_month_sale_amount(), Aior_Revenue::get_this_month_sale_amount() );
						?>
						</span>
					</p>
				</div>
			</div>
		</div>
	</div>
		<?php

	},
	10,
	1
);
