<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpdb;
$teams_table = $wpdb->prefix . 'teams';

// Handle form submission for deleting a team
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_delete_team_nonce']) && wp_verify_nonce($_POST['clm_delete_team_nonce'], 'clm_delete_team')) {
    if (!empty($_POST['team_id'])) {
        $team_id = intval($_POST['team_id']);
        $wpdb->delete($teams_table, ['id' => $team_id]);
        echo '<div class="updated"><p>Team deleted successfully!</p></div>';
        // Redirect to avoid resubmission
        wp_redirect(admin_url('admin.php?page=clm_manage_teams'));
        exit;
    } else {
        echo '<div class="error"><p>Invalid team ID.</p></div>';
    }
}

// Fetch teams
$teams = $wpdb->get_results("SELECT * FROM $teams_table ORDER BY name ASC");
?>
<div class="wrap">
    <h1>Manage Teams</h1>
    <a href="<?php echo admin_url('admin.php?page=clm_add_team'); ?>" class="button-primary">Add Team</a>

    <table class="widefat fixed" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Team Name</th>
                <th>Abbreviation</th>
                <th>Logo</th>
                <th>Twitch Link</th>
                <th>Total Points</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teams as $team): ?>
                <tr>
                    <td><?php echo esc_html($team->id); ?></td>
                    <td><?php echo esc_html($team->name); ?></td>
                    <td><?php echo esc_html($team->abbreviation); ?></td>
                    <td><?php if ($team->logo_url): ?>
                            <img src="<?php echo esc_url($team->logo_url); ?>" alt="<?php echo esc_attr($team->name); ?>" style="width:50px;height:50px;">
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html($team->twitch_link); ?></td>
                    <td><?php echo esc_html($team->total_points); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <?php wp_nonce_field('clm_delete_team', 'clm_delete_team_nonce'); ?>
                            <input type="hidden" name="team_id" value="<?php echo intval($team->id); ?>">
                            <button type="submit" class="button-link-delete">Delete</button>
                        </form>
                        <a href="<?php echo admin_url('admin.php?page=clm_edit_team&team_id=' . intval($team->id)); ?>" class="button-link-edit">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>