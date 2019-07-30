/**
 * @author PYH
 * @since July 9, 2017
 */

define(['page_user_common', 'common', 'bs-modalmanager', 'bs-modal', 'bs-datepicker', 'reasonbox', 'ajax_datatable'], function (page_user_common, common) {

    var fn = {

        init: function() {
            page_user_common.init();
            
            this.initElements();
            this.modalbox.init();
            this.bindEvents();
            this.render();
        },

        initElements: function() {

            this.$container   = $('#jobs_portlet_body');
            this.$form        = $('form#jobs_list');
        },

        modalbox: {

            init: function() {
                
                this.submitButtonClickHandler();
            },

            submitButtonClickHandler: function() {

                // submit button click  -   modal box showing
                fn.$container.off('click', 'button.button-change-status');  // preceding on-click event handler is deleted !
                $(fn.$container).on('click', 'button.button-change-status', function() {

                    var STATUS_CLOSED = 0;
                    var STATUS_ACTIVATE = 1;
                    var STATUS_DELETE = 4;
                    var STATUS_SUSPEND = 5;

                    var text = '';
                    var number = 0;
                    var modal_title = '';
                    var modal_button_caption = '';

                    var target_status = $('select.select-change-status', fn.$form).val();

                    // suspended status
                    if ( target_status == STATUS_SUSPEND ) {
                        modal_title = 'Suspend Project';
                        modal_button_caption = 'Suspend';
                    }
                    // delete status
                    if ( target_status == STATUS_DELETE ) {
                        modal_title = 'Delete Project';
                        modal_button_caption = 'Delete';
                    }
                    // activate status
                    if ( target_status == STATUS_ACTIVATE ) {
                        modal_title = 'Activate Project';
                        modal_button_caption = 'Activate';
                    }

                    $.reasonbox.create({
                        title: modal_title,
                        cancelButton: {
                            label: "Cancel",
                            className: 'btn-default',
                            callback: function() {
                            }
                        },
                        actionButton: {
                            label: modal_button_caption,
                            className: 'blue',
                            callback: function(e, reason) {
                                $('input[name="_action"]', fn.$container).val('CHANGE_STATUS');

                                fn.$form.submit();
                            }
                        },
                        $form: fn.$form                        
                    });
                    
                    return true;

                });
            }
        },

        bindEvents: function() {
            var self = this;
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
            fn.$container.ajaxDatatable({
                success: function(html) {
                    self.initElements();
                    self.modalbox.init();
                    self.render();
                }
            });
        }
    };

    return fn;
});