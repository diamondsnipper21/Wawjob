/**
* change_password.js
*/
define(['jquery-form'], function () {

  	var fn = {
		init: function () {
		  	this.$form = $('#change_password_form');
		  	this.render();
		},

		render: function() {
		  	this.$form.validate();

			Global.renderUniform();
			Global.renderSelect2();
		}
	};

	return fn;
});