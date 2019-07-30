/**
 * withdraw.js
 */

define(['bootbox', 'jquery-validation'], function (bootbox) {
 	var fn = {

 		$form: null,
 		$withdrawForm: null,
 		$withdrawAmount: null,
 		$currentBalance: null,
 		$newBalance: null,
 		$fee: null,
 		$feeTooltip: null,
 		$perPayment: null,
 		$amount: null,
 		$gateway: null,
 		$maximum: null,
 		$btnPreview: null,
 		$btnWithdraw: null,
 		$btnBackGetPaid: null,
 		vBalance: 0,
 		vMaximumSelected: 0,
 		vMin: 0,
 		vMax: 0,
 		vFee: 0,
 		vFeeFixed: 0,

 		init: function () {
 			this.$form = $('#form_user_preview_get_paid');
 			this.$btnPreview = $('.btn-preview', this.$form);
 			this.$withdrawAmount = $('#withdraw_amount', this.$form);
 			this.$currentBalance = $('.current-balance', this.$form);
 			this.$newBalance = $('.new-balance', this.$form);
 			this.$fee = $('.fee', this.$form);
 			this.$feeTooltip = $('.fee-tooltip', this.$form);
 			this.$perPayment = $('.per-payment', this.$form);
 			this.$maximum = $('.maximum', this.$form);
 			this.$amount = $('.gateway-info .amount', this.$form);
 			this.$gateway = $('.gateway', this.$form);

 			this.vBalance = parseFloat(this.$currentBalance.data('balance'));
 			this.vMin = parseFloat(this.$currentBalance.data('min'));
 			this.vMax = parseFloat(this.$currentBalance.data('max'));

			// Step - Preview
			if ( this.$form.length ) {
				this.validate();
				Global.renderUniform();
				
				if ( this.$withdrawAmount.val() ) {
					this.$withdrawAmount.trigger('change');
				}

				fn.changePaymentGateway();

	            // Change other payment method if default selected is 0 maximum withdrawal
	            if ( fn.vMaximumSelected <= 0 ) {
	            	$('input[name="payment_gateway"]').each(function() {
	            		if ( parseFloat($(this).data('maximum')) > 0 ) {
	            			$(this).prop('checked', true).trigger('change');

	            			fn.updatePaymentGateway($(this));

	            			Global.updateUniform();

				            fn.updateMaximum($(this));

	            			return false;
	            		}
	            	});
	            }

	            $('input[name="payment_gateway"]').on('change', this.changePaymentGateway);
	            this.$withdrawAmount.on('change', this.setNewBalance);

	            $('.radiobox.disabled').first().addClass('mt-4');
	        }

			// Step - Get Paid
			this.$withdrawForm = $('#form_user_withdraw');
			this.$btnWithdraw = $('.btn-withdraw', this.$withdrawForm);
			this.$btnBackGetPaid = $('.btn-back-get-paid', this.$withdrawForm);

			if ( this.$withdrawForm.length ) {
				this.bindEvents();
			}

			$('[data-toggle="tooltip"]').tooltip();
		},

        bindEvents: function() {
        	this.$btnWithdraw.on('click', this.withdraw);
			this.$btnBackGetPaid.on('click', this.backGetPaid);
        },

		validate: function() {
			this.$form.validate();
		},

		processFee: function() {
			var feeHtml = '';
			var fee = 0;

            if ( fn.vFee > 0 || fn.vFeeFixed > 0 ) {            	
            	if ( fn.vFeeFixed > 0 ) {
            		feeHtml += '$' + Global.formatCurrency(fn.vFeeFixed);
            	}

            	if ( fn.vFee > 0 ) {
            		if ( feeHtml != '' ) {
            			feeHtml += ' + ';
            		}

            		feeHtml += fn.vFee + '%';
            	}

            	fn.$feeTooltip.removeClass('hide');
            	fn.$perPayment.css('display', 'inline-block');

            	var balanceValue = fn.$withdrawAmount.val();
				if ( !balanceValue || isNaN(balanceValue) ) {
					balanceValue = 0;
				}
				balanceValue = parseFloat(balanceValue);

            	fee = fn.vFeeFixed + balanceValue * fn.vFee / 100;
            	fn.$fee.html('$ ' + Global.formatCurrency(fee));
            } else {
            	feeHtml = trans.free;

            	fn.$feeTooltip.addClass('hide');
            	fn.$perPayment.css('display', 'none');
            	fn.$fee.html(feeHtml);
            }

            $('i', fn.$feeTooltip).attr('title', trans.tip_fee_of_withdraw_amount.replace(':fee', feeHtml)).tooltip('fixTitle');

            return fee;
		},

        changePaymentGateway: function() {
        	var $gateways = $('input[name="payment_gateway"]:enabled');
        	if ( !$gateways.length ) {
        		$('.gateway-info').addClass('hide');
        		fn.$btnPreview.addClass('disabled');

        		return false;
        	}

        	var $gateway = $('input[name="payment_gateway"]:enabled:checked');
            if ( !$gateway.length ) {
				$gateway = $('input[name="payment_gateway"]:enabled').first().prop('checked', true).trigger('change');
            }

            if ( $gateway.length ) {
            	fn.vFee = parseFloat($gateway.data('fee'));
	            fn.vFeeFixed = parseFloat($gateway.data('fee-fixed'));

	            fn.updatePaymentGateway($gateway);

	            fn.updateMaximum($gateway);

	            fn.setNewBalance($gateway);

				// Check gateway
	            if ( $gateway.data('gateway') == '3' ) {
	            // Wechat
	            	$('.gateway-info .currency').html(trans.cny + ' ');
	            	$('.wechat-gateway-info').removeClass('hidden');
	            } else if ( $gateway.data('gateway') == '6' ) {
	            // Payoneer
	            	$('.gateway-info .currency').html(trans.eur + ' ');
	            	$('.payoneer-gateway-info').removeClass('hidden');
	            } else {
	            	$('.gateway-info .currency').html('$');
	            	$('.wechat-gateway-info').addClass('hidden');
	            	$('.payoneer-gateway-info').addClass('hidden');
	            }
	        }
        },

        updatePaymentGateway: function($obj) {
        	var $gatewayBox = $obj.closest('.radiobox');

			$('.radiobox', fn.$form).removeClass('default-boxshadow');
            $gatewayBox.addClass('default-boxshadow');

            fn.processFee();

            // For only PayPal if user is Buyer
            if ( $obj.data('gateway') == '1' ) {
            	$('.note-paypal').removeClass('hide');
            } else {
            	$('.note-paypal').addClass('hide');
            }
        },

        updateMaximum: function($obj) {
            var vMaximum = $obj.data('maximum');
            if ( parseFloat(vMaximum) > parseFloat(fn.vMax) ) {
            	vMaximum = fn.vMax;
            }

            fn.vMaximumSelected = parseFloat(vMaximum);

			fn.$maximum.html(Global.formatCurrency(vMaximum));
            fn.$withdrawAmount.data('rule-max', vMaximum);
        },

		setNewBalance: function() {
			var $gateway = $('input[name="payment_gateway"]:enabled:checked');
			var $gatewayBox = $gateway.closest('.radiobox');

			fn.$gateway.html($gateway.data('gateway-label') + ' - ' + $('.box-title', $gatewayBox).text());
			fn.$amount.html('0.00');

			var balanceValue = fn.$withdrawAmount.val();
			if ( !balanceValue || isNaN(balanceValue) ) {
				return false;
			}

			balanceValue = parseFloat(balanceValue);

			var changedValue = balanceValue;

			var fee = fn.processFee();

			if ( balanceValue < fee ) {
				changedValue = fee + fn.vMin;
			} else {

				var rounded = Global.formatCurrency(balanceValue);
				if ( balanceValue != rounded ) {
					changedValue = rounded;
				}

				var vNewBalance = fn.vBalance - balanceValue;
				if ( vNewBalance < 0 ) {
					if ( fn.vBalance > fn.vMax ) {
						changedValue = fn.vMax;
						vNewBalance = fn.vBalance - fn.vMax;
					} else {
						changedValue = fn.vBalance;
						vNewBalance = fn.vBalance - fn.vBalance;
					}
				}

			}

			fn.$withdrawAmount.val(Global.formatCurrency(changedValue));

            if ( $gateway.data('gateway') == '3' ) {
            // Wechat
            	fn.$amount.html(Global.formatCurrency((changedValue - fee) * trans.cny_exchange_rate));
            } else if ( $gateway.data('gateway') == '6' ) {
            // Payoneer
            	fn.$amount.html(Global.formatCurrency((changedValue - fee) * trans.eur_exchange_rate));
            } else {
            	fn.$amount.html(Global.formatCurrency(changedValue - fee));
            }
		},

	    submit: function() {
	    	fn.$withdrawForm.submit();
	    },

	    withdraw: function() {
	    	fn.$btnWithdraw.attr('disabled', 'disabled');
	    	fn.$btnBackGetPaid.attr('disabled', 'disabled');
	    	fn.submit();
	    },

	    backGetPaid: function() {
	    	$('[name=_action]', fn.$withdrawForm).val('requestGetPaid');
	    	fn.submit();
	    },

	};

	return fn;
});