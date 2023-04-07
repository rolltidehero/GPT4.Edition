<?php
/**
 * Handles logging and retrieval of logs, including storage and display.
 *
 * @since 1.0.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Add custom database table for storing plugin logs.
 *
 * @since 1.0.0
 */
function aigp_create_logs_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'aigp_logs';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      user_id bigint(20) NOT NULL,
      log_text text NOT NULL,
      log_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'aigp_create_logs_table');

/**
 * Log an event in the plugin logs.
 *
 * @param string $text The text to log.
 * @param int $user_id The ID of the user performing the action.
 * @since 1.0.0
 */
function aigp_log_event($text, $user_id)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'aigp_logs';

    $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'log_text' => $text,
            'log_time' => current_time('mysql'),
        )
    );
}

/**
 * Retrieve plugin logs from the database.
 *
 * @param int $limit The maximum number of logs to retrieve.
 * @param int $user_id The ID of the user whose logs to retrieve (optional).
 * @param string $search The search term to use when filtering logs (optional).
 * @since 1.0.0
 */
function aigp_get_logs($limit = 10, $user_id = null, $search = null)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'aigp_logs';

    $query = "SELECT * FROM $table_name";

    if ($user_id) {
        $query .= " WHERE user_id = $user_id";
    }

    if ($search) {
        $query .= " AND log_text LIKE '%$search%'";
    }

    $query .= " ORDER BY log_time DESC LIMIT $limit";

    $results = $wpdb->get_results($query);

    return $results;
}

/**
 * Export plugin logs in CSV format.
 *
 * @since 1.0.0
 */
function aigp_export_logs()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'aigp_logs';

    $logs = $wpdb->get_results("SELECT * FROM $table_name");

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="aigp_logs.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, array('User ID', 'Log Text', 'Log Time'));

    foreach ($logs as $log) {
        fputcsv($output, array($log->user_id, $log->log_text, $log->log_time));
    }

    fclose($output);
}

/**