/**
 * @author KCG
 * @since July 13, 2017
 */

define(['page_user_common', 'common', 'alert', 'ajax_datatable', 'bs-datepicker', 'reasonbox'], function (page_user_common, common) {
    var fn = {
        init: function() {
            page_user_common.init();

            this.initElements();

            this.bindEvents();
            this.render();
        },

        initElements: function() {
            this.$form      = $('form.form-datatable');
            this.$container = $('#proposals');
        },

        bindEvents: function() {
            var self = this;

            // Handler when changing status for todo
            $(this.$container).on('click', 'button.button-submit', function() { 
            	var action = $('select.select-action', fn.$container).val();
            	if ( action == 'DELETE' ) {
            		var totalChecked = $('td input[type="checkbox"].checkboxes:checked').length;
            		var confirm = 'Are you sure to delete ' + totalChecked + ' proposal(s)?';

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
		                        $('input[name="_action"]').val('DELETE');

		                        window.setTimeout(function() {
		                            self.$form.submit();
		                        }, 1);
		                    }
		                },
		                $form: fn.$form
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