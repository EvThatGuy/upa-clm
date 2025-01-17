<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function create_custom_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Teams Table
    $teams_table = $wpdb->prefix . 'teams';
    $teams_sql = "CREATE TABLE $teams_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        abbreviation VARCHAR(10) NOT NULL,
        logo_url TEXT DEFAULT NULL,
        twitch_link TEXT DEFAULT NULL,
        total_points INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) $charset_collate;";

    // Games Table
    $games_table = $wpdb->prefix . 'games';
    $games_sql = "CREATE TABLE $games_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        team1_id BIGINT(20) UNSIGNED NOT NULL,
        team2_id BIGINT(20) UNSIGNED NOT NULL,
        date DATETIME NOT NULL,
        score1 INT DEFAULT 0,
        score2 INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (team1_id) REFERENCES $teams_table (id) ON DELETE CASCADE,
        FOREIGN KEY (team2_id) REFERENCES $teams_table (id) ON DELETE CASCADE
    ) $charset_collate;";

    // Players Table
    $players_table = $wpdb->prefix . 'players';
    $players_sql = "CREATE TABLE $players_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        team_id BIGINT(20) UNSIGNED NOT NULL,
        online_id VARCHAR(100) NOT NULL,
        position VARCHAR(100) DEFAULT '',
        discord_username VARCHAR(100) NOT NULL,
        twitter_handle VARCHAR(100) DEFAULT '',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (team_id) REFERENCES $teams_table (id) ON DELETE CASCADE
    ) $charset_collate;";

    // Player Statistics Table
    $stats_table = $wpdb->prefix . 'player_stats';
    $stats_sql = "CREATE TABLE $stats_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        player_id BIGINT(20) UNSIGNED NOT NULL,
        game_id BIGINT(20) UNSIGNED NOT NULL,
        gp INT DEFAULT 0,
        pts INT DEFAULT 0,
        reb INT DEFAULT 0,
        ast INT DEFAULT 0,
        stl INT DEFAULT 0,
        blk INT DEFAULT 0,
        fouls INT DEFAULT 0,
        turnovers INT DEFAULT 0,
        fgm INT DEFAULT 0,
        fga INT DEFAULT 0,
        tpm INT DEFAULT 0,
        tpa INT DEFAULT 0,
        ftm INT DEFAULT 0,
        fta INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (player_id) REFERENCES $players_table (id) ON DELETE CASCADE,
        FOREIGN KEY (game_id) REFERENCES $games_table (id) ON DELETE CASCADE
    ) $charset_collate;";

    // Game Player Selections Table
    $game_players_table = $wpdb->prefix . 'game_players';
    $game_players_sql = "CREATE TABLE $game_players_table (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        game_id BIGINT(20) UNSIGNED NOT NULL,
        team_id BIGINT(20) UNSIGNED NOT NULL,
        player_id BIGINT(20) UNSIGNED NOT NULL,
        position VARCHAR(10) NOT NULL,
        FOREIGN KEY (game_id) REFERENCES $games_table (id) ON DELETE CASCADE,
        FOREIGN KEY (team_id) REFERENCES $teams_table (id) ON DELETE CASCADE,
        FOREIGN KEY (player_id) REFERENCES $players_table (id) ON DELETE CASCADE
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta([$teams_sql, $games_sql, $players_sql, $stats_sql, $game_players_sql]);
}

function drop_custom_tables() {
    global $wpdb;
    $tables = [
        $wpdb->prefix . 'teams',
        $wpdb->prefix . 'games',
        $wpdb->prefix . 'players',
        $wpdb->prefix . 'player_stats',
        $wpdb->prefix . 'game_players'
    ];
    foreach ($tables as $table) {
        // Ensure to drop each table if it exists
        $wpdb->query("DROP TABLE IF EXISTS $table");
    }
}
?>