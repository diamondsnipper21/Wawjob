/**
 * config.js
 * This script will support the config for app.js.
 */

var require = {
 	baseUrl: siteUrl + '/assets',
 	urlArgs: 'v=' + res_version, 
 	paths: $.extend({}, require_common_paths, {

	    'jquery-form':  'plugins/jquery.form/jquery.form',
	    'jquerymigrate' : 'plugins/jquery-migrate-1.2.1.min', 
	    'jquery-ui' : 'plugins/jquery-ui/jquery-ui.min',
	    'jquery-slimscroll' : 'plugins/jquery-slimscroll/jquery.slimscroll.min', 
	    'jquerycokie' : 'plugins/jquery.cokie.min', 
	    'jquerypulsate': 'plugins/jquery.pulsate.min',
	    
	    // Components.
	    'bootbox': 					'plugins/bootbox/bootbox',
	    'bootstrap': 				'plugins/bootstrap/dist/js/bootstrap.min',
	    'bootstrapswitch' : 		'plugins/bootstrap-switch/js/bootstrap-switch.min',
	    'bootstrapselect' : 		'plugins/bootstrap-select/bootstrap-select.min',

	    'cookie': 'plugins/jquery.cookie/jquery.cookie',
	    'datepicker': 'plugins/bootstrap-datepicker/js/bootstrap-datepicker', //used
	    'datetimepicker': 'plugins/bootstrap3-datetimepicker/js/bootstrap-datetimepicker.min',
	    'daterangepicker': 'plugins/bootstrap-daterangepicker/daterangepicker', //used
	    'defines': 'scripts/helper/defines',
	    'footable': 'plugins/footable/dist/footable.all.min',
	    'growl': 'plugins/bootstrap-growl/jquery.bootstrap-growl',

	    'jcrop': 'plugins/jcrop/js/jquery.Jcrop.min',
	    'common': 'scripts/helper/common',
	    'ajax': 'scripts/helper/ajax',
	    'wjbuyer': 'scripts/helper/buyer/buyer',
	    'flot': 'plugins/flot/jquery.flot.min',
	    'piechart': 'plugins/flot/jquery.flot.pie.min',
	    'rangeSlider': 'plugins/ion.rangeslider/js/ion-rangeSlider/ion.rangeSlider.min',

	    'adyen': 'plugins/adyen.encrypt.nodom.min',
	}),

	shim: $.extend({}, require_common_shims, {
		'bootstrap': {'deps': ['jquery']},
		'defines': {'deps': ['jquery']},
		'footable': {'deps': ['jquery']},
		'bootbox' : {'deps': ['jquery', 'bootstrap']}, 
		'wjbuyer': {'deps': ['bootbox']},
		'jquery-form': {'deps': ['jquery']},
		'timepicker': {'deps': ['bootstrap', 'moment']},
		'flot': {'deps': ['jquery']},
		'piechart': {'deps': ['flot']},

		'ajax_page': {'deps': ['jquery-form']},
    	'fileinput': {'deps': ['jquery-form', 'bs-tooltip']}
	})
};

var config = {
	noScriptPages: [
		'auth/signup',
		'auth/verify',
		'frontend/coming_soon',
		'freelancer/step/start',
		'frontend/unsubscribe',
		'error'
	],
	ajaxServiceUrl: 'client/v2/ajax.php',
};