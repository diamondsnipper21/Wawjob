/**
 * @author KCG
 * @since July 29, 2017
 */

define(['common', 'bootbox', 'bs-modalmanager', 'bs-modal', 'bs-datepicker', 'ajax_datatable', 'jquery-form', 'reasonbox'], function (common, bootbox) {

    var fn = {

    	$container: null,
    	$form: null,
        $editForm: null,
    	$btnSubmit: null,

        init: function() {
            this.initElements();
            this.bindEvents();
            this.formSubmitHandler();
            this.render();

            fn.modal.init();
        },

        initElements: function() {
            this.$container   = $('.portlet-body');
            this.$form        = $('form#deposit_list', this.$container);
            this.$editForm    = $('form#formDeposit');
            this.$btnSubmit   = $('.button-submit', this.$form);
        },

        bindEvents: function() {
        	$(this.$container).on('click', 'a.btn-view', function() {
                fn.popup.init($(this));
            });

            $('[data-toggle="tooltip"]').tooltip();
        },

        formSubmitHandler: function() {
            $(fn.$container).on('click', 'button.button-submit', function() {
                bootbox.hideAll();

                var action = $('select.select-action', fn.$container).val();

            	if ( action == 'EDIT' ) {
            		var $checked = $('td input[type="checkbox"].checkboxes:checked');
            		if ( $checked.length > 1 ) {
            			$checked.each(function(k, v) {
            				if ( k > 0 ) {
            					$(v).prop('checked', false).uniform();
            				}
            			});

            			$checked = $checked.get(0);
            		}

            		fn.modal.$modal.on('show.bs.modal', function() {
            			fn.modal.showModal($checked.closest('tr').find('.btn-view'));
            		}).on('hide.bs.modal', function(e) {
	                	fn.modal.hideModal();
	                }).modal('show');
            	} else if ( action == 'DELETE' ) {
                    var totalChecked = $('td input[type="checkbox"].checkboxes:checked').length;
                    var confirm = 'Are you sure to delete ' + totalChecked + ' deposit(s)?';

                    bootbox.confirm(confirm, function(result) {
                        if ( result ) {
                            $('input[name="_action"]', fn.$form).val('CHANGE_STATUS');
                            fn.$form.submit();
                        }
                    });
                } else if ( action == '6' ) { // Suspend
                	var totalChecked = $('td input[type="checkbox"].checkboxes:checked').length;
                    var confirm = 'Are you sure to suspend ' + totalChecked + ' deposit(s)?';

                    $.reasonbox.create({
                        title: confirm,
                        cancelButton: {
                            label: 'Cancel',
                            className: 'btn-default',
                            callback: function() {
                            }
                        },
                        actionButton: {
                            label: 'Submit',
                            className: 'blue',
                            callback: function(e, reason, reason_option) {
                                $('input[name="_action"]', fn.$form).val('CHANGE_STATUS');

                                window.setTimeout(function() {
                                    fn.$form.submit();
                                }, 1);
                            }
                        },
                        $form: fn.$form
                    });
                } else {
                	$('input[name="_action"]', fn.$form).val('CHANGE_STATUS');
                	fn.$form.submit();
                }
            });
        },

        render: function() {
            this.renderDataTable();
            this.renderDateTimePicker();
            this.renderSelect2();

            common.handleUniform();
        },

        renderDateTimePicker: function() {
            $('.datepicker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: 'right',
                autoclose: true,
                changeDate: function() {
                }
            });
        },

        renderSelect2: function() {
            common.renderSelect2();
        },

        renderDataTable: function() {
            var self = this;
            fn.$container.ajaxDatatable({
                success: function(html) {
                    self.init();
                }
            });
        },

        modal: {
        	$modal: null,
        	$form: null,
        	$btnAddDeposit: null,
        	$id: null,
        	$userId: null,
        	$amount: null,
        	$info: null,
        	$paymentGatewayType: null,
        	$allGatewayFields: null,

        	$paypalEmail: null,
            $skrillEmail: null,
        	$wepayPhoneNumber: null,

        	$firstName: null,
        	$lastName: null,
        	$cardNumber: null,
        	$cardType: null,
        	$expDateMonth: null,
        	$expDateYear: null,
        	$cvv: null,

        	$bankName: null,
        	$bankBranch: null,
        	$bankCountry: null,
        	$ibanAccountNo: null,
        	$accountName: null,
            $beneficiaryAddress1: null,
            $beneficiaryAddress2: null,
            $beneficiarySwiftCode: null,
        	$bankFields: null,

        	init: function() {
        		this.$modal = $('#modal_deposit');
        		this.$form = $('#formDeposit', this.$modal);
        		this.$id = $('input[name="_id"]', this.$form);
        		this.$userId = $('#user_id', this.$form);
        		this.$amount = $('#amount', this.$form);
        		this.$info = $('#description', this.$form);
        		this.$paymentGatewayType = $('#user_payment_gateway_type', this.$form);
        		this.$allGatewayFields = $('.all-gateway-fields', this.$form)

        		this.$paypalEmail = $('#paypalEmail', this.$form);
                this.$skrillEmail = $('#skrillEmail', this.$form);
        		this.$wepayPhoneNumber = $('#wepayPhoneNumber', this.$form);

        		this.$firstName = $('#firstName', this.$form);
        		this.$lastName = $('#lastName', this.$form);
        		this.$cardNumber = $('#cardNumber', this.$form);
        		this.$cardType = $('#cardType', this.$form);
        		this.$expDateMonth = $('#expDateMonth', this.$form);
        		this.$expDateYear = $('#expDateYear', this.$form);
        		this.$cvv = $('#cvv', this.$form);
        		
        		this.$bankName = $('#bankName', this.$form);
        		this.$bankBranch = $('#bankBranch', this.$form);
        		this.$bankCountry = $('#bankCountry', this.$form);
        		this.$ibanAccountNo = $('#ibanAccountNo', this.$form);
                this.$accountName = $('#accountName', this.$form);
                this.$beneficiaryAddress1 = $('#beneficiaryAddress1', this.$form);
        		this.$beneficiaryAddress2 = $('#beneficiaryAddress2', this.$form);
                this.$beneficiarySwiftCode = $('#beneficiarySwiftCode', this.$form);
        		
        		this.$btnAddDeposit = $('#btnAddDeposit');

        		this.bindEvents();
        	},

        	bindEvents: function() {
	            this.$paymentGatewayType.on('change', function() {
	            	if ( $(this).val() != '' ) {
	            		fn.modal.showGatewayFields($(this).val());

	            		var gateway = fn.modal.$paymentGatewayType.val();

                        $.post(fn.$form.attr('action'), {
                        	_action: 'user_gateway',
                        	user_id: fn.modal.$userId.val(),
                        	gateway: gateway
                        }, function (json) {
                        	if ( json.data ) {
                        		if ( gateway == '1' ) { // PayPal
                        			$('#email_1').val(json.data);
                        		} else if ( gateway == '2' ) { // Credit Card
                        			$('#firstName_2').val(json.data.firstName);
                        			$('#lastName_2').val(json.data.lastName);
                        		} else if ( gateway == '3' ) { // WeChat
                        			$('#phoneNumber_3').val(json.data);
                        		} else if ( gateway == '4' ) { // Bank
                        			$('#bankName_4').val(json.data.bankName);
                        			$('#bankCountry_4').val(json.data.bankCountry);
                        			$('#bankBranch_4').val(json.data.bankBranch);
                        			$('#ibanAccountNo_4').val(json.data.ibanAccountNo);
                        			$('#accountName_4').val(json.data.accountName);
                        			$('#beneficiaryAddress1_4').val(json.data.beneficiaryAddress1);
                        			$('#beneficiaryAddress2_4').val(json.data.beneficiaryAddress2);
                        			$('#beneficiarySwiftCode_4').val(json.data.beneficiarySwiftCode);
                        		} else if ( gateway == '5' ) { // Skrill
                        			$('#email_5').val(json.data);
                        		} else if ( gateway == '6' ) { // Payoneer
                        			$('#email_6').val(json.data);
                        		}
                        	}
                        });
	            	}
	            });

	            this.$btnAddDeposit.on('click', function() {
	            	fn.modal.$id.val('');
	            	fn.modal.$modal.on('show.bs.modal', function() {
            			fn.modal.showModal();
            		}).on('hide.bs.modal', function(e) {
	                	fn.modal.hideModal();
	                }).modal('show');
	            });

	            this.$form.validate();
        	},

        	showModal: function($obj) {
        		$('span', $('.modal-title', fn.$modal)).text('New');

        		if ( $obj ) {
        			var json = $obj.data('json');
                    var $fields = $('#gateway_fields_' + json.user_payment_gateway_type, fn.$modal);

        			$('span', $('.modal-title', fn.$modal)).text('Update');

        			fn.modal.$id.val(json.id);
                    
                    // Add selected user into select2 box.
                    var userOption = new Option(json.user_name, json.user_id, false, false);
                    fn.modal.$userId.append(userOption).trigger('change');

        			fn.modal.$amount.val(json.amount);
        			fn.modal.$info.val(json.comment);

        			fn.modal.$paymentGatewayType.val(json.user_payment_gateway_type);

        			if ( json.user_payment_gateway_data != undefined ) {
                        $.each(json.user_payment_gateway_data, function(i, v) {
                            var $obj = $('#' + i + '_' + json.user_payment_gateway_type, $fields);
                            if ( $obj.hasClass('select2') ) {
                                $obj.val(v).trigger('change.select2');
                            } else if ( i == 'cardNumber') {
                                $obj.val('xxxx xxxx xxxx ' + v).removeAttr('data-rule-number');
                            } else {
                                $obj.val(v);
                            }
                        });
	        		}
        		}

                common.renderSelect2();

        		this.$userId.select2({
                    width: '100%',
                    placeholder: 'Search for a buyer',
                    minimumInputLength: 1,
		            ajax: {
	                    url: fn.modal.$form.attr('action'),
	                    dataType: 'json',
	                    type: 'POST',
                        blockUI: false,
	                    data: function (params) {
	                        return {
	                            term: params.term, // search term
                                page_limit: 10,
	                            _action: 'search_user'
	                        };
	                    },
	                    processResults: function (data) {
	                        return {
	                            results: data.users
	                        };
	                    }
	                },
	                templateResult: function(user) {
                        if (!user.id || !user.fullname)
                            return user.text;

                        return '#' + user.id + ' - ' + user.fullname;
                    },
                    templateSelection: function(user) {
                        if (!user.id || !user.fullname)
                            return user.text;

                        return '#' + user.id + ' - ' + user.fullname;
                    }
		        });

	            if ( $obj ) {
		            if ( fn.modal.$paymentGatewayType.val() != '' ) {
	            		fn.modal.showGatewayFields(fn.modal.$paymentGatewayType.val());
		            }
		        }
        	},

        	hideModal: function() {
        		fn.modal.$id.val('');
        		$('.form-control', fn.modal.$form).val('');
        		$('.gateway-fields', fn.modal.$form).addClass('hide');

        		$('.has-error', fn.modal.$form).removeClass('has-error');
        		$('.help-block-error', fn.modal.$form).remove();
        	},

        	showGatewayFields: function(type) {
        		var $selectedFields = $('#gateway_fields_' + type);

        		$('.gateway-fields', fn.modal.$form).addClass('hide');
        		$selectedFields.removeClass('hide');

            	$('.form-control', fn.modal.$allGatewayFields).attr('data-rule-required', false);
            	$('.form-control', $selectedFields).attr('data-rule-required', true);            	

            	fn.modal.$form.validate();
        	},
        },

        popup: {
        	modalClass: 'modal-deposit',
        	$container: null,

            init: function($obj) {
            	this.$container = $('body');
            	$('.' + this.modalClass).remove();

            	var json = $obj.data('json');

                var html = '';
                html += '<div class="modal fade modal-scroll ' + this.modalClass + '" tabindex="-1" aria-hidden="true" data-backdrop="static" data-width="1000">';
                    html += '<div class="modal-header">';
                        html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
                        html += '<h4 class="modal-title">Deposit Details</h4>';
                    html += '</div>';

                    html += '<div class="modal-body">';

                        html += '<div class="row">';
                            html += '<div class="col-md-12">';
                                html += '<div class="table-container">';
                                    html += '<table class="table table-striped table-bordered">';
                                        html += '<tr>';
                                            html += '<td width="15%" align="right"><label class="control-label">User</label></td>';
                                            html += '<td width="35%">' + json.user_name + '</td>';
                                            html += '<td width="15%" align="right"><label class="control-label">Amount</label></td>';
                                            html += '<td width="35%">$' + json.amount + '</td>';
                                        html += '</tr>';

                                        var gateway_data = json.user_payment_gateway.split(' - ');

                                        html += '<tr>';
                                            html += '<td align="right"><label class="control-label">Payment Gateway</label></td>';
                                            html += '<td>';
                                                html += '<div class="pb-2"><strong>' + gateway_data[0] + '</strong></div>';

                                                // Bank Transfer
                                                if ( json.user_payment_gateway_type == '4' ) {
                                                    html += '<div class="pl-2">';
                                                    html += '<div class="row"><div class="col-sm-6"><strong>Bank Name:</strong></div><div class="col-sm-6">' + json.user_payment_gateway_data.bankName + '</div></div>';
                                                    html += '<div class="row"><div class="col-sm-6"><strong>Country of Bank:</strong></div><div class="col-sm-6">' + json.user_payment_gateway_data.bankCountryName + '</div></div>';
                                                    html += '<div class="row"><div class="col-sm-6"><strong>Bank Branch:</strong></div><div class="col-sm-6">' + json.user_payment_gateway_data.bankBranch + '</div></div>';
                                                    html += '<div class="row"><div class="col-sm-6"><strong>IBAN / Account No:</strong></div><div class="col-sm-6">' + json.user_payment_gateway_data.ibanAccountNo + '</div></div>';
                                                    html += '<div class="row"><div class="col-sm-6"><strong>Account Name:</strong></div><div class="col-sm-6">' + json.user_payment_gateway_data.accountName + '</div></div>';
                                                    if ( json.meta != undefined ) {
                                                        html += '<div class="row"><div class="col-sm-6"><strong>Reference Date:</strong></div><div class="col-sm-6">' + json.meta.deposit_date + '</div></div>';
                                                    }
                                                    html += '<div class="row"><div class="col-sm-6"><strong>Reference ID:</strong></div><div class="col-sm-6">' + json.order_id + '</div></div>';
                                                    html += '</div>';
                                                } else {
                                                    html += gateway_data[1];
                                                }
                                            html += '</td>';
                                            html += '<td align="right"><label class="control-label">Status</label></td>';
                                            html += '<td><span class="label label-' + (json.status == 1 ? 'active' : json.status_string.toLowerCase()) + '">' + (json.status == 1 ? 'In Queue' : json.status_string) + '</span></td>';
                                        html += '</tr>';

                                        html += '<tr>';
                                            html += '<td align="right"><label class="control-label">Deposited At</label></td>';
                                            html += '<td>' + json.created_at + '</td>';
                                            html += '<td align="right"><label class="control-label">Updated At</label></td>';
                                            html += '<td>' + json.updated_at + '</td>';
                                        html += '</tr>';

                                        html += '<tr>';
                                            html += '<td align="right"><label class="control-label">Comment</label></td>';
                                            html += '<td>' + json.comment + '</td>';
                                            
                                            if ( json.reason_message != undefined ) {
                                            	html += '<td align="right"><label class="control-label">Reason</label></td>';
                                            	html += '<td>' + json.reason_message + '</td>';
                                            } else {
                                            	html += '<td colspan="2"></td>';
                                            }
                                        html += '</tr>';

                                        if ( json.modified_by != undefined ) {
	                                        html += '<tr>';
	                                            html += '<td align="right"><label class="control-label">Modified By</label></td>';
	                                            html += '<td>' + json.modified_by + '</td>';
	                                            
	                                            if ( json.action_history != undefined ) {
	                                            	html += '<td align="right"><label class="control-label">Action</label></td>';
	                                            	html += '<td>' + json.action_history + '</td>';
	                                            } else {
	                                            	html += '<td colspan="2"></td>';
	                                            }
	                                        html += '</tr>';
	                                    }

                                    html += '</table>';
                                html += '</div>';
                            html += '</div>';
                        html += '</div>';

                    html += '</div>';

                    html += '<div class="modal-footer">';
                        html += '<button type="submit" data-dismiss="modal" class="btn btn-default btn-cancel">Close</button>';
                    html += '</div>';
		        html += '</div><!-- .modal -->';

                this.$container.append(html);

                var $modal = $('.' + this.modalClass);
                $modal.modal('show');
            }
        }
    };

    return fn;
});