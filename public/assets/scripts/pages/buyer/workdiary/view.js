/**
 * view.js - Work Diary
 */

 define(['moment', 'datepicker', 'cookie', 'cubeportfolio'], function (moment) {

 	var fn = {

 		nav: {
 			init: function() {
 				var $datepicker = $('.date-picker').datepicker({
 					orientation: 'auto',
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
			    	location.href = currentURL + "?wdate=" + date + '&tz=' + $('select.wtimezone').val();
			    });

			    $datepicker.datepicker('update', new Date($(".date-picker").data('date')));

			    $(".btn-group-viewmode .btn-mode").click(function() {
			    	var mode = $(this).data("mode");
			    	$(this).siblings(".active").removeClass("active");
			    	$(this).addClass("active");

			    	var $newPane = $(".pane-" + mode);
			    	var $curPane = $newPane.siblings().first();

			    	$curPane.fadeOut();
			    	$newPane.fadeIn();

			    	$.cookie("workdiary_mode", mode);
			    });
			}
		},

		slot: {
			$modal: null,

			init: function() {
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

		onChooseContract: function () {
			var cid = $(this).val();
			var $option = $('[value="' + cid + '"]', $(this));
			var url = $option.data('url');

			location.href = url;
		}, 
		onChooseTimezone: function() {
			var tz = $(this).val();
			var queries = getQuery(location.href);
			var wdate = queries["wdate"] || '';
			location.href = currentURL + "?wdate=" + wdate + "&tz=" + tz;
		},

		init: function() {
			$('#contract_selector').on('change', fn.onChooseContract);
			$('select.wtimezone').on('change', fn.onChooseTimezone);

			this.nav.init();
			this.slot.init();

			$('[data-toggle="tooltip"]').tooltip();

			Global.renderUniform();
			Global.renderSelect2();

			// Render cubeportfolio
			if ($('#grid-container').data('cubeportfolio'))
				$('#grid-container').cubeportfolio('destroy');
            $('#grid-container').cubeportfolio({});
		}
	};

	return fn;
});