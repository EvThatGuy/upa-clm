<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Get all statistics for a specific player.
 *
 * @param int $player_id The ID of the player.
 * @return array List of statistics.
 */
function clm_get_player_stats($player_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'player_stats';
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE player_id = %d ORDER BY created_at DESC", $player_id), ARRAY_A);
}

/**
 * Add new statistics for a player.
 *
 * @param int $player_id The ID of the player.
 * @param int $game_id The ID of the game.
 * @param int $gp Games played by the player.
 * @param int $pts Points scored by the player.
 * @param int $reb Rebounds made by the player.
 * @param int $ast Assists made by the player.
 * @param int $stl Steals made by the player.
 * @param int $blk Blocks made by the player.
 * @param int $fouls Fouls committed by the player.
 * @param int $turnovers Turnovers committed by the player.
 * @param int $fgm Field goals made by the player.
 * @param int $fga Field goals attempted by the player.
 * @param int $tpm Three-pointers made by the player.
 * @param int $tpa Three-pointers attempted by the player.
 * @param int $ftm Free throws made by the player.
 * @param int $fta Free throws attempted by the player.
 * @return int|false The inserted statistics ID or false on failure.
 */
function clm_add_player_stats($player_id, $game_id, $gp, $pts, $reb, $ast, $stl, $blk, $fouls, $turnovers, $fgm, $fga, $tpm, $tpa, $ftm, $fta) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'player_stats';

    $result = $wpdb->insert($table_name, [
        'player_id' => intval($player_id),
        'game_id' => intval($game_id),
        'gp' => intval($gp),
        'pts' => intval($pts),
        'reb' => intval($reb),
        'ast' => intval($ast),
        'stl' => intval($stl),
        'blk' => intval($blk),
        'fouls' => intval($fouls),
        'turnovers' => intval($turnovers),
        'fgm' => intval($fgm),
        'fga' => intval($fga),
        'tpm' => intval($tpm),
        'tpa' => intval($tpa),
        'ftm' => intval($ftm),
        'fta' => intval($fta),
    ], ['%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d']);

    return $result ? $wpdb->insert_id : false;
}

/**
 * Update a player's statistics.
 *
 * @param int $stats_id The ID of the statistics.
 * @param array $data Associative array of data to update.
 * @return bool True on success, false on failure.
 */
function clm_update_player_stats($stats_id, $data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'player_stats';

    $data['updated_at'] = current_time('mysql');
    return (bool) $wpdb->update($table_name, $data, ['id' => $stats_id], null, ['%d']);
}

/**
 * Delete a player's statistics.
 *
 * @param int $stats_id The ID of the statistics.
 * @return bool True on success, false on failure.
 */
function clm_delete_player_stats($stats_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'player_stats';
    return (bool) $wpdb->delete($table_name, ['id' => $stats_id], ['%d']);
}
?>