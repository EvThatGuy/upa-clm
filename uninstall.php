<?php
// If uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Access the database via SQL
global $wpdb;

// Delete custom tables
$tables = [
    $wpdb->prefix . 'teams',
    $wpdb->prefix . 'games',
    $wpdb->prefix . 'player_stats',
    $wpdb->prefix . 'standings',
];

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS $table");
}

// Delete options if any were added
delete_option('clm_option_name');
delete_option('clm_another_option_name');

// Remove custom post types and taxonomies
$custom_post_types = ['team', 'game', 'player'];
foreach ($custom_post_types as $post_type) {
    // Get all posts of this custom post type
    $posts = get_posts([
        'post_type' => $post_type,
        'numberposts' => -1,
        'post_status' => 'any'
    ]);

    // Delete each post
    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }
}

// Remove custom user meta data
$meta_keys = ['clm_user_meta_key1', 'clm_user_meta_key2'];
$user_ids = get_users(['fields' => 'ID']);  // Get all user IDs

foreach ($user_ids as $user_id) {
    foreach ($meta_keys as $key) {
        delete_user_meta($user_id, $key);
    }
}

// Remove scheduled events
wp_clear_scheduled_hook('clm_custom_cron_event');

// Delete transients
$transients = ['clm_transient1', 'clm_transient2'];
foreach ($transients as $transient) {
    delete_transient($transient);
}

// Remove custom capabilities
$roles = ['administrator', 'editor', 'author', 'contributor', 'subscriber'];
$capabilities = ['clm_custom_capability1', 'clm_custom_capability2'];

foreach ($roles as $role_name) {
    $role = get_role($role_name);
    if ($role) {
        foreach ($capabilities as $capability) {
            $role->remove_cap($capability);
        }
    }
}

// Clear any cached data that has been added
wp_cache_flush();
?>