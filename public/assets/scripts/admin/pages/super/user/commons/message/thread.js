/**
 * @author KCG
 * @since July 14, 2017
 */

define(['page_user_common', 'common', 'reasonbox', 'moment'], function (page_user_common, common) {
	var fn = {
		init: function() {
			page_user_common.init();

			this.initElements();

			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$container = $('#user_message_thread');
            this.$form      = $('form.form-datatable', this.$container);
        },

		bindEvents: function() {
            var self = this;
		},

		render: function() {
            this.renderReasonBox();
            this.renderDataTable();

            Global.renderMessageBoard();
		},

		renderReasonBox: function() {
			var self = this;

			$('.btn-delete').reasonbox({
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
		            	$('input[name="action"]').val('DELETE');
		            	$('input[name="reason"]').val(reason);

		            	self.$form.submit();
		            }
				},
				$form: self.$form
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
	};

	return fn;
});