/**
* job/my_jobs.js
*/

define(['wjbuyer', 'common', 'jquery-form'], function (buyer, common) {
    var fn = {
    	$form: null,

        init: function () {
        	this.$form = $('#form_my_jobs');

            buyer.initJobsSelectLinkHandler();

            $('body').on('click', '#open_jobs .pagination li a', function(e) {
                e.preventDefault();
                var url = $(this).prop('href');

                $(document).data('block-ui-target', $('#open_jobs'));

                $.post(url, {}, function(json) {
                    $('#open_jobs').html(json.strHTML);
                });
            });

            $('body').on('click', '#offers .pagination li a', function(e) {
                e.preventDefault();
                var url = $(this).prop('href');

                $(document).data('block-ui-target', $('#offers'));

                $.post(url, {}, function(json) {
                    $('#offers').html(json.strHTML);
                });
            });

            $('body').on('click', '#open_contracts .pagination li a', function(e) {
                e.preventDefault();
                var url = $(this).prop('href');

                $(document).data('block-ui-target', $('#open_contracts'));
                $.post(url, {}, function(json) {
                    $('#open_contracts').html(json.strHTML);

                    fn.withdraw.init();
                });
            });

            Global.renderUniform();

            fn.withdraw.init();
        },

        submit: function() {
	    	fn.$form.submit();
	    },

        withdraw: {
        	$section: null,
            $btnWithdraw: null,
            $btnCancelWithdraw: null,
            $btnCloseWithdraw: null,
            $btnSubmitWithdraw: null,
            $boxWithdraw: null,
            
            init: function() {
            	this.$section = $('#offers');
                this.$boxWithdraw = $('.box-withdraw');
                this.$btnWithdraw = $('.btn-withdraw');
                this.$btnCloseWithdraw = $('.close', this.$boxWithdraw);
                this.$btnSubmitWithdraw = $('.btn-submit-withdraw', this.$boxWithdraw);
                this.$btnCancelWithdraw = $('.btn-cancel-withdraw', this.$boxWithdraw);

                this.bind();

                Global.renderMaxlength();
            },

            bind: function() {

                $('.page-content').on('click', '.btn-withdraw', fn.withdraw.showWithdraw);
                $('.page-content').on('click', '.btn-submit-withdraw', fn.withdraw.submitWithdraw);
                $('.page-content').on('click', '.close', fn.withdraw.closeWithdraw);
                $('.page-content').on('click', '.btn-cancel-withdraw', fn.withdraw.closeWithdraw);

                $('body').on('click', function(e) {
                    var $obj = $(e.target);
                    if ( $obj.hasClass('btn-withdraw') || $obj.closest('.box-withdraw').length ) {
                        return;
                    }

                    fn.withdraw.$boxWithdraw.find('textarea').val('');
                    fn.withdraw.$boxWithdraw.css('display', 'none');
                });
            },

            showWithdraw: function(e) {
                var $this = $(e.target);
                fn.withdraw.$boxWithdraw.css('display', 'none');
                $this.closest('.object-item').find('.box-withdraw').css('display', 'block');
            },

            closeWithdraw: function(e) {
                var $box = $(this).closest('.box-withdraw');
                $box.find('textarea').val('');
                $box.css('display', 'none');
            },

            submitWithdraw: function(e) {
                var $this = $(this);
                var $box = $this.closest('.box-withdraw');
                var $form = $this.closest('form');

                $box.find('.box-message').prepend('<div class="overlay"></div>');

				$form.ajaxSubmit({
					success: function(json) {
						if ( json.success ) {
							$box.css('display', 'none');
							$this.closest('.object-item').find('.btn-withdraw').addClass('disabled');
							$this.closest('.object-item').remove();
						}

						$box.find('.overlay').remove();

						if ( $('.object-item', fn.withdraw.$section).length < 1 ) {
							fn.withdraw.$section.remove();
						}

                        var $alert= '<div class="alert alert-success fade in" id="alertWithdraw">';
                        $alert += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                        $alert += '<p><i class="fa fa-check"></i> ' + trans.withdraw_offer + '</p>';
                        $alert += '</div>';

                        $('.page-content-section').prepend($($alert));
					},
					error: function(xhr) {
						$box.find('.overlay').remove();
						console.log(xhr);
					},

					dataType: 'json',
				});
            }
        }
    };

    return fn;
});