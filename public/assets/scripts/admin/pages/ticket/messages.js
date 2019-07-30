/**
 * @author KCG
 * @since Dec 30, 2017
 */

define.amd = false;
define(['common', 'alert', 'ajax_datatable', 'bs-datepicker'], function (common) {

	var fn = {
		init: function() {
            this.initElements();
            
			this.bindEvents();
			this.render();

            this.modal.init();
		},

        initElements: function() {
            this.$container = $('#messages');
            this.$form      = $('form', this.$container);
        },

		bindEvents: function() {
            var self = this;

            // Handler when clicking "Delete" button
            $(this.$container).off('click', '.toolbar button.button-submit.button-delete, .delete-btn');
            $(this.$container).on('click', '.toolbar button.button-submit.button-delete, .delete-btn', function() {
                var checked = null;
                var $checkbox = null;

                if ($(this).hasClass('delete-btn')) {
                    var $checkbox = $('input[type="checkbox"]', $(this).closest('tr'));
                    checked = $checkbox.prop('checked');

                    $checkbox.attr('checked', true);
                    $checkbox.uniform();
                }

                $.alert.create({
                    message: 'Are you sure to delete these messages?',
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
                            $('input[name="_action"]', self.$form).val('DELETE');
                            self.$form.submit();
                        }
                    }
                });

                return false;
            });

            $(this.$container).off('click', 'tr');
            $(this.$container).on('click', 'tr', function() {
                var $self = $(this);
                var url = $(this).data('read-url');
            });
		},

		render: function() {
            var self = this;

            common.renderSelect2();

            common.handleUniform();

            this.$container.ajaxDatatable({
                success: function(html) {
                    self.initElements();
                    self.render();
                }
            });

            $('.datepicker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: 'right',
                autoclose: true
            });
		},

        modal: {
            init: function() {
                this.$container = $('#modal_create_thread');

                this.bindEvents();
            },

            bindEvents: function() {
                var self = this;

                this.$container.on('show', function() {
                    self.render();
                });
            },

            render: function() {
                $('form', this.$container).validate();
                Global.renderSelect2();
                Global.renderTooltip();
            }
        }
	};

	return fn;
});