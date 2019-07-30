/**
 * job/saved_jobs.js
 */

 define(['ajax_page'], function () {
 	var fn = {
 		init: function () {
 			this.$container = $('#saved_jobs');
 			this.$form 		= $('form', this.$container);

 			this.bindEvents();
 			this.render();
 		},

 		bindEvents: function() {
 			var self = this;
 			
 			$('.delete-button').on('click', function() {
 				var url = $(this).attr('href');

 				self.$form.attr('action', url);
 				self.$form.submit();

 				return false;
 			});

 			$('#sortBySelect').on('change', function() {
 				self.$form.submit();
 			});
 		},

 		render: function() {
 			var self = this;

 			this.$container.ajaxPage({
 				'success': function() {
 					self.init();
 				}
 			});

 			Global.renderSelect2();
 		}
	};

	return fn;
});