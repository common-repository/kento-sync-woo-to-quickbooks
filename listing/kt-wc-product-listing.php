<?php
/**
 * Call get listing for woocommerce
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once( plugin_dir_path( dirname(__FILE__) ) . 'api/kento-quickbook-api.php' );
require_once( plugin_dir_path( dirname(__FILE__) ) . 'includes/kento-quickbook-menu.php' );

class Kt_Wc_QB_Product_List extends WP_List_Table {

    /** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Wc Product', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Wc Products', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

    }     

    /**
     * Get Product listing
     */
    public function getProductlisting() {
		$seller_uuid = get_option('kt_seller_uuid');
		$currentStatus = self::getCurrentStatus();
		$title = "Products";
		?>
		<div id="custom-quick-books-table" class="wrap">

			<div id="poststuff">
			<?php 
			include_once(plugin_dir_path( dirname(__FILE__) ) . 'templates/header.php');
		?>
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="ui-sortable col">
							<div class="card">
		
								<form action="<?php echo admin_url('/admin.php?page=kt-wc-quick-books-product-listing'); ?>" method="post">
									<input type="hidden" name="page" value="kt-wc-quick-books-product-listing" />
									<div class="table-responsive">
										<?php
										$this->prepare_items();
										$this->display(); ?>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
		<?php
		include_once(plugin_dir_path( dirname(__FILE__) ) . 'templates/footer.php');
	}

	/**
	 * Retrieve products data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public function get_products( $per_page = 5, $page_number = 1 ) {
		$seller_uuid = get_option('kt_seller_uuid');

		$status = self::getCurrentStatus();

		$request = [
			'per_page' => $per_page,
			'page_number' => $page_number,
			'status'	=> $status
		];
		$quickBookApi = new Kento_Woo_Quickbook_Api;
		$wc_products = $quickBookApi->apiPostRequest('get_wc_products', $request, $seller_uuid);
	
		return $wc_products;
	}

	public static function getCurrentStatus() {
		$status = '';
		if (!empty( $_REQUEST )) {
			$status = isset( $_REQUEST['status'] ) ? sanitize_text_field($_REQUEST['status']) : '';
		}
		return $status;
	}
	
	/**
	 * Render columm: sku.
	 */
	function column_sku( $item ) {
		return $item->sku ? esc_html( $item->sku ) : '<span class="na">&ndash;</span>';
    }

	/**
	 * Render columm: sku.
	 */
	function column_title( $item ) {
		return '<strong>' . esc_html( $item->title ) . '</strong>';
	}

	/**
	 * Render columm: sync_status.
	 */
	protected function column_sync_status( $item ) {
		return $item->sync_status ? esc_html( $item->sync_status ) : '<span class="na">&ndash;</span>';
	}

	/** Text displayed when no product data is available */
	public function no_items() {
		_e( 'No products avaliable.', 'sp' );
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$show_columns['title']  = __( 'Name', 'sp' );
		$show_columns['sku'] = __( 'SKU', 'sp' );
		$show_columns['sync_status'] = __( 'Status', 'sp' );

		return $show_columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array();

		return $sortable_columns;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$getData = self::get_products( $per_page, $current_page );

		$total_items  =  $getData->productsCount;

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = $getData->products;

		$columns               = $this->get_columns();
		$sortable_columns = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, array(), $sortable_columns);
	}

	/**
	 * Display the pagination.
	 *
	 * @since 3.1.0
	 *
	 * @param string $which
	 */
	protected function pagination( $which ) {
		if ( empty( $this->_pagination_args ) ) {
			return;
		}

		$total_items     = $this->_pagination_args['total_items'];
		$total_pages     = $this->_pagination_args['total_pages'];
		$paged = $this->get_pagenum();
		$pagerange = 2;
		$numpages = $total_pages;
		$currentStatus = self::getCurrentStatus();
		$args = array();
		if ($currentStatus) {
			$args = array('status' => $currentStatus);
		}
		
		$pagination_args = array(
			'base'            => add_query_arg( 'paged', '%#%' ),
			'format'          => '',
			'total'           => $numpages,
			'current'         => $paged,
			'show_all'        => False,
			'end_size'        => 1,
			'mid_size'        => $pagerange,
			'prev_next'       => True,
			'prev_text'       => __('«'),
			'next_text'       => __('»'),
			'type'            => 'array',
			'add_args'        => $args,
			'add_fragment'    => ''
		);
		
		$paginate_links = paginate_links($pagination_args);
		
		if (is_array($paginate_links)) {
			echo "<div class='wc-pagination'>";
			echo "<span class='page-numbers page-num'>Page " . $paged . " of " . $numpages . "</span> ";
			echo '<ul class="wc-pagination-links">';
			foreach ( $paginate_links as $page ) {
				echo "<li>$page</li>";
			}
			echo '</ul>';
			echo "</div>";
		}

	}
}