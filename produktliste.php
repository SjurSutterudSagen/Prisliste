<?php
/*
Plugin Name: Produktliste
Description: En produktliste plugin for Hadeland Viltslakteri
Author: <a href="https://www.linkedin.com/in/sjur-sutterud-sagen-a1483911b/">Sjur Sutterud Sagen</a>, <a href="https://eriksendesign.no/">Dag-Roger Eriksen</a>
Version: 1.1
*/

//security check for XSS attack
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'functions.php';

//loading css
add_action( 'wp_enqueue_scripts', 'load_produktliste_css' );
add_action( 'admin_enqueue_scripts', 'load_produktliste_css_admin' );

//loading javascript
add_action('wp_enqueue_scripts', 'load_produktliste_js');
add_action('admin_enqueue_scripts', 'load_produktliste_js_admin');


//DB versioning
global $produktliste_db_version;
$produktliste_db_version = "1.5";

//creating the db when plugin is activated
register_activation_hook( __FILE__, 'produktliste_install' );

//update check on the db for new plugin version
add_action( 'plugins_loaded', 'produktliste_update_db_check' );

//registrering the shortcode
add_shortcode('produktliste', 'show_produktliste');

//code for registrering the plugin with wordpress
add_action('admin_menu', 'produktliste_setup_menu');

//adding the plugin capabilities to userroles
register_activation_hook( __FILE__, 'add_plugin_caps' );

?>
