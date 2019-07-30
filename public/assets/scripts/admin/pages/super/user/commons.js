/**
 * @author KCG
 * @since July 11, 2017
 */

define(['bs-toastr', 'select2', 'ajax_datatable', 'reasonbox'], function (toastr) {

	var fn = {
		init: function() {
			this.bindEvents();
			this.render();
		},

		bindEvents: function() {
			var self = this;

            // redirect another tab
            $('.tabbable-custom > ul.nav-tabs > li > a').on('click', function() {
            	if (!$(this).parent().hasClass('active'))
                	window.location.href = $(this).attr('href');

                return false;
            });

            var $container = $('.user-status-actions');

            $container.ajaxDatatable({
            	beforeRender: function() {

            	},
                success: function(html) {
                    $('.user-status').empty();
                    $('.user-status').append($('.user-hidden-status'));
                    $('.user-detail-message').html($('span.hide', $container).html());

                    self.bindEvents();
                    self.render();
                }
            });

            $('select.select-change-status', $container).on('change', function() {
            	if (this.value == '')
            		$('.button-change-status').attr('disabled', true);
            	else
            		$('.button-change-status').attr('disabled', false);
            });

            this.$container = $container;
            this.$form 		= $('form', this.$container);

            // Delete, Suspended
            $container.off('click');
            $container.on('click', '.button-change-status', function() {
                var action = $('select.select-change-status', self.$container).val();
                var modal_title = '';
                var modal_button_title = '';

                if (action == 2) { // Suspension
                    modal_title         = 'User Suspension';
                    modal_button_title  = 'Suspend';
                }

                if (action == 4) { // Financial Suspension
                    modal_title         = 'User Financial Suspension';
                    modal_button_title  = 'Financial Suspend';
                }

                if (action == 5) { // Delete
                    modal_title         = 'Delete User';
                    modal_button_title  = 'Delete';
                }

                if (action == 1) { // Active
                    modal_title         = 'Activate User';
                    modal_button_title  = 'Activate';
                }

                if (modal_title != '' && modal_button_title != '') {
                    $.reasonbox.create({
                        title: modal_title,
                        cancelButton: {
                            label: "Cancel",
                            className: 'btn-default',
                            callback: function() {
                            }
                        },
                        actionButton: {
                            label: modal_button_title,
                            className: 'blue',
                            callback: function(e, reason) {
                                self.$form.submit();
                            }
                        },
                        $form: $('#form_user_status')
                    });

                    return false;
                }

                self.$form.submit();
            });
		},

		render: function() {
			$('.portlet-title select.select2').select2({
				allowClear: true,
                minimumResultsForSearch: -1
			});

			this.adjustTabHeight();
		},

		adjustTabHeight: function() {
			// adjust height and line height for tab
			var maxHeight = 0;
			$('.tabbable-custom > .nav-tabs > li > a').each(function() {
				maxHeight = Math.max(maxHeight, $(this).height());
			});

			$('.tabbable-custom > .nav-tabs > li > a').each(function() {
				var html = $(this).html();

				$(this).css('line-height', maxHeight + 'px');
				if (html.indexOf('<br') >= 0) // tab has 2 lines
					$(this).css('line-height', maxHeight / 2 + 'px');

				$(this).height(maxHeight);
			});
		}
	};

	return fn;
});