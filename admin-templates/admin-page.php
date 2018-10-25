<?php


if(!class_exists('Areas_Org_Table')){
    require_once( AREASORG_PLUGIN_PATH . 'admin-templates/Areas_Org_Table.php' );
}
/**
 * areasorg Tabbed Settings Page
 */

add_action( 'admin_menu', 'areasorg_qi_admin' );

function areasorg_qi_admin() {
    add_menu_page(
        'Cuestionario Calidad de Vida Laboral',     // page title
        'Áreas de Tu Organización',     // menu title
        'areasorg',   // capability
        'areasorg-qi-adminpage',     // menu slug
		'render_areasorg_qi_admin', // callback function
		'dashicons-groups'
    );
}

function render_areasorg_qi_admin() {
    echo 'Hello';
}