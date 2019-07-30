/**
 * @author KCG
 * @since July 24, 2017
 */

define(['common', 'alert', 'ajax_datatable', 'bs-datepicker', 'reasonbox'], function (common) {
    var fn = {
        init: function() {

            this.initElements();

            this.bindEvents();
            this.render();
        },

        initElements: function() {
            this.$form      = $('form.form-datatable');
            this.$container = $('#escrows');
        },

        bindEvents: function() {
            var self = this;

            // Handler when changing status for todo
            $(this.$container).on('click', 'button.button-submit', function() {
            	var title = 'Release to freelancer';
            	if ( $('select.select-action', self.$container).val() == 4 ) {
            		title = 'Refund to buyer';
            	}

                $.reasonbox.create({
                    title: title,
                    fields: [
                		{
                			'type': 'select',
                    		'name': 'reason_option',
                    		'id': 'reason_option',
                    		'class': 'form-control select2',
                            'required': true,
                    		'options': {
                    			'1': 'Unresponsive',
                    			'2': 'Dispute',
                    			'3': 'Other'
                    		}
                    	}
                    ],
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
                            $('input[name="_action"]').val('CHANGE_STATUS');

                            window.setTimeout(function() {
                                self.$form.submit();
                            }, 1);
                        }
                    },
                    $form: self.$form
                });
            });

            $(this.$container).on('click', 'a.btn-view', function() {
                fn.popup.init($(this));
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

        renderDataTable: function() {
            var self = this;
            this.$container.ajaxDatatable({
                success: function(html) {
                    self.initElements();
                    self.render();
                }
            });
        },

        renderSelect2: function() {
            common.renderSelect2();
        },

        popup: {
        	modalClass: 'modal-escrow',
        	$container: null,

            init: function($obj) {
            	this.$container = $('body');
            	$('.' + this.modalClass).remove();

            	var json = $obj.data('json');

                var html = '';
                html += '<div class="modal fade modal-scroll ' + this.modalClass + '" tabindex="-1" aria-hidden="true" data-backdrop="static" data-width="1000">';
                    html += '<div class="modal-header">';
                        html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
                        html += '<h4 class="modal-title">Escrow Details</h4>';
                    html += '</div>';

                    html += '<div class="modal-body">';

                    	html += '<div class="row">';
                            html += '<div class="col-md-12">';
                                html += '<div class="table-container">';
                                    html += '<table class="table table-striped table-bordered">';
                                        html += '<tr>';
                                            html += '<td width="15%" align="right"><label class="control-label">Contract</label></td>';
                                            html += '<td width="35%">' + json.contract_title + '</td>';
                                            html += '<td width="15%" align="right"><label class="control-label">Milestone</label></td>';
                                            html += '<td width="35%">' + json.name + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                            html += '<td align="right"><label class="control-label">Buyer</label></td>';
                                            html += '<td>' + json.buyer_name + '</td>';
                                            html += '<td align="right"><label class="control-label">Freelancer</label></td>';
                                            html += '<td>' + json.freelancer_name + '</td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                            html += '<td align="right"><label class="control-label">Amount</label></td>';
                                            html += '<td>$' + json.amount + '</td>';
                                            html += '<td align="right"><label class="control-label">Status</label></td>';
                                            html += '<td><span class="label label-' + json.status_string.toLowerCase().replace(/ /g, '-') + '">' + json.status_string + '</span></td>';
                                        html += '</tr>';
                                        html += '<tr>';
                                            html += '<td align="right"><label class="control-label">Funded At</label></td>';
                                            html += '<td>' + json.funded_at + '</td>';
                                            html += '<td align="right"><label class="control-label">Updated At</label></td>';
                                            html += '<td>' + json.updated_at + '</td>';
                                        html += '</tr>';
                                    html += '</table>';
                                html += '</div>';
                            html += '</div>';
                    	html += '</div>';

                		html += '<hr>';

                    	html += '<div class="row">';
	                    	html += '<div class="col-md-12">';
                                html += '<div class="table-container">';
                                    html += '<table class="table table-striped table-bordered">';
                                        html += '<tr>';
	                                        html += '<td width="15%" align="right"><label class="control-label">Performed By</label></td>';
                                            html += '<td width="35%">' + json.performed_by + '</td>';
                                            
                                            if ( json.reason != undefined ) {
	                                            html += '<td width="15%" align="right"><label class="control-label">Reason</label></td>';
	                                            html += '<td width="35%">' + json.reason + '</td>';
	                                        } else {
	                                        	html += '<td colspan="2"></td>';
	                                        }
                                        html += '</tr>';

                                        if ( json.reason != undefined ) {
	                                        html += '<tr>';
	                                        	html += '<td colspan="2"></td>';
		                                        html += '<td align="right"><label class="control-label">Comment</label></td>';
	                                            html += '<td>' + json.reason_message + '</td>';
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