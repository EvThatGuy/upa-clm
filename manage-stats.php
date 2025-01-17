<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function clm_manage_stats_page() {
    global $wpdb;
    $players_table = $wpdb->prefix . 'players';
    $games_table = $wpdb->prefix . 'games';
    $stats_table = $wpdb->prefix . 'player_stats';

    // Fetch players and games for the dropdown
    $players = $wpdb->get_results("SELECT id, online_id FROM $players_table");
    $games = $wpdb->get_results("SELECT id, CONCAT('Game ', id) AS game_name FROM $games_table");

    // Handle form submission for adding player stats
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_add_stats_nonce']) && wp_verify_nonce($_POST['clm_add_stats_nonce'], 'clm_add_stats')) {
        $player_id = intval($_POST['player_id']);
        $game_id = intval($_POST['game_id']);
        $gp = 1; // Each entry represents one game played
        $pts = intval($_POST['pts']);
        $reb = intval($_POST['reb']);
        $ast = intval($_POST['ast']);
        $stl = intval($_POST['stl']);
        $blk = intval($_POST['blk']);
        $fouls = intval($_POST['fouls']);
        $turnovers = intval($_POST['turnovers']);
        $fgm = intval($_POST['fgm']);
        $fga = intval($_POST['fga']);
        $tpm = intval($_POST['tpm']);
        $tpa = intval($_POST['tpa']);
        $ftm = intval($_POST['ftm']);
        $fta = intval($_POST['fta']);

        if ($player_id && $game_id) {
            clm_add_player_stats($player_id, $game_id, $gp, $pts, $reb, $ast, $stl, $blk, $fouls, $turnovers, $fgm, $fga, $tpm, $tpa, $ftm, $fta);
            echo '<div class="updated"><p>Player stats added successfully!</p></div>';
        } else {
            echo '<div class="error"><p>There was an error adding the stats. Please check your input.</p></div>';
        }
    }

    // Handle form submission for deleting player stats
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_delete_stats_nonce']) && wp_verify_nonce($_POST['clm_delete_stats_nonce'], 'clm_delete_stats')) {
        $stats_id = intval($_POST['stats_id']);
        if (!empty($stats_id)) {
            clm_delete_player_stats($stats_id);
            echo '<div class="updated"><p>Player stats deleted successfully!</p></div>';
            // Redirect to avoid resubmission
            wp_redirect(admin_url('admin.php?page=clm_manage_stats'));
            exit;
        } else {
            echo '<div class="error"><p>Invalid stats ID.</p></div>';
        }
    }

    // Fetch stats for display
    $selected_player_id = isset($_GET['player_id']) ? intval($_GET['player_id']) : 0;
    $stats = $selected_player_id ? clm_get_player_stats($selected_player_id) : [];

    ?>
    <div class="wrap">
        <h1>Manage Player Stats</h1>
        <form method="POST">
            <?php wp_nonce_field('clm_add_stats', 'clm_add_stats_nonce'); ?>
            <input type="hidden" name="action" value="add_stats">
            <select name="player_id" required>
                <option value="">Select Player</option>
                <?php foreach ($players as $player): ?>
                    <option value="<?php echo esc_attr($player->id); ?>"><?php echo esc_html($player->online_id); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="game_id" required>
                <option value="">Select Game</option>
                <?php foreach ($games as $game): ?>
                    <option value="<?php echo esc_attr($game->id); ?>"><?php echo esc_html($game->game_name); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="pts" placeholder="PTS (Points)" required>
            <input type="number" name="reb" placeholder="REB (Rebounds)">
            <input type="number" name="ast" placeholder="AST (Assists)">
            <input type="number" name="stl" placeholder="STL (Steals)">
            <input type="number" name="blk" placeholder="BLK (Blocks)">
            <input type="number" name="fouls" placeholder="FOULS (Fouls)">
            <input type="number" name="turnovers" placeholder="TO (Turnovers)">
            <input type="number" name="fgm" placeholder="FGM (Field Goals Made)">
            <input type="number" name="fga" placeholder="FGA (Field Goals Attempted)">
            <input type="number" name="tpm" placeholder="3PM (3 Points Made)">
            <input type="number" name="tpa" placeholder="3PA (3 Points Attempted)">
            <input type="number" name="ftm" placeholder="FTM (Free Throws Made)">
            <input type="number" name="fta" placeholder="FTA (Free Throws Attempted)">
            <button type="submit" class="button-primary">Add Stats</button>
        </form>

        <form method="GET" style="margin-top: 20px;">
            <input type="hidden" name="page" value="clm_manage_stats">
            <select name="player_id" onchange="this.form.submit()">
                <option value="">Select Player to View Stats</option>
                <?php foreach ($players as $player): ?>
                    <option value="<?php echo esc_attr($player->id); ?>" <?php selected($selected_player_id, $player->id); ?>><?php echo esc_html($player->online_id); ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($stats): ?>
            <table class="widefat fixed" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th>Game ID</th>
                        <th>GP</th>
                        <th>PTS</th>
                        <th>REB</th>
                        <th>AST</th>
                        <th>STL</th>
                        <th>BLK</th>
                        <th>FOULS</th>
                        <th>TO</th>
                        <th>FGM</th>
                        <th>FGA</th>
                        <th>3PM</th>
                        <th>3PA</th>
                        <th>FTM</th>
                        <th>FTA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $stat): ?>
                        <tr>
                            <td><?php echo esc_html($stat['game_id']); ?></td>
                            <td><?php echo esc_html($stat['gp']); ?></td>
                            <td><?php echo esc_html($stat['pts']); ?></td>
                            <td><?php echo esc_html($stat['reb']); ?></td>
                            <td><?php echo esc_html($stat['ast']); ?></td>
                            <td><?php echo esc_html($stat['stl']); ?></td>
                            <td><?php echo esc_html($stat['blk']); ?></td>
                            <td><?php echo esc_html($stat['fouls']); ?></td>
                            <td><?php echo esc_html($stat['turnovers']); ?></td>
                            <td><?php echo esc_html($stat['fgm']); ?></td>
                            <td><?php echo esc_html($stat['fga']); ?></td>
                            <td><?php echo esc_html($stat['tpm']); ?></td>
                            <td><?php echo esc_html($stat['tpa']); ?></td>
                            <td><?php echo esc_html($stat['ftm']); ?></td>
                            <td><?php echo esc_html($stat['fta']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <?php wp_nonce_field('clm_delete_stats', 'clm_delete_stats_nonce'); ?>
                                    <input type="hidden" name="stats_id" value="<?php echo intval($stat['id']); ?>">
                                    <button type="submit" class="button-link-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}
?>