/**
 * @author KCG
 * @since July 4, 2017
 */

var amd = define.amd;
define.amd= false;

define(['common', 'bs-modalmanager', 'bs-modal', 'bs-datepicker', 'bs-fileinput', 'jcrop', 'jquery-form'], function (common) {

	var fn = {
        init: function() {
            this.initElements();
            
			this.bindEvents();
			this.render();
		},

        initElements: function() {
            this.$container = $('#ajax-modal');
            this.$form      = $('form', this.$container);
            this.$success   = $('.alert-success', this.$container);
            this.$error     = $('.alert-danger', this.$container);
            this.image_info = null;;
        },

		bindEvents: function() {
			var self = this;
            this.$form.validate();

            this.$form.on('submit', function() {
                if (!$(this).valid())
                    return false;

                self.$success.hide();
                self.$error.hide();

                self.$form.attr('action', $(this).data('url'));
                self.$form.ajaxSubmit({
                    success: function(html) {
                        self.$success.show();
                        self.$container.modal('hide');

                        self.$container.trigger('bs.modal.success.close', [html]); 
                    }
                });
                return false;
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
                            $('#temp-avatar').html(src);
                            self.image_info = file.info;

                            $('#tempImage').Jcrop({
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
            
            $('[data-dismiss="fileinput"]', self.$form).on('click.bs.fileinput', function() {
                $('#user-avatar', self.$form).addClass('show');
                $('#user-avatar', self.$form).removeClass('hide');

                $('#temp-avatar', self.$form).addClass('hide');
                $('#temp-avatar', self.$form).removeClass('show');
                return;
            });
		},

		render: function() {
            $('select.select2', this.$form).select2({
                allowClear: true,
                minimumResultsForSearch: -1
            });
		},

        setCoords: function (c) {
            var xRatio = fn.image_info['width']/$('#temp-avatar img').width();
            var yRatio = fn.image_info['height']/$('#temp-avatar img').height();
            
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
        },
    }

	return fn;
});
define.amd = amd;