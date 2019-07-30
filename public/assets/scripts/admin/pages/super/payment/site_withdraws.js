/**
 * @author KCG
 * @since July 27, 2017
 */

define(['common', 'bootbox', 'alert', 'ajax_datatable', 'bs-datepicker', 'alert'], function (common, bootbox) {
    var fn = {
        init: function() {

            this.initElements();

            this.bindEvents();
            this.render();

            this.modal.init();
        },

        initElements: function() {
            this.$form  = $('form.form-datatable');
            this.$container = $('#site_withdraws');
        },

        bindEvents: function() {
            var self = this;

            // Handler when changing status for todo
            $(this.$container).on('click', 'button.button-submit', function() {
                var action = $('select.select-action').val();

                if (action == 'DELETE') {
                    $.alert.create({
                        message: 'Are you sure to delete the selected withdraws?',
                        title: 'Confirm',
                        cancelButton: {
                            label: "No",
                            className: 'btn-default',
                            callback: function() {
                            }
                        },
                        actionButton: {
                            label: "Yes",
                            className: 'blue',
                            callback: function() {
                                $('input[name="_action"]', self.$form).val(action);

                                fn.$form.submit();
                            }
                        }
                    });

                    return false;
                } else {
                    $('input[name="_action"]', self.$form).val(action);
                    self.$form.submit();
                }
            });
        },

        render: function() {
            this.renderDataTable();
            this.renderDateTimePicker();
            this.renderSelect2();

            common.handleUniform();
        },

        renderDateTimePicker: function() {
            $('.datepicker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: 'right',
                autoclose: true,
                changeDate: function() {
                }
            });
        },

        renderDataTable: function() {
            var self = this;
            this.$container.ajaxDatatable({
                success: function(html) {
                    self.initElements();
                    self.render();

                    self.modal.init();
                }
            });
        },

        renderSelect2: function() {
            common.renderSelect2();
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
	    
        modal: {
            init: function() {
                this.$container = $('#modal_withdraw');
                this.$form = $('form', this.$container);
                this.$amount = $('#amount', this.$form);
                this.$currentBalance = $('.current-balance', this.$form);
 				this.$newBalance = $('.new-balance', this.$form);
 				this.vBalance = this.$currentBalance.data('balance');

                this.$form.validate();
                this.bindEvents();
                this.render();
            },

            bindEvents: function() {
            	this.$amount.on('keyup change', this.setNewBalance);

            	this.$form.on('submit', function() {
            		$('.btn-submit-withdraw').addClass('disabled');
            	});
            },

            render: function() {
                Global.renderMaxlength();
            },

            setNewBalance: function() {
				var balanceValue = fn.modal.$amount.val();
				if ( !balanceValue || isNaN(balanceValue) ) {
					return false;
				}

				var vNewBalance = parseFloat(fn.modal.vBalance) - parseFloat(balanceValue);
				if ( vNewBalance < 0 ) {
					vNewBalance = 0;
					// fn.modal.$amount.val(fn.vBalance);
				}
				fn.modal.$newBalance.html( '$' + fn.formatCurrency(vNewBalance) );
			}
        }
    };

    return fn;
});