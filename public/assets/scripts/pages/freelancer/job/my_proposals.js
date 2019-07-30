/**
 * job/my_proposals.js
 */

define(['ajax_page'], function () {
	var fn = {
		init: function () {
			this.$container = $('#all_proposals');
			this.$form 		= $('form', this.$container);

			this.bindEvents();
			this.render();
		},

		bindEvents: function() {
			var self = this;

			$('.tab-section li a').on('click', function() {
				self.$form.attr('action', $(this).attr('href'));
				self.$form.submit();

				return false;
			});
		},

		render: function() {
			var self = this;

			this.$container.ajaxPage({
				'success': function() {
					self.init();
				}
			});
		}
	};

	return fn;
});