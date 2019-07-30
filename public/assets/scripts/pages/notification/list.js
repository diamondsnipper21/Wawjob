/**
 * list.js - notification/list
 */

define(['ajax_page'], function () {	

 	var fn = {
 		$listWrap: null,

	    //delete the clicked notification
	    deleteNotification: function ($nItem) {
	    	var notificationId = $nItem.data('id');
	    	var _url = $nItem.data('delete-url');
			// Make ajax call
			$.ajax({
				url:  _url,
				type: 'POST',
				dataType: 'html',
				success: function(html) {
					$.ajaxPage.replaceHTML($('#notification_rows'), html);

					if ( $nItem.hasClass('unread') ) {
						var $header_notification = $("#header_notification_bar");
						var notification_cnt = parseInt($header_notification.find(".notfication-cnt").text()) - 1;
						$header_notification.find(".nid" + notificationId ).remove();
						$header_notification.find(".notfication-cnt").html( notification_cnt == 0 ? '' :  notification_cnt );
					}

					fn.init();
				}
			});
		},

	    //read the clicked notification
	    readNotification: function($nItem) {
	    	var notificationId = $nItem.data('id');
	    	var _url = $nItem.data('read-url');
			
			// Make ajax call
			$.ajax({
				url: _url,
				type: 'POST',
				success: function(json) {
					if (json.status == 'success') {
						var $header_notification = $('#header_notification_bar');
						var notification_cnt = parseInt($header_notification.find('.notfication-cnt').text()) - 1;

						$header_notification.find(".nid" + notificationId ).remove();
						$header_notification.find(".notfication-cnt").html( notification_cnt == 0 ? '' :  notification_cnt );
						$nItem.removeClass('unread');
					}       
				}
			});
		},

		init: function () {

			$('#notifications').ajaxPage({
				'success': function() {
					fn.init();
				}
			});

			this.$listWrap = $('.list-rows');
			this.$listWrap.off('click');
			this.$listWrap.on('click', function (e) {
				var $obj = $(e.target);

				if ( $obj.hasClass('notification-close') ) {
					fn.deleteNotification($obj.closest('.notification-item'));
				} else if ( $obj.parent().parent().hasClass('unread') ) {
					fn.readNotification($obj.closest('.notification-item'));
				}
			});

		}
	};

	return fn;
});