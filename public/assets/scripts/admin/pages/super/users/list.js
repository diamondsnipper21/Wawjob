/**
 * @author KCG
 * @since July 9, 2017
 * User List
 */

define(['common', 'ajax_datatable', 'bs-datepicker', 'reasonbox', 'alert'], function (common) {

	var fn = {
		init: function() {
            this.initElements();

			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$container = $('#user_list');
            this.$form      = $('form', this.$container);
        },

        bindEvents: function() {
            var self = this;
            
            // Handler when changing status for todo
            this.$container.on('click', 'button.button-change-status', function() {
                $('input[name="_action"]', self.$form).val('CHANGE_STATUS');
            });

            // delete, suspended, activate
            this.$container.off('click', '#button_action');   // preceding on-click event handler is deleted !
            this.$container.on('click', '#button_action', function() {
                var action = $('#select_action', self.$container).val();

                $('input[name="_action"]', self.$container).val(action);

                var modal_title = '';
                var modal_button_title = '';

                if (action == 2) { // Suspension
                    modal_title = 'Suspend Account';
                    modal_button_title = 'Suspend';
                } else if (action == 4) { // Financial Suspension
                    modal_title = 'Suspend Financial';
                    modal_button_title = 'Suspend';
                } else if (action == 5) {
                    modal_title = 'Delete User';
                    modal_button_title = 'Delete';
                } else if (action == 1) { // Activate
                    modal_title = 'Activate User';
                    modal_button_title = 'Activate';
                }

                if (modal_title != '' && modal_button_title != '') {
                    $.reasonbox.create({
                        title: modal_title,
                        cancelButton: {
                            label: "Cancel",
                            className: 'btn-default',
                            callback: function() {
                            }
                        },
                        actionButton: {
                            label: modal_button_title,
                            className: 'blue',
                            callback: function(e, reason) {
                                $('input[name="_action"]', self.$container).val(action);

                                window.setTimeout(function() {
                                    self.$form.submit();
                                }, 1);
                            }
                        },
                        $form: self.$form
                    });

                    return false;
                }

                if (action == 12) { // ID verified
                    $.alert.create({
                        message: 'Are you sure to force ID verification?',
                        title: 'Require ID verified',
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
                                $('input[name="_action"]', self.$container).val(action);

                                window.setTimeout(function() {
                                    self.$form.submit();
                                }, 1);
                            }
                        }
                    });

                    return false;
                }

                self.$form.submit();
            });
            
            this.$container.on('change', '#select_user_role', function() {
                document.location.href = $(this).data('url') + '/' + $(this).val();
            });
        },

        render: function() {
            this.renderSelect2();
            this.renderDataTable();
            this.renderDateTimePicker();

            common.handleUniform();

            $('td .label.label-status').each(function() {
                var title = $(this).attr('title');

                if (typeof title != 'undefined') {
                    $(this).tooltip()
                }
            });
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

        renderSelect2: function() {
            common.renderSelect2();
        },

        renderDataTable: function() {
            var self = this;
            this.$container.ajaxDatatable({
                success: function(html) {
                    self.initElements();
                    self.bindEvents();
                    self.render();
                }
            });
        }
	};

	return fn;
});