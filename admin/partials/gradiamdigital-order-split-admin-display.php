<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://gradiamdigital.com
 * @since      1.0.0
 *
 * @package    Gradiamdigital_Order_Split
 * @subpackage Gradiamdigital_Order_Split/admin/partials
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

function gradiamdigital_order_split_admin_value_or_def( $options, $name, $def = '' ) {
	return ( isset( $options[ $name ] ) && ! empty( $options[ $name ] ) ) ? esc_attr( $options[ $name ] ) : $def;
}
?>

<div class="wrap">
	<h2>Gradiamdigital Order Split <?php esc_attr_e( 'Settings', 'gradiamdigital-order-split' ); ?></h2>

	<form id="<?php echo esc_attr( $this->plugin_name ) . '-admin-form'; ?>" class="<?php echo esc_attr( $this->plugin_name ) . '-admin-form'; ?>" method="post" name="<?php echo esc_attr( $this->plugin_name ); ?>" action="options.php">
		<?php
		// Grab all options.
		$options = get_option( $this->plugin_name );

		$if_split_orders = gradiamdigital_order_split_admin_value_or_def( $options, 'if_split_orders', 'yes' );


		settings_fields( $this->plugin_name );
		do_settings_sections( $this->plugin_name );

		?>
		<?php
		$this->create_field(
			array(
				'name'  => 'if_split_orders',
				'value' => $if_split_orders,
				'label' => 'Confirm that the orders containing more than one item should be split',
			)
		);

		submit_button( __( 'Save all changes', 'gradiamdigital-order-split' ), 'primary', 'submit', true );
		?>

	</form>
</div>
