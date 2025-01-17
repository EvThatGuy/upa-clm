<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Get all teams.
 *
 * @return array List of teams.
 */
function clm_get_teams() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'teams';
    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY name ASC", ARRAY_A);
}

/**
 * Add a new team.
 *
 * @param string $name The name of the team.
 * @param string $logo_url The URL of the team's logo.
 * @return int|false The inserted team ID or false on failure.
 */
function clm_add_team($name, $logo_url = null) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'teams';

    $result = $wpdb->insert($table_name, [
        'name' => sanitize_text_field($name),
        'logo_url' => esc_url_raw($logo_url),
    ], ['%s', '%s']);

    return $result ? $wpdb->insert_id : false;
}

/**
 * Update a team's data.
 *
 * @param int $team_id The ID of the team.
 * @param array $data Associative array of data to update.
 * @return bool True on success, false on failure.
 */
function clm_update_team($team_id, $data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'teams';

    $data['updated_at'] = current_time('mysql');
    return (bool) $wpdb->update($table_name, $data, ['id' => $team_id], null, ['%d']);
}

/**
 * Delete a team.
 *
 * @param int $team_id The ID of the team.
 * @return bool True on success, false on failure.
 */
function clm_delete_team($team_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'teams';
    return (bool) $wpdb->delete($table_name, ['id' => $team_id], ['%d']);
}
?>