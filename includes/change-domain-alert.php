<?php
if ( ! class_exists( 'Change_Domain_Alert', false ) ) {
	/**
	 * This prompts the user for show details when domain changed.
     *
	 */
	class Change_Domain_Alert {
		/**
		 * Primary class constructor.
		 *
		 */
		public function __construct() {
			add_action( 'admin_footer', array( $this, 'js'    ), 20 );
			add_action( 'admin_footer', array( $this, 'css'   )     );
			add_action( 'admin_footer', array( $this, 'modal' )     );
		}

		/**
		 * Modal javascript.
		 *
		 */
		public function js() {

			?>
			<script type="text/javascript">
			jQuery(function($){
				var popup        = $('#kento-woo-domain-popup');
				
				popup.css('display', 'table');
				
				// close button
				$(document).on('click', '.kento-woo-domain-close', function(event) {
					event.preventDefault();
					popup.css('display', 'none');
				});
			});
			</script>
			<?php
		}

		/**
		 * load CSS.
		 *
		 */
		public function css() {
			?>
			<style type="text/css">
			.kento-woo-domain-modal {
				display: none;
				table-layout: fixed;
				position: fixed;
				z-index: 9999;
				width: 100%;
				height: 100%;
				text-align: center;
				font-size: 14px;
				top: 0;
				left: 0;
				background: rgba(0,0,0,0.8);
			}
			.kento-woo-domain-wrap {
				display: table-cell;
				vertical-align: middle;
			}
			.kento-woo-domain {
				background-color: #fff;
				max-width: 550px;
				margin: 0 auto;
				padding: 30px;
				text-align: left;
			}
			.kento-woo-domain-header {
				display: block;
				font-size: 18px;
				font-weight: 700;
				text-transform: uppercase;
				border-bottom: 1px solid #ddd;
				padding: 0 0 18px 0;
				margin: 0 0 18px 0;	
				position: relative;
			}
			.kento-woo-domain-title {
				text-align: left;
			}
			.kento-woo-domain-close {
				text-align: right;
				position: absolute;
				right: 0px;
				font-size: 24px;
				cursor: pointer;
			}
			.kento-woo-domain-title span {
				color: #999;
				margin-right: 10px;
			}
			.kento-woo-domain-desc {
				display: block;
				font-weight: normal;
				margin: 0 0 18px 0;
			}
			</style>
			<?php
		}

		/**
		 * Survey modal.
		 *
		 */
		public function modal() {

			?>
			<div class="kento-woo-domain-modal" id="kento-woo-domain-popup">
				<div class="kento-woo-domain-wrap">
					<form class="kento-woo-domain" method="post">
						<span class="kento-woo-domain-header">
							<span class="dashicons dashicons-warning"></span>
							<?php echo ' ' . esc_html__( 'Domain Mismatch Alert', 'kentoSync-for-woocommerce' ); ?>
							<span title="<?php esc_attr_e( 'Close', 'kentoSync-for-woocommerce' );?> " class="kento-woo-domain-close">✕</span>
						</span>

						<span class="kento-woo-domain-desc">
							<?php
							printf(
								esc_html__( 'Site Url has been changed. Please Contact our support team to update site Url to Resume Sync with QuickBooks.', 'kentoSync-for-woocommerce' ),
							);
							?>
						</span>
					</form>
				</div>
			</div>
			<?php
		}
	}
} 
