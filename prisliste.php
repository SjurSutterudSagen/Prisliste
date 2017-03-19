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

//security check
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

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

//loading css
function load_prisliste_css() {
    if (!is_admin()) {
        wp_register_style( 'load_prisliste_css', plugins_url('/style/prisliste.css', __FILE__) );
        wp_enqueue_style( 'load_prisliste_css' );

        //enqueueing font awsome
        wp_register_style( 'load_font_awsome_min_css', plugins_url('/font-awesome-4.7.0/css/font-awesome.min.css', __FILE__) );
        wp_enqueue_style( 'load_font_awsome_min_css' );
    }
}
add_action( 'wp_enqueue_scripts', 'load_prisliste_css' );

//loading css for the plugin adminpage
function load_prisliste_css_admin($hook) {
    // Load only on correct admin page for the plugin
    if($hook != 'toplevel_page_prisliste-plugin') {
        return;
    }

    wp_register_style( 'load_prisliste_css_admin', plugins_url('/style/prisliste.css', __FILE__) );
    wp_enqueue_style( 'load_prisliste_css_admin' );

    //enqueueing font awsome
    wp_register_style( 'load_font_awsome_min_css', plugins_url('/font-awesome-4.7.0/css/font-awesome.min.css', __FILE__) );
    wp_enqueue_style( 'load_font_awsome_min_css' );
}
add_action( 'admin_enqueue_scripts', 'load_prisliste_css_admin' );

//loading javascript
function load_prisliste_js(){
    if (!is_admin()) {
        wp_register_script( 'prisliste_script', plugins_url( '/js/prisliste.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script('prisliste_script');
    }
}
add_action('wp_enqueue_scripts', 'load_prisliste_js_admin');

//loading javascript for the plugin adminpage
function load_prisliste_js_admin($hook){
    // Load only on correct admin page for the plugin
    if($hook != 'toplevel_page_prisliste-plugin') {
        return;
    }

    wp_register_script( 'prisliste_script', plugins_url( '/js/prisliste.js', __FILE__ ), array( 'jquery' ) );
    wp_enqueue_script('prisliste_script');
}
add_action('admin_enqueue_scripts', 'load_prisliste_js_admin');


//DB versioning
global $prisliste_db_version;
$prisliste_db_version = "1.1";

//Function for creating the DB tables on plugin activation
function prisliste_install() {

    global $wpdb;
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
      picture_alt_tag varchar(255) DEFAULT '' NOT NULL,
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
            picture_alt_tag varchar(255) DEFAULT '' NOT NULL,
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
            'picture_url' => 'img/eksempel-bilde-1.png',
            'picture_alt_tag' => 'Kort om bildet til produkt 1'
        )
    );

    $wpdb->insert(
        $table_name_main,
        array(
            'category' => 1,
            'product_name' => 'Hjortestek',
            'pris' => 240,
            'picture_url' => 'img/eksempel-bilde-2.png',
            'picture_alt_tag' => 'Kort om bildet til produkt 2'
        )
    );

    $wpdb->insert(
        $table_name_main,
        array(
            'category' => 2,
            'product_name' => 'Elgpølse',
            'pris' => 200,
            'picture_url' => 'img/eksempel-bilde-3.png',
            'picture_alt_tag' => 'Kort om bildet til produkt 3'
        )
    );

    $wpdb->insert(
        $table_name_main,
        array(
            'category' => 2,
            'product_name' => 'Plagepølse',
            'pris' => 190,
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

    //allergens table
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

    $wpdb->insert(
        $table_name_product_allergens,
        array(
            'product_id' => 4,
            'allergen_name' => 'Pollenallergi'
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

//code for registrering the plugin with wordpress
function prisliste_setup_menu() {
    add_menu_page(
        'Prisliste Plugin Side',
        'Prisliste Plugin',
        'manage_options',
        'prisliste-plugin',
        'prisliste_init'
    );

    //the code that creates the plugin admin page
    function prisliste_init() {
        //prisliste_handle_post();

//        global $wpdb;//grabbing the wp database prefix in this install
//        echo $wpdb->prefix . 'prisliste<br>';
//        echo $wpdb->prefix . 'prisliste_kategorier<br>';
//        echo $wpdb->prefix . 'prisliste_produkt_ingredienser<br>';
//        echo $wpdb->prefix . 'prisliste_produkt_allergener<br>';
//        echo $wpdb->get_charset_collate() . '<br>';
//        echo '<img src="' . plugins_url( 'img/eksempel-bilde-1.png', __FILE__ ) . '" > ';
//        echo '<br>';
        show_prisliste();

    }

    //processing POST to the plugin page
    //function prisliste_handle_post() {

    //}
}
//function for building the frontend part
function show_prisliste()
{
    global $wpdb;
    $categories;
    $prisliste_results;
    $ingredients;
    $allergens;
    $prisliste_full_arr;

    $table_name_main = $wpdb->prefix . "prisliste_produkter";
    $table_name_product_category = $wpdb->prefix . "prisliste_kategorier";
    $table_name_product_ingredients = $wpdb->prefix . "prisliste_produkt_ingredienser";
    $table_name_product_allergens = $wpdb->prefix . "prisliste_produkt_allergener";

    //query the db for categories
    $categories =   $wpdb->get_results("SELECT * FROM $table_name_product_category", ARRAY_A)
                    or die ( $wpdb->last_error );

    //query the db for all products with category name
    $prisliste_results =  $wpdb->get_results("
        SELECT id, category, product_name, pris, picture_url, picture_alt_tag
        FROM    {$table_name_main}
    ", ARRAY_A)or die ( $wpdb->last_error );

    //query db for data on ingredients and allergens
    $ingredients = $wpdb->get_results("
        SELECT product_id, ingredient_name, allergen
        FROM    {$table_name_product_ingredients}
    ", ARRAY_A)or die ( $wpdb->last_error );

    $allergens = $wpdb->get_results("
        SELECT product_id, allergen_name
        FROM    {$table_name_product_allergens}
    ", ARRAY_A)or die ( $wpdb->last_error );

    //building the html output
    ?>
    <div class="prisliste_wrapper">
        <div><h1 class="hv-header_first">Prisliste</h1></div>
        <?php
        foreach ($categories as $category) {
            ?>
            <div class='prisliste_category_wrapper'>
                <h2><?php echo esc_html( $category['category_name'] ) ?></h2>
                <?php
                foreach ($prisliste_results as $product) {
                    if ($category['category_id'] === $product['category']){
                        ?>
                        <div class="accordion">
                            <div class="accordion-thumbnail-div">
                                <img src="<?php echo esc_url(plugins_url( $product['picture_url'], __FILE__ )); ?>"
                                     alt="<?php echo esc_attr( $product['picture_alt_tag'] ) ?>"
                                     class="accordion-thumbnail"
                                />
                            </div>
                            <div class="accordion-content"><?php echo esc_html( $product['product_name'] ) ?></div>
                            <div class="accordion-content"><?php echo esc_html( $product['pris'] ) ?></div>
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
                                <div>
                                    <?php
                                    $count=0;
                                    foreach ($allergens as $allergen) {
                                        if ($product['id'] === $allergen['product_id']) {
                                            if ($count == 0){
                                                echo '<h3>Allergier</h3>';
                                                $count++;
                                            }
                                            echo esc_html( $allergen['allergen_name'] ) . ' ';
                                        }
                                    }
                                    ?>
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

//    foreach ($categories as $category) {
//        echo 'Kategori navnet er: '; print($category['category_name']); echo '<br><br>';
//        foreach ($prisliste_results as $product) {
//            if ($category['category_id'] === $product['category']){
//                echo 'Produkt navnet er: '; print($product['product_name']); echo '<br>';
//                foreach ($ingredients as $ingredient) {
//                    if ($product['id'] === $ingredient['product_id']) {
//                        echo 'Ingrediens navnet er: '; print($ingredient['ingredient_name']);
//                        if ($ingredient['allergen'] == 1) {
//                            echo ' som er et allergen. ';
//                        } else {
//                            echo ' som er ikke et allergen. ';
//                        }
//                        echo '<br>';
//                    }
//                }
//                echo 'Allergenene i dette produktet er: ';
//                foreach ($allergens as $allergen) {
//                    if ($product['id'] === $allergen['product_id']) {
//                        print($allergen['allergen_name']); echo ' ';
//                    }
//                }
//                echo '<br><br>';
//            }
//        }
//        echo '<br>';
//    }
//}

//registrering the shortcode
?>