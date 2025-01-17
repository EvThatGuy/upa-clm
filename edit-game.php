<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function clm_edit_game_page() {
    global $wpdb;
    $games_table = $wpdb->prefix . 'games';
    $teams_table = $wpdb->prefix . 'teams';
    $players_table = $wpdb->prefix . 'players';
    $stats_table = $wpdb->prefix . 'player_stats';
    $game_id = intval($_GET['game_id']);

    // Fetch game data
    $game = $wpdb->get_row($wpdb->prepare("SELECT * FROM $games_table WHERE id = %d", $game_id), ARRAY_A);
    if (!$game) {
        echo '<div class="error"><p>Invalid game ID.</p></div>';
        return;
    }

    // Handle form submission for updating a game
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_edit_game_nonce']) && wp_verify_nonce($_POST['clm_edit_game_nonce'], 'clm_edit_game')) {
        $team1_id = intval($_POST['team1_id']);
        $team2_id = intval($_POST['team2_id']);
        $date = sanitize_text_field($_POST['date']);
        $score1 = intval($_POST['score1']);
        $score2 = intval($_POST['score2']);
        $team1_stats = $_POST['team1_stats'];
        $team2_stats = $_POST['team2_stats'];

        if (!empty($team1_id) && !empty($team2_id) && !empty($date)) {
            clm_update_game($game_id, [
                'team1_id' => $team1_id,
                'team2_id' => $team2_id,
                'date' => $date,
                'score1' => $score1,
                'score2' => $score2
            ], $team1_stats, $team2_stats);
            echo '<div class="updated"><p>Game updated successfully!</p></div>';
            // Redirect to avoid resubmission
            wp_redirect(admin_url('admin.php?page=clm_manage_games'));
            exit;
        } else {
            echo '<div class="error"><p>Please fill in all required fields.</p></div>';
        }
    }

    // Fetch teams
    $teams = $wpdb->get_results("SELECT id, name FROM $teams_table ORDER BY name ASC");

    // Fetch players and stats for both teams
    $team1_players = $wpdb->get_results($wpdb->prepare("SELECT p.id, p.online_id, ps.* FROM $players_table p LEFT JOIN $stats_table ps ON p.id = ps.player_id WHERE p.team_id = %d AND ps.game_id = %d", $game['team1_id'], $game_id), ARRAY_A);
    $team2_players = $wpdb->get_results($wpdb->prepare("SELECT p.id, p.online_id, ps.* FROM $players_table p LEFT JOIN $stats_table ps ON p.id = ps.player_id WHERE p.team_id = %d AND ps.game_id = %d", $game['team2_id'], $game_id), ARRAY_A);
    ?>
    <div class="wrap">
        <h1>Edit Game</h1>
        <form method="POST">
            <?php wp_nonce_field('clm_edit_game', 'clm_edit_game_nonce'); ?>
            <select name="team1_id" required>
                <option value="">Select Team 1</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?php echo intval($team->id); ?>" <?php selected($game['team1_id'], $team->id); ?>><?php echo esc_html($team->name); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="team2_id" required>
                <option value="">Select Team 2</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?php echo intval($team->id); ?>" <?php selected($game['team2_id'], $team->id); ?>><?php echo esc_html($team->name); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="date" value="<?php echo esc_attr($game['date']); ?>" required>
            <input type="number" name="score1" value="<?php echo esc_attr($game['score1']); ?>" placeholder="Score 1" required>
            <input type="number" name="score2" value="<?php echo esc_attr($game['score2']); ?>" placeholder="Score 2" required>

            <h3>Team 1 Players and Stats</h3>
            <div id="team1-players">
                <?php foreach ($team1_players as $player): ?>
                    <div>
                        <input type="hidden" name="team1_players[]" value="<?php echo intval($player['id']); ?>">
                        <span><?php echo esc_html($player['online_id']); ?></span>
                        <input type="text" name="team1_stats[<?php echo intval($player['id']); ?>][position]" value="<?php echo esc_attr($player['position']); ?>" placeholder="Position" required>
                        <input type="number" name="team1_stats[<?php echo intval($player['id']); ?>][pts]" value="<?php echo esc_attr($player['pts']); ?>" placeholder="Points" required>
                        <input type="number" name="team1_stats[<?php echo intval($player['id']); ?>][reb]" value="<?php echo esc_attr($player['reb']); ?>" placeholder="Rebounds" required>
                        <!-- Add more stats inputs as needed -->
                    </div>
                <?php endforeach; ?>
            </div>

            <h3>Team 2 Players and Stats</h3>
            <div id="team2-players">
                <?php foreach ($team2_players as $player): ?>
                    <div>
                        <input type="hidden" name="team2_players[]" value="<?php echo intval($player['id']); ?>">
                        <span><?php echo esc_html($player['online_id']); ?></span>
                        <input type="text" name="team2_stats[<?php echo intval($player['id']); ?>][position]" value="<?php echo esc_attr($player['position']); ?>" placeholder="Position" required>
                        <input type="number" name="team2_stats[<?php echo intval($player['id']); ?>][pts]" value="<?php echo esc_attr($player['pts']); ?>" placeholder="Points" required>
                        <input type="number" name="team2_stats[<?php echo intval($player['id']); ?>][reb]" value="<?php echo esc_attr($player['reb']); ?>" placeholder="Rebounds" required>
                        <!-- Add more stats inputs as needed -->
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="button-primary">Update Game</button>
        </form>
    </div>
    <?php
}
?>