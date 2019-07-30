/**
 * @author KCG
 * @since Jan 21, 2018
 */

define([], function () {
    var fn = {
        init: function() {
            this.initElements();
            this.bindEvents();
            this.render();
        },

        initElements: function() {
            this.$container = $('#contact_us_detail, #thread_detail');
            this.$form      = $('form', this.$container);
        },

        bindEvents: function() {
            this.$container.off('click', '.message.unread');
            this.$container.on('click', '.message.unread', function() {
                var url = $(this).data('url');
                var $self = $(this);

                $.ajax({
                    'url': url,
                    'type': 'post',
                    'dataType': 'json',
                    'blockUI': false,
                    success: function(data) {
                        $self.removeClass('unread');
                    }
                });

                return false;
            });
        },

        render: function() {
            var self = this;

            this.$form.validate();

            this.$container.ajaxDatatable({
                success: function(html) {
                    self.init();
                }
            });

            Global.renderMaxlength();
        } 
    };

    return fn;
});