<?php

/**
 * The file that defines the  plugin actions class
 *
 * @link       https://gradiamdigital.com
 * @since      1.0.0
 *
 * @package    Gradiamdigital_Order_Split
 * @subpackage Gradiamdigital_Order_Split/includes
 */

/**
 * The action plugin class.
 *
 * @since      1.0.0
 * @package    Gradiamdigital_Order_Split
 * @subpackage Gradiamdigital_Order_Split/includes
 * @author     White Label Coders <contact@gradiamdigital.com>
 */
class Gradiamdigital_Order_Split_Custom_Actions {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name      = $plugin_name;
		$this->version          = $version;
		$this->enable_split     = get_option( 'gradiamdigital-order-split' )['if_split_orders'];
		$this->meta_condition_1 = '_parent_order';
		$this->meta_condition_2 = '_parent_order_split';
	}

	/**
	 * Check number of items in order and update order meta with info.
	 *
	 * @since    1.0.0
	 * @param   integer $order_id Order ID.
	 * @return  void
	 */
	public function check_parent_order( $order_id ) {

		$order        = new WC_Order( $order_id );
		$items        = $order->get_items();
		$items_number = 0;
		foreach ( $items as $item_id => $item ) {
			if ( 'line_item' === $item->get_type() ) {
				++$items_number;
			}
		}
		return $items_number;
	}

	/**
	 * Select Parent Order's postdata to be passed to children orders
	 *
	 * @since    1.0.0
	 * @param   integer $order_id Order ID.
	 * @return  array
	 */
	public function get_postdata_to_pass( $order_id ) {

		global $wpdb;
		$data_object        = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT
					*
				From
					{$wpdb->prefix}posts
				Where
					{$wpdb->prefix}posts.ID = %d",
				$order_id
			)
		);
		$new_order_postdata = array(
			'post_author'    => $data_object->post_author,
			'post_date'      => $data_object->post_date,
			'post_date_gmt'  => $data_object->post_date_gmt,
			'post_content'   => $data_object->post_content,
			'post_title'     => 'Child-' . $data_object->post_title,
			'post_excerpt'   => $data_object->post_excerpt,
			'post_status'    => $data_object->post_status,
			'comment_status' => $data_object->comment_status,
			'ping_status'    => $data_object->ping_status,
			'post_name'      => 'child-' . $data_object->post_name,
			'post_parent'    => $data_object->ID,
			'menu_order'     => '',
			'post_type'      => 'shop_order',
		);
		return $new_order_postdata;
	}

	/**
	 * Select Parent Order's meta to be passed to children orders
	 *
	 * @since    1.0.0
	 * @param   integer $order_id Order ID.
	 * @return  array
	 */
	public function get_meta_to_pass_to_children( $order_id ) {
		global $wpdb;
		$order_meta_to_pass = array();
		$data_array         = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT
					*
				From
					{$wpdb->prefix}postmeta
				Where
					{$wpdb->prefix}postmeta.post_id = %d",
				$order_id
			)
		);
		foreach ( $data_array as $data_object ) {
			$key           = $data_object->meta_key;
			$value         = $data_object->meta_value;
			$keys_excluded = array(
				'_order_key',
				'_cart_discount',
				'_cart_discount_tax',
				'_order_shipping',
				'_order_shipping_tax',
				'_order_tax',
				'_order_total',
				'_new_order_email_sent',
				'_parent_order',
				'_parent_order_split',
				'_barcode_text',
				'_group',
				'_edit_lock',
			);
			if ( ! in_array( $key, $keys_excluded, true ) ) {
				$order_meta_to_pass[ $key ] = $value;
			}
		}
		return $order_meta_to_pass;
	}

	/**
	 * Select Parent Order's item data to be passed to children orders items
	 *
	 * @since    1.0.0
	 * @param   integer $item_id Item ID.
	 * @return  object
	 */
	public function get_split_order_item_postdata( $item_id ) {

		global $wpdb;
		$data_object = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT
					*
				From
					{$wpdb->prefix}woocommerce_order_items
				Where
					{$wpdb->prefix}woocommerce_order_items.order_item_id = %d",
				$item_id
			)
		);
		return $data_object;

	}

	/**
	 * Select Parent Order's item data to be passed to children orders items
	 *
	 * @since    1.0.0
	 * @param   integer $item_id Item ID.
	 * @return  array
	 */
	public function get_split_order_items_metadata( $item_id ) {
		$data_to_pass = array();
		global $wpdb;
		$data_array = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT
					*
				From
					{$wpdb->prefix}woocommerce_order_itemmeta
				Where
					{$wpdb->prefix}woocommerce_order_itemmeta.order_item_id = %d",
				$item_id
			)
		);
		foreach ( $data_array as $data_object ) {
			$key                  = $data_object->meta_key;
			$value                = $data_object->meta_value;
			$data_to_pass[ $key ] = $value;
		}
		return $data_to_pass;
	}

	// BARCODES.

	/**
	 * Retrieve order ID from barcode
	 *
	 * @param string $barcode Scanned barcode.
	 * @return integer   Order ID
	 */
	public function local_get_barcode_order( $barcode ) {

		if ( ! $barcode ) {
			return 0;
		}

		// Set up query.
		$args = array(
			'post_type'      => 'shop_order',
			'posts_per_page' => 1,
			'meta_key'       => '_barcode_text',
			'meta_value'     => $barcode,
		);

		if ( version_compare( WC()->version, 2.2, '>=' ) ) {
			$args['post_status'] = array_keys( wc_get_order_statuses() );
		}

		// Get orders.
		$orders = get_posts( $args );

		// Get order ID.
		$order_id = 0;
		if ( 0 < count( $orders ) ) {
			foreach ( $orders as $order ) {
				$order_id = $order->ID;
				break;
			}
		}

		return $order_id;

	} // End get_barcode_order ()

	/**
	 * Get text string for barcode
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	public function local_get_barcode_string() {

		// Use PHP's uniqid() for the barcode.
		$barcode_string = uniqid();

		// Check if this barcode already exists and add increment if so.
		$existing_order_id = $this->local_get_barcode_order( $barcode_string );
		$orig_string       = $barcode_string;
		$i                 = 1;
		while ( 0 !== $existing_order_id ) {
			$barcode_string    = $orig_string . $i;
			$existing_order_id = $this->local_get_barcode_order( $barcode_string );
			++$i;
		}

		// Return unique barcode.
		return $barcode_string;

	} // End get_barcode_string ()

	// End of barcode parts .

	/**
	 * Split Parent Order's to children orders
	 *
	 * @since    1.0.0
	 * @param   integer $order_id Order ID.
	 * @return  void
	 */
	public function split_parent_order( $order_id ) {

		update_post_meta( $order_id, $this->meta_condition_1, '1' );
		$items_number = $this->check_parent_order( $order_id );

		update_post_meta( $order_id, $this->meta_condition_2, $items_number );

		// Only run if orders split is enabled and is parent order and parent order is to split.
		if ( 'yes' === $this->enable_split && 1 < (int) $items_number ) {

			$order       = new WC_Order( $order_id );
			$order_items = $order->get_items();

			foreach ( $order_items as $item_id => $item ) {

				if ( 'line_item' === $item->get_type() ) {

					$new_order_meta = $this->get_meta_to_pass_to_children( $order_id );
					$item_meta      = $this->get_split_order_items_metadata( $item_id );

					$new_order_id = wp_insert_post( $this->get_postdata_to_pass( $order_id ), true );
					foreach ( $new_order_meta as $key => $value ) {
						update_post_meta( $new_order_id, $key, $value );
					}

					$new_order_additional_meta = array(
						'_order_tax'          => $item_meta['_line_tax'],
						'_order_total'        => $item_meta['_line_total'],
						'_cart_discount'      => '',
						'_cart_discount_tax'  => '',
						'_order_shipping'     => '',
						'_order_shipping_tax' => '',
						'_group'              => $item_meta['Group_id'],
					);

					foreach ( $new_order_additional_meta as $key => $value ) {
						update_post_meta( $new_order_id, $key, $value );
					}

					$barcode_string = $this->local_get_barcode_string();

					update_post_meta( $new_order_id, '_barcode_text', $barcode_string );

					$new_order_item_postdata = array(
						'order_item_name' => $this->get_split_order_item_postdata( $item_id )->order_item_name,
						'order_item_type' => $this->get_split_order_item_postdata( $item_id )->order_item_type,
					);

					$new_order_item_id = woocommerce_add_order_item( $new_order_id, $new_order_item_postdata );

					if ( $new_order_item_id ) {
						foreach ( $item_meta as $key => $value ) {
							woocommerce_add_order_item_meta( $new_order_item_id, $key, $value );
						}
					}
				}
			}

			wp_update_post(
				array(
					'ID'          => $order_id,
					'post_status' => 'wc-split',
				)
			);
		}
	}

}
