<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Get all games.
 *
 * @return array List of games.
 */
function clm_get_games() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'games';
    $query = "
        SELECT g.id, t1.name AS team1_name, t2.name AS team2_name, g.date
        FROM $table_name g
        LEFT JOIN {$wpdb->prefix}teams t1 ON g.team1_id = t1.id
        LEFT JOIN {$wpdb->prefix}teams t2 ON g.team2_id = t2.id
        ORDER BY g.date DESC
    ";
    return $wpdb->get_results($query, ARRAY_A);
}

/**
 * Add a new game.
 *
 * @param int $team1_id The ID of team 1.
 * @param int $team2_id The ID of team 2.
 * @param string $date The date of the game.
 * @return int|false The inserted game ID or false on failure.
 */
function clm_add_game($team1_id, $team2_id, $date) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'games';

    $result = $wpdb->insert($table_name, [
        'team1_id' => intval($team1_id),
        'team2_id' => intval($team2_id),
        'date' => sanitize_text_field($date),
    ], ['%d', '%d', '%s']);

    return $result ? $wpdb->insert_id : false;
}

/**
 * Update a game's data.
 *
 * @param int $game_id The ID of the game.
 * @param array $data Associative array of data to update.
 * @return bool True on success, false on failure.
 */
function clm_update_game($game_id, $data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'games';

    $data['updated_at'] = current_time('mysql');
    return (bool) $wpdb->update($table_name, $data, ['id' => $game_id], null, ['%d']);
}

/**
 * Delete a game.
 *
 * @param int $game_id The ID of the game.
 * @return bool True on success, false on failure.
 */
function clm_delete_game($game_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'games';
    return (bool) $wpdb->delete($table_name, ['id' => $game_id], ['%d']);
}
?>