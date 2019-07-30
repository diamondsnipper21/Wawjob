/**
 * deposit.js
 * @author Ro Un Nam
 */

define(['bootbox', 'moment', 'adyen', 'datepicker', 'jquery-validation'], function (bootbox, moment) {
 	var fn = {

 		$form: null,
 		$depositForm: null,
 		$action: null,
 		$gateway: null,
 		$paymentGateway: null,
 		$depositAmount: null,
 		$currentBalance: null,
 		$newBalance: null,
 		$btnDeposit: null,
 		$tokenCSE: null,
 		$fee: null,
 		$feeTooltip: null,
 		$total: null,
 		vFee: 0,
 		vBalance: 0,
 		vQRCodeCycle: 30,
 		vCheckWCCycle: 50,

 		init: function () {
 			this.$form = $('#form_user_preview_deposit');
 			this.$depositAmount = $('#deposit_amount', this.$form);
 			this.$currentBalance = $('.current-balance', this.$form);
 			this.$newBalance = $('.new-balance', this.$form);
 			this.$fee = $('.fee', this.$form);
 			this.$feeTooltip = $('.fee-tooltip', this.$form);
 			this.$total = $('.total', this.$form);
 			this.vBalance = parseFloat(this.$currentBalance.data('balance'));

			// Step - Deposit
			this.$depositForm = $('#form_user_deposit');
			this.$action = $('input[name="_action"]', this.$depositForm);
			this.$gateway = $('input[name="gateway"]', this.$depositForm);
			this.$paymentGateway = $('input[name="payment_gateway"]', this.$depositForm);
			this.$btnDeposit = $('.btn-deposit', this.$depositForm);

			// Step - Preview			
			Global.renderUniform();

			this.$depositAmount.on('change', this.setNewBalance);
			if ( this.$depositAmount.val() ) {
				this.$depositAmount.trigger('change');
			}

			fn.changePaymentGateway();

			this.validate();
			this.bindEvents();

			$('[data-toggle="tooltip"]').tooltip();
		},

        bindEvents: function() {
        	$('input[name="payment_gateway"]').on('change', this.changePaymentGateway);

			this.$btnDeposit.on('click', this.deposit);

			$('.date-picker', fn.$depositForm).datepicker({
				orientation: 'auto',
				autoclose: true,
				startDate: moment().format('YYYY-MM-DD')
			}).on('changeDate', function(e) {
				var date = moment(e.date);
				date = date.format('YYYY-MM-DD');
				$('#deposit_date').focus().val(date).trigger('change').blur();
			});

			$('.copy-tooltip').tooltip();
			$('#btn_copy').on('click', function() {
				document.getElementById('copy_address').select();
				try {
					var success = document.execCommand('copy');
					if (success) {
						$('.copy-tooltip').trigger('copied', ['Copied!']);
					} else {
						$('.copy-tooltip').trigger('copied', ['Copy with Ctrl-c']);
					}
				} catch (err) {
					$('.copy-tooltip').trigger('copied', ['Copy with Ctrl-c']);
				}
			});

			// Handler for updating the tooltip message.
			$('.copy-tooltip').bind('copied', function(event, message) {
				$('.copy-tooltip').attr('title', message)
								.tooltip('fixTitle')
								.tooltip('show')
								.attr('title', 'Copy to Clipboard')
								.tooltip('fixTitle');
			});

			// Request QR code
			if ( fn.$action.val() == 'deposit' && fn.$gateway.val() == '3' ) {
				//fn.requestQRCode();
			}
        },

		validate: function() {
			if ( fn.$form )
				fn.$form.validate();

			if ( fn.$depositForm )
				fn.$depositForm.validate();
		},

        changePaymentGateway: function() {
            var $gateway = $('input[name="payment_gateway"]:checked');
            var $gatewayBox = $gateway.closest('.radiobox');
        	
            $('.radiobox', fn.$form).removeClass('default-boxshadow');
            $gatewayBox.addClass('default-boxshadow');

            // Wechat
            if ( $gateway.data('gateway') == '3' ) {
            	$('.gateway-info').removeClass('hidden');

            	var amount = fn.$depositAmount.val().trim() != '' ? parseFloat(fn.$depositAmount.val().trim()) : 0;
            	$('.gateway-info .amount').html(Global.formatCurrency(amount * trans.cny_exchange_rate));
            } else {
            	$('.gateway-info').addClass('hidden');
            }

            fn.vFee = parseFloat($gateway.data('fee'));

            fn.setNewBalance();
        },

		setNewBalance: function() {
			var balanceValue = fn.$depositAmount.val();
			if ( !balanceValue || isNaN(balanceValue) ) {
				balanceValue = 0;
			}
			balanceValue = parseFloat(balanceValue);

			var fee = 0;
			if ( fn.vFee > 0 ) {
				fee = Global.formatCurrency(balanceValue * fn.vFee / 100);
				fn.$fee.html('$ ' + fee);

				fn.$feeTooltip.removeClass('hide');
				$('i', fn.$feeTooltip).attr('title', trans.tip_fee_of_deposit_amount.replace(':fee', fn.vFee)).tooltip('fixTitle');
			} else {
				fn.$fee.html(trans.free);

				fn.$feeTooltip.addClass('hide');
			}

			var rounded = Global.formatCurrency(balanceValue);
			if ( balanceValue != rounded ) {
				balanceValue = rounded;
				fn.$depositAmount.val(balanceValue);
			}
			
			var total = Global.formatCurrency(balanceValue + parseFloat(fee))
			fn.$total.html(total);

			/*
			var vNewBalance = fn.vBalance + balanceValue;
			if ( vNewBalance < 0 ) {
				vNewBalance = 0;
				fn.$depositAmount.val(fn.vBalance);
			}
			fn.$newBalance.html( '$' + Global.formatCurrency(vNewBalance, true) );
			*/

        	var $gateway = $('input[name="payment_gateway"]:checked');

        	// Wechat
            if ( $gateway.data('gateway') == '3' ) {
        		$('.gateway-info .amount').html(Global.formatCurrency(parseFloat(balanceValue) * trans.cny_exchange_rate));
        	}
		},

		submit: function() {
			var gateway = $('input[name="gateway"]', fn.$depositForm).val();

			if ( gateway == 2 ) {
		 		$.ajax({
		 			url: trans.cse_url,
		 			type: 'post',
		 			data: {'id': $('input[name="payment_gateway"]', fn.$depositForm).val()},
		 			success: function(json) {
		 				if ( json.status == 'success' ) {
							var cseInstance = adyen.encrypt.createEncryption(json.key, {});
		 					var cseToken = cseInstance.encrypt(json.data);
		 					$('input[name="_tokenCSE"]', fn.$depositForm).val(cseToken);
		 					fn.$depositForm.submit();
		 					fn.$btnDeposit.addClass('disabled');
		 				}       
		      		}
		  		});
			} else {
	    		fn.$depositForm.submit();
	    	}
	    },

		deposit: function() {
			fn.submit();
		},

		requestQRCode: function() {
			fn.vQRCodeCycle--;

			// Request WeChat QR code
			$.post(fn.$depositForm.data('qrcode-action'), {
				id: $('input[name="wechat_queue_id"]', fn.$depositForm).val(),
            	user_payment_gateway_id: fn.$paymentGateway.val()
            }, function (json) {
            	if ( json.success && json.qrcode ) {
            		$('img#qrcode').attr('src', json.qrcode);
            		$('.box-waiting-qrcode').addClass('hide');
            		$('.btn-deposit').addClass('hide');

            		setTimeout(fn.checkWCPayment, 5000);
            	} else {
            		if ( fn.vQRCodeCycle > 0 ) {
            			setTimeout(fn.requestQRCode, 5000);
            		}
            	}
            });
		},

		checkWCPayment: function() {
			fn.vCheckWCCycle--;

			// Check WeChat Payment
			$.post(fn.$depositForm.data('wcpayment-action'), {
				id: $('input[name="wechat_queue_id"]', fn.$depositForm).val(),
            	user_payment_gateway_id: fn.$paymentGateway.val()
            }, function (json) {
            	if ( json.success ) {
            		location.href = fn.$depositForm.attr('action');
            	} else {
            		if ( fn.vCheckWCCycle > 0 ) {
            			setTimeout(fn.checkWCPayment, 5000);
            		}
            	}
            });
		}

	};

	return fn;
});