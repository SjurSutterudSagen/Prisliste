<?php
/*
Plugin Name: Prisliste
Description: En prisliste plugin for Hadeland Viltslakteri
Author: Sjur Sutterud Sagen
Version: 0.1
*/

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
    }

    //function prisliste_handle_post() {

    //}


}

?>