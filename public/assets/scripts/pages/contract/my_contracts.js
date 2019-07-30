/**
 * contract/my_contracts.js
 */

 define(['common', 'ajax_page'], function (common) {
 	var fn = {
 		init: function () {
 			this.$container = $('#contracts');
 			this.$form 		= $('form', this.$container);

 			this.bindEvents();
 			this.render();
 			
 			common.initFooter();
 		},

 		bindEvents: function() {
 			var self = this;

 			$('.nav.nav-tabs li a').off('click');
 			$('.nav.nav-tabs li a').on('click', function() {
 				var url = $(this).attr('href');

 				self.$form.attr('action', url);
 				self.$form.submit();

 				return false;
 			});
 		},

 		render: function() {
 			var self = this;

 			this.$container.ajaxPage({
 				success: function() {
 					self.init();
 				}
 			});
 		}
 	};

 	return fn;
 });