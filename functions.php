<?php
/**
 * Created by PhpStorm.
 * User: Sjur
 * Date: 27.03.2017
 * Time: 16.58
 */

//security check for XSS attack
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*************************************************
*   Functions for Plugin Installation/updating   *
*************************************************/

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

//function for update check on the db for new plugin version
function produktliste_update_db_check() {
    global $produktliste_db_version;
    if (get_site_option( 'produktliste_db_version' ) != $produktliste_db_version ) {
        produktliste_install();
    }
}

/************************************
 *   Functions for Loading Files    *
 ***********************************/

//functions for loading css
function load_produktliste_css() {
    if (!is_admin()) {
        wp_register_style( 'load_produktliste_css', plugins_url('/style/produktliste.css', __FILE__) );
        wp_enqueue_style( 'load_produktliste_css' );

        //enqueueing font awsome
        wp_register_style( 'load_font_awsome_min_css', plugins_url('/vendor/font-awesome-4.7.0/css/font-awesome.min.css', __FILE__) );
        wp_enqueue_style( 'load_font_awsome_min_css' );
    }
}

function load_produktliste_css_admin($hook) {
    // Load only on correct admin page for the plugin
    if($hook != 'toplevel_page_produktliste-plugin') {
        return;
    }

    wp_register_style( 'load_produktliste_css_admin', plugins_url('/style/produktliste.css', __FILE__) );
    wp_enqueue_style( 'load_produktliste_css_admin' );

    //enqueueing font awsome
    wp_register_style( 'load_font_awsome_min_css', plugins_url('/vendor/font-awesome-4.7.0/css/font-awesome.min.css', __FILE__) );
    wp_enqueue_style( 'load_font_awsome_min_css' );
}

//functions for loading javascript
function load_produktliste_js(){
    if (!is_admin()) {
        wp_register_script( 'produktliste_script', plugins_url( '/js/produktliste.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script('produktliste_script');
    }
}

function load_produktliste_js_admin($hook){
    // Load only on correct admin page for the plugin
    if($hook != 'toplevel_page_produktliste-plugin') {
        return;
    }

    wp_register_script( 'produktliste_script', plugins_url( '/js/produktliste_admin.js', __FILE__ ), array( 'jquery' ) );
    wp_enqueue_script('produktliste_script');
}

/****************************************
 *   Functions for Outputting to HTML   *
 ***************************************/

//function for building the html part for frontend productliste
function show_produktliste() {
    global $wpdb;
    $categories;
    $produktliste_results;
    $ingredients;

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
                            <div class="accordion-content"><?php echo esc_html( $product['price'] );
                                if ( $product['price_type'] == 0 ) {
                                    echo 'kr/kg';
                                } elseif ($product['price_type'] == 1) {
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

//function for building the html part for admin page productliste
function show_produktliste_admin($categories, $produktliste_results, $ingredients) {

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
                            <div class="accordion-content"><?php echo esc_html( $product['price'] );
                                if ( $product['price_type'] == 0 ) {
                                    echo 'kr/kg';
                                } elseif ($product['price_type'] == 1) {
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
                                <div class="buttons_wrapper">
                                    <div>
                                        <form method="POST">
                                            <input type="hidden" name="edit_product" value="true" />
                                            <?php wp_nonce_field( 'produktliste_product_edit_update', 'produktliste_product_edit_form' ); ?>
                                            <p class="submit">
                                                <input type="hidden" name="product_id" value="<?php echo esc_html( $product['id'] ); ?>" />
                                                <input type="submit" name="edit_product_submit" class="button button-primary" value="Endre">
                                            </p>
                                        </form>
                                    </div>
                                    <div>
                                        <form method="POST">
                                            <input type="hidden" name="delete_product" value="true" />
                                            <?php wp_nonce_field( 'produktliste_product_delete_update', 'produktliste_product_delete_form' ); ?>
                                            <p class="submit">
                                                <input type="hidden" name="product_id" value="<?php echo esc_html( $product['id'] ); ?>" />
                                                <input type="submit" name="delete_product_submit" class="button button-warning" value="Slett">
                                            </p>
                                        </form>
                                    </div>
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

function show_adminpage_forms($categories, $post_values) {
    ?>
    <div class="wrap">
        <div>
            <h1>Administrator side for Produktliste plugin</h1>
        </div>

        <!-- TODO: Legg til kategori valg. Legg til ny/Oppdaternavn/Slett Form -->
        <!--            <div class="form_wrapper_category">-->
        <!--                <h2>Forandre kategori navn</h2>-->
        <!--                <form method="POST">-->
        <!--                    <div>-->
        <!--                        <label for="selCat">Velg Kategori</label>-->
        <!--                        <select class="form-control" name="category" id="selCat">-->
        <!--                            --><?php //category_selects($categories, $cat); ?>
        <!--                        </select>-->
        <!--                    </div>-->
        <!--                </form>-->
        <!--            </div>-->



        <div class="form_wrapper_product">
            <?php
            if ($post_values['editing_status']) {
                echo '<h2>Endre produkt</h2>';
            } else {
                echo '<h2>Legg til nytt produkt</h2>';
            }
            ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="main_form_updated" value="true" />
                <?php wp_nonce_field( 'produktliste_update', 'produktliste_form' );
                if ($post_values['editing_status']) {
                    echo '<input type="hidden" name="editing_status" value="true" />';
                } else {
                    echo '<input type="hidden" name="editing_status" value="false" />';
                }
                ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th><label for="productname">Produktnavn</label></th>
                            <td><input name="productname" type="text" value="<?php
                                if ($post_values['productname']){
                                    echo esc_html( $post_values['productname'] );
                                }?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="category">Kategori</label></th>
                            <td>
                                <select name="category" type="text" value="" class="regular-text">
                                    <?php
                                    foreach ($categories as $category) {
                                        if ($post_values['category'] === $category['category_name']) {
                                            echo "<option value='" . esc_html( $category['category_name'] ) . "' selected='selected'>" . esc_html( $category['category_name'] ) . "</option>";
                                        } else {
                                            echo "<option value='" . esc_html( $category['category_name'] ) . "'>" . esc_html( $category['category_name'] ) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="price">Pris</label></th>
                            <td>
                                <input name="price" type="text" value="<?php
                                if ($post_values['price']){
                                    echo esc_html( $post_values['price'] );
                                }?>" class="regular-text" />
                            </td>
                            <td>
                                <select name="price_type" type="text" value="" class="regular-text">
                                    <?php
                                    if ($post_values['price_type'] === '0') {
                                        echo "<option value='kr/kg' selected='selected'>kr/kg</option>";
                                        echo "<option value='kr/stk'>kr/stk</option>";
                                    } elseif ($post_values['price_type'] === '1') {
                                        echo "<option value='kr/kg'>kr/kg</option>";
                                        echo "<option value='kr/stk' selected='selected'>kr/stk</option>";
                                    } else {
                                        echo "<option value='kr/kg'>kr/kg</option>";
                                        echo "<option value='kr/stk'>kr/stk</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="product_image">Last opp bilde</label></th>
                            <td>
                                <input type="file" name="product_image">
                            </td>
                            <?php
                            if ($post_values['image_url']) {
                                ?>
                                <td>
                                    <p>Eksisterende bilde</p>
                                    <img src="<?php echo esc_url(plugins_url( $post_values['image_url'], __FILE__ )); ?>"/>
                                </td>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <th><label for="alt_txt">Alt-tekst: Kort og beskrivende tekst av selve bildet.</label></th>
                            <td><input name="alt_txt" type="text" value="<?php
                                if ($post_values['alt_txt']){
                                    echo esc_html( $post_values['alt_txt'] );
                                }?>" class="regular-text" /></td>
                        </tr>
                    </tbody>
                </table>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th><h3>Ingredienser</h3></th>
                            <td></td>
                            <td><p>Allergen?</p></td>
                        </tr>
                        <?php
                        //loop for ingredients
                        if ( count($post_values['ingredient']) !== 0) {
                            for ($i = 0; $i < count($post_values['ingredient']); $i++) {
                                echo "<tr> 
                                        <th><label for='ingredient[" . ($i + 1) . "]'>Ingrediens " . ($i + 1) . "</label></th>
                                        <td><input name='ingredient[" . ($i + 1) . "][" . 'ingredient_name' . "]' type='text' value='" . esc_html($post_values['ingredient'][$i]['ingredient_name']) . "' class='regular-text productlist_ingredient' /></td>";
                                if ( $post_values['ingredient'][($i + 1)]['allergen'] === '1') {
                                    echo "<td><input name='ingredient[" . ($i + 1) . "][" . 'allergen' . "]' type='checkbox' value='1' class='regular-text' checked='checked'/></td>";
                                } else {
                                    echo "<td><input name='ingredient[" . ($i + 1) . "][" . 'allergen' . "]' type='checkbox' value='1' class='regular-text' /></td>";
                                }
                                echo "</tr>";
                            }
                        }
                        ?>
                        <tr id="ingredients_wrapper">
                            <th></th>
                            <td><p class="button button-primary" id="new_ingredient">Legg til en ingrediens</p></td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" class="button button-primary" value="Lagre Produkt">
                </p>
            </form>
            <form method="POST">
                <p class="submit">
                    <input type="submit" name="reset" class="button" value="Reset siden">
                </p>
            </form>
        </div>
    </div>

    <?php
}

/**********************************************************
 *   Functions for building and processing the adminpage   *
 *********************************************************/
//processing POST to the plugin page from the main form
//TODO: Add check for what the current user can do!
function produktliste_handle_post_main_form($wpdb, $table_name_main, $table_name_product_category, $table_name_product_ingredients, $post_values) {
    if(
        ! isset( $_POST['produktliste_form'] ) ||
        ! wp_verify_nonce( $_POST['produktliste_form'], 'produktliste_update' )
    ){ ?>
        <div class="error">
            <p>Sikkerhetsjekk feilet: Din nonce var ikke korrekt. Vennligst prøv igjen.</p>
        </div> <?php
        exit;
    } else {
        // Handle our form data
        //updating editing status for displaying correct text
        $editing_status = sanitize_text_field($_POST['editing_status']);
        if ($editing_status) {
            $post_values['editing_status'] = TRUE;
        }
        //TODO: Confirm editing status is working with faulty inputs

        //validating the data
        //TODO: Add validation of data before storing with error messages


        //storing the data
        //TODO: Add storing logic for both products and ingredients

        //outputting the success message
        ?>
        <div class="updated">
            <p>Produkt lagret!</p>
        </div> <?php

    }
}

//processing POST to the plugin page from the product edit button
function produktliste_handle_post_product_edit_form($wpdb, $table_name_main, $table_name_product_category, $table_name_product_ingredients, $post_values) {
    if(
        ! isset( $_POST['produktliste_product_edit_form'] ) ||
        ! wp_verify_nonce( $_POST['produktliste_product_edit_form'], 'produktliste_product_edit_update' )
    ){ ?>
        <div class="error">
            <p>Sikkerhetsjekk feilet: Din nonce var ikke korrekt. Vennligst prøv igjen.</p>
        </div> <?php
        exit;
    } else {
        // Processing the POST
        //querying db for data on the product
        $product_id = $_POST['product_id'];
        $product = $wpdb->get_row( $wpdb->prepare( "
          SELECT m.id, c.category_name, m.product_name, m.price, m.price_type, m.picture_url, m.picture_alt_tag 
          FROM {$table_name_main} m, {$table_name_product_category} c 
          WHERE m.category = c.category_id 
          AND ID = %d", $product_id), ARRAY_A)or die ( $wpdb->last_error );

        //querying db for data on the products ingredients
        $produkt_ingredients = $wpdb->get_results( $wpdb->prepare( "
          SELECT i.ingredient_name, i.allergen 
          FROM {$table_name_main} m, {$table_name_product_ingredients} i 
          WHERE m.id = i.product_id 
          AND m.id = %d", $product_id), ARRAY_A)or die ( $wpdb->last_error );

        //updating $post_values with correct values
        $post_values['product_id'] = $product['id'];
        $post_values['productname'] = $product['product_name'];
        $post_values['category'] = $product['category_name'];
        $post_values['price'] = $product['price'];
        $post_values['price_type'] = $product['price_type'];
        $post_values['alt_txt'] = $product['picture_alt_tag'];
        $post_values['image_url'] = $product['picture_url'];

        for ($i = 0; $i < count($produkt_ingredients); $i++) {
            $post_values['ingredient'][$i] = $produkt_ingredients[$i];
        }

        $post_values['editing_status'] = TRUE;

        return $post_values;
    }
}

//processing POST to the plugin page from the product delete button
function produktliste_handle_post_product_delete_form($wpdb, $table_name_main, $table_name_product_category, $table_name_product_ingredients) {
    if(
        ! isset( $_POST['produktliste_product_delete_form'] ) ||
        ! wp_verify_nonce( $_POST['produktliste_product_delete_form'], 'produktliste_product_delete_update' )
    ){ ?>
        <div class="error">
            <p>Sikkerhetsjekk feilet: Din nonce var ikke korrekt. Vennligst prøv igjen.</p>
        </div> <?php
        exit;
    } else {
        // Handle our form data

        //outputting the success message
        ?>
        <div class="updated">
            <p>Delete Button Clicked for Product with ID: <?php echo esc_html( $_POST['product_id'] ); ?>.</p>
        </div> <?php

    }
}

function produktliste_setup_menu() {

    //the code that creates the admin page of the plugin
    function produktliste_init() {
        $post_values;

        //declaring db variables
        global $wpdb;
        $table_name_main = $wpdb->prefix . "produktliste_produkter";
        $table_name_product_category = $wpdb->prefix . "produktliste_kategorier";
        $table_name_product_ingredients = $wpdb->prefix . "produktliste_produkt_ingredienser";

        //Checking for 'main_form_updated' to process the form on POST
        if( $_POST['main_form_updated'] === 'true' ){
            produktliste_handle_post_main_form($wpdb, $table_name_main, $table_name_product_category, $table_name_product_ingredients, $post_values);
        }

        //Checking for 'edit_product' to process the form on POST
        if( $_POST['edit_product'] === 'true' ){
            $post_values = produktliste_handle_post_product_edit_form($wpdb, $table_name_main, $table_name_product_category, $table_name_product_ingredients, $post_values);
        }

        //Checking for 'delete_product' to process the form on POST
        if( $_POST['delete_product'] === 'true' ){
            produktliste_handle_post_product_delete_form($wpdb, $table_name_main, $table_name_product_category, $table_name_product_ingredients);
        }

        //declaring variables and querying db for needed information
        $categories;
        $produktliste_results;
        $ingredients;

        //query the db for categories
        $categories =   $wpdb->get_results("SELECT * FROM $table_name_product_category", ARRAY_A)
        or die ( $wpdb->last_error );

        //query the db for all products
        $produktliste_results =  $wpdb->get_results("
            SELECT id, category, product_name, price, price_type, picture_url, picture_alt_tag
            FROM    {$table_name_main}
        ", ARRAY_A)or die ( $wpdb->last_error );

        //query db for data on ingredients and allergens
        $ingredients = $wpdb->get_results("
            SELECT product_id, ingredient_name, allergen
            FROM    {$table_name_product_ingredients}
        ", ARRAY_A)or die ( $wpdb->last_error );

        show_adminpage_forms($categories, $post_values);
        show_produktliste_admin($categories, $produktliste_results, $ingredients);
    }

    add_menu_page(
        'Produktliste Plugin Side',
        'Produktliste Plugin',
        'manage_options',
        'produktliste-plugin',
        'produktliste_init',
        'dashicons-admin-plugins'
    );
}
?>