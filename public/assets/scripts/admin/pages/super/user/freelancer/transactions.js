/**
 * @author KCG
 * @since July 21, 2017
 */

define(['/assets/scripts/pages/freelancer/report/transactions.js', 'page_user_common', 'ajax_datatable'], function (transactionsPage, userPage) {
    var fn = {
        init: function() {
            transactionsPage.init();
            userPage.init();

            this.render();
        },

        render: function() {
        	var self = this;
            $('.report-transactions-page').ajaxDatatable({
                success: function(html) {
                	transactionsPage.init();
                	self.render();
                }
            });
        }
    };

    return fn;
});