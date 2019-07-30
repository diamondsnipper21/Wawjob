/**
 * @author KCG
 * @since July 13, 2017
 */

define(['page_user_common', 'common', 'stars'], function (page_user_common, common, stars) {

	var fn = {
		init: function() {
			page_user_common.init();

			this.bindEvents();
			this.render();
		},

		bindEvents: function() {
		},

		render: function() {
			this.renderSelect2();
		},

        renderSelect2: function() {
            common.renderSelect2();
        }
	};

	return fn;
});