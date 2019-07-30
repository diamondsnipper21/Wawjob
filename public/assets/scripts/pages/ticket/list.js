/**
* list.js - Monitor / Overview
*/

define(['bootbox', 'ajax_page'], function (bootbox) {

	var fn = {
		
		$objSaveBtnCreate: null,
		$objCreateModal: null,

		$objCreateFrm: null,
		$objCreateModalFiles: null,
		$callType: null,

		$objListFrm: null,
		$postType: null,
		$postTicketId: null,

		$sortSel: null,
		$searchTitle: null,

		CONTENT_LENGTH: 5000,

		init: function () {

			//init create-modal
			this.$objCreateModal      = $('#createModal');
			this.$objCreateFrm        = this.$objCreateModal.find('form#createForm');
			this.$objSaveBtnCreate    = this.$objCreateModal.find('#saveBtn');

			this.$callType      = $('[name="callType"]', this.$objCreateFrm);

			this.$objListFrm    = $('form#ticketListForm');
			this.$postType      = $('[name="postType"]', this.$objListFrm);
			this.$postTicketId  = $('[name="postTicketId"]', this.$objListFrm);

			this.$sortSel  = $('#sortSel', this.$objListFrm);
			this.$searchTitle  = $('#search_title', this.$objListFrm);

			this.bindEvents();
			this.render();

			if ( trans.new == '1' ) {
				this.$objCreateModal.modal('show');
			}
		},

		//close ticket handler
		closeTicketHandler: function ($postType, $ticketId) {
			var self = this;

	      	//set data
	      	this.$postType.val($postType);
	      	this.$postTicketId.val($ticketId);

	      	var html = '<label>Are you sure to close this ticket?</label>';
					
			bootbox.dialog({
				title: '',
				message: html,
				buttons: {
					cancel: {
						label: trans.btn_no,
						className: 'btn-link',
						callback: function() {
						}
					},
					ok: {
						label: trans.btn_yes,
						className: 'btn-primary',
						callback: function() {
							self.$objListFrm.submit();
						}
					},					
				},
			});
  		},

		bindEvents: function() {
			var self = this;

			//ticket create
			this.$objSaveBtnCreate.off('click');
			this.$objSaveBtnCreate.on('click', function (event) {
				self.$objCreateFrm.submit();
				return false;
			});

			// close button click 
			$('ul.dropdown-menu').off('click');
			$('ul.dropdown-menu').on('click', function (e) {
				var $obj = $(e.target);
				if ($obj.hasClass('close-link')) {
					self.closeTicketHandler('close', $obj.data('id'));
				};
			});

			//sort-combo event-handler
			this.$sortSel.off('change');
			this.$sortSel.on('change', function () {
				self.$objListFrm.submit();
			});

			//search title handler
			this.$searchTitle.off('change');
			this.$searchTitle.on('change', function () {
				self.$objListFrm.submit();
			});
			
		},

		render: function() {
			var self = this;
			
			this.$objCreateFrm.validate();

            $('#tickets_page').ajaxPage({
                success: function(html) {
                    self.init();
                }
            });

            Global.renderFileInput();
            Global.renderMaxlength();
            Global.renderSelect2();
		},
	};

	return fn;
});