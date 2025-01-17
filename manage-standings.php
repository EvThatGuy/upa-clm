<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function clm_manage_standings_page() {
    $calculation_method = isset($_GET['method']) ? $_GET['method'] : 'points'; // Default to points

    global $wpdb;
    $teams_table = $wpdb->prefix . 'teams';
    $games_table = $wpdb->prefix . 'games';

    if ($calculation_method === 'wins_losses') {
        // Fetch standings data based on wins/losses
        $standings = $wpdb->get_results("
            SELECT t.id, t.name,
            COALESCE(SUM(CASE WHEN g.team1_id = t.id AND g.team1_score > g.team2_score THEN 1 ELSE 0 END), 0) AS wins,
            COALESCE(SUM(CASE WHEN g.team1_id = t.id AND g.team1_score < g.team2_score THEN 1 ELSE 0 END), 0) AS losses
            FROM $teams_table t
            LEFT JOIN $games_table g ON t.id = g.team1_id OR t.id = g.team2_id
            GROUP BY t.id, t.name
            ORDER BY wins DESC, losses ASC
        ");
    } else {
        // Fetch standings data based on points
        $standings = $wpdb->get_results("
            SELECT t.id, t.name,
            COALESCE(SUM(CASE WHEN g.team1_id = t.id THEN g.team1_score ELSE 0 END), 0) +
            COALESCE(SUM(CASE WHEN g.team2_id = t.id THEN g.team2_score ELSE 0 END), 0) AS total_points
            FROM $teams_table t
            LEFT JOIN $games_table g ON t.id = g.team1_id OR t.id = g.team2_id
            GROUP BY t.id, t.name
            ORDER BY total_points DESC
        ");
    }

    ?>
    <div class="wrap">
        <h1>League Standings</h1>
        <form method="GET" action="">
            <input type="hidden" name="page" value="clm_manage_standings">
            <select name="method" onchange="this.form.submit()">
                <option value="points" <?php selected($calculation_method, 'points'); ?>>Points</option>
                <option value="wins_losses" <?php selected($calculation_method, 'wins_losses'); ?>>Wins/Losses</option>
            </select>
        </form>
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Team</th>
                    <?php if ($calculation_method === 'wins_losses'): ?>
                        <th>Wins</th>
                        <th>Losses</th>
                    <?php else: ?>
                        <th>Points</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($standings as $index => $team): ?>
                    <tr>
                        <td><?php echo esc_html($index + 1); ?></td>
                        <td><?php echo esc_html($team->name); ?></td>
                        <?php if ($calculation_method === 'wins_losses'): ?>
                            <td><?php echo esc_html($team->wins); ?></td>
                            <td><?php echo esc_html($team->losses); ?></td>
                        <?php else: ?>
                            <td><?php echo esc_html($team->total_points); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Handle form submission for deleting a standing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_delete_standing_nonce']) && wp_verify_nonce($_POST['clm_delete_standing_nonce'], 'clm_delete_standing')) {
    $standing_id = intval($_POST['standing_id']);
    if (!empty($standing_id)) {
        clm_delete_standing($standing_id);
        echo '<div class="updated"><p>Standing deleted successfully!</p></div>';
        // Redirect to avoid resubmission
        wp_redirect(admin_url('admin.php?page=clm_manage_standings'));
        exit;
    } else {
        echo '<div class="error"><p>Invalid standing ID.</p></div>';
    }
}
?>