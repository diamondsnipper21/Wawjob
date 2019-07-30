/**
 * Payment Methods Page
 */

var amd = define.amd;
define.amd= false;

define(['common', 'jquery-form', 'alert', 'ajax_datatable'], function (common) {

    var fn = {
        init: function() {
            this.initElements();            
            this.bindEvents();
            this.render();
        },

        initElements: function() {
            this.$container = $('#payment_methods');
            this.$form = $('form.form-datatable', this.$container);
        },

        bindEvents: function() {
        	var self = this;

			$(this.$container).off('click', 'button.button-submit');
            $(this.$container).on('click', 'button.button-submit', function() {
                var action = $('#template_action').val();
                $('input[name="_action"]').val('CHANGE_STATUS');

                var label = 'disable';
                var title = 'Disable';

                if ( action == '1' ) {
                    label = 'enable';
                    title = 'Enable';
                } else if ( action == '2' ) {
                    label = 'disable withdrawal';
                    title = 'Disable Withdrawal';
                } else if ( action == '3' ) {
                    label = 'enable withdrawal';
                    title = 'Enable Withdrawal';
                } else if ( action == '4' ) {
                    label = 'disable deposit';
                    title = 'Disable deposit';
                } else if ( action == '5' ) {
                    label = 'enable deposit';
                    title = 'Enable deposit';
                }

                $.alert.create({
                    message: 'Are you sure to ' + label + ' these payment methods?',
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
                            self.submit();
                        }
                    }
                });
            });
            /*
            $(fn.$container).on('click', 'button.button-submit', function() {
            	var confirm = 'Are you sure to change the settings?';

                $.alert.create({
                    message: confirm,
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
                            $('[name="_action"]', fn.$form).val('SAVE');
                            fn.$form.submit();
                        }
                    }
                });
            });
            */
        },

        render: function() {
            common.initModal();

            this.renderDataTable();
            this.renderSelect2();

            common.handleUniform();
        },

        renderDataTable: function() {
            var self = this;
            this.$container.ajaxDatatable({
                success: function(html) {
                    self.init();
                }
            });
        },

        renderSelect2: function() {
            common.renderSelect2();
        },

        submit: function() {
            this.$form.submit();
        },

        modal: {

            init: function() {
                this.$modalContainer = $('#modalPaymentMethod');
            },

            bindEvents: function() {
                var self = this;

                this.$modal.on('show', function() {
                    self.render();
                });

                self.$modal.on('submit', 'form', function() {
                    $(this).ajaxSubmit({
                        dataType: 'json',
                        success: function(data) {
                            var success = Global.showAlertMessages(data.alerts);

                            if (!success)
                                return;

                            self.$modal.modal('hide');
                            fn.submit();
                        }
                    });

                    return false;
                })
            },

            render: function() {
                var self = this;

                this.$modalForm = $('.form-horizontal', self.$modal);
                this.$modalForm.validate();

                common.renderSelect2();
                common.handleUniform();
            },
        }
    };

    return fn;
});

define.amd = amd;