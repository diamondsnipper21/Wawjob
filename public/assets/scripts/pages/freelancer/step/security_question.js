define([], function () {

  	var fn = {
		init: function () {
		  	this.$form = $('#security_question_form');
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