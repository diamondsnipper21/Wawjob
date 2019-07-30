/**
 * @author KCG
 * @since July 21, 2017
 */

define(['/assets/scripts/pages/freelancer/workdiary/viewjob.js', 'page_user_common', 'ajax_datatable'], function (workdiaryPage, userPage) {
    var fn = {
        init: function() {
            workdiaryPage.init();
            userPage.init();

            this.bindEvents();
            this.render();
        },

        bindEvents: function() {
            var $checkboxes = $('.tab-pane input[type="checkbox"]');
            $checkboxes.on('change', function() {
                $.uniform.update($checkboxes);
            });
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