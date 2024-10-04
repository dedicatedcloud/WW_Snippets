<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function custom_theme_settings_page() {
    add_options_page(
        'Custom Theme Settings',          // Page title
        'Theme Settings',                 // Menu title
        'manage_options',                 // Capability
        'custom-theme-settings',          // Menu slug
        'custom_theme_settings_page_html' // Callback function to render the page content
    );
}
add_action( 'admin_menu', 'custom_theme_settings_page' );

function custom_theme_settings_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    // Check if the settings have been updated
    if ( isset( $_GET['settings-updated'] ) ) {
        add_settings_error( 'custom_theme_messages', 'custom_theme_message', 'Settings Saved', 'updated' );
    }
    
    // Show error/update messages
    settings_errors( 'custom_theme_messages' );
    ?>
    <div class="wrap">
        <h1>Custom Theme Settings</h1>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'custom_theme_settings' );
            do_settings_sections( 'custom-theme-settings' );
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php
}

function custom_theme_settings_init() {
    // Register a new setting for custom theme settings page with sanitize callback
    register_setting( 'custom_theme_settings', 'custom_theme_settings', 'custom_theme_sanitize_checkbox' );

    // Add a new section in the settings page
    add_settings_section(
        'custom_theme_settings_section',
        'File Inclusion Settings',
        'custom_theme_settings_section_cb',
        'custom-theme-settings'
    );

    // Add a checkbox field for each file
    $files = array(
		// 'cart_checkout_merge' => 'SKIP CART <p style="font-style:italic; font-weight:normal;">Straight to Checkout</p>',
	    'dashboard' => 'CUSTOM DASHBOARD <p style=" font-weight:normal;">Removes Default Dashboard and Creates Custom Buttons</p>',
        'improve'             => 'Include Improve Functions',
        'store-information-settings' => 'Store Information Settings'

    );

    foreach ( $files as $option_name => $label ) {
        add_settings_field(
            $option_name . '_include', // Option ID
            $label,                    // Title
            'custom_theme_checkbox_cb',// Callback function
            'custom-theme-settings',   // Page slug
            'custom_theme_settings_section', // Section ID
            array( 'option_name' => $option_name ) // Arguments for the callback
        );
    }
}
add_action( 'admin_init', 'custom_theme_settings_init' );

function custom_theme_settings_section_cb() {
    echo '<p>Toggle the inclusion of specific functionality files in the theme.</p>';
}

// Reusable checkbox callback for the files
function custom_theme_checkbox_cb( $args ) {
    $option_name = $args['option_name'];
    $options = get_option( 'custom_theme_settings' );

    // Check if the option is set, if not, default to unchecked (0)
    $is_checked = isset( $options[ $option_name . '_include' ] ) ? $options[ $option_name . '_include' ] : 0;

    // Use the 'checked' function to check the checkbox only if the value is 1
    echo '<input type="checkbox" name="custom_theme_settings[' . $option_name . '_include]" value="1" ' . checked( 1, $is_checked, false ) . ' />';
}

// Sanitize function to handle checkbox saving properly
function custom_theme_sanitize_checkbox( $input ) {
    $sanitized_input = array();

    // List all the checkbox options (same as the ones you created earlier)
    $checkboxes = array(
		//'cart_checkout_merge_include',
		'dashboard_include',
        'improve_include',
        'menu_item_include',
        'order_alert_include',
        'order_numbers_include',
        'order_view_include',
        'remove_menu_item_include',
		'remove_product_url_include',
		'remove_extensions_include',

    );

    // Loop through each checkbox and set value to '1' if checked, otherwise set it to '0'
    foreach ( $checkboxes as $checkbox ) {
        $sanitized_input[ $checkbox ] = isset( $input[ $checkbox ] ) ? 1 : 0;
    }

    return $sanitized_input;
}

// Get the theme settings
$theme_settings = get_option( 'custom_theme_settings' );

// Conditionally include the 'dashboard.php' file
if ( isset( $theme_settings['dashboard_include'] ) && $theme_settings['dashboard_include'] ) {
    require_once( __DIR__ . '/includes/dashboard.php' );
}

// Conditionally include the 'store-information-settings.php' file
if ( isset( $theme_settings['store-information-settings_include'] ) && $theme_settings['store-information-settings_include'] ) {
    require_once( __DIR__ . '/Woocommerce/store-information-settings.php' );
}

