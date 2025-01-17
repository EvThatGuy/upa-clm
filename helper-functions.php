<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Calculate total points for a team.
 *
 * @param int $team_id Team ID.
 * @return int Total points.
 */
function clm_calculate_team_points($team_id) {
    global $wpdb;
    $games_table = $wpdb->prefix . 'games';

    $total_points = $wpdb->get_var($wpdb->prepare("
        SELECT SUM(CASE WHEN team1_id = %d THEN team1_score ELSE 0 END) +
               SUM(CASE WHEN team2_id = %d THEN team2_score ELSE 0 END)
        FROM $games_table
        WHERE team1_id = %d OR team2_id = %d
    ", $team_id, $team_id, $team_id, $team_id));

    return $total_points ?: 0;
}

/**
 * Get team details by ID.
 *
 * @param int $team_id Team ID.
 * @return object|false Team details object or false on failure.
 */
function clm_get_team_details($team_id) {
    global $wpdb;
    $teams_table = $wpdb->prefix . 'teams';

    return $wpdb->get_row($wpdb->prepare("
        SELECT *
        FROM $teams_table
        WHERE id = %d
    ", $team_id));
}

/**
 * Get all teams.
 *
 * @return array List of teams.
 */
function clm_get_all_teams() {
    global $wpdb;
    $teams_table = $wpdb->prefix . 'teams';

    return $wpdb->get_results("
        SELECT *
        FROM $teams_table
        ORDER BY name ASC
    ");
}

/**
 * Get player details by ID.
 *
 * @param int $player_id Player ID.
 * @return object|false Player details object or false on failure.
 */
function clm_get_player_details($player_id) {
    global $wpdb;
    $players_table = $wpdb->prefix . 'players';

    return $wpdb->get_row($wpdb->prepare("
        SELECT *
        FROM $players_table
        WHERE id = %d
    ", $player_id));
}

/**
 * Get all games.
 *
 * @return array List of games.
 */
function clm_get_all_games() {
    global $wpdb;
    $games_table = $wpdb->prefix . 'games';
    $teams_table = $wpdb->prefix . 'teams';

    return $wpdb->get_results("
        SELECT g.*, t1.name AS team1_name, t2.name AS team2_name
        FROM $games_table g
        JOIN $teams_table t1 ON g.team1_id = t1.id
        JOIN $teams_table t2 ON g.team2_id = t2.id
        ORDER BY g.created_at DESC
    ");
}

/**
 * Get player stats by player ID.
 *
 * @param int $player_id Player ID.
 * @return array List of player stats.
 */
function clm_get_player_stats($player_id) {
    global $wpdb;
    $stats_table = $wpdb->prefix . 'player_stats';

    return $wpdb->get_results($wpdb->prepare("
        SELECT *
        FROM $stats_table
        WHERE player_id = %d
        ORDER BY created_at DESC
    ", $player_id));
}

/**
 * Get team standings.
 *
 * @return array List of team standings.
 */
function clm_get_team_standings() {
    global $wpdb;
    $teams_table = $wpdb->prefix . 'teams';
    $standings_table = $wpdb->prefix . 'standings';

    return $wpdb->get_results("
        SELECT s.*, t.name, t.logo_url
        FROM $standings_table s
        JOIN $teams_table t ON s.team_id = t.id
        ORDER BY s.points DESC, s.goal_difference DESC
    ");
}
?>