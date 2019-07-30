/**
 * @author PYH
 * @since July 29, 2017
 */

define(['common', 'bs-modalmanager', 'bs-modal', 'bs-datepicker', 'ajax_datatable', 'jquery-form', 'alert'], function (common, bootbox) {

    var fn = {

        init: function() {
            this.initElements();
            this.bindEvents();
            this.formSubmitHandler();
            this.render();
        },

        initElements: function() {
            this.$container   = $('.portlet-body');
            this.$form        = $('form#withdraw_list', this.$container);
            this.$formProcess = $('form#withdraw_process', this.$container);
            this.$submitBtn   = $('.button-submit', this.$form );
            this.$modalNotify = $('#modal_notify');
        },

        bindEvents: function() {
        	$(this.$container).on('click', 'a.btn-view', function() {
                fn.popup.init($(this));
            });

            Global.renderTooltip();

            $('form', fn.$modalNotify).validate();

            fn.$modalNotify.on('hide.bs.modal', function() {
    			$('input[name="notify_manager"]', fn.$modalNotify).val('');
    			$('.has-error', fn.$modalNotify).removeClass('has-error');
    			$('.help-block-error', fn.$modalNotify).remove();
    		});
        },

        formSubmitHandler: function() {
            $(fn.$container).on('click', 'button.button-submit', function() {
            	var action = 'proceed';
            	switch ($('[name="withdraw_status"]').val()) {
            		case '3':
            			action = 'cancel';
            			break;
            		case '4':
            			action = 'review';
            			break;
                    case '7':
                        action = 'notify';
                        break;
            		default:
            			break;
            	}

            	var checkedIds = [];
                $('td input[type="checkbox"].checkboxes:checked').each(function() {
                    checkedIds.push($(this).val());
                });

                if ( action == 'notify' ) {
                	$('[name="ids"]', fn.$modalNotify).val(checkedIds.join(','));   
                	fn.$modalNotify.modal('show');
                } else {
                    var totalChecked = $('td input[type="checkbox"].checkboxes:checked').length;
                	var totalInReview = $('td input[data-inreview="true"].checkboxes:checked').length;
                	var confirm = 'Are you sure to ' + action + ' ' + totalChecked + ' withdraw(s)';
                	if ( totalInReview > 0 ) {
                		confirm += ' (<span class="text-danger">including ' + totalInReview + ' request(s) in review</span>)';
                	}
                	confirm += '?';

                    $.alert.create({
                        message: confirm,
                        title: 'Confirm',
                        cancelButton: {
                            label: "No",
                            className: 'btn-default',
                            callback: function() {
                            }
                        },
                        actionButton: {
                            label: "Yes",
                            className: 'blue',
                            callback: function() {
                                $('[name="_action"]', fn.$formProcess).val('CHANGE_STATUS');
                                $('[name="ids"]', fn.$formProcess).val(checkedIds.join(','));                        
                                $('[name="withdraw_status"]', fn.$formProcess).val($('[name="withdraw_status"]', fn.$form).val());

                                fn.$formProcess.submit();
                            }
                        }
                    });
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

        popup: {
        	modalClass: 'modal-withdraw',
        	$container: null,

            init: function($obj) {
            	this.$container = $('body');
            	$('.' + this.modalClass).remove();

            	var json = $obj.data('json');

                var html = '';
                html += '<div class="modal fade modal-scroll ' + this.modalClass + '" tabindex="-1" aria-hidden="true" data-backdrop="static" data-width="1000">';
                    html += '<div class="modal-header">';
                        html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
                        html += '<h4 class="modal-title">Withdrawal Details</h4>';
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
                                                    html += '</div>';
                                                } else {
                                                    html += gateway_data[1];
                                                }

                                            html += '</td>';
                                            html += '<td align="right"><label class="control-label">Status</label></td>';
                                            html += '<td><span class="label label-' + (json.status == 1 ? 'active' : json.status_string.toLowerCase()) + '">' + (json.status == 1 ? 'In Queue' : json.status_string) + '</span></td>';
                                        html += '</tr>';

                                        html += '<tr>';
                                            html += '<td align="right"><label class="control-label">Requested At</label></td>';
                                            html += '<td>' + json.created_at + '</td>';
                                            html += '<td align="right"><label class="control-label">Updated At</label></td>';
                                            html += '<td>' + json.updated_at + '</td>';
                                        html += '</tr>';

                                        // Wechat
                                        if ( json.user_payment_gateway_type == '3' ) {
	                                        html += '<tr>';
	                                            html += '<td align="right"><label class="control-label">QR Code</label></td>';
	                                            html += '<td align="center">';
                                                if ( json.user_payment_gateway_data.file_url != undefined ) {
                                                    html += '<img src="' + json.user_payment_gateway_data.file_url + '" width="150" height="150">';
                                                } else {
                                                    html += 'No QR code uploaded';
                                                }
                                                html += '</td>';
	                                            html += '<td align="right"><label class="control-label">CNY</label></td>';
                                                html += '<td><span class="fs-25"><strong>' + (json.exchange_amount) + '</strong></span><br/><span>(Exchange Rate: ' + json.exchange_rate + ')</span></td>';
	                                        html += '</tr>';
                                        // Payoneer
                                        } else if ( json.user_payment_gateway_type == '6' ) {
                                            html += '<tr>';
                                                html += '<td align="right"><label class="control-label">EUR</label></td>';
                                                html += '<td><span class="fs-25"><strong>' + (json.exchange_amount) + '</strong></span><br/><span>(Exchange Rate: ' + json.exchange_rate + ')</span></td>';
                                                html += '<td colspan="2"></td>';
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