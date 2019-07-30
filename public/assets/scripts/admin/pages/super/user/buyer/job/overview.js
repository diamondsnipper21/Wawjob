/**
 * @author KCG
 * @since July 19, 2017
 */

define(['page_user_common', '/assets/scripts/admin/pages/super/job/overview.js'], function (userPage, page) {
    var fn = {
        init: function() {
        	userPage.init();
            page.init();
        }
    };

    return fn;
});