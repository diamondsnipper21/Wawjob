/**
* profile.js
*
* @author: So Gwang
*
*/

define(['stars', 'ajax_page', 'cubeportfolio'], function (stars) {

    var fn = {
        $morelink: null,
        
        init: function () {
            this.bindEvents();
            this.render();
            this.invitation.init();
        },

        bindEvents: function() {
            $('.morelink').on('click', function() {              
                var $text =  $(this).closest('.description').text().substring(0,300);
                $text += $(this).parent().next().text();
                $(this).parent().next().text('');
                $(this).parent().text($text);
                $(this).remove();
            });

            $('#saved_user').on('click', function() {
                $.ajax({
                    type: "get",
                    cache: false,
                    url: $(this).data('url'),
                    success: function(res) {
                        $('#saved_user').attr('disabled', true).html('<i class="fa fa-heart"></i>&nbsp;&nbsp;' + trans.saved);
                    }
                });

                return false;
            });

            $('.freelancer-user-page').on('change', '#feedback_sort_by', function() {
                $('#work_history_feedback form').submit();
            });

            $('.freelancer-user-page').on('change', '#portfolio_category', function() {
                $('#portfolios form').submit();
            });
        },

        render: function() {
            stars.init($('.stars'));
            
            this.renderAjaxPage();

            Global.renderTooltip();
            Global.renderMaxlength();
            Global.renderUniform();
            Global.renderSelect2();

            $('#grid-container').cubeportfolio({
                // options
            });
        },


        renderAjaxPage: function() {
            var self = this;

            $('#work_history_feedback').ajaxPage({
                success: function(html) {
                    $('#grid-container').cubeportfolio('destroy');

                    self.render();
                }
            });

            $('#portfolios').ajaxPage({
                success: function(html) {
                    self.render();
                }
            });
        },

        invitation: {
        	$form: null,
            $boxInvite: null,       
            $btnInvite: null,
            $btnInviteSubmit: null,
            $btnClose: null,
            $btnViewMore: null,

            init: function() {
                this.$boxInvite = $('#boxInvite');
				this.$form = $('.form-invitation', this.$boxInvite);
                this.$btnInviteSubmit = $('.btn-submit', this.$boxInvite);
                this.$btnClose = $('.close', this.$boxInvite);
                this.$btnInvite = $('#btnInvite');
                this.$btnViewMore = $('.btn-view-more');

                this.bind();
            },

            bind: function() {
                // Button for invite
                this.$btnInvite.on('click', function() {
                    fn.invitation.$boxInvite.css('display', 'block');
                });

                // Button for close the invitation box
                this.$btnClose.on('click', function() {
                    $('.box-ctrl', fn.invitation.$boxInvite).removeClass('has-error').val('');
                    fn.invitation.$boxInvite.css('display', 'none');
                });

                this.$btnViewMore.on('click', function() {
                	$(this).attr('href', $('option:selected', $('#job_id')).data('url'));
                });

                $('body').on('click', function(e) {
                    var $obj = $(e.target);
                    if ( $obj.hasClass('btn-invite') || $obj.closest('.box-invite').length ) {
                        return;
                    }

                    $('.box-ctrl', fn.invitation.$boxInvite).removeClass('has-error').val('');
                    fn.invitation.$boxInvite.css('display', 'none');
                });

                // Button for submit on the invitation form
                this.$btnInviteSubmit.on('click', function() {
                    var $this = $(this);
                    var $box = fn.invitation.$boxInvite;
                    var $form = fn.invitation.$form;
                    var $select = $form.find('select#job_id');
                    var $message = $form.find('textarea');

                    $select.parent().removeClass('has-error');
                    $message.parent().removeClass('has-error');

                    if ( $message.val() == '' ) {
                        $message.parent().addClass('has-error');
                        $message.focus();
                    }

                    if ( $select.val() == '' ) {
                        $select.parent().addClass('has-error');
                        $select.focus();
                    }

                    if ( $select.val() != '' && $message.val() != '' ) {
                        $('.box-message', $form).prepend('<div class="overlay"></div>');
                        $this.addClass('disabled');
                        $form.ajaxSubmit({
                            success: function(json) {
                                $this.removeClass('disabled');
                                $box.css('display', 'none');
                                $box.find('.overlay').remove();
                                $('option[value="' + json.job_id + '"]', $box).remove();

                                var $alert = '<div id="alertInvitationWrap">';
                                $alert += '<div class="alert alert-success fade in" id="alertInvitation">';
                                $alert += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                                $alert += '<p><i class="fa fa-check"></i> ' + json.message + '</p>';
                                $alert += '</div>';
                                $alert += '</div>';

                                $('.freelancer-user-page').prepend($($alert));
                                window.setTimeout(fn.invitation.hideInvitationAlert, 3000);
                            },
                            error: function(xhr) {
                                $this.removeClass('disabled');
                                $box.find('.overlay').remove();
                                console.log(xhr);
                            },

                            dataType: 'json',
                        });
                    }
                });
            },

            hideInvitationAlert: function() {
                $('#alertInvitationWrap').remove();
            }
        }
    };

    return fn;
});
