<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://gradiamdigital.com
 * @since      1.0.0
 *
 * @package    Gradiamdigital_Order_Split
 * @subpackage Gradiamdigital_Order_Split/admin
 */
class Gradiamdigital_Order_Split_Admin_Field {


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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $props ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->name        = isset( $props['name'] ) ? $props['name'] : 'undefined';
		$this->value       = isset( $props['value'] ) ? $props['value'] : '';
		$this->label       = isset( $props['label'] ) ? $props['label'] : '';
	}

	/**
	 * Render element.
	 *
	 * @since    1.0.0
	 */
	public function display() {
		include 'partials/' . $this->plugin_name . '-admin-field.php';
	}
}
