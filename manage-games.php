<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// This file should not redeclare clm_manage_games_page or other functions declared in the main plugin file
// Ensure this file only contains the logic specific to managing games without redeclaring functions

global $wpdb;
$games_table = $wpdb->prefix . 'games';
$teams_table = $wpdb->prefix . 'teams';

// Handle form submission for adding a game
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_add_game_nonce']) && wp_verify_nonce($_POST['clm_add_game_nonce'], 'clm_add_game')) {
    $team1_id = intval($_POST['team1_id']);
    $team2_id = intval($_POST['team2_id']);
    $team1_score = floatval($_POST['team1_score']);
    $team2_score = floatval($_POST['team2_score']);

    $team1_players = array_map(function($player_id, $position) {
        return ['player_id' => intval($player_id), 'position' => sanitize_text_field($position)];
    }, $_POST['team1_player_id'], $_POST['team1_position']);

    $team2_players = array_map(function($player_id, $position) {
        return ['player_id' => intval($player_id), 'position' => sanitize_text_field($position)];
    }, $_POST['team2_player_id'], $_POST['team2_position']);

    $game_data = [
        'team1_id' => $team1_id,
        'team2_id' => $team2_id,
        'score1' => $team1_score,
        'score2' => $team2_score,
        'team1_players' => $team1_players,
        'team2_players' => $team2_players
    ];

    if (!function_exists('clm_add_game')) {
        function clm_add_game($game_data) {
            // Implementation of clm_add_game function
        }
    }

    $game_id = clm_add_game($game_data);

    if ($game_id) {
        // Add player stats for team 1
        foreach ($team1_players as $player) {
            clm_add_player_stats_for_game($game_id, $player['player_id'], $_POST['team1_stats'][$player['player_id']]);
        }

        // Add player stats for team 2
        foreach ($team2_players as $player) {
            clm_add_player_stats_for_game($game_id, $player['player_id'], $_POST['team2_stats'][$player['player_id']]);
        }

        echo '<div class="updated"><p>Game added successfully!</p></div>';
    } else {
        echo '<div class="error"><p>There was an error adding the game. Please check your input.</p></div>';
    }
}

$games = $wpdb->get_results("
    SELECT g.*, t1.name AS team1_name, t2.name AS team2_name
    FROM $games_table g
    JOIN $teams_table t1 ON g.team1_id = t1.id
    JOIN $teams_table t2 ON g.team2_id = t2.id
    ORDER BY g.created_at DESC
");
$teams = $wpdb->get_results("SELECT id, name FROM $teams_table");

?>
<div class="wrap">
    <h1>Manage Games</h1>
    <form method="POST">
        <?php wp_nonce_field('clm_add_game', 'clm_add_game_nonce'); ?>
        <input type="hidden" name="action" value="add_game">
        <h2>Select Teams</h2>
        <label for="team1_id">Team 1:</label>
        <select name="team1_id" id="team1_id" required>
            <option value="">Select Team 1</option>
            <?php foreach ($teams as $team): ?>
                <option value="<?php echo esc_attr($team->id); ?>"><?php echo esc_html($team->name); ?></option>
            <?php endforeach; ?>
        </select>
        <label for="team2_id">Team 2:</label>
        <select name="team2_id" id="team2_id" required>
            <option value="">Select Team 2</option>
            <?php foreach ($teams as $team): ?>
                <option value="<?php echo esc_attr($team->id); ?>"><?php echo esc_html($team->name); ?></option>
            <?php endforeach; ?>
        </select>
        <h2>Score</h2>
        <label for="score1">Score Team 1:</label>
        <input type="number" name="team1_score" id="score1" required step="0.1">
        <label for="score2">Score Team 2:</label>
        <input type="number" name="team2_score" id="score2" required step="0.1">
        <h2>Team 1 Players</h2>
        <div id="team1-players"></div>
        <h2>Team 2 Players</h2>
        <div id="team2-players"></div>
        <button type="submit" class="button-primary">Add Game</button>
    </form>

    <table class="widefat fixed">
        <thead>
            <tr>
                <th>ID</th>
                <th>Team 1</th>
                <th>Team 2</th>
                <th>Scores</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($games as $game): ?>
                <tr>
                    <td><?php echo esc_html($game->id); ?></td>
                    <td><?php echo esc_html($game->team1_name); ?></td>
                    <td><?php echo esc_html($game->team2_name); ?></td>
                    <td><?php echo esc_html($game->score1); ?> - <?php echo esc_html($game->score2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const team1Select = document.getElementById('team1_id');
        const team2Select = document.getElementById('team2_id');
        const team1PlayersDiv = document.getElementById('team1-players');
        const team2PlayersDiv = document.getElementById('team2-players');

        function fetchTeamPlayers(teamId, callback) {
            fetch(ajaxurl + '?action=clm_get_team_players&team_id=' + teamId)
                .then(response => response.json())
                .then(data => callback(data))
                .catch(error => console.error('Error:', error));
        }

        function populatePlayers(teamId, playersDiv, teamIndex) {
            playersDiv.innerHTML = '';
            fetchTeamPlayers(teamId, function(players) {
                players.forEach((player, index) => {
                    const playerDiv = document.createElement('div');
                    playerDiv.className = 'player';

                    const onlineIdSpan = document.createElement('span');
                    onlineIdSpan.textContent = player.online_id;

                    const positionSelect = document.createElement('select');
                    positionSelect.name = `team${teamIndex}_position[${player.id}]`;
                    ['PG', 'SG', 'SF', 'PF', 'C'].forEach(position => {
                        const option = document.createElement('option');
                        option.value = position;
                        option.textContent = position;
                        positionSelect.appendChild(option);
                    });

                    const statsDiv = document.createElement('div');
                    statsDiv.className = 'stats';

                    ['pts', 'reb', 'ast', 'stl', 'blk', 'fouls', 'turnovers', 'fgm', 'fga', 'tpm', 'tpa', 'ftm', 'fta'].forEach(stat => {
                        const input = document.createElement('input');
                        input.type = 'number';
                        input.name = `team${teamIndex}_stats[${player.id}][${stat}]`;
                        input.placeholder = stat.toUpperCase();
                        statsDiv.appendChild(input);
                    });

                    playerDiv.appendChild(onlineIdSpan);
                    playerDiv.appendChild(positionSelect);
                    playerDiv.appendChild(statsDiv);
                    playersDiv.appendChild(playerDiv);
                });
            });
        }

        team1Select.addEventListener('change', function() {
            populatePlayers(this.value, team1PlayersDiv, 1);
        });

        team2Select.addEventListener('change', function() {
            populatePlayers(this.value, team2PlayersDiv, 2);
        });
    });
</script>
<?php
?><?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function clm_manage_games_page() {
    global $wpdb;
    $games_table = $wpdb->prefix . 'games';
    $teams_table = $wpdb->prefix . 'teams';

    // Handle form submission for deleting a game
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_delete_game_nonce']) && wp_verify_nonce($_POST['clm_delete_game_nonce'], 'clm_delete_game')) {
        $game_id = intval($_POST['game_id']);
        if (!empty($game_id)) {
            clm_delete_game($game_id);
            echo '<div class="updated"><p>Game deleted successfully!</p></div>';
            // Redirect to avoid resubmission
            wp_redirect(admin_url('admin.php?page=clm_manage_games'));
            exit;
        } else {
            echo '<div class="error"><p>Invalid game ID.</p></div>';
        }
    }

    // Fetch games
    $games = clm_get_games();
    ?>
    <div class="wrap">
        <h1>Manage Games</h1>
        <a href="<?php echo admin_url('admin.php?page=clm_add_game'); ?>" class="button-primary">Add Game</a>
        <table class="widefat fixed" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Team 1</th>
                    <th>Team 2</th>
                    <th>Date</th>
                    <th>Score 1</th>
                    <th>Score 2</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><?php echo esc_html($game['id']); ?></td>
                        <td><?php echo esc_html($game['title']); ?></td>
                        <td><?php echo esc_html($game['team1_name']); ?></td>
                        <td><?php echo esc_html($game['team2_name']); ?></td>
                        <td><?php echo esc_html($game['date']); ?></td>
                        <td><?php echo esc_html($game['score1']); ?></td>
                        <td><?php echo esc_html($game['score2']); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=clm_edit_game&game_id=' . intval($game['id'])); ?>" class="button-link-edit">Edit</a>
                            <form method="POST" style="display:inline;">
                                <?php wp_nonce_field('clm_delete_game', 'clm_delete_game_nonce'); ?>
                                <input type="hidden" name="game_id" value="<?php echo intval($game['id']); ?>">
                                <button type="submit" class="button-link-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>