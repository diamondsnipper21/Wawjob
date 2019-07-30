define(['scripts/pages/freelancer/user/my_profile'], function (profile) {

  	var fn = {
		init: function () {
			this.$form = $('#about_me form:eq(0)');

		  	profile.init();

		  	this.bindEvents();
		},

		bindEvents: function() {
			var self = this;

			$('button[type="submit"]', this.$form).on('click', function() {
				self.$form.data('no-ajax', true);
				
				self.$form.submit();
			});

			$('body').on('ajaxPage.success', function() {
				self.init();
			});
		},
	};

  	return fn;
});