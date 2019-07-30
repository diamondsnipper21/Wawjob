/**
 * @author KCG
 * @since July 14, 2017
 */

define(['page_user_common', 'ajax_datatable'], function (page_user_common) {
    var fn = {
        init: function() {
            page_user_common.init();
            
            this.bindEvents();
            this.render();
        },

        bindEvents: function() {
        },

        render: function() {
            var self = this;
            $('.messages').ajaxDatatable({
                success: function(html) {
                    self.render();
                }
            });
        }
    };

    return fn;
});