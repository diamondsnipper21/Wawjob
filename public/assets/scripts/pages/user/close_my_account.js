/**
 * close_my_account.js
 */


define([], function () {

  	var fn = {
	    $form: null,

		validate: function() {
		  	this.$form.validate({
				errorElement: 'span',
				errorClass: 'help-block help-block-error',
				focusInvalid: false,
				ignore: '',

				invalidHandler: function (event, validator) {
				  	return false;
				},		

				highlight: function (element) {
				  	$(element).closest('.form-group').addClass('has-error');
				},

				success: function (label, element) {
				  	$(element).closest('.form-group').removeClass('has-error');
				},

				submitHandler: function (form) {
					form.submit();
				}
		  	});
		},

	    init: function () {

	    	this.$form = $('#close_my_account_form');
	    	this.validate();

	    	$('#btn_confirm_close', this.$form).on('click', function() {
	    		$('input[name="_action"]', fn.$form).val('close');
	    	});

	    	Global.renderSelect2();

	    }
  	};

  	return fn;
});