/**
 * @author KCG
 * @since July 14, 2017
 */
(function ($) {
	$.reasonbox = {
		className: 'modal-reasonbox',
		close: function() {
			$('.' + $.reasonbox.className).modal('hide');
		},

		create: function(params, $this) {
			var options = $.extend({}, $.fn.reasonbox.defaults, params);

			var $container = $('body');

			if ($this != undefined)
				$container = $this.closest('form');

			var $container_form = params.$form;

			var html = '';
			html += '<div class="modal fade modal-scroll ' + $.reasonbox.className + '" tabindex="-1" aria-hidden="true" data-width="500" data-backdrop="static">';
				html += '<form action="" method="post">';
				html += '<div class="modal-header">';
					html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
					html += '<h4 class="modal-title">' + options.title + '</h4>';
				html += '</div>';
				
				html += '<div class="modal-body">';
		            if (options.message != '') {
		            	html += '<div class="margin-top-10">';
		            		html += options.message;
		            	html += '</div>';
		            }

		            html += '<div class="fields">';
			            if ( options.fields.length ) {
			            	html += '<div class="margin-top-10 margin-bottom-10 field">';
			            	$.each(options.fields, function(k, field) {
			            		switch ( field.type ) {
			            			case 'select':
			            				html += '<select name="' + field.name + '" id="' + field.id + '" class="' + field.class + '" data-rule-required="true">';
			            				
			            				html += '<option value="">Select...</option>';

			            				$.each(field.options, function(i, v) {
			            					html += '<option value="' + i + '">' + v + '</option>';
			            				});

			            				html += '</select>';

			            				var $field = $('<input type="hidden" name="' + field.name + '" value="" />');
							            $('input[name="' + field.name + '"]', $container_form).remove();
							            $container_form.append($field);

			            				break;
			            			default:
			            				break;
			            		}
			            	});
			            	html += '</div><!-- .field -->';
			            }

			            html += '<div class="field">';
		                	html += '<textarea name="message" class="form-control maxlength-handler" rows="5" maxlength="1000" placeholder="' + options.placeholder + '" data-rule-required="true" aria-required="true"></textarea>';
	  					html += '</div>';
  					html += '</div><!-- .fields -->';
				html += '</div><!-- .modal-body -->';

				html += '<div class="modal-footer">';
					var button = options.cancelButton;
					html += '<button type="submit" data-dismiss="modal" class="btn ' + button.className + ' btn-cancel">' + button.label + '</button>';

					button = options.actionButton;
					html += '<button type="submit" class="btn ' + button.className + ' btn-action">' + button.label + '</button>';

				html += '</div>';
				html += '</form>';
			html += '</div>';

			$('.' + $.reasonbox.className).remove();
			$container.append(html);

			var $modal = $('.' + $.reasonbox.className);

			if ( $('.select2', $modal).length > 0 ) {
				$('.select2', $modal).select2({
                	minimumResultsForSearch: -1
				});
			}
			
			Global.renderMaxlength();

            // append input for reason element into form
            var $input = $('<input type="hidden" name="_reason" value="" class="hidden-reasonbox" />');
            $('input[name="_reason"]', $container_form).remove();
            $container_form.append($input);

            if (typeof $this != 'undefined') {
            	$this.off('click');
				$this.on('click', function() {
					$modal.modal('show');
				});
			} else {
				$modal.modal('show');
			}

			var $form = $('form', $modal);
			var validator = $form.validate();

			$('.btn-action', $modal).on('click', function(e) {
				var $textarea = $('textarea', $modal);
				var reason    = $textarea.val().trim();

				if (!validator.form()) {
					return false;
				}

				$('input[name="_reason"]', $container_form).val(reason);
				$.each(options.fields, function(k, field) {
					$('input[name="_' + field.name + '"]', $container_form).val($('[name="' + field.name + '"]', $modal).val());
				});

				options.actionButton.callback(e, reason);
				$.reasonbox.close();

				return false;
			});
		}
	};

	$.fn.reasonbox = function (params) {
		var options = $.extend({}, $.fn.reasonbox.defaults, params);

		return this.each(function() {
			$.reasonbox.create(params, $(this));		
		});
	}

	$.fn.reasonbox.defaults = {
		message: '',
		title: '',
		placeholder: 'Reason',
		$form: null,
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
		},
		fields: []
	};

})(jQuery);