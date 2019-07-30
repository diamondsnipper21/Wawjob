/**
 * change_security_question.js
 */


define(['jquery-form'], function () {

  	var fn = {
		init: function () {
		  	this.$form = $('#change_security_question_form');
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