(function ($) {
	$.ajaxDatatable = {

		selector: function($obj) {
			var id 			= $obj.attr('id');
			var className 	= $obj.attr('class');
			var selector = $obj[0].tagName;

			if (typeof id != 'undefined') {
				selector += '#' + id;

				return selector;
			}

			if (typeof className != 'undefined') {
				var parts = className.split(' ');
				for (var i = 0; i < parts.length; i++) {
					selector += '.' + parts[i];
				}
			}

			return selector;
		}
	};

	$.fn.ajaxDatatable = function (params) {
		var options = $.extend({}, $.fn.ajaxDatatable.defaults, params);

		return this.each(function() {
			var $self = $(this);
			var $table = $('table.table', $self);

			var sortParams = '';

			if ($table.length != 0)
				$table.addClass('dataTable');

			var $form = $('form:eq(0)', $self);
			function submitForm() {
				var url   = $form.attr('action');

				if (typeof CKEDITOR != 'undefined') {
					for(var instanceName in CKEDITOR.instances) {
	                    CKEDITOR.instances[instanceName].updateElement();
	                }
				}

				var data = '';//$form.serialize();
				var validator = $form.data('validator');
				if (validator && !$form.valid())
					return false;

				// sort params
				var sortParams = '';
				$('thead tr th', $form).each(function() {
					if (!options.sort)
						return false;

					var className = $(this).attr('class');
	            	if (className == undefined || className.indexOf('sorting') < 0)
	            		return true;

					if ($(this).hasClass('sorting_asc')) { // case of "desc"
	            		sortParams = 'sort=' + $(this).data('sort') + '&sort_dir=asc';
	            		return false;
	            	} else if ($(this).hasClass('sorting_desc')) { // case of "desc"
	            		sortParams = 'sort=' + $(this).data('sort') + '&sort_dir=desc';
	            		return false;
	            	}
				});
				if (data != '' && sortParams != '')
					data += '&';
				data += sortParams;

				if ($self.data('locked')) // during sending request, lock
					return;

				$self.data('locked', true);

				if (url == undefined)
					url = currentURL;
				
				if (typeof data == 'string' && data != '') {
					if (url.indexOf('?') < 0)
						url += '?';
					url += '&' + data;
				}

				url = url.replace('/?', '?');
				$form.attr('action', url);

				$form.ajaxSubmit({
	                success: function(html) {
	                	var $html = $(html);
	                    var $contents = $($.ajaxDatatable.selector($self), $html);

	                    if ($.ajaxDatatable.selector($self) == $.ajaxDatatable.selector($html))
	                    	$self.html($html.html());
	                    else if ($contents.length == 0)
	                    	$self.html(html);
	                    else
	                    	$self.html($contents.html());

	                    options.success(html);

	                    $self.trigger('ajaxDatatable.success', [$self]);

	                    $self.data('locked', false);
	                }
	            });
			}

			// handler when clicking page number
			$('ul.pagination li a', $(this)).on('click', function() {
				var url = $(this).attr('href');
				var $form = $('form:eq(0)', $self);
				
				$form.attr('action', url);
				$form.submit();

				return false;
			});

			// handler when submitting form
			$form.on('submit', function() {
				submitForm();
				return false;
			});

			function changeTableRowClass($this) {
				var checked = $this.is(':checked');
                // if checked, change background color of row
                if (checked)
                	$this.closest('tr').addClass('selected');
                else
                	$this.closest('tr').removeClass('selected');
			}

            function disabledChangeStatus() {
                var status = $('.toolbar-table select', $self).val();

                var $checkboxes = $('tbody td input[type="checkbox"]', $self);

                if (status != undefined) {
	                if (status) {
	                    $checkboxes.attr('disabled', true);
	                    $('td input[data-status-' + status + '="true"]').attr('disabled', false);
	                    $('td input[data-status-' + status + '!="true"]').attr('checked', false);
	                } else {
	                    // $checkboxes.attr('disabled', false);
	                    $('tbody input[type="checkbox"]:enabled', $self).attr('disabled', false);
	                    $('tbody input[type="checkbox"]:disabled', $self).attr('disabled', true);
	                }	
                }
                
                var disabled = (status != undefined && status == '') || $('td input[type="checkbox"]:checked', $self).length == 0;
                $('.toolbar-table button', $self).attr('disabled', disabled);

                $checkboxes.each(function() {
                	changeTableRowClass($(this));
                });

                $checkboxes.trigger('change', [true]);
                
                $.uniform.update($checkboxes);
            }

			// Action ToolBar
			$self.off('change', '.toolbar-table select');
            $self.on('change', '.toolbar-table select', function() {
                disabledChangeStatus();
            });

			// Handler when changing checkbox on header
			$self.off('change', 'thead input[type="checkbox"]');
			$self.on('change', 'thead input[type="checkbox"]', function() {
                var checked = $(this).is(":checked");

                $('tbody input[type="checkbox"]:disabled', $self).attr('disabled', true);
                $('tbody input[type="checkbox"]:enabled', $self).attr('checked', checked);
                $('tbody input[type="checkbox"]:enabled', $self).trigger('change');

                $.uniform.update($('tbody input[type="checkbox"]', $self));

                disabledChangeStatus();
            });

			// Handler when changing checkbox on each row
			$self.off('change', 'tbody input[type="checkbox"]');
            $self.on('change', 'tbody input[type="checkbox"]', function(e, force_event) {
                var checked = $(this).is(':checked');

                var all_checked = false;
                $('tbody input[type="checkbox"]:enabled', $self).each(function() {
                    if (!$(this).is(":checked")) {
                        all_checked = false;
                        return false;
                    }

                    all_checked = true;
                });

                $('thead input[type="checkbox"]', $self).attr('checked', all_checked);

                $.uniform.update('table.table thead input[type="checkbox"]');

                changeTableRowClass($(this));

                if (!force_event)
                	disabledChangeStatus();
            });

            // filter bar
            $self.off('change', 'form th input, form th select');
            $self.on('change', 'form th input, form th select', function() {
            	if ($(this).attr('type') == 'checkbox') // ignore for checkbox
            		return true;

            	var autoSubmitOption = $(this).data('auto-submit');
            	if (typeof autoSubmitOption != 'undefined' && !autoSubmitOption)
            		return true;

            	var oldValue = $(this).data('value');
            	if ($(this).val() != '' && (oldValue == $(this).val() || $(this).val().replace(/( )/g, '') == ''))
            		return true;

				$form.submit();
            });

            // sort
            $self.off('click', 'thead tr.heading th');
            $self.on('click', 'thead tr.heading th', function() {
            	if (!options.sort) {
            		$(this).removeClass('sorting sorting_asc sorting_desc');
            		return true;
            	}
            	var className = $(this).attr('class');
            	var $this = $(this);

            	if (className == undefined || className.indexOf('sorting') < 0)
            		return true;

            	if ($(this).hasClass('sorting')) {
            		$(this).removeClass('sorting');
            		$(this).addClass('sorting_asc');
            	} else if ($(this).hasClass('sorting_asc')) { // case of "desc"
            		$(this).removeClass('sorting_asc');
            		$(this).addClass('sorting_desc');
            	} else if ($(this).hasClass('sorting_desc')) { // case of "desc"
            		$(this).removeClass('sorting_desc');
            		$(this).addClass('sorting_asc');
            	}

            	$('thead tr.heading th', $self).each(function() {
            		var className = $(this).attr('class');
            		if ($(this).is($this) || className == undefined || className.indexOf('sorting') < 0)
            			return true;

            		$(this).addClass('sorting').removeClass('sorting_asc sorting_desc');
            	});

				$form.submit();
            });

			$self.off('click', 'a.clear-filter');
			$self.on('click', 'a.clear-filter', function() {
				if (currentURL == undefined)
					return true;
				
				document.location.href = currentURL;
				return false;
			});
		});
	}

	$.fn.ajaxDatatable.defaults = { 'success': function(html) {}, 'sort': true };

})(jQuery);