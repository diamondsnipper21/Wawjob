/**
 * @author Ro Un Nam
 * @since Dec 12, 2017
 */

define(['common', 'alert', 'ajax_datatable', 'bs-datepicker'], function (common) {
	var fn = {
		init: function() {
			this.initElements();
			
			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$form          = $('form.form-datatable');
            this.$container     = $('#cronjobs');
        },

		bindEvents: function() {
            var self = this;

            $(fn.$container).on('click', 'button.button-submit', function() {
                $('input[name="_action"]', fn.$container).val('CHANGE_STATUS');

                fn.$form.submit();
            });

            $('[data-toggle="tooltip"]').tooltip();
		},

		render: function() {
            this.renderDataTable();
            this.renderDateTimePicker();
            this.renderSelect2();

            common.handleUniform();
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
        }
	};

	return fn;
});