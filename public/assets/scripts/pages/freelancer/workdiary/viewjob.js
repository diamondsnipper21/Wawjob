/**
 * view.js - Work Diary
 */

define(['moment', 'bootbox', 'datepicker', 'cookie', 'cubeportfolio', 'select2'], function (moment, bootbox) {

 	var fn = {

 		nav: {
 			$currentDate: null,
 			$timezone: null,
 			MEMO_LENGTH: 100,

			// Timezone
			onChooseTimezone: function() {
				$('select.wtimezone').change(function() {
					var tz = $(this).val();
					var queries = getQuery(location.href);
					var wdate = queries["wdate"] || '';
					location.href = currentURL + '?wdate=' + wdate + '&tz=' + tz;
				});
			},

			onChooseContract: function() {
				$('#contract_selector').on('change', function () {
					var cid = $(this).val();
					var $option = $('[value="' + cid + '"]', $(this));
					var url = $option.data('url');

					location.href = url;
				});
			},

			onChooseDate: function() {
				var $datepicker = $('.date-picker').datepicker({
					orientation: 'left',
					autoclose: true,
					startDate: $('#start_date').val(),
					endDate: moment().format('MM/DD/YYYY'),
					todayBtn: true,
					todayHighlight: true,
          			beforeShowDay: function(day) {
			          	var classes = 'worked';

			            for (var i in log_dates) {
			            	if (day.valueOf() == moment(log_dates[i]).valueOf()) {
				            	if (i == log_dates.length - 1) // selected date
				            		return 'active';

			            		return classes;
			            	}
			            }

			            return '';
			        }
				}).on('changeDate', function(e) {
					var date = moment(e.date);
					date = date.format('YYYY-MM-DD');
					var queries = getQuery(location.href);
					location.href = currentURL + '?wdate=' + date + '&tz=' + $('select.wtimezone').val();
				});

				$datepicker.datepicker('update', new Date($('.date-picker').data('date')));
			},

			onViewMode: function() {
				$(".btn-group-viewmode .btn-mode").click(function() {
					var mode = $(this).data("mode");
					$(this).siblings(".active").removeClass("active");
					$(this).addClass("active");

					var $newPane = $(".pane-" + mode);
					var $curPane = $newPane.siblings().first();
					var ids      = new Array();
					var index    = 0;

					$('input.select-slot:checked', $curPane).each(function() {
						ids[index] = $(this).data("id");
						index ++;
	        		});

	        		$('input.select-slot', $newPane).each(function() {
	        			$(this).prop('checked', false);
	        			for (var x in ids) {
	        				if ($(this).data("id") == ids[x]) {
		        				$(this).prop('checked', true);
		        			};
	        			}
	        		});

	        		var $hrs = $('.select-hour', $curPane);

	        		$newPane.find('.select-hour').prop('checked', false);
	        		if ($hrs.filter(':checked').length != 0) {
	        			$hrs.filter(':checked').each(function() {
	        				var id = $(this).closest('.row-hour').find('input.select-slot').first().data('id');
	        				$newPane.find('input[data-id="' + id + '"]').closest('.row-hour').find('.select-hour').prop('checked', true);
	        			});
	        		}

	        		fn.nav.insertSelectTimeText($newPane);

					$curPane.fadeOut();
					$newPane.fadeIn();

					$.cookie("workdiary_mode", mode);
				});
			},

			insertSelectTimeText: function($pane) {

				var count_checked = $('input.select-slot:checked', $pane).length;
	        	var select_time = '00:00';

	        	if (count_checked != 0) {
	        		var hr = Math.floor(count_checked / 6);
	        		var min = count_checked % 6;
	        		if (hr < 9) {
	        			select_time = '0' + hr + ':' + min + '0';
	        		} else {
	        			select_time = hr + ':' + min + '0';
	        		};
	        	}
	        	
	        	$('span.selected-time').text(select_time);
			},

			checkHandler: function() {

	      		// Check / uncheck hour
		        $('input.select-hour').change(function(){

		        	var $pane = $(this).closest('.pane');
		        	var $hr = $(this).closest('.row-hour');
		        	var $cols = $('.slots', $hr);
		        	var $chks = $('input.select-slot', $hr);

		        	$chks.prop('checked', $(this).is(':checked'));
		        	$('button.require-slots').toggleClass('disabled', $('input.select-slot:checked', $pane).length == 0);

		        	fn.nav.insertSelectTimeText($pane);

		        	Global.updateUniform();
		        });

		        // Check / uncheck slot
		        $('input.select-slot').change(function(){

		        	var $pane = $(this).closest('.pane');
		        	var $hr = $(this).closest('.row-hour');
		        	var $chks = $('input.select-slot', $hr);

		        	var isAllChecked = ($chks.length == $chks.filter(':checked').length);
		        	$('input.select-hour', $hr).prop('checked', isAllChecked);

		        	$('button.require-slots').toggleClass('disabled', $('input.select-slot:checked', $pane).length == 0);

		        	fn.nav.insertSelectTimeText($pane);

		        	Global.updateUniform();
		        });

	      	},

			init: function() {
		      	this.$currentDate = $('span.current-date');
		      	this.$timezone = $('select[name=wtimezone]');

		      	this.$addModal = $('#addManualModal');
		      	this.$editModal = $('#EditMemoModal');

		      	this.onChooseTimezone();
		      	this.onChooseContract();
		      	this.onChooseDate();
		      	this.onViewMode();
		      	this.checkHandler();

				$('#startHour, #endHour').select2({
					minimumResultsForSearch: 30
				});

		        // Deselect all
		        $('#deselectAll').click(function(){
		        	$('input.selectable-box[type="checkbox"]').prop('checked', false);
		        	Global.updateUniform();

		        	$(this).add($('#delete')).add($('#editMemo')).addClass('disabled', true);
		        	$('span.selected-time').text('00:00');
		        });

		        // Delete slots
		        $('#delete').click(function() {
		        	var _msg = "<div class='freelancer-delete-workdiary-confirm'>"+trans.delete_screenshot+"</div>";

		        	bootbox.dialog({
		        		title: '',
		        		message: _msg,
		        		buttons: {
			                cancel: {
			                	label: trans.btn_cancel,
			                	className: 'btn-default',
			                	callback: function() {
			                		var id_arr = new Array();
			                		$('li span.select-box input.selectable-box:checked').each(function(){
			                			id_arr.push($(this).data('id'));
			                		});
			                	}
			                },		        			
		        			ok: {
		        				label: trans.btn_ok,
		        				className: 'btn-primary',
		        				callback: function() {
		        					var id_arr = new Array();
		        					$('li span.select-box input.selectable-box:checked').each(function(){
		        						id_arr.push($(this).data('id'));
		        					});

			                        // Contract ID
			                        var cid = fn.nav.$currentDate.data("cid");

			                        // Current Date
			                        var currentDate = fn.nav.$currentDate.data("date");

			                        $.post(url_workdiary_action, {
			                        	cmd: "deleteSlot",
			                        	sid: id_arr,
			                        	cid: cid,
			                        	date: currentDate
			                        }, function(json) {
			                        	if ( !json.success ) {
			                        		return false;
			                        	} else {
			                        		location.reload();
			                        	}

			                        	$(".info-table", fn.slot.$modal).html(json.html);

			                        }).fail(function(xhr) {
			                        });
			                    }
			                },
			            },
			        });

			        return false;
		        });

		        $('#editMemo').click(function() {
		        	if ( $('input.select-slot:checked').length < 1 ) {
		        		var _msg = "<div class='freelancer-delete-workdiary-confirm'>"+trans.select_screenshot+"</div>";

		        		bootbox.dialog({
		        			title: '',
		        			message: _msg,
		        			buttons: {
		        				ok: {
		        					label: trans.btn_ok,
		        					className: 'btn-primary',
		        					callback: function() {
		        					}
		        				}
		        			},
		        		});

		        	} else {
		        		var $firstSlot = $('input.select-slot:checked').first().closest('li.slot');
		        		var existing_memo = $firstSlot.data('comment');
		        		var unique = true;

		        		$('input.select-slot:checked').each(function() {
		        			if ( existing_memo != $(this).closest('li.slot').data('comment') ){
		        				unique = false;
		        			}
		        		});

		        		var val = '';
		        		var count = fn.nav.MEMO_LENGTH;
		        		if ( unique ){
		        			val = existing_memo;
		        			count = fn.nav.MEMO_LENGTH - val.length;

		        			if (count != fn.nav.MEMO_LENGTH) {
								$('span.alert', fn.nav.$editModal).hide();
								$('#newMemo', fn.nav.$editModal).removeClass('border-alert');
							};
		        		}

		        		$('#EditMemoModal #newMemo').val(val).focus();
		        		$('#EditMemoModal span.count-left').text(count);
		        		fn.nav.$editModal.modal('show');
		        	}
		        });

		        $('#updateMemo').click(function(){
		        	var $_slot_array = new Array();
		        	$('input.select-slot:checked').each(function(){
		        		$_slot_array.push($(this).data('id'));
		        	});

					// Contract ID
					var cid = fn.nav.$currentDate.data("cid");

					// Current Date
					var currentDate = fn.nav.$currentDate.data("date");

					// Memo
					var memo = $('#newMemo').val();

					if (memo.trim() != '') {

						$('span.alert', fn.nav.$editModal).hide();
						$('#newMemo', fn.nav.$editModal).removeClass('border-alert');

						$.post(url_workdiary_action, {
							cmd: "editMemo",
							cid: cid,
							date: currentDate,
							sid: $_slot_array,
							memo: $('#newMemo').val()
						}, function(json) {
							if ( !json.success ) {
								return false;
							} else {
								location.reload(true);
							}
						}).fail(function(xhr) {
						});

						fn.nav.$editModal.modal('hide');

					} else {

						$('span.alert').show();
						$('#newMemo').addClass('border-alert');

					}
					
				});

		        $('#insertManual').click(function(){

		        	var from_hour = $('#startHour').val();
		        	var to_hour = $('#endHour').val();
		        	var from_min = $('#startMinute').val();
		        	var to_min = $('#endMinute').val();

					// Contract ID
					var cid = fn.nav.$currentDate.data("cid");

					// Current Date
					var currentDate = fn.nav.$currentDate.data("date");

					// Timezone
					var timezone = fn.nav.$timezone.val();

					// Memo
					var memo = $('#manualMemo').val();

					if (memo.trim() != '') {

						$('span.alert', fn.nav.$addModal).hide();
						$('#manualMemo', fn.nav.$addModal).removeClass('border-alert');

						$.post(url_workdiary_action, {
							cmd: "addManual",
							cid: cid,
							date: currentDate,
							from_hour: from_hour,
							to_hour: to_hour,
							from_min: from_min,
							to_min: to_min,
							tz: timezone,
							memo: memo
						}, function(json) {
							if ( !json.success ) {
								return false;
							}

							location.reload(true);
						}).fail(function(xhr) {
						});

						$('#addManualModal').modal('hide');

					} else{

						$('span.alert', fn.nav.$addModal).show();
						$('#manualMemo', fn.nav.$addModal).addClass('border-alert');

					}
					
		      	});

				$('#addManualModal').on('show.bs.modal', function(e) {
					// ToDo
					var $modal = $('#addManualModal');
					$('span.timezone', $modal).html($('option:selected', fn.nav.$timezone).html());
					$('input.timezone', $modal).val(fn.nav.$timezone.val());
				});
		    }
		},

		slot: {
			$modal: null,

			init: function() {

		        //this.onImageLoad();
		        this.$modal = $("#modalSlot");

		        this.$modal.on("show.bs.modal", function(e) {
		        	var $btn = $(e.relatedTarget);
		        	var $slot = $btn.closest("li.slot");
		        	var comment = $slot.data("comment");
		          	var sid = $btn.data("id"); // screenshot ID

		          	$("h4.modal-title", fn.slot.$modal).html(comment);

					var $a = $("a.link-full", $slot);
					var fullSrc = $a.attr("href");
					var thumbSrc = $a.children("img").attr("src");
					var tz = $("select.wtimezone").val();

					$("a.link-full", fn.slot.$modal).attr("href", fullSrc).children('img.ss-img').attr("src", thumbSrc);

					$(".info-table", fn.slot.$modal).html('');

		          	// Load activity info
		          	$.post(url_workdiary_action, {
			          	cmd: "loadSlot",
			          	sid: sid,
			          	tz: tz
		          	}, function(json) {
						if ( !json.success ) {
							return false;
						}

						$(".info-table", fn.slot.$modal).html(json.html);

		          	}).fail(function(xhr) {
		          	});
		      	});
		    }
		},

		init: function() {
			this.nav.init();
			this.slot.init();

			Global.renderUniform();
	      	Global.renderMaxlength();
	      	Global.renderSelect2();

	      	$('[data-toggle="tooltip"]').tooltip();

	      	// Render cubeportfolio
			if ($('#grid-container').data('cubeportfolio'))
				$('#grid-container').cubeportfolio('destroy');
            $('#grid-container').cubeportfolio({});
		}
	}

	return fn;

});