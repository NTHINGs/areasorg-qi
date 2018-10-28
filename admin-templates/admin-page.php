<?php


if(!class_exists('Areas_Org_Table')){
    require_once( AREASORG_PLUGIN_PATH . 'admin-templates/Areas_Org_Table.php' );
}
if(!class_exists('Orgs_Areas_Table')){
    require_once( AREASORG_PLUGIN_PATH . 'admin-templates/Orgs_Areas_Table.php' );
}
/**
 * areasorg Tabbed Settings Page
 */

add_action( 'admin_menu', 'areasorg_qi_admin' );
add_action( "admin_action_area_save_page", "area_save_page_admin_action");

function areasorg_qi_admin() {
    $hook = add_menu_page(
        'Áreas de Tu Organización',     // page title
        'Áreas de Tu Organización',     // menu title
        'areasorg',   // capability
        'areasorg-qi-validateadminpage',     // menu slug
		'render_areasorg_qi_validate_admin', // callback function
		'dashicons-groups'
    );
    add_action( "load-$hook", 'areasorq_qi_screen_option' );

    $hook2 = add_submenu_page(
        null,
        'Área', //page title
        'Área', //menu title
        'areasorg', //capability,
        'areasorg-qi-adminpage-editar',//menu slug
        'render_areasorg_qi_admin_editar' //callback function
    );
    add_action( "load-$hook2", 'areasorq_qi_screen_option' );

    $hook3 = add_submenu_page(
        null,
        'Areas de Tu Organización', //page title
        'Areas de Tu Organización', //menu title
        'areasorg', //capability,
        'areasorg-qi-adminpage',     // menu slug
		'render_areasorg_qi_admin' // callback function
    );
    add_action( "load-$hook3", 'screen_option' );
}

function get_user_org_id() {
    $hash = NULL;
    if( isset($_GET['org_id']) ){
        $hash = $_GET['org_id'];
    } else {
        $current_user = wp_get_current_user();
        $hash = get_user_meta($current_user->ID, 'hash', true);
    }
    return $hash;
}

function render_areasorg_qi_validate_admin() {
    if (current_user_can('areasorg') && !current_user_can('areasorg_admin')) {
        render_areasorg_qi_admin(get_user_org_id());
    } else {
        render_table_orgs_areas();
    }
}

function render_areasorg_qi_admin($org_id = NULL) {
    if( isset($_GET['org_id']) ){
        $org_id = $_GET['org_id'];
    }
    if($org_id != NULL) {
        $wp_list_table = NULL;
        if( isset($_POST['s']) ){
            $wp_list_table = new Areas_Org_Table($_POST['s'], $org_id);
        } else {
            $wp_list_table = new Areas_Org_Table(null, $org_id);
        }
        ob_start();
        ?>
        <h2>Areas de Tu Organización</h2>
        <a href="<?php echo add_query_arg( 'org_id', get_user_org_id(), admin_url('admin.php?page=areasorg-qi-adminpage-editar')); ?>" class="button">Agregar Área</a>
        <div id="poststuff">
            <form method="post">
            <?php
                $wp_list_table->prepare_items();
                $wp_list_table->search_box( 'Buscar', 'search_id' ); 
                $wp_list_table->display();
            ?>
            </form>
        </div>
        <?php
        ob_end_flush();
    } else {
        ob_start();
        ?>
        ERROR ORG_ID NO ESTA DEFINIDO
        <?php
        ob_end_flush();
    }
}

function render_areasorg_qi_admin_editar() {
    $area = '';
    $modo = 'nuevo';
    if( isset($_GET['area_id']) ){
        // Modo Editar
        global $wpdb;
        $sql = "SELECT nombre FROM {$wpdb->prefix}areasorgareas WHERE id = '{$_GET['area_id']}'";
        $area = $wpdb->get_var( $sql );
        $titulo = 'Editar ' . $area;
        $modo = 'editar';
    } else {
        // Nueva Area
        $titulo = 'Nueva Área ';
    }
    

    ?>
    <a href="<?php echo add_query_arg( 'org_id', get_user_org_id(), admin_url('admin.php?page=areasorg-qi-adminpage') ); ?>"><- Volver a la lista </a>
    <div class="wrap">
        <h1><?php echo $titulo; ?></h1>
        <form method="post" action="<?php echo esc_html( admin_url( 'admin.php' ) ); ?>">
            <div id="universal-message-container">
                <div class="options">
                    <p>
                        <label>Nombre del Área</label>
                        <br />
                        <input type="text" name="nombre" value="<?php echo $area; ?>" />
                    </p>
                </div>
            </div><!-- #universal-message-container -->
            <input type="hidden" name="action" value="area_save_page">
            <input type="hidden" name="organizacion" value="<?php echo get_user_org_id(); ?>">
            <input type="hidden" name="modo" value="<?php echo $modo; ?>">
            <input type="hidden" name="id" value="<?php echo $_GET['area_id']; ?>">
        <?php
            submit_button();
        ?>
        </form>
    </div><!-- .wrap -->
    <?php
}

function areasorq_qi_screen_option() {
	$option = 'per_page';
	$args   = [
		'label'   => 'Resultados',
		'default' => 10,
		'option'  => 'resultados_per_page'
	];

	add_screen_option( $option, $args );
}

function area_save_page_admin_action() {
    global $wpdb;

    $table_name = $wpdb->prefix . "areasorgareas";
    $values = array(
        'nombre'             => $_POST['nombre'],
        'organizacion'       => $_POST['organizacion'],
    );
    if($_POST['modo'] == 'nuevo') {
        $wpdb->insert( $table_name, $values, array('%s', '%s'));
    } else {
        $wpdb->update( $table_name, $values, array( 'id' => $_POST['id'] ), array('%s', '%s'), array( '%d' ));
    }
    wp_redirect(add_query_arg( 'org_id', $_POST['organizacion'], admin_url('admin.php?page=areasorg-qi-adminpage') ));
    exit;
}

function render_table_orgs_areas() {
    $wp_list_table = NULL;
    if( isset($_POST['s']) ){
        $wp_list_table = new Orgs_Areas_Table($_POST['s']);
    } else {
        $wp_list_table = new Orgs_Areas_Table(null);
    }

    ob_start();
    ?>
    <h2>Organizaciones</h2>
    <div id="poststuff">

        <form method="post">
            <?php
                $wp_list_table->prepare_items();
                $wp_list_table->search_box( 'Buscar', 'search_id' ); 
                $wp_list_table->display();
            ?>
        </form>
    </div>
    <?php
    ob_end_flush();
}