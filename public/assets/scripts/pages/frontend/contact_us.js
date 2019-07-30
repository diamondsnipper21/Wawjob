/**
 * contact_us.js
 */

define(['select2', 'ajax_page'], function () {

	var fn = {
		init: function() {
			this.initElements();
			this.bindEvents();
			this.render();
		},

		initElements: function() {
			this.$container = $('#contact_us_container');
			this.$form 		= $('form', this.$container);
		},

		bindEvents: function() {
			$('.captcha-refresh').on('click', function() {
				var $captcha = $('.captcha-img img');
				$captcha.attr('src', $captcha.attr('src') + parseInt(Math.random() * 100) / 10);

				return false;
			});

			$('#captcha').off('keypress');
			$('#captcha').on('keypress', function() {
				$('#captch_f_error').fadeOut();
			});
		},

		render: function() {
			var self = this;

			this.$form.validate();

            Global.renderMaxlength();

            this.$container.ajaxPage({
                success: function(html) {
                	self.init();
                }
            });
		}
	};

	return fn;
});
