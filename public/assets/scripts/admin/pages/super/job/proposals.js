/**
 * @author KCG
 * @since July 21, 2017
 */

define(['/assets/scripts/pages/buyer/job/review_proposals.js', 'ajax_datatable'], function (proposalsPage) {
    var fn = {
        init: function() {
            proposalsPage.init();

            this.render();
        },

        render: function() {
        	var self = this;

            $('input[type="checkbox"]').uniform();

            $('.tab-pane').ajaxDatatable({
                success: function(html) {
                	proposalsPage.init();
                	self.render();
                }
            });
        }
    };

    return fn;
});