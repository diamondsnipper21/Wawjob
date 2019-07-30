/**
 * @author KCG
 * @since July 21, 2017
 */

define(['/assets/scripts/pages/buyer/workdiary/view.js', 'page_user_common', 'ajax_datatable'], function (workdiaryPage, userPage) {
    var fn = {
        init: function() {
            workdiaryPage.init();
            userPage.init();

            this.render();
        },

        render: function() {
        	var self = this;
            $('.tab-pane').ajaxDatatable({
                success: function(html) {
                	workdiaryPage.init();
                	self.render();
                }
            });
        }
    };

    return fn;
});