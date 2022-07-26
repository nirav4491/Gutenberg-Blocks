jQuery(function($) {
  'use strict';
  $.fn.loginFormJs = function() {
    var loginButton = $('#login_button');

    if (loginButton.closest('.login-form').length === 0) {
      return;
    }

    var emailInput = $('#id_email');
    var passwordInput = $('#id_password');
    var refererInput = $('#id_referer');
    var registerParagraph = $('<p />');
    var registerLabel = $('<label/>', {for: 'register_checkbox', text: performanceinCustom.register_checkbox_text});
    var registerCheckbox = $('<input/>', {type: 'checkbox', id: 'register_checkbox'});
    emailInput.parent().after(registerParagraph);
    registerCheckbox.appendTo(registerParagraph);
    registerLabel.appendTo(registerParagraph);

    var login_label = loginButton.val();
    var register_label = loginButton.attr('data-value');

    // Remove the password disabled attribute, as some browsers can leave it
    // behind on refresh.
    passwordInput.removeAttr('disabled');

    // Bind the register checkbox to turning the login form into a
    // form which forwards for registration.
    registerCheckbox.on('click', function() {
      if (registerCheckbox.is(':checked')) {
        passwordInput.attr('disabled', '');
        passwordInput.parent().addClass('disabled');
        loginButton.val(register_label);
        loginButton.attr('data-value', login_label);
      } else {
        passwordInput.removeAttr('disabled');
        passwordInput.parent().removeClass('disabled');
        loginButton.val(login_label);
        loginButton.attr('data-value', register_label);
      }

    });

    loginButton.on('click', function(event) {
      if ( !registerCheckbox.is(':checked')) {
        var isEmailInputFill = $.fn.customValidation(emailInput, 'error', performanceinCustom.this_field_required_text), isEmailValidation = $.fn.customEmailValidation(emailInput, 'error', performanceinCustom.email_input_validation_text),
          isPasswordFill = $.fn.customValidation(passwordInput, 'error', performanceinCustom.this_field_required_text);

        if ((true === isEmailInputFill && true === isEmailValidation) && true === isPasswordFill) {
          $.fn.loginFormAjax($('.login-form'));
          localStorage.removeItem('user_email');
        } else {
          return false;
        }
      } else {
        event.preventDefault();
        var urlParm = '';
        if ('' !== refererInput.val() && '' !== emailInput.val()) {
          urlParm = '?referer=' + encodeURIComponent(refererInput.val()) + '&email=' + encodeURIComponent(emailInput.val());
        } else if ('' !== refererInput.val()) {
          urlParm = '?referer=' + encodeURIComponent(refererInput.val()) + '&email=' + encodeURIComponent(emailInput.val());
        } else if ('' !== emailInput.val()) {
          urlParm = '?email=' + encodeURIComponent(emailInput.val());
        }

        window.location.href = '/account/register/' + urlParm;
      }
    });
  };

  $.fn.companyProfileEdit = function() {
    var companyProfileButton = $('#company_profile_button');

    var scSelectWoo = {
      escapeMarkup: function(m) {
        return m;
      },
      placeholder: 'Please select key services',
      maximumSelectionLength: 10,
    };
    $('#id_company_tags').selectWoo(scSelectWoo);

    if (companyProfileButton.closest('.company_profile_form').length === 0) {
      return;
    }

    companyProfileButton.on('click', function(event) {
      event.preventDefault();
      var companyNameInputFill = $('#id_company_name'),
        accountInputHidden = $('#id_account'),
        productInputHidden = $('#id_product'),
        isCompanyNameInputFill = $.fn.customValidation(companyNameInputFill, 'error', performanceinCustom.this_field_required_text),
        //isLogoInputFill = $.fn.customValidation($('#id_logo'), 'error', performanceinCustom.this_field_required_text, 'file');

        isLogoUploaded = $.fn.customValidation($(' #div_id_logo #id_logo_hidden'), 'error', performanceinCustom.this_field_required_text);

      if (true === isCompanyNameInputFill && true === isLogoUploaded) {
        $.fn.companyProfileFormAjax($('.company_profile_form'));
      } else {
        $('html, body').animate({
          scrollTop: $('.company_profile_form').offset().top,
        }, 2000);
      }
    });

  };

  $.fn.companyProfileFormAjax = function($this) {

    if ($('#id_is_active').is(':checked')) {
      var is_active = true;
    } else {
      var is_active = false;
    }

    var formData = new FormData();
    var account = $this.find('#id_account').val();
    var product = $this.find('#id_product').val();
    var company_id = $this.find('#id_company').val();
    var company_name = $this.find('#id_company_name').val();
    var company_email = $this.find('#id_company_email').val();
    var logo = $this.find('#id_logo');
    var company_description = tinyMCE.get($this.find('#id_company_description').attr('id')).getContent();
    var custom_header = $this.find('#id_custom_header');
    var website_url = $this.find('#id_website_url').val();
    var address1 = $this.find('#id_address1').val();
    var address2 = $this.find('#id_address2').val();
    var city = $this.find('#id_city').val();
    var postcode = $this.find('#id_postcode').val();
    var tags = $this.find('select#id_company_tags').val();
    var country = $this.find('#id_country').val();
    var telephone_number = $this.find('#id_telephone_number').val();
    var facebook_profile = $this.find('#id_facebook_profile').val();
    var twitter_profile = $this.find('#id_twitter_profile').val();
    var linkedin_profile = $this.find('#id_linkedin_profile').val();
    var founded_year = $this.find('#id_founded_year').val();
    var number_of_staff = $this.find('#id_number_of_staff').val();
    var client_testimonial_1 = $this.find('#id_client_testimonial_1').val();
    var client_testimonial_2 = $this.find('#id_client_testimonial_2').val();
    var client_testimonial_3 = $this.find('#id_client_testimonial_3').val();
    var security = $this.find('#company_profile_form_name').val();
    var is_logo_uploaded = $this.find('#div_id_logo #id_logo_hidden').val();
    var logoFile = logo[0].files[0];
    var customHeaderFile = custom_header[0].files[0];
    formData.append('account', account);
    formData.append('product', product);
    formData.append('company_id', company_id);
    formData.append('company_name', company_name);
    formData.append('company_email', company_email);
    formData.append('logo', logoFile);
    formData.append('company_description', company_description);
    formData.append('custom_header', customHeaderFile);
    formData.append('website_url', website_url);
    formData.append('address1', address1);
    formData.append('address2', address2);
    formData.append('city', city);
    formData.append('postcode', postcode);
    formData.append('tags', tags);
    formData.append('country', country);
    formData.append('telephone_number', telephone_number);
    formData.append('facebook_profile', facebook_profile);
    formData.append('twitter_profile', twitter_profile);
    formData.append('linkedin_profile', linkedin_profile);
    formData.append('founded_year', founded_year);
    formData.append('number_of_staff', number_of_staff);
    formData.append('client_testimonial_1', client_testimonial_1);
    formData.append('client_testimonial_2', client_testimonial_2);
    formData.append('client_testimonial_3', client_testimonial_3);
    formData.append('security', security);
    formData.append('is_active', is_active);
    formData.append('is_logo_uploaded', is_logo_uploaded);
    formData.append('action', 'company_profile_form');

    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      data: formData,
      contentType: false,
      processData: false,
      beforeSend: function() {
        $this.block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6,
          },
        });
      }, complete: function() {
        $this.unblock();
      }, success: function(response) {
        var result = JSON.parse(response);
        if (true === result.success) {
          location.reload();
        } else {
          $('<span>', {class: 'form-message error', text: result.msg}).prependTo($this);
          $('html, body').animate({
            scrollTop: $('.form-message').offset().top,
          }, 2000);
        }
      },
    });
  };

  /* Account Registration from After confirmation end */

  /* Account Registration from before confirmation start */

  $.fn.CompanyAccountRegisterBefore = function() {
    var CompanyAccountRegisterButtonBefore = $('#partner_account_register_button');

    if (CompanyAccountRegisterButtonBefore.closest('.partner_account_register_form').length === 0) {
      return;
    }

    CompanyAccountRegisterButtonBefore.on('click', function(event) {
      event.preventDefault();

      var emailInput = $('#id_email'), fulltName = $('#id_full_name'),
        company_name = $('#id_company_name'),
        isfulltNameFill = $.fn.customValidation(fulltName, 'error', performanceinCustom.this_field_required_text),
        isEmailInputFill = $.fn.customValidation(emailInput, 'error', performanceinCustom.this_field_required_text),
        isEmailValidation = $.fn.customEmailValidation(emailInput, 'error', performanceinCustom.email_input_validation_text),
        isCompanyNameInputFill = $.fn.customValidation(company_name, 'error', performanceinCustom.this_field_required_text);
      if (true === isfulltNameFill && true === isEmailInputFill && true === isEmailValidation && true === isCompanyNameInputFill) {
        $.fn.CompanyAccountRegisterFormAjaxBefore($('.partner_account_register_form'));
      }
    });

  };

  $.fn.CompanyAccountRegisterFormAjaxBefore = function($this) {

    var security = $this.find('#partner_account_register_form_name').val();
    var full_name = $this.find('#id_full_name').val();
    var partner_package_type = $this.find('#id_partner_package_type').val();
    var email = $this.find('#id_email').val();
    var company_name = $this.find('#id_company_name').val();
    var website_url = $this.find('#id_website_url').val();
    var company_biography = tinyMCE.get($this.find('#company_biography').attr('id')).getContent();

    var data = {
      'action': 'partner_account_registration_form',
      'security': security,
      'full_name': full_name,
      'email': email,
      'company_name': company_name,
      'website_url': website_url,
      'company_biography': company_biography,
      'partner_package_type': partner_package_type,
    };

    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      data: data,
      dataType: 'json',
      beforeSend: function() {
        $this.block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6,
          },
        });
      }, complete: function() {
        $this.unblock();
      }, success: function(response) {
        if (true === response.success) {
          $('.form-message').remove();
          $('<span>', {class: 'form-message success', html: response.msg}).prependTo($this);

          $('html, body').animate({
            scrollTop: $('.entry-content').offset().top,
          }, 2000);
          if ('undefined' !== typeof response.url) {
            window.location.href = response.url;
          }
          $('.form').css('display','none');
          $('.page-template-page-without-menu-template .entry-content').html(response.html);
          $this.trigger('reset');

        } else {
          $('.form-message').remove();
          $('<span>', {class: 'form-message error', html: response.msg}).prependTo($this);
          $('html, body').animate({
            scrollTop: $('.entry-content').offset().top,
          }, 2000);
        }
      },
    });
  };

  /* Account Registration from before confirmation end */

  $.fn.registrationFormJs = function() {
    var registrationButton = $('#registration_button');

    if (registrationButton.closest('.registration-form').length === 0) {
      return;
    }
    registrationButton.on('click', function(event) {
      event.preventDefault();
      var emailInput = $('#id_email'),
        passwordInput = $('#id_password'),
        confirmPasswordInput = $('#id_confirm_password'),
        firstName = $('#id_first_name'),
        lastName = $('#id_last_name'),
        isEmailInputFill = $.fn.customValidation(emailInput, 'error', performanceinCustom.this_field_required_text),
        isEmailValidation = $.fn.customEmailValidation(emailInput, 'error', performanceinCustom.email_input_validation_text),
        isPasswordFill = $.fn.customValidation(passwordInput, 'error', performanceinCustom.this_field_required_text),
        isConfirmPasswordFill = $.fn.customValidation(confirmPasswordInput, 'error', performanceinCustom.this_field_required_text),
        isFirstNameFill = $.fn.customValidation(firstName, 'error', performanceinCustom.this_field_required_text),
        isLastNameFill = $.fn.customValidation(lastName, 'error', performanceinCustom.this_field_required_text),
        isPasswordValidation = false,
        isPasswordValidationFill = false;

      if (true === isPasswordFill) {
        isPasswordValidationFill = $.fn.customPasswordValidation(passwordInput, 'error', performanceinCustom.this_field_required_text);
      }

      if (true === isPasswordFill && true === isPasswordValidationFill && true === isConfirmPasswordFill) {
        isPasswordValidation = $.fn.customMatchingValidation(passwordInput, confirmPasswordInput, 'error', 'Passwords do not match.');

      }
      if ((true === isEmailInputFill && true === isEmailValidation) && true === isPasswordValidation && true === isFirstNameFill && true === isLastNameFill) {
        $.fn.registrationFormAjax($('.registration-form'));
      }

    });
  };
  $.fn.competeProfileFormJs = function() {
    var completeProfileButton = $('#complete_profile_button');
    if (completeProfileButton.closest('.complete-profile-form').length === 0) {
      return;
    }
    var companyNameInput = $('#id_company_name');
    var demographicSelect = $('#id_demographic');

    completeProfileButton.on('click', function(event) {
      event.preventDefault();
      var isCompanyNameInputFill = $.fn.customValidation(companyNameInput, 'error', performanceinCustom.this_field_required_text),
        isDemographicSelect = $.fn.customValidation(demographicSelect, 'error', performanceinCustom.this_field_required_text);
      if (true === isCompanyNameInputFill && true === isDemographicSelect) {
        $.fn.completeProfileFormAjax($('.complete-profile-form'));
      }

    });
  };
  $.fn.recruiterFormJs = function() {
    var recruiterButton = $('#recruiter_button');
    if (recruiterButton.closest('.recruiter-form').length === 0) {
      return;
    }

    recruiterButton.on('click', function(event) {
      event.preventDefault();
      $.fn.recruiterFormAjax($('.recruiter-form'));
    });
  };
  $.fn.jobPackageFormJs = function() {
    var jobPackageButton = $('#job_package_button');

    if (jobPackageButton.closest('.job-package-form').length === 0) {
      return;
    }
    jobPackageButton.on('click', function(event) {
      $.fn.jobPackageFormAjax($('.job-package-form'));

    });
  };
  $.fn.saveJobFormJs = function() {
    var saveJobButton = $('#save_new_job');
    var previewJobButton = $('#preview_job');
    if ($('.datepicker').is(':visible')) {

      var dateArgs = {
        dateFormat: 'yy/mm/dd',
        minDate: 0,
      };
      if (performanceinCustom.job_min_limit_date) {
        var dateArgs2 = {minDate: '-' + performanceinCustom.job_min_limit_date + 'D'};
        dateArgs = $.extend(dateArgs, dateArgs2);
      }
      if ('undefined' !== typeof performanceinCustom.job_max_limit_date) {
        var dateArgs3 = {maxDate: '+' + performanceinCustom.job_max_limit_date + 'D'};
        dateArgs = $.extend(dateArgs, dateArgs3);
      }

      $('#id_closing_date').datepicker(dateArgs);
    }
    var scSelectWoo = {
      escapeMarkup: function(m) {
        return m;
      },
      placeholder: performanceinCustom.select2_categories_text,
      maximumSelectionLength: 2,
    };
    $('#id_categories').selectWoo(scSelectWoo).addClass('enhanced');
    if (saveJobButton.closest('.save-job-form').length === 0 || previewJobButton.closest('.save-job-form').length === 0) {
      return;
    }
    saveJobButton.on('click', function(event) {
      var isValidData = $.fn.jobSaveAndPreview();
      if (false !== isValidData) {
        $.fn.saveJobFormAjax($('.save-job-form'), isValidData);
      }

    });
    previewJobButton.on('click', function(event) {
      var isValidData = $.fn.jobSaveAndPreview(true);
      if (false !== isValidData) {
        $.fn.saveJobFormAjax($('.save-job-form'), isValidData);
      }
    });
  };
  $.fn.iForgotFormJs = function() {
    var iForgotButton = $('#iforgot_button');
    if (iForgotButton.closest('.iforgot-form').length === 0) {
      return;
    }
    iForgotButton.on('click', function(event) {
      var emailInput = $('#id_email'),
        isEmailInputFill = $.fn.customValidation(emailInput, 'error', performanceinCustom.this_field_required_text),
        isEmailValidation = $.fn.customEmailValidation(emailInput, 'error', performanceinCustom.email_input_validation_text);

      if ((true === isEmailInputFill && true === isEmailValidation)) {
        var data = {
          'id_email': emailInput.val(),
        };
        $.fn.iForgotFormAjax($('.iforgot-form'), data);

      }

    });
  };
  $.fn.passwordResetFormJs = function() {
    var passwordRestButton = $('#password_reset_button');
    if (passwordRestButton.closest('.passowrd-reset-form').length === 0) {
      return;
    }
    passwordRestButton.on('click', function(event) {
      var emailInputHidden = $('#id_email'),
        codeInputHidden = $('#id_code'),
        passwordInput = $('#id_password'),
        confirmPasswordInput = $('#id_confirm_password'),
        isPasswordFill = $.fn.customValidation(passwordInput, 'error', performanceinCustom.this_field_required_text),
        isConfirmPasswordFill = $.fn.customValidation(confirmPasswordInput, 'error', performanceinCustom.this_field_required_text),
        isPasswordValidation = false;

      if (true === isPasswordFill && true === isConfirmPasswordFill) {
        isPasswordValidation = $.fn.customMatchingValidation(passwordInput, confirmPasswordInput, 'error', performanceinCustom.password_match_input_text);
      }

      if (true === isPasswordFill && true === isPasswordValidation && true === isConfirmPasswordFill) {
        var data = {
          'id_email': emailInputHidden.val(),
          'id_code': codeInputHidden.val(),
          'id_password': passwordInput.val(),
          'id_confirm_password': confirmPasswordInput.val(),
        };
        $.fn.passwordResetFormAjax($('.passowrd-reset-form'), data);
      }
    });
  };
  $.fn.applyJobFormJs = function() {
    var jobApplyButton = $('#job_apply_button');

    if (jobApplyButton.closest('.job-apply-form').length === 0) {
      return;
    }
    jobApplyButton.on('click', function(event) {
      event.preventDefault();
      var userCV = $('#id_resume'),
        emailInput = $('#user_id_email'),
        coverDescription = $('#id_cover_description'),

        isCvInputFill = $.fn.customValidation(userCV, 'error', performanceinCustom.this_field_required_text),
        isEmailInputFill = $.fn.customValidation(emailInput, 'error', performanceinCustom.this_field_required_text),
        isEmailInputFillValidation = $.fn.customEmailValidation(emailInput, 'error', performanceinCustom.email_input_validation_text),
        isCoverDescriptionEditorFill = $.fn.customValidation(coverDescription, 'error', performanceinCustom.this_field_required_text);

      if ('' !== userCV.val() && true === isCvInputFill && true === isEmailInputFill && true === isEmailInputFillValidation && true === isCoverDescriptionEditorFill) {
        $.fn.applyJobFormAjax($('.job-apply-form'));
      }

    });
  };

  // Ajax callback function.
  $.fn.loginFormAjax = function($this) {

    var email = $this.find('#id_email').val();
    var password = $this.find('#id_password').val();
    var referer = $this.find('#id_referer').val();
    var security = $this.find('#login_form_name').val();
    var data = {
      'action': 'login_form',
      'email': email,
      'password': password,
      'referer': referer,
      'security': security,
    };
    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      data: data,
      dataType: 'json',
      beforeSend: function() {
        $this.block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6,
          },
        });
      }, complete: function() {
        $this.unblock();
      }, success: function(response) {
        if (true === response.success) {
          $('.form-message').remove();
          $('<span>', {class: 'form-message success', html: response.msg}).prependTo($this);
          if ('undefined' !== typeof response.url) {
            window.location.href = response.url;
          }

        } else {
          $('.form-message').remove();
          $('<span>', {class: 'form-message error', html: response.msg}).prependTo($this);
          $('html, body').animate({
            scrollTop: $('.form-message ').offset().top,
          }, 2000);
        }

      },
    });
  };
  $.fn.registrationFormAjax = function($this) {

    var email = $this.find('#id_email').val();
    var password = $this.find('#id_password').val();
    var confirmPassword = $this.find('#id_confirm_password').val();
    var firstName = $this.find('#id_first_name').val();
    var lastName = $this.find('#id_last_name').val();
    var security = $this.find('#registration_form_name').val();
    var data = {
      'action': 'registration_form',
      'email': email,
      'password': password,
      'confirm_password': confirmPassword,
      'first_name': firstName,
      'last_name': lastName,
      'security': security,
    };
    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      data: data,
      dataType: 'json',
      beforeSend: function() {
        $this.block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6,
          },
        });
      }, complete: function() {
        $this.unblock();
      }, success: function(response) {
        if (true === response.success) {
          $('.form-message').remove();
          localStorage.setItem('user_email', email);
          $('<span>', {class: 'form-message success', html: response.msg}).prependTo($this);
          if ('undefined' !== typeof response.url) {
            window.location.href = response.url;
          }
        } else {
          $('.form-message').remove();
          $('<span>', {class: 'form-message error', html: response.msg}).prependTo($this);
          $('html, body').animate({
            scrollTop: $('.form-message ').offset().top,
          }, 2000);
        }
      },
    });
  };
  $.fn.completeProfileFormAjax = function($this) {

    var companyName = $this.find('#id_company_name').val();
    var jobTitle = $this.find('#id_job_title').val();
    var demographic = $this.find('#id_demographic').val();
    var userEmail = $this.find('#user_email').val();
    var security = $this.find('#complete_profile_form_name').val();

    var data = {
      'action': 'complete_profile_form',
      'company_name': companyName,
      'job_title': jobTitle,
      'demographic': demographic,
      'user_email': userEmail,
      'security': security,
      'regions[]': [],
      'verticals[]': [],
      'topics[]': [],

    };
    $('input[name=\'regions[]\']:checked').each(function() {
      data['regions[]'].push($(this).val());
    });
    $('input[name=\'verticals[]\']:checked').each(function() {
      data['verticals[]'].push($(this).val());
    });
    $('input[name=\'topics[]\']:checked').each(function() {
      data['topics[]'].push($(this).val());
    });

    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      data: data,
      dataType: 'json',
      beforeSend: function() {
        $this.block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6,
          },
        });
      }, complete: function() {
        $this.unblock();
      }, success: function(response) {
        if (true === response.success) {
          $('.form-message').remove();

          $('<span>', {class: 'form-message success', html: response.msg}).prependTo($this);
          if ('undefined' !== typeof response.url) {
            window.location.href = response.url;
          }
        } else {
          $('.form-message').remove();
          $('<span>', {class: 'form-message error', text: response.msg}).prependTo($this);
          $('html, body').animate({
            scrollTop: $('.form-message ').offset().top,
          }, 2000);
        }
      },
    });
  };
  $.fn.recruiterFormAjax = function($this) {
    var formData = new FormData();
    var recruiterName = $this.find('#id_recruiter_name').val();
    var id_image = $this.find('#id_image');
    var security = $this.find('#recruiter_form_name').val();
    var individualFile = id_image[0].files[0];
    formData.append('recruiter_name', recruiterName);
    formData.append('image', individualFile);
    formData.append('security', security);
    formData.append('action', 'recruiter_form');
    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      data: formData,
      contentType: false,
      processData: false,
      beforeSend: function() {
        $this.block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6,
          },
        });
      }, complete: function() {
        $this.unblock();
      }, success: function(response) {
        var result = JSON.parse(response);
        if (true === result.success) {
          location.reload();
        } else {
          $('.form-message').remove();
          $('<span>', {class: 'form-message error', text: result.msg}).prependTo($this);
          $('html, body').animate({
            scrollTop: $('.form-message ').offset().top,
          }, 2000);
        }
      },
    });
  };
  $.fn.jobPackageFormAjax = function($this) {
    var jobPackageForm = $this.serialize();
    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      data: jobPackageForm,
      dataType: 'json',
      beforeSend: function() {
        $this.block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6,
          },
        });
      }, complete: function() {
        $this.unblock();
      }, success: function(response) {
        if (true === response.success) {
          $('.form-message').remove();
          $('<span>', {class: 'form-message success', html: response.msg}).prependTo($this);
          if ('undefined' !== typeof response.url) {
            window.location.href = response.url;
          }
        } else {
          $('.form-message').remove();
          $('<span>', {class: 'form-message error', text: response.msg}).prependTo($this);
          if ('undefined' !== typeof response.url) {
            window.location.href = response.url;
          } else {
            $('html, body').animate({
              scrollTop: $('.form-message ').offset().top,
            }, 2000);
          }

        }
      },
    });
  };
  $.fn.saveJobFormAjax = function($this, formData) {
    if ('' === $('#id_product').val()) {
      return false;
    }
    var data;
    if ($this.hasClass('has-edit-job')) {
      data = {
        'action': 'save_job_form',
        'security': $this.find('#save_edited_job_form_name').val(),
        'product_id': $this.find('#id_product').val(),
        'job_id': $this.find('#id_job').val(),
      };
    } else {
      data = {
        'action': 'save_job_form',
        'security': $this.find('#save_job_form_name').val(),
        'product_id': $this.find('#id_product').val(),
      };
    }
    var saveJobFormData = $.extend(formData, data);
    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      data: saveJobFormData,
      dataType: 'json',
      beforeSend: function() {
        $this.block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6,
          },
        });
      }, complete: function() {
        $this.unblock();
      }, success: function(response) {
        if (true === response.success) {
          $('.form-message').remove();
          if ('undefined' !== typeof response.preview) {
            var previewJobWin = window.open('', 'PerformanceIN Job Preview','noopener=true',true);
            previewJobWin.document.write(response.html);
            previewJobWin.document.close()
          } else {
            $('<span>', {class: 'form-message success', html: response.msg}).prependTo($this);
            if ('undefined' !== typeof response.url) {
              window.location.href = response.url;
            }
          }
        } else {
          $('.form-message').remove();
          $('<span>', {class: 'form-message error', text: response.msg}).prependTo($this);
          if ('undefined' !== typeof response.url) {
            window.location.href = response.url;
          } else {
            $('html, body').animate({
              scrollTop: $('.form-message ').offset().top,
            }, 2000);
          }
        }
      },
    });
  };

  $.fn.iForgotFormAjax = function($this, formData) {
    var data = {
      'action': 'iforgot_form',
      'security': $this.find('#iforgot_form_name').val(),
    };
    var iForgotFormData = $.extend(formData, data);
    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      data: iForgotFormData,
      dataType: 'json',
      beforeSend: function() {
        $this.block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6,
          },
        });
      }, complete: function() {
        $this.unblock();
      }, success: function(response) {
        if (true === response.success) {
          var formSelector = $('.form');
          $('form.iforgot-form').remove();
          $('<p>', {text: response.msg}).appendTo(formSelector);
          formSelector.addClass('iforgot-success');
        } else {
          $('.form-message').remove();
          $('<span>', {class: 'form-message error', text: response.msg}).prependTo($this);
          if ('undefined' !== typeof response.url) {
            window.location.href = response.url;
          } else {
            $('html, body').animate({
              scrollTop: $('.form-message ').offset().top,
            }, 2000);
          }
        }
      },
    });
  };
  $.fn.passwordResetFormAjax = function($this, formData) {
    var data = {
      'action': 'password_reset_form',
      'security': $this.find('#password_reset_form_name').val(),
    };
    var passwordResetFormData = $.extend(formData, data);
    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      data: passwordResetFormData,
      dataType: 'json',
      beforeSend: function() {
        $this.block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6,
          },
        });
      }, complete: function() {
        $this.unblock();
      }, success: function(response) {
        if (true === response.success) {
          $('.form-message').remove();
          $('.passowrd-reset-form').trigger('reset');
          $('<span>', {class: 'form-message success', html: response.msg}).prependTo($this);
          setTimeout(function() { window.location.href = response.url; }, 3000);
        } else {
          $('.form-message').remove();
          $('<span>', {class: 'form-message error', html: response.msg}).prependTo($this);
          if ('undefined' !== typeof response.url) {
            window.location.href = response.url;
          } else {
            $('html, body').animate({
              scrollTop: $('.form-message ').offset().top,
            }, 2000);
          }
        }
      },
    });
  };
  $.fn.applyJobFormAjax = function($this) {
    var formData = new FormData();
    var jobIDHidden = $this.find('#id_jobs').val();
    var emailIDHidden = $this.find('#id_email').val();
    var productIDHidden = $this.find('#id_product').val();
    var resume = $this.find('#id_resume');
    var emailInput = $this.find('#user_id_email').val();

    var coverDescription = $this.find('#id_cover_description').val();
    var security = $this.find('#job_apply_form_name').val();
    var individualFile = resume[0].files[0];
    formData.append('security', security);
    formData.append('action', 'apply_job_form');
    formData.append('job_id', jobIDHidden);
    formData.append('email_id', emailIDHidden);
    formData.append('email', emailInput);
    formData.append('resume', individualFile);
    formData.append('id_product', productIDHidden);
    formData.append('cover_description', coverDescription);

    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      data: formData,
      contentType: false,
      processData: false,
      beforeSend: function() {
        $this.block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6,
          },
        });
      }, complete: function() {
        $this.unblock();
      }, success: function(response) {
        var result = JSON.parse(response);
        if (true === result.success) {
          $('.form-message').remove();
          $('.job-apply-form').trigger('reset');
          $('<span>', {class: 'form-message success', html: result.msg}).prependTo($this);
          $('html, body').animate({
            scrollTop: $('.form-message ').offset().top - 80,
          }, 2000);
        } else {
          $('.form-message').remove();
          $('<span>', {class: 'form-message error', text: result.msg}).prependTo($this);
          $('html, body').animate({
            scrollTop: $('.form-message ').offset().top - 80,
          }, 2000);
        }
      },
    });
  };

  $.fn.customValidation = function(selector, finder, message, type = '') {
    selector.parent().find('span.error').remove();
    var selectorID = selector.attr('id');
    if ('' === selector.val() || '0' === selector.val() || null === selector.val()) {
      if ('select2' === type) {
        $('#div_' + selectorID).addClass('error');
        $('<span>', {class: finder, text: message}).insertBefore(selector.parent().find('#hint_id_categories'));
      } else if ('file' === type) {
        $('#div_' + selectorID).addClass('error');
        $('<span>', {class: finder, text: message}).insertAfter(selector.parent().find('.help-block'));
      } else {
        $('#div_' + selectorID).addClass('error');
        $('<span>', {class: finder, text: message}).insertAfter(selector);
      }
      return false;
    } else {
      $('#div_' + selectorID).removeClass('error');
      $('<span>', {class: finder, text: ''}).insertAfter(selector);
      return true;
    }
  };

  $.fn.customPasswordValidation = function(selector, finder, message, type = '') {
    var checkValidation = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/;
    var selectorID = selector.attr('id');
    if ( !checkValidation.test(selector.val())) {
      selector.parent().find('span.error').remove();
      $('<span>', {class: finder, text: ''}).insertAfter(selector);
      $('#div_' + selectorID).addClass('error');
      $('<span>', {class: finder, text: 'Password must have at least 8 characters with 1 uppercase letter, 1 digit and 1 speacial character (! ,@, #,..)'}).insertAfter(selector);
      return false;
    } else {
      $('#div_' + selectorID).removeClass('error');
      $('<span>', {class: finder, text: ''}).insertAfter(selector);
      return true;
    }
  };

  $.fn.customValidatePhone = function(selector, finder, message) {
    var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
    var selectorID = selector.attr('id');
    if ( !filter.test(selector.val())) {
      $('#div_' + selectorID).addClass('error');
      $('<span>', {class: finder, text: message}).insertAfter(selector);
      return false;
    } else {
      $('#div_' + selectorID).removeClass('error');
      $('<span>', {class: finder, text: ''}).insertAfter(selector);
      return true;
    }
  };
  $.fn.customMultiSelectValidation = function(selector, finder, message) {
    if (selector.val()) {
      var selectorID = selector.attr('id');
      selector.parent().find('span.error').remove();
      if (2 < selector.find('option:selected').length) {
        $('#div_' + selectorID).addClass('error');
        $('<span>', {class: finder, text: message}).insertAfter(selector);
        return false;
      } else {
        $('#div_' + selectorID).removeClass('error');
        $('<span>', {class: finder, text: ''}).insertAfter(selector);
        return true;
      }
    } else {
      return false;
    }
  };
  $.fn.customWPEditorValidation = function(selector, finder, message) {
    selector.parent().find('span.error').remove();
    var selectorID = selector.attr('id');
    if ('' === tinyMCE.get(selector.attr('id')).getContent()) {
      $('#div_' + selectorID).addClass('error');
      $('<span>', {class: finder, text: message}).insertAfter(selector);
      return false;
    } else {
      $('#div_' + selectorID).removeClass('error');
      $('<span>', {class: finder, text: ''}).insertAfter(selector);
      return true;
    }
  };
  $.fn.customEmailValidation = function(selector, finder, message) {
    if ('' !== selector.val()) {
      var selectorID = selector.attr('id');
      var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      if ( !regex.test(selector.val())) {
        selector.parent().find('span.error').remove();
        $('#div_' + selectorID).addClass('error');
        $('<span>', {class: finder, text: message}).insertAfter(selector);
        return false;
      } else {
        $('#div_' + selectorID).removeClass('error');
        selector.parent().find('span.error').remove();
        $('<span>', {class: finder, text: ''}).insertAfter(selector);
        return true;
      }
    }
  };
  $.fn.customMatchingValidation = function(selector1, selector2, finder, message) {
    var selectorID = selector1.attr('id');
    if (selector1.val() !== selector2.val()) {
      selector1.parent().find('span.error').remove();
      $('#div_' + selectorID).addClass('error');
      $('<span>', {class: finder, text: message}).insertAfter(selector1);
      return false;
    } else {
      $('#div_' + selectorID).removeClass('error');
      selector1.parent().find('span.error').remove();
      $('<span>', {class: finder, text: ''}).insertAfter(selector1);
      return true;
    }
  };
  $.fn.customMinimumNumberValidation = function(selector, finder, message, minNum = 0) {
    var selectorID = selector.attr('id');
    selector.parent().find('span.error').remove();
    if ('' !== selector.val()) {
      if (parseInt(minNum) > parseInt(selector.val())) {
        $('#div_' + selectorID).addClass('error');
        $('<span>', {class: finder, text: message}).insertAfter(selector);
        return false;
      } else {
        $('#div_' + selectorID).removeClass('error');
        $('<span>', {class: finder, text: ''}).insertAfter(selector);
        return true;
      }
    } else {
      return true;
    }
  };

  $.fn.sendActivationLink = function() {
    var sendActivationLink = $('.send_activation_link');
    sendActivationLink.on('click', function(e) {
      e.preventDefault();
      var user_email = localStorage.getItem('user_email');
      if (user_email) {
        window.location.href = this.href + '&user-email=' + user_email;
      }
    });
  };

  $.fn.fileUploadValidation = function() {
    var selector = $('#id_resume');
    selector.on('change', function() {
      selector.parent().find('span.error').remove();
      var selectorID = selector.attr('id');
      var finder = 'error';
      var fileExt = $.fn.fileExtValidate(this);
      var fileSize = $.fn.fileSizeValidate(this);
      if (false === fileExt && false === fileSize) {
        $('#div_' + selectorID).addClass('error');
        $('<span>', {class: finder, text: 'This file is not allowed. Try again with PDF, DOC, DOCX file.'}).insertAfter(selector);
        return false;
      } else if (false === fileExt) {
        $('#div_' + selectorID).addClass('error');
        $('<span>', {class: finder, text: 'This file is not allowed. Try again with PDF, DOC, DOCX file.'}).insertAfter(selector);
        return false;
      } else if (false === fileSize) {
        $('#div_' + selectorID).addClass('error');
        $('<span>', {class: finder, text: 'Maximum file size exceed, Maximum file size 10240 kb allow.'}).insertAfter(selector);
        return false;
      } else {
        $('#div_' + selectorID).removeClass('error');
        $('<span>', {class: finder, text: ''}).insertAfter(selector);
        return true;
      }
    });
  };

  $.fn.companyLogValidation = function() {
    $('#id_logo').on('change', function() {
      $(this).parent().find('span.error').remove();
    });
  };
  $.fn.companyCustomHeaderValidation = function() {
    $('#id_custom_header').on('change', function() {
      $(this).parent().find('span.error').remove();
    });
  };
  $.fn.companyLogValidation();
  $.fn.companyCustomHeaderValidation();
  var validExt = '.pdf, .doc, .docx';
  $.fn.fileExtValidate = function(fileData) {
    var filePath = fileData.value;
    var getFileExt = filePath.substring(filePath.lastIndexOf('.') + 1).toLowerCase();
    var pos = validExt.indexOf(getFileExt);
    if (pos < 0) {
      return false;
    } else {
      return true;
    }
  };
  var maxSize = '10240';
  $.fn.fileSizeValidate = function(fileData) {
    if (fileData.files && fileData.files[0]) {
      var fsize = fileData.files[0].size / 1024;
      if (fsize > maxSize) {
        return false;
      } else {
        return true;
      }
    }
  };

  $.fn.loginFormJs();
  $.fn.registrationFormJs();
  $.fn.competeProfileFormJs();
  $.fn.recruiterFormJs();
  $.fn.jobPackageFormJs();
  $.fn.saveJobFormJs();
  $.fn.iForgotFormJs();
  $.fn.passwordResetFormJs();
  $.fn.applyJobFormJs();
  $.fn.companyProfileEdit();
  $.fn.CompanyAccountRegisterBefore();

  $.fn.sendActivationLink();
  $.fn.fileUploadValidation();

  $.fn.jobSaveAndPreview = function(isPreview = false) {
    var jobTitleInput = $('#id_job_title'),
      jobTypeSelect = $('#id_job_type'),
      jobLengthSelect = $('#id_job_length'),
      jobAreaInput = $('#id_job_area'),
      jobDescriptionEditor = $('#id_job_description'),
      minimumSalaryInput = $('#id_minimum_salary'),
      maximumSalaryInput = $('#id_maximum_salary'),
      categoriesSelect = $('#id_categories'),
      closingDateInput = $('#id_closing_date'),
      contactPhoneInput = $('#id_contact_phone'),
      contactEmailInput = $('#id_contact_email'),
      streetAddressEditor = $('#id_street_address'),
      postCodeInput = $('#id_post_code'),
      addressRegionInput = $('#id_address_region'),
      addressCountryInput = $('#id_address_country'),
      isMinMaxiSalaryInputFill = false,
      isPhoneValidationFill = false,
      isJobTitleInputFill = $.fn.customValidation(jobTitleInput, 'error', performanceinCustom.this_field_required_text),
      isJobTypeSelectFill = $.fn.customValidation(jobTypeSelect, 'error', performanceinCustom.this_field_required_text),
      isJobLengthSelectFill = $.fn.customValidation(jobLengthSelect, 'error', performanceinCustom.this_field_required_text),
      isJobAreaInputFill = $.fn.customValidation(jobAreaInput, 'error', performanceinCustom.this_field_required_text),
      isJobDescriptionEditorFill = $.fn.customWPEditorValidation(jobDescriptionEditor, 'error', performanceinCustom.this_field_required_text),
      isMinimumSalaryInputFill = $.fn.customMinimumNumberValidation(minimumSalaryInput, 'error', performanceinCustom.minimum_salary_input_text),
      isMaximumSalaryInputFill = $.fn.customMinimumNumberValidation(maximumSalaryInput, 'error', performanceinCustom.maximum_salary_input_text),
      isCategoriesSelectFill = $.fn.customValidation(categoriesSelect, 'error', performanceinCustom.this_field_required_text, 'select2'),
      isCategoriesSelectValidationFill = $.fn.customMultiSelectValidation(categoriesSelect, 'error', performanceinCustom.select2_categories_limit_text),
      isClosingDateInputFill = $.fn.customValidation(closingDateInput, 'error', performanceinCustom.this_field_required_text),
      isContactPhoneInputFill = $.fn.customValidation(contactPhoneInput, 'error', performanceinCustom.this_field_required_text),
      isContactEmailInputFill = $.fn.customValidation(contactEmailInput, 'error', performanceinCustom.this_field_required_text),
      isContactEmailInputValidation = $.fn.customEmailValidation(contactEmailInput, 'error', performanceinCustom.email_input_validation_text);

    if (true === isContactPhoneInputFill) {
      isPhoneValidationFill = $.fn.customValidatePhone(contactPhoneInput, 'error', performanceinCustom.valid_phone_number_input_text);
    }

    if (true === isMaximumSalaryInputFill) {
      isMinMaxiSalaryInputFill = $.fn.customMinimumNumberValidation(maximumSalaryInput, 'error', performanceinCustom.camper_salary_input_text, minimumSalaryInput.val());
    }
    if (true === isJobTitleInputFill &&
      true === isJobTypeSelectFill &&
      true === isJobLengthSelectFill &&
      true === isJobAreaInputFill &&
      true === isJobDescriptionEditorFill &&
      true === isMinimumSalaryInputFill &&
      true === isMaximumSalaryInputFill &&
      true === isMinMaxiSalaryInputFill &&
      true === isClosingDateInputFill &&
      (true === isCategoriesSelectFill && true === isCategoriesSelectValidationFill) &&
      (true === isContactPhoneInputFill && true === isPhoneValidationFill) &&
      (true === isContactEmailInputFill && true === isContactEmailInputValidation)
    ) {
      if (true === isPreview) {
        return {
          'job_title': jobTitleInput.val(),
          'job_type': jobTypeSelect.val(),
          'job_length': jobLengthSelect.val(),
          'job_area': jobAreaInput.val(),
          'job_description': tinyMCE.get(jobDescriptionEditor.attr('id')).getContent(),
          'minimum_salary': minimumSalaryInput.val(),
          'maximum_salary': maximumSalaryInput.val(),
          'categories': categoriesSelect.val(),
          'closing_date': closingDateInput.val(),
          'contact_phone': contactPhoneInput.val(),
          'contact_email': contactEmailInput.val(),
          'is_preview': true,
          'security_preview': $('#preview_job_form_name').val(),
        };
      } else {
        return {
          'job_title': jobTitleInput.val(),
          'job_type': jobTypeSelect.val(),
          'job_length': jobLengthSelect.val(),
          'job_area': jobAreaInput.val(),
          'job_description': tinyMCE.get(jobDescriptionEditor.attr('id')).getContent(),
          'minimum_salary': minimumSalaryInput.val(),
          'maximum_salary': maximumSalaryInput.val(),
          'categories': categoriesSelect.val(),
          'closing_date': closingDateInput.val(),
          'contact_phone': contactPhoneInput.val(),
          'contact_email': contactEmailInput.val(),
          // 'street_address': tinyMCE.get(streetAddressEditor.attr('id')).getContent(),
          'street_address': streetAddressEditor.val(),
          'post_code': postCodeInput.val(),
          'address_region': addressRegionInput.val(),
          'address_country': addressCountryInput.val(),
        };
      }
    }
    return false;
  };

  $(document).on('click', '.pi_endless_more', function() {
    var page = $(this).attr('data-page');
    var data = {
      action: $(this).attr('data-action'),
      security: $(this).attr('data-security'),
      class: $(this).attr('data-class'),
      extra_fields: $(this).attr('data-extra-fields'),
      paged: page,
      search: $('#page_att_search').data('search'),
    };
    $.fn.loadMoreWithPaginationAjax($(this), data, page, true);
  });
  $(document).on('scroll resize', function() {
    // if ($(window).scrollTop() + $(window).height() + 30 > $(document).height()) {
    var elementTop = $('.site-footer').offset().top;
    var elementBottom = elementTop + $('.site-footer').outerHeight();
    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();
    if (elementBottom > viewportTop && elementTop < viewportBottom) {
      var lessMoreSelector = $('.pi_endless_more'),
        page = lessMoreSelector.attr('data-page');
      if ('on' === lessMoreSelector.attr('data-loading')) {
        lessMoreSelector.attr('data-loading', 'off');
        var data = {
          action: lessMoreSelector.attr('data-action'),
          security: lessMoreSelector.attr('data-security'),
          class: lessMoreSelector.attr('data-class'),
          extra_fields: lessMoreSelector.attr('data-extra-fields'),
          paged: page,
          search: $('#page_att_search').data('search'),
        };
        $.fn.loadMoreWithPaginationAjax(lessMoreSelector, data, page);
      }
    }
  });

  /**
   * Load more and pagination Ajax
   * @param $this
   * @param data
   * @param page
   * @param is_click
   */
  $.fn.loadMoreWithPaginationAjax = function($this, data, page, is_click = false) {
    $.ajax({
      type: 'POST',
      url: performanceinCustom.admin_url,
      dataType: 'json',
      data: data,
      beforeSend: function() {
        $('.pi_endless_loading').css('display', 'block');
        $this.css('display', 'none');
      }, complete: function() {
        $('.pi_endless_loading').css('display', 'none');
      }, success: function(response) {
        $('.pi_endless_loading').css('display', 'none');
        $this.css('display', 'block');
        if (true === response.success) {

          if ('undefined' !== typeof response.html) {
            $(response.html).appendTo($('.pi_listing'));
          }
          if ('undefined' !== typeof response.pagination_html) {
            $('nav.pagination-box').replaceWith(response.pagination_html);
          }
        }
        $('.pi_endless_page_link').removeClass('disabled_page');
        $('.page_number_' + page).addClass('disabled_page');
        $this.attr('data-loading', 'on');
        page++;
        if (page % 2 !== 0) {
          $this.attr('data-loading', 'off');
        }
        if (page > $this.attr('data-total_pages')) {
          $this.css('display', 'none');
          $('.pi_endless_container').remove();
          if (true === is_click) {
            $this.attr('data-loading', 'off');
          }
        } else {
          $this.attr('data-page', page);
        }
      },
    });
  };




  jQuery(document).ready(function() {
    jQuery('div#js-tab-testimonials').removeClass('current');
    jQuery('div#js-tab-profile').addClass('current');
  });
  jQuery(document).on('click', 'a[data-tab="js-tab-testimonials"]', function() {
    jQuery('div#js-tab-profile').removeClass('current');
    jQuery('div#js-tab-testimonials').addClass('current');
  });
  jQuery(document).on('click', 'a[data-tab="js-tab-profile"]', function() {
    jQuery('div#js-tab-testimonials').removeClass('current');
    jQuery('div#js-tab-profile').addClass('current');
  });
  jQuery(document).on('click', '#job_toogle_form', function() {
    jQuery(this).remove();
    jQuery('.jobapplication').toggle('slow', function() {

    });
  });
  jQuery(document).on('click', '#js_toggle_subscription_info', function() {
    jQuery('#subscription_info').toggle('slow');
  });
  jQuery(document).on('change', '#div_id_logo #id_logo', function() {
    jQuery('#div_id_logo #id_logo_hidden').val('true');
  });

});

