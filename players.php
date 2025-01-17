<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Get all players for a specific team.
 *
 * @param int $team_id The ID of the team.
 * @return array List of players.
 */
function clm_get_players($team_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'players';
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE team_id = %d ORDER BY online_id ASC", $team_id), ARRAY_A);
}

/**
 * Get all players in the system.
 *
 * @return array List of all players.
 */
function clm_get_all_players() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'players';
    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY online_id ASC", ARRAY_A);
}

/**
 * Add a new player to a team.
 *
 * @param int $team_id The ID of the team.
 * @param string $online_id The online ID of the player.
 * @param string $position The position of the player.
 * @param string $discord_username The Discord username of the player.
 * @param string $twitter_handle The Twitter handle of the player.
 * @return int|false The inserted player ID or false on failure.
 */
function clm_add_player($team_id, $online_id, $position, $discord_username, $twitter_handle) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'players';

    $result = $wpdb->insert($table_name, [
        'team_id' => intval($team_id),
        'online_id' => sanitize_text_field($online_id),
        'position' => sanitize_text_field($position),
        'discord_username' => sanitize_text_field($discord_username),
        'twitter_handle' => sanitize_text_field($twitter_handle),
    ], ['%d', '%s', '%s', '%s', '%s']);

    return $result ? $wpdb->insert_id : false;
}

/**
 * Update a player's data.
 *
 * @param int $player_id The ID of the player.
 * @param array $data Associative array of data to update.
 * @return bool True on success, false on failure.
 */
function clm_update_player($player_id, $data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'players';

    $data['updated_at'] = current_time('mysql');
    return (bool) $wpdb->update($table_name, $data, ['id' => $player_id], null, ['%d']);
}

/**
 * Delete a player.
 *
 * @param int $player_id The ID of the player.
 * @return bool True on success, false on failure.
 */
function clm_delete_player($player_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'players';
    return (bool) $wpdb->delete($table_name, ['id' => $player_id], ['%d']);
}
?>