<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://gradiamdigital.com
 * @since      1.0.0
 *
 * @package    Gradiamdigital_Order_Split
 * @subpackage Gradiamdigital_Order_Split/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Gradiamdigital_Order_Split
 * @subpackage Gradiamdigital_Order_Split/includes
 * @author     White Label Coders <contact@gradiamdigital.com>
 */
class Gradiamdigital_Order_Split_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'gradiamdigital-order-split',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
