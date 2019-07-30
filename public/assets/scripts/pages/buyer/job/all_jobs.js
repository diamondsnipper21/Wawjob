/**
* job/all_jobs.js
*/

define(['wjbuyer', 'ajax_page'], function (buyer) {    //   buyer.js include
    var fn = {
    	$form: null,

        init: function () {
            buyer.initJobsSelectLinkHandler();

            fn.withdraw.init();

            Global.renderUniform();

            var self = this;

            $('#offers, #open_jobs, #open_contracts').ajaxPage({
                'success': function() {
                    self.init();
                }
            });

            $('#job_postings').ajaxPage({
                'success': function() {
                    self.init();
                }
            });

            $('#job_postings .nav-tabs a').on('click', function() {
                var url = $(this).attr('href');

                if (url == '')
                    return false;

                $('#job_postings > form').attr('action', url);
                $('#job_postings > form').submit();

                return false;
            });

            $('#job_postings [data-hover="dropdown"]').dropdownHover();

            $('[data-toggle="tooltip"]').tooltip();
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

                // $('body').off('click');
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

				$form.submit();
            }
        }
    };

    return fn;
});