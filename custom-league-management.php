<?php
/*
Plugin Name: Custom League Management
Description: A plugin to manage teams, games, player statistics, and standings for a custom league.
Version: 1.0
Author: Unified Pro-Am Association
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Activation and Deactivation Hooks
register_activation_hook(__FILE__, 'clm_activate_plugin');
register_deactivation_hook(__FILE__, 'clm_deactivate_plugin');

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/database.php';
require_once plugin_dir_path(__FILE__) . 'includes/games.php';
require_once plugin_dir_path(__FILE__) . 'includes/standings.php';
require_once plugin_dir_path(__FILE__) . 'includes/players.php';
require_once plugin_dir_path(__FILE__) . 'includes/stats.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/helper-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/manage-teams.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/add-team.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/edit-team.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/manage-games.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/add-game.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/edit-game.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/manage-players.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/add-player.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/edit-player.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/manage-stats.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/manage-standings.php';

// Activation function to create custom database tables
function clm_activate_plugin() {
    create_custom_tables();
}

// Deactivation function to drop custom database tables
function clm_deactivate_plugin() {
    drop_custom_tables();
}

// Add Admin Menu
add_action('admin_menu', 'clm_add_admin_menu');
function clm_add_admin_menu() {
    add_menu_page(
        'League Management',       // Page title
        'League Management',       // Menu title
        'manage_options',          // Capability
        'clm_dashboard',           // Menu slug
        'clm_dashboard_page',      // Callback for main page
        'dashicons-awards',        // Menu icon
        6                          // Menu position
    );

    add_submenu_page(
        'clm_dashboard',
        'Manage Teams',
        'Teams',
        'manage_options',
        'clm_manage_teams',
        'clm_manage_teams_page'
    );

    add_submenu_page(
        'clm_dashboard',
        'Add Team',
        'Add Team',
        'manage_options',
        'clm_add_team',
        'clm_add_team_page'
    );

    add_submenu_page(
        null,
        'Edit Team',
        'Edit Team',
        'manage_options',
        'clm_edit_team',
        'clm_edit_team_page'
    );

    add_submenu_page(
        'clm_dashboard',
        'Manage Games',
        'Games',
        'manage_options',
        'clm_manage_games',
        'clm_manage_games_page'
    );

    add_submenu_page(
        'clm_dashboard',
        'Add Game',
        'Add Game',
        'manage_options',
        'clm_add_game',
        'clm_add_game_page'
    );

    add_submenu_page(
        null,
        'Edit Game',
        'Edit Game',
        'manage_options',
        'clm_edit_game',
        'clm_edit_game_page'
    );

    add_submenu_page(
        'clm_dashboard',
        'Manage Players',
        'Players',
        'manage_options',
        'clm_manage_players',
        'clm_manage_players_page'
    );

    add_submenu_page(
        'clm_dashboard',
        'Add Player',
        'Add Player',
        'manage_options',
        'clm_add_player',
        'clm_add_player_page'
    );

    add_submenu_page(
        null,
        'Edit Player',
        'Edit Player',
        'manage_options',
        'clm_edit_player',
        'clm_edit_player_page'
    );

    add_submenu_page(
        'clm_dashboard',
        'Manage Standings',
        'Standings',
        'manage_options',
        'clm_manage_standings',
        'clm_manage_standings_page'
    );

    add_submenu_page(
        'clm_dashboard',
        'Manage Player Stats',
        'Player Stats',
        'manage_options',
        'clm_manage_stats',
        'clm_manage_stats_page'
    );
}

// Admin Page Callbacks
function clm_dashboard_page() {
    echo '<h1>League Management Dashboard</h1>';
    echo '<p>Welcome to the League Management Plugin. Use the submenu items to manage your league.</p>';
}

// Include and display the manage teams page
function clm_manage_teams_page() {
    include plugin_dir_path(__FILE__) . 'includes/admin/manage-teams.php';
}

// Include and display the add team page
function clm_add_team_page() {
    include plugin_dir_path(__FILE__) . 'includes/admin/add-team.php';
}

// Include and display the edit team page
function clm_edit_team_page() {
    include plugin_dir_path(__FILE__) . 'includes/admin/edit-team.php';
}

// Include and display the manage games page
function clm_manage_games_page() {
    include plugin_dir_path(__FILE__) . 'includes/admin/manage-games.php';
}

// Include and display the add game page
function clm_add_game_page() {
    include plugin_dir_path(__FILE__) . 'includes/admin/add-game.php';
}

// Include and display the edit game page
function clm_edit_game_page() {
    include plugin_dir_path(__FILE__) . 'includes/admin/edit-game.php';
}

// Include and display the manage players page
function clm_manage_players_page() {
    include plugin_dir_path(__FILE__) . 'includes/admin/manage-players.php';
}

// Include and display the add player page
function clm_add_player_page() {
    include plugin_dir_path(__FILE__) . 'includes/admin/add-player.php';
}

// Include and display the edit player page
function clm_edit_player_page() {
    include plugin_dir_path(__FILE__) . 'includes/admin/edit-player.php';
}

// Include and display the manage standings page
function clm_manage_standings_page() {
    include plugin_dir_path(__FILE__) . 'includes/admin/manage-standings.php';
}

// Include and display the manage stats page
function clm_manage_stats_page() {
    include plugin_dir_path(__FILE__) . 'includes/admin/manage-stats.php';
}

// Enqueue Styles and Scripts for Admin Pages
add_action('admin_enqueue_scripts', 'clm_enqueue_admin_assets');
function clm_enqueue_admin_assets($hook_suffix) {
    if (strpos($hook_suffix, 'clm_') !== false) {
        wp_enqueue_style(
            'clm-admin-styles',
            plugin_dir_url(__FILE__) . 'assets/css/admin-styles.css',
            [],
            '1.0'
        );
        wp_enqueue_script(
            'clm-admin-scripts',
            plugin_dir_url(__FILE__) . 'assets/js/admin-scripts.js',
            ['jquery'],
            '1.0',
            true
        );
    }
}

// Enqueue Styles for Front-end
add_action('wp_enqueue_scripts', 'clm_enqueue_frontend_assets');
function clm_enqueue_frontend_assets() {
    wp_enqueue_style(
        'clm-frontend-styles',
        plugin_dir_url(__FILE__) . 'assets/css/style.css',
        [],
        '1.0'
    );
}

// AJAX handler to fetch team players
add_action('wp_ajax_clm_get_team_players', 'clm_ajax_get_team_players');
function clm_ajax_get_team_players() {
    if (!isset($_GET['team_id']) || !is_numeric($_GET['team_id'])) {
        wp_send_json_error(['message' => 'Invalid team ID']);
    }
    $team_id = intval($_GET['team_id']);
    $players = clm_get_players($team_id);
    wp_send_json_success($players);
}
?>