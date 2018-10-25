<?php
/**
 * Plugin Name:       Áreas de Tu Organización QI
 * Plugin URI:        https://github.com/NTHINGs/areasorg-qi
 * Description:       Plugin hecho a la medida para manejar el cuestionario de calidad de vida laboral del Instituto QI.
 * Version:           1.0.0
 * Author:            Mauricio Martinez
 * Author URI:        https://github.com/NTHINGs
 * License:           MIT
 * License URI:       https://github.com/NTHINGs/areasorg-qi/blob/master/LICENSE
 * Text Domain:       areasorg-qi
 *
 * @link              https://github.com/NTHINGs/areasorg-qi
 * @package           areasorg-qi
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define global constants.
 *
 * @since 1.0.0
 */
// Plugin version.
if ( ! defined( 'ABS_VERSION_AREASORG' ) ) {
	define( 'ABS_VERSION_AREASORG', '1.0.0' );
}
if ( ! defined( 'AREASORG_PLUGIN_PATH' ) ) {
	define( 'AREASORG_PLUGIN_PATH',  plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'AREASORG_PLUGIN_URL' ) ) {
	define( 'AREASORG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
/**
 * Link.
 *
 * @since 1.0.0
 */
if ( file_exists( AREASORG_PLUGIN_PATH . 'admin-templates/admin-page.php' ) ) {
	require_once( AREASORG_PLUGIN_PATH . 'admin-templates/admin-page.php' );
}

// Crear Tablas en MySql
function areasorg_qi_create_plugin_database() {
    global $table_prefix, $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$sql = str_replace(array("%TABLE_PREFIX%", "%CHARSET_COLLATE%"), array($table_prefix . "areasorg", $charset_collate), file_get_contents( AREASORG_PLUGIN_PATH . "/schema.sql" ));
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}
register_activation_hook( __FILE__, 'areasorg_qi_create_plugin_database' );
