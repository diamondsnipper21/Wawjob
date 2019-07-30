/**
 * signup.js - Sign Up Page
 * @author	KCG
 * @updated	2016/2/22
*/

define(['select2'], function () {

	var fn = {

		init: function () {
			this.initElements();
            this.bindEvents();
            this.render();
        },

        initElements: function() {
			this.$form = $('#frm_register');
			this.$checkbox = $('#terms_of_service');
		},

		bindEvents: function() {
			Global.renderValidator();

			this.validator = this.$form.validate({
				messages: {
					username: {
						remote: trans.duplicated_username
					},
					email: {
						remote: trans.duplicated_email
					},
					/*captcha: {
						remote: trans.invalid_captcha
					}*/
				},
				highlight: function (element) {
					$(element).closest('div').addClass('has-error');
				},
				success: function (label, element) {
					$('.error', $(element).closest('.row')).remove();
					$('.has-error', $(element).closest('.row')).removeClass('has-error');
				},
			});

			$('#ele_password, #ele_password2').on('change', function() {
				$(this).val($.trim($(this).val().replace(/\r/g, "")));
			});

			this.$checkbox.on('click', function() {
				$(':submit').prop('disabled', true);
				if ($(this).prop('checked')) {
					$(':submit').prop('disabled', false);
				}
			});

			$('.captcha-refresh').on('click', function() {
				var $captcha = $('.captcha-img img');
				$captcha.attr('src', $captcha.attr('src') + parseInt(Math.random() * 100) / 10);

				return false;
			});
		},

		render: function() {
			$('.select2').select2({
				minimumResultsForSearch: -1
			});
			$('.select2-country').select2({
				minimumResultsForSearch: 6
			});

			Global.renderUniform();
		}
	};

	return fn;
});