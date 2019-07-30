/**
 * security_question.js
 */
define(['jquery-form'], function () {

  var fn = {
		init: function () {
		  	this.$form = $('#security_question_form');
		  	this.$form.validate();

		  	Global.renderUniform();
		}
 	};

  return fn;
});