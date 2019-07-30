/**
 * @author KCG
 * @since June 30, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'ajax_datatable', 'reasonbox', 'bs-datepicker', 'jcrop', 'alert'], function (common, modal) {

	var fn = {
		init: function() {
            var self = this;

            this.initElements();
            
			this.bindEvents();
			this.render();
            
            this.modal.init();
		},

        initElements: function() {
            this.$container = $('.administrators-page');
            this.$form      = $('form#admin_list');
        },

		bindEvents: function() {
            var self = this;

            // Handler when changing status for admin users
            $(this.$container).off('click', 'button.button-change-status');
            $(this.$container).on('click', 'button.button-change-status', function() {
                var STATUS_ACTIVE       = 1; // User::STATUS_AVAILABLE
                var STATUS_SUSPENDED    = 2; // User::STATUS_SUSPENDED
                var STATUS_DELETE       = 5;

                var text = '';
                var number = 0;

                var target_status = $('select.select-change-status', fn.$form).val();

                var modal_title = '';
                var modal_button_caption = '';

                if (target_status == STATUS_SUSPENDED) {
                    modal_title = 'Suspend Administrators';
                    modal_button_caption = 'Suspend';
                } else if (target_status == STATUS_DELETE) {
                    modal_title = 'Delete Users';
                    modal_button_caption = 'Delete';
                } else if (target_status == STATUS_ACTIVE) {
                    modal_title = 'Activate Users';
                    modal_button_caption = 'Activate';
                }

                if (target_status == STATUS_ACTIVE) {
                    $.alert.create({
                        message: 'Are you sure to activate the selected administrators?',
                        title: 'Confirm',
                        cancelButton: {
                            label: "No",
                            className: 'btn-default',
                            callback: function() {
                            }
                        },
                        actionButton: {
                            label: "Yes",
                            className: 'blue',
                            callback: function() {
                                $('input[name="_action"]', self.$form).val('CHANGE_STATUS');

                                fn.$form.submit();
                            }
                        }
                    });
                } else {
                    $.reasonbox.create({
                        title: modal_title,
                        $form: self.$form,
                        cancelButton: {
                            label: "Cancel",
                            className: 'btn-default',
                            callback: function() {
                            }
                        },
                        actionButton: {
                            label: modal_button_caption,
                            className: 'blue',
                            callback: function(e, reason) {
                                $('input[name="_action"]', self.$form).val('CHANGE_STATUS');

                                fn.$form.submit();
                            }
                        }
                    });
                }
            });

            $('.open-modal', this.$container).off('click');
            $('.open-modal', this.$container).on('click', function() {
                var url = $(this).data('url');
                self.modal.open(url);

                return false;
            });
		},

		render: function() {
            this.renderDataTable();
            this.renderDateTimePicker();
            this.renderSelect2();

            common.handleUniform();
		},

        renderDateTimePicker: function() {
            $('.datepicker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: 'right',
                autoclose: true,
                changeDate: function() {
                }
            });
        },

        renderSelect2: function() {
            // Select2
            common.renderSelect2();

            function select2_status_format(option) {
                var key = option.text.toLowerCase();

                if (option.id == '')
                    return option.text;

                return '<div class="label label-' + key + ' option-icon">' + '&nbsp;' + '</div>' + '<div class="option-text">' + option.text + '</div><div class="clearfix"></div>';
            }
            
            $('.select2-status').select2({
                placeholder: "Select a Status",
                allowClear: true,
                minimumResultsForSearch: -1,
                formatResult: select2_status_format,
                formatSelection: select2_status_format,
                escapeMarkup: function (m) {
                    return m;
                }
            });
        },

        renderDataTable: function() {
            var self = this;
            this.$container.ajaxDatatable({
                success: function(html) {
                    self.init();
                }
            });
        },

        modal: {

            init: function() {
                this.$modalContainer = $('#modal_admin_user_container');
                this.image_info = [];
            },

            bindEvents: function() {
                var self = this;

                $('#modal_admin_user').on('show', function() {
                    self.render();

                    setTimeout(function(){
                        $(window).trigger('resize');
                    }, 1500);
                });

                //onchange event-handler
                $('#avatar').on('change', function () {  
                    if( $('#avatar').val() == '' )  
                        return;

                    var $form = self.$form;
                    var url   = $form.attr('action');

                    self.$form.attr('action', config_file_uploads['url']);
                    self.$form.ajaxSubmit({
                        success: function(json) {
                            if (!json.success) {
                                return true;
                            }

                            var files = $('[name="file_ids"]', $form).val();
                            $.each(json.files, function(i, file) {
                                //show message detail result
                                var src = '<img src="' + file.url + '" id="tempImage" width="100%" height="100%"/>';

                                // Preloading Image
                                var image = new Image();
                                image.src = file.url;
                                image.onload = function() {
                                    $(window).trigger('resize');
                                };

                                $('#temp-avatar').html(src);
                                fn.image_info = file.info;

                                $('#tempImage').Jcrop({
                                    bgFade:     true,
                                    bgOpacity: .2,
                                    setSelect: [ 130, 80, 280, 230 ],
                                    aspectRatio: 1,
                                    onChange:   self.setCoords,
                                    onSelect:   self.setCoords,
                                    onRelease:  self.clearCoords,
                                },function(){
                                    $jcropCont = this;
                                });

                                files += '[' + file.id +']';
                            });
                            $('[name="file_ids"]', $form).val(files);

                            $('#user-avatar', self.$form).addClass('hide');
                            $('#user-avatar', self.$form).removeClass('show');

                            $('#temp-avatar', self.$form).addClass('show');
                            $('#temp-avatar', self.$form).removeClass('hide');
                        },

                        error: function(xhr) {
                            console.log(xhr);
                        },

                        dataType: 'json',
                    });

                    $form.attr('action', url);
                });
            },

            render: function() {
                var self = this;

                this.$form = $('.form-horizontal', self.$modal);
                this.$form.validate({
                    'messages': {
                        'username': {
                            'remote': 'This username "{0}"" is already in use'
                        },
                        'email': {
                            'remote': 'This email "{0}"" is already in use'
                        }
                    }
                });

                this.$modal.ajaxDatatable({
                    success: function(html) {
                        self.$modal.modal('hide');

                        setTimeout(function() {
                            fn.$form.submit();
                        }, 1000);
                    }
                });

                common.renderSelect2();

                Global.renderValidator();
            },

            open: function(url) {
                var self = this;

                Global.blockUI();
                setTimeout(function() {
                    self.$modalContainer.load(url, '', function() {
                        self.$modal = $('#modal_admin_user', self.$modalContainer);
                        
                        self.bindEvents();

                        self.$modal.modal();
                    });
                }, 1000);
            },

            setCoords: function (c) {
                var xRatio = fn.image_info['width']/$('#temp-avatar img').width();
                var yRatio = fn.image_info['height']/$('#temp-avatar img').height();
                
                $('#x1').val(Math.round(c.x * xRatio));
                $('#y1').val(Math.round(c.y * yRatio));
                $('#w').val( Math.round(c.w * xRatio));
                $('#h').val( Math.round(c.h * yRatio));
            },

            clearCoords: function (c) {
                $('#x1').val('');
                $('#y1').val('');
                $('#w').val('');
                $('#h').val('');
            }
        }
	};

	return fn;
});
define.amd = amd;