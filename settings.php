<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

// Add settings page to the WordPress dashboard
add_action('admin_menu', 'openai_add_settings_page');
function openai_add_settings_page()
{
    add_submenu_page(
        'options-general.php',
        __('OpenAI Settings', 'openai-plugin'),
        __('OpenAI Settings', 'openai-plugin'),
        'manage_options',
        'openai-settings',
        'openai_render_settings_page'
    );
}

// Render the settings page
function openai_render_settings_page()
{

    // Check if the user is authorized to access the settings page
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'openai-plugin'));
    }

    // Get the current settings from the database
    $settings = get_option('openai_settings');

    // Check if the form has been submitted
    if (isset($_POST['openai_settings_submit'])) {

        // Update the settings in the database
        $settings['openai_api_key'] = sanitize_text_field($_POST['openai_api_key']);
        $settings['openai_completions_per_minute'] = sanitize_text_field($_POST['openai_completions_per_minute']);
        $settings['openai_temperature'] = sanitize_text_field($_POST['openai_temperature']);
        $settings['openai_max_tokens'] = sanitize_text_field($_POST['openai_max_tokens']);
        $settings['openai_stop_sequence'] = sanitize_text_field($_POST['openai_stop_sequence']);
        $settings['openai_prompt'] = sanitize_text_field($_POST['openai_prompt']);
        update_option('openai_settings', $settings);

        // Display a success message
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved successfully.', 'openai-plugin') . '</p></div>';

    }

    // Display the settings page HTML
    include_once('templates/settings.php');

}