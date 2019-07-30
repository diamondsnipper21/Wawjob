/**
 * help.js
 */

define([], function () {

	var fn = {
		init: function() {
			this.bindEvents();
			this.render();
		},

		bindEvents: function() {
			$('.tabs a').on('click', function() {
				if ($(this).hasClass('active')) {
					return false;
				}

				$('.tabs a').removeClass('active');
				$(this).addClass('active');

				var id = $(this).attr('href');
				$('.tab-p-content').hide();
				$(id).show();

				return false;
			});

			$('.help-item.has-children > a, .help-item.has-children > span').on('click', function() {
				$('.sub-help-items', $(this).parent()).slideToggle();
				$(this).parent().toggleClass('opening');
			});
		},

		render: function() {
			var url = window.location.href;

			if (url.indexOf('#freelancer_content') >= 0) {
				$('.tabs a:eq(1)').trigger('click');
			}
		}
	};

	return fn;
});
