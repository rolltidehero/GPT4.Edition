<?php
/*
Plugin Name: AI Content Generator Pro: GPT4 Edition
Plugin URI: https://www.example.com/
Description: This plugin uses the latest GPT4 language model from OpenAI to generate high-quality content for your website.
Version: 1.0
Author: John Doe
Author URI: https://www.example.com/
License: GPL2
*/

// Register the plugin settings
require_once(plugin_dir_path(__FILE__) . 'settings.php');

// Register the plugin dashboard for each user
require_once(plugin_dir_path(__FILE__) . 'user-dashboard.php');

// Register the user analytics page
require_once(plugin_dir_path(__FILE__) . 'user-analytics.php');

// Register the search logs page
require_once(plugin_dir_path(__FILE__) . 'search-logs.php');

// Register the export data page
require_once(plugin_dir_path(__FILE__) . 'export-data.php');

// Register the custom notifications feature
require_once(plugin_dir_path(__FILE__) . 'custom-notifications.php');

// Register the multi-user support feature
require_once(plugin_dir_path(__FILE__) . 'multi-user-support.php');

// Register the API integration feature
require_once(plugin_dir_path(__FILE__) . 'api-integration.php');

// Register the prompt messages customization feature
require_once(plugin_dir_path(__FILE__) . 'prompt-messages.php');

// Register the logging functionality
require_once(plugin_dir_path(__FILE__) . 'logs.php');

/**
 * Initializes the plugin.
 */
function openai_plugin_init()
{
    // Initialize logging functionality
    openai_logs_init();
}
add_action('plugins_loaded', 'openai_plugin_init');