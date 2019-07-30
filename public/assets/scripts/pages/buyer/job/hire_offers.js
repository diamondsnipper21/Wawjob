/**
 * job/hire_offers.js
 * @author Ro Un Nam
 * @since May 31, 2017
 */

define(['wjbuyer', 'jquery-form'], function (buyer) {
	var fn = {
		$section: null,
		$modalJobTerm: null,
		$formJobTerm: null,

		init: function () {
			this.$section = $('.contracts-section');
			this.$modalJobTerm = $('#modalJobTerm');
			this.$formJobTerm = $('#formJobTerm');

			buyer.initJobsSelectLinkHandler();
			this.initAcceptTerms();
			this.validate();
			this.buttons.init();

			Global.renderUniform();
			Global.renderSelect2();

			$('[data-toggle="tooltip"]').tooltip();
		},

		initAcceptTerms: function() {
			if ( this.$modalJobTerm.length > 0 ) {
				this.$modalJobTerm.modal({
					show: true,
					keyboard: false,
					backdrop: 'static'
				});
			}			
		},

		validate: function() {
			this.$formJobTerm.validate();
		},

		buttons: {
			$btnWithdraw: null,
			$btnCancelWithdraw: null,
			$btnCloseWithdraw: null,
			$btnSubmitWithdraw: null,
			$boxWithdraw: null,
			
			init: function() {				
				this.$boxWithdraw = $('.box-withdraw');
				this.$btnWithdraw = $('.btn-withdraw');
				this.$btnCloseWithdraw = $('.close', this.$boxWithdraw);
				this.$btnSubmitWithdraw = $('.btn-submit-withdraw', this.$boxWithdraw);
				this.$btnCancelWithdraw = $('.btn-cancel-withdraw', this.$boxWithdraw);

				this.bind();

				Global.renderMaxlength();
			},

			bind: function() {
				this.$btnWithdraw.on('click', fn.buttons.showWithdraw);
				this.$btnSubmitWithdraw.on('click', fn.buttons.submitWithdraw);
				this.$btnCloseWithdraw.on('click', fn.buttons.closeWithdraw);
				this.$btnCancelWithdraw.on('click', fn.buttons.closeWithdraw);

				$('body').on('click', function(e) {
					var $obj = $(e.target);
					if ( $obj.hasClass('btn-withdraw') || $obj.closest('.box-withdraw').length ) {
						return;
					}

					fn.buttons.$boxWithdraw.find('textarea').val('');
					fn.buttons.$boxWithdraw.css('display', 'none');
				});
			},

			showWithdraw: function(e) {
				var $this = $(e.target);
				fn.buttons.$boxWithdraw.css('display', 'none');
				$this.closest('.user-action').find('.box-withdraw').css('display', 'block');
			},

			closeWithdraw: function(e) {
				var $box = $(this).closest('.box-withdraw');
				$box.find('textarea').val('');
				$box.css('display', 'none');
			},

			submitWithdraw: function(e) {
				var $this = $(this);
				var obj = $(e.target);
				var $box = $this.closest('.box-withdraw');
				var $form = $this.closest('form');

				$box.find('.box-message').prepend('<div class="overlay"></div>');

				$form.ajaxSubmit({
					success: function(json) {
						if ( json.success ) {
							location.reload(true);
						}

						$box.find('.overlay').remove();
					},
					error: function(xhr) {
						$box.find('.overlay').remove();
						console.log(xhr);
					},

					dataType: 'json',
				});
			}
		},

	};

	return fn;
});