/**
 * @author KCG
 * @since Feb 22, 2018
 */

define(['ajax_page', 'jquery-slimscroll'], function () {	

 	var fn = {
 		init: function() {
 			this.$container = $('#message_threads_container');
 			this.$form 		= $('form:eq(0)', this.$container);

 			this.initVars();

 			this.bindEvents();
 			this.render();
 		},

 		initVars: function() {
 			this.tab 	 = $('.tabs .tab a.active').data('id');
 		},

 		bindEvents: function() {
 			var self = this;

 			window.onpopstate = function(e){
                if(e.state) {
                    var html        = e.state.html;
                    var $container  = $('#' + e.state.content_id);
                    var thread_id   = e.state.thread_id;
                    var page_title  = e.state.page_title;

                    $.ajaxPage.replaceHTML($container, html);
                    document.title = page_title;

                    $('.thread[data-id="' + thread_id + '"]').trigger('click', [true]);

                    self.init();
                }
            };

 			this.$form.off('keyup keypress');
 			this.$form.on('keyup keypress', function(e) {
 				if (e.keyCode === 13) {
 					$('[name="action"]').val('SEARCH');
 				}
			});

			$('.message-threads .input-group-addon').off('click');
			$('.message-threads .input-group-addon').on('click', function() {
				$('[name="action"]').val('SEARCH');
				self.$form.submit();
			});

 			// Handler when clicking each thread
 			$('.thread:not(.active)').off('click');
 			$('.thread:not(.active)').on('click', function(e, force) {
 				var thread_id = $(this).data('id');
 				var $form = $('#messages_container form:eq(0)');
 				var url = $(this).data('url');
 				var $thread = $('.thread[data-id="' + thread_id + '"]');

 				$('[name="thread_id"]').val(thread_id);

 				$('.thread.active').removeClass('active');
 				$thread.addClass('active');

 				if (typeof force != 'undefined')
 					return false;

 				// if ($(this).hasClass('new-message')) {
 				if ($(this).closest('.threads').attr('id').indexOf('unread') >= 0)
 					$form = self.$form;

 				$('[name="action"]').val('LOAD_THREAD');

 				$('body').data('change_url', true);

	 			var count = $('.thread-unreads', $thread).html();
	 			self.markedAsRead(count);

	 			$form.data('container', '#messages_container');
 				$form.attr('action', url);
 				$form.submit();

 				return false;
 			});

 			// Infinite Scroll
 			$('.threads .tab-content').off('scroll');
 			$('.threads .tab-content').on('scroll', function() {
		        if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
		        	$(document).data('block-ui-custom', $('.loading', $(this)));

		            $('form', $(this)).submit();
		        }
		    });

 			// Handler after submitting form on ajax page
 			$('body').off('ajaxPage.success');
		    $('body').on('ajaxPage.success', function(e, $container, $form, html) {
		    	self.init();

		    	if ($('body').data('change_url')) {
		    		var $thread = $('.thread.active');

		    		window.history.pushState({
	                        'html'      : html,
	                        'content_id': $container.attr('id'),
	                        'thread_id': $thread.data('id'),
	                        'page_title': $(document).find('title').text()
	                    }, "", $thread.data('url')
	                );

	                $('body').data('change_url', null);
		    	}
		    });

		    // Handler after sending message
		    $('body').on('ajaxPage.success', function(e, $container, $form, data) {
				if (typeof data != 'object')
					return true;

				$('#scroll-panel').append(data.message_row);
				$('.attachments.slim-scroll').html(data.attachments_html);

				// Initialize form content.
				var $form = $('.send-message-form form');
				$('textarea', $form).val('');
				$('.attachments', $form).html('');

		    	self.adjustColumnHeight();
		    	self.renderSlimScroll();
		    });

		    // Tab: Inbox, Unread, Archived and All
		    $('.tabs .tab a').off('click');
		    $('.tabs .tab a').on('click', function() {
		    	if (!$(this).hasClass('active')) {
		    		$('[name="tab"]').val($(this).attr('href').replace('#threads_', ''));

		    		$('.threads').removeClass('active');
		    		$($(this).attr('href')).addClass('active');

		    		$('.tabs .tab a').removeClass('active');
		    		$(this).addClass('active');

		    		self.renderSlimScroll();
		    	}

		    	return false;
		    });

		    // Handler when clicking "Archive"
		    $('.tools .archive').off('click');
		    $('.tools .archive').on('click', function() {
		    	var $thread = $(this).closest('.thread');

		    	$('[name="action"]').val('ARCHIVE');
		    	$('[name="thread_id"]').val($thread.data('id'));

		    	self.$form.submit();
		    });

		    // Handler when clicking "Move to Inbox"
		    $('.tools .move-to-inbox').off('click');
		    $('.tools .move-to-inbox').on('click', function() {
		    	var $thread = $(this).closest('.thread');

		    	$('[name="action"]').val('MOVE_TO_INBOX');
 				// $('[name="tab"]').val('inbox');
		    	$('[name="thread_id"]').val($thread.data('id'));

		    	self.$form.submit();
		    });

		    // Handler when uploading file
		    $('body').off('fileinput.delete.file, fileinput.delete.file');
		    $('body').on('fileinput.delete.file, fileinput.delete.file', function() {
		    	self.adjustColumnHeight();
		    });

		    // Handler when resizing window
		    $(window).on('resize', function() {
		    	self.adjustColumnHeight();
		    });
 		},

 		render: function() {
 			var self = this;
 			var $thread = $('.thread.active');
 			var thread_id = $thread.data('id');

 			this.$container.ajaxPage({
 				success: function() {
 				}
 			});

 			$('.threads').ajaxPage({
 				success: function() {
 				}
 			});

 			$('#messages_container').ajaxPage({
 				success: function(data) {
 				}
 			});

 			// Adjust heightes for three columns such as senders, messages and attachments
 			this.adjustColumnHeight();
 			
 			Global.renderMessageBoard();

 			// Select actived thread in other tabs.
 			var $thread = $('.thread.thread-' + thread_id);
 			$('.thread').removeClass('active');
 			$thread.addClass('active');

 			// Marked As Read for active thread
 			var count = $('.thread-unreads', $thread).html();
 			this.markedAsRead(count);

 			// Remove thread on unreads tab
 			$('#threads_unread .thread.thread-' + thread_id).remove();
 			if ($('#threads_unread .thread').length == 0)
 				$('#threads_unread .no-threads').removeClass('hide');

 			$(window).on('resize', function() {
 				self.adjustColumnHeight();
 			});
		    
		    window.setInterval(function() {
		    	self.adjustColumnHeight();
		    }, 500);

		    // All form actions will be changed to "/messages" because of redirecting from "/messages/id"
		    $('form', this.$container).each(function() {
		    	var action = $(this).attr('action');

		    	if (action == '')
		    		action = message_base_url;

		    	$(this).attr('action', action);
		    });
 		},

 		renderSlimScroll: function() {
 			$('.slim-scroll').each(function() {
 				$(this).slimScroll({
			  	      height: $(this).height()
			    });
 			});
 		},

 		initFormVariables: function($form) {
 			$('input[type="hidden"]', $form).each(function() {
 				var name  = $(this).attr('name');
 				var value = $(this).attr('value');

 				$('[name="' + name + '"]').val(value);
 			});
 		},

 		adjustColumnHeight: function() {
 			var $header 		= $('.header-wrapper');
 			var $footer 		= $('.page-footer');
 			var $page 			= $('.page-wrapper');

 			var $cThreads 		= $('.message-threads-inner');
 			var $cMessages 		= $('#messages');
 			var $cAttachments 	= $('#attachments .inner');

 			var contentH 		= $(window).height() - $header.height();
 			
 			contentH = Math.max(contentH, 500);
 			// Page Content will changed according to window size.
 			$page.height(contentH);
 			contentH = $page.height();

 			var adjust_offset = 35;

 			var height = 0;
 			var changed = false;

 			// Threads Panel
 			height = contentH
 				- $('.message-threads-inner form').outerHeight()
 				- $('.message-threads-inner .tabs').outerHeight()
 				- 5 - 15 - 15 - 2
 				- 14
 				+ adjust_offset;
 			if (height != $('.threads .slim-scroll').height())
 				changed = true;
 			$('.threads .slim-scroll').height(height);

 			// Messages Panel
 			height = contentH
 				- $('.thread-short-info').outerHeight() 
 				- $('.send-message-form').outerHeight()
 				- 5 - 15 - 15 - 2
 				- 0
 				+ adjust_offset;

 			if ($('.message-list .scrollspy-panel').length != 0) {
 				height -= 44;
	 			if (height != $('.message-list .scrollspy-panel').height())
	 				changed = true;
	 			$('.message-list .scrollspy-panel').height(height);
 			} else {
 				height -= 175;
 				if (height != $('.message-list .not-found-result').height())
	 				changed = true;
	 			$('.message-list .not-found-result').height(height);
 			}

 			// Attachments Panel
 			height = contentH
 				- $('.thread-short-info').outerHeight()
 				- $('.application').outerHeight()
 				- $('.attachments-title').outerHeight()
 				- 5 - 15 - 15 - 2
 				- 39
 				+ adjust_offset;
 			if (height != $('#attachments .attachments.slim-scroll').height())
 				changed = true;
 			$('#attachments .attachments.slim-scroll').height(height);

 			if (changed)
	 			this.renderSlimScroll();
 		},

 		markedAsRead: function(count) {
 			var $thread = $('.thread.active');
 			var thread_id = $thread.data('id');

 			if (thread_id && this.tab != 'unread') {
 				var totals = $('.msg-notification').html();

				if (!count)
					count = 0;

				if (totals - count <= 0)
					$('.msg-notification').addClass('hide');
				else
					$('.msg-notification').html(totals - count);
				
				$thread.removeClass('new-message');
				$('.thread-unreads', $thread).html(0);
				$('.thread-unreads', $thread).addClass('hide');
 			}
 		}
	};

	return fn;
});