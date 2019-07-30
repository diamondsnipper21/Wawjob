/**
 * how_it_works.js
 */

define([], function () {

	var fn = {
		init: function() {
			this.bindEvents();

 			Global.renderGoToTop();
		},

		bindEvents: function() {
			$('.tabs a').on('click', function() {
				if ($(this).hasClass('active')) {
					return false;
				}

				$('.tabs a').removeClass('active');
				$(this).addClass('active');

				var id = $(this).attr('href');
				$('.tab-content').hide();
				$(id).show();

				return false;
			});
		}
	};

	return fn;
});
