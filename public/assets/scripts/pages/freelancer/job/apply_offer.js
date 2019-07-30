/**
 * job/apply_offer.js
 */

define(['bootbox', 'jquery-validation', 'inputmask', 'jquery-form'], function (bootbox) {
 	var fn = {
 		init: function () {

 			var $_form = $('#ApplyOfferForm');
 			var $_rate = $('#EarningRate');
 			var _pos = $_rate.offset();

 			if ( _error == "error" ) {        
 				window.scrollTo(0, _pos.top-10);
 				$_rate.focus();
 				$_rate.addClass('has-errors');
 				$_rate.animate({opacity: 0.1}, 100).animate({opacity: 1}, 500);
 			}

 			$_rate.on('keydown', function(e){
 				if ( ( e.keyCode >= 48 && e.keyCode <= 57 ) || ( e.keyCode >= 96 && e.keyCode <= 105 ) || e.keyCode == 110 || e.keyCode == 190 || e.keyCode == 46 || e.keyCode == 8 ){
 					return true;
 				}else{
 					e.preventDefault();
 				}
 			});

 			$_rate.on('keyup', function(e){
 				if ( parseFloat($(this).val()) > parseFloat($(this).attr('data-max')) ){
 					$_rate.addClass('has-errors');
 					if ( $('.has-error').length < 1 ){
 						$_rate.closest('.col-sm-9').append('<div class="has-error"><span class="help-block">You couldn\'t increase price.</span></div>');
 					}
 					$_rate.animate({opacity: 0.1}, 100).animate({opacity: 1}, 500);
 				}else{
 					$_rate.removeClass('has-errors');
 					$('.has-error').remove();
 				}
 			});

 			$('#rejectOffer').on('click', function(e){
 				var _msg = "<div class='freelancer-reject-offer'>"+trans.reject_offer+"</div>";
 				bootbox.dialog({
 					title: '',
 					message: _msg,
 					buttons: {
 						cancel: {
 							label: trans.btn_cancel,
 							className: 'btn-default',
 							callback: function() {
 								$('.bootbox-confirm').modal('hide');
 							}
 						},
 						ok: {
 							label: trans.btn_ok,
 							className: 'btn-primary',
 							callback: function() {
 								$('.bootbox-confirm').modal('hide');
 								$('#SubmitAction').val('reject');
 								
 								$_form.submit();
 							}
 						},
 					},
 				});
 			});

 			$('#acceptOffer').on('click', function(e){
				$('#SubmitAction').val('accept');
				$('.bootbox-confirm').modal('hide');
				$_form.submit();
 			});

 			$_form.validate();

 			$_form.submit(function(){
 				if ( parseFloat($_rate.val()) > parseFloat($_rate.attr('data-max')) ){
 					window.scrollTo(0, _pos.top-10);
 					$('.help-block').animate({opacity: 0.1}, 100).animate({opacity: 1}, 500);
 					$_rate.animate({opacity: 0.1}, 100).animate({opacity: 1}, 500);
 					return false;
 				}
 			});

 			$('[data-toggle="tooltip"]').tooltip();

 			Global.renderMaxlength();
 		}
 	};

 	return fn;
});