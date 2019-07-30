/**
* app.js
* This script is the main script on this project.
*/

var app = {
	init: function() {
		Metronic.setAssetsPath('/assets/plugins/metronic/');

		Metronic.init(); // init metronic core componets
	   	Layout.init(); // init layout
		Demo.init(); // init demo features
	   	QuickSidebar.init(); // init quick sidebar

	   	$('[data-hover="dropdown"]').dropdownHover();
	}
}

// Start the main app logic.
requirejs(['jquery', 'defines', 'common', 'layout', 'quick-sidebar', 'demo'], function ($, def, common) {
	Global.init();

	app.init();
	common.init();

	// Load page scripts.
	if (!pageId || $.inArray(pageId, config.noScriptPages) != -1) {
		return;
	}

	require(['scripts/admin/pages/' + pageId], function (subPage) {
		subPage.init();
	});
});