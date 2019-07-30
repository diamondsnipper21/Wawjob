/**
 * @author KCG
 * @since July 21, 2017
 */

define(['/assets/scripts/pages/buyer/job/interviews.js', 'page_user_common', 'ajax_datatable'], function (interviewsPage, userPage) {
    var fn = {
        init: function() {
            interviewsPage.init();
            userPage.init();

            this.render();
        },

        render: function() {
            var self = this;

            $('input[type="checkbox"]').uniform();

            $('.tab-pane').ajaxDatatable({
                success: function(html) {
                    interviewsPage.init();
                    self.render();
                }
            });
        }
    };

    return fn;
});