<?php

$limepay_settings = array(
  'enabled'         => array(
    'title'		      => __( 'Enable / Disable', 'limepay' ),
    'label'		      => __( 'Enable this payment gateway', 'limepay' ),
    'type'		      => 'checkbox',
    'default'	      => 'no',
  ),
  'title'           => array(
    'title'		      => __( 'Title', 'limepay' ),
    'type'		      => 'text',
    'desc_tip'	    => __( 'Payment title the customer will see during the checkout process.', 'limepay' ),
    'default'	      => __( 'Card Payment or Payment Plan', 'limepay' ),
  ),
  'description'     => array(
    'title'		      => __( 'Description', 'limepay' ),
    'type'		      => 'textarea',
    'desc_tip'	    => __( 'Payment description the customer will see during the checkout process.', 'limepay' ),
    'default'	      => __( 'Credit, debit or Amex card - Full payment or Payment plan.', 'limepay' ),
    'css'		        => 'max-width:350px;'
  ),
  'publishable_key' => array(
    'title'		      => __( 'Publishable Key', 'limepay' ),
    'type'		      => 'text',
    'desc_tip'	    => __( 'This key is provided to you by Limepay.', 'limepay' ),
  ),
  'secret_key'      => array(
    'title'		      => __( 'Secret Key', 'limepay' ),
    'type'		      => 'text',
    'desc_tip'	    => __( 'This key is provided to you by Limepay.', 'limepay' ),
  ),
  'payment_option'  => array(
    'title'		      => __( 'Available payment options', 'limepay' ),
    'type'          => 'select',
    'description'   => __( 'Allows to provide only one payment option at checkout.', 'limepay' ),
    'default'       => '0',
    'desc_tip'      => true,
    'options'       => array(
        '0'		      => __( 'Full payment & split payment', 'limepay' ),
        'paycard'   => __( 'Full payment only', 'limepay' ),
        'payplan'   => __( 'Split payment only', 'limepay' ),
    ),
  ),
  'hide_icon'     => array(
    'title'		      => __( 'Hide cards image', 'limepay' ),
    'label'         => ' ',
    'type'          => 'checkbox',
    'default'       => 'no',
    'desc_tip'      => true,
  ),
  'request_3ds'     => array(
    'title'		      => __( 'Request 3DS on payments', 'limepay' ),
    'label'         => ' ',
    'type'          => 'checkbox',
    'default'       => 'no',
    'desc_tip'      => true,
  ),
  'minimum_amount_3ds'      => array(
    'title'		      => __( 'Minimum Amount for 3DS', 'limepay' ),
    'type'		      => 'text',
    'desc_tip'	    => __( 'Minimum amount to request 3DS.', 'limepay' ),
  ),
  'primary_color'      => array(
    'title'		      => __( 'Primary Color (hex code)', 'limepay' ),
    'type'		      => 'text',
    'desc_tip'	    => __( 'Primary color of checkout.', 'limepay' ),
  ),
  'wallet_payments_place_order' => array(
    'title'		      => __( 'Allow Wallet payments to place the order', 'limepay' ),
    'label'         => 'Automatically place the order with Wallet payments',
    'description'   => __( 'Submit orders immediately when a digital wallet payment such as Apple Pay or Google Pay is selected.', 'limepay' ),
    'type'          => 'checkbox',
    'default'       => 'no',
    'desc_tip'      => true,
  ),
);

return $limepay_settings;
