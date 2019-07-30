define(['scripts/pages/freelancer/user/my_profile'], function (profile) {

  	var fn = {
		init: function () {
			this.$form = $('.profile-page form:eq(0)');

		  	profile.init();

		  	this.bindEvents();
		  	this.render();
		},

		bindEvents: function() {
			var self = this;

			$('.btn-next').on('click', function() {
				self.$form.data('no-ajax', true);
				
				self.$form.submit();
			});

			$('.btn-back').on('click', function() {
				$('[name="_action"]', self.$form).val('back');
				self.$form.data('no-ajax', true);

				self.$form.submit();
			});

			$('body').on('ajaxPage.success', function() {
				self.init();
			});
		},

		render: function() {
			var $form = $('.profile-page form:eq(0)');
			var collection_var_name = $('.profile-page > .page-content').attr('id');
			var var_name = $('.profile-page > .page-content').data('var');

			$form.append('<input type="hidden" name="var_name" value="' + var_name + '" />');
			$form.append('<input type="hidden" name="collection_var_name" value="' + collection_var_name + '" />');
			
			$('a.remove-item-action').attr('href', delete_item_url);
		}
	};

  	return fn;
});