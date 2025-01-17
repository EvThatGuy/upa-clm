<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpdb;
$teams_table = $wpdb->prefix . 'teams';

// Fetch team data for editing
$team_id = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0;
$team = $wpdb->get_row($wpdb->prepare("SELECT * FROM $teams_table WHERE id = %d", $team_id));

if (!$team) {
    echo '<div class="error"><p>Invalid team ID.</p></div>';
    return;
}

// Handle form submission for updating a team
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_edit_team_nonce']) && wp_verify_nonce($_POST['clm_edit_team_nonce'], 'clm_edit_team')) {
    $team_name = sanitize_text_field($_POST['team_name']);
    $abbreviation = sanitize_text_field($_POST['abbreviation']);
    $logo_url = esc_url_raw($_POST['logo_url']);
    $twitch_link = esc_url_raw($_POST['twitch_link']);
    $total_points = intval($_POST['total_points']);

    if (!empty($team_name) && !empty($abbreviation) && filter_var($twitch_link, FILTER_VALIDATE_URL) && strpos($twitch_link, 'https://twitch.tv/') === 0) {
        clm_update_team($team_id, $team_name, $abbreviation, $logo_url, $twitch_link, $total_points);
        echo '<div class="updated"><p>Team updated successfully!</p></div>';
        // Redirect to manage teams page
        wp_redirect(admin_url('admin.php?page=clm_manage_teams'));
        exit;
    } else {
        echo '<div class="error"><p>There was an error updating the team. Please check your input. The Twitch link must be a valid URL and begin with https://twitch.tv/.</p></div>';
    }
}
?>
<div class="wrap">
    <h1>Edit Team</h1>
    <form method="POST">
        <?php wp_nonce_field('clm_edit_team', 'clm_edit_team_nonce'); ?>
        <input type="hidden" name="team_id" value="<?php echo esc_attr($team->id); ?>">
        <input type="text" name="team_name" placeholder="Team Name" value="<?php echo esc_attr($team->name); ?>" required>
        <input type="text" name="abbreviation" placeholder="Abbreviation" value="<?php echo esc_attr($team->abbreviation); ?>" required>
        <input type="text" name="logo_url" placeholder="Logo URL" value="<?php echo esc_attr($team->logo_url); ?>">
        <input type="text" name="twitch_link" placeholder="Twitch Link (https://twitch.tv/username)" value="<?php echo esc_attr($team->twitch_link); ?>" required>
        <input type="number" name="total_points" placeholder="Total Points" value="<?php echo esc_attr($team->total_points); ?>">
        <button type="submit" class="button-primary">Update Team</button>
    </form>
</div>