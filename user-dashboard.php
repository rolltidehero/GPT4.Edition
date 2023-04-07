<?php
// Add a submenu page for the user dashboard
function ai_content_generator_pro_user_dashboard_menu()
{
    add_submenu_page(
        'ai-content-generator-pro-settings',
        'AI Content Generator Pro User Dashboard',
        'User Dashboard',
        'manage_options',
        'ai-content-generator-pro-user-dashboard',
        'ai_content_generator_pro_user_dashboard_page'
    );
}
add_action('admin_menu', 'ai_content_generator_pro_user_dashboard_menu');

// Display the user dashboard page
function ai_content_generator_pro_user_dashboard_page()
{
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Retrieve user data
    $user_id = get_current_user_id();
    $generated_content_count = ai_content_generator_pro_get_generated_content_count($user_id);
    $generated_comments_count = ai_content_generator_pro_get_generated_comments_count($user_id);
    $prompt_messages = get_option('ai_content_generator_pro_prompt_messages', array());
    ?>
                <div class="wrap">
                    <h1>AI Content Generator Pro User Dashboard</h1>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th>Generated Content</th>
                                <th>Generated Comments</th>
                                <th>Prompt Messages</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= $generated_content_count; ?></td>
                                <td><?= $generated_comments_count; ?></td>
                                <td><?= count($prompt_messages); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php
}

// Get the count of generated content for the user
function ai_content_generator_pro_get_generated_content_count($user_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_generated_content';
    $sql = $wpdb->prepare("SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d", $user_id);
    $count = $wpdb->get_var($sql);
    return $count;
}

// Get the count of generated comments for the user
function ai_content_generator_pro_get_generated_comments_count($user_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'ai_generated_comments';
    $sql = $wpdb->prepare("SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d", $user_id);
    $count = $wpdb->get_var($sql);
    return $count;
}