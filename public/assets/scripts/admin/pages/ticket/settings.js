define(['common'], function (common) {

	var fn = {
		$form: null,
        $remember: null,

        init: function() {
            var $form     = $('#change_password_form');
            var validator = $form.validate();

            //onchange event-handler
            $('#avatar').on('change', function () {
                $form.ajaxSubmit({
                    success: function(json) {
                        if (!json.success) {
                            Global.toastr('', json.msg, 'error');
                            return false;
                        }

                        //show message detail result
                        var src = '<img src="' + json.imgUrl + '" id="tempImage" width="100%" height="100%"/>';
                        $('#temp-avatar').html(src);
                        $imageInfo = json.imageInfo;

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
                    },

                    error: function(xhr) {
                        console.log(xhr);
                    },

                    dataType: 'json',
                });
            });
        }
	};

	return fn;
});
