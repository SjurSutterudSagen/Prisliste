<?php
/*
Plugin Name: Prisliste
Description: En prisliste plugin for Hadeland Viltslakteri
Author: Sjur Sutterud Sagen
Version: 0.1
*/

//check for security
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//DB versioning
global $prisliste_db_version;
$prisliste_db_version = "1.1";

//Function for creating the DB tables on plugin activation
function prisliste_install() {

    global $wpdb;//grabbing the wp database prefix in this install
    global $prisliste_db_version;

    //loading library for dbDelta function
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name_main = $wpdb->prefix . "prisliste_produkter";
    $table_name_product_category = $wpdb->prefix . "prisliste_kategorier";
    $table_name_product_ingredients = $wpdb->prefix . "prisliste_produkt_ingredienser";
    $table_name_product_allergens = $wpdb->prefix . "prisliste_produkt_allergener";
    $charset_collate = $wpdb->get_charset_collate();

    //SQL queries and tables creation
    $sql = "CREATE TABLE $table_name_product_category (
      category_id mediumint(9) NOT NULL AUTO_INCREMENT,
      category_name varchar(255) NOT NULL,
      PRIMARY KEY  (category_id)
    ) $charset_collate;";
    dbDelta($sql);


    $sql = "CREATE TABLE $table_name_main (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      category mediumint(9) NOT NULL,
      product_name varchar(255) NOT NULL,
      pris mediumint(9) NOT NULL,
      picture_url varchar(255) DEFAULT '' NOT NULL,
      PRIMARY KEY  (id),
      FOREIGN KEY  (category) REFERENCES $table_name_product_category(category_id)
    ) $charset_collate;";
    dbDelta($sql);

    $sql = "CREATE TABLE $table_name_product_allergens (
      allergen_name varchar(255) NOT NULL,
      product_id mediumint(9) NOT NULL,
      PRIMARY KEY  (allergen_name, product_id)
    ) $charset_collate;";
    dbDelta($sql);

    $sql = "CREATE TABLE $table_name_product_ingredients (
      product_id mediumint(9) NOT NULL,
      ingredient_name varchar(255) NOT NULL,
      allergen boolean DEFAULT 0 NOT NULL,
      PRIMARY KEY  (product_id, ingredient_name)
    ) $charset_collate;";
    dbDelta($sql);
    add_option( 'prisliste_db_version', $prisliste_db_version );

    //updating and modifying the db for a new version
    $installed_version = get_option( "prisliste_db_version" );
    if( $installed_version != $prisliste_db_version ){

        $table_name_main = $wpdb->prefix . "prisliste";
        $table_name_product_category = $wpdb->prefix . "prisliste_kategorier";
        $table_name_product_ingredients = $wpdb->prefix . "prisliste_produkt_ingredienser";
        $table_name_product_allergens = $wpdb->prefix . "prisliste_produkt_allergener";
        $charset_collate = $wpdb->get_charset_collate();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php');

        //SQL queries and tables creation
        $sql = "CREATE TABLE $table_name_product_category (
          category_id mediumint(9) NOT NULL AUTO_INCREMENT,
          category_name varchar(255) NOT NULL UNIQUE,
          PRIMARY KEY  (category_id)
        );";
        dbDelta($sql);

        $sql = "CREATE TABLE $table_name_main (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          category mediumint(9) NOT NULL,
          name varchar(255) NOT NULL,
          pris mediumint(9) NOT NULL,
          picture_url varchar(255) DEFAULT '' NOT NULL,
          PRIMARY KEY  (id)
        );";
        dbDelta($sql);
    
        $sql = "CREATE TABLE $table_name_product_allergens (
          allergen_name varchar(255) NOT NULL,
          product_id mediumint(9) NOT NULL,
          PRIMARY KEY  (allergen_name, product_id)
        );";
        dbDelta($sql);
        
        $sql = "CREATE TABLE $table_name_product_ingredients (
          product_id mediumint(9) NOT NULL,
          ingredient_name varchar(255) NOT NULL,
          allergen boolean DEFAULT 0 NOT NULL,
          PRIMARY KEY  (product_id, ingredient_name)
        );";
        dbDelta($sql);

        update_option( 'prisliste_db_version', $prisliste_db_version );
    }
}

//creating the db when plugin is activated
register_activation_hook( __FILE__, 'prisliste_install' );

//update check on the db for new plugin version
function prisliste_update_db_check() {
    global $prisliste_db_version;
    if (get_site_option( 'prisliste_db_version' ) != $prisliste_db_version ) {
        prisliste_install();
    }
}
add_action( 'plugins_loaded', 'prisliste_update_db_check' );


add_action('admin_menu', 'prisliste_setup_menu');

function prisliste_setup_menu() {
    add_menu_page(
        'Prisliste Plugin Side',
        'Prisliste Plugin',
        'manage_options',
        'prisliste-plugin',
        'prisliste_init'
    );

    function prisliste_init() {
        //prisliste_handle_post();

        echo '<h1>Prisliste</h1>';
        global $wpdb;//grabbing the wp database prefix in this install
        echo $wpdb->prefix . 'prisliste<br>';
        echo $wpdb->prefix . 'prisliste_kategorier<br>';
        echo $wpdb->prefix . 'prisliste_produkt_ingredienser<br>';
        echo $wpdb->prefix . 'prisliste_produkt_allergener<br>';
        echo $wpdb->get_charset_collate();
    }

    //function prisliste_handle_post() {

    //}


}

?>