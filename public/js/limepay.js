/* global wc_limepay_params, wc_limepay_params_amount, wc_limepay_params_currency */

jQuery( function( $ ) {
	'use strict';

	var lpWooComForm = {

		init: function() {
			this.id = 'limepay';	// Change the id to match payment method
			this.method = 'limepay';	// Change the method name to match payment method

			if (this.isCheckout()) {
				this.wcForm = $('form.woocommerce-checkout');
			}

			if (this.isOrderReview()) {
				this.wcForm = $('form#order_review');
			}

			if (this.isAddPaymentMethod()) {
				this.wcForm = $('form#add_payment_method');
			}

			if (!this.wcForm) {
				return;
			}

			$('form#order_review, form#add_payment_method').on(
				'submit',
				this.onInitialSubmit
			);

			this.wcForm.on('checkout_place_order_' + this.id, this.onInitialSubmit);

			this.limepayCheckout = LimepayCheckout.createCheckout();
			this.limepayPaymentSource = LimepayCheckout.createPaymentSource();
			if (!this.orderPayPaymentAction()) {
				this.renderOnCheckoutUpdates();
				this.renderLimepay();
			}

			this.paymentTokenInvalid = true;
			this.paymentSourceInvalid = true;
			window.addEventListener('hashchange', lpWooComForm.onHashChange);
		},

		loadLimepayParams: function() {
			try {
				lpWooComForm.limepayCurrency = wc_limepay_params_currency;	// Change variable name to match payment method
				lpWooComForm.limepayAmount = wc_limepay_params_amount;	// Change variable name to match payment method
				lpWooComForm.limepayParams = wc_limepay_params;	// Change variable name to match payment method
			} catch(e) {
				console.log('Limepay checkout parameters are missing');
				return false;
			}
			return true;
		},

		checkoutDisabledMessage: function(message) {
			$('#' + lpWooComForm.limepayParams.element_id).html(message);
		},

		isOrderReview: function() {
			return !!$('form#order_review').length;
		},

		isAddPaymentMethod: function() {
			return !!$('form#add_payment_method').length;
		},

		isCheckout: function() {
			return !!$('form.woocommerce-checkout').length;
		},

		renderLimepay: function() {
			if (lpWooComForm.loadLimepayParams()) {
				if (lpWooComForm.limepayParams.checkout_disabled) {
					lpWooComForm.checkoutDisabledMessage(lpWooComForm.limepayParams.checkout_disabled_message);
				} else {
					if (lpWooComForm.limepayParams.pay_source_only) {
						lpWooComForm.renderLimepayPaymentSource();
					} else {
						lpWooComForm.renderLimepayCheckout();
					}
				}
			}
		},

		renderLimepayCheckout: function() {
			var initParams = {
				publicKey: lpWooComForm.limepayParams.publishable_key,
				preventWalletSubmit: lpWooComForm.limepayParams.prevent_wallet_submit,
				email: lpWooComForm.limepayParams.email,
				customerFirstName: lpWooComForm.limepayParams.first_name,
				customerLastName: lpWooComForm.limepayParams.last_name,
				hidePayLaterOption: (lpWooComForm.limepayParams.available_payment_option === "paycard"),
				hideFullPayOption: (lpWooComForm.limepayParams.available_payment_option === "payplan"),
				paymentToken: lpWooComForm.handlePaymentToken,
				platform: lpWooComForm.limepayParams.platform,
				platformVersion: lpWooComForm.limepayParams.platform_version,
				platformPluginVersion: lpWooComForm.limepayParams.platform_plugin_version,
				customerToken: lpWooComForm.limepayParams.custom_token
			};

			lpWooComForm.limepayCheckout.init(initParams);
			lpWooComForm.limepayCheckout.errorHandler(this.handleLimepayError);
			lpWooComForm.limepayCheckout.eventHandler(this.handleLimepayEvent);

			var renderParams = {
				elementId: lpWooComForm.limepayParams.element_id,
				currency: lpWooComForm.limepayCurrency,
				amount: lpWooComForm.limepayAmount,
				paymentType: lpWooComForm.limepayParams.payment_type,
				showPayNow: false,
				showPayPlanSubmit: false,
			};
			if (lpWooComForm.limepayParams.primary_color) {
				renderParams.primaryColor = lpWooComForm.limepayParams.primary_color;
			}
			lpWooComForm.limepayCheckout.render(renderParams);
		},

		renderLimepayPaymentSource: function() {
			var initParams = {
				publicKey: lpWooComForm.limepayParams.publishable_key,
				// submitCallbackFunction: lpWooComForm.handlePaymentSource,
				// platform: lpWooComForm.limepayParams.platform,
				// platformVersion: lpWooComForm.limepayParams.platform_version,
				// platformPluginVersion: lpWooComForm.limepayParams.platform_plugin_version,
				customerToken: lpWooComForm.limepayParams.custom_token
			};

			lpWooComForm.limepayPaymentSource.init(initParams);
			// lpWooComForm.limepayPaymentSource.errorHandler(this.handleLimepayError); // TODO

			var renderParams = {
				elementId: lpWooComForm.limepayParams.element_id,
				showSubmit: false,
				hideSavedCards: false
			};
			if (lpWooComForm.limepayParams.primary_color) {
				renderParams.primaryColor = lpWooComForm.limepayParams.primary_color;
			}
			lpWooComForm.limepayPaymentSource.render(renderParams);
		},

		renderOnCheckoutUpdates: function() {
			$(document.body).on('updated_checkout', this.renderLimepay);
		},

		handlePaymentToken: function(paymentToken) {
			lpWooComForm.setPaymentToken(paymentToken);
			lpWooComForm.formSubmit();
		},

		handlePaymentSource: function(paymentSource) {
			if (paymentSource && paymentSource.cardPaymentSource) {
				lpWooComForm.setPaymentSource(paymentSource.cardPaymentSource.cardPaymentSourceId);
				lpWooComForm.formSubmit();
			}
		},

		onInitialSubmit: function() {
			if (lpWooComForm.isChecked()) {
				if (lpWooComForm.limepayParams.pay_source_only) {
					return lpWooComForm.requestPaymentSource();
				} else {
					return lpWooComForm.requestPaymentToken();
				}
			}
			return true;
		},

		formSubmit: function() {
			lpWooComForm.wcForm.trigger('submit');
		},

		requestPaymentToken: function() {
				var $pTInput = $('input.' + lpWooComForm.method + '-payment-token');
				if ($pTInput.length && $pTInput.val() && $pTInput.val() != '0' && !this.paymentTokenInvalid) {
					this.paymentTokenInvalid = true;
					return true;
				}
				lpWooComForm.limepayCheckout.submit();
				return false;
		},

		requestPaymentSource: function() {
				var $pSInput = $('input.' + lpWooComForm.method + '-payment-source');
				if ($pSInput.length && $pSInput.val() && $pSInput.val() != '0' && !this.paymentSourceInvalid) {
					this.paymentSourceInvalid = true;
					return true;
				}
				lpWooComForm.limepayPaymentSource.submit( lpWooComForm.handlePaymentSource );
				return false;
		},

		isChecked: function() {
			var limepayInput = $('input#payment_method_' + lpWooComForm.id);
			return limepayInput.is(':checked');
		},

		scrollIntoViewLimepay: function () {
			var limepayPH = document.getElementById(lpWooComForm.limepayParams.element_id);
		  limepayPH.scrollIntoView();
		},

		setPaymentToken: function(paymentToken) {
			$('input.' + lpWooComForm.method + '-payment-token').remove();
			lpWooComForm.wcForm.append($('<input type="hidden" />').addClass(lpWooComForm.method + '-payment-token').attr('name', lpWooComForm.id + '_payment_token').val(paymentToken));
			this.paymentTokenInvalid = false;
		},

		setPaymentSource: function(paymentSource) {
			$('input.' + lpWooComForm.method + '-payment-source').remove();
			lpWooComForm.wcForm.append($('<input type="hidden" />').addClass(lpWooComForm.method + '-payment-source').attr('name', lpWooComForm.id + '_payment_source').val(paymentSource));
			this.paymentSourceInvalid = false;
		},

		onHashChange: function() {
			var payActParams = window.location.hash.split('::');
			if ( ! payActParams || 2 > payActParams.length ) {
				return;
			}

			var payAction = JSON.parse(decodeURIComponent(payActParams[1]));
			var redirectURL = decodeURIComponent( payActParams[2] );
			var paymentMethodId = decodeURIComponent( payActParams[3] );

			if (paymentMethodId === lpWooComForm.id) {
				window.location.hash = '';
				lpWooComForm.openPayActionModal(payAction, redirectURL);
			}
		},

		orderPayPaymentAction: function() {
			if ( ! $( '#' + lpWooComForm.method + '-payment-action' ).length || ! $( '#' + lpWooComForm.method + '-payment-action-url' ).length ) {
				return false;
			}

			var payAction = JSON.parse($( '#' + lpWooComForm.method + '-payment-action' ).val());
			var payActionUrl = $( '#' + lpWooComForm.method + '-payment-action-url' ).val();

			lpWooComForm.openPayActionModal(payAction, payActionUrl);
			return true;
		},

		openPayActionModal: function( payAction, redirectURL ) {
			lpWooComForm.limepayCheckout.handlePaymentActionRequired(
			  payAction,
			  lpWooComForm.submitAfterPaymentAction( redirectURL ),
			  function ( message ) {
			    lpWooComForm.submitError( message );
			  }
			);
		},

		submitAfterPaymentAction: function ( redirectURL ) {
			return function() {
				$.ajax({
					type:	'GET',
					url: redirectURL + (lpWooComForm.isOrderReview() ? '&wc-limepay-order-review=1' : '') + '&methodId=' + lpWooComForm.id + '&is_ajax',
					dataType: 'json',
					success: function( result ) {
						try {
							if ( true === result.success ) {
								if ( result.data && result.data.payment_action_required) {
									var payAction = JSON.parse(result.data.payment_action_required);
									var redirect = result.data.redirect;
									lpWooComForm.openPayActionModal( payAction, redirect );
								} else if ( result.data && result.data.redirect) {
									if ( -1 === result.data.redirect.indexOf( 'https://' ) || -1 === result.data.redirect.indexOf( 'http://' ) ) {
										window.location = result.data.redirect;
									} else {
										window.location = decodeURI( result.data.redirect );
									}
								} else {
									throw 'Redirect URL not found';
								}
							} else if ( false === result.success ) {
								throw 'Result failure';
							} else {
								throw 'Invalid response';
							}
						} catch( err ) {
							if ( result.data.message ) {
								lpWooComForm.submitError( result.data.message );
							} else {
								lpWooComForm.submitError( '<div class="woocommerce-error">' + err.message + '</div>' );
							}
						}
					},
					error:	function( jqXHR, textStatus, errorThrown ) {
						lpWooComForm.submitError( '<div class="woocommerce-error">' + errorThrown + '</div>' );
					}
				});
			}
		},

		submitError: function( errorMessage ) {
			var checkoutForm = $( 'form.checkout' );
			$( '.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message' ).remove();
			checkoutForm.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout"><ul class="woocommerce-error"><li>' + errorMessage + '</li></ul></div>' );
			checkoutForm.removeClass( 'processing' ).unblock();
			checkoutForm.find( '.input-text, select, input:checkbox' ).trigger( 'validate' ).blur();
			lpWooComForm.scrollToNotices();
			$( document.body ).trigger( 'checkout_error' );
		},

		scrollToNotices: function() {
			var scrollElement = $( '.woocommerce-NoticeGroup-updateOrderReview, .woocommerce-NoticeGroup-checkout' );

			if ( ! scrollElement.length ) {
				scrollElement = $( '.form.checkout' );
			}
			$.scroll_to_notices( scrollElement );
		},

		handleLimepayEvent: function(lpEvent) {
			if (lpEvent.eventName == 'limepay_card_3DS_pending') {
				this.scrollIntoViewLimepay();
			}
		},

		handleLimepayError: function(error) {
			lpWooComForm.formUnblock();
			lpWooComForm.scrollIntoViewLimepay();
		},

		reset: function() {
			$('.' + lpWooComForm.method + '-payment-token').remove();
		},

		formUnblock: function() {
			lpWooComForm.wcForm && lpWooComForm.wcForm.unblock();
		},

		formBlock: function() {
			lpWooComForm.wcForm && lpWooComForm.wcForm.block();
		},

	};

	lpWooComForm.init();
});
