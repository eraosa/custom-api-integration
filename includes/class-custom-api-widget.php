<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Custom_API_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'custom_api_widget',
			__( 'Custom API Widget', 'custom-api-integration' ),
			array( 'description' => __( 'Displays data from a custom API based on user preferences.', 'custom-api-integration' ) )
		);
	}

	public function widget( $args, $instance ) {
		$current_user = wp_get_current_user();
		$user_elements = get_user_meta( $current_user->ID, 'custom_api_elements', true );

		$data = Custom_API_Handler::get_api_data( $user_elements );

		echo $args['before_widget'];
		echo $args['before_title'] . __( 'Custom API Data', 'custom-api-integration' ) . $args['after_title'];

		if ( ! empty( $data ) ) {
			echo '<ul>';
			foreach ( $data as $item ) {
				echo '<li>' . esc_html( $item ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo __( 'No data available.', 'custom-api-integration' );
		}

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		// No settings for this widget.
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		return $instance;
	}
}
?>
