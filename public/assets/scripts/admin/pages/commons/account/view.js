define(['common', 'jquery', 'ajax_datatable', 'jcrop', 'bs-fileinput'], function (common) {

    var fn = {
        $form: null,
        $imageInfo: null,

        init: function() {
            this.$container = $('#edit_account');
            this.initElements();

            this.bindEvent();
            this.render();
        },

        initElements: function() {
            this.$container = $('#edit_account');
            this.$form = $('#account_form', this.$container);
        },

        render: function() {
            var self = this;

            this.$form.validate();

            common.renderSelect2();
            
            $('.page-body-inner').ajaxDatatable({
                success: function(html) {
                    app.init();

                    self.init();
                }
            });
        },

        bindEvent: function() {
            var self = this;

            //onchange event-handler
            $('#avatar').off('change');
            $('#avatar').on('change', function () {
                if ( $(this).val() === undefined || $(this).val() != '') {
                    var $form = self.$form;
                    var url   = $form.attr('action');

                    $form.attr('action', config_file_uploads['url']);

                    self.$form.ajaxSubmit({
                        blockUI: false ,
                        success: function(json) {
                            if (!json.success) {
                                return false;
                            }

                            var files = $('[name="file_ids"]', $form).val();
                            $.each(json.files, function(i, file) {
                                //show message detail result
                                var src = '<img src="' + file.url + '" id="temp_image" width="100%" height="100%"/>';
                                $('#temp_avatar').html(src);
                                self.$imageInfo = file.info;

                                $('#temp_image').Jcrop({
                                    bgFade:     true,
                                    bgOpacity: .2,
                                    setSelect: [ 130, 80, 280, 230 ],
                                    aspectRatio: 1,
                                    onchange:   self.setCoords,
                                    onSelect:   self.setCoords,
                                    onRelease:  self.clearCoords,
                                },function(){
                                    $jcropCont = this;
                                });

                                files += '[' + file.id +']';
                            });
                            $('[name="file_ids"]', $form).val(files);

                            $('#temp_avatar').show();
                        },
                        error: function(xhr) {
                            console.log(xhr);
                        },
                        dataType: 'json',
                        sort: false
                    });

                    if (typeof url == 'undefined')
                        url = currentURL;
                    $form.attr('action', url);
                }
            });
    
            this.$container.off('click', 'a.fileinput-exists');
            this.$container.on('click', 'a.fileinput-exists', function() {
                $('#temp_avatar').html('')
                $('#temp_avatar').hide();
                $('#user_avatar').show();

                return false;
            });

            $('.btn-reset-form').on('click', function() {
                document.location.href = currentURL;
            });
        },

        setCoords: function (c) {
            var xRatio = fn.$imageInfo['width']/$('#temp_avatar img').width();
            var yRatio = fn.$imageInfo['height']/$('#temp_avatar img').height();

            $('#x1').val(Math.round(c.x * xRatio));
            $('#y1').val(Math.round(c.y * yRatio));
            //$('#x2').val(c.x2);
            //$('#y2').val(c.y2);
            $('#w').val( Math.round(c.w * xRatio));
            $('#h').val( Math.round(c.h * yRatio));
        },

        clearCoords: function (c) {
            $('#x1').val('');
            $('#y1').val('');
            //$('#x2').val(c.x2);
            //$('#y2').val(c.y2);
            $('#w').val('');
            $('#h').val('');
        }
    };

    return fn;
});
