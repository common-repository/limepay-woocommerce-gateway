<?php
/**
 * Limepay_Payment_Action_Controller class.
 *
 */
class Limepay_Payment_Action_Controller {

	/**
	 * Constructor
	 *
	 * @version 3.1.0
	 */
	public function __construct() {
		add_action( 'wc_ajax_wc_lp_payment_action', [ $this, 'handle_payment_action_completion' ] );
	}

	/**
	 * Returns an instantiated gateway.
	 *
	 * @version 3.1.0
	 * @return Limepay payment gateway
	 */
	protected function get_limepay_gateway( $methodId ) {
		if ( empty( $methodId ) ) {
			$methodId = Limepay::ID;
		}
		$gateways = WC()->payment_gateways()->payment_gateways();
		$this->limepay_gateway = $gateways[ $methodId ];

		return $this->limepay_gateway;
	}

	/**
	 * Get the order from the GET request.
	 *
	 * @version 3.1.0
	 *
	 * @throws Exception If order doesn't exist.
	 * @return WC_Order
	 */
	protected function get_order_from_request() {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['nonce'] ), 'wc_limepay_payment_action' ) ) {
			throw new Exception( __( 'CSRF verification failed.', 'limepay' ) );
		}

		$order_id = null;
		if ( isset( $_GET['order'] ) && absint( $_GET['order'] ) ) {
			$order_id = absint( $_GET['order'] );
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			throw new Exception( __( 'Missing order ID for payment confirmation', 'limepay' ) );
		}

		return $order;
	}

	/**
	 * Handle payment action completion.
	 *
	 * @version 3.1.0
	 */
	public function handle_payment_action_completion() {
		global $woocommerce;

		$methodId = null;
		if ( isset( $_GET['methodId'] ) ) {
			$methodId = $_GET['methodId'];
		}
		$limepay_gateway = $this->get_limepay_gateway( $methodId );

		try {
			$order = $this->get_order_from_request();
		} catch ( Exception $e ) {
			$message = sprintf( 'Payment verification error: %s', $e->getMessage() );
			$this->handle_error( $message );
			exit;
		}

		try {
			$resp = $limepay_gateway->complete_payment_action( $order );
			wp_send_json_success( $resp, 200 );
			exit;
		} catch ( Exception $e ) {
			$this->handle_error( $e->getMessage() );
			exit;
		}
	}

	/**
	 * Redirect on error
	 *
	 * @version 3.1.0
	 */
	protected function handle_error( $message ) {
		$error_message = sprintf( __( 'Error: %s', 'limepay-woocommerce-gateway' ), $message );
		wp_send_json_error(	['message' => $error_message] );
	}

}

new Limepay_Payment_Action_Controller();
