define(['stars'], function (stars) {

	var fn = {
		init: function() {
			this.bindEvents();
			this.render();
		},

		bindEvents: function() {
		},

		render: function() {
			stars.init($('.profile-score .stars'));
			Global.renderTooltip();

			var contentH = 0;
			$('.content').each(function() {
				contentH = Math.max($(this).outerHeight(), contentH);
			});

			$('.content').outerHeight(contentH);

			if (show_congratulation) {
				$('#modal_congratulations').modal('show');
			}

			$('[data-toggle="tooltip"]').tooltip();
		}
	};

	return fn;
});
