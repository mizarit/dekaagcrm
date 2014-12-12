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
  
  jQuery('#date').datepicker({});
  jQuery('#enddate').datepicker({});
  jQuery('#payedbefore').datepicker({});
  
  jQuery('#consumer').autocomplete({
    source: '/wp-admin/admin.php?page=dekaagcrm_consumers&action=suggest',
    delay: 500,
    minLength: 2,
    select: function(event, ui) {
      jQuery('#address').text(ui.item.address);
       jQuery('#consumer-id').val(ui.item.link);
       jQuery('.consumer-required').each(function(s,i) {
         jQuery(i).addClass('has-consumer');
       });
    }
  });
  
  
  if (typeof(jQuery('#consumer-id').val()) != 'undefined' && jQuery('#consumer-id').val() != '') {
    jQuery('.consumer-required').each(function(s,i) {
       jQuery(i).addClass('has-consumer');
     });
     jQuery('#allow_downpayment').change();
  }
  
});