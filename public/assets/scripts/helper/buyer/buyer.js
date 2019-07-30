define(['bootbox', 'common'], function (bootbox, common) {
	var fn = {
		initJobsSelectLinkHandler: function() {
			$('.page-content .job-action .dropdown-menu a').off('click');
			$('.page-content .job-action .dropdown-menu a').on('click', function(e) {
	        	var $this = $(this);
	        	var status = $this.data('status');

	        	if ( status == 'view' || status == 'edit' ) {
	        		return true;
	        	} else if ( status == 'public' || status == 'protected' || status == 'private' ) {
	        		fn.job_change_public($this);
	        	} else if ( status == 'close' || status == 'cancel' || status == 'delete' ) {
	        		fn.job_change_status($this);
	        	}
	        });
		},

		job_change_public: function(obj) {
			var _url = obj.data('url');
			var _status = obj.data('status');

			var _msg = "<div class='buyer-job-confirm-message'>" + t(trans.change_public, {'status':trans.status[_status]}) + "</div>";
			
			bootbox.dialog({
				title: '',
				message: _msg,
				buttons: {					
					cancel: {
						label: trans.btn_cancel,
						className: 'btn-link',
						callback: function() {
						}
					},
					ok: {
						label: trans.btn_ok,
						className: 'btn-primary',
						callback: function() {
							$.ajax({
								url: _url,
								type: 'POST',
								data:{},
								success: function(json) {
									if ( json.success ) {
										location.reload(true);
									}
								}
							});
						}
					},
				},
			});
		}, 

		job_change_status: function(obj) {
			var _url = obj.data('url');
			var _status = obj.data('status');
			var _draft = obj.data('draft');

			var _msg = '';

			if ( _status == 'close' ) {
				_msg = trans.close_job;
			} else if ( _status == 'cancel' ) {
				_msg = trans.cancel_job;
			} else if ( _status == 'delete' ) {
				_msg = trans.delete_job;
				if ( _draft == '1' ) {
					_msg = trans.delete_draft;
				} else {
					_msg = trans.delete_job;
				}
			}

			_msg = "<div class='buyer-job-confirm-message'>" + _msg + "</div>";

			bootbox.dialog({
				title: '',
				message: _msg,
				buttons: {					
					cancel: {
						label: trans.btn_cancel,
						className: 'btn-link',
						callback: function() {
						}
					},
					ok: {
						label: trans.btn_ok,
						className: 'btn-primary',
						callback: function() {
							$.ajax({
								url: _url,
								type: 'POST',
								data:{},
								success: function(json) {
									if ( json.success ) {
										location.reload(true);
									}
								}
							});
						}
					},
				},
			});
		}, 

		application_change_status: function(e, obj) {
			var _url = obj.data('url');
			var _status = obj.data('status');

			var _msg = '';
			if ( _status == 'client-declined' ) {
				_msg = "<div class='buyer-job-confirm-message'>" + trans.app_declined +"</div>";
			}

			bootbox.dialog({
				title: '',
				message: _msg,
				buttons: {
					ok: {
						label: trans.btn_ok,
						className: 'btn-primary',
						callback: function() {
							$.ajax({
								url: _url,
								type: 'POST',
								data:{},
								beforeSend: function(jqXHR, settings) {},
								error: function() {},
								success: function(json) {
									if (json.status == 'success') {
									} else {
									}        
								},
								complete: function (jqXHR, textStatus) {
									location.href = location.href;
								}
							});
						}
					},
					cancel: {
						label: trans.btn_cancel,
						className: 'btn-link',
						callback: function() {
						}
					},
				},
			});
		},

	    save_profile: function() {
	    	var $obj = $(this);
			$.ajax({
				url: $obj.data('url'),
				type: 'POST',
				data: {id: $obj.data('id')},
				beforeSend: function(jqXHR, settings) {},
				error: function() {},
				success: function(json) {
					if ( json.success ) {
						$obj.remove();
					}
				},
			});
	    },

  	};

  	return fn;
}); 