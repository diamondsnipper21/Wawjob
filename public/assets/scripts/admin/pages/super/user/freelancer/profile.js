/**
 * @author KCG
 * @since July 21, 2017
 */

define(['/assets/scripts/pages/freelancer/user/profile.js', 'page_user_common', 'ajax_datatable'], function (profilePage, userPage) {
    var fn = {
        init: function() {
            profilePage.init();
            userPage.init();

            this.render();
        },

        render: function() {
        	var self = this;
            // $('.tab-pane').ajaxDatatable({
            //     success: function(html) {
            //     	profilePage.init();
            //     	self.render();
            //     }
            // });
        }
    };

    return fn;
});