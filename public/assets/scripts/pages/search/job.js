/**
 * job.js
 * so gwang
 */

define(['stars', 'jquery-ui'], function (stars) {

	var fn = {
		$searchFrm: null,
		$searchBtn: null,
		$keyword: null,
		$mainCategory: null,
		$subCategory: null,
		$allCheck: null,
		$rssButton: null,
		$budgetBox: null,
		$type: null,
		$typeFixed: null,
		$typeHourly: null,
		$experienceLevel: null,
		$duration: null,
		$workload: null,
		$workloadBox: null,
		$state: null,
		$sort: null,
		url: '',
		
		init: function() {
			this.$searchForm      	= $('#search_form');
			this.$searchButton    	= $('#search_btn');
			this.$keyword 			= $('input[name="q"]', this.$searchForm);
			this.$mainCategory    	= $('#main_category', this.$searchForm);
			this.$subCategory     	= $('input[name="cs[]"]', this.$searchForm);
			this.$allCheck        	= $('#all_check');
			this.$rssButton       	= $('.btn-rss');
			this.$budgetBox       	= $('#budget_box');
			this.$type   			= $('input[name="t[]"]');
			this.$typeFixed       	= $('#type_fixed');
			this.$typeHourly      	= $('#type_hourly');
			this.$price 			= $('input[name="p[]"]');
			this.$experienceLevel 	= $('input[name="el[]"]');
			this.$duration        	= $('input[name="d[]"]');
			this.$workload        	= $('input[name="wl[]"]');
			this.$workloadBox     	= $('#workload_box');
			this.$state        		= $('input[name="st"]');
			// this.$sort            	= $('#sort', $('#job_list'));
			
			this.bindEvents();
			this.render();
		},

		makePrettyUrl: function() {
			fn.getUrlParams();

			if (fn.url == '')
				location.href = currentURL;
			else
			location.href = currentURL + '?' + fn.url;
			
			return false;
		},

		getUrlParams: function() {

			if ( fn.$keyword.val().trim() != '' ) {
				fn.addUrlParam({name:'q', value:fn.$keyword.val().trim()});
			}

			if ( fn.$mainCategory.val() != '' ) {
				fn.addUrlParam({name:'c', value:fn.$mainCategory.val()});

				var subCategoriesArray = [];
				var subCategoriesAllChecked = subCategoriesAllUnchecked = true;
				$('[name="cs[]"]', $('#sub_category_' + fn.$mainCategory.val())).each(function() {
					if ( $(this).prop('checked') ) {
						subCategoriesArray.push($(this).val());
						subCategoriesAllUnchecked = false;
					} else {
						subCategoriesAllChecked = false;
					}
				});

				if ( !subCategoriesAllChecked && !subCategoriesAllUnchecked && subCategoriesArray.length ) {
					fn.addUrlParam({name:'cs', value:subCategoriesArray.join(',')});
				}

				if ( subCategoriesAllUnchecked ) {
					fn.$allCheck.prop('checked', true);
				}

				if ( fn.$allCheck.prop('checked') ) {
					fn.addUrlParam({name:'ac', value:'1'});
				}
			}

			var typesArray = [];
			var typesAllChecked = typesAllUnchecked = true;
			fn.$type.each(function() {
				if ( $(this).prop('checked') ) {
					typesArray.push($(this).val());
					typesAllUnchecked = false;
				} else {
					typesAllChecked = false;
				}
			});

			var typesValue = typesArray.join(',');
			if ( !typesAllChecked && !typesAllUnchecked && typesArray.length ) {
				fn.addUrlParam({name:'t', value:typesValue});
			}

			var pricesArray = [];
			var pricesAllChecked = pricesAllUnchecked = true;
			fn.$price.each(function() {
				if ( $(this).prop('checked') ) {
					pricesArray.push($(this).val());
					pricesAllUnchecked = false;
				} else {
					pricesAllChecked = false;
				}
			});

			if ( !pricesAllUnchecked && !pricesAllChecked && pricesArray.length ) {
				fn.addUrlParam({name:'p', value:pricesArray.join(',')});
			}

			var experienceLevelsArray = [];
			var experienceLevelsAllChecked = experienceLevelsAllUnchecked = true;
			fn.$experienceLevel.each(function() {
				if ( $(this).prop('checked') ) {
					experienceLevelsArray.push($(this).val());
					experienceLevelsAllUnchecked = false;
				} else {
					experienceLevelsAllChecked = false;
				}
			});

			if ( !experienceLevelsAllUnchecked && !experienceLevelsAllChecked && experienceLevelsArray.length ) {
				fn.addUrlParam({name:'el', value:experienceLevelsArray.join(',')});
			}

			var durationsArray = [];
			var durationsAllChecked = durationsAllUnchecked = true;
			fn.$duration.each(function() {
				if ( $(this).prop('checked') ) {
					durationsArray.push($(this).val());
					durationsAllUnchecked = false;
				} else {
					durationsAllChecked = false;
				}
			});

			if ( !durationsAllChecked && !durationsAllUnchecked && durationsArray.length ) {
				fn.addUrlParam({name:'d', value:durationsArray.join(',')});
			}

			var workloadsArray = [];
			var workloadsAllChecked = workloadsAllUnchecked = true;
			fn.$workload.each(function() {
				if ( $(this).prop('checked') ) {
					workloadsArray.push($(this).val());
					workloadsAllUnchecked = false;
				} else {
					workloadsAllChecked = false;
				}
			});

			if ( !workloadsAllChecked && !workloadsAllUnchecked && workloadsArray.length ) {
				fn.addUrlParam({name:'wl', value:workloadsArray.join(',')});
			}

			var state = $('[name="st"]:checked', fn.$searchForm).val();
			if ( state != '' ) {
				fn.addUrlParam({name:'st', value:state});
			}
		},

		addUrlParam: function(param) {
			if ( fn.url != '' ) {
				fn.url += '&';
			}

			fn.url += param.name + '=' + param.value;

			return fn.url;
		},

		bindEvents: function() {

			fn.$searchForm.on('submit', fn.makePrettyUrl);

			// main category change handler
			fn.$mainCategory.on('change', function() {
				$this = $(this);
				var mainCategoryId = $this.val();
				if ( mainCategoryId == '' ) {
					fn.$allCheck.closest('.checkbox').hide();
				} else {
					fn.$allCheck.prop('checked', true);
					fn.$allCheck.closest('.checkbox').show();
				}

				$('.sub-category.checkbox-list').each(function(){
					var parentCategoryId = $(this).data('id');
					if ( mainCategoryId != parentCategoryId ) {
						$('input[type="checkbox"]', $(this)).each(function() {
							$(this).prop('checked', false);
						});
						$(this).hide();
					} else {
						$('input[type="checkbox"]', $(this)).each(function() {
							$(this).prop('checked', true);
						});
						$(this).show();
					}
				});

				fn.makePrettyUrl();
			});

			// sub category check handler
			fn.$subCategory.on('change', function() {
				var $this = $(this);
				var allChecked = true;

				$('[type="checkbox"]', $this.closest('.sub-category')).each(function() {
					if ( !$(this).prop('checked') ) {
						allChecked = false;
					}
				});

				if ( allChecked ) {
					fn.$allCheck.prop('checked', true);
				} else {
					fn.$allCheck.prop('checked', false);
				}

				fn.makePrettyUrl();
			});

			// all subcategories check handler
			fn.$allCheck.on('change', function() {
				var mainCategoryId = fn.$mainCategory.val();
				var subCategoryWrap = $('#sub_category_' + mainCategoryId);
				var checkboxArray = $('input[type="checkbox"]', subCategoryWrap);
				
				if ($(this).prop('checked')) {
					checkboxArray.each(function() {
						$(this).prop('checked', true);
					});
				} else {
					checkboxArray.each(function() {
						$(this).prop('checked', false);
					});
				}

				fn.makePrettyUrl();
			});

			// Job type change handler
			fn.$type.on('change', function() {
				var type = $(this).attr('id');
				if (type == 'type_fixed') {
					if ( $(this).prop('checked') ) {
						fn.$budgetBox.show();
					} else {
						fn.$budgetBox.hide();
					}
				} else {
					if ( $(this).prop('checked') ) {
						fn.$workloadBox.show();
					} else {
						fn.$workloadBox.hide();
					}
				}

				fn.makePrettyUrl();
			});

			// experience level change handler
			fn.$price.on('change', fn.makePrettyUrl);

			// experience level change handler
			fn.$experienceLevel.on('change', fn.makePrettyUrl);

			// duration level change handler
			fn.$duration.on('change', fn.makePrettyUrl);

			// workload level change handler
			fn.$workload.on('change', fn.makePrettyUrl);

			// state change handler
			fn.$state.on('change', fn.makePrettyUrl);

			// sort change handler
			//fn.$sort.on('change', fn.makePrettyUrl);

			// save mark click handler
			$('body').on('click', '.save a', function() {
				var $this = $(this);
				var id = $this.data('id');
				var action = $this.data('action');

				var url = fn.$searchForm.data('saved-job-create') + '/' + id;
				if ( action == 'destroy' ) {
					url = fn.$searchForm.data('saved-job-destroy') + '/' + id;
				}

				$.post(url, function() {
					if ( action == 'create' ) {
						$('i', $this).removeClass('fa-heart-o').addClass('fa-heart');
						$this.data('action', 'destroy');
					} else {
						$('i', $this).removeClass('fa-heart').addClass('fa-heart-o');
						$this.data('action', 'create');
					}
				});

				return false;
			});
		},

		render: function() {
			var self = this;

			var budget_min = $('#bgt_amt_min').val();
			var budget_max = $('#bgt_amt_max').val();

			if (!budget_min)
				budget_min = 0;

			if (!budget_max)
				budget_max = 50000;

			$('#bgt_amt_min').val(budget_min);
			$('#budget_max').val(budget_max);

			// define Slider control
			$("#budget").slider({
				range: true,
				min: 0,
				max: 50000,
				values: [budget_min, budget_max],
				step: 100,
				slide: function(event, ui) {
					$("#bgt_amt_min").val(ui.values[0]);
					$("#bgt_amt_max").val(ui.values[1]);

					$("#budget-value-var").html('$' + self.formatNumber(ui.values[0]) + ' - $' + self.formatNumber(ui.values[1]));
				},

				stop: function(event, ui) {
					self.makePrettyUrl();
				}
			});

			// define star
			stars.init($('.client-score .stars'));

			Global.renderSelect2();
			Global.renderUniform();
			Global.renderTooltip();
            Global.renderGoToTop();
		},

		formatNumber: function(nStr) {
		    nStr += '';
		    x = nStr.split('.');
		    x1 = x[0];
		    x2 = x.length > 1 ? '.' + x[1] : '';
		    var rgx = /(\d+)(\d{3})/;
		    while (rgx.test(x1)) {
		        x1 = x1.replace(rgx, '$1' + ',' + '$2');
		    }
		    return x1 + x2;
		}
	};

	return fn;
});
