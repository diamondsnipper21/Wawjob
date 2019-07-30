/**
 * job/overview.js
 * @author Ro Un Nam
 * @since May 31, 2017
 */

define(['wjbuyer', 'jquery-form'], function (buyer) {
	var fn = {
		$modalJobTerm: null,
		$formJobTerm: null,

		init: function () {
			this.$modalJobTerm = $('#modalJobTerm');
			this.$formJobTerm = $('#formJobTerm');

			buyer.initJobsSelectLinkHandler();
			this.initAcceptTerms();
			this.validate();
			this.proposal.init();

			Global.renderUniform();

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

		proposal: {
			init: function() {
				this.bind();
			},

			bind: function() {
				$('.more-link').on('click', function() {
					$(this).closest('.description').addClass('expanded');
				});
			}
		}

	};

	return fn;
});