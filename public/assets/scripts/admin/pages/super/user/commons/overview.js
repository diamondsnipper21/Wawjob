/**
 * @author KCG
 * @since July 11, 2017
 */

define(['/assets/scripts/admin/pages/super/user/commons.js', 'stars'], function (commons, stars) {

	var fn = {
		init: function() {
			commons.init();

			this.bindEvents();
			this.render();
		},

		bindEvents: function() {
		},

		render: function() {
			this.renderSelect2();

			stars.init($('.stars'));
			Global.renderTooltip();
		},

        renderSelect2: function() {
            Global.renderSelect2();
        }
	};

	return fn;
});