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

// Register the plugin settings
add_action('admin_init', 'openai_register_settings');
function openai_register_settings()
{

    // Register the settings group
    register_setting('openai_settings', 'openai_settings');

    // Add the API key setting
    add_settings_section(
        'openai_settings_section_api_key',
        __('API Key', 'openai-plugin'),
        'openai_render_section_api_key',
        'openai_settings'
    );
    add_settings_field(
        'openai_api_key',
        __('API Key', 'openai-plugin'),
        'openai_render_field_api_key',
        'openai_settings',
        'openai_settings_section_api_key'
    );

    // Add the completions per minute setting
    add_settings_section(
        'openai_settings_section_completions_per_minute',
        __('Completions per Minute', 'openai-plugin'),
        'openai_render_section_completions_per_minute',
        'openai_settings'
    );
    add_settings_field(
        'openai_completions_per_minute',
        __('Completions per Minute', 'openai-plugin'),
        'openai_render_field_completions_per_minute',
        'openai_settings',
        'openai_settings_section_completions_per_minute'
    );

    // Add the temperature setting
add_settings_section(
    'openai_settings_section_temperature',
    __('Temperature', 'openai-plugin'),
    'openai_render_section_temperature',
    'openai_settings');

    // Render the field
        echo '<select id="' . esc_attr($args['label_for']) . '" name="openai_settings[openai_disable_for_post_types][' . $post_type->name . ']" class="regular-text">';
        echo '<option value="">' . __('Enabled', 'openai-plugin') . '</option>';
        foreach ($post_types as $pt) {
            if ($pt != $post_type->name) {
                echo '<option value="' . $pt . '"';
                if (isset($settings['openai_disable_for_post_types'][$post_type->name]) && $settings['openai_disable_for_post_types'][$post_type->name] == $pt) {
                    echo ' selected';
                }
                echo '>' . esc_html($pt) . '</option>';
            }
        }
        echo '</select>';

    }



/**
 * Render the field for the default prompt for a specific post type
 *
 * @param array $args Arguments for the field rendering
 */
function openai_render_field_default_prompt_post_type($args)
{

    // Get the post type
    $post_type_name = str_replace('openai_default_prompt_', '', $args['id']);

    // Get the current settings from the database
    $settings = get_option('openai_settings');

    // Get the default prompt for the post type
    $default_prompt = isset($settings['openai_default_prompts'][$post_type_name]) ? $settings['openai_default_prompts'][$post_type_name] : '';

    // Render the field
    echo '<textarea id="' . esc_attr($args['label_for']) . '" name="openai_settings[openai_default_prompts][' . $post_type_name . ']" class="large-text">' . esc_textarea($default_prompt) . '</textarea>';

}

/**
 * Render the field for the stop sequence for a specific post type
 *
 * @param array $args Arguments for the field rendering
 */
function openai_render_field_stop_sequence_post_type($args)
{

    // Get the post type
    $post_type_name = str_replace('openai_stop_sequence_', '', $args['id']);

    // Get the current settings from the database
    $settings = get_option('openai_settings');

    // Get the stop sequence for the post type
    $stop_sequence = isset($settings['openai_stop_sequences'][$post_type_name]) ? $settings['openai_stop_sequences'][$post_type_name] : '';

    // Render the field
    echo '<input type="text" id="' . esc_attr($args['label_for']) . '" name="openai_settings[openai_stop_sequences][' . $post_type_name . ']" value="' . esc_attr($stop_sequence) . '" class="regular-text">';

}

/**
 * Sanitize the plugin settings
 *
 * @param array $input The unsanitized plugin settings
 *
 * @return array The sanitized plugin settings
 */
function openai_sanitize_settings($input)
{

    // Sanitize the API key setting
    if (isset($input['openai_api_key'])) {
        $input['openai_api_key'] = sanitize_text_field($input['openai_api_key']);
    }

    // Sanitize the completions per minute setting
    if (isset($input['openai_completions_per_minute'])) {
        $input['openai_completions_per_minute'] = absint($input['openai_completions_per_minute']);
    }

    // Sanitize the temperature setting
    if (isset($input['openai_temperature'])) {
        $input['openai_temperature'] = floatval($input['openai_temperature']);

        // Render the custom post type settings fields
        function openai_render_custom_post_type_settings_fields()
        {
            $post_types = get_post_types(array('public' => true), 'objects');

            foreach ($post_types as $post_type) {

                // Get the current settings from the database
                $settings = get_option('openai_settings');

                // Get the default prompt for the post type
                $default_prompt = isset($settings['openai_default_prompt_' . $post_type->name]) ? $settings['openai_default_prompt_' . $post_type->name] : '';

                // Sanitize the default prompt setting
                $default_prompt = sanitize_textarea_field($default_prompt);

                // Get the custom stop sequence for the post type
                $custom_stop_sequence = isset($settings['openai_custom_stop_sequence_' . $post_type->name]) ? $settings['openai_custom_stop_sequence_' . $post_type->name] : '';

                // Sanitize the custom stop sequence setting
                $custom_stop_sequence = sanitize_text_field($custom_stop_sequence);

                // Get the maximum completions per prompt for the post type
                $max_completions_per_prompt = isset($settings['openai_max_completions_per_prompt_' . $post_type->name]) ? intval($settings['openai_max_completions_per_prompt_' . $post_type->name]) : '';

                // Sanitize the completions per minute setting
                $max_completions_per_prompt = absint($max_completions_per_prompt);

                // Output the custom post type settings fields HTML
                echo '<div class="openai-settings-custom-post-type">';
                echo '<h3>' . $post_type->labels->name . '</h3>';
                echo '<p><label for="openai_default_prompt_' . esc_attr($post_type->name) . '">' . __('Default Prompt', 'openai-plugin') . '</label><br />';
                echo '<textarea id="openai_default_prompt_' . esc_attr($post_type->name) . '" name="openai_settings[openai_default_prompt_' . esc_attr($post_type->name) . ']">' . esc_textarea($default_prompt) . '</textarea></p>';
                echo '<p><label for="openai_custom_stop_sequence_' . esc_attr($post_type->name) . '">' . __('Custom Stop Sequence', 'openai-plugin') . '</label><br />';
                echo '<input type="text" id="openai_custom_stop_sequence_' . esc_attr($post_type->name) . '" name="openai_settings[openai_custom_stop_sequence_' . esc_attr($post_type->name) . ']" value="' . esc_attr($custom_stop_sequence) . '"></p>';
                echo '<p><label for="openai_max_completions_per_prompt_' . esc_attr($post_type->name) . '">' . __('Max Completions per Prompt', 'openai-plugin') . '</label><br />';
                echo '<input type="number" id="openai_max_completions_per_prompt_' . esc_attr($post_type->name) . '" name="openai_settings[openai_max_completions_per_prompt_' . esc_attr($post_type->name) . ']" min="1" value="' . esc_attr($max_completions_per_prompt) . '"></p>';
                echo '</div>';

            }
        }

        // Validate the settings before saving
        function openai_validate_settings($input)
        {
            // Sanitize the completions per minute setting
            $input['openai_completions_per_minute'] = absint($input['openai_completions_per_minute']);

            // Register the settings fields for each user role
            foreach (get_editable_roles() as $role_name => $role_info) {
                // Skip the administrator role, since it has access to all settings
                if ($role_name === 'administrator') {
                    continue;
                }

                // Add a section for the user role's settings
                add_settings_section(
                    'openai_settings_' . $role_name,
                    sprintf(__('OpenAI Settings for %s', 'openai-plugin'), $role_info['name']),
                    function () { },
                    'openai_settings'
                );

                // Add a setting to enable or disable the plugin for the user role
                add_settings_field(
                    'openai_enable_for_' . $role_name,
                    sprintf(__('Enable for %s', 'openai-plugin'), $role_info['name']),
                    'openai_render_field_enable_for_role',
                    'openai_settings',
                    'openai_settings_' . $role_name,
                    array(
                        'label_for' => 'openai_enable_for_' . $role_name,
                        'role_name' => $role_name
                    )
                );

                // Add a setting to set the default prompt for the user role
                add_settings_field(
                    'openai_default_prompt_' . $role_name,
                    sprintf(__('Default Prompt for %s', 'openai-plugin'), $role_info['name']),
                    'openai_render_field_default_prompt_role',
                    'openai_settings',
                    'openai_settings_' . $role_name,
                    array(
                        'label_for' => 'openai_default_prompt_' . $role_name,
                        'role_name' => $role_name
                    )
                );

                // Add a setting to set the custom stop sequence for the user role
                add_settings_field(
                    'openai_stop_sequence_' . $role_name,
                    sprintf(__('Stop Sequence for %s', 'openai-plugin'), $role_info['name']),
                    'openai_render_field_stop_sequence_role',
                    'openai_settings',
                    'openai_settings_' . $role_name,
                    array(
                        'label_for' => 'openai_stop_sequence_' . $role_name,
                        'role_name' => $role_name
                    )
                );

                // Add a setting to set the maximum completions per prompt for the user role
                add_settings_field(
                    'openai_completions_per_prompt_' . $role_name,
                    sprintf(__('Completions per Prompt for %s', 'openai-plugin'), $role_info['name']),
                    'openai_render_field_completions_per_prompt_role',
                    'openai_settings',
                    'openai_settings_' . $role_name,
                    array(
                        'label_for' => 'openai_completions_per_prompt_' . $role_name,
                        'role_name' => $role_name
                    )
                );
            }

            // Render the settings page HTML
            include_once('templates/settings.php');

        }
    }
}