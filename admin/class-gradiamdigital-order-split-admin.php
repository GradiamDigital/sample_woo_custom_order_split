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
class Gradiamdigital_Order_Split_Admin {

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
	public function __construct( $plugin_name, $version ) {

		require_once dirname( __FILE__ ) . '/class-gradiamdigital-order-split-admin-field.php';

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/gradiamdigital-order-split-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/gradiamdigital-order-split-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		/**
		 * Add a settings page for this plugin to the Settings menu.
		 */
		add_submenu_page(
			'tools.php',
			'Gradiamdigital Order Split Settings',
			'Gradiamdigital Order Split',
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_setup_page' )
		);
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 *
	 * @param string $links ...
	 */
	public function add_action_links( $links ) {
		/*
		*  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_( plugin_file_name)
		*/
		$settings_link = array(
			'<a href="' . admin_url( 'tools.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', 'gradiamdigital-split-order' ) . '</a>',
		);
		return array_merge( $settings_link, $links );
	}

	/**
	 * Create setting field.
	 *
	 * @since    1.0.0
	 */
	protected function create_field( array $props ) {
		return ( new Gradiamdigital_Order_Split_Admin_Field( $this->plugin_name, $this->version, $props ) )->display();
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
		include_once 'partials/' . $this->plugin_name . '-admin-display.php';
	}

	/**
	 * Validate fields from admin area plugin settings form ( 'exopite-lazy-load-xt-admin-display.php')
	 *
	 * @param  mixed $input as field form settings form.
	 * @return mixed as validated fields
	 */
	public function validate( $input ) {
		$fields_default = array(
			'if_split_orders' => 'yes',
		);
		$fields         = array(
			'if_split_orders',
		);

		$fields_norm  = array();
		$count_fields = count( $fields );

		for ( $i = 0; $i < $count_fields; $i++ ) {
			$field_name = $fields[ $i ];
			if ( ! isset( $input[ $field_name ] ) || empty( $input[ $field_name ] ) ) {
				$fields_norm[ $field_name ] = isset( $fields_default[ $field_name ] ) ? $fields_default[ $field_name ] : '';
			} else {
				$fields_norm[ $field_name ] = $input[ $field_name ];
			}
		}
		return $fields_norm;
	}

	/**
	 * Updading options.
	 *
	 * @since    1.0.0
	 */
	public function options_update() {
		register_setting(
			$this->plugin_name,
			$this->plugin_name,
			array(
				'sanitize_callback' => array( $this, 'validate' ),
			)
		);
	}

	/**
	 * Create custom order status for parent order split
	 *
	 * @since    1.0.0
	 * @return  void
	 */
	public function register_split_order_status() {
		register_post_status(
			'wc-split',
			array(
				'label'                     => _x( 'Split', 'WooCommerce Order status', 'gradiamdigital-order-split' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Approved (%s)', 'Approved (%s)', 'gradiamdigital-order-split' ),
			)
		);
	}

	/**
	 * Add custom wc-split order status to WC order statuses
	 *
	 * @since    1.0.0
	 * @return  array
	 */
	public function add_split_to_order_statuses( $order_statuses ) {

		$order_statuses['wc-split'] = _x( 'Split', 'WooCommerce Order status', 'gradiamdigital-order-split' );
		return $order_statuses;
	}



}
