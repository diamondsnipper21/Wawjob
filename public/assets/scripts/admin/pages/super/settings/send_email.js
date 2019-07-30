define(['common', 'ajax_datatable', 'bs-datepicker', 'reasonbox'], function (common) {

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

            this.$container.off('change', '#select_action');
            this.$container.on('change', '#select_action', function() {
            	if ( $(this).val() ) {
            		$('#button_action').prop('disabled', false);
            	} else {
            		$('#button_action').prop('disabled', true);
            	}
            });

            // delete, suspended, activate
            this.$container.off('click', '#button_action');
            this.$container.on('click', '#button_action', function() {
                var action = $('#select_action', self.$container).val();

                $('input[name="_action"]', self.$container).val(action);

                var modal_title = '';
                var modal_button_title = '';

                $.reasonbox.create({
                    title: 'Send email to user(s)',
                    placeholder: 'Content',
                    cancelButton: {
                        label: "Cancel",
                        className: 'btn-default',
                        callback: function() {
                        }
                    },
                    actionButton: {
                        label: 'Send',
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
            });
        },

        render: function() {
            this.renderSelect2();
            this.renderDataTable();
            this.renderDateTimePicker();

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