/**
 * notification_settings.js
 */


define(['ajax_page'], function () {

  	var fn = {
	    init: function () {
	    	this.$container = $('#profile_settings');
	    	this.$form 		= $('#profile_settings_form');

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