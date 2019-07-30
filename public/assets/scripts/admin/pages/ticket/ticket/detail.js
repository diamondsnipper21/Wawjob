/**
 * @author KCG
 * @since July 4, 2017
 */

var amd = define.amd;
define.amd= false;

define(['page_user_common', 'common', 'alert', 'ajax_datatable'], function (page_user_common, common) {

    var fn = {
        modal : null,

        init: function() {
            var self = this;
            ticket_ids = [];

            page_user_common.init();
            this.initElements();
            
            requirejs(['/assets/scripts/admin/pages/ticket/ticket/solve_modal.js'], function(modal) {
                
                fn.modal = modal;

                modal.init();

                this.modal = modal;

                $('body').off('bs.modal.success.close', this.modal.$container);
                $('body').on('bs.modal.success.close', this.modal.$container, function(e, html) {
                    self.refresh(html);
                });

                $(modal.$container).on('bs.modal.success.close', function(e, html) {
                    self.refresh(html);
                });
            });

            this.bindEvents();
            this.render();
        },

        initElements: function() {
            this.$container        = $('.ticket-detail');
            this.$comments_form    = $('#form_message', this.$container);
            this.$comments_filter  = $('#comments_filter', this.$container);
            this.$detail_form      = $('#form_detail', this.$container);
        },

        bindEvents: function() {
            var self = this;

            // Assign To Me
            $('body').off('click', '.alert-assign-to-me button');
            $('body').on('click', '.alert-assign-to-me button', function() {
                $('input[name="_action"]', self.$detail_form).val('ASSIGN_TO_ME');
                self.$detail_form.submit();

                return false;
            });

            // Change Priority
            $('select[name="priority"]', this.$detail_form).off('change');
            $('select[name="priority"]', this.$detail_form).on('change', function(e) {
                $('input[name="_action"]', self.$detail_form).val('CHANGE_PRIORITY');

                $.alert.create({
                    message: 'Are you sure to change priority of this ticket?',
                    title: 'Confirm',
                    cancelButton: {
                        label: "No",
                        className: 'btn-default',
                        callback: function() {
                        }
                    },
                    actionButton: {
                        label: "Yes",
                        className: 'blue',
                        callback: function() {
                            self.$detail_form.submit();
                        }
                    }
                });
            });

            // Handler for changing assigner
            $('select[name="assignee"]', this.$detail_form).off('change');
            $('select[name="assignee"]', this.$detail_form).on('change', function(e) {
                $.alert.create({
                    message: 'Are you sure to change assignee of this ticket?',
                    title: 'Confirm',
                    cancelButton: {
                        label: "No",
                        className: 'btn-default',
                        callback: function() {
                        }
                    },
                    actionButton: {
                        label: "Yes",
                        className: 'blue',
                        callback: function() {
                            $('input[name="_action"]', self.$detail_form).val('CHANGE_ASSIGNEE');
                            self.$detail_form.submit();
                        }
                    }
                });
            });

            // Handler for Saving Memo
            $('.btn-save-memo', this.$detail_form).off('click');
            $('.btn-save-memo', this.$detail_form).on('click', function() {
                $('input[name="_action"]', self.$detail_form).val('SAVE_MEMO');
                self.$detail_form.submit();
            });

            $('.message.unread').off('click');
            $('.message.unread').on('click', function() {
                var url = $(this).data('url');
                var $self = $(this);

                $.ajax({
                    'url': url,
                    'type': 'post',
                    'dataType': 'json',
                    'blockUI': false,
                    success: function(data) {
                        $self.removeClass('unread');
                    }
                });

                return false;
            });

            // Handler for filtering by user role.
            $('select.select-change-filter', this.$comments_filter).off('change');
            $('select.select-change-filter', this.$comments_filter).on('change', function(e){
                $('input[name="_action"]', self.$comments_filter).val('CHANGE_COMMENT_FILTER');
                self.$comments_filter.submit();
            });

            this.$comments_form.off('submit');
            this.$comments_form.on('submit', function() {
                if (!$(this).valid())
                    return false;

                $(this).ajaxSubmit({
                    success: function(html) {
                        self.refresh(html);
                    }
                });

                return false;
            });

            // Solve ticket
            $('#btn-solve').off('click');
            $('#btn-solve').on('click', function() {
                var url = $(this).data('url');

                // if ticket is dispute, redirect dispute listing page.
                if (url != undefined) {
                    document.location.href = url;
                    return false;
                }

                $tickets = [];
                $tickets.push($('#ticket_id').val());
                fn.modal.setTicketIDs($tickets, "detail");
            });

            this.$detail_form.off('submit');
            this.$detail_form.on('submit', function() {
                self.$detail_form.ajaxSubmit({
                    success: function(html) {
                        self.refresh(html);
                    }
                });

                return false;
            });
        },

        render: function() {
            common.handleUniform();

            this.renderSelect2();

            Global.renderMessageBoard();
        },

        renderSelect2: function() {
            common.renderSelect2();
        },

        refresh: function(html) {
            var self = this;

            var $html = $(html);
            var $contents = $($.ajaxDatatable.selector(this.$container), $html);

            this.$container.html($contents.html());

            this.initElements();

            this.bindEvents();
            this.render();
        }
    }

    return fn;
});
define.amd = amd;