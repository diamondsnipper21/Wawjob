/**
* app.js
* This script is the main script on this project.
*/

// Start the main app logic.
requirejs(['jquery', 'defines', 'common', 'bootstrap'], function ($, def, common) {
	Global.init();
	// Load the scripts in common.
	common.init();

	// Load page scripts.
	if (!pageId || $.inArray(pageId, config.noScriptPages) != -1) {
		return;
	}
	require(['scripts/pages/' + pageId], function (subPage) {
		subPage.init();
	});
});