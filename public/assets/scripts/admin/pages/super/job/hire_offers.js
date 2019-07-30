/**
 * @author KCG
 * @since July 21, 2017
 */

define(['/assets/scripts/pages/buyer/job/hire_offers.js', 'page_user_common', 'ajax_datatable'], function (hireoffersPage, userPage) {
    var fn = {
        init: function() {
            hireoffersPage.init();
            userPage.init();

            this.render();
        },

        render: function() {
            var self = this;

            $('.tab-pane').ajaxDatatable({
                success: function(html) {
                    hireoffersPage.init();
                    self.render();
                }
            });
        }
    };

    return fn;
});