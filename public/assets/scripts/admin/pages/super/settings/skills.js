/**
 * @author KCG
 * @since April 4, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'alert', 'ajax_datatable', 'bs-modalmanager', 'bs-modal', 'jquery-form'], function (common) {

    var fn = {
        init: function() {
            var self = this;

            this.initElements();

            this.modal.init();
            
            this.bindEvents();
            this.render();
        },

        initElements: function() {
            this.$container = $('#skills');
            this.$form      = $('form.form-datatable');
        },

        bindEvents: function() {
            var self = this;
            // 
            $('.edit-modal-link').on('click', function() {
                var url = $(this).data('url');

                self.modal.open(url);

                return false;
            });
        },

        render: function() {

            common.initModal();

            this.renderDataTable();
            this.renderSelect2();
            this.renderAlert();

            common.handleUniform();
        },

        renderSelect2: function() {
            $('select.select2').select2({
                minimumResultsForSearch: -1
            });
        },

        renderDataTable: function() {
            var self = this;
            this.$container.ajaxDatatable({
                success: function(html) {
                    self.init();
                }
            });
        },

        renderAlert: function() {
            var self = this;
            $('.button-submit').alert({
                message: 'Are you sure to delete these skills?',
                title: 'Delete Skills',
                cancelButton: {
                    label: "Cancel",
                    className: 'btn-default',
                    callback: function() {
                    }
                },
                actionButton: {
                    label: "Delete",
                    className: 'blue',
                    callback: function() {
                        $('input[name="_action"]').val('DELETE');

                        self.$form.submit();
                    }
                }
            });         
        },

        modal: {
            init: function() {
                this.$modalContainer = $('#modal_skill_page_container');
            },

            bindEvents: function() {
                var self = this;

                $('#modal_skill_page').on('show', function() {
                    self.render();

                    setTimeout(function(){
                        $(window).trigger('resize');
                    }, 1500);
                });
            },

            render: function() {
                var self = this;

                Global.renderMaxlength();

                var $form      = $('form', this.$modal);

                $form.validate();
                $.validator.messages.remote= 'Duplicated name of skill.';

                this.$modal.ajaxDatatable({
                    success: function(html) {
                        self.init();
                    }
                });
            },

            open: function(url) {
                var self = this;

                Global.blockUI();
                setTimeout(function(){
                    self.$modalContainer.load(url, '', function() {
                        self.$modal = $('#modal_skill_page', self.$modalContainer);
                        
                        self.bindEvents();

                        self.$modal.modal();
                    });
                }, 1000);
            },

            close: function() {
                var $modal = this.$container;
                $modal.modal('hide');
            }
        }
    };

    return fn;
});
define.amd = amd;