/**
* common.js
*
* This scripts serves to support the scripts on the header and sidebar etc as common.
*/

define(['jquery', 'bs-toastr', 'jquery-uniform', 'jquery-validation', 'jquery-blockui', 'bs-hover-dropdown'], function ($, toastr) {

    var fn = {

        initValidator: function() {

            var options = {
                ignore: '',
                errorElement: 'span',
                errorPlacement: function(error, element) {

                    if ($(element).parent().is('.form-line-wrapper')) {
                        $(element).parent().after(error);
                    } else if ($(element)[0].tagName == 'SELECT' && $(element).data('select2-id')) {
                        $(element).next().after(error);
                    } else if ($(element).parent().hasClass("form-md-line-input")) {
                        $(element).parent().append(error);
                    } else if ($(element).parent().hasClass('input-group')) {
                        $(element).parent().after(error);
                    } else if ($(element).next().hasClass('input-group-addon')) {
                        $(element).after(error);
                    } else if ($(element).closest('.checkbox, .radio').length) {
                        $(element).closest('.checkbox, .radio').after(error);
                    } else if ($(element).closest('.chk').length) {
                        $(element).closest('.chk').after(error);
                    } else if ($(element).parent().hasClass('btn-file') && $(element).attr('type').toLowerCase() == 'file') {
                        $(element).closest('.file-upload-container').after(error);
                    } else {
                        error.insertAfter($(element));
                    }
                },

                highlight: function (element) {
                    $(element).closest('.form-group').addClass('has-error');
                },

                success: function (label, element) {
                    $(element).closest('.form-group').removeClass('has-error')
                },
            };

            if (typeof lang == 'undefined') {
                lang = 'en';
            }

            if (lang == 'en') {
                /*
                messages: {
                required: "This field is required.",
                remote: "Please fix this field.",
                email: "Please enter a valid email address.",
                url: "Please enter a valid URL.",
                date: "Please enter a valid date.",
                dateISO: "Please enter a valid date ( ISO ).",
                number: "Please enter a valid number.",
                digits: "Please enter only digits.",
                creditcard: "Please enter a valid credit card number.",
                equalTo: "Please enter the same value again.",
                maxlength: $.validator.format( "Please enter no more than {0} characters." ),
                minlength: $.validator.format( "Please enter at least {0} characters." ),
                rangelength: $.validator.format( "Please enter a value between {0} and {1} characters long." ),
                range: $.validator.format( "Please enter a value between {0} and {1}." ),
                max: $.validator.format( "Please enter a value less than or equal to {0}." ),
                min: $.validator.format( "Please enter a value greater than or equal to {0}." )
                },
                */
            } else if (lang == 'ch') {
                $.validator.messages = {
                    required: "请输入这个条目。",
                    remote: "请输入正确的值。",
                    email: "请输入正确的邮址。",
                    url: "请输入正确的URL。",
                    date: "请输入正确的日期。",
                    dateISO: "请输入正确的日期(ISO)。",
                    number: "请输入正确的数字。",
                    digits: "只可填写数字。",
                    creditcard: "请输入正确的信用卡号。",
                    equalTo: "请再输入同一个值。",
                    maxlength: $.validator.format( "最大字数为{0}。" ),
                    minlength: $.validator.format( "最少字数为{0}。" ),
                    rangelength: $.validator.format( "字数范围为P{0}至{1}。" ),
                    range: $.validator.format( "输入值范围为{0}至{1}。" ),
                    max: $.validator.format( "最大数值为{0}。" ),
                    min: $.validator.format( "最小数值为{0}。" )
                };
            }

            $.validator.setDefaults(options);

            $.each($.validator.methods, function (key, value) {
                $.validator.methods[key] = function () {    
                    if(key == 'required' && arguments.length > 0) {
                        arguments[0] = $.trim(arguments[0]);
                    }

                    return value.apply(this, arguments);
                };
            });
        },

        /**
        * Init scripts associated with the elements on the header.
        */
        initHeader: function () {
            $('.sysnotification').on("click", function(){
                var notificationId = parseInt($(this).attr("sysnotification-id"));
                var _url = siteUrl + '/notifications/read/' + notificationId;

                $.ajax({
                    url:   _url,
                    type:   'POST',
                    data:{},
                    blockUI: false,
                    beforeSend: function(jqXHR, settings) {},
                    error: function() {},
                    success: function(json) {
                        if (json.status == 'success') {
                            return true;
                        } else {
                            return false;
                        }
                    },   // END OF SUCESS FUNCTION
                    complete: function (jqXHR, textStatus) {
                    }
                });
            });

            $('#header_notification_bar .dropdown-menu li.notification a').on("click", function() {
                var $this_parent = $(this).parent();
                var $unread_notify_obj = $this_parent.parent().prev().find(".notfication-cnt");
                var _url = siteUrl + '/notifications/read/' + $(this).attr("notification-id");

                $.ajax({
                    url:   _url,
                    type:   'POST',
                    data:{},
                    blockUI: false,
                    beforeSend: function(jqXHR, settings) {},
                    error: function() {},
                    success: function(json) {
                        if (json.status == 'success') {
                            $('.fa-times', $this_parent).remove();
                            $this_parent.slideToggle("slow", function() {
                                $this_parent.remove();
                                var unread_notify_cnt = parseInt($unread_notify_obj.text()) - 1;
                                if (unread_notify_cnt == 0)
                                {
                                    $unread_notify_obj.html('');
                                    $unread_notify_obj.parent().parent().find(".notification-all").addClass("notification-all-empty");

                                } else
                                {
                                    $unread_notify_obj.html(unread_notify_cnt);
                                }
                                $(".notification-list-wrap").find( ".nid" + json.notification_id ).removeClass("unread");
                            });

                            $('#alert_' + json.notification_id).remove();
                        } else {
                        }
                    },   // END OF SUCESS FUNCTION
                    complete: function (jqXHR, textStatus) {
                    }
                });

                return false;
            });

            $('#header_notification_bar .dropdown-menu li.notification a').on("click", "i", function() {
                var $this_parent = $(this).parent().parent();
                var $unread_notify_obj = $this_parent.parent().prev().find(".notfication-cnt");

                var notificationId = $(this).parent().attr("notification-id");
                var _url = siteUrl + '/notifications/delete/' + notificationId;

                // Make ajax call
                $.ajax({
                    url:  _url,
                    type: 'POST',
                    dataType: 'html',
                    blockUI: false,
                    success: function(html) {
                        $('.fa-times', $this_parent).remove();
                        $this_parent.slideToggle("slow", function() {
                            $this_parent.remove();
                            var unread_notify_cnt = parseInt($unread_notify_obj.text()) - 1;
                            if (unread_notify_cnt == 0)
                            {
                                $unread_notify_obj.html('');
                                $unread_notify_obj.parent().parent().find(".notification-all").addClass("notification-all-empty");

                            } else
                            {
                                $unread_notify_obj.html(unread_notify_cnt);
                            }
                            $(".notification-list-wrap").find( ".nid" + json.notification_id ).removeClass("unread");
                        });

                        $('#alert_' + json.notification_id).remove();
                    }
                });

                return false;
            });

            $('a.magnifier').on('click', function() {
                $('.magnifier-box').addClass('opening');
                $('#search_keyword').focus();
            });

            $('body').on('click', function(e) {
                var $obj = $(e.target);
                if (!$obj.hasClass('magnifier') && !$obj.hasClass('icon-magnifier') && !$obj.closest('.magnifier-box').length) {
                    $('.magnifier-box').removeClass('opening');
                };
            });

            var $topSearchBox = $('#top_search_box');
            var $topSearchForm = $('#frm_header_search', $topSearchBox);
            var $searchKeyword = $('#search_keyword', $topSearchBox);
            
            $('.btn-search-freelancers', $topSearchBox).on('click', function() {
                $topSearchForm.attr('action', '/search/freelancers');
                $searchKeyword.attr('placeholder', trans.find_freelancers);
            });

            $('.btn-search-jobs', $topSearchBox).on('click', function() {
                $topSearchForm.attr('action', '/search/jobs');
                $searchKeyword.attr('placeholder', trans.find_jobs);
            });

			$topSearchForm.on('submit', function() {
				if ( $searchKeyword.val().trim() == '' ) {
					location.href = $topSearchForm.attr('action');

					return false;
				}
			});

            // Post Job Button
            $('.header .post-job button').on('click', function() {
                window.location.href = $(this).data('href');
            });
        },

        /**
        * Add the functionalities associated with scroll event.
        */
        bindScroll: function () {
            $(window).scroll(function (event) {
                var scrollTop = $(window).scrollTop();

                if (scrollTop) {
                    $('body').addClass('scroll');
                } else {
                    $('body').removeClass('scroll');
                }

                if (scrollTop > $('.header-wrapper').height()) {
                    $('body').addClass('invisible-header');
                } else {
                    $('body').removeClass('invisible-header');
                }
            });

            $(window).trigger('scroll');
        },

        initHandleInput: function() {
            // Floating labels
            var handleInput = function(el) {
                if (el.val() != "") {
                    el.addClass('edited');
                } else {
                    el.removeClass('edited');
                }
            } 

            $('body').on('keydown', '.form-md-floating-label .form-control', function(e) { 
                handleInput($(this));
            }).on('keydown', '.form-md-floating-label .select2', function(e) { 
                handleInput($(this).prev());
            }).on('blur', '.form-md-floating-label .form-control', function(e) { 
                handleInput($(this));
            }).on('change', 'select.select2', function(e) {
                $(this).trigger('blur');
            });

            $('body').on('focus', 'input.form-control', function() {
                var $input_group = $(this).closest('.input-group');
                if ($input_group.length == 0)
                    return true;

                if (!$input_group.hasClass('no-border'))
                    $input_group.addClass('state-focus');
            });

            $('body').on('blur', 'input.form-control', function() {
                var $input_group = $(this).closest('.input-group');
                if ($input_group.length == 0)
                    return true;

                $input_group.removeClass('state-focus');
            });
        },

        initModal: function() {
            var modalTimer = null;
            function reposition() {
                var modal   = $('.modal.in');
                var dialog  = modal.find('.modal-dialog');

                modal.css('display', 'block');
                
                // Dividing by two centers the modal exactly, but dividing by three 
                // or four works better for larger screens.
                var old_margin_top = parseInt(dialog.css('margin-top'));
                var new_margin_top = Math.max(0, (($(window).height() - dialog.height()) / 2));

                if (Math.abs(new_margin_top - old_margin_top) <= 1)
                    new_margin_top = old_margin_top;

                dialog.css("margin-top", new_margin_top);

                if (modalTimer)
                    window.clearTimeout(modalTimer);

                modalTimer = window.setTimeout(function() {
                    reposition();
                }, 300);
            }
            // Reposition when a modal is shown
            $('body').on('shown.bs.modal', '.modal', reposition);
            $('.modal').on('hide.bs.modal', function() {
                if (modalTimer)
                    window.clearTimeout(modalTimer);
            });
            // Reposition when the window is resized
            $(window).on('resize', function() {
                $('.modal:visible').each(reposition);
            });


        },

        initSidebar: function() {
            if ($('body').hasClass('user-page')) {
                var sidebarH = $('.page-content:eq(0)').height();
                var contentH = $('.page-content:eq(1)').height();

                if (sidebarH > contentH)
                    $('.page-content').height(sidebarH);
            }
        },

        initFooter: function() {
            $(window).on('resize', function() {
                // Adjust footer position.
                var bodyHeight      = $('body').height();
                var winHeight       = $(window).height();
                var contentHeight    = $('.page-wrapper').outerHeight();
                var footerHeight    = $('.page-footer').outerHeight();
                var headerHeight    = $('.header-wrapper').outerHeight();

                if ( contentHeight + footerHeight + headerHeight < winHeight ) {
                    $('.page-footer').addClass('fixed');
                } else {
                    $('.page-footer').removeClass('fixed');
                }
            });

            $(window).trigger('resize');
        },

        initTable: function() {
            Global.renderTable();
        },

        bindEvents: function() {
            // Ignoring warnings
            $('body').off('click', '.alert.warning .close');
            $('body').on('click', '.alert.warning .close', function() {
                var url         = $(this).data('url');
                var $self       = $(this);
                var $container  = $(this).closest('.alert.warning');

                if (!url) {
                    $container.slideUp(500, function() {
                        $container.remove();
                    });
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    blockUI: false,
                    success: function() {
                        $container.slideUp(500, function() {
                            $container.remove();
                        });
                    }
                });

                return false;
            });
        },

        /**
        * Init common scripts.
        */
        init: function () {
            this.initValidator();

            this.initHeader();
            this.initSidebar();
            this.initFooter();
            
            this.initHandleInput();

            this.initModal();

            this.initTable();

            this.bindScroll();
            this.bindEvents();
        }
    };

    return fn;
});

function t(str, pattern, lang) {
    for (var prop in pattern) {
        if( pattern.hasOwnProperty( prop ) ) {
            str = str.replace(':'+prop, pattern[prop]);
        }
    }

    return str;
}