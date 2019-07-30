/**
 * @author KCG
 * @since July 14, 2017
 */
(function ($) {
	$.alert = {
		className: 'modal-alert',
		close: function() {
			$('.' + $.alert.className).modal('hide');
		},

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
		},

		create: function(params, $this) {
			var options = $.extend({}, $.fn.alert.defaults, params);

			var $self = $this;
			var className = (typeof $this == 'undefined'?'modal-alone-alert':'modal-' + $.alert.selector($this).replace(/[ \.#]/g, ''));
			var html = '';

			html += '<div class="modal fade modal-scroll ' + className + ' ' + $.alert.className + '" tabindex="-1" aria-hidden="true" data-width="500" data-backdrop="static">';
				html += '<div class="modal-header">';
					html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
					html += '<h4 class="modal-title">' + options.title + '</h4>';
				html += '</div>';
				html += '<div class="modal-body">';
					html += options.message;
				html += '</div>';
				html += '<div class="modal-footer">';
					var button = options.cancelButton;
					html += '<button type="submit" data-dismiss="modal" class="btn ' + button.className + ' btn-cancel">' + button.label + '</button>';

					button = options.actionButton;
					html += '<button type="submit" data-dismiss="modal" class="btn ' + button.className + ' btn-action">' + button.label + '</button>';

				html += '</div>';
			html += '</div>';

			$('.' + className).remove();
			$('body').append(html);

			var $modal = $('.' + $.alert.className + '.' + className);

			if (typeof $this != 'undefined') {
				$this.off('click');
				$this.on('click', function() {
					$modal.modal('show');
				});
			} else {
				$modal.modal('show');
			}

			$('.btn-action', $modal).off('click');
			$('.btn-action', $modal).on('click', function(e) {
				$modal.on('hidden.bs.modal', function() {
					options.actionButton.callback(e, $self);
				});
			});

			$('.btn-cancel', $modal).off('click');
			$('.btn-cancel', $modal).on('click', function(e) {
				options.cancelButton.callback(e, $self);
			});
		}
	};

	$.fn.alert = function (params) {
		var options = $.extend({}, $.fn.alert.defaults, params);

		return this.each(function() {
			$.alert.create(params, $(this));
		});
	}

	$.fn.alert.defaults = {
		message: '',
		title: '',
		cancelButton: {
			label: "Cancel",
            className: 'btn-default',
            callback: function() {

            }
		},
		actionButton: {
			label: "Okay",
            className: 'blue',
            callback: function() {
            	
            }
		}
	};

})(jQuery);