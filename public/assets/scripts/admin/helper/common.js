/**
 * common.js
 *
 * This scripts serves to support the scripts on the header and sidebar etc as common.
 */

define(['jquery', 'jquery-uniform', 'bootstrap', 'jquery-slimscroll', 'jquery-validation', 'select2', 'ajax_datatable'], function ($) {

    var fn = {
        /**
         * Init scripts associated with the elements on the header.
         */
        initHeader: function () {
            var self = this;

            $('.page-body').off("click", ".page-header-top #header_bar .dropdown-menu li ul a, i.fa-times");
            $('.page-body').on("click", ".page-header-top #header_bar .dropdown-menu li ul a, i.fa-times", function() {
                var $li = $(this).closest('li');
                var url = $(this).attr('href');
                var $container = $(this).closest('li.dropdown-extended');

                if (typeof url == 'undefined')
                    url = $(this).data('url');

                if (url.indexOf('msg_id') >= 0) {
                    document.location.href = url;
                    return false;
                }

                $.ajax({
                    'url': url,
                    'type': 'post',
                    'dataType': 'json',
                    'blockUI': false,
                    success: function(data) {
                        $li.slideUp();

                        $('.unread-count', $container).text(data.count);

                        if (data.count == 0)
                            $('.no-unread-data', $container).removeClass('hide');

                        $('.dropdown-menu-list', $container).height('auto');
                        $('.slimScrollDiv', $container).height('auto');
                    }
                });

                return false;
            });
        },

        initSlimScroll: function(el) {
            $(el).each(function() {
                if ($(this).attr("data-initialized")) {
                    return; // exit
                }

                var height;

                if ($(this).attr("data-height")) {
                    height = $(this).attr("data-height");
                } else {
                    height = $(this).css('height');
                }

                $(this).slimScroll({
                    allowPageScroll: true, // allow page scroll when the element scroll is ended
                    size: '7px',
                    color: ($(this).attr("data-handle-color") ? $(this).attr("data-handle-color") : '#bbb'),
                    wrapperClass: ($(this).attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
                    railColor: ($(this).attr("data-rail-color") ? $(this).attr("data-rail-color") : '#eaeaea'),
                    position: 'right',
                    height: height,
                    alwaysVisible: ($(this).attr("data-always-visible") == "1" ? true : false),
                    railVisible: ($(this).attr("data-rail-visible") == "1" ? true : false),
                    disableFadeOut: true
                });

                $(this).attr("data-initialized", "1");
            });
        },

        destroySlimScroll: function(el) {
            $(el).each(function() {
                if ($(this).attr("data-initialized") === "1") { // destroy existing instance before updating the height
                    $(this).removeAttr("data-initialized");
                    $(this).removeAttr("style");

                    var attrList = {};

                    // store the custom attribures so later we will reassign.
                    if ($(this).attr("data-handle-color")) {
                        attrList["data-handle-color"] = $(this).attr("data-handle-color");
                    }
                    if ($(this).attr("data-wrapper-class")) {
                        attrList["data-wrapper-class"] = $(this).attr("data-wrapper-class");
                    }
                    if ($(this).attr("data-rail-color")) {
                        attrList["data-rail-color"] = $(this).attr("data-rail-color");
                    }
                    if ($(this).attr("data-always-visible")) {
                        attrList["data-always-visible"] = $(this).attr("data-always-visible");
                    }
                    if ($(this).attr("data-rail-visible")) {
                        attrList["data-rail-visible"] = $(this).attr("data-rail-visible");
                    }

                    $(this).slimScroll({
                        wrapperClass: ($(this).attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
                        destroy: true
                    });

                    var the = $(this);

                    // reassign custom attributes
                    $.each(attrList, function(key, value) {
                        the.attr(key, value);
                    });

                }
            });
        },


        // Handles Bootstrap Dropdowns
        handleDropdowns: function () {
            /*
              Hold dropdown on click  
            */
            $('body').on('click', '.dropdown-menu.hold-on-click', function (e) {
                e.stopPropagation();
            });
        },

        handleUniform: function () {
            Global.renderUniform();
        },

        initValidator: function() {
            var options = {
                ignore: '',
                errorElement: 'span',
                errorClass: 'help-block help-block-error', // default input error message class
                errorPlacement: function(error, element) {

                    if ($(element).parent().is('.form-line-wrapper')) {
                        $(element).parent().after(error);
                    } else if ($(element)[0].tagName == 'SELECT' && $(element).data('select2-id')) {
                        $(element).next().after(error);
                    } else if ($(element).parent().hasClass("form-md-line-input")) {
                        $(element).parent().append(error);
                    } else if ($(element).next().hasClass('input-group-addon') || $(element).prev().hasClass('input-group-addon')) {
                        $(element).parent().after(error);
                    } else if($(element).parent().parent().hasClass('fileinput')) {
                        $(element).parent().parent().after(error);
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

            $.validator.setDefaults(options);
        },

        renderSelect2: function() {
            // Select2 for status
            function select2_with_color_format(option) {
                if (!option.id) {
                    return option.text;
                }
                var key = option.text.toLowerCase().replace(/ /g, '-');
                var $option = $(option.element);

                if ( $option.data('color-key') != undefined ) {
                    key = $option.data('color-key');
                }

                return $('<div class="label label-' + key + ' option-icon">' + '&nbsp;' + '</div>' + '<div class="option-text">' + option.text + '</div><div class="clearfix"></div>');
            }
            
            // Select2 for icon & color
            function select2_with_icon_color_format(option) {
                if (!option.id) {
                    return option.text;
                }
                var key = option.text.toLowerCase().replace(/ /g, '-');
                var $option = $(option.element);

                if ( $option.data('color-key') != undefined ) {
                    key = $option.data('color-key');
                }

                return $('<div class="label-color-icon label-' + key + '">' + '<i class="fa ' + $option.data('icon') + '"></i>' + '</div>' + '<div class="label-text">' + option.text + '</div><div class="clearfix"></div>');
            }
            
            // Select2 for users
            function select2_user_format(option) {
                if (!option.id) {
                    return option.text;
                }
                var key = option.text.toLowerCase().replace(/ /g, '-');
                var $option = $(option.element);

                if ( $option.data('color-key') != undefined ) {
                    key = $option.data('color-key');
                }

                if (!$option.data('role-css'))
                    return option.text;

                return $('<div class="iblock"><span class="badge ' + $option.data('role-css') + ' user-role" title="' + $option.data('role-name') + '">' + $option.data('role-short-name') + '</span>&nbsp;' + option.text + '</div>');
            }

            $('select.select2').each(function() {
                var defaultOptions = { allowClear: false, minimumResultsForSearch: -1 };
                var options = {};
                
                if ($(this).data('width') != undefined)
                    options['width'] = $(this).data('width');
                
                if ($(this).data('placeholder') != undefined)
                    options['placeholder'] = $(this).data('placeholder');

                if ($(this).data('with-color') != undefined) {
                    options['templateResult']     = select2_with_color_format;
                    options['templateSelection']  = select2_with_color_format;
                }
                
                if ($(this).data('with-colored-icon') != undefined) {
                    options['templateResult']     = select2_with_icon_color_format;
                    options['templateSelection']  = select2_with_icon_color_format;
                }
                
                if ($(this).data('select2-show-users') != undefined) {
                    options['templateResult']     = select2_user_format;
                    options['templateSelection']  = select2_user_format;
                }
                
                if ($(this).data('select2-show-search') != undefined) {
                    options['minimumResultsForSearch'] = 5;
                }

                options = $.extend({}, defaultOptions, options);
                
                $(this).select2(options);
            });
        },

        initModal: function() {
            if (!$.fn.modal)
                return;

            // general settings
            $.fn.modal.defaults.spinner = $.fn.modalmanager.defaults.spinner = 
              '<div class="loading-spinner" style="width: 200px; margin-left: -100px;">' +
                '<div class="progress progress-striped active">' +
                  '<div class="progress-bar" style="width: 100%;"></div>' +
                '</div>' +
              '</div>';

            $.fn.modalmanager.defaults.resize = true;
        },

        initFooter: function() {

        },

        bindEvents: function() {
            $(window).scroll(function (event) {
                var scrollTop = $(window).scrollTop();

                if (scrollTop) {
                    $('body').addClass('scroll');
                } else {
                    $('body').removeClass('scroll');
                }

                if (scrollTop > $('.page-header').height()) {
                    $('body').addClass('invisible-header');
                } else {
                    $('body').removeClass('invisible-header');
                }
            });

            $(window).trigger('scroll');
        },

        /**
         * Init common scripts.
         */
        init: function () {
            var self = this;

            this.initHeader();

            fn.initSlimScroll('.scroller');
            fn.handleDropdowns();
            fn.handleUniform();

            this.initValidator();

            this.bindEvents();
        }
    };

	return fn;
});