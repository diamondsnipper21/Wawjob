/**
 * @author KCG
 * @since July 20, 2017
 */

define(['page_user_common', '/assets/scripts/admin/pages/ticket/tickets.js'], function (userCommonPage, ticketPage) {
    var fn = {
        init: function() {
        	$('form#createForm').validate();;
        	Global.renderFileInput();

            userCommonPage.init();
            ticketPage.init();
        }
    };

    return fn;
});