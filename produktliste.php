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

//function for dropping db when uninstalling
function produktliste_drop_db() {
    //drop custom database tables
    global $wpdb;

    $table_name_main = $wpdb->prefix . "produktliste_produkter";
    $table_name_product_category = $wpdb->prefix . "produktliste_kategorier";
    $table_name_product_ingredients = $wpdb->prefix . "produktliste_produkt_ingredienser";

    $wpdb->query("DROP TABLE IF EXISTS $table_name_product_ingredients");
    $wpdb->query("DROP TABLE IF EXISTS $table_name_main");
    $wpdb->query("DROP TABLE IF EXISTS $table_name_product_category");
}
/******  TEMP FUNCTION FOR DEV, drops the db when deactivating the plugin   ******/
register_deactivation_hook( __FILE__, 'produktliste_drop_db' );

//loading css
function load_produktliste_css() {
    if (!is_admin()) {
        wp_register_style( 'load_produktliste_css', plugins_url('/style/produktliste.css', __FILE__) );
        wp_enqueue_style( 'load_produktliste_css' );

        //enqueueing font awsome
        wp_register_style( 'load_font_awsome_min_css', plugins_url('/font-awesome-4.7.0/css/font-awesome.min.css', __FILE__) );
        wp_enqueue_style( 'load_font_awsome_min_css' );
    }
}
add_action( 'wp_enqueue_scripts', 'load_produktliste_css' );

//loading css for the plugin adminpage
function load_produktliste_css_admin($hook) {
    // Load only on correct admin page for the plugin
    if($hook != 'toplevel_page_produktliste-plugin') {
        return;
    }

    wp_register_style( 'load_produktliste_css_admin', plugins_url('/style/produktliste.css', __FILE__) );
    wp_enqueue_style( 'load_produktliste_css_admin' );

    //enqueueing font awsome
    wp_register_style( 'load_font_awsome_min_css', plugins_url('/font-awesome-4.7.0/css/font-awesome.min.css', __FILE__) );
    wp_enqueue_style( 'load_font_awsome_min_css' );
}
add_action( 'admin_enqueue_scripts', 'load_produktliste_css_admin' );

//loading javascript
function load_produktliste_js(){
    if (!is_admin()) {
        wp_register_script( 'produktliste_script', plugins_url( '/js/produktliste.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script('produktliste_script');
    }
}
add_action('wp_enqueue_scripts', 'load_produktliste_js');

//loading javascript for the plugin adminpage
function load_produktliste_js_admin($hook){
    // Load only on correct admin page for the plugin
    if($hook != 'toplevel_page_produktliste-plugin') {
        return;
    }

    wp_register_script( 'produktliste_script', plugins_url( '/js/produktliste.js', __FILE__ ), array( 'jquery' ) );
    wp_enqueue_script('produktliste_script');
}
add_action('admin_enqueue_scripts', 'load_produktliste_js_admin');


//DB versioning
global $produktliste_db_version;
$produktliste_db_version = "1.3";

//Function for creating the DB tables on plugin activation
function produktliste_install() {

    global $wpdb;
    global $produktliste_db_version;

    //loading library for dbDelta function
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name_main = $wpdb->prefix . "produktliste_produkter";
    $table_name_product_category = $wpdb->prefix . "produktliste_kategorier";
    $table_name_product_ingredients = $wpdb->prefix . "produktliste_produkt_ingredienser";
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
      price mediumint(9) NOT NULL,
      price_type boolean NOT NULL DEFAULT 0,
      picture_url varchar(255) DEFAULT '' NOT NULL,
      picture_alt_tag varchar(255) DEFAULT '' NOT NULL,
      PRIMARY KEY  (id),
      FOREIGN KEY  (category) REFERENCES $table_name_product_category(category_id)
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
    add_option( 'produktliste_db_version', $produktliste_db_version );

    //updating and modifying the db for a new version
    $installed_version = get_option( "produktliste_db_version" );
    if( $installed_version != $produktliste_db_version ){

        $table_name_main = $wpdb->prefix . "produktliste";
        $table_name_product_category = $wpdb->prefix . "produktliste_kategorier";
        $table_name_product_ingredients = $wpdb->prefix . "produktliste_produkt_ingredienser";
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
            price mediumint(9) NOT NULL,
            price_type boolean NOT NULL DEFAULT 0,
            picture_url varchar(255) DEFAULT '' NOT NULL,
            picture_alt_tag varchar(255) DEFAULT '' NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY  (category) REFERENCES $table_name_product_category(category_id)
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

        update_option( 'produktliste_db_version', $produktliste_db_version );
    }
}

//function for creating 2 dummy items in 2 dummy categories
function produktliste_install_data() {
    global $wpdb;

    $table_name_main = $wpdb->prefix . "produktliste_produkter";
    $table_name_product_category = $wpdb->prefix . "produktliste_kategorier";
    $table_name_product_ingredients = $wpdb->prefix . "produktliste_produkt_ingredienser";

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
            'price' => 250,
            'price_type' => 0,
            'picture_url' => 'img/eksempel-bilde-1.png',
            'picture_alt_tag' => 'Kort om bildet til produkt 1'
        )
    );

    $wpdb->insert(
        $table_name_main,
        array(
            'category' => 1,
            'product_name' => 'Hjortestek',
            'price' => 240,
            'price_type' => 0,
            'picture_url' => 'img/eksempel-bilde-2.png',
            'picture_alt_tag' => 'Kort om bildet til produkt 2'
        )
    );

    $wpdb->insert(
        $table_name_main,
        array(
            'category' => 2,
            'product_name' => 'Elgpølse',
            'price' => 200,
            'price_type' => 1,
            'picture_url' => 'img/eksempel-bilde-3.png',
            'picture_alt_tag' => 'Kort om bildet til produkt 3'
        )
    );

    $wpdb->insert(
        $table_name_main,
        array(
            'category' => 2,
            'product_name' => 'Plagepølse',
            'price' => 190,
            'price_type' => 1,
            'picture_url' => 'img/eksempel-bilde-4.png',
            'picture_alt_tag' => 'Kort om bildet til produkt 4'
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
            'product_id' => 2,
            'ingredient_name' => 'Svinehjerter',
            'allergen' => 0
        )
    );

    $wpdb->insert(
        $table_name_product_ingredients,
        array(
            'product_id' => 2,
            'ingredient_name' => 'Svinetunger',
            'allergen' => 0
        )
    );

    $wpdb->insert(
        $table_name_product_ingredients,
        array(
            'product_id' => 2,
            'ingredient_name' => 'Storfekjøtt',
            'allergen' => 0
        )
    );

    $wpdb->insert(
        $table_name_product_ingredients,
        array(
            'product_id' => 2,
            'ingredient_name' => 'Krydderblanding (Surhetsregulerende middel (E575), krydder, dekstrose (mais), Smaksforsterker (E621), Antioksidant (E301, E392, E300))',
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
}

//creating the db when plugin is activated
register_activation_hook( __FILE__, 'produktliste_install' );
register_activation_hook( __FILE__, 'produktliste_install_data' );

//update check on the db for new plugin version
function produktliste_update_db_check() {
    global $produktliste_db_version;
    if (get_site_option( 'produktliste_db_version' ) != $produktliste_db_version ) {
        produktliste_install();
    }
}
add_action( 'plugins_loaded', 'produktliste_update_db_check' );

//function for building the html part for frontend page
function show_produktliste() {
    global $wpdb;
    $categories;
    $produktliste_results;
    $ingredients;
    $produktliste_full_arr;

    $table_name_main = $wpdb->prefix . "produktliste_produkter";
    $table_name_product_category = $wpdb->prefix . "produktliste_kategorier";
    $table_name_product_ingredients = $wpdb->prefix . "produktliste_produkt_ingredienser";

    //query the db for categories
    $categories =   $wpdb->get_results("SELECT * FROM $table_name_product_category", ARRAY_A)
                    or die ( $wpdb->last_error );

    //query the db for all products with category name
    $produktliste_results =  $wpdb->get_results("
        SELECT id, category, product_name, price, price_type, picture_url, picture_alt_tag
        FROM    {$table_name_main}
    ", ARRAY_A)or die ( $wpdb->last_error );

    //query db for data on ingredients and allergens
    $ingredients = $wpdb->get_results("
        SELECT product_id, ingredient_name, allergen
        FROM    {$table_name_product_ingredients}
    ", ARRAY_A)or die ( $wpdb->last_error );

    //building the html output
    ?>
    <div class="produktliste_wrapper">
        <div><h1 class="hv-header_first">Produkter</h1></div>
        <?php
        //loop for each category
        foreach ($categories as $category) {
            ?>
            <div class='produktliste_category_wrapper'>
                <h2><?php echo esc_html( $category['category_name'] ) ?></h2>
                <?php
                //loop for processing each product
                foreach ($produktliste_results as $product) {
                    if ($category['category_id'] === $product['category']){
                        ?>
                        <div class="accordion">
                            <div class="accordion-thumbnail-div">
                                <img src="<?php echo esc_url(plugins_url( $product['picture_url'], __FILE__ )); ?>"
                                     alt="<?php echo esc_attr( $product['picture_alt_tag'] ); ?>"
                                     class="accordion-thumbnail"
                                />
                            </div>
                            <div class="accordion-content"><?php echo esc_html( $product['product_name'] ); ?></div>
                            <div class="accordion-content"><?php echo esc_html( $product['pris'] );
                                                                if ( $product['pris_type'] == 0 ) {
                                                                    echo 'kr/kg';
                                                                } elseif ($product['pris_type'] == 1) {
                                                                    echo 'kr/stk';
                                                                }
                                                            ?></div>
                            <div class="accordion-content"><i class="fa fa-chevron-down icon-placement" aria-hidden="true"></i></div>
                        </div>
                        <div class="panel">
                            <img src="<?php echo esc_url(plugins_url( $product['picture_url'], __FILE__ )); ?>"
                                 alt="<?php echo esc_attr( $product['picture_alt_tag'] ) ?>"
                                 class="accordion-image"
                            />
                            <div class="accordion-list">
                                <div><h3>Ingredienser</h3></div>
                                <div>
                                    <ul>
                                        <?php
                                        //loop for building the ingredients list
                                        foreach ($ingredients as $ingredient) {
                                            if ($product['id'] === $ingredient['product_id']) {
                                                echo '<li>';
                                                if ( $ingredient['allergen'] == 1 ) {
                                                    echo '<b>';
                                                        echo esc_html( $ingredient['ingredient_name'] );
                                                    echo '</b>';
                                                } else {
                                                    echo esc_html( $ingredient['ingredient_name'] );
                                                }
                                                echo '</li>';
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}

//function for building the html part for admin page
function show_produktliste_admin() {
    global $wpdb;
    $categories;
    $produktliste_results;
    $ingredients;
    $produktliste_full_arr;

    $table_name_main = $wpdb->prefix . "produktliste_produkter";
    $table_name_product_category = $wpdb->prefix . "produktliste_kategorier";
    $table_name_product_ingredients = $wpdb->prefix . "produktliste_produkt_ingredienser";

    //query the db for categories
    $categories =   $wpdb->get_results("SELECT * FROM $table_name_product_category", ARRAY_A)
    or die ( $wpdb->last_error );

    //query the db for all products with category name
    $produktliste_results =  $wpdb->get_results("
        SELECT id, category, product_name, pris, pris_type, picture_url, picture_alt_tag
        FROM    {$table_name_main}
    ", ARRAY_A)or die ( $wpdb->last_error );

    //query db for data on ingredients and allergens
    $ingredients = $wpdb->get_results("
        SELECT product_id, ingredient_name, allergen
        FROM    {$table_name_product_ingredients}
    ", ARRAY_A)or die ( $wpdb->last_error );

    //building the html output
    ?>
    <div class="produktliste_wrapper">
        <div><h2 class="hv-header_first">Eksisterende produkter i Produktlisten</h2></div>
        <?php
        //loop for each category
        foreach ($categories as $category) {
            ?>
            <div class='produktliste_category_wrapper'>
                <h2><?php echo esc_html( $category['category_name'] ) ?></h2>
                <?php
                //loop for processing each product
                foreach ($produktliste_results as $product) {
                    if ($category['category_id'] === $product['category']){
                        ?>
                        <div class="accordion">
                            <div class="accordion-thumbnail-div">
                                <img src="<?php echo esc_url(plugins_url( $product['picture_url'], __FILE__ )); ?>"
                                     alt="<?php echo esc_attr( $product['picture_alt_tag'] ); ?>"
                                     class="accordion-thumbnail"
                                />
                            </div>
                            <div class="accordion-content"><?php echo esc_html( $product['product_name'] ); ?></div>
                            <div class="accordion-content"><?php echo esc_html( $product['pris'] );
                                if ( $product['pris_type'] == 0 ) {
                                    echo 'kr/kg';
                                } elseif ($product['pris_type'] == 1) {
                                    echo 'kr/stk';
                                }
                                ?></div>
                            <div class="accordion-content"><i class="fa fa-chevron-down icon-placement" aria-hidden="true"></i></div>
                        </div>
                        <div class="panel">
                            <img src="<?php echo esc_url(plugins_url( $product['picture_url'], __FILE__ )); ?>"
                                 alt="<?php echo esc_attr( $product['picture_alt_tag'] ) ?>"
                                 class="accordion-image"
                            />
                            <div class="accordion-list">
                                <div><h3>Ingredienser</h3></div>
                                <div>
                                    <ul>
                                        <?php
                                        //loop for building the ingredients list
                                        foreach ($ingredients as $ingredient) {
                                            if ($product['id'] === $ingredient['product_id']) {
                                                echo '<li>';
                                                if ( $ingredient['allergen'] == 1 ) {
                                                    echo '<b>';
                                                    echo esc_html( $ingredient['ingredient_name'] );
                                                    echo '</b>';
                                                } else {
                                                    echo esc_html( $ingredient['ingredient_name'] );
                                                }
                                                echo '</li>';
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div>
                                <!-- TODO: add the buttons for the delete and edit options to each product -->
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}

//registrering the shortcodes
add_shortcode('produktliste', 'show_produktliste');


//code for registrering the plugin with wordpress
add_action('admin_menu', 'produktliste_setup_menu');
function produktliste_setup_menu() {
    //processing POST to the plugin page
    function produktliste_handle_post() {

    }

    //the code that creates the plugin admin page
    function produktliste_init() {
        produktliste_handle_post();

        //fetching the categories from the db


        ?>
        <div class="wrap">
            <div>
                <h1>Administrator side for Produktliste plugin</h1>
            </div>
            <div class="form_wrapper_category">
                <h2>Forandre kategori navn</h2>
                <form method="POST">
                    <div>
                        <label for="selCat">Velg Kategori</label>
                        <select class="form-control" name="category" id="selCat">
                            <?php category_selects($categories, $cat); ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="form_wrapper_product">
                <h2>Legg til nytt produkt</h2>
                <form method="POST" enctype="multipart/form-data">

                </form>
            </div>
        </div>

        <?php

        show_produktliste_admin();

//        global $wpdb;//grabbing the wp database prefix in this install
//        echo $wpdb->prefix . 'produktliste<br>';
//        echo $wpdb->prefix . 'produktliste_kategorier<br>';
//        echo $wpdb->prefix . 'produktliste_produkt_ingredienser<br>';
//        echo $wpdb->get_charset_collate() . '<br>';
//        echo '<img src="' . plugins_url( 'img/eksempel-bilde-1.png', __FILE__ ) . '" > ';
//        echo '<br>';
        //https://www.smashingmagazine.com/2016/04/three-approaches-to-adding-configurable-fields-to-your-plugin/


    }

    add_menu_page(
        'Produktliste Plugin Side',
        'Produktliste Plugin',
        'manage_options',
        'produktliste-plugin',
        'produktliste_init'
    );
}

?>