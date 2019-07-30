/**
 * Fees Page
 */

var amd = define.amd;
define.amd= false;

define(['common', 'jquery-form', 'alert'], function (common) {

    var fn = {
        init: function() {
            var self = this;

            this.initElements();
            
            this.bindEvents();
            this.render();
        },

        initElements: function() {
            this.$container = $('#user_points');
            this.$form = $('form', this.$container);
        },

        bindEvents: function() {   
            $(fn.$container).on('click', 'button.button-submit', function() {
            	var confirm = 'Are you sure to change the settings?';

                $.alert.create({
                    message: confirm,
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
                            $('[name="_action"]', fn.$form).val('SAVE');
                            fn.$form.submit();
                        }
                    }
                });
            });

            $('[data-toggle="tooltip"]').tooltip();
        },

        render: function() {
            this.$form.validate();

            common.handleUniform();
        },
    };

    return fn;
});

define.amd = amd;