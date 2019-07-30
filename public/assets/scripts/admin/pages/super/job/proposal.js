/**
 * @author PYH
 * @since July 21, 2017
 */

define(['common'], function (common) {

    var fn = {

        init: function() {

            this.initElements();
            this.initMoreLink();
            this.bindEvents();
            this.render();
        },

        initElements: function() {
            
            this.$container = $('#job_proposal');
            this.$form = $('.form-filter', this.$container);
            this.$sort = $('.sort', this.$form);
        },

        initMoreLink: function() {
            $('.user-cover-letter').on('click', function(e) {
                var obj = $(e.target);

                if ( obj.hasClass('btn-link') && obj.parent().hasClass('more') ) {
                    obj.closest('.user-cover-letter').addClass('expanded');
                }
            });
        },

        bindEvents: function() {
            // redirect another tab
            $('.tabbable-custom > ul.nav-tabs > li > a').on('click', function() {
                if (!$(this).parent().hasClass('active'))
                    window.location.href = $(this).attr('href');

                return false;
            });
        },

        render: function() {

            fn.$sort.select2({
                allowClear: true,
                width: 250,
                minimumResultsForSearch: -1
            });

            fn.$sort.on('change', function() {
                fn.$form.submit();
            });
            
        },
    };

    return fn;
});