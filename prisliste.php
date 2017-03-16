<?php
/*
Plugin Name: Prisliste
Description: En prisliste plugin for Hadeland Viltslakteri
Author: Sjur Sutterud Sagen
Version: 0.1
*/

/*****
 * Tutorials Used:
 * https://codex.wordpress.org/Creating_Tables_with_Plugins
 * https://codex.wordpress.org/Shortcode_API
 *****/

/******  TEMP FUNCTION FOR DEV   ******/
function prisliste_drop_db() {
    //drop custom database tables
    global $wpdb;

    $table_name_main = $wpdb->prefix . "prisliste_produkter";
    $table_name_product_category = $wpdb->prefix . "prisliste_kategorier";
    $table_name_product_ingredients = $wpdb->prefix . "prisliste_produkt_ingredienser";
    $table_name_product_allergens = $wpdb->prefix . "prisliste_produkt_allergener";

    $wpdb->query("DROP TABLE IF EXISTS $table_name_product_ingredients");
    $wpdb->query("DROP TABLE IF EXISTS $table_name_product_allergens");
    $wpdb->query("DROP TABLE IF EXISTS $table_name_main");
    $wpdb->query("DROP TABLE IF EXISTS $table_name_product_category");
}
register_deactivation_hook( __FILE__, 'prisliste_drop_db' );


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
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      product_id mediumint(9) NOT NULL,
      allergen_name varchar(255) NOT NULL,
      PRIMARY KEY  (id),
      FOREIGN KEY  (product_id) REFERENCES $table_name_main(id)
    ) $charset_collate;";
    dbDelta($sql);

    $sql = "CREATE TABLE $table_name_product_ingredients (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      product_id mediumint(9) NOT NULL,
      ingredient_name varchar(255) NOT NULL,
      allergen boolean DEFAULT 0 NOT NULL,
      PRIMARY KEY  (id),
      FOREIGN KEY  (product_id) REFERENCES $table_name_main(id)
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
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            product_id mediumint(9) NOT NULL,
            allergen_name varchar(255) NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY  (product_id) REFERENCES $table_name_main(id)
        ) $charset_collate;";
        dbDelta($sql);

        $sql = "CREATE TABLE $table_name_product_ingredients (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            product_id mediumint(9) NOT NULL,
            ingredient_name varchar(255) NOT NULL,
            allergen boolean DEFAULT 0 NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY  (product_id) REFERENCES $table_name_main(id)
        ) $charset_collate;";
        dbDelta($sql);

        update_option( 'prisliste_db_version', $prisliste_db_version );
    }
}

//function for creating 2 dummy items in 2 dummy categories
function prisliste_install_data() {
    global $wpdb;

    $table_name_main = $wpdb->prefix . "prisliste_produkter";
    $table_name_product_category = $wpdb->prefix . "prisliste_kategorier";
    $table_name_product_ingredients = $wpdb->prefix . "prisliste_produkt_ingredienser";
    $table_name_product_allergens = $wpdb->prefix . "prisliste_produkt_allergener";

    //category table
    $wpdb->insert(
        $table_name_product_category,
        array(
            'category_name' => 'Kjøtt'
        )
    );

    $wpdb->insert(
        $table_name_product_category,
        array(
            'category_name' => 'Pølser'
        )
    );

    //product table
    $wpdb->insert(
        $table_name_main,
        array(
            'category' => 1,
            'product_name' => 'Elgstek',
            'pris' => 250,
            'picture_url' => 'img/eksempel-bilde-1.png'
        )
    );

    $wpdb->insert(
        $table_name_main,
        array(
            'category' => 1,
            'product_name' => 'Hjortestek',
            'pris' => 240,
            'picture_url' => 'img/eksempel-bilde-2.png'
        )
    );

    $wpdb->insert(
        $table_name_main,
        array(
            'category' => 2,
            'product_name' => 'Elgpølse',
            'pris' => 200,
            'picture_url' => 'img/eksempel-bilde-3.png'
        )
    );

    $wpdb->insert(
        $table_name_main,
        array(
            'category' => 2,
            'product_name' => 'Plagepølse',
            'pris' => 190,
            'picture_url' => 'img/eksempel-bilde-4.png'
        )
    );

    //ingredients table
    $wpdb->insert(
        $table_name_product_ingredients,
        array(
            'product_id' => 1,
            'ingredient_name' => 'Elgkjøtt',
            'allergen' => 0
        )
    );

    $wpdb->insert(
        $table_name_product_ingredients,
        array(
            'product_id' => 1,
            'ingredient_name' => 'Krydder Mix',
            'allergen' => 1
        )
    );

    $wpdb->insert(
        $table_name_product_ingredients,
        array(
            'product_id' => 2,
            'ingredient_name' => 'Elgkjøtt',
            'allergen' => 0
        )
    );

    $wpdb->insert(
        $table_name_product_ingredients,
        array(
            'product_id' => 2,
            'ingredient_name' => 'Krydder Mix',
            'allergen' => 1
        )
    );

    $wpdb->insert(
        $table_name_product_ingredients,
        array(
            'product_id' => 3,
            'ingredient_name' => 'Elgkjøtt',
            'allergen' => 0
        )
    );

    $wpdb->insert(
        $table_name_product_ingredients,
        array(
            'product_id' => 3,
            'ingredient_name' => 'Krydder Mix',
            'allergen' => 1
        )
    );

    $wpdb->insert(
        $table_name_product_ingredients,
        array(
            'product_id' => 4,
            'ingredient_name' => 'Elgkjøtt',
            'allergen' => 0
        )
    );

    $wpdb->insert(
        $table_name_product_ingredients,
        array(
            'product_id' => 4,
            'ingredient_name' => 'Krydder Mix',
            'allergen' => 1
        )
    );

    //allergens table
    $wpdb->insert(
        $table_name_product_allergens,
        array(
            'product_id' => 1,
            'allergen_name' => 'Nøtteallergi'
        )
    );

    $wpdb->insert(
        $table_name_product_allergens,
        array(
            'product_id' => 2,
            'allergen_name' => 'Nøtteallergi'
        )
    );

    $wpdb->insert(
        $table_name_product_allergens,
        array(
            'product_id' => 3,
            'allergen_name' => 'Nøtteallergi'
        )
    );

    $wpdb->insert(
        $table_name_product_allergens,
        array(
            'product_id' => 4,
            'allergen_name' => 'Nøtteallergi'
        )
    );

    $wpdb->insert(
        $table_name_product_allergens,
        array(
            'product_id' => 4,
            'allergen_name' => 'Melkeallergi'
        )
    );
}

//creating the db when plugin is activated
register_activation_hook( __FILE__, 'prisliste_install' );
register_activation_hook( __FILE__, 'prisliste_install_data' );

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
        echo $wpdb->get_charset_collate() . '<br>';
        echo '<img src="' . plugins_url( 'img/eksempel-bilde-1.png', __FILE__ ) . '" > ';

    }

    //function prisliste_handle_post() {

    //}
}

/*
 * Section for shortcode
 */


?>