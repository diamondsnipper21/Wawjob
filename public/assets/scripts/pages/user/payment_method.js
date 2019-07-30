/**
* payment_method.js
*/

define(['bootbox', 'adyen', 'jquery-validation', 'jquery-form'], function (bootbox) {
    var fn = {

        $wrap: null,
        $formList: null,

        add: {
            $modal: null,
            $box: null,
            $button: null,

            init: function() {
                this.$modal = $('#modalPaymentGateway');
                this.$box = $('.payment-gateways', this.$modal);
                this.$button = $('.btn-submit-payment-gateway', this.$modal);

                // Hide the payment gateway dialog
                this.$modal.on('hide.bs.modal', function(e) {
                	fn.add.hideErrors();
                    $('.fields').addClass('hidden');
                    $('.radiobox').removeClass('selected');
                	
                	$('input[name="_gateway"]:checked').prop('checked', false);

                    $('input[type="text"]', fn.$modal).val('');

                	Global.updateUniform();
                });

                this.changePaymentGateway();
                this.bindEvents();
            },

            bindEvents: function() {
            	$('input[name="_gateway"]').on('change', this.changePaymentGateway);

                $('#qrcode').on('change', function () {
                    var $form = $(this).closest('form');
                    var url     = $form.attr('action');

                    if ($(this).val() == '')
                        return true;

                    if (!Global.validateUploadFile($(this)))
                        return false;

                    $form.attr('action', config_file_uploads['url']);

                    $form.ajaxSubmit({
                        success: function(json) {
                            if (!json.success) {
                                Global.showAlertMessages(json.alerts);
                                return true;
                            }

                            $.each(json.files, function(i, file) {
                                $('#temp_qrcode').html('<img src="' + file.url + '" id="tempImage" />');
                                $('[name="file_id"]', $form).val(file.id);
                            });
                        },

                        error: function(xhr) {
                            console.log(xhr);
                        },

                        dataType: 'json',
                    });

                    $form.attr('action', url);
                });

            	$('.fields form', this.$modal).each(function() {
                	$(this).validate({
                        errorPlacement: function (error, element) {
                            if ( $(element).attr('id') == 'qrcode' ) {
                                $(element).closest('.file-upload-container').after(error);
                            } else {
                                if ( $(element).closest('.input-group').length ) {
                                    $(element).closest('div').after(error);
                                } else {
                                    $(element).closest('div').append(error);
                                }
                            }
                        }
                    });
                });

            	this.$button.on('click', this.submitPaymentGateway);
            },

            hideErrors: function() {
				$('.has-error', this.$modal).removeClass('has-error');
        		$('.error', this.$modal).remove();
            },

            changePaymentGateway: function() {
                var $gateway = $('input[name="_gateway"]:checked', fn.add.$modal);
                var $gatewayBox = $gateway.closest('.radiobox');
            	
                var selected = $gateway.val();
            	var $fields = $('.payment-gateway-fields-' + selected, fn.add.$modal);

        		$('.fields', fn.add.$modal).addClass('hidden');
				fn.add.hideErrors();

                $('.radiobox', fn.add.$modal).removeClass('selected');
                $gatewayBox.addClass('selected');

        		$fields.removeClass('hidden');

                $('select.select2', $fields).each(function() {
                    $(this).select2();
                });

                if ( $fields.outerHeight() < fn.add.$box.outerHeight() ) {
                	$fields.outerHeight(fn.add.$box.outerHeight());
                }
            },

            submitPaymentGateway: function() {
            	var selected = $('input[name="_gateway"]:checked', fn.add.$modal).val();
            	$('.payment-gateway-fields-' + selected + ' form', fn.add.$modal).submit();
            }
        },

        edit: {
            $modal: null,
            $button: null,
            cardNumber: '',

            init: function() {
                this.$modal = $('#modalEditPaymentGateway');
                this.$button = $('.btn-submit-payment-gateway', this.$modal);

                // Show the payment gateway dialog
                this.$modal.on('show.bs.modal', function(e) {
                	var $this = $(e.relatedTarget);
                	var $fields = $('.payment-gateway-fields-' + $this.data('gateway'), fn.edit.$modal);
                	
                	$fields.removeClass('hidden');

                	$('input[name="_id"]', $fields).val($this.data('id'));
                	$('.gateway-logo', fn.edit.$modal).attr('src', $this.data('logo'));

                	$.each($this.data('json'), function(i, v) {
                		var $obj = $('[name="' + i + '"]', $fields);
                		if ( $obj.hasClass('select2') ) {
                			$obj.val(v).trigger('change.select2');
                		} else if ( i == 'cardNumber') {
                            fn.edit.cardNumber = 'xxxx xxxx xxxx ' + v;
                			$obj.val(fn.edit.cardNumber).prop('disabled', true);
                		} else {
                			$obj.val(v);
                		}
                	});
                });

                this.$modal.on('hide.bs.modal', function(e) {
                    fn.edit.hideErrors();

                    $('input[type="text"]', fn.edit.$modal).val('');
                    $('.select2', fn.edit.$modal).val('').trigger('change.select2');

                    $('input[name="_id"]', fn.edit.$modal).val(0);
                    $('.gateway-logo', fn.edit.$modal).removeAttr('src');
                    $('.fields').addClass('hidden');
                });

                this.bindEvents();
            },

            bindEvents: function() {
                $('#cardNumber', this.$modal).on('change', function() {
                    if ( $(this).val() != fn.edit.cardNumber ) {
                        $(this).attr('data-rule-number', 'true');
                    } else {
                        $(this).removeAttr('data-rule-number');
                    }
                });

                $('.fields form', this.$modal).each(function() {
                	$(this).validate();
                });

                this.$button.on('click', this.submitPaymentGateway);
            },

            hideErrors: function() {
                $('.has-error', this.$modal).removeClass('has-error');
                $('.error', this.$modal).remove();
            },

            submitPaymentGateway: function() {
                $('.fields:not(.hidden) form', fn.edit.$modal).submit();
            }
        },

        view: {
            $modal: null,

            init: function() {
                this.$modal = $('#modalViewPaymentGateway');

                // Show the payment gateway dialog
                this.$modal.on('show.bs.modal', function(e) {
                	var $this = $(e.relatedTarget);
                	$('.img-qrcode', fn.view.$modal).attr('src', $this.data('qrcode'));
                });

                this.$modal.on('hide.bs.modal', function(e) {
                    $('.img-qrcode', fn.view.$modal).removeAttr('src');
                });
            },
        },

        init: function () {
            this.$wrap = $('.user-payment-method-page');
            this.$formList = $('#formListPaymentGateway', this.$wrap);

            $('.btn-delete', fn.$wrap).on('click', this.deletePaymentGateway);
            $('.btn-make-primary', fn.$wrap).on('click', this.makePrimaryPaymentGateway);
            $('.additional .btn-link').on('click', this.showInformation);

            Global.renderUniform();
            Global.renderSelect2();

            this.add.init();
            this.edit.init();
            this.view.init();
        },

        showInformation: function() {
            var $row = $(this).closest('.list-group-item');
            $row.toggleClass('expanded');
        },

        deletePaymentGateway: function() {
            $('input[name="_gatewayId"]', fn.$formList).val($(this).data('id'));
            $('input[name="_action"]', fn.$formList).val('deletePaymentGateway');

            bootbox.dialog({
                message: trans.confirm_delete_payment_method,
                buttons: {
                    cancel: {
                        label: trans.cancel,
                        className: 'btn-link',
                        callback: function() {}
                    },
                    ok: {
                        label: trans.ok,
                        className: 'btn-primary',
                        callback: function() {
                            fn.$formList.submit();
                        }
                    },                    
                },
            });
        },    

        makePrimaryPaymentGateway: function() {
            $('input[name="_gatewayId"]', fn.$formList).val($(this).data('id'));
            $('input[name="_action"]', fn.$formList).val('makePrimaryPaymentGateway');
            fn.$formList.submit();
        },
    };

    return fn;
});