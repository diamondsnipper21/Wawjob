/**
 * @author KCG
 * @since July 4, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'bs-modalmanager', 'bs-modal', 'jquery-form', 'moment'], function (common) {

	var fn = {
		init: function() {
            this.initElements();
            
			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$container = $('#modal_archive');
            this.$form      = $('form', this.$container);
        },

		bindEvents: function() {
			var self = this;

            $('body').off('submit', '#modal_archive form');
            $('body').on('submit', '#modal_archive form', function() {
                self.$form.ajaxSubmit({
                    success: function(html) {
                        self.$container.modal('hide');
                        self.$container.trigger('bs.modal.success.close', [html]);
                    }
                });

                return false;
            });

            $('body').off('show', '#modal_archive');
            $('body').on('show', '#modal_archive', function() {
                self.init();

                common.renderSelect2()
            });
		},

		render: function() {
            this.$form.validate();
		},

        setTicketIDs: function(tickets, source) {
            for($i = 0; $i < tickets.length; $i++) {
                $('input[name="ticket_id[]"]:eq(' + $i + ')').val(tickets[$i]);
            }

            $('input[name="return_page"]').val(source);
        },
    }

	return fn;
});
define.amd = amd;