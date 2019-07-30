/**
 * signup_user.js - Sign Up Page
 * @author  KCG
 * @updated 2018/04/24
*/

define(['scripts/pages/auth/signup/signup'], function (signup) {
    var fn = {

        init: function () {
            signup.init();
        }
    };

    return fn;
});
