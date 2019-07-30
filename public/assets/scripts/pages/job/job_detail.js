define(['stars'], function (stars) {
    var fn = {
        init: function () {
            this.render();
            this.bindEvents();
        },

        bindEvents: function() {
            $('#saved_job').on('click', function() {
                $.ajax({
                    type: "post",
                    cache: false,
                    url: $(this).data('url'),
                    dataType: "json",
                    success: function(res) {
                        $('#saved_job').attr('disabled', true).html('<i class="fa fa-heart"></i>&nbsp;&nbsp;' + trans.saved_job);
                    }
                });
            });

            $('.job-in-progress').on('click', function() {
                $('#jobs_in_progress').slideToggle();

                if ($('.glyphicon', this).hasClass('glyphicon-menu-down'))
                    $('.glyphicon', this).removeClass('glyphicon-menu-down').addClass('glyphicon-menu-up');
                else
                    $('.glyphicon', this).removeClass('glyphicon-menu-up').addClass('glyphicon-menu-down');

                return false;
            });

            this.bindFeedbackEvents();
        },

        bindFeedbackEvents: function() {
            var self = this;

            $('.show-buyer-feedback').off('click');
            $('.show-buyer-feedback').on('click', function() {
                $(this).toggleClass('opening');

                $(this).closest('.client-review').next().slideToggle();

                return false;
            });

            $('.load-more-messages').off('click');
            $('.load-more-messages').on('click', function() {
                $('.load-more-messages').remove();
                $('#ended_contracts .loading').show();

                $.ajax({
                    'url': $(this).attr('href'),
                    'type': 'post',
                    'dataType': 'json',
                    'blockUI': false,
                    'success': function(json) {
                        var html = json.html;

                        $('#ended_contracts .loading').hide();
                        $('#ended_contracts .loading').before(html);

                        self.bindFeedbackEvents();
                        self.render();
                    }
                });

                return false;
            });
        },

        render: function() {
            stars.init($('.client-score .stars'));
            Global.renderTooltip();
        },
    };

    return fn;
});