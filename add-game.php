<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function clm_add_game_page() {
    global $wpdb;
    $teams_table = $wpdb->prefix . 'teams';

    // Handle form submission for adding a game
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_add_game_nonce']) && wp_verify_nonce($_POST['clm_add_game_nonce'], 'clm_add_game')) {
        $team1_id = intval($_POST['team1_id']);
        $team2_id = intval($_POST['team2_id']);
        $date = sanitize_text_field($_POST['date']);
        $score1 = intval($_POST['score1']);
        $score2 = intval($_POST['score2']);
        $team1_players = array_map('intval', $_POST['team1_players']);
        $team2_players = array_map('intval', $_POST['team2_players']);
        $team1_stats = $_POST['team1_stats'];
        $team2_stats = $_POST['team2_stats'];

        if (!empty($team1_id) && !empty($team2_id) && !empty($date)) {
            clm_add_game($team1_id, $team2_id, $date, $score1, $score2, $team1_players, $team2_players, $team1_stats, $team2_stats);
            echo '<div class="updated"><p>Game added successfully!</p></div>';
            // Redirect to avoid resubmission
            wp_redirect(admin_url('admin.php?page=clm_manage_games'));
            exit;
        } else {
            echo '<div class="error"><p>Please fill in all required fields.</p></div>';
        }
    }

    // Fetch teams
    $teams = $wpdb->get_results("SELECT id, name FROM $teams_table ORDER BY name ASC");
    ?>
    <div class="wrap">
        <h1>Add Game</h1>
        <form method="POST">
            <?php wp_nonce_field('clm_add_game', 'clm_add_game_nonce'); ?>
            <select name="team1_id" required>
                <option value="">Select Team 1</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?php echo intval($team->id); ?>"><?php echo esc_html($team->name); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="team2_id" required>
                <option value="">Select Team 2</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?php echo intval($team->id); ?>"><?php echo esc_html($team->name); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="date" required>
            <input type="number" name="score1" placeholder="Score 1" required>
            <input type="number" name="score2" placeholder="Score 2" required>
            <h3>Team 1 Players and Stats</h3>
            <div id="team1-players">
                <div>
                    <select name="team1_players[]" required>
                        <option value="">Select Player</option>
                        <!-- Add player options dynamically via JavaScript -->
                    </select>
                    <input type="text" name="team1_stats[][position]" placeholder="Position" required>
                    <input type="number" name="team1_stats[][pts]" placeholder="Points" required>
                    <input type="number" name="team1_stats[][reb]" placeholder="Rebounds" required>
                    <!-- Add more stats inputs as needed -->
                </div>
            </div>
            <button type="button" onclick="addTeam1Player()">Add Another Player</button>

            <h3>Team 2 Players and Stats</h3>
            <div id="team2-players">
                <div>
                    <select name="team2_players[]" required>
                        <option value="">Select Player</option>
                        <!-- Add player options dynamically via JavaScript -->
                    </select>
                    <input type="text" name="team2_stats[][position]" placeholder="Position" required>
                    <input type="number" name="team2_stats[][pts]" placeholder="Points" required>
                    <input type="number" name="team2_stats[][reb]" placeholder="Rebounds" required>
                    <!-- Add more stats inputs as needed -->
                </div>
            </div>
            <button type="button" onclick="addTeam2Player()">Add Another Player</button>

            <button type="submit" class="button-primary">Add Game</button>
        </form>
    </div>
    <script>
        function addTeam1Player() {
            const playerDiv = document.createElement('div');
            playerDiv.innerHTML = `
                <select name="team1_players[]" required>
                    <option value="">Select Player</option>
                    <!-- Add player options dynamically via JavaScript -->
                </select>
                <input type="text" name="team1_stats[][position]" placeholder="Position" required>
                <input type="number" name="team1_stats[][pts]" placeholder="Points" required>
                <input type="number" name="team1_stats[][reb]" placeholder="Rebounds" required>
                <!-- Add more stats inputs as needed -->
            `;
            document.getElementById('team1-players').appendChild(playerDiv);
        }

        function addTeam2Player() {
            const playerDiv = document.createElement('div');
            playerDiv.innerHTML = `
                <select name="team2_players[]" required>
                    <option value="">Select Player</option>
                    <!-- Add player options dynamically via JavaScript -->
                </select>
                <input type="text" name="team2_stats[][position]" placeholder="Position" required>
                <input type="number" name="team2_stats[][pts]" placeholder="Points" required>
                <input type="number" name="team2_stats[][reb]" placeholder="Rebounds" required>
                <!-- Add more stats inputs as needed -->
            `;
            document.getElementById('team2-players').appendChild(playerDiv);
        }
    </script>
    <?php
}
?>