<?php
/*
Plugin Name: Produktliste
Description: En produktliste plugin for Hadeland Viltslakteri
Author: Sjur Sutterud Sagen
Version: 0.2
*/

/*****
 * Tutorials Used:
 * https://codex.wordpress.org/Creating_Tables_with_Plugins
 * https://codex.wordpress.org/Shortcode_API
 *****/

//security check for XSS attack
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'functions.php';

/******  TEMP FUNCTION FOR DEV, drops the db when deactivating the plugin   ******/
register_deactivation_hook( __FILE__, 'produktliste_drop_db' );

//loading css
add_action( 'wp_enqueue_scripts', 'load_produktliste_css' );
add_action( 'admin_enqueue_scripts', 'load_produktliste_css_admin' );

//loading javascript
add_action('wp_enqueue_scripts', 'load_produktliste_js');
add_action('admin_enqueue_scripts', 'load_produktliste_js_admin');


//DB versioning
global $produktliste_db_version;
$produktliste_db_version = "1.3";

//creating the db when plugin is activated
register_activation_hook( __FILE__, 'produktliste_install' );
register_activation_hook( __FILE__, 'produktliste_install_data' );

//update check on the db for new plugin version
add_action( 'plugins_loaded', 'produktliste_update_db_check' );

//registrering the shortcode
add_shortcode('produktliste', 'show_produktliste');

//code for registrering the plugin with wordpress
add_action('admin_menu', 'produktliste_setup_menu');

?>