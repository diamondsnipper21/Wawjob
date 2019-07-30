/**
* report/weekly_summary.js
*/

define(['moment', 'daterangepicker', 'bootstrapselect', 'flot', 'piechart'], function (moment) {
    var fn = {
    	$container: null,

        init: function () {
        	this.$container = $('.report-weekly-summary-page');

            this.filter.init();
            this.render();
        },

        render: function() {
            this.drawChart();
        },

        drawChart: function() {
            var data = [];
            var contract_datas = $('.contract_amount', '#contract_amounts_data'); 
            var count = contract_datas.length;

            if ( count < 0 ) {
                for (var i = 0; i < count; i++) {
                    data[i] = {
                        label: contract_datas.eq(i).find('.contract_title').val(),
                        data: contract_datas.eq(i).find('.contract_price').val()
                    };
                }

	            if ($('#contract_amount_chart').size() !== 0) {
	                $.plot($('#contract_amount_chart'), data, {
	                    series: {
	                        pie: {
	                            show: true
	                        }
	                    },
	                    grid: {
	                        hoverable: true
	                    }
	                });

	                $('#contract_amount_chart').bind('plothover', pieHover);
	            }
	        }
            
            function pieHover(event, pos, obj) {
                if ( !obj ) {
                    $('.contract_label').hide();
                    return;
                }

                $('.contract_label p').html(obj.series.label + '($' + obj.series.data[0][1] + ')');
                $('.contract_label').show();
                $('.contract_label').css({left:pos.pageX - $(window).scrollLeft(), top:pos.pageY - $(window).scrollTop() + 20});
            }
        },

        filter: {
            initDateRanger: function() {
                $('#date_range').daterangepicker({
                        opens: 'right',
                        format: 'MM/DD/YYYY',
                        startDate: moment(date_from),
                        endDate: moment(date_to),
                        singleDatePicker: true, 
		                'locale': {
		                	'applyLabel': trans.apply,
		                	'cancelLabel': trans.cancel,
		                	'fromLabel': trans.from,
		                	'toLabel': trans.to,
		                	'customRangeLabel': trans.custom,
		                	'daysOfWeek': [
								trans.sun,
								trans.mon,
								trans.tue,
								trans.wed,
								trans.thu,
								trans.fri,
								trans.sat,
							],
							'monthNames': [
								trans.jan,
								trans.feb,
								trans.mar,
								trans.apr,
								trans.may,
								trans.jun,
								trans.jul,
								trans.aug,
								trans.sep,
								trans.oct,
								trans.nov,
								trans.dec,
							],
		                }
                    },

                    function (start, end) {
                        wstart = start.startOf('isoWeek');
                        wend = end.endOf('isoWeek');
                        var s = wstart.format('MMM D, YYYY'); 
                        var e = wend.format('MMM D, YYYY'); 
                        var str;

                        if (s == e) {
                            str = s;
                        } else {
                            str = s + ' - ' + e;
                        }

                        $('#date_range input').val(str);

                        location.href = currentURL + '?from=' + wstart.format('YYYY-MM-DD');
                    }
                ); 

                // Previous, Next Range Link
                $('.prev-unit, .next-unit').on('click', function() {
                    location.href = currentURL + '?from=' + $(this).data('from');
                });

                // Customize datepicker
                var start_date = $('.available.active.start-date');
                start_date.removeClass('end-date');
                for(i = 0; i < 6; i++)
                {
                    if(start_date.next().length == 0)
                        start_date = start_date.parent().next().children(0).eq(0);
                    else
                        start_date = start_date.next();
                    if(i == 5)
                        start_date.addClass('active end-date');
                    else
                        start_date.addClass('active in-range');
                }
            },

            init: function() {
                this.initDateRanger();
            }
        },

        contract: {
        	modalClass: 'modal-contract',
        	modalTitle: '',
        	$container: null,

            init: function($obj) {
            	this.$container = $('body');

            	$('.' + this.modalClass).remove();

            	var json = $obj.data('json');
            	this.modalTitle = 'Manual Time for ' + json.contractor + ' - ' + json.title;

                var html = '';
                html += '<div class="modal fade modal-scroll ' + this.modalClass + '" tabindex="-1" aria-hidden="true" data-backdrop="static">';
                	html += '<div class="modal-dialog">';
                		html += '<div class="modal-content">';
		                	html += '<form action="" method="post">';
		                		html += '<input type="hidden" name="_token" value="' + json.token + '">';
		                		html += '<input type="hidden" name="_action" value="allow_manual_time">';
		                		html += '<input type="hidden" name="_contract_id" value="' + json.id + '">';

			                    html += '<div class="modal-header">';
			                        html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
			                        html += '<h4 class="modal-title">' + this.modalTitle + '</h4>';
			                    html += '</div>';

			                    html += '<div class="modal-body">';

			                        html += '<div class="row">';
			                            html += '<div class="col-md-12">';
			                            	html += '<div class="info margin-bottom-10">Manual time is allowed in the contract. <a href="' + json.url + '">View</a></div>';
			                            html += '</div>';
			                        html += '</div>';

			                        html += '<div class="row">';
			                            html += '<div class="col-md-6">';
			                            	html += '<select name="_manual_time" class="form-control">';
			                            		html += '<option value="1">Yes, I\'m going to pay</option>';
			                            		html += '<option value="0">No, I\'m not going to pay</option>';
			                            	html += '</select>';
			                            html += '</div>';
			                        html += '</div>';

			                    html += '</div>';

			                    html += '<div class="modal-footer">';
			                        html += '<button type="submit" class="btn btn-primary btn-submit">Submit</button>';
			                        html += '<button type="button" data-dismiss="modal" class="btn btn-default btn-cancel">Close</button>';
			                    html += '</div>';

			                html += '</form>';
			            html += '</div>';
		            html += '</div>';
		        html += '</div><!-- .modal -->';

                this.$container.append(html);

                var $modal = $('.' + this.modalClass);
                $modal.modal('show');
            }
        }
    };

    return fn;
});