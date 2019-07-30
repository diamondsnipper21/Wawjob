/**
 * list.js - Admin / User/list
 */

define(['notify', 'plugin'], function (notify, plugin) {

	var fn = {	
		
		list: {

			$wrapper: null,
			$list: null,

			init: function() {

				setTimeout(function(){if (nt2show) notify.success(nt2show);}, 0);
				setTimeout(function(){if (error2show) notify.error(error2show);}, 0);

				fn.list.$wrapper = $('.user-list-wrap');
				fn.list.$list = $('ul.list', fn.list.wrapper);

				fn.list.$list.on('click', '.collapse-toggler', fn.list.toggleDetails);
			},

			toggleDetails: function (event) {
		      event.preventDefault();


		      var $this = $(this),
		        $li = $($this.parents('li').get(0)),
		        $detail_body = $li.find('.item-body');

		      // $detail_body.collapse('toggle');

		      if ($this.is('.open')) {
		      	
		      	$detail_body.slideUp();
		        // $('.for-collapse', $li).css('visibility', 'visible');
		        $('.for-collapse', $li).css({'opacity':1});

		      } else {
		      	
		      	$('li.open', $li.parent()).each(function(i, o){
		      		if ($(o).find('.collapse-toggler').hasClass('open')) {
		      			$(o).find('.collapse-toggler').trigger('click');
		      		}
		      	});
		      	
		      	$detail_body.slideDown();
		        // $('.for-collapse', $li).css('visibility', 'hidden');
		        $('.for-collapse', $li).css({'opacity':0});
		      }

		      $this.toggleClass('open');
		      $li.toggleClass('open');
		    }
		},

		modal: {
			$modalEdit: null,

			init: function() {
				fn.modal.$modalEdit = $("#modalConfirm4Action");	
				fn.modal.initDialog();
				fn.modal.init4Modal();
			},

			initDialog: function() {
		        fn.modal.$modalEdit.on("show.bs.modal", function(e) {
		          $('.notice-block', fn.modal.$modalEdit).hide().html('');

		          $('#mca_text', fn.modal.$modalEdit).val('');

		          var action_type = $('#mca_action', fn.modal.$modalEdit).val();
		          fn.getLabelbyType(action_type, 2);

		        }).on("click", ".btn-save", function() {
		        	
		        	var user_id = $('#mca_user_id', fn.modal.$modalEdit).val();
		        	var action_type = $('#mca_action', fn.modal.$modalEdit).val();
		        	var action_type_value = $('#mca_action_value', fn.modal.$modalEdit).val();
		        	var textv = $('#mca_text', fn.modal.$modalEdit).val();

		        	var data = {};
		        	data['action_type'] = action_type;
	            	data['user_id'] = user_id;
	            	data['action_type_value'] = action_type_value;
	            	data['msg'] = textv;

		        	fn.modal.ajaxAction(data, function(){fn.modal.$modalEdit.modal("hide");});
		        	
		        }).on("click", ".btn-cancel", function() {
		          	fn.modal.$modalEdit.modal("hide");
		        });
			},

			init4Modal: function() {

		        $(".user-item", fn.list.$list).on("click", ".do-ajaxaction", function() {

		        	var $li = $(this).parents('.dd-item');
		        	var user_id = $li.attr('data-id');
		        	var action_type = $(this).attr('ref');
		        	var action_type_value = $(this).attr('refv');

		        	if (!action_type_value) action_type_value = '';

		        	$('.modal-title .entity', fn.modal.$modalEdit).html(fn.getLabelbyType(action_type, 0));
		        	$('.btn-save', fn.modal.$modalEdit).html(fn.getLabelbyType(action_type, 1));
		        	$('#mca_user_id', fn.modal.$modalEdit).val(user_id);
		        	$('#mca_action', fn.modal.$modalEdit).val(action_type);
		        	$('#mca_action_value', fn.modal.$modalEdit).val(action_type_value);

		        	fn.modal.$modalEdit.modal();
		        	return;
		          
		        });
		    },

		    showNotice: function(msg, type) {
		    	var className = 'alert-danger';
		    	if (type == 'info') {
		    		className = 'alert-success';
		    	}
		    	$('.notice-block', fn.modal.$modalEdit).hide().html('');
	    		var $error_div = $('<div class="alert ' + className + '"></div>');
	    		$error_div.html(msg).appendTo($('.notice-block', fn.modal.$modalEdit));
	    		$('.notice-block', fn.modal.$modalEdit).stop(true, true).slideDown();
		    },

		    ajaxAction: function(data, success_callback, error_callback) {

		    	if (data['msg'].trim() == '') {
		    		// notify.error(fn.getLabelbyType(data['action_type'], 3));
		    		fn.modal.showNotice(fn.getLabelbyType(data['action_type'], 3), 'error');
		    		return;
		    	}

		    	$.post('/admin/user/ajax_action', {
	            	s: data['action_type'],
	            	i: data['user_id'],
	            	sv: data['action_type_value'],
	            	m: data['msg']
	          	}, function(json) {

	            	if (!json.success) {
	              		notify.error(json.msg);
	              		if (error_callback) error_callback.call();
	              		return false;
	            	}

	            	notify.success(json.msg);
	            	if (success_callback) success_callback.call();

	            	if (json.s == 'message') {
	            		// message sent, then do something else here
	            	} else if (json.s == 'status') {

		            	var $li = $('#user_' + json.i);
		            	var $status = $('.user-state', $li);
		            	var $dropdown = $('.do-more', $li);

		            	for (var i=0; i<10; i++) {
		            		$status.removeClass('state-' + i);
		            		$dropdown.removeClass('for-status-' + i);
		            	}
		            	$status.addClass('state-' + json.sv).html(json.svl);
		            	$dropdown.addClass('for-status-' + json.sv);
	            	}
	          	});
		    }

		},
		
	    getLabelbyType: function(type, def) {
	    	return ajaxActionLabels[type][def];
	    },

		init: function () {
			fn.list.init();
			fn.modal.init();


			if ($('#notify_wrapper').html().trim() != '') {
		      setTimeout(function(){ $('#notify_wrapper').slideUp(); }, 2000);
		    }
		}

	};

	return fn;
});