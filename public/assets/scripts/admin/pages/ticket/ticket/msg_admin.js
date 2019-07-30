/**
 * @author KCG
 * @since July 4, 2017
 */

var amd = define.amd;
define.amd= false;

define([], function () {

	var fn = {

		init: function() {
            this.initElements();
            
			this.bindEvents();
			this.render();
		},

        initElements: function() {
        },

		bindEvents: function() {
		},

		render: function() {
            Global.renderMessageBoard();
		}
    }

	return fn;
});
define.amd = amd;