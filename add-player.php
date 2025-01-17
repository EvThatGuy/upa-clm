<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function clm_manage_players_page() {
    global $wpdb;
    $teams_table = $wpdb->prefix . 'teams';
    $players_table = $wpdb->prefix . 'players';

    // Handle form submission for deleting a player
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_delete_player_nonce']) && wp_verify_nonce($_POST['clm_delete_player_nonce'], 'clm_delete_player')) {
        $player_id = intval($_POST['player_id']);
        if (!empty($player_id)) {
            clm_delete_player($player_id);
            echo '<div class="updated"><p>Player deleted successfully!</p></div>';
            // Redirect to avoid resubmission
            wp_redirect(admin_url('admin.php?page=clm_manage_players'));
            exit;
        } else {
            echo '<div class="error"><p>Invalid player ID.</p></div>';
        }
    }

    // Fetch all players
    $players = clm_get_all_players();

    ?>
    <div class="wrap">
        <h1>Manage Players</h1>
        <a href="<?php echo admin_url('admin.php?page=clm_add_player'); ?>" class="button-primary">Add Player</a>
        <table class="widefat fixed" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Team</th>
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
                        <td><?php echo esc_html($wpdb->get_var($wpdb->prepare("SELECT name FROM $teams_table WHERE id = %d", $player['team_id']))); ?></td>
                        <td><?php echo esc_html($player['online_id']); ?></td>
                        <td><?php echo esc_html($player['position']); ?></td>
                        <td><?php echo esc_html($player['discord_username']); ?></td>
                        <td><?php echo esc_html($player['twitter_handle']); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=clm_edit_player&player_id=' . intval($player['id'])); ?>" class="button-link-edit">Edit</a>
                            <form method="POST" style="display:inline;">
                                <?php wp_nonce_field('clm_delete_player', 'clm_delete_player_nonce'); ?>
                                <input type="hidden" name="player_id" value="<?php echo intval($player['id']); ?>">
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