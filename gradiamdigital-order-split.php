<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://gradiamdigital.com
 * @since             1.0.0
 * @package           Gradiamdigital_Order_Split
 *
 * @wordpress-plugin
 * Plugin Name:       Gradiamdigital Order Split
 * Plugin URI:        https://gradiamdigital.com
 * Description:       Split the order containing more than 1 item into many orders containing 1 item each.
 * Version:           1.0.0
 * Author:            White Label Coders
 * Author URI:        https://gradiamdigital.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gradiamdigital-order-split
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GRADIAMDIGITAL_ORDER_SPLIT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gradiamdigital-order-split-activator.php
 */
function activate_gradiamdigital_order_split() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gradiamdigital-order-split-activator.php';
	Gradiamdigital_Order_Split_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gradiamdigital-order-split-deactivator.php
 */
function deactivate_gradiamdigital_order_split() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gradiamdigital-order-split-deactivator.php';
	Gradiamdigital_Order_Split_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gradiamdigital_order_split' );
register_deactivation_hook( __FILE__, 'deactivate_gradiamdigital_order_split' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gradiamdigital-order-split.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gradiamdigital_order_split() {

	$plugin = new Gradiamdigital_Order_Split();
	$plugin->run();

}
run_gradiamdigital_order_split();
