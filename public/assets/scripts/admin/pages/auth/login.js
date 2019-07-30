/**
 * @author KCG
 * @since June 8, 2017
 */

define(['jquery-validation'], function () {

	var fn = {
		init: function() {
			this.$form = $('.login-form');

			this.bindEvents();
			this.render();
		},

		bindEvents: function() {
			var self = this;

			this.$form.validate();

	        $('input', this.$form).on('keypress', function (e) {
	            if (e.which == 13) {
	                if (self.$form.validate().form()) {
	                    self.$form.submit(); //form validation success, call ajax form submit
	                }
	                return false;
	            }
	        });

		},

		render: function() {
		}
	};

	return fn;
});