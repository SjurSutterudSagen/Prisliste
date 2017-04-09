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
      weight varchar(255) NOT NULL,
      picture_id mediumint(9) NOT NULL,
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
            weight varchar(255) NOT NULL,
            picture_id mediumint(9) NOT NULL,
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

//function for update check on the db for new plugin version
function produktliste_update_db_check() {
    global $produktliste_db_version;
    if (get_site_option( 'produktliste_db_version' ) != $produktliste_db_version ) {
        produktliste_install();
    }
}

//function for adding user role capabilities
function add_plugin_caps() {
    $role = get_role( 'editor' );
    $role->add_cap( 'produktliste_cap' );

    $role = get_role( 'administrator' );
    $role->add_cap( 'produktliste_cap' );

    if ( get_role( 'shop_manager' !== NULL )) {
        $role = get_role( 'shop_manager' );
        $role->add_cap( 'produktliste_cap' );
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
    if( ($hook != 'toplevel_page_produktliste') && ($hook != 'produktliste_page_produktliste-produkter') ) {
        return;
    }

    //enqueueing font awsome
    wp_register_style( 'load_font_awsome_min_css', plugins_url('/vendor/font-awesome-4.7.0/css/font-awesome.min.css', __FILE__) );
    wp_enqueue_style( 'load_font_awsome_min_css' );

    //enqueueing Toastr
    wp_register_style( 'load_toastr_min_css', plugins_url('/vendor/toastr/toastr.min.css', __FILE__) );
    wp_enqueue_style( 'load_toastr_min_css' );

    wp_register_style( 'load_produktliste_css_admin', plugins_url('/style/produktliste_admin.css', __FILE__) );
    wp_enqueue_style( 'load_produktliste_css_admin' );
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
    if( ($hook != 'toplevel_page_produktliste') && ($hook != 'produktliste_page_produktliste-produkter') ) {
        return;
    }

    wp_register_script( 'produktliste_script', plugins_url( '/js/produktliste_admin.js', __FILE__ ), array( 'jquery' ) );
    wp_enqueue_script('produktliste_script');

    wp_register_script( 'toastr_script', plugins_url( '/vendor/toastr/toastr.min.js', __FILE__ ), array( 'jquery' ) );
    wp_enqueue_script('toastr_script');
}

/****************************************
 *   Functions for Outputting to HTML   *
 ***************************************/

//function for building the html part for category inputs on the admin page for the plugin
function show_create_new_or_edit_categories($categories, $post_values_cat) {
    if ( empty($categories) ) {
        ?>
        <div class="form_wrapper_category">
            <h2>Kategorier</h2>
            <form method="POST">
                <input type="hidden" name="new_category" value="true" />
                <?php wp_nonce_field( 'produktliste_new_category_update', 'produktliste_new_category_form' ); ?>
                <table>
                    <tbody>
                    <tr>
                        <th><label for="category_input"></label>Ny Kategori</th>
                        <td><input name="category_input" type="text" value="<?php
                            if ( isset($post_values_cat['category_input']) ){
                                echo esc_attr( $post_values_cat['category_input'] );
                            }?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <p class="submit">
                                <input type="submit" name="new_category_submit" class="button button-primary button-large" value="Lagre ny kategori">
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </form>
        </div>
        <?php
    } else {
        ?>
        <div class="form_wrapper_category">
            <h2>Kategorier</h2>
            <form method="POST">
                <input type="hidden" name="new_category" value="true" />
                <?php
                wp_nonce_field( 'produktliste_new_category_update', 'produktliste_new_category_form' );
                if ( isset($post_values_cat['category']) ) {
                    echo '<input type="hidden" name="cat_id" value="'. esc_attr( $post_values_cat['category'] ) .'" />';
                } else {
                    echo '<input type="hidden" name="cat_id" value="" />';
                }
                ?>
                <table class="form-table">
                    <tbody>
                    <tr><?php
                        if ( isset($post_values_cat['editing_status']) && ($post_values_cat['editing_status'] === TRUE) ) {
                            ?>
                            <th><label for="category_input"></label>Endre Kategori</th>
                            <input type="hidden" name="editing_status" value="true" />
                            <?php
                        } else {
                            ?>
                            <th><label for="category_input"></label>Ny Kategori</th>
                            <input type="hidden" name="editing_status" value="false" />
                            <?php
                        }
                        ?>
                        <td><input name="category_input" type="text" value="<?php
                            if ( isset($post_values_cat['category']) ) {
                                echo esc_attr( $post_values_cat['category_name'] );
                            }?>" class="regular-text" />
                            <?php
                            if ( isset($post_values_cat['errormessage']) && $post_values_cat['errormessage'] !== 0 ) {
                                echo '<p>' . $post_values_cat['errormessage'] . '</p>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <p class="submit">
                                <input type="submit" name="new_category_submit" class="button button-primary button-large" value="Lagre kategori">
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <form method="POST" id="delete_category_form">
                <input type="hidden" name="edit_or_delete_category" value="true" />
                <?php
                wp_nonce_field( 'produktliste_edit_or_delete_category_update', 'produktliste_edit_or_delete_category_form' );
                ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><label for="edit_or_delete_category_select"></label>Nåværende kategorier</th>
                        <td>
                            <select name="edit_or_delete_category_select" type="text" value="" class="regular-text">
                                <?php
                                foreach ($categories as $category) {
                                    if ($post_values_cat['category'] === $category['category_id']) {
                                        echo "<option value='" . esc_attr( $category['category_id'] ) . "' selected='selected'>" . esc_html( $category['category_name'] ) . "</option>";
                                    } else {
                                        echo "<option value='" . esc_attr( $category['category_id'] ) . "'>" . esc_html( $category['category_name'] ) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <p class="submit">
                                <input type="submit" name="edit_category_submit" class="button button-primary" value="Endre kategori">
                            </p>
                            <p class="submit">
                                <input type="submit" name="delete_category_submit" class="button button-warning" value="Slett kategori">
                            </p>
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <?php
    }
}

//function for building the html part for frontend productliste
function show_produktliste() {
    global $wpdb;
    $categories = NULL;
    $produktliste_results = NULL;
    $ingredients = NULL;

    $table_name_main = $wpdb->prefix . "produktliste_produkter";
    $table_name_product_category = $wpdb->prefix . "produktliste_kategorier";
    $table_name_product_ingredients = $wpdb->prefix . "produktliste_produkt_ingredienser";

    //query the db for categories
    $categories =   $wpdb->get_results("SELECT * FROM $table_name_product_category ORDER BY category_name", ARRAY_A);

    //query the db for all products with category name
    $produktliste_results =  $wpdb->get_results("
        SELECT id, category, product_name, price, price_type, weight, picture_id, picture_alt_tag
        FROM    {$table_name_main}
        ORDER BY product_name
    ", ARRAY_A);

    //query db for data on ingredients and allergens
    $ingredients = $wpdb->get_results("
        SELECT product_id, ingredient_name, allergen
        FROM    {$table_name_product_ingredients}
        ORDER BY ingredient_name
    ", ARRAY_A);

    //building the html output
    if ( empty($categories) || empty($produktliste_results) ) {
        ?>
        <div class="produktliste_wrapper">
            <div><h1 class="hv-header_first">Produkter</h1></div>
            <div><p>Det er ikke lagt til noen produkter i produktlisten.</p></div>
        </div>
        <?php
    } else {
        ?>
        <div class="produktliste_wrapper">
            <div><h1 class="hv-header hv-header_first">Produkter</h1></div>
            <?php
            //loop for each category
            foreach ($categories as $category) {
                $product_in_category_count = 0;
                $output = NULL;

                $output = "<div class='produktliste_category_wrapper'>
                    <h2>" . esc_html( $category['category_name'] ) . "</h2>";

                foreach ($produktliste_results as $product) {
                    if ($category['category_id'] === $product['category']) {
                        $product_in_category_count++;
                        $output .= "
                            <div class='accordion'>
                                <div class='accordion-thumbnail-div'>
                                    <img src='" . wp_get_attachment_url( $product['picture_id'] ) . "'
                                        alt='" . esc_attr( $product['picture_alt_tag'] ) . "'
                                         class='accordion-thumbnail'
                                    />
                                </div>
                                <div class='accordion-content'>" . esc_html( $product['product_name'] ) . "</div>
                                <div class='accordion-content'>" . esc_html( $product['price'] );
                                    if ( $product['price_type'] == 0 ) {
                                        $output .= "kr/kg";
                                    } elseif ($product['price_type'] == 1) {
                                        $output .= "kr/stk";
                                    }
                                $output .= "</div>
                                <div class='accordion-content'><i class='fa fa-chevron-down icon-placement' aria-hidden='true'></i></div>
                            </div>
                            <div class='panel'>
                                <img src='" . wp_get_attachment_url( $product['picture_id'] ) . "'
                                        alt='" . esc_attr( $product['picture_alt_tag'] ) . "'
                                         class='accordion-image'
                                    />
                                <div class='accordion-list'>
                                    <div>
                                        <h3>
                                            Vekt
                                        </h3>
                                        <p>" . esc_html( $product['weight'] ) . "</p>
                                    </div>
                                    <div><h3>Ingredienser</h3></div>
                                    <div>
                                        <ul>";
                                            //loop for building the ingredients list
                                            foreach ($ingredients as $ingredient) {
                                                if ($product['id'] === $ingredient['product_id']) {
                                                    if ( $ingredient['allergen'] == 1 ) {
                                                        $output .= "<li><b>" . esc_html( $ingredient['ingredient_name'] ) . "</b></li>";
                                                    } else {
                                                        $output .= "<li>" . esc_html( $ingredient['ingredient_name'] ) . "</li>";
                                                    }
                                                }
                                            }
                                            $output .="
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        ";
                    }
                }
                if ($product_in_category_count > 0) {
                    echo $output;
                }
            }
            ?>
        </div>
        <?php
    }
}

//function for building the html part for admin page productliste
function show_produktliste_admin($categories, $produktliste_results, $ingredients) {
    if ( empty($produktliste_results) ) {
        if ( empty($categories) ) {
            //do nothing since no categories exists
        } else {
            ?>
            <div class="produktliste_wrapper">
                <div><h1 class="hv-header hv-header_first">Eksisterende produkter i Produktlisten</h1></div>
                <div><p>Det er ikke lagt til noen produkter i produktlisten.</p></div>
            </div>
            <?php
        }

    } else {
        //building the html output
        ?>
        <div class="produktliste_wrapper">
            <div><h2 class="hv-header_first">Eksisterende produkter i Produktlisten</h2></div>
            <?php
            //loop for each category
            foreach ($categories as $category) {
                $product_in_category_count = 0;
                $output = NULL;

                $output = "<div class='produktliste_category_wrapper'>
                        <h2>" . esc_html( $category['category_name'] ) . "</h2>";

                $output2 = "<div class='produktliste_category_wrapper'>
                        <h2>" . esc_html( $category['category_name'] ) . "</h2>
                        <p>Er laget, men har ingen produkter.</p>";

                foreach ($produktliste_results as $product) {
                    if ($category['category_id'] === $product['category']) {
                        $product_in_category_count++;
                        $output .= "
                                <div class='accordion'>
                                    <div class='accordion-thumbnail-div'>
                                        <img src='" . wp_get_attachment_url( $product['picture_id'] ) . "'
                                            alt='" . esc_attr( $product['picture_alt_tag'] ) . "'
                                             class='accordion-thumbnail'
                                        />
                                    </div>
                                    <div class='accordion-content'>" . esc_html( $product['product_name'] ) . "</div>
                                    <div class='accordion-content'>" . esc_html( $product['price'] );
                        if ( $product['price_type'] == 0 ) {
                            $output .= "kr/kg";
                        } elseif ($product['price_type'] == 1) {
                            $output .= "kr/stk";
                        }
                        $output .= "</div>
                                    <div class='accordion-content'><i class='fa fa-chevron-down icon-placement' aria-hidden='true'></i></div>
                                </div>
                                <div class='panel'>
                                    <img src='" . wp_get_attachment_url( $product['picture_id'] ) . "'
                                            alt='" . esc_attr( $product['picture_alt_tag'] ) . "'
                                             class='accordion-image'
                                        />
                                    <div class='accordion-list'>
                                        <div>
                                            <h3>
                                                Vekt
                                            </h3>
                                            <p>" . esc_html( $product['weight'] ) . "</p>
                                        </div>
                                        <div><h3>Ingredienser</h3></div>
                                        <div>
                                            <ul>";
                        //loop for building the ingredients list
                        foreach ($ingredients as $ingredient) {
                            if ($product['id'] === $ingredient['product_id']) {
                                if ( $ingredient['allergen'] == 1 ) {
                                    $output .= "<li><b>" . esc_html( $ingredient['ingredient_name'] ) . "</b></li>";
                                } else {
                                    $output .= "<li>" . esc_html( $ingredient['ingredient_name'] ) . "</li>";
                                }
                            }
                        }
                        $output .="
                                            </ul>
                                        </div>
                                        <div class='buttons_wrapper'>
                                            <div>
                                                <form method='POST'>
                                                    <input type='hidden' name='edit_product' value='true' />" .
                                                    wp_nonce_field( 'produktliste_product_edit_update', 'produktliste_product_edit_form' ) . "
                                                    <p class='submit'>
                                                        <input type='hidden' name='product_id' value='" . esc_html( $product['id'] ) . "' />
                                                        <input type='submit' name='edit_product_submit' class='button button-primary' value='Endre'>
                                                    </p>
                                                </form>
                                            </div>
                                            <div>
                                                <form method='POST' id='delete_product_form'>
                                                    <input type='hidden' name='delete_product' value='true' />" .
                                                    wp_nonce_field( 'produktliste_product_delete_update', 'produktliste_product_delete_form' ) . "
                                                    <p class='submit'>
                                                        <input type='hidden' name='product_id' value='" . esc_html( $product['id'] ). "' />
                                                        <input type='submit' name='delete_product_submit' class='button button-warning' value='Slett'>
                                                    </p>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ";
                    }
                }
                if ($product_in_category_count > 0) {
                    echo $output;
                } else {
                    echo $output2;
                }
            }
            ?>
        </div>
        <?php
    }
}

function show_adminpage_product_forms($categories, $post_values) {
    if (!empty($categories)) {
        ?>
        <div class="form_wrapper_product">
            <?php
            if ( isset($post_values['editing_status']) &&  ($post_values['editing_status'] === TRUE) ) {
                echo '<h2>Endre produkt</h2>';
            } else {
                echo '<h2>Legg til nytt produkt</h2>';
            }
            ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="main_form_updated" value="true" />
                <?php wp_nonce_field( 'produktliste_update', 'produktliste_form' );
                if ( isset($post_values['editing_status']) &&  ($post_values['editing_status'] === TRUE) ) {
                    echo '<input type="hidden" name="editing_status" value="true" />';
                } else {
                    echo '<input type="hidden" name="editing_status" value="false" />';
                }
                if ($post_values['product_id']) {
                    echo '<input type="hidden" name="prod_id" value="'. esc_attr( $post_values["product_id"] ) .'" />';
                } else {
                    echo '<input type="hidden" name="prod_id" value="" />';
                }
                ?>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><label for="productname">Produktnavn</label></th>
                        <td><input name="productname" type="text" value="<?php
                            if ($post_values['productname']){
                                echo esc_attr( $post_values['productname'] );
                            }?>" class="regular-text" />
                            <?php
                            //if there is a product name error message
                            if ( isset($post_values['validation_errors']['product_name']) ) {
                                echo $post_values['validation_errors']['product_name'];
                            }
                            ?>
                        </td>

                    </tr>
                    <tr>
                        <th><label for="category">Kategori</label></th>
                        <td>
                            <select name="category" type="text" value="" class="regular-text">
                                <?php
                                foreach ($categories as $category) {
                                    if ($post_values['category'] === $category['category_id']) {
                                        echo "<option value='" . esc_attr( $category['category_id'] ) . "' selected='selected'>" . esc_html( $category['category_name'] ) . "</option>";
                                    } else {
                                        echo "<option value='" . esc_attr( $category['category_id'] ) . "'>" . esc_html( $category['category_name'] ) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="price">Pris (bare tall er tillatt).</label></th>
                        <td>
                            <input name="price" type="text" value="<?php
                            if ($post_values['price']){
                                echo esc_attr( $post_values['price'] );
                            }?>" class="regular-text" />
                            <?php
                            //if there is a price error message
                            if ((isset($post_values['validation_errors']['price'])) && count($post_values['validation_errors']['price'])) {
                                echo $post_values['validation_errors']['price'];
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="price_type">Pristype</label></th>
                        <td>
                            <select name="price_type" type="text" value="" class="regular-text">
                                <?php
                                if ($post_values['price_type'] === '0') {
                                    echo "<option value='0' selected='selected'>kr/kg</option>";
                                    echo "<option value='1'>kr/stk</option>";
                                } elseif ($post_values['price_type'] === '1') {
                                    echo "<option value='0'>kr/kg</option>";
                                    echo "<option value='1' selected='selected'>kr/stk</option>";
                                } else {
                                    echo "<option value='0'>kr/kg</option>";
                                    echo "<option value='1'>kr/stk</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="weight">Vekt</label></th>
                        <td>
                            <input name="weight" type="text" value="<?php
                            if ($post_values['weight']){
                                echo esc_attr( $post_values['weight'] );
                            }?>" class="regular-text" />
                            <?php
                            //if there is a weight error message
                            if ((isset($post_values['validation_errors']['weight'])) && count($post_values['validation_errors']['weight'])) {
                                echo $post_values['validation_errors']['weight'];
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="product_image">Last opp bilde</label></th>
                        <td>
                            <input type="file" name="product_image">
                            <?php
                            //if there is a product image error message
                            if ((isset($post_values['validation_errors']['product_image'])) && count($post_values['validation_errors']['product_image'])) {
                                echo $post_values['validation_errors']['product_image'];
                            }
                            ?>
                        </td>
                        <?php
                        if ( isset($post_values['image']) && ( !is_array($post_values['image']) ) ) {
                            ?>
                            <tr>
                                <th>Nåværende bilde</th>
                                <td>
                                    <img src="<?php echo wp_get_attachment_url( $post_values['image'] ); ?>" class="produktliste_existing_image"/>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <th><label for="alt_txt">Kort og beskrivende tekst av selve bildet.</label></th>
                        <td><input name="alt_txt" type="text" value="<?php
                            if ($post_values['alt_txt']){
                                echo esc_attr( $post_values['alt_txt'] );
                            }?>" class="regular-text" />
                            <?php
                            //if there is a iamge alt-txt error message
                            if ((isset($post_values['validation_errors']['alt_txt'])) && count($post_values['validation_errors']['alt_txt'])) {
                                echo $post_values['validation_errors']['alt_txt'];
                            }
                            ?>
                        </td>

                    </tr>
                    </tbody>
                </table>
                <div class="ingredients-div-container">
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th><h3>Ingredienser</h3></th>
                            <td></td>
                            <td><p class="float-right allergen-titel">Allergen?</p></td>
                        </tr>
                        <?php
                        //if no ingredients were added for a new product
                        if ((isset($post_values['validation_errors']['ingredients_number'])) && $post_values['validation_errors']['ingredients_number']) {
                            echo '<tr><th></th><td>';
                            echo $post_values['validation_errors']['ingredients_number'];
                            echo '</td></tr>';
                        }
                        //loop for ingredients
                        if ( isset($post_values['ingredient']) && (count($post_values['ingredient']) !== 0) ) {
                            for ($i = 0; $i < count($post_values['ingredient']); $i++) {
                                echo "<tr>
                                    <th><label for='ingredient[" . ($i) . "]'>Ingrediens " . ($i+1) . "</label></th>
                                    <td>
                                        <input name='ingredient[" . ($i) . "][" . 'ingredient_name' . "]' type='text' value='" . esc_attr($post_values['ingredient'][($i)]['ingredient_name']) . "' class='regular-text productlist_ingredient' />";
                                        if ( isset($post_values['validation_errors']['ingredient'][$i]['ingredient_name'])
                                                && $post_values['validation_errors']['ingredient'][$i]['ingredient_name'] ) {
                                            echo $post_values['validation_errors']['ingredient'][$i]['ingredient_name'];
                                        }

                                    echo "</td>";
                                    if ( $post_values['ingredient'][$i]['allergen'] === 1 ) {
                                        echo "<td><p class='allergen-titel-mobile'>Allergen?</p><div class='allergen-checkbox-div'><input name='ingredient[" . ($i) . "][" . 'allergen' . "]' type='checkbox' value='1' class='regular-text' checked='checked'/></div></td>";
                                    } else {
                                        echo "<td><p class='allergen-titel-mobile'>Allergen?</p><div class='allergen-checkbox-div'><input name='ingredient[" . ($i) . "][" . 'allergen' . "]' type='checkbox' value='1' class='regular-text' /></div></td>";
                                    }
                                    echo "<td><input type='hidden' name='ingredient[" . ($i) . "][" . 'remove' . "]' value='0'/><button class='ingredient-delete-button button'>Slett</button></td>";
                                    if ( isset($post_values['ingredient'][$i]['ingredient_id']) ) {
                                        echo "<input type='hidden' name='ingredient[" . ($i) . "][" . 'ingredient_id' . "]' value='" . esc_attr($post_values['ingredient'][($i)]['ingredient_id']) . "' />";
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
                </div>
                <p class="submit">
                    <input type="submit" name="submit" class="button button-primary button-large" value="Lagre Produkt">
                </p>
            </form>
            <form method="POST">
                <p class="submit">
                    <input type="submit" name="reset" class="button" value="Reset siden">
                </p>
            </form>
        </div>
        <?php
    } else {
        ?>
        <div class="produktliste_wrapper">
            <div><h2 class="hv-header hv-header_first">Mangler kategorier!</h2></div>
            <div><p>Det er ikke lagt til noen kategorier i produktlisten. Vennligst gjør dette under Kategorier-menyen først.</p></div>
        </div>
        <?php
    }
}

/**********************************************************
 *   Functions for building and processing the adminpage  *
 *********************************************************/

//processing POST to the plugin page from the category form (new category)
function produktliste_handle_post_new_category($wpdb, $table_name_product_category, $post_values_cat) {
    if(
        ! isset( $_POST['produktliste_new_category_form'] ) ||
        ! wp_verify_nonce( $_POST['produktliste_new_category_form'], 'produktliste_new_category_update' ) ||
        ! current_user_can('produktliste_cap')
    ){  ?>
        <div class="error">
            <p>Sikkerhetsjekk feilet: Din nonce var ikke korrekt. Vennligst prøv igjen.</p>
        </div>
        <?php
        exit;
    } else {
        // Processing the POST
        if (isset($_POST['editing_status'])) {
            $editing_status = sanitize_text_field($_POST['editing_status']);
            if (strtolower($editing_status) === 'true') {
                $post_values_cat['editing_status'] = TRUE;
            }
        }

        //sanetizing and storing the $_POST values
        $post_values_cat['category'] = absint($_POST['cat_id']);
        $post_values_cat['category_name'] = sanitize_text_field($_POST['category_input']);

        //validate postdata
        $post_values_cat['errormessage'] = validate_category_name( $wpdb, $table_name_product_category, $post_values_cat['category_name'] );
        if ( count($post_values_cat['errormessage']) !== 0  ) {
            ?>
            <script>
              toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-center",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
              };
              toastr.warning('<br /><br />Vennligst fiks feilene!<br /><br />');
            </script>

            <?php
            return $post_values_cat;
        } else {
            //new product
            if ( !isset($post_values_cat['editing_status']) || !($post_values_cat['editing_status'] === TRUE) ) {
                //saving new category
                $wpdb->insert( $table_name_product_category, array(
                    'category_name' => $post_values_cat['category_name']
                    ), array( '%s')
                );

            } else {
                //saving edit category
                $wpdb->update( $table_name_product_category,
                    array(
                        'category_name' => $post_values_cat['category_name']
                    ),  array('category_id' => $post_values_cat['category']),
                    array( '%s' ),
                    array( '%d' )
                );
            }
        }

        //outputting the success message
        ?>
        <script>
          toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
          };
          toastr.success('<br /><br />Kategorien er lagret<br /><br />');
        </script>
        <?php
    }
}
//processing POST to the plugin page from the category form (edit/delete category)
function produktliste_handle_post_edit_or_delete_category($wpdb, $table_name_main, $table_name_product_category, $post_values_cat) {
    if(
        ! isset( $_POST['produktliste_edit_or_delete_category_form'] ) ||
        ! wp_verify_nonce( $_POST['produktliste_edit_or_delete_category_form'], 'produktliste_edit_or_delete_category_update' ) ||
        ! current_user_can('produktliste_cap')
    ){  ?>
      <script>
        toastr.options = {
          "closeButton": false,
          "debug": false,
          "newestOnTop": false,
          "progressBar": false,
          "positionClass": "toast-top-center",
          "preventDuplicates": false,
          "onclick": null,
          "showDuration": "300",
          "hideDuration": "1000",
          "timeOut": "5000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
        };
        toastr.warning('<br /><br />Sikkerhetsjekk feilet: Din nonce var ikke korrekt. Vennligst prøv igjen.<br /><br />');
      </script>

        <?php
        exit;
    } else {
        $cat_id = NULL;
        $cat = NULL;
        // Processing the POST
        if ( isset($_POST['edit_category_submit']) ) { //editing a category


            $cat_id = absint($_POST['edit_or_delete_category_select']);
            $cat = $wpdb->get_row( $wpdb->prepare( "
                SELECT category_id, category_name
                FROM {$table_name_product_category}
                WHERE category_id = %d", $cat_id), ARRAY_A) or die ( 'Det har skjedd en feil. Vennligst prøv igjen.' );

            $post_values_cat['category'] = $cat_id;
            $post_values_cat['category_name'] = $cat['category_name'];

            $post_values_cat['editing_status'] = TRUE;
            return $post_values_cat;

        } elseif ( isset($_POST['delete_category_submit']) ) { //deleting a category
            $cat_id = absint($_POST['edit_or_delete_category_select']);
            $cat = $wpdb->get_row( $wpdb->prepare( "
                SELECT category
                FROM {$table_name_main}
                WHERE category = %d", $cat_id), ARRAY_A);

            if ( empty($cat) ) {
                //no products in the category, safe to delete
                $wpdb->delete( $table_name_product_category, array(
                        'category_id' => $cat_id
                    ), array( '%d' ) )
                    or die ( 'Det har skjedd en feil. Vennligst prøv igjen.' );
                ?>
                <script>
                  toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-center",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                  };
                  toastr.success('<br /><br />Kategori slettet.<br /><br />');
                </script>
                <?php

            } else {
                //products in the category, not safe to delete
                ?>
                <script>
                  toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-center",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "10000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                  };
                  toastr.warning('<br /><br />Det er produkter i denne kategorien, og kategorier kan bare slettes hvis de ikke inneholder produkter.<br /><br />');
                </script>
                <?php
                return $post_values_cat;
            }



        } else {
            ?>
            <script>
              toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-center",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
              };
              toastr.warning('<br /><br />Det har skjedd en feil. Vennligst prøv igjen.<br /><br />');
            </script>
            <?php
            }
    }
}

//processing POST to the plugin page from the main form
function produktliste_handle_post_main_form($wpdb, $table_name_main, $table_name_product_ingredients, $post_values) {
    if(
        ! isset( $_POST['produktliste_form'] ) ||
        ! wp_verify_nonce( $_POST['produktliste_form'], 'produktliste_update' ) ||
        ! current_user_can('produktliste_cap')
    ){ ?>
      <script>
        toastr.options = {
          "closeButton": false,
          "debug": false,
          "newestOnTop": false,
          "progressBar": false,
          "positionClass": "toast-top-center",
          "preventDuplicates": false,
          "onclick": null,
          "showDuration": "300",
          "hideDuration": "1000",
          "timeOut": "5000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
        };
        toastr.warning('<br /><br />Sikkerhetsjekk feilet: Din nonce var ikke korrekt. Vennligst prøv igjen.<br /><br />');
      </script>
       <?php
        exit;
    } else {
        function delete_specified_ingredients($wpdb, $table_name, $stored_ingredients_array, $post_ingredients_array) {
            foreach ( $post_ingredients_array as $post_ingredient ) {
                foreach ( $stored_ingredients_array as $stored_ingredient) {
                    if ( (isset($post_ingredient['ingredient_id']) && isset($stored_ingredient['id']))
                        && ($post_ingredient['ingredient_id'] === absint($stored_ingredient['id'])) ) {
                        if ( isset($post_ingredient['remove']) && ($post_ingredient['remove'] === 1) ) {
                            //delete the correct ingredients
                            $wpdb->delete( $table_name, array(
                                'ID' => absint($stored_ingredient['id'])
                            ), array( '%d' ) )
                            or die ( 'Det har skjedd en feil. Vennligst prøv igjen.' );
                            break;
                        }
                    }
                }
            }
        }

        // Handle our form data
        //updating editing status for displaying correct text
        $editing_status = sanitize_text_field($_POST['editing_status']);
        if (strtolower($editing_status) === 'true') {
            $post_values['editing_status'] = TRUE;
        }

        //sanetizing and storing the $_POST values
        $post_values['product_id'] = absint($_POST['prod_id']);
        $post_values['productname'] = sanitize_text_field($_POST['productname']);
        $post_values['category'] = absint($_POST['category']);
        $post_values['price'] = sanitize_text_field($_POST['price']); //using sanitize_text_field and not absint because absint changes a string of only characters to 0.
        $post_values['price_type'] = absint($_POST['price_type']);
        $post_values['weight'] = sanitize_text_field($_POST['weight']);
        $post_values['alt_txt'] = sanitize_text_field($_POST['alt_txt']);

        //validating the inputs as they are declared
        if (validate_product_name($post_values['productname']) !== NULL) {
            $post_values['validation_errors']['product_name'] = validate_product_name($post_values['productname']);
        }
        if (validate_price($post_values['price']) !== NULL) {
            $post_values['validation_errors']['price'] = validate_price($post_values['price']);
        }
        if (validate_weight($post_values['weight']) !== NULL) {
            $post_values['validation_errors']['weight'] = validate_weight($post_values['weight']);
        }
        if (validate_image_alt_txt($post_values['alt_txt']) !== NULL) {
            $post_values['validation_errors']['alt_txt'] = validate_image_alt_txt($post_values['alt_txt']);
        }

        //loop for sanitizing ingredients array and declaring ingredients validation errors variables
        if ( isset($_POST['ingredient']) ) {
            $count = 0;
            foreach ($_POST['ingredient'] as $ingredient) {
                $post_values['ingredient'][$count]['ingredient_name'] = sanitize_text_field($ingredient['ingredient_name']);
                if ( isset($ingredient['allergen']) ) {
                    $post_values['ingredient'][$count]['allergen'] = 1;
                } else {
                    $post_values['ingredient'][$count]['allergen'] = 0;
                }
                if ( isset($ingredient['ingredient_id']) ) {
                    if ( absint($ingredient['ingredient_id']) !== 0 ) {
                        $post_values['ingredient'][$count]['ingredient_id'] = absint($ingredient['ingredient_id']);
                    }
                }
                $post_values['ingredient'][$count]['remove'] = absint($ingredient['remove']);

                //only validating ingredient name since allergen is a boolean and ingredient_id is an id number(
                if ( $post_values['ingredient'][$count]['remove'] === 0 ) {
                    if (validate_ingredient($post_values['ingredient'][$count]['ingredient_name']) !== NULL) {
                        $post_values['validation_errors']['ingredient'][$count]['ingredient_name'] = validate_ingredient($post_values['ingredient'][$count]['ingredient_name']);
                    }
                }
                $count++;
            }
        } else {
            $post_values['validation_errors']['ingredients_number'] = '<p class="custom-error-message">Mangler ingredienser</p>';
        }

        if ( isset($post_values['product_id']) && ($post_values['product_id'] !== 0) ) {
            $product_ingredients = $wpdb->get_results( $wpdb->prepare( "
            SELECT i.id
            FROM {$table_name_main} m, {$table_name_product_ingredients} i
            WHERE m.id = i.product_id
            AND m.id = %d", $post_values['product_id']), ARRAY_A)or die ( 'Det har skjedd en feil. Vennligst prøv igjen.' );
        }

        //if the user is editing an existing product and adds a new image OR creating a new product; add the image to post_values variable and validate it
        if ( ( ( isset($post_values['editing_status']) && ($post_values['editing_status'] === TRUE) ) && ($_FILES['product_image']['error'] === 0) )
            || ( !isset($post_values['editing_status']) ) ) {
            $post_values['image'] = $_FILES['product_image'];

            if (validate_image($post_values['image']) !== NULL) {
                $post_values['validation_errors']['product_image'] = validate_image($post_values['image']);
            }

            //output message depending on validation errors or not
            if ( isset($post_values['validation_errors']) && count($post_values['validation_errors']) !== 0) {
                //if there are errors
                ?>
                <script>
                  toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-center",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                  };
                  toastr.warning('<br /><br />Vennligst fiks feilene!<br /><br />');
                </script>
                <?php
                return $post_values;

            } else {
                //there are no errors: storing to db and outputting the success message
                //adding functions for changing the file name
                add_filter('wp_handle_upload_prefilter', 'image_filename_change_upload_filter' );
                function image_filename_change_upload_filter( $img ){
                    $img_info = pathinfo( $img['name'] );
                    $img['name'] = sprintf('%s%s.%s','Produktliste-', current_time( 'Y-m-d-H-i-s' ), $img_info["extension"]);
                    return $img;
                }

                //store image
                $image_id = media_handle_upload('product_image', 0);
                //checking for error uploading the file
                if ( is_wp_error($image_id) ) {
                    ?>
                    <script>
                      toastr.options = {
                        "closeButton": false,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-top-center",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                      };
                      toastr.warning('<br /><br />Opplasting av bildet feilet. Vennligst prøv igjen.!<br /><br />');
                    </script>
                    <?php
                    return $post_values;
                }

                //new product
                if (!$post_values['editing_status'] === TRUE) {

                    //store new product with reference to image
                    $wpdb->insert( $table_name_main, array(
                            'product_name' => $post_values['productname'],
                            'category' => $post_values['category'],
                            'price' => $post_values['price'],
                            'price_type' => $post_values['price_type'],
                            'picture_id' => $image_id,
                            'picture_alt_tag' => $post_values['alt_txt'],
                            'weight' => $post_values['weight']
                        ), array( '%s', '%d', '%d', '%d', '%d', '%s', '%s' )
                    );
                    $id_of_new_product = $wpdb->insert_id;


                    foreach ($post_values['ingredient'] as $ingredient) {
                        if ($ingredient['remove'] !== 1) {
                            $wpdb->insert( $table_name_product_ingredients, array(
                                    'product_id' => $id_of_new_product,
                                    'ingredient_name' => $ingredient['ingredient_name'],
                                    'allergen' => $ingredient['allergen']
                                ), array( '%d', '%s', '%d' )
                            );
                        }
                    }
                } else {
                    //existing product with a new image
                    //grabbing old image id for deleting later
                    $old_image_id = $wpdb->get_row( $wpdb->prepare( "
                    SELECT picture_id
                    FROM {$table_name_main}
                    WHERE ID = %d", $post_values['product_id']), ARRAY_A)or die ( 'Det har skjedd en feil. Vennligst prøv igjen.' );

                    $wpdb->update( $table_name_main,
                            array(
                                'product_name' => $post_values['productname'],
                                'category' => $post_values['category'],
                                'price' => $post_values['price'],
                                'price_type' => $post_values['price_type'],
                                'picture_id' => $image_id,
                                'picture_alt_tag' => $post_values['alt_txt'],
                                'weight' => $post_values['weight']
                        ),  array('ID' => $post_values['product_id']),
                            array( '%s', '%d', '%d', '%d', '%d', '%s', '%s' ),
                            array( '%d' )
                    );

                    foreach ($post_values['ingredient'] as $ingredient) {
                        if ($ingredient['remove'] !== 1) {
                            //if new ingredient(no ingredient_id)
                            if (!$ingredient['ingredient_id']) {
                                $wpdb->insert( $table_name_product_ingredients, array(
                                    'product_id' => $post_values['product_id'],
                                    'ingredient_name' => $ingredient['ingredient_name'],
                                    'allergen' => $ingredient['allergen']
                                ), array( '%d', '%s', '%d' )
                                );
                            } else {
                                //else update(has ingredient id)
                                $wpdb->update( $table_name_product_ingredients,
                                    array(
                                        'ingredient_name' => $ingredient['ingredient_name'],
                                        'allergen' => $ingredient['allergen']),
                                    array('ID' => $ingredient['ingredient_id']),
                                    array( '%s', '%d' ),
                                    array( '%d' )
                                );
                            }
                        }
                        //delete old picture
                        wp_delete_attachment( $old_image_id['picture_id'] );
                    }
                    delete_specified_ingredients( $wpdb, $table_name_product_ingredients, $product_ingredients, $post_values['ingredient'] );
                }
                ?>
                <script>
                  toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-center",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                  };
                  toastr.success('<br /><br />Produkt er lagt til.<br /><br />');
                </script>
                <?php
            }
        } else {
            //existing product with no new image

            //output message depending on validation errors or not
            if ( isset($post_values['validation_errors']) && count($post_values['validation_errors']) !== 0){
                //if there are errors
                ?>
                <script>
                  toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-center",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                  };
                  toastr.warning('<br /><br />Vennligst fiks feilene!<br /><br />');
                </script>
                <?php
                return $post_values;

            } else {
                //there are no errors: storing to db and outputting the success message
                $wpdb->update( $table_name_main,
                    array(
                        'product_name' => $post_values['productname'],
                        'category' => $post_values['category'],
                        'price' => $post_values['price'],
                        'price_type' => $post_values['price_type'],
                        'picture_alt_tag' => $post_values['alt_txt'],
                        'weight' => $post_values['weight']
                    ),  array('ID' => $post_values['product_id']),
                    array( '%s', '%d', '%d', '%d', '%s', '%s' ),
                    array( '%d' )
                );

                foreach ($post_values['ingredient'] as $ingredient) {
                    if ($ingredient['remove'] !== 1) {
                        //if new ingredient(no ingredient_id)
                        if (!$ingredient['ingredient_id']) {
                            $wpdb->insert($table_name_product_ingredients, array(
                                'product_id' => $post_values['product_id'],
                                'ingredient_name' => $ingredient['ingredient_name'],
                                'allergen' => $ingredient['allergen']
                            ), array('%d', '%s', '%d')
                            );
                        } else {
                            //else update(has ingredient id)
                            $wpdb->update($table_name_product_ingredients,
                                array(
                                    'ingredient_name' => $ingredient['ingredient_name'],
                                    'allergen' => $ingredient['allergen']),
                                array('ID' => $ingredient['ingredient_id']),
                                array('%s', '%d'),
                                array('%d')
                            );
                        }
                    }
                }
                delete_specified_ingredients( $wpdb, $table_name_product_ingredients, $product_ingredients, $post_values['ingredient'] );
                ?>
                <script>
                  toastr.options = {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-center",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                  };
                  toastr.success('<br /><br />Produkt lagret.<br /><br />');
                </script>
                <?php
            }
        }
    }
}

//processing POST to the plugin page from the product edit button
function produktliste_handle_post_product_edit_form($wpdb, $table_name_main, $table_name_product_category, $table_name_product_ingredients, $post_values) {
    if(
        ! isset( $_POST['produktliste_product_edit_form'] ) ||
        ! wp_verify_nonce( $_POST['produktliste_product_edit_form'], 'produktliste_product_edit_update' ) ||
        ! current_user_can('produktliste_cap')
    ){ ?>
        <div class="error">
            <p>Sikkerhetsjekk feilet: Din nonce var ikke korrekt. Vennligst prøv igjen.</p>
        </div> <?php
        exit;
    } else {
        // Processing the POST
        //querying db for data on the product
        $product_id = absint($_POST['product_id']);
        $product = $wpdb->get_row( $wpdb->prepare( "
          SELECT m.id, m.category, c.category_name, m.product_name, m.price, m.price_type, m.weight, m.picture_id, m.picture_alt_tag
          FROM {$table_name_main} m, {$table_name_product_category} c
          WHERE m.category = c.category_id
          AND ID = %d", $product_id), ARRAY_A)or die ( 'Det har skjedd en feil. Vennligst prøv igjen.' );

        //querying db for data on the products ingredients
        $product_ingredients = $wpdb->get_results( $wpdb->prepare( "
          SELECT i.id, i.ingredient_name, i.allergen
          FROM {$table_name_main} m, {$table_name_product_ingredients} i
          WHERE m.id = i.product_id
          AND m.id = %d
          ORDER BY i.ingredient_name", $product_id), ARRAY_A)or die ( 'Det har skjedd en feil. Vennligst prøv igjen.' );

        //updating $post_values with correct values
        $post_values['product_id'] = $product['id'];
        $post_values['productname'] = $product['product_name'];
        $post_values['category'] = $product['category'];
        $post_values['price'] = $product['price'];
        $post_values['price_type'] = $product['price_type'];
        $post_values['weight'] = $product['weight'];
        $post_values['alt_txt'] = $product['picture_alt_tag'];
        $post_values['image'] = $product['picture_id'];

        for ($i = 0; $i < count($product_ingredients); $i++) {
            $post_values['ingredient'][$i]['ingredient_id'] = absint($product_ingredients[$i]['id']);
            $post_values['ingredient'][$i]['ingredient_name'] = $product_ingredients[$i]['ingredient_name'];
            $post_values['ingredient'][$i]['allergen'] = absint($product_ingredients[$i]['allergen']);
        }

        $post_values['editing_status'] = TRUE;
        return $post_values;
    }
}

//processing POST to the plugin page from the product delete button
function produktliste_handle_post_product_delete_form($wpdb, $table_name_main, $table_name_product_ingredients) {
    if(
        ! isset( $_POST['produktliste_product_delete_form'] ) ||
        ! wp_verify_nonce( $_POST['produktliste_product_delete_form'], 'produktliste_product_delete_update' ) ||
        ! current_user_can('produktliste_cap')
    ){ ?>
        <div class="error">
            <p>Sikkerhetsjekk feilet: Din nonce var ikke korrekt. Vennligst prøv igjen.</p>
        </div> <?php
        exit;
    } else {
        // Handle our form data
        $product_id = absint($_POST['product_id']);

        if ( $product_id !== 0 ) {
            //deleting the correct product

            //query db for product image id
            $product_image = $wpdb->get_row( $wpdb->prepare( "
                SELECT picture_id
                FROM {$table_name_main}
                WHERE ID = %d", $product_id), ARRAY_A)or die ( 'Det har skjedd en feil. Vennligst prøv igjen.' );

            //querying db for data on the products ingredients
            $product_ingredients = $wpdb->get_results( $wpdb->prepare( "
                SELECT i.id
                FROM {$table_name_main} m, {$table_name_product_ingredients} i
                WHERE m.id = i.product_id
                AND m.id = %d", $product_id), ARRAY_A)or die ( 'Det har skjedd en feil. Vennligst prøv igjen.' );

            //deleting the image
            wp_delete_attachment( $product_image['picture_id'] );

            //deleting the ingredients
            foreach ($product_ingredients AS $ingredient) {
                $wpdb->delete( $table_name_product_ingredients, array(
                    'ID' => $ingredient['id']
                ), array( '%d' ) )
                or die ( 'Det har skjedd en feil. Vennligst prøv igjen.' );
            }

            //deleting the product
            $wpdb->delete( $table_name_main, array(
                'ID' => $product_id
            ), array( '%d' ) )
            or die ( 'Det har skjedd en feil. Vennligst prøv igjen.' );

            //outputting the success message
            ?>
            <script>
              toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-center",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
              };
              toastr.success('<br /><br />Produktet er slettet<br /><br />');
            </script>
            <?php
        } else {
            //outputting error message
            ?>
            <script>
              toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-center",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
              };
              toastr.warning('<br /><br />Det har skjedd en feil. Vennligst prøv igjen.<br /><br />');
            </script>
            <?php
        }
    }
}

function produktliste_setup_menu() {

    //the code that creates the admin page of the plugin, contains the category options
    function produktliste_products() {
        $post_values = NULL;

        //declaring db variables
        global $wpdb;
        $table_name_main = $wpdb->prefix . "produktliste_produkter";
        $table_name_product_category = $wpdb->prefix . "produktliste_kategorier";
        $table_name_product_ingredients = $wpdb->prefix . "produktliste_produkt_ingredienser";

        //Checking for 'main_form_updated' to process the form on POST
        if( (isset($_POST['main_form_updated'])) && ($_POST['main_form_updated'] === 'true') ){
            $post_values = produktliste_handle_post_main_form($wpdb, $table_name_main, $table_name_product_ingredients, $post_values);
        }

        //Checking for 'edit_product' to process the form on POST
        if( (isset($_POST['edit_product'])) && ($_POST['edit_product'] === 'true') ){
            $post_values = produktliste_handle_post_product_edit_form($wpdb, $table_name_main, $table_name_product_category, $table_name_product_ingredients, $post_values);
        }

        //Checking for 'delete_product' to process the form on POST
        if( (isset($_POST['delete_product'])) && ($_POST['delete_product'] === 'true') ){
            produktliste_handle_post_product_delete_form($wpdb, $table_name_main, $table_name_product_ingredients);
        }

        //declaring variables and querying db for needed information
        $categories = NULL;
        $produktliste_results = NULL;
        $ingredients = NULL;

        //query the db for categories
        $categories =   $wpdb->get_results("SELECT * FROM $table_name_product_category ORDER BY category_name", ARRAY_A);

        //query the db for all products
        $produktliste_results =  $wpdb->get_results("
            SELECT id, category, product_name, price, price_type, weight, picture_id, picture_alt_tag
            FROM    {$table_name_main}
            ORDER BY product_name
        ", ARRAY_A);

        //query db for data on ingredients
        $ingredients = $wpdb->get_results("
            SELECT product_id, ingredient_name, allergen
            FROM    {$table_name_product_ingredients}
            ORDER BY ingredient_name
        ", ARRAY_A);


        ?>
        <div>
            <div>
                <h1>Administrator side for Produktliste plugin</h1>
            </div>
            <?php
            show_adminpage_product_forms($categories, $post_values);
            show_produktliste_admin($categories, $produktliste_results, $ingredients);
            ?>
        </div>
        <?php

    }

    function produktliste_init() {
        $post_values_cat = NULL;

        //declaring db variables
        global $wpdb;
        $table_name_main = $wpdb->prefix . "produktliste_produkter";
        $table_name_product_category = $wpdb->prefix . "produktliste_kategorier";

        //Checking for 'new_category' to process the form on POST
        if( (isset($_POST['new_category'])) && ($_POST['new_category'] === 'true') ){
            $post_values_cat = produktliste_handle_post_new_category($wpdb, $table_name_product_category, $post_values_cat);
        }

        //Checking for 'edit_or_delete_category' to process the form on POST
        if( (isset($_POST['edit_or_delete_category'])) && ($_POST['edit_or_delete_category'] === 'true') ){
            $post_values_cat = produktliste_handle_post_edit_or_delete_category($wpdb, $table_name_main, $table_name_product_category, $post_values_cat);
        }

        //declaring variables and querying db for needed information
        $categories = NULL;

        //query the db for categories
        $categories =   $wpdb->get_results("SELECT * FROM $table_name_product_category ORDER BY category_name", ARRAY_A);

        ?>
        <div>
            <div>
                <h1>Administrator side for Produktliste plugin</h1>
            </div>
            <?php
            show_create_new_or_edit_categories($categories, $post_values_cat);
            ?>
        </div>
        <?php
    }

    add_menu_page(
        'Produktliste Plugin Side',
        'Produktliste',
        'produktliste_cap',
        'produktliste',
        'produktliste_init',
        'dashicons-admin-plugins'
    );

    add_submenu_page(
        'produktliste',
        'Produktliste Kategorier',
        'Kategorier',
        'produktliste_cap',
        'produktliste'
    );

    add_submenu_page(
        'produktliste',
        'Produktliste Produkter',
        'Produkter',
        'produktliste_cap',
        'produktliste-produkter',
        'produktliste_products'
    );
}

/******************************************
 *   Functions for validating the inputs  *
 *****************************************/
function validate_category_name($wpdb, $table_name_product_category, $categoryname) {
    $cat = $wpdb->get_row( $wpdb->prepare( "
                SELECT category_name
                FROM {$table_name_product_category}
                WHERE category_name = %s", $categoryname), ARRAY_A);

    $preg_pattern = "/[^a-zA-ZøæåØÆÅ ]/";
    if ( $categoryname === "") {
        return '<p class="custom-error-message">Kategorinavn mangler.</p>';
    } elseif ( preg_match($preg_pattern, $categoryname) ) {
        return '<p class="custom-error-message">Bare store og små bokstaver og mellomrom er tillatt i kategorinavnet.</p>';
    } elseif ( (strlen($categoryname) < 3) || (strlen($categoryname) > 20) ) {
        return '<p class="custom-error-message">Kategorinavn må være mellom 3 og 20 bokstaver.</p>';
    } elseif ( !empty($cat) ) {
        return '<p class="custom-error-message">Kategorier må være unike.</p>';
    } else {
        return NULL;
    }
}

function validate_product_name($productname) {
    $preg_pattern = "/[^a-zA-ZøæåØÆÅ ]/";
    if ( $productname === "") {
        return '<p class="custom-error-message">Produktnavn mangler.</p>';
    } elseif ( preg_match($preg_pattern, $productname) ) {
        return '<p class="custom-error-message">Bare store og små bokstaver og mellomrom er tillatt i produktnavnet.</p>';
    } elseif ( (strlen($productname) < 3) || (strlen($productname) > 20) ) {
        return '<p class="custom-error-message">Produktnavn må være mellom 3 og 20 bokstaver.</p>';
    } else {
        return NULL;
    }
}

function validate_price($price) {
    $preg_pattern = "/d{1,5}/";
    if ($price === "") {
        return '<p class="custom-error-message">Pris mangler.</p>';
    } elseif ( preg_match($preg_pattern, $price) ) {
        return '<p class="custom-error-message">Bare tall er tillatt i prisen.</p>';
    } elseif ( (strlen($price) < 1) || (strlen($price) > 5) ) {
        return '<p class="custom-error-message">Pris må være mellom 1 og 5 tall.</p>';
    } else {
        return NULL;
    }
}

function validate_image_alt_txt($img_alt_txt) {
    $preg_pattern = "/[^a-zA-ZøæåØÆÅ0-9,. ]/";
    if ( $img_alt_txt === "") {
        return '<p class="custom-error-message">Alt-teksten til bildet mangler.</p>';
    } elseif ( preg_match($preg_pattern, $img_alt_txt) ) {
        return '<p class="custom-error-message">Bare store og små bokstaver, komma, punktum og tall er tillatt i alt-teksten til bildet.</p>';
    } elseif ( (strlen($img_alt_txt) < 3) || (strlen($img_alt_txt) > 100) ) {
        return '<p class="custom-error-message">Alt-teksten til bildet må være mellom 3 og 100 bokstaver.</p>';
    } else {
        return NULL;
    }
}

function validate_image($img) {
    $image_type_info = wp_check_filetype_and_ext($img['name'], $img['name']);

    $allowed_size = 5000000;

    $ext = NULL;
    switch ($image_type_info['type']) {
        case 'image/jpeg':
            $ext = 'jpeg';
            break;
        case 'image/jpg':
            $ext = 'jpg';
            break;
        case 'image/png';
            $ext = 'png';
            break;
        default:
            $ext = '';
            break;
    }

    if ($img['error'] !== 0) {
        return '<p class="custom-error-message">Dette er ikke et gyldig bildet.</p>';
    } elseif (!$ext) {
        return '<p class="custom-error-message">Bildet har ikke en gyldig filtype. Gyldige filetyper er: .jpeg .jpg .png.</p>';
    } elseif ($img['size'] > $allowed_size) {
        return '<p class="custom-error-message">Bildet er for stort. Maks tillatt størrelse er 5MB.</p>';
    } else {
        return NULL;
    }
}

function validate_ingredient($ingredient_name) {
    $preg_pattern = "/[^a-zA-ZøæåØÆÅ0-9(),.\&\%\- ]/";
    if ( $ingredient_name === "" ) {
        return '<p class="custom-error-message">Ingrediensnavnet mangler.</p>';
    } elseif ( preg_match($preg_pattern, $ingredient_name) ) {
        return '<p class="custom-error-message">Bare store og små bokstaver, komma, punktum, tall, bindestrek, parenteser, & og % er tillatt i ingrediensnavnet.</p>';
    } elseif ( (strlen($ingredient_name) < 3) || (strlen($ingredient_name) > 200) ) {
        return '<p class="custom-error-message">Ingrediensnavnet må være mellom 3 og 200 bokstaver.</p>';
    } else {
        return NULL;
    }
}

function validate_weight($weight) {
    $preg_pattern = "/[^a-zA-ZøæåØÆÅ0-9() ]/";
    if ( $weight === "" ) {
        return '<p class="custom-error-message">Vekt mangler.</p>';
    } elseif ( preg_match($preg_pattern, $weight) ) {
        return '<p class="custom-error-message">Bare store og små bokstaver, tall og mellomrom er tillatt i vekten.</p>';
    } elseif ( (strlen($weight) < 3) || (strlen($weight) > 20) ) {
        return '<p class="custom-error-message">Vekt må være mellom 3 og 20 bokstaver.</p>';
    } else {
        return NULL;
    }
}
?>
