/**
 * list.js - Admin / User/Add 
 */

 ///////////////////////////////////////////////////////////
 // exactly SAME with user/edit.js
 //

define(['datepicker'], function () {

	var fn = {	
		
		edit: {

			$container: null,
      		$wrap: null,
			$form: null,

			init: function() {

				fn.edit.$container = $('.page-container');
        		fn.edit.$wrap = $('.page-body');
				fn.edit.$form = $('#frm_user_edit');

		        $('.date-picker').datepicker({
		            rtl: false,
		            orientation: "left",
		            autoclose: true,
		            format: "yyyy-mm-dd",
		        });

		        /*
		        $('.fa-lock').tooltip('destroy').tooltip({
		            placement: 'left'
		        });
		        */

		        fn.edit.$container.on('click', '.btn-save', function(e){
		        	e.preventDefault();
		        	$('#frm_user_edit').submit();
		        });

			}
		},
		

		init: function () {
			fn.edit.init();
		}

	};

	return fn;
});

