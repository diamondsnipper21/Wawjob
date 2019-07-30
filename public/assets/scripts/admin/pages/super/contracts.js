/**
 * @author KCG
 * @since July 13, 2017
 */

define(['page_user_common', 'common', 'bs-modalmanager', 'bs-modal', 'reasonbox', 'ajax_datatable', 'bs-datepicker', 'jquery-form'], function (page_user_common, common) {
	var fn = {
		init: function() {
			page_user_common.init();

			this.initElements();
            this.modalbox.init();

			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$form          = $('form.form-datatable');
            this.$container     = $('#user_contracts');
            this.$reasonModal   = $('div#modal_reason');
            this.$error         = $('.alert-danger', this.$reasonModal);
        },

        modalbox: {

            init: function() {

                this.statusChangeHandler();
                this.submitButtonClickHandler();
                this.formSubmitHandler();
            },

            statusChangeHandler: function() {
                var self = this;
                
                // status change 
                fn.$form.on('change', 'select.select-change-status', function() {
                    var status = $(this).val();
                    var $checkboxes = $('td input[type="checkbox"]', self.$container);

                    if (status) {
                        $checkboxes.attr('disabled', true);
                        $('td input[data-status-' + status + '="true"]').attr('disabled', false);
                        $('td input[data-status-' + status + '!="true"]').attr('checked', false);
                    } else {
                        $checkboxes.attr('disabled', false);
                    }

                    $checkboxes.trigger('change', [true]);
                    
                    $.uniform.update($('td input[type="checkbox"]'));
                });
            },

            submitButtonClickHandler: function() {

                // submit button click  -   modal box showing
                $(fn.$container).off('click', 'button.button-submit');  // preceding on-click event handler is deleted !
                $(fn.$container).on('click', 'button.button-submit', function() {

                    var STATUS_CLOSED = 9;
                    var STATUS_ACTIVATE = 1;
                    var STATUS_SUSPEND = 3;
                    var STATUS_DELETE = 'DELETE';

                    var text = '';
                    var number = 0;

                    var target_status = $('select.select-action', fn.$form).val();

                    var modal_title = '';
                    var modal_button_caption = '';

                    if (target_status == STATUS_SUSPEND) {
                        modal_title = 'Suspend Contract';
                        modal_button_caption = 'Suspend';
                    } else if (target_status == STATUS_CLOSED) {
                        modal_title = 'Close Contract';
                        modal_button_caption = 'Done';
                    } else if (target_status == STATUS_ACTIVATE) {
                        modal_title = 'Activate Contract';
                        modal_button_caption = 'Activate';
                    } else if (target_status == STATUS_DELETE) {
                        modal_title = 'Delete Contract';
                        modal_button_caption = 'Delete';
                    }

                    $.reasonbox.create({
                        title: modal_title,
                        cancelButton: {
                            label: "Cancel",
                            className: 'btn-default',
                            callback: function() {
                            }
                        },
                        actionButton: {
                            label: modal_button_caption,
                            className: 'blue',
                            callback: function(e, reason) {
                                $('input[name="_action"]', fn.$container).val('CHANGE_STATUS');

                                fn.$form.submit();
                            }
                        },
                        $form: fn.$form
                    });
                    
                    return true;

                    $('tr input[type="checkbox"]:checked', fn.$container).each(function() {

                        var $tr = $(this).closest('tr');
                        text += $('td:eq(1)', $tr).text() + '&nbsp;&nbsp;&nbsp;&nbsp;' + $('td:eq(3)', $tr).text() + '<br />';
                        number ++;
                    });

                    // suspended status
                    if ( target_status == STATUS_SUSPEND ) {
                        $('span.action', fn.$reasonModal).text('Suspended Reason');
                        if (!$('textarea', fn.$reasonModal).hasClass('maxlength-handler')) {
                            $('textarea', fn.$reasonModal).addClass('maxlength-handler');
                        }
                        $('div.box', fn.$reasonModal).css('display','block');
                    }
                   
                    // activate status
                    if ( target_status == STATUS_ACTIVATE ) {
                        $('span.action', fn.$reasonModal).text('Activated Reason');
                        // $('.modal-title', fn.$reasonModal).hide();
                        // $('.maxlength-handler', fn.$reasonModal).val('activated');
                        // $('div.box', fn.$reasonModal).css('display','none');
                        if (!$('textarea', fn.$reasonModal).hasClass('maxlength-handler')) {
                            $('textarea', fn.$reasonModal).addClass('maxlength-handler');
                        }
                        $('div.box', fn.$reasonModal).css('display','block');

                    }

                    // delete status
                    if ( target_status == STATUS_DELETE ) {
                        $('span.action', fn.$reasonModal).text('Delete');
                        if ($('textarea', fn.$reasonModal).hasClass('maxlength-handler')) {
                            $('textarea', fn.$reasonModal).removeClass('maxlength-handler');
                        }
                        $('div.box', fn.$reasonModal).css('display','none');
                    }

                    $('span.list-number', fn.$reasonModal).html(number);
                    $('div.cancell', fn.$reasonModal).html(text);
                    $('textarea[name="description"]', fn.$reasonModal).val('');

                    fn.$reasonModal.modal('show');

                    $('input[name="_action"]', fn.$form).val('CHANGE_STATUS');

                });
            },

            formSubmitHandler: function() {
                //save button of modal box   -  click 
                $(fn.$reasonModal).on('click', 'button.save-button', function() {
                    
                    var reason_desc = '';
                    reason_desc = $('.maxlength-handler', fn.$reasonModal).val();

                    if ( reason_desc != '' ) {
                        fn.$error.hide();
                        $('.maxlength-handler', fn.$reasonModal).removeClass('has-error');
                        $('.help-block-error', fn.$reasonModal).hide();
                        $('input#reason_desc', fn.$form).val(reason_desc);

                        fn.$form.submit();

                        fn.$reasonModal.modal('hide');
                        $('input[name="_action"]', fn.$form).val('');
                    }
                    else {
                        fn.$error.show();
                        $('.maxlength-handler', fn.$reasonModal).addClass('has-error');
                        $('.help-block-error', fn.$reasonModal).show();
                    }
                });
            },
        },

		bindEvents: function() {
            var self = this;

            // Handler when changing status for todo
            $(this.$container).on('click', 'button.button-submit', function() {
                
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
                    // var $next = $(this).next();
                    // var $prev = $(this).prev();
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
        }
	};

	return fn;
});