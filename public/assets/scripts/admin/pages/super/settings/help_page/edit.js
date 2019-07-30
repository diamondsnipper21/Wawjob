/**
 * @author KCG
 * @since April 4, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'ajax_datatable', 'bs-datepicker', 'bs-modalmanager', 'bs-modal', /*'ckeditor',*/ 'alert'], function (common) {

    var fn = {
        init: function() {
            this.$modal     = $('#modal_help_page_container');
            this.$modalForm = $('.form-horizontal', self.$modal);

            this.bindEvents();
            this.render();
        },

        bindEvents: function() {
            $('.main.select2-category').on('change', function() {
                var $option = $(':selected', $(this));
                var data_for = $option.data('for');

                if ($('#type').val() == 0 && $('.second.select2-category').val() == 0)
                    return true;

                $('#type').val(data_for);
                $('#type').trigger('change');
            });

            $('.second.select2-category').on('change', function() {
                if ($(this).val() != 0) {
                    $('#type').val(0);
                    $('#type').trigger('change');
                }
            });

            $('#type').on('change', function() {
                if ($(this).val() == 0) {
                    $('.second-category-row').removeClass('disable');
                } else {
                    $('.second.select2-category').val(0);
                    $('.second.select2-category').trigger('change');
                    $('input[name="second_order"]').val('');

                    $('.second-category-row').addClass('disable');
                }
            });

            $('input[name="title[en]"]').on('keypress keydown keyup blur', function() {
                var title = $(this).val();
                var slug = title.toLowerCase();
                slug = slug.replace(/[ ]/g, '-');
                slug = slug.replace(/[\.\'\"\?\/\(\)]/g, '');

                $('input[name="slug"]').val(slug);
            });
        },

        render: function() {
            var self = this;

            this.$modalForm.validate();

            this.$modal.ajaxDatatable({
                success: function(html) {
                    self.$modalForm = $('.form-horizontal', self.$modal);

                    self.init();
                }
            });

            // $('.ckeditor', this.$modal).each(function() {
            //     CKEDITOR.replace($(this).attr('id'), {
            //         baseFloatZIndex: 20000
            //     });
            // });

            common.renderSelect2();
            common.handleUniform();

            $('.select2-category').select2({
                templateResult: function(option) {
                    var $option = $(option.element);
                    var option_text = option.text;

                    if (!option.id) {
                        return option_text;
                    }

                    var parent_id = $option.data('parent');

                    if (!parent_id) // if this is parent category, this cateogry will be bold.
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
        }
    };

    return fn;
});
define.amd = amd;