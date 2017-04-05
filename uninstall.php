<?php
/**
 * Created by PhpStorm.
 * User: Sjur Sutterud Sagen
 * Date: 16.03.2017
 * Time: 14.17
 */

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

//drop custom database tables
global $wpdb;

$table_name_main = $wpdb->prefix . "produktliste_produkter";
$table_name_product_category = $wpdb->prefix . "produktliste_kategorier";
$table_name_product_ingredients = $wpdb->prefix . "produktliste_produkt_ingredienser";

//querying db for the product images and deleting them
$produkt_images = $wpdb->get_results( "
          SELECT picture_id
          FROM {$table_name_main}
          ", ARRAY_A)or die ( $wpdb->last_error );

foreach ( $produkt_images as $image ) {
    wp_delete_attachment( $image['picture_id'] );
}

$wpdb->query("DROP TABLE IF EXISTS $table_name_product_ingredients");
$wpdb->query("DROP TABLE IF EXISTS $table_name_main");
$wpdb->query("DROP TABLE IF EXISTS $table_name_product_category");