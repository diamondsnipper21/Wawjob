/**
 * @author KCG
 * @since July 13, 2017
 */

define(['page_user_common', 'common', 'reasonbox', 'ajax_datatable', 'bs-datepicker'], function (page_user_common, common) {
	var fn = {
		init: function() {
			page_user_common.init();

			this.initElements();

			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$form      = $('form.form-datatable');
            this.$container = $('#user_messages');
        },

		bindEvents: function() {
            var self = this;
		},

		render: function() {
            this.renderDataTable();
            this.renderReasonBox();
            this.renderDateTimePicker();
            this.renderSelect2();

            common.handleUniform();
		},

		renderReasonBox: function() {
			var self = this;

			$('.button-submit').reasonbox({
				// message: 'Are you sure to delete these messages?',
				title: 'Delete Messages',
				cancelButton: {
					label: "Cancel",
		            className: 'btn-default',
		            callback: function() {
		            }
				},
				actionButton: {
					label: "Delete",
		            className: 'blue',
		            callback: function(e, reason) {
		            	$('input[name="_action"]').val('DELETE');

		            	self.$form.submit();
		            }
				},
                $form: self.$form
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