<?php
/**
 * Plugin Name: CartX Checkout
 * Plugin URI: https://docs.cartx.io/pt-br/article/importando-produtos-do-woocommerce-oar357/?1588601363349
 * Description: O checkout transparente de 1-página de mais alta conversão do mercado. Upsell de 1-clique e Order Bump nativo.
 * Version: 1.0.0
 * Author: CartX
 * Author URI: https://cartx.io/
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package CartX
*/

class CartX_WC
{
	public function __construct()
	{
		if (!class_exists('WooCommerce')) {
			return;
		}

		add_action('woocommerce_before_cart', [$this, 'add_cart_script']);
		add_action('woocommerce_before_checkout_form', [$this, 'add_checkout_script']);
	}


	/**
	* add_checkout_script
	* Put the CartX Snippet on WC template for checkout page.
	*
	* @access        public
	* @return        void
	*/
	public function add_checkout_script()
	{
		$this->script(true);
	}


	/**
	* add_checkout_script
	* Put the CartX Snippet on WC template for cart page.
	*
	* @access        public
	* @return        void
	*/
	public function add_cart_script()
	{
		$this->script();
	}


	/**
	 * add_checkout_script
	 * Put the CartX Snippet on WC template for cart page.
	 *
	 * @access        public
	 * @param bool $isCheckout
	 * @return        void
	 */
	public function script($isCheckout = false)
	{
		?>
		<style>
			@import url('https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap');
			.cartx-loader {
				background: rgb(255,255,255);
				z-index: 999999999;
				position: fixed;
				width: 100%;
				height: 100%;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
			}

			.spinner-wrapper {
				position: fixed;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				margin: 0 auto;
				min-width: 220px;
			}

			.spinner {
				border: 6px solid #e2e2e2;
				border-top: 6px solid #297FBC;
				border-radius: 50%;
				width: 100px;
				height: 100px;
				animation: spin 2s linear infinite;
				margin: auto;
			}

			@keyframes spin {
				from {
					transform:rotate(0deg);
				}
				to {
					transform:rotate(360deg);
				}
			}

			.spinner-icon {
				position: relative;
			}

			.spinner-icon .spinner-icon-svg {
				position: absolute;
				top: 50%;
				left: 50%;
				margin-left: -19px;
				margin-top: -70px;
				width: 40px;
				height: 40px;
			}

			svg:not(:root) {
				overflow: hidden;
			}

			.spinner-text {
				font-family: 'Poppins', sans-serif;
				font-weight: 400;
				text-align: center;
				color: #526473;
				font-size: 16px;
				padding-top: 2rem;
			}
		</style>

		<div class="cartx-loader">
			<div class="spinner-wrapper">
				<div class=''>
					<div class="spinner"></div>
					<div class="spinner-icon">
						<svg fill="#e2e2e2" class="spinner-icon-svg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M18 10v-4c0-3.313-2.687-6-6-6s-6 2.687-6 6v4h-3v14h18v-14h-3zm-5 7.723v2.277h-2v-2.277c-.595-.347-1-.984-1-1.723 0-1.104.896-2 2-2s2 .896 2 2c0 .738-.404 1.376-1 1.723zm-5-7.723v-4c0-2.206 1.794-4 4-4 2.205 0 4 1.794 4 4v4h-8z"></path></svg>
					</div>
				</div>
				<div class=''><div class="spinner-text">Finalizando compra</div></div>
			</div>
		</div>

		<script type='text/javascript'>
			window.CartX = {
				page: <?php echo $isCheckout ? '"checkout"' : '"cart"'; ?>,
				shop_url: "<?php echo $_SERVER['HTTP_HOST']; ?>",
				cart: <?php echo $this->format_cart(); ?>
			};

			(function() {
				var ch = document.createElement('script'); ch.type = 'text/javascript'; ch.async = true;
				ch.src = 'https://accounts.cartx.io/assets/js/woocommerce_redirect.js';
				var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(ch, x);
			})();
		</script>
		<?php
	}

	/**
	* format_cart
	*
	* Format cart payload.
	*
	* @access        public
	* @return        string
	*/
	public function format_cart()
	{
		$cartData = WC()->cart->get_cart();
		$cart = [];

		foreach ($cartData as $key => $item) {
			$cart['items'][] = [
				// not using ?? assuming client is using older version of php
				'variant_id' => $item['variation_id'] ? $item['variation_id'] : $item['product_id'],
				'quantity' => $item['quantity'],
			];
		}

		return json_encode($cart);
	}

}

/**
* Load CartX checkout plugnin
*/
function cartx_plugins_loaded() {
	new CartX_WC();
}

add_action('plugins_loaded', 'cartx_plugins_loaded');
