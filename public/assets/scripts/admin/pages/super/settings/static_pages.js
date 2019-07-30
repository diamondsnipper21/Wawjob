/**
 * @author KCG
 * @since April 4, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'ajax_datatable', 'bs-datepicker', 'bs-modalmanager', 'bs-modal', 'ckeditor', 'alert'], function (common) {

    var fn = {
        init: function() {
            var self = this;

            this.initElements();
            this.bindEvents();
            this.formSubmitHandler();
            
            this.render();
        },

        initElements: function() {
            this.$container   = $('#static_pages');
            this.$form        = $('form.form-datatable');
            this.$formButton  = $('button.button-submit', this.$form);
        },

        bindEvents: function() {
            var self = this;
        },

        formSubmitHandler: function() {
            $(fn.$container).off('click', 'button.button-submit');
            $(fn.$container).on('click', 'button.button-submit', function() {
                var action = $('#page_action').val();
                $('input[name="_action"]').val('CHANGE_STATUS');

                if (action == '2') { // Delete
                    $.alert.create({
                        message: 'Are you sure to delete these pages?',
                        title: 'Delete Pages',
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
                                fn.$form.submit();
                            }
                        }
                    });
                } else {
                    fn.$form.submit();
                }
            });
        },

        render: function() {

            common.initModal();

            this.renderDataTable();
            this.renderSelect2();

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
        }
    };

    return fn;
});
define.amd = amd;