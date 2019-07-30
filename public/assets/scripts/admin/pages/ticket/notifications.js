/**
 * @author KCG
 * @since June 23, 2017
 */

define.amd = false;
define(['common', 'alert', 'ajax_datatable', 'bs-datepicker'], function (common) {

	var fn = {
		init: function() {
            this.initElements();
            
			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$container = $('#notifications');
            this.$form      = $('form', this.$container);
        },

		bindEvents: function() {
            var self = this;

            // Handler when changing status for todo
            $(this.$container).on('click', 'button.button-change-status', function() {
                var action = $('select.select-change-status').val();
                var $checkboxes = $('input[type="checkbox"]:checked', $(self.$container));

                if (action == 'DELETE') {
                    self.deleteNotifications();
                } else {
                    $('input[name="_action"]', self.$form).val('CHANGE_STATUS');

                    self.$form.submit();
                }
            });

            $(this.$container).on('click', 'a.delete-btn', function() {
                var $checkbox = $('input[type="checkbox"]', $(this).closest('tr'));
                checked = $checkbox.prop('checked');
                
                $checkbox.attr('checked', true);
                $checkbox.uniform();

                self.deleteNotifications();

                return false;
            });
		},

        deleteNotifications: function() {
            var checked = null;
            var $checkbox = null;
            var self = this;

            $.alert.create({
                message: 'Are you sure to delete these notifications?',
                title: 'Delete Notification',
                cancelButton: {
                    label: "Cancel",
                    className: 'btn-default',
                    callback: function() {
                        if ($checkbox != null) {
                            $checkbox.attr('checked', checked);
                            $checkbox.uniform();
                        }
                    }
                },
                actionButton: {
                    label: "Delete",
                    className: 'blue',
                    callback: function() {
                        $('input[name="_action"]', self.$form).val('CHANGE_STATUS');
                        $('select.select-change-status', self.$form).val('DELETE');

                        self.$form.submit();
                    }
                }
            });
        },

		render: function() {
            var self = this;

            common.renderSelect2();

            common.handleUniform();

            $('.page-body-inner').ajaxDatatable({
                success: function(html) {
                    app.init();

                    self.init();
                }
            });

            $('.datepicker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: 'right',
                autoclose: true
            });

            Global.renderTooltip();
		}
	};

	return fn;
});