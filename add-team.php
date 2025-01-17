<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpdb;
$teams_table = $wpdb->prefix . 'teams';

// Handle form submission for adding a team
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clm_add_team_nonce']) && wp_verify_nonce($_POST['clm_add_team_nonce'], 'clm_add_team')) {
    $team_name = sanitize_text_field($_POST['team_name']);
    $abbreviation = sanitize_text_field($_POST['abbreviation']);
    $logo_url = esc_url_raw($_POST['logo_url']);
    $twitch_link = esc_url_raw($_POST['twitch_link']);

    if (!empty($team_name) && !empty($abbreviation) && filter_var($twitch_link, FILTER_VALIDATE_URL) && strpos($twitch_link, 'https://twitch.tv/') === 0) {
        clm_add_team($team_name, $abbreviation, $logo_url, $twitch_link);
        echo '<div class="updated"><p>Team added successfully!</p></div>';
        // Redirect to manage teams page
        wp_redirect(admin_url('admin.php?page=clm_manage_teams'));
        exit;
    } else {
        echo '<div class="error"><p>There was an error adding the team. Please check your input. The Twitch link must be a valid URL and begin with https://twitch.tv/.</p></div>';
    }
}
?>
<div class="wrap">
    <h1>Add Team</h1>
    <form method="POST">
        <?php wp_nonce_field('clm_add_team', 'clm_add_team_nonce'); ?>
        <input type="text" name="team_name" placeholder="Team Name" required>
        <input type="text" name="abbreviation" placeholder="Abbreviation" required>
        <input type="text" name="logo_url" placeholder="Logo URL">
        <input type="text" name="twitch_link" placeholder="Twitch Link (https://twitch.tv/username)" required>
        <button type="submit" class="button-primary">Add Team</button>
    </form>
</div>