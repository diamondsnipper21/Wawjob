/**
 * contact_info.js
 * Modified By KCG on 2017/3/9
 */

 define(['bootbox', 'select2', 'jcrop', 'jquery-form'], function (bootbox) {

 	var fn = {
 		$form: null,

 		init: function () {
 			$('.right-action-link a.edit-action').css('display', 'block');

 			this.accountSection.init();
 			// this.locationSection.init();
 			// this.invoiceAddressSection.init();
 			// this.contactSection.init();
 			this.companySection.init();

			Global.renderSelect2();
			Global.renderMaxlength();
 		},

 		// My Account
 		accountSection: {
 			$jcropCont: null,
    		$imageInfo: null,
    		$form: null,

 			init: function() {
 				this.$container = $('.account-section');

 				this.editModal.init();
 			},

	 		editModal: {
	 			$modal: null,
	 			$container: null,
	 			$form: null,

	 			init: function() {
	 				this.$container = $('.modal-edit-account');
	 				this.$form = $('#frm_edit_account', this.$container);
	 				
	 				this.bindEvents();

	 				this.render();
	 			},

	 			render: function() {
	 				$('#countryCode').trigger('change');

	 				this.$form.validate({
						messages: {
							email: {
								remote: trans.duplicated_email
							}
						}
	 				});
	 			},

	 			bindEvents: function() {
	 				var self = this;

	 				$('#is_company').off();
	 				$('#is_company').on('change', function() {
	 					if ( $(this).val() == '1' ) {
	 						$('.individual-fields').addClass('hidden');
	 						$('#firstName').val('Company');
	 						$('#lastName').val('User');
	 					} else {
	 						$('#firstName').val('');
	 						$('#lastName').val('');
	 						$('.individual-fields').removeClass('hidden');
	 					}
	 				});

	 				$('#countryCode').off();
	 				$('#countryCode').on('change', function() {
	 					var country_code = $('#countryCode option:selected').data('phone-prefix');
	 					$('.phone-input .input-group-addon').html('<img src="/assets/images/common/flags/' + $(this).val().toLowerCase() + '.png">&nbsp;&nbsp;+' + country_code);
	 				});

	 				this.$container.on('shown.bs.modal', function() {
	 					Global.renderInputMask();
	 					
		 				$('.control-value-avatar a').off('click');
						$('.control-value-avatar a').on('click', function() {
							var $self = $(this);
							var $form = $self.closest('form');
							var url   = $(this).attr('href');
							// bootbox.confirm(trans.confirm_removing_avatar, function(result) {
			    //                 if ( result ) {
									
			    //                 }
			    //             });
			    			$.ajax({
								url: url,
								type: 'DELETE',
								dataType: 'json',
								success: function(json) {
									Global.showAlertMessages(json.alerts);

			            			$('img', $self.parent()).attr('src', json.url);
			            			$('.user-avatar').attr('src', json.url);
			            			$self.remove();
								}
							});

							return false;
						});

	 					if ( $('#is_company').val() == '1' ) {
	 						$('.individual-fields').addClass('hidden');
	 					} else {
	 						$('.individual-fields').removeClass('hidden');
	 					}

						$('#firstName, #lastName').off('change, keyup');
						$('#firstName, #lastName').on('change, keyup', function() {
							var value = $(this).val();
							var old_value =$(this).data('value');

							if (old_value == '')
								return true;

							if (value != old_value)
								$('#note_require_id_verification').removeClass('hide');
							else
								$('#note_require_id_verification').addClass('hide'); 
						});
	 				});

			        // Handler when cancelling to upload avatar
			        $('.btn-upload-cancel').off('click');
			        $('.btn-upload-cancel').on('click', function() {
			        	$('#temp-avatar').html('');
			        	$(this).addClass('hide');
			        });

	 				// onchange event-handler
				    $('#avatar').on('change', function () {
				    	var $form = $('#frm_edit_account');
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
						            var src = '<img src="' + file.url + '" id="tempImage" />';
						            $('#temp-avatar').html(src);
						            $imageInfo = file.info;

						            $('#tempImage').Jcrop({
						              	bgFade:     true,
						              	bgOpacity: .2,
						              	setSelect: [ 130, 80, 130 + AVATAR_WIDTH, 80 + AVATAR_HEIGHT],
				              			aspectRatio: AVATAR_WIDTH / AVATAR_HEIGHT,
						              	onchange:   self.setCoords,
						              	onSelect:   self.setCoords,
						              	onRelease:  self.clearCoords,
						            }, function() {
						            	$jcropCont = this;
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
	 			},

	 			setCoords: function (c) {
			      	var xRatio = $imageInfo['width']/$('#temp-avatar img').width();
			      	var yRatio = $imageInfo['height']/$('#temp-avatar img').height();

			      	$('#x1').val(Math.round(c.x * xRatio));
			      	$('#y1').val(Math.round(c.y * yRatio));
			      	$('#w').val( Math.round(c.w * xRatio));
			      	$('#h').val( Math.round(c.h * yRatio));
			    },

			    clearCoords: function (c) {
				    $('#x1').val('');
				    $('#y1').val('');
				    $('#w').val('');
				    $('#h').val('');
			    },

	 			hide: function() {
	 				this.$container.modal('hide');
	 			}
	 		}
 		},

 		locationSection: {
 			init: function() {
 				this.$container = $('.location-section');

 				this.render();
 				this.bindEvents();

 				this.editModal.init();
 			},

 			render: function() {
 			},

 			bindEvents: function() {
 			},

	 		editModal: {
	 			$modal: null,
	 			$container: null,

	 			init: function() {
	 				this.$container = $('.modal-edit-location');
	 				this.$form = $('#frm_edit_location', this.$container);
	 				this.render();
	 				this.bindEvents();
	 				this.$form.validate();
	 			},

	 			render: function() {
	 			},

	 			bindEvents: function() {
	 				var self = this;

	 				// Event handler when clicking button for closing modal.
	 				$('.btn-cancel', this.$container).on('click', function() {
	 					self.hide();
	 				});
	 			},

	 			show: function() {
	 				this.$container.modal('show');
	 			},

	 			hide: function() {
	 				this.$container.modal('hide');
	 			}
	 		}
 		},

 		invoiceAddressSection: {
 			init: function() {
 				this.$container = $('.invoice-address-section');

 				this.render();
 				this.bindEvents();

 				this.editModal.init();
 			},

 			render: function() {
 			},

 			bindEvents: function() {
 			},

	 		editModal: {
	 			$modal: null,
	 			$container: null,

	 			init: function() {
	 				this.$container = $('.modal-edit-invoice-address');
	 				this.$form = $('#frm_edit_invoice_address', this.$container);
	 				this.render();
	 				this.bindEvents();
	 				this.$form.validate();
	 			},

	 			render: function() {
	 			},

	 			bindEvents: function() {
	 				var self = this;

	 				// Event handler when clicking button for closing modal.
	 				$('.btn-cancel', this.$container).on('click', function() {
	 					self.hide();
	 				});
	 			},

	 			show: function() {
	 				this.$container.modal('show');
	 			},

	 			hide: function() {
	 				this.$container.modal('hide');
	 			}
	 		}
 		},

 		// Company Contact
		contactSection: {
 			init: function() {
 				this.$container = $('.contact-section');
 		
 				this.bindEvents();
 				this.render();
 		
 				this.editModal.init();
 			},
 		
 			render: function() {
 				$('[name="country_code"]', this.$container).trigger('change');
 			},
 		
 			bindEvents: function() {
 				var self = this;

 				$('[name="country_code"]', this.$container).off();
 				$('[name="country_code"]', this.$container).on('change', function() {
 					var country_code = $('[name="country_code"] option:selected', self.$container).data('phone-prefix');
 					$('.phone-input .input-group-addon', self.$container).html('<img src="/assets/images/common/flags/' + $(this).val().toLowerCase() + '.png">&nbsp;&nbsp;+' + country_code);
 				});
 			},
 		
			editModal: {
				$modal: null,
				$container: null,

				init: function() {
					this.$container = $('.modal-edit-contact');
					this.$form = $('#frm_edit_contact', this.$container);
					
					this.bindEvents();
				},

				render: function() {
					this.$form.validate();
				},

				bindEvents: function() {
					var self = this;

					// Event handler when clicking button for closing modal.
					$('.btn-cancel', this.$container).on('click', function() {
						self.hide();
					});

					this.$container.on('shown.bs.modal', function() {
	 					self.render();
	 				});
				},

				show: function() {
					this.$container.modal('show');
				},

				hide: function() {
					this.$container.modal('hide');
				}
			}
 		},
 		
 		companySection: {
 			init: function() {
 				this.$container = $('.company-detail-section');
 		
 				this.render();
 				this.bindEvents();
 		
 				this.editModal.init();
 			},
 		
 			render: function() {
 			},
 		
 			bindEvents: function() {
 			},
 		
	 		editModal: {
	 			$modal: null,
	 			$container: null,

	 			init: function() {
	 				this.$container = $('.modal-edit-company');
	 				this.$form = $('#frm_edit_company', this.$container);
	 				this.bindEvents();
	 				this.$form.validate();

	 				this.render();
	 			},

	 			render: function() {
	 				Global.renderInputMask();
	 			},

	 			bindEvents: function() {
	 				var self = this;

	 				// Event handler when clicking button for closing modal.
	 				$('.btn-cancel', this.$container).on('click', function() {
	 					self.hide();
	 				});

	 				this.$container.on('shown.bs.modal', function() {
	 					self.render();
	 				});
	 			},

	 			show: function() {
	 				this.$container.modal('show');
	 			},

	 			hide: function() {
	 				this.$container.modal('hide');
	 			}
	 		}
 		}, 		
 	};

 	return fn;
});