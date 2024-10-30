<?php
/**
 * Limepay_Customer class.
 */
class Limepay_Customer {

	/**
	 * Limepay customer ID
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * WP User ID
	 *
	 * @var integer
	 */
	private $user_id = 0;

	public function __construct( $user_id = 0 ) {
		if ( $user_id ) {
			$this->set_user_id( $user_id );
			$this->set_id( $this->get_id_from_meta( $user_id ) );
		}
	}

	public function get_id() {
		return $this->id;
	}

	public function set_id( $id ) {
		$this->id = wc_clean( $id );
	}

	public function get_user_id() {
		return absint( $this->user_id );
	}

	public function set_user_id( $user_id ) {
		$this->user_id = absint( $user_id );
	}

	public function create_customer( $args = [] ) {

		$resp = Limepay_API::request( $args, 'customers/plugin', 'POST' );
		$resp = $this->process_customer_create_response( $resp );

		$this->set_id( $resp['customer_id'] );

		if ( $this->get_user_id() ) {
			$this->update_id_in_meta( $resp['customer_id'] );
		}

		return $resp['customer_id'];
	}

	public function get_id_from_meta( $user_id ) {
		return get_user_option( '_limepay_customer_id', $user_id );
	}

	public function update_id_in_meta( $id ) {
		update_user_option( $this->get_user_id(), '_limepay_customer_id', $id, false );
	}

	public static function map_customer_data( WC_Customer $wc_customer = null ) {
		if ( null === $wc_customer ) {
			return [];
		}

		$firstName  = $wc_customer->get_billing_first_name();
		$lastName 	= $wc_customer->get_billing_last_name();

		$data = [
				'givenName' => $firstName,
				'familyName' => $lastName,
				'emailAddress' => $wc_customer->get_email()
		];

		return $data;
	}

	protected function process_customer_create_response( $resp ) {

		if ($resp['response']['code'] != 200) {
			throw new Exception( 'Limepay create customer API call failed.' );
		}

		$data = json_decode( $resp['body'], true );
		return [
				'result' => 'success',
				'customer_id' => $data['customerId']
		];

	}

	public static function signin_customer( $id ) {
			$args = [
					'customerId' => $id
			];
			$resp = Limepay_API::request( $args, 'authn/tenants/accounts:signinCustomerWithCustomerId', 'POST' );
			$resp = self::process_customer_signin_response( $resp );

			return $resp['custom_token'];
	}

	protected static function process_customer_signin_response( $resp ) {
			if ($resp['response']['code'] != 200) {
				throw new Exception( 'Limepay signin customer API call failed.' );
			}

			$data = json_decode( $resp['body'], true );
			return [
					'result' => 'success',
					'custom_token' => $data['customToken']
			];
	}

}
