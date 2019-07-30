/**
 * @author KCG
 * @since June 30, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'moment', 'ajax_datatable', 'bs-datepicker'], function (common) {

	var fn = {
		init: function() {
            var self = this;

            this.initElements();
            
			this.bindEvents();
			this.render();

            requirejs(['/assets/scripts/admin/pages/ticket/todo/edit_modal.js'], function(modal) {
                self.editModal = modal;
                self.editModal.init();

                $('body').on('bs.modal.success.close', self.editModal.$container, function() {
                    self.$form.submit();
                });
            });
		},

        initElements: function() {
            this.$container = $('.tab-pane.active');
            this.$form      = $('form#todo_list');
        },

		bindEvents: function() {
            var self = this;

            // Handler when changing status for todo
            $(this.$container).on('click', 'button.button-change-status', function() {
                $('input[name="_action"]', self.$form).val('CHANGE_STATUS');
            });

            this.$container.on('change', '.filter-show-only-mine input[type="checkbox"]', function() {
                self.$form.submit();
            });
            
		},

		render: function() {

            this.renderDataTable();
            this.renderDateTimePicker();
            this.renderSelect2();

            common.handleUniform();

            $('[data-toggle="tooltip"]').tooltip();
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
                    self.render();
                    self.editModal.init();
                }
            });
        }
	};

	return fn;
});
define.amd = amd;