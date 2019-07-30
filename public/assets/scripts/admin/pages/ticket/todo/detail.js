/**
 * @author KCG
 * @since July 4, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'ajax_datatable'], function (common) {

    var fn = {
        sent_msgs: null,
        load_more: true,

        init: function() {
            var self = this;

            this.initElements();
            
            this.bindEvents();
            this.render();

            requirejs(['/assets/scripts/admin/pages/ticket/todo/edit_modal.js'], function(modal) {
                modal.init();

                this.modal = modal;

                $(modal.$container).off('bs.modal.success.close');
                $(modal.$container).on('bs.modal.success.close', function(e, html) {
                    self.refresh();
                });
            });
        },

        initElements: function() {
            this.$container = $('.todo-detail');
        },

        bindEvents: function() {
            var self = this;
        },

        render: function() {
            Global.renderMessageBoard();
        },

        refresh: function(html) {
            var $html = $(html);
            var $contents = $($.ajaxDatatable.selector(this.$container), $html);

            this.$container.html($contents.html());

            this.init();
        }
    }

    return fn;
});
define.amd = amd;