/**
 * @author PYH
 * @since July 30, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'ajax_datatable', 'bs-datepicker', 'bs-modalmanager', 'bs-modal'], function (common) {

    var fn = {
        init: function() {
            var self = this;

            this.initElements();
            this.bindEvents();
            this.render();

            this.modal.init();
        },

        initElements: function() {
            this.$container   = $('#disputes');
            this.$form        = $('form.form-datatable');
        },

        bindEvents: function() {
            var self = this;

            $('.button-determine').on('click', function() {
                var href = $(this).data('url');
                // create the backdrop and wait for next modal to be triggered
                // $('body').modalmanager('loading');
                fn.modal.open(href);

                return false;
            });
        },

        render: function() {
            common.initModal();

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
                    self.init();
                }
            });
        },

        modal: {

            init: function() {
                this.$modalContainer = $('#modal_determine_container');
            },

            bindEvents: function() {
                var self = this;

                $('#modal_determine').on('show', function() {
                    self.render();

                    setTimeout(function(){
                        $(window).trigger('resize');
                    }, 1500);
                });
            },

            render: function() {
                var self = this;

                this.$modalForm = $('.form-horizontal', self.$modal);
                this.$modalForm.validate();

                this.$modal.ajaxDatatable({
                    success: function(html) {
                        self.$modalForm = $('.form-horizontal', self.$modal);

                        var $alert_box = self.$modalForm.find('div.alert');
                        if ($alert_box.length == 0) {
                            self.$modal.modal('hide');

                            setTimeout(function() {
                                fn.$form.submit();
                            }, 1000);
                        } else {
                            self.init();
                            self.render();
                        }
                    }
                });

                Global.renderMaxlength();

                common.renderSelect2();
            },

            open: function(href) {
                var self = this;

                Global.blockUI();
                setTimeout(function(){
                    self.$modalContainer.load(href, '', function() {
                        self.$modal = $('#modal_determine', self.$modalContainer);
                        
                        self.bindEvents();

                        self.$modal.modal();
                    });
                }, 1000);
            }
        }
    };

    return fn;
});
define.amd = amd;