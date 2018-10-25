<?php
/**
 * Tabla para visualizar los resultados
 *
 *
 * @package	 resiliencia-qi
 * @since    1.0.0
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
$org_id = NULL;
$search = '';
class Areas_Org_Table extends WP_List_Table {

	public function __construct($search_string ='', $hash) {
		global $org_id;
		global $search;
		$org_id = $hash;
		$search = $search_string;
		parent::__construct( array(
	   'singular'=> 'Área', //Singular label
	   'plural' => 'Áreas', //plural label, also this well be one of the table css class
	   'ajax'  => true //We won't support Ajax for this table
	   ) );
	}
	 /**
     * Metodo para preparar la informacion de la tabla
     *
     * @return Void
     */
    public function prepare_items() {
		global $org_id;
		global $search;
		// Construir columnas
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		
		// Traer informacion y ordenarla
        $data = $this->table_data($search);
		usort( $data, array( &$this, 'sort_data' ) );
		$this->_column_headers = array($columns, $hidden, $sortable);

		/** Acciones en lote */
		$this->process_bulk_action();

		// Paginacion
        $perPage = $this->get_items_per_page( 'resultados_per_page', 10 );
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        $this->items = $data;
	}
	
    /**
	 * Sobreescribir el metodo padre para las columnas. Define las columnas utilizadas para la tabla
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
			'cb'     	   => '<input type="checkbox" />',
            'id'           => 'ID',
			'nombre'       => 'Nombre',
		);

        return $columns;
	}

    /**
     * Definir las columnas ocultas
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return array();
	}
	
    /**
     * Definir las columnas "sortable"
     *
     * @return Array
     */
    public function get_sortable_columns() {
        return array(
			'id' => array( 'id', true ),
			'nombre' => array('nombre', false)
		);
	}
	
    /**
     * Construir datos de la tabla
     *
     * @return Array
     */
    private function table_data($search='') {
		global $wpdb, $org_id;
		$sql = "SELECT id, nombre FROM {$wpdb->prefix}areasorg_areas WHERE organizacion = '{$org_id}'";
		if(!empty($search)){
			$sql .= " AND nombre LIKE '%{$search}%'";
		}

		return $wpdb->get_results( $sql, 'ARRAY_A' );
	}
	
	/**
     * Construir nombres de columnas
     *
     * @return Mixed
     */
	function column_id( $item ) {
		global $org_id;
		$title = '<strong>' . $item['id'] . '</strong>';
	  
		$actions = [
			'delete'  => sprintf( '<a href="?page=%s&action=%s&registro=%s&noheader=true&org_id=%s">Eliminar</a>', $_REQUEST['page'], 'delete', $item['id'], $org_id )
		];
	  
		return $title . $this->row_actions( $actions );
	  }
    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'id':
			case 'nombre':
                return $item[ $column_name ];
			default:
                return print_r( $item, true ) ;
        }
    }
    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'id';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order'])) {
            $order = $_GET['order'];
        }
        $result = $a[$orderby] - $b[$orderby];
        if($order === 'asc') {
            return $result;
        }
        return -$result;
	}

	/**
     * Columna para seleccionar multiples filas
     *
     * @return String
     */
	function column_cb( $item ) {
		return sprintf(
		  '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}

	/**
     * Mensaje para cuando no hay datos
     *
     * @return Mixed
     */
	public function no_items() {
		echo 'No hay resultados.';
	}

	/**
     * Definir acciones en lote
     *
     * @return Mixed
     */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Eliminar'
		];
		
		return $actions;
	}

	public static function delete_registro( $id ) {
		global $wpdb;
		
		$wpdb->delete(
			"{$wpdb->prefix}areasorg_areas",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}
	
	public function process_bulk_action() {
		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
			self::delete_registro( absint( $_GET['registro'] ) );
			wp_redirect(add_query_arg( 'org_id', $_GET['org_id'], admin_url('admin.php?page=areasorg-qi-adminpage') ));
			exit;
		}
	  
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
			 || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {
	  
			$delete_ids = esc_sql( $_POST['bulk-delete'] );
		
			foreach ( $delete_ids as $id ) {
				self::delete_registro( $id );
			}
	  
			wp_redirect(add_query_arg( 'org_id', $_GET['org_id'], admin_url('admin.php?page=areasorg-qi-adminpage') ));
			exit;
		}
	}
}