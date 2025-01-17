<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Calculate points from games for a specific team.
 *
 * @param int $team_id Team ID.
 * @return float Points earned from games.
 */
function clm_calculate_points_from_games($team_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'games';
    $points = 0;

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT team1_id, team2_id, points1, points2 FROM $table_name WHERE team1_id = %d OR team2_id = %d",
        $team_id,
        $team_id
    ));

    foreach ($results as $game) {
        if ($game->team1_id == $team_id) {
            $points += floatval($game->points1);
        } elseif ($game->team2_id == $team_id) {
            $points += floatval($game->points2);
        }
    }

    return $points;
}

/**
 * Calculate wins and losses from games for a specific team.
 *
 * @param int $team_id Team ID.
 * @return array Wins and losses.
 */
function clm_calculate_wins_losses_from_games($team_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'games';
    $wins = 0;
    $losses = 0;

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT team1_id, team2_id, team1_score, team2_score FROM $table_name WHERE team1_id = %d OR team2_id = %d",
        $team_id,
        $team_id
    ));

    foreach ($results as $game) {
        if ($game->team1_id == $team_id) {
            if ($game->team1_score > $game->team2_score) {
                $wins++;
            } elseif ($game->team1_score < $game->team2_score) {
                $losses++;
            }
        } elseif ($game->team2_id == $team_id) {
            if ($game->team2_score > $game->team1_score) {
                $wins++;
            } elseif ($game->team2_score < $game->team1_score) {
                $losses++;
            }
        }
    }

    return ['wins' => $wins, 'losses' => $losses];
}
?>