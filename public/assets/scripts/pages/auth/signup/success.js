/**
 * success.js - Sign Up Success Page
 * @author Ro Un Nam
*/

define(['jquery-form'], function () {

	var fn = {
		$form: null,
		$btnChangeEmail: null,
		$btnResendEmail: null,
		$btnSubmitChange: null,
		$btnCancelChange: null,
		$boxChange: null,
		$action: null,
		$newEmail: null,

		init: function () {
			this.$form = $('#success_signup_form');
			this.$btnChangeEmail = $('#btn_change_email');
			this.$btnResendEmail = $('#btn_resend_email');
			this.$btnSubmitChange = $('#btn_submit_change');
			this.$btnCancelChange = $('#btn_cancel_change');
			this.$boxChange = $('.change-box');
			this.$newEmail = $('#new_email');
			this.$action = $('input[name="_action"]', this.$form);

			this.$btnChangeEmail.on('click', this.showChangeBox);
			this.$btnCancelChange.on('click', this.hideChangeBox);
			this.$btnSubmitChange.on('click', this.changeEmail);
			this.$btnResendEmail.on('click', this.resendEmail);
		},

		showChangeBox: function() {
			fn.$boxChange.slideDown(100);
		},

		hideChangeBox: function() {
			fn.$boxChange.slideUp(100, function() {
				fn.$newEmail.val('');
				fn.$newEmail.closest('.has-error').removeClass('has-error');
			});
		},

		checkEmail: function() {
			var email = fn.$newEmail.val();
			if ( email.trim() == '' )
				return false;

			var regex = /^[\w-\.\d*]+@[\w\d]+(\.[\w\d]+)*$/;

			return regex.test(email);
		},

		changeEmail: function() {
			if ( !fn.checkEmail() ) {
				fn.$newEmail.focus();
				fn.$newEmail.parent().addClass('has-error');

				return false;
			} else {
				fn.$newEmail.closest('.has-error').removeClass('has-error');
			}

			fn.$action.val('change');
			fn.submit();
		},

		resendEmail: function() {
			fn.$action.val('resend');
			fn.submit();
		},

		submit: function() {
			$('.alert', fn.$form).remove();
			
			fn.$form.ajaxSubmit({
				success: function(json) {
					if (json.success)
						Global.toastr('', json.message, 'success');
					else
						Global.toastr('', json.message, 'danger');
				},
				error: function(xhr) {
				},

				dataType: 'json',
			});
		}
	};

	return fn;
});