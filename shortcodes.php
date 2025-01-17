<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Display Teams Shortcode
function clm_display_teams() {
    global $wpdb;
    $teams_table = $wpdb->prefix . 'teams';
    $teams = $wpdb->get_results("SELECT * FROM $teams_table ORDER BY name ASC");

    ob_start();
    ?>
    <div class="clm-teams">
        <h2>Teams</h2>
        <ul>
            <?php foreach ($teams as $team) : ?>
                <li>
                    <strong><?php echo esc_html($team->name); ?></strong>
                    <?php if ($team->logo_url) : ?>
                        <img src="<?php echo esc_url($team->logo_url); ?>" alt="<?php echo esc_attr($team->name); ?>" class="team-logo" />
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <style>
        .clm-teams ul {
            list-style-type: none;
            padding: 0;
        }
        .clm-teams li {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .clm-teams .team-logo {
            margin-left: 10px;
            width: 30px;
            height: 30px;
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('clm_teams', 'clm_display_teams');

// Display Games Shortcode
function clm_display_games() {
    global $wpdb;
    $games_table = $wpdb->prefix . 'games';
    $teams_table = $wpdb->prefix . 'teams';
    $games = $wpdb->get_results("
        SELECT g.*, t1.name AS team1_name, t2.name AS team2_name
        FROM $games_table g
        JOIN $teams_table t1 ON g.team1_id = t1.id
        JOIN $teams_table t2 ON g.team2_id = t2.id
        ORDER BY g.created_at DESC
    ");

    ob_start();
    ?>
    <div class="clm-games">
        <h2>Games</h2>
        <ul>
            <?php foreach ($games as $game) : ?>
                <li>
                    <strong><?php echo esc_html($game->team1_name); ?></strong> vs <strong><?php echo esc_html($game->team2_name); ?></strong>
                    <br>
                    Score: <?php echo esc_html($game->score1); ?> - <?php echo esc_html($game->score2); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <style>
        .clm-games ul {
            list-style-type: none;
            padding: 0;
        }
        .clm-games li {
            margin-bottom: 10px;
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('clm_games', 'clm_display_games');

// Display Standings Shortcode
function clm_display_standings() {
    global $wpdb;
    $teams_table = $wpdb->prefix . 'teams';
    $standings_table = $wpdb->prefix . 'standings';
    $standings = $wpdb->get_results("
        SELECT s.*, t.name, t.logo_url
        FROM $standings_table s
        JOIN $teams_table t ON s.team_id = t.id
        ORDER BY s.points DESC, s.goal_difference DESC
    ");

    ob_start();
    ?>
    <div class="clm-standings">
        <h2>Standings</h2>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Team</th>
                    <th>Played</th>
                    <th>Won</th>
                    <th>Drawn</th>
                    <th>Lost</th>
                    <th>Goals For</th>
                    <th>Goals Against</th>
                    <th>Goal Difference</th>
                    <th>Points</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($standings as $index => $standing) : ?>
                    <tr>
                        <td><?php echo esc_html($index + 1); ?></td>
                        <td>
                            <?php if ($standing->logo_url) : ?>
                                <img src="<?php echo esc_url($standing->logo_url); ?>" alt="<?php echo esc_attr($standing->name); ?>" class="team-logo" />
                            <?php endif; ?>
                            <?php echo esc_html($standing->name); ?>
                        </td>
                        <td><?php echo esc_html($standing->played); ?></td>
                        <td><?php echo esc_html($standing->won); ?></td>
                        <td><?php echo esc_html($standing->drawn); ?></td>
                        <td><?php echo esc_html($standing->lost); ?></td>
                        <td><?php echo esc_html($standing->goals_for); ?></td>
                        <td><?php echo esc_html($standing->goals_against); ?></td>
                        <td><?php echo esc_html($standing->goal_difference); ?></td>
                        <td><?php echo esc_html($standing->points); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <style>
        .clm-standings table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .clm-standings th, .clm-standings td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .clm-standings th {
            background-color: #f2f2f2;
        }
        .clm-standings .team-logo {
            margin-right: 10px;
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('clm_standings', 'clm_display_standings');

// Display Player Statistics Shortcode
function clm_display_player_stats($atts) {
    global $wpdb;
    $atts = shortcode_atts(array(
        'player_id' => 0,
    ), $atts, 'clm_player_stats');

    $player_id = intval($atts['player_id']);
    if ($player_id === 0) {
        return '<p>Invalid player ID.</p>';
    }

    $stats_table = $wpdb->prefix . 'player_stats';
    $player_stats = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $stats_table WHERE player_id = %d ORDER BY created_at DESC",
        $player_id
    ));

    if (empty($player_stats)) {
        return '<p>No stats available for this player.</p>';
    }

    ob_start();
    ?>
    <div class="clm-player-stats">
        <h2>Player Stats</h2>
        <table>
            <thead>
                <tr>
                    <th>Game</th>
                    <th>Points</th>
                    <th>Rebounds</th>
                    <th>Assists</th>
                    <th>Steals</th>
                    <th>Blocks</th>
                    <th>Fouls</th>
                    <th>Turnovers</th>
                    <th>FGM</th>
                    <th>FGA</th>
                    <th>TPM</th>
                    <th>TPA</th>
                    <th>FTM</th>
                    <th>FTA</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($player_stats as $stats) : ?>
                    <tr>
                        <td><?php echo esc_html($stats->game_id); ?></td>
                        <td><?php echo esc_html($stats->pts); ?></td>
                        <td><?php echo esc_html($stats->reb); ?></td>
                        <td><?php echo esc_html($stats->ast); ?></td>
                        <td><?php echo esc_html($stats->stl); ?></td>
                        <td><?php echo esc_html($stats->blk); ?></td>
                        <td><?php echo esc_html($stats->fouls); ?></td>
                        <td><?php echo esc_html($stats->turnovers); ?></td>
                        <td><?php echo esc_html($stats->fgm); ?></td>
                        <td><?php echo esc_html($stats->fga); ?></td>
                        <td><?php echo esc_html($stats->tpm); ?></td>
                        <td><?php echo esc_html($stats->tpa); ?></td>
                        <td><?php echo esc_html($stats->ftm); ?></td>
                        <td><?php echo esc_html($stats->fta); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <style>
        .clm-player-stats table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .clm-player-stats th, .clm-player-stats td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .clm-player-stats th {
            background-color: #f2f2f2;
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('clm_player_stats', 'clm_display_player_stats');

?>