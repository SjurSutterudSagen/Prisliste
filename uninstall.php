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

$table_name_main = $wpdb->prefix . "prisliste_produkter";
$table_name_product_category = $wpdb->prefix . "prisliste_kategorier";
$table_name_product_ingredients = $wpdb->prefix . "prisliste_produkt_ingredienser";

$wpdb->query("DROP TABLE IF EXISTS $table_name_product_ingredients");
$wpdb->query("DROP TABLE IF EXISTS $table_name_main");
$wpdb->query("DROP TABLE IF EXISTS $table_name_product_category");