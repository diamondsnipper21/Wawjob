/**
* job/job_apply.js
*/

define(['jquery-validation', 'jquery-form', 'inputmask', 'select2'], function () {
    var fn = {
        init: function () {
			this.$form = $('#JobDetailForm');
			this.$billingRate = $('#BillingRate');
			this.$earningRate = $('#EarningRate');
			this.$fee = $('.service-fee-value');
            this.$btnSubmit = $('#acceptSubmitProposal');

			this.bindEvents();
			this.render();
        },

        bindEvents: function() {
            var self = this;

            fn.$billingRate.on('keyup change', function(e) {
                fn.$earningRate.val(self.currencyFormat(Math.round(fn.$billingRate.val() * parseFloat(rate) * 100) / 100));
                fn.$fee.html(self.currencyFormat( parseFloat(fn.$billingRate.val()) - parseFloat(fn.$earningRate.val()) ));
            });

            fn.$earningRate.on('keyup change', function(e) {
                fn.$billingRate.val(self.currencyFormat(Math.round(fn.$earningRate.val() * 100 / parseFloat(rate)) / 100));
                fn.$fee.html(self.currencyFormat( parseFloat(fn.$billingRate.val()) - parseFloat(fn.$earningRate.val()) ));
            });

            // Check the available connections
            this.$btnSubmit.on('click', function() {
                if (fn.checkAvailableConnections())
                    self.$form.submit();
            });

            $('#featured').on('click', function() {
                var $this = $(this);
                var needed_connections = $('.needed-connections').data('value');
                var total_connections  = $('.total-connections').data('value');

                if ( $this.is(':checked') )
                    needed_connections = needed_connections * 2;

                $('.needed-connections strong').text(needed_connections);
                $('.total-connections strong').text(total_connections - needed_connections + ' / ' + total_connections);
            });
        },

        render: function() {
            $('.select2').select2({
                minimumResultsForSearch: 6
            });

            Global.renderFileInput();
            Global.renderMaxlength();
            Global.renderUniform();

            this.$form.validate();
        },

        // Check the available connections before submit
        checkAvailableConnections: function() {
            $('#errorConnection').remove();

            var connections = parseInt(fn.$form.data('connections'));
            var neededConnections = parseInt(fn.$form.data('needed-connections'));

            if ( connections > 0 ) {
                if ( connections < neededConnections ) {
                    var html = '<div class="alert alert-danger fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button><p>' + errorNotEnoughConnections + '</p></div>';
                    $('.page-content').find('.box-alert-section').html(html);

                    return false;
                } else {
                    if (!fn.$form.valid())
                        return false;
                    
                    fn.$btnSubmit.attr('disabled', true);
                    return true;
                }
            } else {
                var html = '<div class="alert alert-danger fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times</button><p>' + errorConnectionLimit + '</p></div>';
                $('.page-content').find('.box-alert-section').html(html);

                return false;
            }
        },

        currencyFormat: function(val) {
        	if ( val == '' || isNaN(val) ) {
        		val = 0; 
        	}

            return parseFloat(Math.round(val * 100) / 100).toFixed(2);
        }
    };

    return fn;
});