jQuery(function($) {
  'use strict';

  $('#pi_partner_is_conform_id').on('click', 'input[type=checkbox]', function() {
    if ($(this).is(':checked')) {
      var pi_input_hidden = $("<input>",{'type':'hidden','name':'pi_partner_hidden_confirm_check','id':'pi_partner_hidden_confirm_check','class':'pi_partner_hidden_confirm_class','value':'true'});
      pi_input_hidden.appendTo($('#pi_partner_is_conform_id'));
    } else {
      $('#pi_partner_is_conform_id').find('.pi_partner_hidden_confirm_class').remove();
    }
  });
});
