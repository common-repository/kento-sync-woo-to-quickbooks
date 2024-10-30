<?php
/**
 * Call get listing for Wc Orders
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
require_once( plugin_dir_path( dirname(__FILE__) ) . 'api/kento-quickbook-api.php' );
require_once( plugin_dir_path( dirname(__FILE__) ) . 'includes/kento-quickbook-menu.php' );

class Kt_Wc_QB_Order_List extends WP_List_Table {

    /** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Wc Order', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Wc Orders', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

    }     
   
    /**
     * Get order listing
     */
    public function getOrderlisting() {
		$seller_uuid = get_option('kt_seller_uuid');
		$currentStatus = self::getCurrentStatus();
		$title = 'Orders';
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
								<form action="<?php echo admin_url('/admin.php?page=kt-wc-quick-books-order-listing'); ?>" method="post">
									<input type="hidden" name="page" value="kt-wc-quick-books-order-listing" />
									<div class="form-group float-right">
										<label for="qb-status">QB Order:</label>
										<select class="form-control" id="qb-status" name="status"  onchange="javascript:this.form.submit()">
											<option <?php if($currentStatus == '') { echo "selected"; } ?> value="">All</option>
											<option <?php if($currentStatus == 'P') { echo "selected"; } ?> value="P">Unsync</option>
											<option <?php if($currentStatus == 'Y') { echo "selected"; } ?> value="Y">Sync</option>
										</select>
									</div>
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
	 * Retrieve wc orders data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_wc_orders( $per_page = 5, $page_number = 1 ) {
		$seller_uuid = get_option('kt_seller_uuid');

		$status = self::getCurrentStatus();

		$request = [
			'per_page' => $per_page,
			'page_number' => $page_number,
			'status'	=> $status
		];
		$quickBookApi = new Kento_Woo_Quickbook_Api;
		$wc_orders = $quickBookApi->apiPostRequest('get_wc_orders', $request, $seller_uuid);
	
		return $wc_orders;
	}

	public static function getCurrentStatus() {
		$status = '';
		if (!empty( $_REQUEST )) {
			$status = isset( $_REQUEST['status'] ) ? sanitize_text_field($_REQUEST['status']) : '';
		}
		return $status;
	}

	/** Text displayed when no order data is available */
	public function no_items() {
		_e( 'No items avaliable.', 'sp' );
	}

	/**
	 * Method for order number column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_order_number( $item ) {

		$title = '<strong># ' . $item->order_number . '</strong>';

		$actions = [
		];

		return $title . $this->row_actions( $actions );
    }
    
    /**
	 * Method for order date column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_date_created( $item ) {

        $order_timestamp = $item->date_created ? strtotime($item->date_created ) : '';

		if ( ! $order_timestamp ) {
			echo '&ndash;';
			return;
		}
        return date('M j, Y', $order_timestamp);
    }

    /**
	 * Render columm: order_total.
	 */
	function column_formatted_total( $item ) {
		$formatted_total = wc_price( $item->total, array( 'currency' => $item->currency ) );
		$order_total     = $item->total;
		$refunds = json_decode($item->refunds);

		if(!empty($refunds)) {
			$item_refund = 0;
			foreach ($refunds as $refund) {
				$item_refund += $refund->total;
			}
		}
		$total_refunded  = isset($item_refund) ? $item_refund : '';
		$display_refunded = true;

		if ( $total_refunded && $display_refunded ) {
			$formatted_total = '<del>' . wp_strip_all_tags( $formatted_total ) . '</del> <ins>' . wc_price( $order_total + $total_refunded, array( 'currency' => $item->currency ) ) . '</ins>';
		}

		return apply_filters( 'woocommerce_get_formatted_order_total', $formatted_total, $this, '', $display_refunded );
    }

    /**
	 * Render columm: order_status.
	 */
	function column_wc_status( $item ) {
		return '<mark class="order-status '.esc_attr( sanitize_html_class( 'status-' . $item->wc_status ) ).'"><span>'.esc_html( wc_get_order_status_name( $item->wc_status ) ).'</span></mark>';
	}
	
	/**
	 * Render columm: qbo_sync_status.
	 */
	function column_qbo_sync_status( $item ) {
		return '<span>'.esc_html( $item->qbo_sync_status ).'</span>';
    }

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
            'order_number'  => __( 'Order', 'sp' ),
			'date_created'  => __( 'Order Date', 'sp' ),
			'wc_status'  => __( 'Woo Status', 'sp' ),
			'formatted_total' => __( 'Total', 'sp' ),
			'qbo_sync_status'  => __( 'QB Status', 'sp' ),
		];

		return $columns;
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
		$getData = self::get_wc_orders( $per_page, $current_page );

		$total_items  =  $getData->ordersCount;

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );
		$this->items = $getData->orders;
		
		$columns               = $this->get_columns();
		$this->_column_headers = array( $columns, array() );
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