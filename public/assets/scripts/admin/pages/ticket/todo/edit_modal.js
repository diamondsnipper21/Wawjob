/**
 * @author KCG
 * @since July 4, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'moment', 'bs-modalmanager', 'bs-modal', 'bs-datepicker', 'jquery-form'], function (common, moment) {

    var fn = {
        init: function() {
            this.initElements();
            
            this.bindEvents();
            this.render();
        },

        initElements: function() {
            this.$container = $('#modal_todo');
            this.$form      = $('form', this.$container);
            this.$success   = $('.alert-success', this.$container);
            this.$error     = $('.alert-danger', this.$container);
        },

        bindEvents: function() {
            var self = this;

            this.$container.off('shown');
            this.$container.on('shown', function() {
                self.render();
            });
        },

        render: function() {
            this.$form.validate();

            // File Input
            Global.renderFileInput();

            // Due Date
            $('.datepicker-due-date').datepicker({
                rtl: Metronic.isRTL(),
                orientation: 'right',
                autoclose: true,
                startDate: moment().format('MM/DD/YYYY')
            });

            Global.renderMaxlength();

            // Select2
            // $('select[class*="select2"], input[class*="select2"]').select2('destroy');
            common.renderSelect2();

            // Related Ticket
            $('.select2-related-ticket').select2({
                width: '100%',
                placeholder: "Search for a ticket",
                minimumInputLength: 1,
                ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                    url: $('.select2-related-ticket').data('ajax-url'),
                    dataType: 'json',
                    type: 'POST',
                    blockUI: false,
                    data: function (params) {
                        return {
                            term: params.term, // search term
                            page_limit: params.page || 1
                        };
                    },
                    processResults: function (data) { // parse the results into the format expected by Select2.
                        // since we are using custom formatting functions we do not need to alter remote JSON data
                        return {
                            results: data.tickets
                        };
                    }
                },
                templateResult: function(ticket) {
                    if (!ticket.id)
                        return ticket.text;

                    var subject = ticket.subject?ticket.subject:ticket.text;
                    return $('<div class="ticket-item">' + '<span class="badge badge-warning">#' + ticket.id + '</span>&nbsp;' + subject + '</div>');
                },
                templateSelection: function(ticket) {
                    if (!ticket.id)
                        return ticket.text;
                    
                    var subject = ticket.subject?ticket.subject:ticket.text;
                    return $('<div class="ticket-item">' + '<span class="badge badge-warning">#' + ticket.id + '</span>&nbsp;' + subject + '</div>');
                }
            });
        }
    }

    return fn;
});
define.amd = amd;