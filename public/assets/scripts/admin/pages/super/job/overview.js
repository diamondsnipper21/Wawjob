/**
 * @author PYH
 * @since July 9, 2017
 */

define(['common', 'jquery-form'], function (common) {

    var fn = {

        init: function() {

            this.initElements();
            this.initMoreLink();
            this.bindEvents();
            this.render();
        },

        initElements: function() {
        },

        initMoreLink: function() {
            $('.job-details').on('click', function(e) {
                var obj = $(e.target);

                if ( obj.hasClass('btn-link') && obj.parent().hasClass('more') ) {
                    obj.closest('.source').addClass('expanded');
                }
            });
        },

        bindEvents: function() {
            // redirect another tab
            $('ul.nav-job-detail.nav-tabs > li > a').on('click', function() {
                if (!$(this).parent().hasClass('active'))
                    window.location.href = $(this).attr('href');

                return false;
            });
        },

        render: function() {
        },
    };

    return fn;
});