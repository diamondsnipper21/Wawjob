/**
 * @author KCG
 * @since Apr 28, 2018
 */

define([], function () {
	var fn = {
		init: function() {
            this.bindEvents();
            this.render();
        },

        bindEvents: function() {
            $('.table-container tr').on('click', function () {
                $('#' + $(this).data('display')).toggle();
            });

            // $('#table-log').DataTable({
            //     "order": [1, 'desc'],
            //     "stateSave": true,
            //     "stateSaveCallback": function (settings, data) {
            //         window.localStorage.setItem("datatable", JSON.stringify(data));
            //     },
            //     "stateLoadCallback": function (settings) {
            //         var data = JSON.parse(window.localStorage.getItem("datatable"));
            //         if (data) data.start = 0;
            //             return data;
            //     }
            // });
            $('#delete-log, #delete-all-log').click(function () {
                return confirm('Are you sure?');
            });
        },

        render: function() {
        } 
	};

	return fn;
});