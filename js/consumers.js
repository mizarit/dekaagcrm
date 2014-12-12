function addPersona()
{
  personas++;
  if (personas < 11) {
    jQuery('#persona-container-'+personas).show();
    jQuery('#personas').val(personas);
    if (personas == 10) {
      jQuery('#add-persona-button').hide();
    }
  }
}

function addAccount(button)
{
  jQuery(button).hide();
  jQuery('.account-required').each(function(i, elem) {
    jQuery(elem).addClass('has-account');
  });
}

jQuery(document).ready(function() {
  jQuery(function($){
  	$.datepicker.regional.nl = {
  		closeText: 'Sluiten',
  		prevText: '←',
  		nextText: '→',
  		currentText: 'Vandaag',
  		monthNames: ['januari', 'februari', 'maart', 'april', 'mei', 'juni',
  		'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
  		monthNamesShort: ['jan', 'feb', 'mrt', 'apr', 'mei', 'jun',
  		'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
  		dayNames: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
  		dayNamesShort: ['zon', 'maa', 'din', 'woe', 'don', 'vri', 'zat'],
  		dayNamesMin: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
  		weekHeader: 'Wk',
  		dateFormat: 'dd-mm-yy',
  		firstDay: 1,
  		isRTL: false,
  		showMonthAfterYear: false,
  		yearSuffix: ''};
  	$.datepicker.setDefaults($.datepicker.regional.nl);
  });
  
  jQuery(diplomas).each(function(s,x) {
    for (i = 1; i < 11; i++) {
      jQuery('#persona_'+i+'_diploma_'+x+'_date').datepicker({});
    }
  });
    
});

function checkPasswordStrength( $pass1,
                                $pass2,
                                $strengthResult,
                                $submitButton,
                                blacklistArray ) {
    var pass1 = $pass1.val();
    var pass2 = $pass2.val();
 
    // Reset the form & meter
    //$submitButton.attr('disabled', 'disabled');
        
    $strengthResult.removeClass( 'short bad good strong' );
    
    blacklistArray = blacklistArray.concat( wp.passwordStrength.userInputBlacklist() )
    var strength = wp.passwordStrength.meter( pass1, blacklistArray, pass2 );
    switch ( strength ) {
        case 2:
            $strengthResult.addClass('bad').html( pwsL10n.bad );
            break;
 
        case 3:
            $strengthResult.addClass('good').html( pwsL10n.good );
            break;
 
        case 4:
            $strengthResult.addClass('strong').html( pwsL10n.strong );
            break;
 
        case 5:
            $strengthResult.addClass('short').html( pwsL10n.mismatch );
            break;
 
        default:
            $strengthResult.addClass('short').html( pwsL10n.short );
    }
 
    if ( 4 === strength && '' !== pass2.trim() ) {
        //$submitButton.removeAttr( 'disabled' );
    }
 
    return strength;
}
 
jQuery(document).ready(function($) {
  // Binding to trigger checkPasswordStrength
  $('body').on('keyup', 'input[name=password], input[name=password_retyped]',
    function(event) {
      checkPasswordStrength(
        $('input[name=password]'),         // First password field
        $('input[name=password_retyped]'), // Second password field
        $('#pass-strength-result'),           // Strength meter
        $('input[type=submit]'),           // Submit button
        []
      );
    }
  );
  
  $('input[type=submit]').removeAttr('disabled');
  
  $('body').on('keyup', 'input[name=password]',
    function(event) {
      $('.password-required').each(function(s,i) {
        $('#password').val() != '' ? $(i).addClass('has-password') : $(i).removeClass('has-password');
      });
    });
});