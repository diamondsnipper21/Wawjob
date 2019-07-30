/**
 * reset.js - Reset Password Page
 */

define([], function () {

	var fn = {
		init: function () {
			this.render();
		},

		render: function() {
			$('#reset_password_form').validate();
		}
	};

	return fn;
});