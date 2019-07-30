/**
 * job/interviews.js
 * @author Ro Un Nam
 * @since May 30, 2017
 */

define(['wjbuyer', 'stars', 'jquery-form'], function (buyer, stars) {
	var fn = {
		$section: null,
		$modalJobTerm: null,
		$formJobTerm: null,

		init: function () {
			this.$section = $('.proposals-section');
			this.$modalJobTerm = $('#modalJobTerm');
			this.$formJobTerm = $('#formJobTerm');

			buyer.initJobsSelectLinkHandler();

			this.initAcceptTerms();
			this.validate();

			this.proposal.init();
			this.filters.init();
			this.buttons.init();
			this.messages.init();

			Global.renderUniform();
			Global.renderSelect2();

			stars.init($('.score .stars'));

			// Set as read
			this.markAsRead();

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

		ajaxProcess: function(obj, data) {
			var loadUrl = fn.$section.data('url');

			$.post(loadUrl, data, function(json) {
				if ( json.success ) {
					if ( obj.hasClass('btn-submit-decline') ) {
						obj.prop('disabled', true);

						var $box = obj.closest('.box-decline');
						$box.css('display', 'none');
						$box.find('.overlay').remove();

						fn.proposal.updateTotalActive(false);
						fn.proposal.updateTotalArchived(true);

						obj.closest('.user-action').find('.btn-decline').addClass('disabled');
						obj.closest('.proposal-item').remove();

						fn.proposal.refresh();
					} else if ( obj.hasClass('btn-like') ) {
						if ( obj.hasClass('like') ) {
							obj.closest('.proposal-item').removeClass('disliked').addClass('liked');
							obj.removeClass('like').addClass('dislike');
						} else if ( obj.hasClass('dislike') ) {
							obj.closest('.proposal-item').removeClass('liked').addClass('disliked');
							obj.removeClass('dislike').addClass('like');

							if ( fn.filters.$show.val() == 'shortlisted' ) {
								obj.closest('.proposal-item').remove();
								fn.proposal.updateTotalActive(false);
							}
						}

						fn.proposal.refresh();
					} else if ( obj.hasClass('btn-archive') ) {
						obj.prop('disabled', true);

						obj.closest('.proposal-item').fadeOut(250, function() {
							$(this).remove();

							fn.proposal.updateTotalActive(false);
							fn.proposal.updateTotalArchived(true);

							fn.proposal.refresh();
						});

					} else if ( obj.hasClass('btn-unarchive') ) {
						obj.prop('disabled', true);

						obj.closest('.proposal-item').fadeOut(250, function() {

							$(this).remove();

							fn.proposal.updateTotalActive(true);
							fn.proposal.updateTotalArchived(false);

							fn.proposal.refresh();
						});
					}

					obj.closest('.proposal-item').find('.widgets .label-new').remove();
				} else {
					location.reload(true);
				}
			});
		},

		markAsRead: function() {
			var ids = [];
			$('.proposal-item', fn.$section).each(function() {
				if ( $('.label-new', $(this)).length ) {
					ids.push($(this).data('id'));
				}
			});

			if ( ids.length ) {
				var data = {
					ids: ids, 
					action: 'read',
					_token: fn.$section.data('token')
				};

				$.ajax({
					url: fn.$section.data('url'), 
					data: data,
					dataType: 'json',
					method: 'post',
					blockUI: false,
				}).done(function(json) {
					return false;
				});
			}
		},

		proposal: {
			$wrap: null,

			init: function() {
				this.$wrap = $('.proposals');

				this.bind();
			},

			bind: function() {
				$('.more-link').on('click', function() {
					$(this).closest('.user-cover-letter').addClass('expanded');
				});

				$('.less-link').on('click', function() {
					$(this).closest('.user-cover-letter').removeClass('expanded');
				});
			},

			updateTotalActive: function(type) {
				var total = parseInt($('.total-active').text());

				if ( type ) {
					$('.total-active').text(total + 1);
					$('.total-proposals').text(total + 1);
				} else {
					$('.total-active').text(Math.abs(total - 1));
					$('.total-proposals').text(Math.abs(total - 1));
				}
			},

			updateTotalArchived: function(type) {
				var total = parseInt($('.total-archived').text());

				if ( type ) {
					$('.total-archived').text(total + 1);
				} else {
					$('.total-archived').text(Math.abs(total - 1));
				}
			},

			refresh: function() {
				var total = parseInt($('.total-active').text());
				if ( this.$wrap.hasClass('archived-proposals') ) {
					total = parseInt($('.total-archived').text());
				}

				if ( total <= 0 ) {
					var message = trans.no_archived_proposals;
					if ( this.$wrap.hasClass('active-proposals') ) {
						message = trans.no_active_proposals;

						if ( fn.filters.$show.val() == 'shortlisted' ) {
							message = trans.no_shortlisted_proposals;
						} else if ( fn.filters.$show.val() == 'interviewing' ) {
							message = trans.no_interviews;
						}
					}

					var html = '<div class="not-found-result">';
							html += '<div class="row">';
								html += '<div class="col-md-12 text-center">';
									html += '<div class="heading">' + message + '</div>';
								html += '</div>';
							html += '</div>';
						html += '</div>';

					this.$wrap.html(html);
				}
			}
		},

		filters: {
			$proposals: null,
			$form: null,
			$show: null,
			$sort: null,

			init: function() {
				this.$proposals = $('.proposals');				
				this.$form = $('.form-filter');
				this.$show = $('#show');
				this.$sort = $('#sort', this.$form);

				this.bind();
			},

			bind: function() {
				this.$show.on('change', function() {
					var selected = $(this).val();

					fn.filters.generateUrl();
					
					/*
					var count = 0;
										
					$('.proposal-item').removeClass('hidden');

					if ( selected == 'shortlisted' ) {
						$('.proposal-item:not(.liked)').addClass('hidden');
						
						count = $('.proposal-item.liked').length;
					} else if ( selected == 'interviewing' ) {
						$('.proposal-item:not(.interviewing)').addClass('hidden');
						
						count = $('.proposal-item.interviewing').length;
					} else {
						$('.proposal-item.hidden').removeClass('hidden');
						
						count = $('.proposal-item').length;
					}

					if ( count > 0 ) {
						$('.not-found-result').addClass('hidden');
					} else {
						$('.not-found-result').removeClass('hidden');
					}
					*/
				});

				this.$sort.on('change', function() {
					fn.filters.generateUrl();

					//fn.filters.$form.submit();
				});
			},

			generateUrl: function() {
				var params = [];
				if ( fn.filters.$sort.val() != '' ) {
					params.push('sort=' + fn.filters.$sort.val());
				}
					
				if ( fn.filters.$show.val() != '' ) {
					params.push('show=' + fn.filters.$show.val());
				}

				if (params.length != 0)
					currentURL += '?' + params.join('&');

				window.location.href = currentURL;
			}
		},

		buttons: {
			$btnLike: null,
			$btnArchived: null,
			$btnUnarchived: null,
			$btnHire: null,
			
			$btnDecline: null,
			$btnCloseDecline: null,
			$btnCancelDecline: null,
			$btnSubmitDecline: null,
			$boxDecline: null,
			
			init: function() {
				this.$btnLike = $('.btn-like');
				this.$btnArchived = $('.btn-archive');
				this.$btnUnarchived = $('.btn-unarchive');
				this.$btnHire = $('.btn-hire');

				this.$btnDecline = $('.btn-decline');
				this.$boxDecline = $('.box-decline');
				this.$btnCloseDecline = $('.close', this.$boxDecline);
				this.$btnCancelDecline = $('.btn-cancel-decline', this.$boxDecline);
				this.$btnSubmitDecline = $('.btn-submit-decline', this.$boxDecline);

				this.bind();
			},

			bind: function() {
				$('[data-toggle="tooltip"]').tooltip();
				
				this.$btnLike.on('click', fn.buttons.doAction);
				this.$btnArchived.on('click', fn.buttons.doAction);
				this.$btnUnarchived.on('click', fn.buttons.doAction);
				this.$btnDecline.on('click', fn.buttons.showDecline);
				this.$btnSubmitDecline.on('click', fn.buttons.submitDecline);
				this.$btnCloseDecline.on('click', fn.buttons.closeDecline);
				this.$btnCancelDecline.on('click', fn.buttons.closeDecline);

				$('body').on('click', function(e) {
					var $obj = $(e.target);
					if ( $obj.hasClass('btn-decline') || $obj.parent().hasClass('btn-decline') || $obj.closest('.box-decline').length ) {
						return;
					}

					fn.buttons.$boxDecline.css('display', 'none');
				});
			},

			doAction: function(e) {
				var $this = $(e.target);
				if ( $this.prop('tagName') == 'I' ) {
					$this = $this.parent();
				}

				var action = '';
				if ( $this.hasClass('btn-like') ) {
					if ( $this.hasClass('like') ) {
						action = 'like';
					} else {
						action = 'dislike';
					}
				} else if ( $this.hasClass('btn-archive') ) {
					action = 'archive';
				} else if ( $this.hasClass('btn-unarchive') ) {
					action = 'unarchive';
				}

				var id = $this.closest('.proposal-item').data('id');
				var token = fn.$section.data('token');

				var data = {
					id: id, 
					action: action,
					_token: token
				};			

				fn.ajaxProcess($this, data);
			},

			showDecline: function(e) {
				var $this = $(e.target);
				if ( $this.hasClass('fa') ) {
					$this = $this.parent();
				}

				fn.buttons.$boxDecline.css('display', 'none');
				$this.closest('.user-action').find('.box-decline').css('display', 'block');
			},

			closeDecline: function(e) {
				var $box = $(this).closest('.box-decline');
				$box.find('.box-ctrl').removeClass('has-error').val('');
				$box.css('display', 'none');
			},

			submitDecline: function(e) {
				var $this = $(this);
				var $box = $this.closest('.box-decline');

				$box.find('.box-message').prepend('<div class="overlay"></div>');

				var id = $this.closest('.proposal-item').data('id');
				var decline_message = $box.find('textarea').val();
				var reason = $box.find('[name=reason]:checked').val();
				var token = fn.$section.data('token');
				var action = 'decline';

				var data = {
					id: id, 
					reason: reason,
					decline_message: decline_message,
					action: action,
					_token: token
				};			

				fn.ajaxProcess($this, data);
			}
		},

		messages: {
			$btnShowMessages: null,
			$modal: null,
			$modalHeader: null,
			$userInfo: null,
			$messagesList: null,
			$message: null,
			$btnSubmitMessage: null,
			$form: null,
			$files: null,
			$attachments: null,
			$uploadedFiles: null,
			$action: null,

			init: function() {
				this.$btnShowMessages = $('.btn-send-message');
				this.$modal = $('#messagesModal');
				this.$modalHeader = $('.modal-header', this.$modal);
				this.$userInfo = $('.user-info', this.$modalHeader);
				this.$messagesList = $('.messages-list', this.$modal);
				this.$message = $('textarea', this.$modal);
				this.$files = $('#files', this.$modal);
				this.$attachments = $('.attachments', this.$modal);
				this.$uploadedFiles = $('#uploaded_files', this.$modal);
				this.$form = $('form', this.$modal);
				this.$action = $('[name="action"]', this.$modal);
				this.$btnSubmitMessage = $('.btn-submit-message', this.$modal);

				this.bindEvents();
				this.render();
			},

			render: function() {
				this.$form.validate();
				
				Global.renderFileInput();

	            Global.renderMaxlength();
			},

			bindEvents: function() {
				this.$modal.on('show.bs.modal', function (e) {
					var $btn = $(e.relatedTarget);
					
					var userId = $btn.data('user');
					var userName = $btn.data('user-name');
					var userTitle = $btn.data('user-title');
					var userUrl = $btn.data('user-url');
					var userAvatar = $btn.data('user-avatar');

					var proposalId = $btn.data('proposal');
					var projectId = $btn.data('project');

					fn.messages.$modal.find('[name="id"]').val(proposalId);

					var userHtml = '<div class="avatar pull-left">';
					userHtml += '<a href="' + userUrl + '">';
					userHtml += '<img alt="' + userName + '" class="img-circle pull-left" src="' + userAvatar + '" width="50" height="50">';
					userHtml += '</a></div>';
					userHtml += '<div class="info pull-left">';
					userHtml += '<h4><a href="' + userUrl + '">' + userName + '</a>';
					if ( userTitle ) {
						userHtml += '<span class="mt-1">' + userTitle + '</span>';
					}
					userHtml += '</h4></div>';
					userHtml += '</div>';
					fn.messages.$userInfo.html(userHtml);
				});

				this.$modal.on('show.bs.modal', function (e) {
					fn.messages.$message.val('').trigger('change');
					fn.messages.$files.val('');
					fn.messages.$message.parent().removeClass('has-error');
				});

				fn.messages.$message.on('keydown', function(e) {
					if (e.ctrlKey && e.keyCode == 13) {
						fn.messages.$btnSubmitMessage.trigger('click');
					}
				});

				this.$btnShowMessages.on('click', function(e) {
					var $obj = $(e.target);
					$obj.closest('.proposal-item').find('.widgets .label-new').remove();
				});

				this.$btnSubmitMessage.on('click', fn.messages.sendMessage);
			},

			moveToBottom: function() {
				var scrollHeight = fn.messages.$messagesList.get(0).scrollHeight;
				fn.messages.$messagesList.animate({scrollTop: scrollHeight}, 500);
			},

			sendMessage: function(e) {
				var $this = $(this);
				var $box = $this.closest('.box-message');
				var $form = fn.messages.$form;
				var proposalId = $('input[name="id"]', $form).val();

				$form.find('.has-error').removeClass('has-error');
				if ( fn.messages.$message.val().trim() == '' ) {
					fn.messages.$message.closest('.box-ctrl').addClass('has-error');
					return false;
				}

				fn.messages.$action.val('send_message');

				$form.ajaxSubmit({
					success: function(json) {
						fn.messages.$message.val('').trigger('change');
						fn.messages.$modal.modal('hide');
						// $('.btn-send-message[data-proposal="' + proposalId + '"]').prop('href', '/messages/' + json.message_thread_id).prop('target', '_blank').text(trans.send_message).removeAttr('data-toggle').removeAttr('data-target').removeAttr('data-json');
						fn.filters.generateUrl();
					},
					error: function(xhr) {
						console.log(xhr);
					},

					dataType: 'json',
				});
			}
		}

	};

	return fn;
});