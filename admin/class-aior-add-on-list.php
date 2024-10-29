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

/* WP_List_Table is not loaded automatically so we need to load it in our application */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

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
class Aior_Add_On_List extends WP_List_Table {
	/**
	 * Prepare the items for the table to process
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		$data     = $this->table_data();
		usort( $data, array( &$this, 'sort_data' ) );
		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$total_items  = count( $data );
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
		$data                  = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $data;
	}
	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns() {
		$columns = array(
			'thumb'       => '',
			'title'       => esc_html__( 'Add-On', 'all-in-one-reservation' ),
			'slug'        => esc_html__( 'Slug', 'all-in-one-reservation' ),
			'description' => esc_html__( 'Description', 'all-in-one-reservation' ),
			'version'     => esc_html__( 'Version', 'all-in-one-reservation' ),
			'author'      => esc_html__( 'Developer', 'all-in-one-reservation' ),
		);
		return $columns;
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns() {
		return array( 'title' => array( 'title', false ) );
	}
	/**
	 * Get the table data
	 *
	 * @return Array
	 */
	private function table_data() {
		$data     = array();
		$add_on_s = Aior_Package::info();
		if ( $add_on_s ) {
			$i = 1;
			foreach ( $add_on_s as $addon ) {
				$data[] = array(
					'id'          => $i,
					'title'       => $addon['name'],
					'description' => $addon['description'],
					'version'     => $addon['version'],
					'author'      => $addon['author'],
					'action'      => '',
					'thumb'       => $addon['thumb'],
					'slug'        => $addon['slug'],
				);
				$i++;
			}
		}
		return $data;
	}
	/**
	 * Define what data to show on each column of the table.
	 *
	 * @param  Array  $item        Data.
	 * @param  String $column_name - Current column name.
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'thumb':
				if ( $item[ $column_name ] ) {
					return '<img src="' . AIOR_PLUGIN_URL . '/' . $item[ $column_name ] . '" width="68">';
				} else {
					return '';
				}
			case 'slug':
			case 'description':
			case 'version':
			case 'author':
				return $item[ $column_name ];
			case 'title':
				return $item[ $column_name ] . '<br>' . self::action( $item );
			default:
				return print_r( $item, true );
		}
	}
	/**
	 * Allows you to sort the data by the variables set in the $_GET.
	 *
	 * @param array $a Column name.
	 * @param array $b Order By.
	 *
	 * @return Mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults.
		$orderby = 'title';
		$order   = 'asc';
		// If orderby is set, use this as the sort column.
		if ( ! empty( $_GET['orderby'] ) ) {
			$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
		}
		// If order is set use this as the order.
		if ( ! empty( $_GET['order'] ) ) {
			$order = sanitize_text_field( wp_unslash( $_GET['order'] ) );
		}
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );
		if ( 'asc' === $order ) {
			return $result;
		}
		return -$result;
	}
	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @param Array $item items.
	 * @return Mixed
	 */
	public static function action( $item ) {
		ob_start();
		$slug = $item['slug'];
		$path = WP_PLUGIN_DIR;
		if ( $slug ) {
			$add_on_json   = get_option( 'aior_addon_status' );
			$status        = json_decode( $add_on_json, true );
			$sms_status    = Aior_Package::get_plugin_status( 'twilio-sms/class-aior-twilio.php' );
			$paypal_status = Aior_Package::get_plugin_status( 'paypal/class-aior-paypal.php' );
			$stripe_status = Aior_Package::get_plugin_status( 'pay-stripe/class-aior-stripe.php' );
			$dbt_status    = Aior_Package::get_plugin_status( 'direct-bank-transfer/class-aior-dbt.php' );
			wp_nonce_field( 'on_aior_admin_global_nonce', 'aior_admin_global_nonce' );
			if ( 'twilio-sms' == $slug ) {
				if ( 1 == $sms_status ) {
					echo '<p data-todo="1" data-slug="' . esc_attr( $slug ) . '" class="aior_add_on_action_btn aior_add_on_act">' . esc_html__( 'Activated!', 'all-in-one-reservation' ) . '</p>';
				} elseif ( 2 == $sms_status ) {
					echo '<p data-todo="0" data-slug="' . esc_attr( $slug ) . '" class="aior_add_on_action_btn aior_add_on_dea">' . esc_html__( 'Activate', 'all-in-one-reservation' ) . '</p>';
				} else {
					echo '<a class="aior_add_on_buynow_btn aior_add_on_price" target="_blank" href="https://www.solwininfotech.com/product/wordpress-plugins/twilio-sms-add-on-for-aior/">' . esc_html__( 'BUY NOW - $12', 'all-in-one-reservation' ) . '</a>';
				}
			}
			if ( 'paypal' == $slug ) {
				if ( 1 == $paypal_status ) {
					echo '<p data-todo="1" data-slug="' . esc_attr( $slug ) . '" class="aior_add_on_action_btn aior_add_on_act">' . esc_html__( 'Activated!', 'all-in-one-reservation' ) . '</p>';
				} elseif ( 2 == $paypal_status ) {
					echo '<p data-todo="0" data-slug="' . esc_attr( $slug ) . '" class="aior_add_on_action_btn aior_add_on_dea">' . esc_html__( 'Activate', 'all-in-one-reservation' ) . '</p>';
				} else {
					echo '<a class="aior_add_on_buynow_btn aior_add_on_price" target="_blank" href="https://www.solwininfotech.com/product/wordpress-plugins/paypal-payment-gateway-add-on-for-aior/">' . esc_html__( 'BUY NOW - $12', 'all-in-one-reservation' ) . '</a>';
				}
			}
			if ( 'pay-stripe' == $slug ) {
				if ( 1 == $stripe_status ) {
					echo '<p data-todo="1" data-slug="' . esc_attr( $slug ) . '" class="aior_add_on_action_btn aior_add_on_act">' . esc_html__( 'Activated!', 'all-in-one-reservation' ) . '</p>';
				} elseif ( 2 == $stripe_status ) {
					echo '<p data-todo="0" data-slug="' . esc_attr( $slug ) . '" class="aior_add_on_action_btn aior_add_on_dea">' . esc_html__( 'Activate', 'all-in-one-reservation' ) . '</p>';
				} else {
					echo '<a class="aior_add_on_buynow_btn aior_add_on_price" target="_blank" href="https://www.solwininfotech.com/product/wordpress-plugins/stripe-payment-gateway-add-on-for-aior/">' . esc_html__( 'BUY NOW - $12', 'all-in-one-reservation' ) . '</a>';
				}
			}
			if ( 'direct-bank-transfer' == $slug ) {
				if ( 1 == $dbt_status ) {
					echo '<p data-todo="1" data-slug="' . esc_attr( $slug ) . '" class="aior_add_on_action_btn aior_add_on_act">' . esc_html__( 'Activated!', 'all-in-one-reservation' ) . '</p>';
				} elseif ( 2 == $dbt_status ) {
					echo '<p data-todo="0" data-slug="' . esc_attr( $slug ) . '" class="aior_add_on_action_btn aior_add_on_dea">' . esc_html__( 'Activate', 'all-in-one-reservation' ) . '</p>';
				} else {
					echo '<a class="aior_add_on_buynow_btn aior_add_on_price" target="_blank" href="https://www.solwininfotech.com/product/wordpress-plugins/direct-bank-transfer-add-on-for-aior/">' . esc_html__( 'BUY NOW - $12', 'all-in-one-reservation' ) . '</a>';
				}
			}
			$o = ob_get_contents();
			ob_end_clean();
			return $o;
		}
	}
}
