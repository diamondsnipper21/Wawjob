/**
 * @author KCG
 * @since 20170608
 * job/create.js
*/

define(['bootbox', 'jquery-form', 'select2'], function (bootbox) {

	var fn = {
		init: function () {
			this.$form = $('#form_job_post');
			this.$action = $('#action', this.$form);

			this.bindEvents();
			this.render();
		},

		render: function() {
			this.moveToLink();
			this.validate();

			$('#job_type').trigger('change');

			Global.renderFileInput();
            Global.renderMaxlength();
            Global.renderSelect2();
            Global.renderUniform();
            Global.renderGoToTop();
		},

		bindEvents: function() {
			var self = this;
			
			$('#job_type').on('change', function() {
				if ( $(this).val() == '1' ) { // Hourly Job
					fn.$form.removeClass('fixed-job').addClass('hourly-job');
					$('select, input', $('.hourly-job-section ')).data('ruleRequired', true);;
					
				} else { // Fixed Job
					fn.$form.addClass('fixed-job').removeClass('hourly-job');
					$('select, input', $('.hourly-job-section ')).data('ruleRequired', false);
				}

				fn.validate();
			});

			$('#btn_qualifications').on('click', function() {
				$('.qualifications-section').slideToggle(function() {
					if ( $('.qualifications-section').is(':visible') ) {
						$('#btn_qualifications').text(trans.hide_qualifications);
					} else {
						$('#btn_qualifications').text(trans.show_qualifications);
					}
				});
			});

			$('#btn_save_draft').on('click', function() {
				self.$form.data('validator', null);
				self.$form.off('validate');

				fn.$action.val('save_draft');
			});

			$('#btn_post_job').on('click', function() {
				fn.$action.val('post_job');
			});

			$('#btn_repost_job').on('click', function() {
				fn.$action.val('repost_job');
			});
		},

		moveToLink: function() {
			$('li.menu-item a').on('click', function(e) {
				var $obj = $(e.target);

				if ( $('#job_category', fn.$form).val() != '' || $('#job_title', fn.$form).val() != '' || $('#description', fn.$form).val() != '') {

					var html = '<label>Are you sure to discard changes?</label>';
					
					bootbox.dialog({
						title: '',
						message: html,
						buttons: {
							cancel: {
								label: trans.btn_no,
								className: 'btn-link',
								callback: function() {
								}
							},
							ok: {
								label: trans.btn_yes,
								className: 'btn-primary',
								callback: function() {
									window.location.href = $obj.attr('href');
								}
							},							
						},
					});

					return false;
					
				};
			});
		},

		validate: function() {
			this.$form.data('validator', null);
			this.$form.validate();
		}
	};

	return fn;
});