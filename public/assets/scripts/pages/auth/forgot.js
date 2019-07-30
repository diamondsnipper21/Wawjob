/**
 * forgot.js - Forgot Password Page
 */

define(['ajax_page'], function () {

	var fn = {
		init: function () {
			this.render();
		},

		render: function() {
			$('#forgot_form').validate();
			
			$('#forgot_password').ajaxPage({
				success: function() {
					fn.init();
				}
			});
		}
	};

	return fn;
});