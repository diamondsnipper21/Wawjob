/**
 * @author KCG
 * @since July 4, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common'], function (common) {

	var fn = {
		init: function() {
            this.initElements();
            
			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$container = $('#modal_assign');
            this.$parent_form = $('form#ticket_list');
            this.$success   = $('.alert-success', this.$container);
            this.$error     = $('.alert-danger', this.$container);
            this.$confirm   = $('.alert-confirm', this.$container);
        },

		bindEvents: function() {
			var self = this;

            $('#assign-btn').on('click', function() {
                self.$container.modal('hide');
                self.$success.hide();
                self.$error.hide();

                $('input[name="_action"]', self.$parent_form).val('ASSIGN_TO');

                $assigner_id = $('#assigners', self.$container).val();
                $('input[name="assigner"]', self.$parent_form).val($assigner_id);

                self.$parent_form.submit();
            });
		},

		render: function() {
		},

        showConfirm: function() {
            this.$confirm.show();

            var $assigned = $('tbody input[type="checkbox"]:checked.assigned', self.$parent_form)[0];
            $assigned = $($assigned);

            var assigner_id = $('[data-assigner-id]', $assigned.closest('tr'));
            assigner_id = assigner_id.data('assigner-id');

            $('#assigners').val(assigner_id);
            $('#assigners').select2('val', assigner_id);
        },

        hideConfirm: function() {
            this.$confirm.hide();
        }
    }

	return fn;
});
define.amd = amd;