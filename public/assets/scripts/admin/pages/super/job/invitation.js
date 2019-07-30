/**
 * @author KCG
 * @since July 21, 2017
 */

define(['/assets/scripts/pages/buyer/job/invite_freelancers.js', 'ajax_datatable'], function (interviewsPage) {
    var fn = {
        init: function() {
            interviewsPage.init();

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