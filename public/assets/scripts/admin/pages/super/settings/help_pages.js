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
            this.$container   = $('#help_pages');
            this.$form        = $('form.form-datatable');
            this.$formButton  = $('button.button-submit', this.$form);
        },

        bindEvents: function() {
            var self = this;
            
            $('.select2-category').on('change', function() {
                // var $option = $(':selected', $(this));
                // var data_for = $option.data('for');

                // $('#type').val(data_for);
                // $('#type').trigger('change');
            });
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

            $('.select2-category').select2({
                templateResult: function(option) {
                    var $option = $(option.element);
                    var option_text = option.text;

                    if (!option.id) {
                        return option_text;
                    }

                    var parent_id = $option.data('parent');

                    if (!parent_id) // if parent category, this cateogry will be bold.
                        return $('<strong>' + option_text + '</strong>');

                    return option_text;
                },

                templateSelection: function(option) {
                    var $option = $(option.element);
                    var option_text = option.text.trim();

                    if (!option.id) {
                        return option_text;
                    }

                    var parent_id = $option.data('parent');

                    if (!parent_id)
                        return $('<strong>' + option_text + '</strong>');

                    // if this category has parent one. append name of parent category into this name.
                    var $parent = $('option[value="' + parent_id + '"]', $option.closest('select'));

                    return $('<span>' + $parent.text() + ' &gt; <strong>' + option_text + '</strong></span>');
                }
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