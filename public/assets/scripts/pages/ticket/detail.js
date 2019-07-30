/**
 * Updated By KCG
 * @updated Jan 25, 20018
 */

define([], function () {	

 	var fn = {
  		init: function () {
  			this.bindEvents();
  			this.render();
  		},

  		bindEvents: function() {
  		},

  		render: function() {
  			Global.renderMessageBoard();
  		}
	};

	return fn;
});