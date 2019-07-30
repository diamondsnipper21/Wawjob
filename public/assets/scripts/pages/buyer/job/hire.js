/**
* job/hire.js
*/

define(['moment', 'bootbox', 'datepicker', 'jquery-form', 'inputmask', 'jquery-uniform'], function (moment, bootbox) {
    var fn = {
    	$form: null,
    	$jobType: null,
    	$billingRate: null,
    	$estimate: null,
    	$title: null,
    	$description: null,
    	$files: null,
    	$attachments: null,
    	$uploadedFiles: null,
    	$btnSubmit: null,

		init: function () {
            this.$form = $('#form_hire_form');
            this.$jobType = $('#job_type');
            this.$billingRate = $('#billing_rate');
            this.$estimate = $('#contract_estimate');
            this.$title = $('#contract_title');
            this.$description = $('#work_description');
            this.$files = $('#files', this.$form);
			this.$uploadedFiles = $('#uploaded_files', this.$form);
			this.$attachments = $('.attachments', this.$form);
			this.$btnSubmit = $('#submit_button', this.$form);

			this.milestones.init();

			$('#agree_on_term').prop('checked', false);

			this.bindEvents();
			this.render();
        },

		bindEvents: function() {
			var self = this;

			fn.$title.on('change', fn.validateRequired);
			
			$('#switch_to_fixed_link').on('click', function() {
				self.show_fixed();
			});
			
			$('#switch_to_hourly_link').on('click', function() {
				self.show_hourly();
			});

			this.$attachments.on('click', function(e) {
				if ( $(e.target).parent().hasClass('link-delete') ) {
					$(e.target).closest('.file').remove();
				}
			});

			$('#agree_on_term').on('change', function(e) {
				if ( $(this).is(':checked') ) {
					fn.$btnSubmit.enable(1);
				} else {
					fn.$btnSubmit.enable(0);
				}
			});

            this.$billingRate.inputmask('decimal', {});

            this.$billingRate.on('change', function() {
            	fn.validateNumber($(this));
            });

            this.$attachments.on('click', function(e) {
				if ( $(e.target).parent().hasClass('link-delete') ) {
					var $file = $(e.target).closest('.file');
					$.post('/delete-file', {id: $file.data('id')}, function (json) {
						$file.remove();
					});
				}
			});

            this.$form.on('submit', fn.validate);
		},

		render: function() {
			if ( fn.$jobType.val() == 1 ) {
            	this.show_hourly();
			} else {
            	this.show_fixed();
			}
				
			Global.renderFileInput();
            Global.renderMaxlength();
            Global.renderUniform();
			Global.renderSelect2();
		},

		removeErrors: function() {
			$('.error', fn.$form).remove();
			$('.has-error', fn.$form).removeClass('has-error');
		},

        errorHandler: function($element, error) {
        	var $parent = $element.parent();

        	if ( $parent.hasClass('input-group') ) {
        		$parent = $parent.parent();
        	}

        	$parent.removeClass('has-error');
        	$('.error', $parent).remove();

            $parent.addClass('has-error');
            $parent.append('<span class="error" style="display:inline;">' + error + '</span>');

            $element.focus();
        },

		validate: function() {
			fn.removeErrors();
			
			var result = true;

			var jobType = fn.$jobType.val();
			if ( jobType == '0' ) {
				var $wrapper = $('#fixed_setting');

				// Check total funds
				var total = 0;
				$('.milestone', fn.milestones.$wrapper).each(function() {
					if ( $('[name="milestone_fund[]"]', $(this)).prop('checked') == true ) {
						total += parseFloat($('[name="milestone_price[]"]', $(this)).val());
					}
				});

				if ( total > fn.milestones.balance ) {
					result = false;
					fn.milestones.showErrorExceedBalance();
				}
			} else {
				var $wrapper = $('#hourly_setting');
			}

			$('[data-rule-required="true"]', $wrapper).each(function() {
				if ( $(this).val().trim() == '' ) {
					result = false;
					fn.errorHandler($(this), trans.required);
				}
			});

			$('[data-rule-number="true"]', $wrapper).each(function() {
				var res = fn.validateNumber($(this));
				result = result && res;
			});

			$('[data-rule-date="true"]', $wrapper).each(function() {
				var res = fn.validateDate($(this));
				result = result && res;
			});

			if ( fn.$title.val().trim() == '' ) {
                fn.errorHandler(fn.$title, trans.required);
				result = false;
			}

			if ( fn.$description.val().trim() == '' ) {
                fn.errorHandler(fn.$description, trans.required);
				result = false;
			}

			return result;
		},

		validateRequired: function() {
			var $obj = $(this);

			if ( $obj.val().trim() == '' ) {
				result = false;
				fn.errorHandler($obj, trans.required);
			} else {
				$('.error', $obj.closest('.has-error')).remove();
				$obj.closest('.has-error').removeClass('has-error');
			}
		},

		validateNumber: function($obj) {
			if ( $obj.target ) {
				$obj = $($obj.target);
			}

			var regex = /^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/;
			var result = true;

			var max = $obj.data('rule-max');
			if ( max == undefined ) {
				max = 10000;
			}

			if ( $obj.val().trim() == '' ) {
				result = false;
				fn.errorHandler($obj, trans.required);
			} else if ( !regex.test($obj.val()) ) {
				result = false;
				fn.errorHandler($obj, trans.please_enter_a_valid_number);
			} else if ( parseFloat($obj.val()) < 1 || parseFloat($obj.val()) > max ) {
				result = false;
				fn.errorHandler($obj, trans.please_enter_a_value_less_than_or_equal_to_n + ' ' + max);
			} else {
				$('.error', $obj.closest('.has-error')).remove();
				$obj.closest('.has-error').removeClass('has-error');
			}

			return result;
		},

		validateDate: function($obj) {
			if ( $obj.target ) {
				$obj = $($obj.target);
			}

			var regex = /^(\d{2})(\/)(\d{2})(\/)(\d{4})$/;
			var result = true;

			if ( $obj.val().trim() == '' ) {
				return result;
			}

			if ( !regex.test($obj.val().trim()) ) {
				result = false;
				fn.errorHandler($obj, trans.please_enter_a_valid_date);
			} else {
				var dtArray = $obj.val().trim().match(regex);
				dtMonth = parseInt(dtArray[1]);
				dtDay= parseInt(dtArray[3]);
				dtYear = parseInt(dtArray[5]);

				if ( dtMonth < 1 || dtMonth > 12 ) {
					result = false;
				} else if ( dtDay < 1 || dtDay > 31 ) {
					result = false;
				} else if ( dtYear < (new Date()).getFullYear() ) {
					result = false;
				}

				if ( !result ) {
					fn.errorHandler($obj, trans.please_enter_a_valid_date);
				} else {
					$('.error', $obj.closest('.has-error')).remove();
					$obj.closest('.has-error').removeClass('has-error');
				}
			}

			return result;
		},

		show_fixed: function() {
			fn.$jobType.val(0);
			fn.removeErrors();

			$('.fixed-title').removeClass('hidden');
			$('.hourly-title').addClass('hidden');
			
			$('#switch_to_hourly_link').removeClass('hidden');
			$('#fixed_explanation').removeClass('hidden');
		    $('#fixed_setting').removeClass('hidden');

		    $('#switch_to_fixed_link').addClass('hidden');
		    $('#hourly_explanation').addClass('hidden');
		    $('#hourly_setting').addClass('hidden');
		},

		show_hourly: function() {
			fn.$jobType.val(1);
			fn.removeErrors();

			$('.hourly-title').removeClass('hidden');
			$('.fixed-title').addClass('hidden');

			$('#switch_to_fixed_link').removeClass('hidden');
			$('#hourly_explanation').removeClass('hidden');
		    $('#hourly_setting').removeClass('hidden');

		    fn.$billingRate.val(fn.currencyFormat(Math.round(trans.billing_rate)));

		    $('#switch_to_hourly_link').addClass('hidden');
		    $('#fixed_explanation').addClass('hidden');
		    $('#fixed_setting').addClass('hidden');
		},

		milestones: {
			balance: 0,
			default_name: '',
			default_amount: '',
			default_date: '',
			$wrapper: null,
			$template: null,
			$btnAdd: null,
			$infoMoreMilestones: null,

			init: function() {
				this.balance = parseFloat($('[name="payment_method"]').data('value'));
				this.$wrapper = $('.milestones');
				this.$template = $('.template-milestone .milestone');
				this.$btnAdd = $('.btn-add-milestone');
				this.$infoMoreMilestones = $('.add-milestone-info');

				$('.checkbox', this.$wrapper).uniform();

				$('.date-picker', this.$wrapper).datepicker({
					orientation: 'auto',
					autoclose: true,
					startDate: moment().format('MM/DD/YYYY')
				}).on('changeDate', fn.milestones.changeDate);

				var $firstMilestone = $('.milestone.first', fn.milestones.$wrapper);
				fn.milestones.default_name = $('[name="milestone_title[]"]', $firstMilestone).val();
				fn.milestones.default_amount = $('[name="milestone_price[]"]', $firstMilestone).val();
				fn.milestones.default_date = $('[name="milestone_end[]"]', $firstMilestone).val();

				this.change(true);
				this.bindEvents();
			},

			bindEvents: function() {
				fn.$estimate.on('change', fn.milestones.checkTotal);
				fn.$estimate.on('change', fn.validateNumber);

				$('[name="contract_milestones"]').on('change', fn.milestones.change);
				$('[name="milestone_price[]"]').on('change', fn.milestones.checkTotal);
				$('[name="milestone_fund[]"]').on('change', fn.milestones.checkFund);

				this.$btnAdd.on('click', fn.milestones.add);
			},

			removeErrors: function() {
				$('.error', fn.milestones.$wrapper).remove();
				$('.has-error', fn.milestones.$wrapper).removeClass('has-error');
			},

			change: function() {
				var selected = $('[name="contract_milestones"]:checked').val();
				var nMilestones = $('.milestone', fn.milestones.$wrapper).length;

				if ( selected == '1' ) {
					var changed = false;
					if ( nMilestones > 1 ) {
						changed = true;
					} else {
						var $milestone = $('.milestone', fn.milestones.$wrapper).first();
						if ( $('.order', $milestone).length ) {
							if ( $('[name="milestone_title[]"]', $milestone).val().trim() != '' ||  
							$('[name="milestone_price[]"]', $milestone).val() != '' ) {
								changed = true;
							} else {
								fn.milestones.initFirstMilestone();
							}
						}
					}

					if ( changed ) {
						bootbox.dialog({
			                message: trans.confirm_create_one_milestone,
			                buttons: {
			                    cancel: {
			                        label: trans.cancel,
			                        className: 'btn-link',
			                        callback: function() {
			                        	fn.milestones.keepChanges();
			                        }
			                    },
			                    ok: {
			                        label: trans.ok,
			                        className: 'btn-primary',
			                        callback: function() {
										$('.milestone:not(.first)', fn.milestones.$wrapper).remove();
										$('.order', fn.milestones.$wrapper).remove();

										fn.milestones.initFirstMilestone();
			                        }
			                    },
			                },
			                onEscape: function() {
			                	fn.milestones.keepChanges();
			                }
			            });
					}
				} else {
					fn.milestones.$infoMoreMilestones.removeClass('hidden');

					if ( !$('.order', fn.milestones.$wrapper).length ) {
						$('[name="milestone_title[]"]', fn.milestones.$wrapper).before('<span class="order">1</span>');
						$('[name="milestone_title[]"]', fn.milestones.$wrapper).val('');
						$('[name="milestone_price[]"]', fn.milestones.$wrapper).val('');
					}
				}

				fn.milestones.removeErrors();
			},

			initFirstMilestone: function() {
				fn.milestones.$infoMoreMilestones.addClass('hidden');

				var $milestone = $('.milestone', fn.milestones.$wrapper).first();

				$('.order', $milestone).remove();

				$('[name="milestone_title[]"]', $milestone).val(fn.milestones.default_name).trigger('change');
				$('[name="milestone_price[]"]', $milestone).val(fn.milestones.default_amount).trigger('change');
				$('[name="milestone_end[]"]', $milestone).val(fn.milestones.default_date).trigger('change');

				$('[name="milestone_title[]"]', $milestone).on('change', fn.validateRequired);
				$('[name="milestone_price[]"]', $milestone).on('change', fn.validateNumber);
				$('[name="milestone_end[]"]', $milestone).on('change', fn.validateDate);
			},

			keepChanges: function() {
            	$('#more_milestones').prop('checked', true).trigger('change');
            	Global.updateUniform();
			},

			add: function() {
				var order = $('.milestone', fn.milestones.$wrapper).length + 1;

				var $milestone = fn.milestones.$template.clone();

				if ( !$('.order', $milestone).length ) {
					$('[name="milestone_title[]"]', $milestone).before('<span class="order">' + order + '</span>');
				}

				$('[name="milestone_title[]"]', $milestone).val('');
				$('[name="milestone_price[]"]', $milestone).val('');
				$('[name="milestone_end[]"]', $milestone).val('');
				$('[name="milestone_fund[]"]', $milestone).prop('checked', false);
				$('.milestone_fund_value', $milestone).val('0');

				$('[name="milestone_price[]"]', $milestone).on('change', fn.milestones.checkTotal);
				$('[name="milestone_title[]"]', $milestone).on('change', fn.validateRequired);
				$('[name="milestone_price[]"]', $milestone).on('change', fn.validateNumber);
				$('[name="milestone_end[]"]', $milestone).on('change', fn.validateDate);
				$('[name="milestone_fund[]"]', $milestone).on('change', fn.milestones.checkFund);

				$('.checkbox', $milestone).uniform();

				$('.date-picker', $milestone).datepicker({
					orientation: 'auto',
					autoclose: true,
					startDate: moment().format('MM/DD/YYYY')
				}).on('changeDate', fn.milestones.changeDate);

				$('.btn-delete-milestone', $milestone).on('click', fn.milestones.delete);

				$('[data-toggle="tooltip"]', $milestone).tooltip();

				fn.milestones.$wrapper.append($milestone);

				Global.renderMaxlength();
			},

			delete: function() {
				$(this).closest('.milestone').remove();
				fn.milestones.checkTotal();

				// Reordering
				$('.milestone', fn.milestones.$wrapper).each(function(i, o) {
					$('.order', $(this)).html(i + 1);
				});
			},

			checkFund: function() {
				if ( $(this).prop('checked') ) {
					$(this).closest('.chk').find('.milestone_fund_value').val('1');
				} else {
					$(this).closest('.chk').find('.milestone_fund_value').val('0');
				}
			},

			checkTotal: function() {
				var estimate = parseFloat(fn.$estimate.val() != '' ? fn.$estimate.val() : 0);
				var total = 0;

				$('[name="milestone_price[]"]', fn.milestones.$wrapper).each(function() {
					if ( $(this).val() != '' ) {
						total += parseFloat($(this).val());
					}
				});

				if ( total > estimate ) {
					fn.milestones.showErrorExceedEstimate();
				} else {
					$('.error-exceed-estimate').remove();
				}
			},

			showErrorExceedEstimate(error) {
				if ( $('.error-exceed-estimate').length ) {
					return false;
				}

				fn.milestones.$wrapper.after('<div class="error error-exceed-estimate fs-14 pt-2 pb-2"><span class="error" style="display:block;">' + trans.milestones_total_amount_exceeds_estimate + '</span></div>');
			},

			showErrorExceedBalance(error) {
				if ( $('.error-exceed-balance').length ) {
					return false;
				}

				fn.milestones.$wrapper.after('<div class="error error-exceed-balance fs-14 pt-2 pb-2"><span class="error" style="display:block;">' + trans.milestones_total_funding_exceeds_balance + '</span></div>');
			},

			changeDate: function(e) {
				var date = moment(e.date);
				date = date.format('MM/DD/YYYY');
				$('input[type="text"]', $(e.currentTarget).closest('.input-group')).val(date);
			},
		},

		currencyFormat: function(val) {
        	if ( val == '' || isNaN(val) ) {
        		val = 0; 
        	}

            return parseFloat(Math.round(val * 100) / 100).toFixed(2);
        }
    };

    return fn;
});