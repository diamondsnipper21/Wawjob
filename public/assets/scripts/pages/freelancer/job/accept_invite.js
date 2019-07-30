/**
 * job/accept_invite.js
 */

 define(['common', 'select2', 'jquery-form', 'jquery-validation', 'inputmask'], function (common) {

 	var fn = {
 		$form: null,
 		$formDeclined: null,
 		limit: 5000,

 		init: function () {

 			this.$form = $('#formInvitation');
 			this.$formDeclined = $('#formDeclineInvitation');
			this.$billingRate = $('#BillingRate');
			this.$earningRate = $('#EarningRate');
			this.$fee = $('#FeeValue');

 			this.formValidation();
 			this.bindEvents();

 			Global.renderMaxlength();
  		},

		bindEvents: function() {
            var self = this;

            fn.$billingRate.inputmask('decimal', {});
            fn.$earningRate.inputmask('decimal', {});

            fn.$billingRate.on('keyup change', function(e) {
                fn.$earningRate.val(self.formatCurrency(Math.round(fn.$billingRate.val() * parseFloat(rate) * 100) / 100));
                fn.$fee.html(self.formatCurrency( parseFloat(fn.$billingRate.val()) - parseFloat(fn.$earningRate.val()) ));
                fn.keyChangeHandler(fn.$billingRate);
            });

            fn.$earningRate.on('keyup change', function(e) {
                fn.$billingRate.val(self.formatCurrency(Math.round(fn.$earningRate.val() * 100 / parseFloat(rate)) / 100));
                fn.$fee.html(self.formatCurrency( parseFloat(fn.$billingRate.val()) - parseFloat(fn.$earningRate.val()) ));
                fn.keyChangeHandler(fn.$earningRate);
            });

			$('.more-link').on('click', function() {
				$(this).closest('.description').addClass('expanded');
			});

			$('.select2').select2({
                minimumResultsForSearch: 6
            });

            $('#btnDecline').on('click', function() {
            	$('[name="message"]', fn.$formDeclined).val($('#message').val());
            	fn.$formDeclined.submit();
            });
        },

		/**
	    * Format number with comma
	    */
	    formatCurrency: function(value) {
	    	var decimalValue = '';
	    	var precisionValue = '';

	    	value = value.toFixed(2);

	    	var formatValue = Math.round(value * 100) / 100;  

	    	if ( parseInt(formatValue) == value ){
	    		precisionValue = '00';
	    	} else if ( parseInt(formatValue * 10) == value * 10){
	    		precisionValue = '0';
	    	}

	    	var arrayValue = value.toString().split('.');

	    	decimalValue = arrayValue[0];
	    	if ( arrayValue.length > 1 ) {
	    		precisionValue = arrayValue[arrayValue.length - 1];
	    	}

	    	var formatString = '';
	    	var j = 0;

	    	if ( decimalValue.length > 3 ) {
	    		for (var i = decimalValue.length - 1; i >= 0; i--) {
	    			formatString += decimalValue.charAt(i);
	    			j++;

	    			if ( j == 3) {
	    				formatString += ',';
	    				j = 0;
	    			}
	    		}
	    		formatString = formatString.split('').reverse().join('');
	    	} else {
	    		formatString = decimalValue;
	    	}

	    	if ( formatString == '' ) {
	    		formatString = '0';
	    	}

	    	if ( precisionValue != '' ) {
	    		formatString += '.' + precisionValue;
	    	}

	    	if ( formatString[0] == ',' ) {
				formatString = formatString.substr(1);
			}

	    	return formatString;
	    },

	    errorHandler: function($element,text) {

            $element.parent().addClass('has-error');
            var $container = $element.parent().parent();
            var $error = $container.find('span.error');

            var html = '<span class="error">' + text + '</span>';

            if ($error.length) {
                $error.text(text);
            } else {
                $element.parent().after(html);
            }
        },

		keyChangeHandler: function($element) {

			if ( $element.val().trim() == '' || ( /[a-zA-Z]+/.test($element.val()) )) {

				var text = trans.please_enter_a_valid_number;
                fn.errorHandler($element,text);

			}  else if ( parseFloat($element.val()) > parseInt(trans.MAX_HOURLY_PRICE) && $element.hasClass('billing-hourly-rate') ) {

				var text = trans.please_enter_a_value_less_than_or_equal_to_999;
                fn.errorHandler($element,text);

			} else if ( parseFloat($element.val()) > parseInt(trans.MAX_FIXED_PRICE) && $element.hasClass('billing-fixed-rate') ) {
                
                var text = trans.please_enter_a_value_less_than_or_equal_to_9999999;
                fn.errorHandler($element,text);

            } else if ( parseInt($element.val()) < 1 && ($element.hasClass('billing-hourly-rate') || $element.hasClass('billing-fixed-rate')) ) {

                var text = trans.please_enter_a_value_greater_than_or_equal_to_1;
                fn.errorHandler($element,text);

            } else {
				$element.parent().removeClass('has-error');
				var $container = $element.parent().parent();
				$container.find('span.error').remove();
			}
		},

        formValidation: function() {
            this.$form.validate({
                focusInvalid: false,
                ignore: '',
                rules: {
                    billing_hourly_rate: {
                        min: 1,
                        max: 999
                    },

                    billing_fixed_rate: {
                        min: 1,
                        max: 9999999
                    }
                },
                highlight: function (element) {
					$(element).parent().addClass('has-error');
				},
				success: function (label, element) {
					$(element).parent().removeClass('has-error');
				},
                errorPlacement: function (error, $element) {
                	if ( $element.attr('id') != 'message' ) {
						var $container = $element.parent().parent();
						$container.find('span.error').remove();

						error.insertAfter($element.parent());
					} else {
						error.insertAfter($element);
					}
                },
                submitHandler: function (form) {
					form.submit();
				}
            });
        },

		calculate: function(e, DEFAULT_LENGTH) {
			var $obj = $(e.target);
			var $count = $('span.letters', $obj.closest('.parent'));
			var text = $obj.val();
			var count = DEFAULT_LENGTH - text.length;

			if (count < 0) {
				$count.html(0);

				$obj.val(text.substring(0, DEFAULT_LENGTH));

				if (typeof e != 'undefined')
					e.preventDefault();
				return false;
			}

			$count.html(count);
			return true;
		},
	};

	return fn;
});