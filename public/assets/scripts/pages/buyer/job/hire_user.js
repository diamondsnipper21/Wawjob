/**
 * job/hire_user.js
 */

define(['jquery-form'], function () {
 	var fn = {
 		$form: null,

 		init: function () {
 			$('#job').bind('change', fn.selectJob);

 			this.$form = $('#formHireUser');
 			this.$form.validate();

 			Global.renderSelect2();
 		},

	 	selectJob: function() {
	 		var $this = $(this);

	 		if ($this.val() == '')
	 			return true;
	 		
	 		$.ajax({
	 			url: fn.$form.attr('action'),
	 			type: 'POST',
	 			data: {'id': $this.val()},
	 			beforeSend: function(jqXHR, settings) {
	 				$('.btn-hire-user').attr('disabled', true);
	 				$('.job-info').animate({opacity: 0}, 100);
	 			},
	 			success: function(json) {
	 				if ( json.status == 'success' ) {
	 					$('.job-info').html(json.job_info).animate({opacity: 1}, 500);

	 					$('.btn-hire-user').attr('disabled', false);

	 					$(window).trigger('resize');
	 				}       
	      		}
	  		});
	 	} 		
 	};

 	return fn;
});