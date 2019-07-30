/**
 * @author KCG
 * @since April 4, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'ajax_datatable', 'bs-datepicker', 'bs-modalmanager', 'bs-modal', 'ckeditor', 'alert'], function (common) {

    var fn = {
        init: function() {
            this.$modal     = $('#modal_static_page_container');
            this.$modalForm = $('.form-horizontal', self.$modal);

            this.bindEvents();
            this.render();
        },

        bindEvents: function() {
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

            $('.ckeditor', this.$modal).each(function() {
                CKEDITOR.replace($(this).attr('id'), {
                    baseFloatZIndex: 20000
                });
            });
        }
    };

    return fn;
});
define.amd = amd;