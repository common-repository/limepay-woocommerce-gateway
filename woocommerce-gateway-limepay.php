<?php
/*
Plugin Name: Limepay v2 Gateway for WooCommerce
Plugin URI: https://docs.limepay.com.au/developer-portal/checkout/woocommerce/
Description: Extends WooCommerce by adding Limepay as a payment method.
Version: 4.2.3
Author: Limepay
Text Domain: woocommerce-gateway-limepay
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


define( 'LIMEPAY_VERSION', '4.2.3' );
define( 'LIMEPAY_CHECKOUTJS', 'https://checkout-v3.limepay.com.au/v3/checkout-v3.0.0.min.js' );

define( 'LIMEPAY_GATEWAY_FILE', __FILE__ );
define( 'LIMEPAY_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'LIMEPAY_TOGGLE_DEFAULT_COLOR', '#3A3CA6' );
define( 'LIMEPAY_BNPL_AMOUNT_DEFAULT_COLOR', '#fa5402' );

add_action( 'plugins_loaded', 'limepay_init', 11 );
function limepay_init() {
  // If the parent WC_Payment_Gateway class doesn't exist
  if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;

  // Subscriptions
  require_once dirname( __FILE__ ) . '/includes/traits/limepay-order-pay.php';
  require_once dirname( __FILE__ ) . '/includes/traits/limepay-subscriptions.php';

  // Include Limepay Gateway Class
  include_once( dirname( __FILE__ ) . '/includes/woocommerce-limepay.php' );
  // Include Limepay API Class
  include_once( dirname( __FILE__ ) . '/includes/limepay-api.php' );
  // Include Limepay paymentaction controller
  require_once( dirname( __FILE__ ) . '/includes/payment-action-controller.php');
  // Include Limepay Widgets
  require_once( dirname( __FILE__ ) . '/includes/limepay-widgets.php');
  // Include Limepay Helper
  require_once( dirname( __FILE__ ) . '/includes/helper.php');
  // Include Limepay - Apple Pay domain association
  require_once( dirname( __FILE__ ) . '/includes/apple-pay-domain-association.php');
  // Include Limepay Customer Class
  require_once( dirname( __FILE__ ) . '/includes/limepay-customer.php');

  // Add Limepay Gateway too WooCommerce
  add_filter( 'woocommerce_payment_gateways', 'add_limepay_gateway' );
  function add_limepay_gateway( $methods ) {
    $methods[] = 'Limepay';
    return $methods;
  }
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'limepay_action_links' );

function limepay_action_links( $links ) {
  $plugin_links = array(
      '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'limepay' ) . '</a>',
  );

  return array_merge( $plugin_links, $links );
}
