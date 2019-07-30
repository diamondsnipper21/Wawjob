/**
* contract/contract_detail.js
* @author Ro Un Nam
* @since Jun 05, 2017
*/

 define(['stars', 'common', 'moment', 'bootbox', 'ajax_page', 'datepicker'], function(stars, common, moment, bootbox) {
 	var fn = {
 		$section: null,
 		$navTabs: null,

 		init: function() {
 			this.$section = $('.view-section');
 			this.$navTabs = $('.nav-tabs');

 			this.overview.init();
 			this.milestones.init();
 			this.payment.init();
 			this.feedback.init();
 			this.refund.init();
 			this.dispute.init();
 			this.initTab();

 			Global.renderUniform();
 		},

 		initTab: function() {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                common.initFooter();
                common.initTable();
            });

            if ( location.hash ) {
            	$('a[href="' + location.hash + '"]', fn.$navTabs).tab('show');
            }
 		},

 		overview: {
 			$linkMilestones: null,

 			init: function() {
 				this.$linkMilestones = $('.link-milestones');

 				this.$linkMilestones.on('click', function() {
 					$('li.active', fn.$navTabs).removeClass('active');
 					$('li.nav-milestones', fn.$navTabs).addClass('active');
 				});

 				$('.btn-change-manual').on('click', function(e) {
                    var $form = $(this).closest('form');
                    var title = $(this).hasClass('allowed') ? trans.confirm_disable_manual_time : trans.confirm_allow_manual_time;

                    bootbox.confirm({
                    	title: '',
                    	message: title,
	                    buttons: {
	                        cancel: {
	                            label: trans.btn_cancel,
	                            className: 'btn-link',
	                        },
	                        confirm: {
	                            label: trans.btn_ok,
	                            className: 'btn-primary',
	                        },
	                    },
	                    callback: function (result) {
	                    	if ( result ) {
	                    		$form.submit();
	                    	}
	                    }
                    });

                    return false;
	            });

                $('.btn-change-overtime').on('click', function(e) {
                    var $form = $(this).closest('form');
                    var title = $(this).hasClass('allowed') ? trans.confirm_disable_over_time : trans.confirm_allow_over_time;

                    bootbox.confirm({
                    	title: '',
                    	message: title,
	                    buttons: {
	                        cancel: {
	                            label: trans.btn_cancel,
	                            className: 'btn-link',
	                        },
	                        confirm: {
	                            label: trans.btn_ok,
	                            className: 'btn-primary',
	                        },
	                    },
	                    callback: function (result) {
	                    	if ( result ) {
	                    		$form.submit();
	                    	}
	                    }
                    });

                    return false;
                });

				$('.btn-pause').on('click', function() {
					var $form = $(this).closest('form');

                    bootbox.confirm({
                    	title: '',
                    	message: trans.confirm_contract_pause,
	                    buttons: {
	                        cancel: {
	                            label: trans.btn_cancel,
	                            className: 'btn-link',
	                        },
	                        confirm: {
	                            label: trans.btn_ok,
	                            className: 'btn-primary',
	                        },
	                    },
	                    callback: function (result) {
	                    	if ( result ) {
	                    		$form.submit();
	                    	}
	                    }
                    });

	                return false;
				});

				$('#btnCancelContract').on('click', function() {
                    bootbox.confirm({
                    	title: '',
                    	message: trans.confirm_contract_cancel,
	                    buttons: {
	                        cancel: {
	                            label: trans.btn_cancel,
	                            className: 'btn-link',
	                        },
	                        confirm: {
	                            label: trans.btn_ok,
	                            className: 'btn-primary',
	                        },
	                    },
	                    callback: function (result) {
	                    	if ( result ) {
	                    		location.href = $('#btnCancelContract').data('url');
	                    	}
	                    }
                    });

	                return false;
				});
 			}
 		},

 		milestones: {
 			$wrapper: null,
 			$modal: null,
 			$form: null,
 			$formCreate: null,
 			$btnRequestPayment: null,
 			$btnRefundFund: null,
 			$btnFund: null,
 			$btnRelease: null,
            $btnEdit: null,
            $btnDelete: null,

 			init: function() {
 				this.$wrapper = $('#contract_milestones');
 				this.$modal = $('#modalMilestone');
 				this.$form = $('#formMilestones');
 				this.$formCreate = $('#formMilestone');
 				this.$btnRequestPayment = $('.btn-request-payment', this.$wrapper);
 				this.$btnRefundFund = $('.btn-refund-fund', this.$wrapper);
 				this.$btnFund = $('.btn-fund', this.$wrapper);
 				this.$btnRelease = $('.btn-release', this.$wrapper);
                this.$btnDelete = $('.btn-delete', this.$wrapper);

				this.bindEvents();
 			},

			bindEvents: function() {
				this.$btnFund.on('click', fn.milestones.fund);
				this.$btnRelease.on('click', fn.milestones.release);
				this.$btnRequestPayment.on('click', fn.milestones.requestPayment);
 				this.$btnRefundFund.on('click', fn.milestones.refund);
                this.$btnDelete.on('click', fn.milestones.delete);

				$('.date-picker', fn.milestones.$modal).datepicker({
					orientation: 'auto',
					autoclose: true,
					startDate: moment().format('MM/DD/YYYY')
				}).on('changeDate', function(e) {
					var date = moment(e.date);
					date = date.format('MM/DD/YYYY');
					$('input[type="text"]', $(e.currentTarget).closest('.input-group')).val(date);
				});

				this.$modal.on('show.bs.modal', function(e) {
                    if ( e.namespace == 'bs.modal' ) {
                    	var $btn = $(e.relatedTarget);
    					if ( $btn.hasClass('btn-edit') && $btn.data('id') ) {
    						fn.milestones.$modal.addClass('edit');
                            $('input[name="_id"]', fn.milestones.$modal).val($btn.data('id'));
                            $('input[name="name"]', fn.milestones.$modal).val($btn.data('title'));
                            $('input[name="price"]', fn.milestones.$modal).val($btn.data('amount'));
                            $('input[name="end_time"]', fn.milestones.$modal).val($btn.data('date'));
                            $('input[name="confirm_fund"]', fn.milestones.$modal).prop('checked', false);
                            $('input[name="_action"]', fn.milestones.$modal).val('edit_milestone');
                            Global.updateUniform();
                        }
                    }
				});

			    this.$modal.on('hide.bs.modal', function(e) {
                    if ( e.namespace == 'bs.modal' ) {
                    	fn.milestones.$modal.removeClass('edit');
                        $('input[name="confirm_fund"]', fn.milestones.$modal).prop('checked', true);
                        Global.updateUniform();

    					$('#name', fn.milestones.$modal).val('');
    					$('#price', fn.milestones.$modal).val('');
                        $('#end_time', fn.milestones.$modal).val($('#end_time', fn.milestones.$modal).data('date'));
                        $('input[name="_action"]', fn.milestones.$modal).val('edit_milestone');

    					$('.has-error', fn.milestones.$modal).removeClass('has-error');
    					$('.error', fn.milestones.$modal).remove();
                    }
				});

			    this.$formCreate.validate({
					highlight: function (element) {
						$(element).closest('.row').addClass('has-error');
					},
					success: function (label, element) {
						$('.error', $(element).closest('.row')).remove();
						$(element).closest('.row').removeClass('has-error');
					},
				});
			},

            fund: function() {
            	if ( $('.bootbox').length > 0 ) {
                    return false;
                }
                
                var milestone_id = $(this).data('id');
                var _msg = '<div class="buyer-milestone-confirm">' + trans.confirm_fund + '</div>';
                
                bootbox.dialog({
                    title: '',
                    message: _msg,
                    buttons: {
                        cancel: {
                            label: trans.btn_cancel,
                            className: 'btn-link',
                            callback: function() {
                            }
                        },
                        ok: {
                            label: trans.btn_ok,
                            className: 'btn-primary',
                            callback: function() {
                                $('input[name="_id"]', fn.milestones.$form).val(milestone_id);
                                $('input[name="_action"]', fn.milestones.$form).val('fund');
                                fn.milestones.submit();
                            }
                        },
                    },
                });
            },

            release: function() {
                if ( $('.bootbox').length > 0 ) {
                    return false;
                }

                var milestone_id = $(this).data('id');
                var _msg = '<div class="buyer-milestone-confirm">' + trans.confirm_release + '</div>';
                
                bootbox.dialog({
                    title: '',
                    message: _msg,
                    buttons: {
                        cancel: {
                            label: trans.btn_cancel,
                            className: 'btn-link',
                            callback: function() {
                            }
                        },
                        ok: {
                            label: trans.btn_ok,
                            className: 'btn-primary',
                            callback: function() {
                                $('input[name="_id"]', fn.milestones.$form).val(milestone_id);
                                $('input[name="_action"]', fn.milestones.$form).val('release');
                                fn.milestones.submit();
                            }
                        },
                    },
                });                
            },

            refund: function() {
            	if ( $('.bootbox').length > 0 ) {
                    return false;
                }
                
                var milestone_id = $(this).data('id');
                var _msg = '<div class="buyer-milestone-confirm">' + trans.confirm_refund_escrow + '</div>';
                
                bootbox.dialog({
                    title: '',
                    message: _msg,
                    buttons: {
                        cancel: {
                            label: trans.btn_cancel,
                            className: 'btn-link',
                            callback: function() {
                            }
                        },
                        ok: {
                            label: trans.btn_ok,
                            className: 'btn-primary',
                            callback: function() {
                                $('input[name="_id"]', fn.milestones.$form).val(milestone_id);
                                $('input[name="_action"]', fn.milestones.$form).val('refund_fund');
                                fn.milestones.submit();
                            }
                        },
                    },
                });
            },

            delete: function() {
            	if ( $('.bootbox').length > 0 ) {
                    return false;
                }
                
                var milestone_id = $(this).data('id');
                var _msg = '<div class="buyer-milestone-confirm">' + trans.confirm_delete + '</div>';
                
                bootbox.dialog({
                    title: '',
                    message: _msg,
                    buttons: {
                        cancel: {
                            label: trans.btn_cancel,
                            className: 'btn-link',
                            callback: function() {
                            }
                        },
                        ok: {
                            label: trans.btn_ok,
                            className: 'btn-primary',
                            callback: function() {
                                $('input[name="_id"]', fn.milestones.$form).val(milestone_id);
                                $('input[name="_action"]', fn.milestones.$form).val('delete_milestone');
                                fn.milestones.submit();
                            }
                        },
                    },
                });
            },

            requestPayment: function() {
            	if ( $('.bootbox').length > 0 ) {
                    return false;
                }
                
                var milestone_id = $(this).data('id');
                var _msg = '<div class="buyer-milestone-confirm">' + trans.confirm_request_payment + '</div>';
                
                bootbox.dialog({
                    title: '',
                    message: _msg,
                    buttons: {
                        cancel: {
                            label: trans.btn_cancel,
                            className: 'btn-link',
                            callback: function() {
                            }
                        },
                        ok: {
                            label: trans.btn_ok,
                            className: 'btn-primary',
                            callback: function() {
                                $('input[name="_id"]', fn.milestones.$form).val(milestone_id);
                                $('input[name="_action"]', fn.milestones.$form).val('request_payment');
                                fn.milestones.submit();
                            }
                        },
                    },
                });
            },

            submit: function() {
                fn.milestones.$form.submit();
            },
 		},

		payment: {
			$modal: null,
			$form: null,

			init: function() {
				this.$modal = $('#modalPayment');
				this.$form = $('#form_payment', this.$modal);

				this.validate();

				this.$modal.on('show.bs.modal', function(e) {
					var $btn = $(e.relatedTarget);
					var _type = $btn.data('type');
					$('#payment_type').val(_type);

					$('#payment_amount', fn.payment.$form).val('');
					$('#payment_note', fn.payment.$form).val('');
					$('#confirm_payment', fn.payment.$form).prop('checked', false);

					$('.has-error', fn.payment.$form).removeClass('has-error');
					$('.error', fn.payment.$form).remove();
				});
			},

			validate: function() {
				this.$form.validate({
					errorPlacement: function (error, element) {
						$(element).closest('.col-md-9').append(error);
					},
					highlight: function (element) {
						$(element).closest('.row').addClass('has-error');
					},
					success: function (label, element) {
						$('.error', $(element).closest('.row')).remove();
						$(element).closest('.row').removeClass('has-error');
					},
				});
			}
		},

 		feedback: {
 			$box: null,
 			$stars: null,

 			init: function() {
 				this.$box = $('.box-feedback');
 				this.$stars = $('.stars', this.$box);

 				stars.init(this.$stars);

 				for (var i = 0; i < $('.gray-stars').length; i++) {
 					var $gray_star = $($('.gray-stars')[i]);
	            	var score = $gray_star.parent().data('score');

	            	$gray_star.attr('data-toggle', 'tooltip');
	            	$gray_star.attr('title', score);
 				}

	            Global.renderTooltip();
 			}
 		},

 		refund: {
 			$modal: null,
 			$form: null,

 			init: function() {
 				this.$modal = $('#modalRefund');
 				this.$form = $('#form_payment', this.$modal);

 				this.validate();

 				$.validator.addMethod('max_paid_amount', function (value, element, param) {
				    return this.optional( element ) || parseFloat(value) <= parseFloat(param);
				}, trans.error_refund_amount_over_paid_amount);

 				this.$modal.on('hidden.bs.modal', function (e) {
 					$('#payment_amount', fn.refund.$form).val('');
					$('#payment_note', fn.refund.$form).val('');
					$('#confirm_payment', fn.refund.$form).prop('checked', false);

 					$('.has-error', fn.refund.$form).removeClass('has-error');
 					$('.error', fn.refund.$form).remove();
				});
 			},

			validate: function() {
				this.$form.validate({
					errorPlacement: function (error, element) {
						$(element).closest('.col-md-9').append(error);
					},
					highlight: function (element) {
						$(element).closest('.row').addClass('has-error');
					},
					success: function (label, element) {
						$('.error', $(element).closest('.row')).remove();
						$(element).closest('.row').removeClass('has-error');
					},
				});
			}
 		},

        dispute: {

            $modal: null,
            $reason: null,
            $formDisputeTicket: null,
            $formDisputeLastWeek: null,
            $disputeLastWeek: null,
            $confirmRefund: null,
            $confirmDispute: null,
            $buttonRefund: null,
            $buttonTicket: null,

        	init: function() {
                this.$modal = $('#modalDispute');
                this.$reason = $('#reason', fn.dispute.$modal);
                this.$formDisputeTicket = $('#formDisputeTicket', fn.dispute.$modal);
                this.$formDisputeLastWeek = $('#formDisputeLastWeek', fn.dispute.$modal);
                this.$disputeLastWeek = $('.dispute-last-week');
                this.$confirmRefund = $('#confirm_refund');
                this.$confirmDispute = $('#confirm_file_dispute');
                this.$buttonRefund = $('.btn-dispute-refund');
                this.$buttonTicket = $('.btn-send-ticket');

                this.bindEvents();

        		Global.renderMessageBoard();
        		Global.renderMaxlength();
        		Global.renderFileInput();
                Global.renderSelect2();
        	},

            bindEvents: function() {
                this.$reason.on('change', fn.dispute.refundButtonHandler);

                this.$confirmRefund.on('change', fn.dispute.refundButtonHandler);

                this.$confirmDispute.on('change', fn.dispute.disputeButtonHandler);

                this.$formDisputeLastWeek.on('submit', function() {
                	fn.dispute.$buttonRefund.attr('disabled', true);
                });

                this.$modal.on('hidden.bs.modal', function(e) {
                	fn.dispute.$reason.val('').trigger('change').trigger('change.select2');
                });

                $('#modal_cancel_dispute').on('shown.bs.modal', function(e) {
                    var $form = $('form', $(this));
                    $form.validate();
                });

                fn.dispute.$formDisputeLastWeek.validate();
                fn.dispute.$formDisputeTicket.validate();
            },

            refundButtonHandler: function() {
                if ( fn.dispute.$reason.val() == '1' ) {
                	fn.dispute.$formDisputeTicket.addClass('hidden');
                	fn.dispute.$formDisputeLastWeek.removeClass('hidden');
                	fn.dispute.$disputeLastWeek.removeClass('hidden');

                    if ( fn.dispute.$confirmRefund.is(':checked') ) {
                        fn.dispute.$buttonRefund.attr('disabled', false);
                    } else {
                        fn.dispute.$buttonRefund.attr('disabled', true);
                    }
                } else if ( fn.dispute.$reason.val() == '2' ) {
                	fn.dispute.$formDisputeLastWeek.addClass('hidden');
                	fn.dispute.$disputeLastWeek.addClass('hidden');
                	fn.dispute.$formDisputeTicket.removeClass('hidden');

                    fn.dispute.$buttonRefund.attr('disabled', true);
                } else {
                	fn.dispute.$formDisputeLastWeek.removeClass('hidden');
                	fn.dispute.$disputeLastWeek.addClass('hidden');
                	fn.dispute.$formDisputeTicket.addClass('hidden');

                	fn.dispute.$buttonRefund.attr('disabled', true);
                	fn.dispute.$buttonTicket.attr('disabled', true);
                }
            },

            disputeButtonHandler: function() {
            	if ( fn.dispute.$confirmDispute.is(':checked') ) {
                    fn.dispute.$buttonTicket.attr('disabled', false);
                } else {
                    fn.dispute.$buttonTicket.attr('disabled', true);
                }
            },
        }

    };

	return fn;
});