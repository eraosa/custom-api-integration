<?php
/**
 * Plugin Name: Custom API Integration
 * Description: Fetch and display data from an API based on user preferences.
 * Version: 1.0
 * Author: Josh (SAU/CAL)
 * Author URI: https://saucal.com
 * Text Domain: custom-api-integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants
define( 'CUSTOM_API_INTEGRATION_VERSION', '1.0' );
define( 'CUSTOM_API_INTEGRATION_DIR', plugin_dir_path( __FILE__ ) );
define( 'CUSTOM_API_INTEGRATION_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files with error handling
$includes = array(
	'includes/class-custom-api-widget.php',
	'includes/class-custom-api-my-account.php',
	'includes/class-custom-api-handler.php',
);

foreach ( $includes as $file ) {
	if ( file_exists( CUSTOM_API_INTEGRATION_DIR . $file ) ) {
		require_once CUSTOM_API_INTEGRATION_DIR . $file;
	} else {
		add_action( 'admin_notices', function() use ( $file ) {
			echo '<div class="notice notice-error"><p>' . esc_html( "File not found: $file" ) . '</p></div>';
		} );
	}
}

// Register widget
function custom_api_integration_register_widget() {
	if ( class_exists( 'Custom_API_Widget' ) ) {
		register_widget( 'Custom_API_Widget' );
	} else {
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p>' . esc_html( "Widget class not found: Custom_API_Widget" ) . '</p></div>';
		} );
	}
}
add_action( 'widgets_init', 'custom_api_integration_register_widget' );

// Add My Account tab
function custom_api_integration_add_my_account_tab( $items ) {
	$items['custom-api'] = __( 'Custom API Data', 'custom-api-integration' );
	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'custom_api_integration_add_my_account_tab' );

// Display My Account tab content
function custom_api_integration_my_account_content() {
	// This function should call a method in class-custom-api-my-account.php to display the content.
	Custom_API_My_Account::display_custom_api_data();
}
add_action( 'woocommerce_account_custom-api_endpoint', 'custom_api_integration_my_account_content' );


// Enqueue scripts and styles
function custom_api_integration_enqueue_scripts() {
	wp_enqueue_style( 'custom-api-integration-style', CUSTOM_API_INTEGRATION_URL . 'assets/css/style.css', array(), CUSTOM_API_INTEGRATION_VERSION );
	wp_enqueue_script( 'custom-api-integration-script', CUSTOM_API_INTEGRATION_URL . 'assets/js/scripts.js', array( 'jquery' ), CUSTOM_API_INTEGRATION_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'custom_api_integration_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'custom_api_integration_enqueue_scripts' );

// Add endpoint for My Account tab
function custom_api_integration_add_endpoint() {
	add_rewrite_endpoint( 'custom-api', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'custom_api_integration_add_endpoint' );

// Handle form submission for settings
function custom_api_integration_handle_form_submission() {
	if ( isset( $_POST['custom_api_settings_nonce_field'] ) && wp_verify_nonce( $_POST['custom_api_settings_nonce_field'], 'custom_api_settings_nonce' ) ) {
		$current_user = wp_get_current_user();
		$custom_api_elements = isset( $_POST['custom_api_elements'] ) ? sanitize_text_field( $_POST['custom_api_elements'] ) : '';
		update_user_meta( $current_user->ID, 'custom_api_elements', $custom_api_elements );
		wp_redirect( wc_get_account_endpoint_url( 'custom-api' ) );
		exit;
	}
}
add_action( 'template_redirect', 'custom_api_integration_handle_form_submission' );
?>
