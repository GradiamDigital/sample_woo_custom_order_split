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
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<fieldset>
	<h3 class="<?php echo esc_attr( $this->plugin_name ) . '-admin-input-header'; ?>"><?php esc_attr_e( $this->label, 'gradiamdigital-order-split' ); ?></h3>
	<legend class="screen-reader-text">
		<span><?php esc_attr_e( $this->label, 'gradiamdigital-order-split' ); ?></span>
	</legend>
	<input type="text" class="<?php echo esc_attr( $this->plugin_name ) . '-admin-input'; ?>" id="<?php echo esc_attr( $this->plugin_name ) . '-if_split_order'; ?>" name="<?php echo esc_attr( $this->plugin_name ); ?>[<?php echo $this->name; ?>]" value="<?php echo esc_attr( $this->value ); ?>" />
</fieldset>
