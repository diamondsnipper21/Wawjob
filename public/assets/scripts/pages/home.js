/**
 * home.js - Home Page
 */

 define([], function () {

 	var fn = {
 		$testimonials: null,

 		init: function () {
 			this.render();
 		},

 		render: function() {
 			this.$testimonials = $('#testimonials_carousel');
 			this.$testimonials.carousel();

 			Global.renderGoToTop();
 		}
 	};

 	return fn;
 });