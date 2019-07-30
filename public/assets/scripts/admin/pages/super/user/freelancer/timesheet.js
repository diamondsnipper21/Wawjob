/**
 * @author KCG
 * @since July 21, 2017
 */

define(['/assets/scripts/pages/report/timesheet.js', 'page_user_common', 'ajax_datatable'], function (timesheetPage, userPage) {
    var fn = {
        init: function() {
            timesheetPage.init();
            userPage.init();

            this.render();
        },

        render: function() {
        	var self = this;
            $('.tab-pane').ajaxDatatable({
                success: function(html) {
                	timesheetPage.init();
                	self.render();
                }
            });
        }
    };

    return fn;
});