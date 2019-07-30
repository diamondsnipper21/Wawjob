/**
 * notification_settings.js
 */


define(['ajax_page'], function () {

  	var fn = {
	    init: function () {
	    	this.$container = $('#notification_settings');
	    	this.$form 		= $('#notification_settings_form');

	    	this.render();
	    },

	    render: function() {
	    	var self = this;

	    	Global.renderUniform();

	    	$('.toggle-checkbox', fn.$form).css('visibility', 'visible');

	    	this.$container.ajaxPage({
	    		success: function() {
	    			self.init();
	    		}
	    	});
	    }
  	};

  	return fn;
});