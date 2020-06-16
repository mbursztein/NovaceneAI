<?php
if( !class_exists( 'WP_List_Table' ) )
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class S201_BAI_Table extends WP_List_Table {
    function __construct(){
		global $status, $page;

		parent::__construct( array(
			'singular'  => 's201_bai',     //singular name of the listed records
			'plural'    => 's201_bai',   //plural name of the listed records
			'ajax'      => false        //does this table support ajax?
		) );
    }

	// IMPORTANT, REQUIRED TO DISPLAY
	function column_default( $item, $column_name ) {
		switch( $column_name ) { 
			case 'id':
			case 'shortcode':
			case 'attach':
				return $item->$column_name;
			default:
				return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		}
	}

	function get_columns(){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'id' => 'ID',
			'shortcode' => 'Shortcode',
			'attach' => 'Attached to',
		);
		return $columns;
	}
	
	function prepare_items($search = null) {
		global $wpdb, $tbl_s201_bai;
		$screen = get_current_screen();

		/* -- Preparing your query -- */
		$query = "SELECT * FROM $tbl_s201_bai";
		$query.=" WHERE 1";
		if( $search != null ){
			$search_query = str_replace('.', '', $search);
			$query.=" AND id = '$search_query'";
		}

		/* -- Ordering parameters -- */
		//Parameters that are going to be used to order the result
		$orderby = !empty($_GET["orderby"]) ? sanitize_key($_GET["orderby"]) : "$tbl_s201_bai.id";
		$order = !empty($_GET["order"]) ? sanitize_key($_GET["order"]) : 'DESC';
		if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

		/* -- Pagination parameters -- */
		//Number of elements in your table?
		$totalitems = $wpdb->query($query); //return the total number of affected rows
		//How many to display per page?
		$perpage = 20;
		//Which page is this?
		$paged = !empty($_GET["paged"]) ? sanitize_key($_GET["paged"]) : '';
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
		//How many pages do we have in total?
		$totalpages = ceil($totalitems/$perpage);
		//adjust the query to take pagination into account
		if(!empty($paged) && !empty($perpage)){
			$offset=($paged-1)*$perpage;
			$query.=' LIMIT '.(int)$offset.','.(int)$perpage;
		}

		/* -- Register the pagination -- */
		$this->set_pagination_args( array(
			"total_items" => $totalitems,
			"total_pages" => $totalpages,
			"per_page" => $perpage,
		) );
		//The pagination links are automatically built according to those parameters

		/* -- Register the Columns -- */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		/* -- Fetch the items -- */
		$this->items = $wpdb->get_results($query);
	}
	/**
	 * Display the rows of records in the table
	 * @return string, echo the markup of the rows
	 */
	
	function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="IDs[]" value="%s" />', $item->id
        );    
    }
	
	function column_shortcode($item) {
		$actions = array(
			'edit' => '<a href="'.get_edit_post_link($item->post_id).'">Edit</a>',
			'delete' => sprintf('<a href="?page=%s&action=%s&id=%s&_wpnonce=%s" onclick="return confirm(\'Are you sure you want to delete this item?\');">Delete</a>',$_REQUEST['page'],'delete',$item->id,wp_create_nonce('delete_item'.$item->id)),
		);
		return sprintf('%1$s %2$s', '[s201_bai id="'.$item->id.'"]', $this->row_actions($actions) );
	}
	
	function column_attach($item) {
		$post = get_post($item->post_id);
		if($post){
			$title = $post->post_title;
			if(!$title) $title = '(no title)';
			return '<a href="'.get_edit_post_link($item->post_id).'"><b>'.$title.'</b></a>';
		}
		else{
			return 'Post <b>'.$item->post_id.'</b> does not exists or was deleted!<br><a href="'.admin_url('post-new.php?post_type=s201_bai_post&bai_id='.$item->id).'">Create new post to attach</a>';
		}
	}
	
	function get_bulk_actions() {
		$actions = array(
			'delete'    => 'Delete'
		);
		return $actions;
	}
	
	function single_row( $item ) {
		echo '<tr class="alternate1">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
	
}

?>