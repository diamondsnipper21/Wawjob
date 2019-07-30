/**
 * @author KCG
 * @since July 9, 2017
 * User List
 */'bs-datepicker', 'moment'

define(['common', 'ajax_datatable', 'bs-datepicker', 'bs-modalmanager', 'bs-modal'], function (common) {

	var fn = {
		init: function() {
            this.initElements();

            this.modal.init();

			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$container = $('#faq_list');
            this.$form      = $('form', this.$container);
        },

        bindEvents: function() {

            var self = this;

            // Handler when deleting faq
            $(this.$container).on('click', 'button.button-submit', function() {
                $('input[name="_action"]', self.$form).val('DELETE');
                self.$form.submit();
            });

            //
            $('.edit-modal-link').on('click', function() {
                var url = $(this).data('url');
                // $('body').modalmanager('loading');

                self.modal.open(url);
                return false;
            });
        },

        render: function() {

            common.initModal();

            this.renderSelect2();
            this.renderDataTable();

            common.handleUniform();
        },

        renderSelect2: function() {
            common.renderSelect2();
        },

        renderDataTable: function() {
            var self = this;
            self.$container.ajaxDatatable({
                success: function(html) {
                    self.init();
                }
            });
        },

        modal: {
            init: function() {
                this.$container = $('#modal_faq');

                this.bindEvents();
                this.render();
            },

            bindEvents: function() {
                var self = this;

                this.$container.off('show');
                this.$container.on('show', function() {
                    self.init();
                });

                $('.save-button', this.$container).on('click', function(){
                });
            },

            render: function() {
                var self = this;

                Global.renderMaxlength();

                common.renderSelect2();
                    
                var $form      = $('form', this.$container);

                $form.validate();

                $.validator.messages.remote= 'Duplicated name of Faq.';

                this.$container.ajaxDatatable({
                    success: function(html) {
                        self.render();

                        $('.save-button', self.$container).attr('disabled', true);

                        window.setTimeout(function() {
                            self.close();
                            fn.$form.submit();
                        }, 2000);
                    }
                });
            },

            open: function(url) {
                var $modal = this.$container;

                setTimeout(function(){
                    $modal.load(url, '', function() {
                        $modal.modal();
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