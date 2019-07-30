/**
 * job/my_applicant.js
 */

define(['stars', 'jquery-validation', 'jquery-form', 'inputmask'], function (stars) {
 	var fn = {
 		init: function () {
 			this.$formChange = $('#formChange');
 			this.$formWithdraw = $('#formWithdraw');
			this.$billingRate = $('#BillingRate');
			this.$earningRate = $('#EarningRate');
			this.$fee = $('#FeeValue');

			this.bindEvents();
            this.render();
  		},

        bindEvents: function() {
            var self = this;

            // fn.$billingRate.inputmask('decimal', {});
            // fn.$earningRate.inputmask('decimal', {});

            fn.$billingRate.on('keyup change', function(e) {
                fn.$earningRate.val(self.currencyFormat(Math.round(fn.$billingRate.val() * parseFloat(rate) * 100) / 100));
                fn.$fee.html(self.currencyFormat( parseFloat(fn.$billingRate.val()) - parseFloat(fn.$earningRate.val()) ));
                
                return true;
            });

            fn.$earningRate.on('keyup change', function(e) {
                fn.$billingRate.val(self.currencyFormat(Math.round(fn.$earningRate.val() * 100 / parseFloat(rate)) / 100));
                fn.$fee.html(self.currencyFormat( parseFloat(fn.$billingRate.val()) - parseFloat(fn.$earningRate.val()) ));
            
                return true;
            });
        },	

		currencyFormat: function(val) {
            if ( val == '' || isNaN(val) ) {
                val = 0; 
            }

            return parseFloat(Math.round(val * 100) / 100).toFixed(2);
		},

        render: function() {
            this.$formChange.validate();
            this.$formWithdraw.validate();

            stars.init($('.client-score .stars'));

            Global.renderSelect2();
            Global.renderTooltip();
        }
	};
	
	return fn;
});