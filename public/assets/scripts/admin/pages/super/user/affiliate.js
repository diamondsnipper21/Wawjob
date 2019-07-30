define(['page_user_common', 'common', 'bs-datepicker', 'ajax_datatable'], function (page_user_common, common) {
    var fn = {
        $ajaxcontainer: null,

        init: function() {
            page_user_common.init();
            
            this.initElements();
            this.bindEvents();
            this.render();
        },

        initElements: function() {
            this.$ajaxcontainer = $('#tab_affiliate_payment_history');
        },

        bindEvents: function() {
            $('input[name="check_pay"]').on('change', function() {
                if ( $(this).attr('checked') == 'checked' ) {
                    $('.button-pay').attr('disabled', false);
                    $('.button-cancel').attr('disabled', false);
                } else {
                    $('.button-pay').attr('disabled', true);
                    $('.button-cancel').attr('disabled', true);
                }
            });
        },

        render: function() {
            this.renderDataTable();
            this.renderDateTimePicker();
            this.renderSelect2();

            common.handleUniform();
        },

        renderDataTable: function() {
            var self = this;
            fn.$ajaxcontainer.ajaxDatatable({
                success: function(html) {
                    self.init();
                }
            });
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

        renderSelect2: function() {
            common.renderSelect2();
        },
    };

    return fn;
});