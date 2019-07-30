/**
* job/all_contracts.js
*/

define(['stars', 'ajax_page'], function (stars) {
    var fn = {
        init: function () {
            var self = this;

            this.$container = $('#my_freelancers');
            this.$form = $('#my_freelancers form');

            $('.project-selection').on('change', function() {
                self.$form.submit();
            });
            $('.sort-selection').on('change', function() {
                self.$form.submit();
            });

            $('.show-feedback').on('click', function(e) {
                $(e.target).closest('.contractor-feedback').find('.feedbacks').slideToggle();

                if ($(e.target).children().hasClass('glyphicon-menu-down'))
                    $(e.target).children().removeClass('glyphicon-menu-down').addClass('glyphicon-menu-up');
                else
                    $(e.target).children().removeClass('glyphicon-menu-up').addClass('glyphicon-menu-down');

                return false;
            });

            stars.init($('.score .stars'));
            stars.init($('.client-score .stars'));
            stars.init($('.freelancer-score .stars'));

            $('.nav.nav-tabs li a').on('click', function() {
                var url = $(this).attr('href');

                if (url == '')
                    return false;

                self.$form.attr('action', url);
                self.$form.submit();

                return false;
            });

            this.$container.ajaxPage({
                success: function() {
                    self.init();
                }
            });

            Global.renderSelect2();
            Global.renderTooltip();
        }
    };

    return fn;
});
