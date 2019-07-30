/**
 * @created KCG
 * @since Jan 25, 2018
 */

 var Global = {
    message_focus_bottom: true,
    
    // Message Board
    renderMessageBoard: function(load_more) {
        var self = this;

        $('.message-list').each(function() {
            var $container = $(this);
            var $form      = $('form', $container);

            function bindEvents() {
                $('.button-send', $form).off('click');
                $('.button-send', $form).on('click', function() {
                    Global.message_focus_bottom = true;

                    $(document).data('block-ui-target', $container);

                    $form.submit();
                    return false;
                });

                $('textarea', $form).on('keydown keypress keyup change', function(e) {
                    if ($(this).val() == '')
                        $('.button-send', $form).attr('disabled', true);
                    else
                        $('.button-send', $form).attr('disabled', false);

                    if (e.ctrlKey && e.keyCode == 13) {
                        $form.submit();
                    }
                });

                function loadMoreMessage() {
                    Global.message_focus_bottom = false;

                    $('textarea[name="message"]', $container).attr('disabled', true);
                    $('input[name="_action"]', $container).val('LOAD_MESSAGE');

                    // Hide "Load More" buttons
                    $container.addClass('load-more');
                    $('.load-more-messages', $container).hide();

                    $(document).data('block-ui-target', $container);
                    $(document).data('block-ui-custom', $('.loading', $container));
                    
                    $form.attr('action', null); // Set action of form to current url.
                    $form.submit();
                }

                // Load More
                $('.load-more-messages', $container).off('click');
                $('.load-more-messages', $container).on('click', function() {
                    loadMoreMessage();

                    return false;
                });

                // Load More
                $('.scrollspy-panel', $container).off('scroll');
                $('.scrollspy-panel', $container).on('scroll', function() {
                    var position = $(this).scrollTop();
                    if (position == 0 && $('.load-more-messages', $container).length == 0 && $(this).data('enable-load') == 1) {
                        loadMoreMessage();
                    }
                });
            }

            bindEvents();

            requirejs(['ajax_page'], function() {
                $container.ajaxPage({
                    success: function(html) {
                        self.renderMessageBoard();
                    }
                });
            });

            requirejs(['jquery-form'], function() {
                $form.validate();
            });

            Global.renderMaxlength();
            Global.renderFileInput();

            if (Global.message_focus_bottom)
                if ($('.scrollspy-panel').parent().hasClass('slimScrollDiv'))
                    $('.scrollspy-panel', $container).slimScroll({scrollTo : 999999 });
                else
                    $('.scrollspy-panel', $container).scrollTop(999999);
            else
                if ($('.scrollspy-panel').parent().hasClass('slimScrollDiv'))
                    $('.scrollspy-panel', $container).slimScroll({scrollTo : 400 });
                else
                    $('.scrollspy-panel', $container).scrollTop(400);
        });
    },

 	// render bootstrap maxlength handler plugin
 	renderMaxlength: function(params) {
 		var options = {
            limitReachedClass: "label label-danger",
            alwaysShow: true,
            // threshold: 5,
            utf8: false,
            // showOnReady: true,
            warningClass: 'label label-primary',
 		};

 		if (typeof params != 'undefined')
 			options = $.extend({}, options, params);

 		requirejs(['bs-maxlength'], function() {
 			$('.maxlength-handler').maxlength(options);
 		});
 	},

    renderFileInput: function() {
        requirejs(['fileinput'], function() {
            $('input[type="file"]').fileinput();
        });
    },

    renderTooltip: function() {
        requirejs(['bs-tooltip'], function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    },

    renderInputMask: function() {
        requirejs(['inputmask'], function() {
            $(':input').inputmask();
        });
    },

    validateUploadFile: function($file) {
        if (!window.FileReader)
            return true;

        var result = true;

        var max_size = $file.data('max-size');
        var error_file_size = $file.data('error-file-size');
        var file_types = $file.data('file-types');
        var error_file_type = $file.data('error-file-type');

        var file_element = $file[0];
        var files = file_element.files;

        // Check file size
        if (typeof max_size != 'undefined' && typeof error_file_size != 'undefined') {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];

                if (file.size > max_size) {
                    Global.toastr('', '[' + file.name + ']: ' + error_file_size, 'error', {});
                    result = false;
                }
            }
        }

        // Check file extension
        if (typeof file_types != 'undefined' && typeof error_file_type != 'undefined') {
            file_types = file_types.split(',');
            for (var i = 0; i < files.length; i++) {
                var file = files[i];

                var valid_ext = false;
                $.each(file_types, function(index, file_type) {
                    if (file.name.toLowerCase().indexOf('.' + file_type.toLowerCase()) >= 0) {
                        valid_ext = true;
                        return false;
                    }
                });

                if (!valid_ext) {
                    Global.toastr('', '[' + file.name + ']: ' + error_file_type, 'error', {});
                    result = false;
                }
            }
        }

        return result;
    },

    blockUI: function(e, jqXHR, options) {
        // Setting up default options for block UI plugin
        $.blockUI.defaults.ignoreIfBlocked = true;
        $.blockUI.defaults.message         = block_ui_default_html;

        if (typeof options != 'undefined') {
            if (options.mode == 'abort' && options.port.indexOf('validate') >= 0) // jquery-validation -> remote
                return true;

            if (typeof options.blockUI != 'undefined' && !options.blockUI)
                return true;
        }

        if ($(document).data('block-ui-target')) {
            var $target = $(document).data('block-ui-target');
            $target.block(options);
        } else {
            $.blockUI(options);
        }

        if ($(document).data('block-ui-custom')) { // you want use custom block ui...
            var $custom = $(document).data('block-ui-custom');
            $custom.show();

            $('.blockUI.blockMsg').remove();
            $('.blockUI.blockOverlay').addClass('transparent');
        }
    },

    unblockUI: function() {
        if ($(document).data('block-ui-target')) {
            var $target = $(document).data('block-ui-target');
            $target.unblock($target);
        } else {
            $.unblockUI();
        }

        if ($(document).data('block-ui-custom')) { // you used custom block ui...
            var $custom = $(document).data('block-ui-custom');
            $custom.hide();
        }

        $(document).data('block-ui-target', null);
        $(document).data('block-ui-custom', null);
    },

    toastr: function(title, message, type, options) {
        if (typeof toastr == 'undefined')
            return;
        
        var defaults = {
            "closeButton": true,
            "debug": false,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        if (type == 'danger')
            type = 'error';

        if (type == 'error') {
            defaults['timeOut'] = 30000;
            defaults['extendedTimeOut'] = 30000;
        }

        if (typeof options == 'undefined')
            options = defaults;

        options = $.extend({}, defaults, options);

        toastr.options = options;
        toastr[type](message, title);
    },

    showAlertMessages: function(html) {
        var $messages = null;

        if (typeof html == 'undefined')
            $messages = $('.messages.toastr-messages .alert ul li');
        else
            $messages = $('.alert ul li', html);

        var success = true;
        for (var i = 0; i < $messages.length; i++) {
            var $msg    = $($messages[i]);
            var $alert  = $msg.closest('.alert');

            if ($msg.data('ignore-toastr'))
                continue;

            var type = 'success';

            if ($alert.hasClass('alert-success'))
                type = 'success';
            else if ($alert.hasClass('alert-info'))
                type = 'info';
            else if ($alert.hasClass('alert-warning'))
                type = 'warning';
            else if ($alert.hasClass('alert-danger')) {
                type = 'error';
                success = false;
            }

            var options = $msg.data();
            var toastr_options = {};
            for (var key in options) {
                if (key.indexOf('toastr_') < 0)
                    continue;

                toastr_options[key.replace('toastr_', '')] = options[key];
            }

            this.toastr('', $msg.html(), type, toastr_options);
            $msg.remove();
        }

        return success;
    },

    initAjaxSetup: function() {
        var self = this;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ajaxSend(function(e, jqXHR, options) {
            if (!options.isService) {
                if (typeof toastr == 'undefined')
                    return;
                toastr.remove();
            }

            self.blockUI(e, jqXHR, options);

            // Start service, but run only one time.
            if (!options.isService)
                Global.startService(true);
        }).ajaxSuccess(function(e, jqXHR, options) {
            if (options.blockUI !== false)
                self.unblockUI();
        }).ajaxComplete(function() {
            self.showAlertMessages();
        }).ajaxStart(function(e, jqXHR, options) {
        }).ajaxError(function(jqXHR, response, errorThrown) {
            self.unblockUI();
            
            if (response.status == 500 && response.responseJSON && response.responseJSON.message) {
                self.toastr('', response.responseJSON.message, 'error');
            } else
                self.showAlertMessages();
        });
    },

    initToAstr: function() {
        var self = this;
        requirejs(['bs-toastr'], function(toastr) {
            window.toastr = toastr;

            self.showAlertMessages();
        });
    },

    renderValidator: function() {
        requirejs(['jquery-validation'], function() {
            // Add new custom validation.
            $.validator.addMethod('password_alphabetic', function (value, element) { 
                return this.optional(element) || ((/[a-zA-Z]+/.test(value)) );
            }, "Password must include alphabetic.");

            // Add new custom validation.
            $.validator.addMethod('password_number', function (value, element) { 
                return this.optional(element) || (/[0-9]+/.test(value));
            }, "Password must include number.");

            $.validator.addMethod('email', function (value, element) {
                return this.optional(element) || /^[\w-\.\d*]+@[\w\d]+(\.[\w\d]+)*$/.test(value);
            }, "Please enter a valid email address.");

            $.validator.addMethod('username', function (value, element) { 
                return this.optional(element) || /^[a-zA-Z]+(?:[_-]?[a-zA-Z0-9])*$/.test(value);
            }, "Please enter a valid username.");
        });
    },

    renderUniform: function () {
        require(['jquery-uniform'], function() {
            var test = $("input[type=checkbox]:not(.toggle, .make-switch), input[type=radio]:not(.toggle, .star, .make-switch)");
            if (test.size() > 0) {
                test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });

                $('.toggle-checkbox').css('opacity', 1);
            }
        });
    },

    updateUniform: function () {
        $.uniform.update();
    },

    renderSelect2: function() {
        require(['select2'], function() {
            $('select.select2, select.select2-ajax').each(function() {
                var defaultOptions = { minimumResultsForSearch: -1 };
                var options = {};
                
                if ($(this).data('width') != undefined)
                    options['width'] = $(this).data('width');
                
                if ($(this).data('placeholder') != undefined)
                    options['placeholder'] = $(this).data('placeholder');
                
                if ($(this).data('allow-clear') != undefined)
                    options['allowClear'] = $(this).data('allow-clear');
                
                if ($(this).data('minimumResultsForSearch') != undefined)
                    options['minimumResultsForSearch'] = $(this).data('minimumResultsForSearch');
                
                if ($(this).data('maximumSelectionLength') != undefined)
                    options['maximumSelectionLength'] = $(this).data('maximumSelectionLength');

                if ($(this).hasClass('select2-ajax')) {
                    options['minimumInputLength'] = 2;
                    options['ajax'] = {
                        url: $(this).data('url'),
                        dataType: 'json',
                        type: 'post',
                        blockUI: false,
                        processResults: function (data) {
                            // Tranforms the top-level key of the response object from 'items' to 'results'
                            return {
                                results: data
                            };
                        }
                    };
                }

                var $options = $('option', $(this));
                if ($(this).data('minimumResultsForSearch') == undefined && $options.length > 20)
                    options['minimumResultsForSearch'] = 5;

                options = $.extend({}, defaultOptions, options);
                
                $(this).select2(options);

                // if ($(this).data('sortable') != undefined) {
                //     var $this = $(this);

                //     $(this).next().find('.select2-selection__rendered').sortable({
                //         containment: 'parent',
                //         start: function() { $this.select2("onSortStart"); },
                //         update: function() { $this.select2("onSortEnd"); }
                //     });
                // }

                if ($(this).hasClass('select2-ajax')) {
                    // Fix issue on select2 on skills
                    $('.select2-search--inline input', $(this).next()).off('keydown focus');
                    $('.select2-search--inline input', $(this).next()).on('keydown focus', function() {
                        window.setTimeout(function() {
                            $(window).trigger('scroll');
                        }, 1);
                    });
                }
            });

            /* Project Category */
            $('.select2-project-category').select2({
                templateResult: function(option) {
                    var $option = $(option.element);
                    var option_text = option.text;

                    if (!option.id) {
                        return option_text;
                    }

                    var parent_id = $option.data('parent');

                    if (!parent_id) // if parent category, this cateogry will be bold.
                        return $('<strong>' + option_text + '</strong>');

                    return option_text;
                },

                templateSelection: function(option) {
                    var $option = $(option.element);
                    var option_text = option.text.trim();

                    if (!option.id) {
                        return option_text;
                    }

                    var parent_id = $option.data('parent');
                    var caption_without_parent = $option.data('caption-without-parent');

                    if (!parent_id)
                        return $('<strong>' + option_text + '</strong>');

                    // if this category has parent one. append name of parent category into this name.
                    var $parent = $('option[value="' + parent_id + '"]', $option.closest('select'));

                    return $('<span>' + (!caption_without_parent?($parent.text() + ' &gt; '):'') + '<strong>' + option_text + '</strong></span>');
                }
            });
        });
    },

    /**
    * Format number with comma
    */
    formatCurrency: function(value, sep) {
        var decimalValue = '';
        var precisionValue = '';

        var formatValue = Math.round(value * 100) / 100;  

        if ( parseInt(formatValue) == value ){
            precisionValue = '00';
        } else if ( parseInt(formatValue * 10) == value * 10){
            precisionValue = '0';
        }

        var arrayValue = value.toString().split('.');

        decimalValue = arrayValue[0];
        if ( arrayValue.length > 1 ) {
            precisionValue = arrayValue[arrayValue.length - 1];

            if ( precisionValue.length == 1 ) {
                precisionValue *= 10;
            } else if ( precisionValue.length > 2 ) {
				var nZeroes = '';
				for (var i = 0; i < precisionValue.length; i++) {
					if ( precisionValue[i] == '0' ) {
						nZeroes += '0';
					} else {
						break;
					}
				}
				
                precisionValue = Math.round(precisionValue / Math.pow(10, precisionValue.length - 2));
				
				if ( nZeroes != '' ) {
					precisionValue = nZeroes + '' + precisionValue;
				}
            }
        }

        var formatString = '';
        var j = 0;

        if ( decimalValue.length > 3 ) {
            for (var i = decimalValue.length - 1; i >= 0; i--) {
                formatString += decimalValue.charAt(i);
                j++;

                if ( sep ) {
	                if ( j == 3) {
	                    formatString += ',';
	                    j = 0;
	                }
	            }
            }
            formatString = formatString.split('').reverse().join('');
        } else {
            formatString = decimalValue;
        }

        if ( formatString == '' ) {
            formatString = '0';
        }

        if ( precisionValue != '' ) {
            formatString += '.' + precisionValue;
        }

        if ( formatString[0] == ',' ) {
            formatString = formatString.substr(1);
        }

        return formatString;
    },

    parseJsonMultiLang: function(data, lang) {
        if (!data)
            return '';

        decoded_data = JSON.parse(data);

         if (decoded_data[lang.toUpperCase()])
            return decoded_data[lang.toUpperCase()];
        else if (decoded_data[lang.toLowerCase()])
            return decoded_data[lang.toLowerCase()];

        return '';
    },

    navigateTo: function(target) {
        var $target = $(target);
        
        $('html, body').stop().animate({
            'scrollTop':  $target.offset().top //no need of parseInt here
        }, 900, 'swing', function () {
            window.location.hash = target;
        });
    },

    bindEvents: function() {
        var self = this;

        $('body').off('click', '.more-desc');
        $('body').on('click', '.more-desc', function() {
            $(this).closest('.description').addClass('expanded');
            $(this).next().show();
            $(this).prev().hide();
            $(this).hide();

            return false;
        });

        $('body').off('click', '.less-desc');
        $('body').on('click', '.less-desc', function() {
            $(this).closest('.description').removeClass('expanded');
            $(this).parent().prev().show();
            $(this).parent().prev().prev().show();
            $(this).parent().hide();

            return false;
        });

        $('.user-skills').off('click', '.more');
        $('.user-skills').on('click', '.more', function() {
        	$('.rounded-item', $(this).closest('.user-skills')).removeClass('hidden');
        	$(this).remove();
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            Global.renderTable();
        });
    },

    renderGoToTop: function() {
        var html = '<div class="scroll-to-top"><i class="icon-arrow-up"></i></div>';

        if ($('.scroll-to-top').length != 0)
            return;

        var offset = 300;
        var duration = 500;

        $('body').append(html);

        $(window).on('scroll', function() {
            var scrolltop = $(this).scrollTop();

            // Scroll To Top
            if (scrolltop > offset) {
               $('.scroll-to-top').fadeIn(duration);
            } else {
               $('.scroll-to-top').fadeOut(duration);
            }
        });

        $('.scroll-to-top').off('click');
        $('.scroll-to-top').on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, duration);
            return false;
        });
    },

    startService: function(once) {
        if (!$('body').hasClass('page-logged-in'))
            return;

        var timerID = window.setTimeout(function() {
            $.ajax('/system/service', {
                type: 'GET',
                dataType: 'JSON',
                blockUI: false,
                isService: true,
                success: function(data) {
                    // Unread Messages
                    if (data.unread_msg_count == 0)
                        $('.header .msg-notification').addClass('hide');
                    else
                        $('.header .msg-notification').removeClass('hide');
                    $('.header .msg-notification').html(data.unread_msg_count);

                    // Unread Tickets
                    if (data.unread_cnt == 0)
                        $('.header .notfication-cnt').addClass('hide');
                    else
                        $('.header .notfication-cnt').removeClass('hide');
                    $('.header .notfication-cnt').html(data.unread_cnt);

                    // Unread Ticket Messages
                    if (data.unread_ticket_messages == 0)
                        $('.header .ticket-message-count').addClass('hide');
                    else
                        $('.header .ticket-message-count').removeClass('hide');
                    $('.header .ticket-message-count').html(data.unread_ticket_messages);

                    if (typeof once != 'undefined')
                        window.clearTimeout(timerID);
                },
                error: function() {
                    window.location.reload(true);
                }
            });
        }, 1000);
    },

    renderTable: function() {
        var $tables = $('div.table:visible');

        $tables.each(function() {
            var $table = $(this);
            var $trs = $('.tr', $table);

            if ($table.data('adjusted'))
                return true;

            $trs.each(function() {
                var $tr = $(this);
                var trH = $tr.height();
                var $tds = $('.td', $tr);

                $tds.each(function() {
                    var $td = $(this);
                    $td.css('padding-top', (trH - $td.height()) / 2 + 'px');
                    $td.css('padding-bottom', (trH - $td.height() + 1) / 2 + 'px');
                });
            });

            $table.data('adjusted', true);
        });
    },

    init: function() {
    	this.initAjaxSetup();
        this.initToAstr();
        this.renderTable();

    	this.bindEvents();
    }
 };