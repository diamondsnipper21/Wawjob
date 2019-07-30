/**
 * job/invite_freelancers.js
 * @author Ro Un Nam
 * @since May 19, 2017
 */

define(['common', 'wjbuyer', 'stars', 'bs-toastr', 'jquery-form'], function (common, buyer, stars, toastr) {
	var fn = {
		init: function () {
			this.$modalJobTerm = $('#modalJobTerm');
			this.$formJobTerm = $('#formJobTerm');

			Global.renderUniform();
            Global.renderSelect2();

			buyer.initJobsSelectLinkHandler();
			this.initButtons();
			this.initAcceptTerms();
			this.validate();
			this.invitation.init();
			this.filters.init();

			stars.init($('.score .stars'));

			$('[data-toggle="tooltip"]').tooltip();
		},

    	initButtons: function() {
	        // Button for saving profile
	        $('.btn-save').on('click', buyer.save_profile);
	    },

		initAcceptTerms: function() {
			if ( this.$modalJobTerm.length > 0 ) {
				this.$modalJobTerm.modal({
					show: true,
					keyboard: false,
					backdrop: 'static'
				});
			}			
		},

		validate: function() {
			this.$formJobTerm.validate();
		},

		invitation: {
			$btnInvite: null,

			init: function() {
				this.$btnInvite = $('.btn-invite');

				this.bindEvents();
			},

			bindEvents: function() {
				// Button for invite
				this.$btnInvite.on('click', this.showInvitation);

				$('body').on('click', function(e) {
					var $obj = $(e.target);
					if ( $obj.hasClass('btn-invite') || $obj.closest('.box-invite').length ) {
						return;
					}

					fn.invitation.closeInvitation();
				});
			},

			showInvitation: function() {
				fn.invitation.closeInvitation();
				
				var $obj = $(this);

				var json = $obj.data('json');

				var html = '';

				html += '<div class="box-invite">';
					html += '<button type="button" class="close">&times;</button>';
					html += '<div class="box-title">';
						html += trans.job_invite.replace(':x', '<a href="' + json.user_url + '">' + json.user_name + '</a>');
					html += '</div>';
					html += '<div class="box-user-info">';
						html += '<div class="row">';
							html += '<div class="col-md-2 avatar">';
								html += '<a href="' + json.user_url + '"><img alt="' + json.user_name + '" class="img-circle pull-left" src="' + json.user_avatar + '" width="30" height="30"></a>';
							html += '</div>';
							html += '<div class="col-md-10 info">';
								html += '<a href="' + json.user_url + '">' + json.user_name + '</a>';
								html += '<span>' + json.user_title + '</span>';
							html += '</div>';
						html += '</div>';
					html += '</div>';
					html += '<div class="box-message">';
						html += '<form class="form-horizontal form-invitation" method="post" action="' + json.action_url + '">';
		  					html += '<input type="hidden" name="_token" value="' + json.token + '">';
		  					html += '<input type="hidden" name="user_id" value="' + json.user_id + '">';
		  					html += '<input type="hidden" name="job_id" value="' + json.id + '">';
							html += '<div class="box-ctrl">';
								html += '<label>' + trans.job_message + '</label>';
								html += '<textarea name="invite_message" placeholder="' + trans.job_place_holder_invitation_message + '" class="form-control message maxlength-handler margin-bottom-20" maxlength="5000">' + trans.job_place_holder_invitation_message + '\n\n' + json.buyer_name + '</textarea>';
							html += '</div>';
							html += '<button type="button" class="btn btn-primary btn-submit">' + trans.job_send_invitation + '</button>';
						html += '</form>';
					html += '</div>';
				html += '</div>';

				$obj.after(html);

				var $box = $('.box-invite');
				$box.show();

				$('button.close', $box).on('click', fn.invitation.closeInvitation);
				$('button.btn-submit', $box).on('click', fn.invitation.submitInvitation);

				Global.renderMaxlength();
			},

			closeInvitation: function() {
				$('.box-invite').remove();
			},

			submitInvitation: function() {
				var $this = $(this);
				var $box = $this.closest('.box-invite');
				var $form = $this.closest('form');
				var $message = $form.find('textarea');

				$message.parent().removeClass('has-error');

				if ( $message.val() == '' ) {
					$message.parent().addClass('has-error');
					$message.focus();
				} else {
					$form.find('.box-message').prepend('<div class="overlay"></div>');
					$this.addClass('disabled');

					$form.ajaxSubmit({
						success: function(json) {
							$this.removeClass('disabled');
							$message.val('');
							$box.css('display', 'none');
							$this.closest('.btn-wrap').find('.btn-invite').remove();
							$this.closest('.btn-wrap').prepend('<label class="border"><i class="fa fa-check"></i>' + trans.invitation_sent + '</label>');

							fn.invitation.showMessage(json.message);

							$('.total-invited').text(parseInt($('.total-invited').text()) + 1);
							$box.remove();
						},
						error: function(xhr) {
							$this.removeClass('disabled');
							$box.find('.overlay').remove();
						},

						dataType: 'json',
					});
				}
			},

			showMessage: function(message, type) {
				var options = {
		            "closeButton": true,
		            "debug": false,
		            "positionClass": "toast-top-right",
		            "onclick": null,
		            "showDuration": "1000",
		            "hideDuration": "1000",
		            "timeOut": "5000",
		            "extendedTimeOut": "1000",
		            "showEasing": "swing",
		            "hideEasing": "linear",
		            "showMethod": "fadeIn",
		            "hideMethod": "fadeOut"
		        };

		        if (typeof type == 'undefined')
            		type = 'success';

				toastr.options = options;
	        	toastr[type](message, '');
			}
		},

		filters: {
			url: '',

        	$form: null,
        	$keyword: null,
	        $category: null,
	        $title: null,
	        $hourlyRates: null,
	        $jobSuccesses: null,
	        $feedbacks: null,
	        $englishLevels: null,
	        $languages: null,
	        $hours: null,
	        $activities: null,

			$boxFilters: null,
			$btnFilters: null,
			$btnCancel: null,
			$locations: null,
			$languages: null,

			init: function() {
				this.$form = $('#form_invite_freelancers');
				this.$keyword = $('[name="q"]', this.$form);
				this.$category = $('#category', this.$form);
				this.$title = $('#title', this.$form);
				
				this.$hourlyRates = $('[name="hr"]', this.$form);
				this.$jobSuccesses = $('[name="js"]', this.$form);
				this.$feedbacks = $('[name="f"]', this.$form);			
				this.$englishLevels = $('[name="el"]', this.$form);
				this.$languages = $('#languages', this.$form);	
				this.$hours = $('[name="hb"]', this.$form);			
				this.$activities = $('[name="a"]', this.$form);

				this.$boxFilters = $('.box-filters');
				this.$btnFilters = $('.btn-filters');
				this.$btnCancel = $('.btn-cancel', this.$boxFilters);

				this.bindEvents();

				Global.renderSelect2();
			},

			bindEvents: function() {
				this.$btnFilters.on('click', function() {
					if ( fn.filters.$boxFilters.is(':visible') ) {
						fn.filters.$boxFilters.slideUp();
					} else {
						fn.filters.$boxFilters.slideDown();
					}
				});

				this.$btnCancel.on('click', function() {
					fn.filters.$boxFilters.slideUp();
				});

				this.$form.on('submit', fn.filters.makePrettyUrl);
			},

			getUrlParams: function() {
				var q = fn.filters.$keyword.val().trim();
				var c = fn.filters.$category.val();
				var t = fn.filters.$title.val().trim();
				var hr = $('[name="hr"]:checked', fn.filters.$form).val();
				var js = $('[name="js"]:checked', fn.filters.$form).val();
				var f = $('[name="f"]:checked', fn.filters.$form).val();
				var el = $('[name="el"]:checked', fn.filters.$form).val();			
				var hb = $('[name="hb"]:checked', fn.filters.$form).val();
				var a = $('[name="a"]:checked', fn.filters.$form).val();
				var l = fn.filters.$languages.val();

				if ( q != '' ) {
					fn.filters.addUrlParam({name:'q', value:q});
				}

				if ( c != '' ) {
					fn.filters.addUrlParam({name:'c', value:c});
				}

				if ( t != '' ) {
					fn.filters.addUrlParam({name:'t', value:t});
				}

				if ( hr != '' ) {
					fn.filters.addUrlParam({name:'hr', value:hr});
				}

				if ( js != '' ) {
					fn.filters.addUrlParam({name:'js', value:js});
				}

				if ( f != '' ) {
					fn.filters.addUrlParam({name:'f', value:f});
				}

				if ( el != '' ) {
					fn.filters.addUrlParam({name:'el', value:el});
				}

				if ( l != '' ) {
					fn.filters.addUrlParam({name:'ln', value:fn.$languages.val()});
				}

				if ( hb != '' ) {
					fn.filters.addUrlParam({name:'hb', value:hb});
				}

				if ( a != '' ) {
					fn.filters.addUrlParam({name:'a', value:a});
				}
			},

			makePrettyUrl: function() {
				fn.filters.getUrlParams();

				location.href = currentURL + '?' + fn.filters.url;
				return false;
			},

			addUrlParam: function(param) {
				if ( fn.filters.url != '' ) {
					fn.filters.url += '&';
				}

				fn.filters.url += param.name + '=' + param.value;

				return fn.filters.url;
			},
		},
	};

	return fn;
});