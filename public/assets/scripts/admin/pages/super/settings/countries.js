var amd = define.amd;
define.amd= false;

define(['common', 'alert', 'ajax_datatable', 'jquery-form'], function (common) {

    var fn = {
        init: function() {
            var self = this;

            this.initElements();
            
            this.render();
        },

        initElements: function() {
            this.$container = $('#countries');
            this.$form      = $('form.form-datatable');
        },

        render: function() {
            this.renderDataTable();
            this.renderSelect2();
            this.renderButton();

            common.handleUniform();
        },

        renderSelect2: function() {
            $('select.select2').select2({
                minimumResultsForSearch: -1
            });
        },

        renderDataTable: function() {
            var self = this;
            this.$container.ajaxDatatable({
                success: function(html) {
                    self.init();
                }
            });
        },

        renderButton: function() {
            var self = this;

            $(this.$container).off('click', 'button.button-submit');
            $(this.$container).on('click', 'button.button-submit', function() {
                var action = $('#template_action').val();

                if ( action ) {
                    var label = 'delete';
                    var title = 'Delete Countries';

                    if ( action == 'ENABLE_PAYPAL' ) {
                        label = 'enable PayPal for ';
                        title = 'Enable PayPal';
                    } else if ( action == 'DISABLE_PAYPAL' ) {
                        label = 'disable PayPal for ';
                        title = 'Disable PayPal';
                    } else if ( action == 'ENABLE_PAYONEER' ) {
                        label = 'enable Payoneer for ';
                        title = 'Enable Payoneer';
                    } else if ( action == 'DISABLE_PAYONEER' ) {
                        label = 'disable Payoneer for ';
                        title = 'Disable Payoneer';
                    } else if ( action == 'ENABLE_SKRILL' ) {
                        label = 'enable Skrill for ';
                        title = 'Enable Skrill';
                    } else if ( action == 'DISABLE_SKRILL' ) {
                        label = 'disable Skrill for ';
                        title = 'Disable Skrill';
                    } else if ( action == 'ENABLE_WECHAT' ) {
                        label = 'enable WeChat for ';
                        title = 'Enable WeChat';
                    } else if ( action == 'DISABLE_WECHAT' ) {
                        label = 'disable WeChat for ';
                        title = 'Disable WeChat';
                    } else if ( action == 'ENABLE_BANK' ) {
                        label = 'enable bank transfor for ';
                        title = 'Enable Bank Transfer';
                    } else if ( action == 'DISABLE_BANK' ) {
                        label = 'disable bank transfor for ';
                        title = 'Disable Bank Transfer';
                    }

                    $.alert.create({
                        message: 'Are you sure to ' + label + ' these countries?',
                        title: title,
                        cancelButton: {
                            label: "Cancel",
                            className: 'btn-default',
                            callback: function() {
                            }
                        },
                        actionButton: {
                            label: "Submit",
                            className: 'blue',
                            callback: function() {
                                $('input[name="_action"]').val(action);
                                self.$form.submit();
                            }
                        }
                    });
                }
            });
        },
    };

    return fn;
});
define.amd = amd;