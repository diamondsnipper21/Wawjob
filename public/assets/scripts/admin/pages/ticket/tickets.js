/**
 * @author KCG
 * @since June 30, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'ajax_datatable', 'bs-datepicker'], function (common) {

    var fn = {
        solve_modal : null,
        init: function() {
            var self = this;
            this.initElements();
            
            requirejs(['/assets/scripts/admin/pages/ticket/ticket/solve_modal.js'], function(modal) {
                fn.solve_modal = modal;

                modal.init();

                this.modal = modal;

                $(modal.$container).off('bs.modal.success.close');
                $('body').on('bs.modal.success.close', this.modal.$container, function(e, html) {
                    fn.$form.submit();
                });
                $(modal.$container).on('bs.modal.success.close', function(e, html) {
                    fn.$form.submit(); 
                });
            });

            this.bindEvents();
            this.render();

            requirejs(['/assets/scripts/admin/pages/ticket/ticket/assign_modal.js'], function(modal) {
                self.signModal = modal;
                self.signModal.init();
            });
        },

        initElements: function() {
            this.$container = $('#tickets');
            this.$form      = $('form#ticket_list');
        },

        bindEvents: function() {
            var self = this;

            $(this.$container).off('change', '#action-required-chk');
            $(this.$container).on('change', '#action-required-chk', function(e) {
                if ($(e.target).prop("checked")) {
                    $('select[name="filter[new]"]').val(1);
                    $('select[name="filter[new]"]').trigger('change.select2');
                    self.$form.submit();
                } else {
                    $('select[name="filter[new]"]').val("");
                    $('select[name="filter[new]"]').trigger('change.select2');
                    self.$form.submit();
                }
            });

            $('.button-assign-to').off('click');
            $('.button-assign-to').on('click', function(e) {
                var $disable_assigne_chks = $('tbody input[type="checkbox"]:checked.disable-assign', self.$container);
                $disable_assigne_chks.attr('checked', false);
                $disable_assigne_chks.trigger('change', [true]);

                if ($disable_assigne_chks.length > 0) {
                    $('thead input[type="checkbox"]', self.$container).attr('checked', false);
                }

                self.signModal.hideConfirm();
                if ($('tbody input[type="checkbox"]:checked.assigned', self.$container).length > 0)
                    self.signModal.showConfirm();

                $.uniform.update($('table.table input[type="checkbox"]', self.$container));
            });

            $('tbody input[type="checkbox"]').off('change');
            $('tbody input[type="checkbox"]').on('change', function(e, force_event) {
                if (force_event)
                    return true;

                var is_checked_dispute = $('tbody input[type="checkbox"]:enabled:checked.disable-assign', self.$container).length != 0 ||
                                         $('tbody input[type="checkbox"]:checked', self.$container).length == 0

                $('.button-assign-to').attr('disabled', is_checked_dispute);
            });
            
            $('#btn-submit').off('click');
            $('#btn-submit').on('click', function() {
                $ticket_ids = [];
                $tickets = $('input[name="ids[]"]', $('tr input[type="checkbox"]:checked').parent().parent().parent().parent())
                for($i = 0; $i < $tickets.length; $i++)
                    $ticket_ids.push($($tickets[$i]).val());

                self.solve_modal.setTicketIDs($ticket_ids, "list");
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
                    self.bindEvents();
                    self.render();

                    self.signModal.init();
                }
            });
        }
    };

    return fn;
});
define.amd = amd;