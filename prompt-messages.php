<?php
// Register prompt messages settings
function ai_content_generator_pro_prompt_messages_settings()
{
    register_setting('ai_content_generator_pro_prompt_messages', 'ai_content_generator_pro_prompt_messages');
}
add_action('admin_init', 'ai_content_generator_pro_prompt_messages_settings');

// Add a submenu page for customizing prompt messages
function ai_content_generator_pro_prompt_messages_menu()
{
    add_submenu_page(
        'ai-content-generator-pro-settings',
        'AI Content Generator Pro Prompt Messages',
        'Prompt Messages',
        'manage_options',
        'ai-content-generator-pro-prompt-messages',
        'ai_content_generator_pro_prompt_messages_page'
    );
}
add_action('admin_menu', 'ai_content_generator_pro_prompt_messages_menu');

// Display the prompt messages customization page
function ai_content_generator_pro_prompt_messages_page()
{
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle form submission
    if (isset($_POST['ai_content_generator_pro_prompt_messages'])) {
        update_option('ai_content_generator_pro_prompt_messages', $_POST['ai_content_generator_pro_prompt_messages']);
        echo '<div class="notice notice-success is-dismissible"><p>Prompt messages updated successfully!</p></div>';
    }

    // Retrieve current prompt messages
    $prompt_messages = get_option('ai_content_generator_pro_prompt_messages', array());
    ?>
            <div class="wrap">
                <h1>AI Content Generator Pro Prompt Messages</h1>
                <form method="post">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="ai_content_generator_pro_prompt_messages">Custom Prompt Messages:</label></th>
                                <td>
                                    <textarea name="ai_content_generator_pro_prompt_messages" rows="10" cols="50" class="large-text code"><?= esc_textarea(implode("\n", $prompt_messages)); ?></textarea>
                                    <p class="description">Enter one prompt message per line. These custom messages will be used when generating content.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
}