<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Custom_API_My_Account {

	public static function display_custom_api_data() {
		$current_user = wp_get_current_user();
		$user_elements = get_user_meta( $current_user->ID, 'custom_api_elements', true );
		$data = Custom_API_Handler::get_api_data( $user_elements );

		echo '<h2>' . __( 'Custom API Data', 'custom-api-integration' ) . '</h2>';

		echo '<div class="custom-api-data">';
		if ( ! empty( $data ) ) {
			echo '<ul>';
			foreach ( $data as $item ) {
				echo '<li>' . esc_html( $item ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>' . __( 'No data available.', 'custom-api-integration' ) . '</p>';
		}
		echo '</div>';

		self::display_settings_form();
	}

	public static function display_settings_form() {
		$current_user = wp_get_current_user();
		$user_elements = get_user_meta( $current_user->ID, 'custom_api_elements', true );
		?>

		<h2><?php _e( 'API Settings', 'custom-api-integration' ); ?></h2>

		<form id="custom-api-settings-form" method="post" action="">
			<?php wp_nonce_field( 'custom_api_settings_nonce', 'custom_api_settings_nonce_field' ); ?>
			<p>
				<label for="custom_api_elements"><?php _e( 'Enter Elements (comma separated)', 'custom-api-integration' ); ?></label><br />
				<input type="text" name="custom_api_elements" id="custom_api_elements" value="<?php echo esc_attr( $user_elements ); ?>" class="regular-text" />
			</p>
			<p>
				<input type="submit" value="<?php _e( 'Save Settings', 'custom-api-integration' ); ?>" class="button button-primary" />
			</p>
		</form>

		<?php
	}
}

Custom_API_My_Account::display_custom_api_data();
?>
