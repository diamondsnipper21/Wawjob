/**
 * @author KCG
 * @since July 21, 2017
 */

define(['/assets/scripts/pages/user/notification_settings.js', 'page_user_common'], function (settingsPage, userPage) {
    var fn = {
        init: function() {
            settingsPage.init();
            userPage.init();

            this.bindEvents();
            this.render();
        },

        bindEvents: function() {
            $('input[type="checkbox"]').on('change', function() {
                var checked = $(this).attr('checked');
                $(this).attr('checked', !checked);

                $.uniform.update($(this));
                return false;
            });
        },

        render: function() {
        }
    };

    return fn;
});