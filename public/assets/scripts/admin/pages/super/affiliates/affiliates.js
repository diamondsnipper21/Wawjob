/**
 * @author KCG
 * @since July 13, 2017
 */

define(['page_user_common', 'common', 'bs-modal', 'ajax_datatable', 'bs-datepicker', 'jquery-form'], function (page_user_common, common) {
	var fn = {
		init: function() {
			page_user_common.init();

			this.initElements();
            //this.modalbox.init();

			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$form          = $('form.form-datatable');
            this.$container     = $('.portlet-body');
        },        

		bindEvents: function() {
            var self = this;

            // Handler when changing status for todo
            $(this.$container).on('click', 'button.button-submit', function() {
                 self.$form.submit();
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
        }
	};

	return fn;
});