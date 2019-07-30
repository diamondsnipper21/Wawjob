define(['common', 'bootbox', 'tmpl', 'select2', 'ajax_page', 'cubeportfolio', 'jcrop', 'jquery-ui'], function (common, bootbox, tmpl) {

	var fn = {
		init: function() {
			this.$container = $('#my_profile_page, #profile_setup_page');

			this.swithEditMode(false);

			this.render();
			this.bindEvents();

			this.modal.init();
		},

		bindEvents: function() {
			var self = this;

			if ($('.disable-edit').length > 0) // Disable all actions for suspended
				return;

			// "Edit" button
			$('.edit-action, .edit-action').off('click');
			$('.edit-action, .btn-edit-action').on('click', function() {
				self.swithEditMode(true);
				return false;
			});

			// "Cancel" button
			$('.cancel-action').off('click');
			$('.cancel-action').on('click', function() {
				self.swithEditMode(false);
				return false;
			});

			// Add Button
			$('.add-item-action').off('click');
			$('.add-item-action').on('click', function() {
				var collection_var_name = $(this).closest('.page-content').attr('id');
				var var_name 			= $(this).closest('.page-content').data('var');
				
				var data = $(this).data('object');

                self.modal.open(data, var_name, collection_var_name);

                return false;
			});

			// Edit Button
			$('.edit-item-action').off('click');
			$('.edit-item-action').on('click', function() {
				var index = $(this).closest('.item').data('index');
				
				var collection_var_name = $(this).closest('.page-content').attr('id');
				var var_name 			= $(this).closest('.page-content').data('var');
				
				eval("var data = " + collection_var_name + "[" + index + "];");

                self.modal.open(data, var_name, collection_var_name);

                return false;
			});

			// Remove Button
			$('.remove-item-action').off('click');
			$('.remove-item-action').on('click', function() {
				var index = $(this).closest('.item').data('index');
				
				var collection_var_name = $(this).closest('.page-content').attr('id');
				var var_name 			= $(this).closest('.page-content').data('var');
				
				eval("var data = " + collection_var_name + "[" + index + "];");

				var url = $(this).attr('href');

				bootbox.confirm(trans['delete_confirm_' + var_name], function(result) {
                    if ( result ) {
						$.ajax({
							type: 'DELETE',
							url: url + '?' + 'var_name=' + var_name + '&collection_var_name=' + collection_var_name + '&id=' + data.id,
							dataType: 'json',
							success: function(json) {
								Global.showAlertMessages(json.alerts);

								if (!json.success)
									return;

								var $container 	= $('#' + collection_var_name);
								var $form 		= $('form', $container);

								// Don't use ajax page plugin if current page is on setup profile.
								if ($('.freelancer-step-content').length == 0)
			            			$container.ajaxPage({
			            				success: function() {
			            					fn.bindEvents();
			            					fn.modal.init();
			            				}
			            			});

		            			$('[name="_action"]', $form).val('refresh');
		            			$form.submit();
							}
						});
					}
				});

                return false;
			});

			$('.control-value-avatar a').off('click');
			$('.control-value-avatar a').on('click', function() {
				var $self = $(this);
				var $form = $self.closest('form');
				var url   = $(this).attr('href');
				bootbox.confirm(trans.confirm_removing_avatar, function(result) {
                    if ( result ) {
						$.ajax({
							url: url,
							type: 'DELETE',
							dataType: 'json',
							success: function(json) {
								Global.showAlertMessages(json.alerts);

		            			$('img', $self.parent()).attr('src', json.url);
		            			$('.header .user-avatar').attr('src', json.url);
		            			$self.remove();
							}
						});
                    }
                });

				return false;
			});

	        var duration = 500;
			// When scrolling, toolbar will fix statically.
			$(window).on('scroll', function() {
				self.handlerOnScroll($(this));
			});

	        $('.profile-sidebar a').off('click');
	        $('.profile-sidebar a').on('click', function(e) {
	        	var link = $(this).attr('href');
	        	if (link.indexOf('#') < 0)
	        		return true;

	            e.preventDefault();

	            if (link == '#about_me')
	            	$('html, body').animate({scrollTop: 0}, duration);
	            else
	            	$('html, body').animate({scrollTop: $(link).offset().top - 15}, duration);

	            return false;
	        });

	        // Handler when cancelling to upload avatar
	        $('.btn-upload-cancel').off('click');
	        $('.btn-upload-cancel').on('click', function() {
	        	$('.temp-avatar').html('');
	        	$(this).addClass('hide');
	        });

			// Handler when uploading avatar
			$('#avatar').off('change');
		    $('#avatar').on('change', function () {
		    	var $form = $('#about_me form');
		    	var url 	= $form.attr('action');

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

			            var files = $('[name="file_ids"]', $form).val();
						$.each(json.files, function(i, file) {
				            //show message detail result
				            var src = '<img src="' + file.url + '" class="temp-image" />';
				            $('.temp-avatar').html(src);
				            fn.imageInfo = file.info;

				            $('.temp-image').Jcrop({
				              	bgFade:     true,
				              	bgOpacity: .2,
				              	setSelect: [ 130, 80, 130 + AVATAR_WIDTH, 80 + AVATAR_HEIGHT],
				              	aspectRatio: AVATAR_WIDTH / AVATAR_HEIGHT,
				              	onchange:   self.setCoords,
				              	onSelect:   self.setCoords,
				              	onRelease:  self.clearCoords,
				            }, function() {
				            	fn.jcropCont = this;
				            });

				            files += '[' + file.id +']';
						});
						$('[name="file_ids"]', $form).val(files);

						$('.btn-upload-cancel').removeClass('hide');
		          	},

		          	error: function(xhr) {
			            console.log(xhr);
			        },

		          	dataType: 'json',
		    	});

		    	$form.attr('action', url);
		    });

		    $('#profile_rate').on('change', function() {
		    	$(this).val(Global.formatCurrency($(this).val()));
		    });
		},

		setCoords: function (c) {
	      	var xRatio = fn.imageInfo['width'] / $('.temp-avatar img').width();
	      	var yRatio = fn.imageInfo['height'] / $('.temp-avatar img').height();

	      	$('.x1').val(Math.round(c.x * xRatio));
	      	$('.y1').val(Math.round(c.y * yRatio));
	      	$('.w').val( Math.round(c.w * xRatio));
	      	$('.h').val( Math.round(c.h * yRatio));
	    },

	    clearCoords: function (c) {
		    $('.x1').val('');
		    $('.y1').val('');
		    $('.w').val('');
		    $('.h').val('');
	    },

		handlerOnScroll: function($this) {
			// Ignore this event for pages for setup profile.
			if ($('.freelancer-step-content').length != 0)
				return;

			if ($('.disable-edit').length > 0) // Disable all actions for suspended
				return;

	        var duration = 500;

			var scrolltop = $this.scrollTop();

			var $toolbar 		= $('.toolbar:not(.fixed)');
			var $fixed_toolbar 	= $('.toolbar.fixed');
			var is_hidden_fixed_toolbar = true;

			if (scrolltop < $toolbar.offset().top + $toolbar.height()) {
				$fixed_toolbar.fadeOut(250);
				$toolbar.css('visibility', 'visible');
			} else if (scrolltop > $('#portfolios').offset().top - 100) {
				$fixed_toolbar.fadeOut(250);
				$toolbar.css('visibility', 'visible');
			} else {
				var $buttons 		= $('.buttons', $toolbar);
				var $fixed_buttons 	= $('.buttons', $fixed_toolbar);

				$fixed_buttons.css('left', $buttons.offset().left + 15);
				$fixed_buttons.css('width', $buttons.width());

				$fixed_toolbar.fadeIn(250);
				$toolbar.css('visibility', 'hidden');

				is_hidden_fixed_toolbar = false;
			}

            // Side bar
            var $left_sidebar = $('.left-sidebar .page-content.fixed');
            var $right_sidebar = $('#my_profile_page');

            var $selected_menu = $('.profile-sidebar li.menu-item.active a');
            var $selectable_menu = $selected_menu;

            $('.profile-sidebar ul li.menu-item a').each(function(i) {
	        	var link = $(this).attr('href');
	        	if (link.indexOf('#') < 0)
	        		return true;

            	if (scrolltop > $(link).offset().top - 250 && scrolltop < $(link).offset().top + $(link).height()) {
            		// $(this).parent().addClass('active');
            		$selectable_menu = $(this);
            		return false;
            	}
            });

            // if current selected menus was changed, change new one as selected one.
            if (!$selectable_menu.is($selected_menu)) {
            	$('.profile-sidebar li.menu-item').removeClass('active');
            	$selectable_menu.parent().addClass('active');
            }

            var offset = is_hidden_fixed_toolbar?50:0;
            if ($right_sidebar.height() < scrolltop + $left_sidebar.outerHeight())
            	$left_sidebar.css('top', $right_sidebar.height() - ($left_sidebar.outerHeight()));
            else
            	$left_sidebar.css('top', Math.max(scrolltop - offset, 0));
		},

		render: function() {
			Global.renderMaxlength();
			Global.renderSelect2();
			
			this.renderDatatable();

			// Render fixed toolbar
			if ($('.toolbar.fixed').length == 0) {
				var $fixed_toolbar = $('.toolbar').clone();
				$('.toolbar').after($fixed_toolbar);
				$fixed_toolbar.addClass('fixed');
				$fixed_toolbar.hide();
			}

			// init validator
			this.renderValidator();

			// 
			$('.left-sidebar > .page-content').width($('.left-sidebar > .page-content').width());
			$('.left-sidebar > .page-content').addClass('fixed');

			this.handlerOnScroll($(window));

			// Initialize cubeportfolio
			if ($('#grid-container').data('cubeportfolio'))
				$('#grid-container').cubeportfolio('destroy');
            $('#grid-container').cubeportfolio({
            	singlePageInlineCallback: function() {
            		console.log(1);
            	},
            	singlePageCallback: function() {
            		console.log(2);
            	}
            });

			Global.renderUniform();
			Global.renderGoToTop();

			// Update avatar image on header bar when changing avatar image.
			var avatar_url = $('#about_me .user-avatar').attr('src');
			if (avatar_url)
				$('.header .user-avatar').attr('src', avatar_url);
		},

		renderDatatable: function() {
			var self = this;

			$('.page-content', this.$container).ajaxPage({
				success: function() {
					$('.modal.in').modal('hide');

					self.init();
				}
			});
		},

		renderValidator: function() {
			// Define Custom Validate
			$.validator.addMethod("pairDateCompare", function(value, element, params) {
				var compare_objects = $(element).data('pairdatecompare');
				var dates = compare_objects.split(',');

				if ($(dates[0]).val() == '' || (dates[1] != '' && $(dates[1]).val() == '') || (dates[2] != '' && $(dates[2]).val() == '') || (dates[3] != '' && $(dates[3]).val() == ''))
					return true;

				var left_dates_value = '';
				left_dates_value += $(dates[0]).val();

				if (dates[1] != '') {
					left_dates_value += '-';
					left_dates_value += ($(dates[1]).val().length == 1?'0' + $(dates[1]).val():$(dates[1]).val());
				}

				var right_dates_value = '';
				right_dates_value += $(dates[2]).val();

				if (dates[3] != '') {
					right_dates_value += '-';
					right_dates_value += ($(dates[3]).val().length == 1?'0' + $(dates[3]).val():$(dates[3]).val());
				}

				if (!$.validator.rule_blocked) {
					$.validator.rule_blocked = true;
					for (var i = 0; i < dates.length; i++) {
						if (dates[i] == '' || $(dates[i]).is(element)) {
							continue;
						}

						var validator = $(dates[i]).closest('form').validate();
						validator.check( $(dates[i]) );
					}
					$.validator.rule_blocked = null;
				}

				$(this).data('rule-blocked', null);

				if (params == 1) // greater
					return left_dates_value > right_dates_value;
				else if (params == -1)
					return left_dates_value < right_dates_value;
				else if (params == 0)
					return left_dates_value == right_dates_value;

			    return true; 
			}, function(params, $element) {
				return $element.data('msg-pairDateCompare');
			});

			$('form', this.$container).validate();
		},

		swithEditMode: function(edit_mode) {
			if (edit_mode)
				this.$container.addClass('edit-mode');
			else
				this.$container.removeClass('edit-mode');
		},

		modal: {
			init: function() {
				this.$modalContainer = $('#modal_container');
			},

			bindEvents: function() {
                var self = this;

                this.$modal.off('show.bs.modal');
                this.$modal.on('show.bs.modal', function() {
                    self.render();

                    var $fixed_toolbar 	= $('.toolbar.fixed');
                    $fixed_toolbar.fadeOut(250);
                });

                this.$modal.off('hide.bs.modal');
                this.$modal.on('hide.bs.modal', function() {
                });

                this.$form.off('submit');
                this.$form.on('submit', function() {
                	if (!$(this).valid())
                		return false;

                	$(this).ajaxSubmit({
                		dataType: 'json',
                		success: function(json) {
                			Global.showAlertMessages(json.alerts);

                			if (!json.success)
                				return;

                			var $container = $('#' + self.collection_var_name);

                			$('[name="_action"]', $('form', $container)).val('refresh');
                			$('form', $container).submit();
                		}
                	});

                    return false;
                });
			},

			render: function() {
				this.$form = $('form', this.$modal);

				$('[name="var_name"]', this.$form).val(this.var_name);
				$('[name="collection_var_name"]', this.$form).val(this.collection_var_name);

				this.$form.validate();
				
				Global.renderMaxlength();
				Global.renderSelect2();
				Global.renderInputMask();
				Global.renderUniform();

				// Portfolio Modal - Upload Image
                $('body').off('click', '#modal_portfolio .btn-save');
                $('body').on('click', '#modal_portfolio .btn-save', function() {
                	var $container = $('#modal_portfolio');
                	var $form 	   = $('form', $container);

                	if ($('input[type="file"]', $container).val() == '' && $('input[name="file_ids"]', $container).val() != '')
                		$('input[type="file"]', $container).attr('disabled', true);

                	$form.submit();
                });

                // Employment Modal - Check Present
                $('body').off('change', '#profile_employment_to_present');
                $('body').on('change', '#profile_employment_to_present', function() {
                	var this_month = new Date().getMonth() + 1;
                	var this_year  = new Date().getFullYear();

                	var disabled = $(this).prop('checked');
                	$('#profile_employment_to_month').prop('disabled', disabled);
                	$('#profile_employment_to_year').prop('disabled', disabled);

                	if (disabled) {
                		$('#profile_employment_to_month').val(this_month);
                		$('#profile_employment_to_year').val(this_year);

                		$('#profile_employment_to_month, #profile_employment_to_year').trigger('change');
                	}
                });

				// Portfolio Modal - Upload Image
                $('body').off('change', '#modal_portfolio input[type="file"]');
                $('body').on('change', '#modal_portfolio input[type="file"]', function() {
                	if ($(this).val() == '')
                		return true;

					if (!Global.validateUploadFile($(this)))
						return false;

                	var $container = $('#modal_portfolio');
                	var $form 	   = $('form', $container);
                	var url 	   = $form.attr('action');

                	$form.attr('action', config_file_uploads['url']);
					$form.ajaxSubmit({
						success: function(json) {
							if ( !json.success ) {
								Global.showAlertMessages(json.alerts);
								return false;
							}

							var files = '';
							$.each(json.files, function(i, file) {
								$('.portfolio-img').hide();

					            //show message detail result
					            var src = '<img src="' + file.url + '" class="temp-image" width="100%" height="100%"/>';
					            $('.temp-avatar', $container).html(src);
					            fn.imageInfo = file.info;

					            $('.temp-image', $container).Jcrop({
					              	bgFade:     true,
					              	bgOpacity: .2,
					              	setSelect: [ 130, 80, 130 + U_P_THUMB_WIDTH, 80 + U_P_THUMB_HEIGHT],
					              	aspectRatio: U_P_THUMB_WIDTH / U_P_THUMB_HEIGHT,
					              	onchange:   fn.setCoords,
					              	onSelect:   fn.setCoords,
					              	onRelease:  fn.clearCoords,
					            }, function() {
					            	fn.jcropCont = this;
					            });

					            files += '[' + file.id +']';
							});

							$('[name="file_ids"]', $container).val(files);
						},

						error: function(xhr) {
						},

						dataType: 'json',
					});

					$form.attr('action', url);
                });

                // Employment History
                $('#profile_employment_to_present').trigger('change');
			},

			open: function(data, var_name, collection_var_name) {
				tmpl.arg = var_name;
                var html = tmpl('tmpl_modal_' + var_name, data);

                this.var_name 				= var_name; // LIKE "portfolio"
                this.collection_var_name 	= collection_var_name; // LIKE "portfolioes"
                this.$modalContainer.html(html);
                this.$modal = $('#modal_' + var_name, this.$modalContainer);
                        
                this.render();
                this.bindEvents();

                this.$modal.modal();
			}
		}
	};

	return fn;
});