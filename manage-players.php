<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// This file should not redeclare clm_manage_players_page or other functions declared in the main plugin file
// Ensure this file only contains the logic specific to managing players without redeclaring functions

global $wpdb;
$teams_table = $wpdb->prefix . 'teams';
$players_table = $wpdb->prefix . 'players';

// Fetch teams for the dropdown
$teams = $wpdb->get_results("SELECT id, name FROM $teams_table");

// Handle form submission for adding a player
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_add_player_nonce']) && wp_verify_nonce($_POST['clm_add_player_nonce'], 'clm_add_player')) {
    $team_id = intval($_POST['team_id']);
    $online_id = sanitize_text_field($_POST['online_id']);
    $position = sanitize_text_field($_POST['position']);
    $discord_username = sanitize_text_field($_POST['discord_username']);
    $twitter_handle = sanitize_text_field($_POST['twitter_handle']);
    
    if ($team_id && !empty($online_id) && !empty($discord_username)) {
        clm_add_player($team_id, $online_id, $position, $discord_username, $twitter_handle);
        echo '<div class="updated"><p>Player added successfully!</p></div>';
    } else {
        echo '<div class="error"><p>There was an error adding the player. Please check your input.</p></div>';
    }
}

// Fetch players for display
$selected_team_id = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0;
$players = $selected_team_id ? clm_get_players($selected_team_id) : [];

?>
<div class="wrap">
    <h1>Manage Players</h1>
    <form method="POST">
        <?php wp_nonce_field('clm_add_player', 'clm_add_player_nonce'); ?>
        <input type="hidden" name="action" value="add_player">
        <select name="team_id" required>
            <option value="">Select Team</option>
            <?php foreach ($teams as $team): ?>
                <option value="<?php echo esc_attr($team->id); ?>"><?php echo esc_html($team->name); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="online_id" placeholder="Online ID" required>
        <input type="text" name="position" placeholder="Position">
        <input type="text" name="discord_username" placeholder="Discord Username" required>
        <input type="text" name="twitter_handle" placeholder="Twitter Handle">
        <button type="submit" class="button-primary">Add Player</button>
    </form>

    <form method="GET" style="margin-top: 20px;">
        <input type="hidden" name="page" value="clm_manage_players">
        <select name="team_id" onchange="this.form.submit()">
            <option value="">Select Team to View Players</option>
            <?php foreach ($teams as $team): ?>
                <option value="<?php echo esc_attr($team->id); ?>" <?php selected($selected_team_id, $team->id); ?>><?php echo esc_html($team->name); ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($players): ?>
        <table class="widefat fixed" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Online ID</th>
                    <th>Position</th>
                    <th>Discord Username</th>
                    <th>Twitter Handle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player): ?>
                    <tr>
                        <td><?php echo esc_html($player['id']); ?></td>
                        <td><?php echo esc_html($player['online_id']); ?></td>
                        <td><?php echo esc_html($player['position']); ?></td>
                        <td><?php echo esc_html($player['discord_username']); ?></td>
                        <td><?php echo esc_html($player['twitter_handle']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <?php wp_nonce_field('clm_delete_player', 'clm_delete_player_nonce'); ?>
                                <input type="hidden" name="action" value="delete_player">
                                <input type="hidden" name="player_id" value="<?php echo intval($player['id']); ?>">
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
?>