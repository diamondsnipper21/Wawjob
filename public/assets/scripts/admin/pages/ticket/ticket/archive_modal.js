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
            var validator = this.$form.validate();

            $('button.save-button').on('click', function() {
                if (!validator.valid())
                    return false;

                self.$container.modal('hide');

                self.$form.ajaxSubmit({
                    success: function(html) {
                        self.$container.modal('hide');

                        self.$container.trigger('bs.modal.success.close', [html]);  
                    }
                });

                return false;
            });
		},

		render: function() {
            common.renderSelect2();
		},
    }

	return fn;
});
define.amd = amd;