/**
 * @author PYH
 * @since July 30, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'tmpl', 'alert', 'ajax_datatable', 'bs-datepicker', 'bs-modalmanager', 'bs-modal'], function (common, tmpl) {

    var fn = {
        init: function() {
            this.initElements();
            this.bindEvents();
            this.render();
            
            this.modal.init();
        },

        initElements: function() {
            this.$container   = $('#email_templates');
            this.$form        = $('form.form-datatable');
            this.$formButton  = $('button.button-submit', this.$form);
        },

        bindEvents: function() {
            var self = this;

            $('.edit-link').off('click');
            $('.edit-link').on('click', function() {
                var $tr = $(this).closest('tr');
                var index = $tr.data('index');

                data = data_collection[index];

                self.modal.open(data);

                return false;
            });

            $('.add-link').off('click');
            $('.add-link').on('click', function() {
                var data = $(this).data('object');
                self.modal.open(data);

                return false;
            });


            $(this.$container).off('click', 'button.button-submit');
            $(this.$container).on('click', 'button.button-submit', function() {
                var action = $('#template_action').val();
                $('input[name="_action"]').val('CHANGE_STATUS');

                if (action == '2') { // Delete
                    $.alert.create({
                        message: 'Are you sure to delete these email templates?',
                        title: 'Delete Templates',
                        cancelButton: {
                            label: "Cancel",
                            className: 'btn-default',
                            callback: function() {
                            }
                        },
                        actionButton: {
                            label: "Delete",
                            className: 'blue',
                            callback: function() {
                                self.submit();
                            }
                        }
                    });
                } else {
                    self.submit();
                }
            });
        },

        render: function() {
            common.initModal();

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
                    // var $next = $(this).next();
                    // var $prev = $(this).prev();
                }
            });
        },

        renderSelect2: function() {
            common.renderSelect2();
        },

        renderDataTable: function() {
            var self = this;
            this.$container.ajaxDatatable({
                success: function(html) {
                    self.init();
                }
            });
        },

        submit: function() {
            this.$form.submit();
        },

        modal: {

            init: function() {
                this.$modalContainer = $('#modal_email_template_container');
            },

            bindEvents: function() {
                var self = this;

                this.$modal.on('show', function() {
                    self.render();

                    setTimeout(function(){
                        $(window).trigger('resize');
                    }, 1500);
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
            },

            open: function(data) {
                tmpl.arg = 'template';
                var html = tmpl('tmpl_email_template', data);

                this.$modalContainer.html(html);
                this.$modal = $('#modal_email_template', this.$modalContainer);
                        
                this.bindEvents();

                this.$modal.modal();
            }
        }
    };

    return fn;
});
define.amd = amd;