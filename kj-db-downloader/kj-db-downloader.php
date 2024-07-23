<?php
/*
Plugin Name: KJ DB Downloader
Description: A simple plugin to download the WordPress database.
Version: 1.0
Author: Jigar Kahar
Text Domain: kj-db-downloader
Domain Path: /languages
*/

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Include the main class file
require_once plugin_dir_path(__FILE__) . 'includes/class-kj-db-downloader.php';

// Initialize the plugin
function run_kj_db_downloader() {
    $plugin = new KJ_DB_Downloader();
}
add_action('plugins_loaded', 'run_kj_db_downloader');
