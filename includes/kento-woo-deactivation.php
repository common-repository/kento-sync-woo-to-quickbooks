<?php
if ( ! class_exists( 'Kento_Woo_Deactivation', false ) ) {
	/**
	 * This prompts the user for show details when they deactivate the plugin.
     *
	 */
	class Kento_Woo_Deactivation {
		/**
		 * Primary class constructor.
		 *
		 */
		public function __construct() {
			add_action( 'admin_print_scripts', array( $this, 'js'    ), 20 );
			add_action( 'admin_print_scripts', array( $this, 'css'   )     );
			add_action( 'admin_footer',        array( $this, 'modal' )     );
		}

		/**
		 * admin screen is the plugins page.
		 *
		 * @return bool
		 */
		public function is_plugin_page() {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			if ( empty( $screen ) ) {
				return false;
			}
			return ( ! empty( $screen->id ) && in_array( $screen->id, array( 'plugins', 'plugins-network' ), true ) );
		}

		/**
		 * Modal javascript.
		 *
		 */
		public function js() {

			if ( ! $this->is_plugin_page() ) {
				return;
			}
			?>
			<script type="text/javascript">
			jQuery(function($){
				var deactivateLink = $('#the-list').find('[data-slug="<?php echo KENTO_WOO_PLUGIN_NAME; ?>"] span.deactivate a');
				var popup        = $('#kento-woo-deactivation-popup');
				// Plugin listing table deactivate link.
				deactivateLink.on('click', function(event) {
					event.preventDefault();
					popup.css('display', 'table');
				});
				
				// Deactivate.
				$(document).on('click', '.kento-woo-deactivation-submit', function(event) {
					event.preventDefault();
					location.href = deactivateLink.attr('href');
				});
				// close button
				$(document).on('click', '.kento-woo-deactivation-close, .kento-woo-deactivation-cancel', function(event) {
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

			if ( ! $this->is_plugin_page() ) {
				return;
			}
			?>
			<style type="text/css">
			.kento-woo-deactivation-modal {
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
			.kento-woo-deactivation-wrap {
				display: table-cell;
				vertical-align: middle;
			}
			.kento-woo-deactivation {
				background-color: #fff;
				max-width: 550px;
				margin: 0 auto;
				padding: 30px;
				text-align: left;
			}
			.kento-woo-deactivation-header {
				display: block;
				font-size: 18px;
				font-weight: 700;
				text-transform: uppercase;
				border-bottom: 1px solid #ddd;
				padding: 0 0 18px 0;
				margin: 0 0 18px 0;	
				position: relative;
			}
			.kento-woo-deactivation-title {
				text-align: left;
			}
			.kento-woo-deactivation-close {
				text-align: right;
				position: absolute;
				right: 0px;
				font-size: 24px;
				cursor: pointer;
			}
			.kento-woo-deactivation-title span {
				color: #999;
				margin-right: 10px;
			}
			.kento-woo-deactivation-desc {
				display: block;
				font-weight: normal;
				margin: 0 0 18px 0;
			}
			.kento-woo-deactivation-footer {
				margin-top: 18px;
                text-align: center;
			}
			</style>
			<?php
		}

		/**
		 * Survey modal.
		 *
		 */
		public function modal() {

			if ( ! $this->is_plugin_page() ) {
				return;
			}

			?>
			<div class="kento-woo-deactivation-modal" id="kento-woo-deactivation-popup">
				<div class="kento-woo-deactivation-wrap">
					<form class="kento-woo-deactivation" method="post">
						<span class="kento-woo-deactivation-header">
							<?php echo ' ' . esc_html__( 'Are you sure want to deactivate plugin?', 'kentoSync-for-woocommerce' ); ?>
							<span title="<?php esc_attr_e( 'Close', 'kentoSync-for-woocommerce' );?> " class="kento-woo-deactivation-close">✕</span>
						</span>

						<span class="kento-woo-deactivation-desc">
							<?php
							printf(
								/* translators: %s - plugin name. */
								esc_html__( 'Deactivating plugin will be stop your Payment Subscription as well as Sync. Reactivating will be required you to subscribe again', 'kentoSync-for-woocommerce' )
							);
							?>
						</span>
						<div class="kento-woo-deactivation-footer">
							<a href="#" class="kento-woo-deactivation-cancel button button-default button-large"><?php echo esc_html__( 'Cancel', 'kentoSync-for-woocommerce' ); ?></a>
							<button type="submit" class="kento-woo-deactivation-submit button button-primary button-large"><?php echo esc_html__( 'Deactivate', 'kentoSync-for-woocommerce' ); ?></button>
						</div>
					</form>
				</div>
			</div>
			<?php
		}
	}
} 
