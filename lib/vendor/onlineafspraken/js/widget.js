Widget = Class.create();
Widget.prototype =
{
  initialize: function (options)
  {

    this.options = {
    
    };
    
    Object.extend(this.options, options || {});
   
    this.init();
  },
  
  init: function()
  {
	  
  },
  
  showLoader: function()
  {
	  if($('widget-container-overlay').style.display != 'block') {
		  $('widget-container-overlay').style.display = 'block';
		  $('widget-container-overlay').style.width = $('widget-container').getWidth()+'px';
		  $('widget-container-overlay').style.height = $('widget-container').getHeight()+'px';
		  $('widget-container-overlay-inner').style.display = 'block';
		  $('widget-container-overlay-inner').style.paddingTop = (($('widget-container').getHeight() / 2) - 30) + 'px';
		  $('widget-container-overlay-inner').style.width = $('widget-container').getWidth()+'px';
		  $('widget-container-overlay-inner').style.height = $('widget-container').getHeight()+'px';
	  }
  },
  
  hideLoader: function()
  {
	  $('widget-container-overlay').style.display = 'none';
	  $('widget-container-overlay-inner').style.display = 'none';
	  
	  try {
	    iframeResizePipe();
	  }
	  catch(e) {}
  },
  
  update: function(what, where)
  { 
    //console.log(where);
    
      if(widget.get_url_parameter('code').length > 0) {
          this.handleLoginWithFacebook();
          return;
      }
	  c = 0;
	  for (i in this.options.appTypes) {
		  c++;
	  }
	  if (c < 2) {
		  $('widget-facility-container').style.display = 'none';
	  }
    else {
      $('widget-facility-container').style.display = 'block';
    }
	  if (what == 'facility') {
		  category = $('widget-facility').options[$('widget-facility').options.selectedIndex].innerHTML;
		  $('widget-apptype').options.length = 0;
		  
		  /*
		  calendar.setDate(new Date()); 
		  
		  m = (calendar.date.getMonth()+1);
      if (m < 10) m = '0'+m;
      
      d = calendar.date.getDate();
      if (d < 10) d = '0'+d;
      widget.options.selectedDate = calendar.date.getFullYear()+'-'+m+'-'+d;
      */
      //console.log(widget.options.selectedDate);
		 
		  /*opt = new Element('option');
			opt.value = -1;
			opt.innerHTML = 'Alle reizen';
			$('widget-apptype').insert(opt);
			  */
		  
		  for(i in this.options.appTypes[category]) {
			  opt = new Element('option');
			  opt.value = i;
			  opt.innerHTML = this.options.appTypes[category][i];
			  $('widget-apptype').insert(opt);
		  };
		  
		  if ($('widget-resource') && $('widget-resource').tagName.toLowerCase() != 'input') {
  		  $('widget-resource').options.length = 0;
  		  
  		  if (typeof(this.options.resources[category]) != 'undefined' && this.options.resources[category].length == 0) {
  			  opt = new Element('option');
  			  opt.value = 0;
  			  opt.innerHTML = i18nNoResources;
  			  $('widget-resource').insert(opt);
  		  }
  		  else {
  			  for(i in this.options.resources[category]) {
  				  opt = new Element('option');
  				  opt.value = i;
  				  opt.innerHTML = this.options.resources[category][i];
  				  $('widget-resource').insert(opt);
  			  };
  		  }
		  }
		  
		  // load times for first apptype of the selected facility
		  widget.update('apptype', 'load resources for first apptype of facility');
	  }


	  if (typeof(what) == 'undefined' || what == 'apptype') {
	  $('widget-resource').options.length = 0;

	  if (typeof(this.options.resources[$('widget-apptype').value]) != 'undefined' && this.options.resources[$('widget-apptype').value].length == 0) {
			  opt = new Element('option');
			  opt.value = 0;
			  opt.innerHTML = i18nNoResources;
			  $('widget-resource').insert(opt);
		  }
		  else {
			  for(i in this.options.resources[$('widget-apptype').value]) {
				  opt = new Element('option');
				  opt.value = i;
				  opt.innerHTML = this.options.resources[$('widget-apptype').value][i];
				  $('widget-resource').insert(opt);
			  };
		  }
	  }
	  /*
	  this.showLoader();
	  new Ajax.Request(
			  this.options.URL, 
			  {parameters:{
				  apptypeId: $('widget-apptype').value,
				  resourceId: -1,//$('widget-resource').value,
				  date: this.options.selectedDate
			  },
			  onComplete: function(transport) {
				  var tbody = $('booking-schedule-tbody');

				  var text = $('widget-facility')[$('widget-facility').selectedIndex].text;
				  tbody.innerHTML = '';
				  
				  for (s in transport.responseJSON.BookableDays) {
				  
            parts = s.split('-');
            if (parts.length != 3) continue;
            	
            if(parts[1].length == 1) parts[1] = '0'+parts[1];
            if(parts[2].length == 1) parts[2] = '0'+parts[2];
            
            date = parts[0]+'-'+parts[1]+'-'+parts[2];
            widget.options.selectedDate = date;
            
            var tr = new Element('tr');
            var td1 = new Element('td', {width:270, class: 'date-cell', colspan: 3});
            td1.innerHTML = transport.responseJSON.BookableDayStr[s];
            tr.insert(td1);
            tbody.insert(tr);
  					 
				    for (i in transport.responseJSON.BookableDays[s]) {
				      appTypeId = transport.responseJSON.BookableDays[s][i];
				      if (!(!isNaN(parseFloat(appTypeId)) && isFinite(appTypeId))) continue;
				      
  				    var tr = new Element('tr');
  				    
  				    var td2 = new Element('td', {width:100});
  				    var button = new Element('button', { id: 'date-'+date+'-'+appTypeId});
  				    Event.observe(button, 'click', function() {
  				      widget.options.selectedDate = this.id.substr(5, 10);
  				      widget.startConsumerData({
  							  category:  $('widget-facility').value, 
  							  apptypeId: this.id.substr(16), 
  							  resourceId: -1,
  							  date: widget.options.selectedDate
  						 });
  				    });
  				    button.innerHTML = 'Boek nu';
  				    td2.insert(button);
  				    
  				    var td3 = new Element('td');
  				    td3.innerHTML =  widget.options.appTypes[text][appTypeId];
  				    
  				    var td4 = new Element('td');
  				    td4.innerHTML = widget.options.duration[appTypeId]['Buffer']+' dagen';
  				    
  				    tr.insert(td2);
  				    tr.insert(td3);
  				    tr.insert(td4);
  				    tbody.insert(tr);
				    }
				  }
			   widget.hideLoader();
			  }});
			 */
  },
  
  selectDate: function(what, date, element)
  {
  	if (element.hasClassName('bookable')) {
  		this.options.selectedDate = date;
  		this.update(null, 'widget.js line 236');
  	}
  },
  
  startWizard: function()
  {
    
    this.showLoader();
    
    this.options.appTypeId = $('widget-apptype').value;
	  new Ajax.Updater(
			  'widget-container-inner',
		  this.options.URL, 
		  {parameters:
		  	{
			    step: 'wizard',
			    dob: $('dob') ? $('dob').value : null,
			    apptype: $('widget-apptype').value,
			    category:  $('widget-facility').value
		    },
		    evalScripts: true,
			  onComplete: function(transport) {
			  widget.hideLoader();
			}
		  }
	  );
  },
  
  handleWizard: function()
  {
    new Ajax.Request(
      this.options.URL,
      {
        parameters: {
          step: 'wizard',
          method: 'post'
        },
        onComplete: function(transport) {
          if (transport.responseJSON && transport.responseJSON.Status == 'OK')
          {
            widget.startCalendar(widget.options.bookingOptions);
          }
          else if(transport.responseJSON && transport.responseJSON.Status == 'FAILED')
          {
            $('form-errors').innerHTML = '';
            transport.responseJSON.Errors.each(function(s,i) {
              li = new Element('li');
              li.innerHTML = s.Error;
              $('form-errors').insert(li);
            });
            $('error-container').style.display = 'block';
            $('messages-container').style.display = 'none';
          }
        }
      }
    );
  },
  
  startCalendar: function(options)
  {
    this.options.appTypeId = $('widget-apptype').value;
    
    var params = {
		  step: 'calendar',
		  answers: {},
		  dob: $('dob') ? $('dob').value : null,
	    apptype: $('widget-apptype').value,
	    category:  $('widget-facility').value
    };
    /*jQuery('.form-row').each(function(s, i) {
      if(jQuery(i).css('display') == 'block') {
        v = jQuery("input[name='answer_"+i.id.substr(9) + "']:checked").val();
        q = i.id.substr(9);
        params['answer_'+q] = v;
      }
    });*/
    this.showLoader();
	  new Ajax.Updater(
			  'widget-container-inner',
		  this.options.URL, 
		  { parameters: params,
		    evalScripts: true,
			  onComplete: function(transport) {
	        widget.handleCalendar();
	  }
		  }
	  );
  },
  
  handleCalendar: function()
  {
    widget.showLoader();
    
    var params = {
		  apptypeId: widget.options.appTypeId,
		  resourceId: -1,//$('widget-resource').value,
		  date: widget.options.selectedDate
	  }
    
    jQuery('.form-row').each(function(s, i) {
      if(jQuery(i).css('display') == 'block') {
        v = jQuery("input[name='answer_"+i.id.substr(9) + "']:checked").val();
        if (typeof(v) == 'undefined') {
          v = jQuery("input[name='answer_"+i.id.substr(9) + "']").val();
          if (typeof(v) == 'undefined') {
            v = jQuery("select[name='answer_"+i.id.substr(9) + "']").val();
          }
        }
        q = i.id.substr(9);
        params['answer_'+q] = v;
      }
    });
          
    new Ajax.Request(
	  widget.options.URL, 
	  {parameters: params,
	  onComplete: function(transport) {
	    widget.hideLoader();
	    
		  var tbody = $('booking-schedule-tbody');

		  var text = $('widget-facility-text').value;
		  tbody.innerHTML = '';
		 
		  if(transport.responseJSON.BookableDays.length == 0) {
		    var tr = new Element('tr');
		    var td1 = new Element('td', {class: 'date-cell', colspan: 3});
        td1.innerHTML = 'Geen beschikbaarheid<br><span>Er zijn geen beschikbare datums die voldoen aan jouw wensen.</span>';
        tr.insert(td1);
        tbody.insert(tr);
		  }
		  else {
		   for (s in transport.responseJSON.BookableDays) {
		  
        parts = s.split('-');
        if (parts.length != 3) continue;
        	
        if(parts[1].length == 1) parts[1] = '0'+parts[1];
        if(parts[2].length == 1) parts[2] = '0'+parts[2];
        
        date = parts[0]+'-'+parts[1]+'-'+parts[2];
        widget.options.selectedDate = date;
        
        var tr = new Element('tr');
        var td1 = new Element('td', {class: 'date-cell', colspan: 3});
        td1.innerHTML = transport.responseJSON.BookableDayStr[s];
        tr.insert(td1);
        tbody.insert(tr);
				 
		    for (i in transport.responseJSON.BookableDays[s]) {
		      appTypeId = transport.responseJSON.BookableDays[s][i];
		      if (!(!isNaN(parseFloat(appTypeId)) && isFinite(appTypeId))) continue;
		      
			    var tr = new Element('tr');
			    
			    var td2 = new Element('td', {width:100});
			    var button = new Element('button', { id: 'date-'+date+'-'+appTypeId});
			    Event.observe(button, 'click', function() {
			      widget.options.selectedDate = this.id.substr(5, 10);
			      params = {
						  category:  $('widget-facility').value, 
						  apptypeId: this.id.substr(16), 
						  resourceId: -1,
						  date: widget.options.selectedDate
					 };
					 
					 jQuery('.form-row').each(function(s, i) {
            if(jQuery(i).css('display') == 'block') {
              v = jQuery("input[name='answer_"+i.id.substr(9) + "']:checked").val();
              if (typeof(v) == 'undefined') {
                v = jQuery("input[name='answer_"+i.id.substr(9) + "']").val();
                if (typeof(v) == 'undefined') {
                  v = jQuery("select[name='answer_"+i.id.substr(9) + "']").val();
                }
              }
              q = i.id.substr(9);
              params['answer_'+q] = v;
            }
          });
					 
			      widget.startConsumerData(params);
			    });
			    button.innerHTML = 'Boek nu';
			    td2.insert(button);
			    
			    var td3 = new Element('td');
			    td3.innerHTML =  widget.options.appTypes[text][appTypeId];
			    
			    var td4 = new Element('td', {width:100});
			    td4.innerHTML = widget.options.duration[appTypeId]['Buffer']+' dagen';
			    
			    tr.insert(td2);
			    tr.insert(td3);
			    tr.insert(td4);
			    tbody.insert(tr);
		    }
		  }
	  }
	  }});
  },
  
  startBooking: function(clear)
  {
		if (clear || this.get_url_parameter('code').length > 0) {
			window.location.href = window.location.pathname;
		}
		
	  this.showLoader();
	  new Ajax.Updater(
		  'widget-container-inner',
		  this.options.URL, 
		  {parameters:
		  	{
			  step: 'appointment'
		    },
		    evalScripts: true,
			onComplete: function(transport) {
			  widget.hideLoader();
			 
			}
		  }
	  ); 
  },
  
  startConsumerData: function(options)
  {
	  this.showLoader();
	  this.options.bookingOptions = options;
	  
	  params = options;
	  params['step'] = 'consumerData';
	  /*
	  console.log(options);
	  var params = {
			  category: options.category,
			  apptypeId: options.apptypeId,
			  resourceId: options.resourceId,
			  date: options.date,
			  time: options.time,
			  step: 'consumerData'
		};
		   
    jQuery('.form-row').each(function(s, i) {
      if(jQuery(i).css('display') == 'block') {
        v = jQuery("input[name='answer_"+i.id.substr(9) + "']:checked").val();
        if(typeof(v)=='undefined') {
          v = jQuery("input[name='answer_"+i.id.substr(9) + "']").val();
        }
        q = i.id.substr(9);
        params['answer_'+q] = v;
      }
    });
    */
    
	  new Ajax.Updater(
			'widget-container-inner',
		  this.options.URL, 
		  {
		    parameters: params,
		    evalScripts: true,
  			onComplete: function(transport) {
  			  widget.hideLoader();
  			}
		  }
	  );
  },
  
  handleConsumerData: function()
  {
    console.log('handle consumerData');
  },
  
  handleLogin: function()
  {
    new Ajax.Request(
      this.options.URL,
      {
        parameters: {
          username: $('username').value,
          password: $('password').value,
          step: 'login'
        },
        onComplete: function(transport) {
          if(transport.responseText == 'OK') {
            widget.startConsumerData(widget.options.bookingOptions);
          }
          else {
            $('error-container').style.display = 'block';
          }
        }
      }
    );
  },
  
  handleLoginWithFacebook: function()
  {
    new Ajax.Request(
      this.options.URL,
      {
        parameters: {
          url: window.location.href,
          step: 'loginWithFacebook',
          code: this.get_url_parameter('code')
        },
        onComplete: function(transport) {
          if(transport.responseJSON.Status == 'OK') {
            if(transport.responseJSON.Redirect) {
                window.location.href = transport.responseJSON.Redirect;
            }
            else {
                eval(transport.responseJSON.Callback);
            }
          }
        }
      }
    );
  },
  
  startPersona: function()
  {
	  this.showLoader();
	  new Ajax.Updater(
			  'widget-container-inner',
		  this.options.URL, 
		  {parameters:
		  	{
			  step: 'persona'
		    },
		    evalScripts: true,
			  onComplete: function(transport) {
			  widget.hideLoader();
			}
		  }
	  );
  },
  
  handlePersona: function()
  {
    new Ajax.Request(
      this.options.URL,
      {
        parameters: {
          first_name: $('PersonaFirstName').value,
          insertions: $('PersonaInsertions').value,
          last_name: $('PersonaLastName').value,
          gender: $('Gender').value,
          dob: $('Dob').value,
          remarks: $('Remarks').value,
          step: 'persona',
          method: 'post'
        },
        onComplete: function(transport) {
          if (transport.responseJSON && transport.responseJSON.Status == 'OK')
          {
            widget.startConsumerData(widget.options.bookingOptions);
          }
          else if(transport.responseJSON && transport.responseJSON.Status == 'FAILED')
          {
            $('form-errors').innerHTML = '';
            transport.responseJSON.Errors.each(function(s,i) {
              li = new Element('li');
              li.innerHTML = s.Error;
              $('form-errors').insert(li);
            });
            $('error-container').style.display = 'block';
            $('messages-container').style.display = 'none';
          }
        }
      }
    );
  },
  
  startPasswordReminder: function()
  {
	  this.showLoader();
	  new Ajax.Updater(
			  'widget-container-inner',
		  this.options.URL, 
		  {parameters:
		  	{
			  step: 'passwordReminder'
		    },
		    evalScripts: true,
			  onComplete: function(transport) {
			  widget.hideLoader();
			}
		  }
	  );
  },
  
  handlePasswordReminder: function()
  {
    new Ajax.Request(
      this.options.URL,
      {
        parameters: {
          email: $('email').value,
          step: 'passwordReminder'
        },
        onComplete: function(transport) {
          if (transport.responseJSON && transport.responseJSON.Status == 'OK')
          {
            $('form-messages').innerHTML = '';
            transport.responseJSON.Messages.each(function(s,i) {
              li = new Element('li');
              li.innerHTML = s.Message;
              $('form-messages').insert(li);
            });
            $('error-container').style.display = 'none';
            $('messages-container').style.display = 'block';
          }
          else if(transport.responseJSON && transport.responseJSON.Status == 'FAILED')
          {
            $('form-errors').innerHTML = '';
            transport.responseJSON.Errors.each(function(s,i) {
              li = new Element('li');
              li.innerHTML = s.Error;
              $('form-errors').insert(li);
            });
            $('error-container').style.display = 'block';
            $('messages-container').style.display = 'none';
          }
        }
      }
    );
  },
  
  startConfirm: function()
  {
    this.showLoader();
	  new Ajax.Updater(
	    'widget-container-inner',
		  this.options.URL,
		  {parameters:
		  	{
		  	  step: 'confirm',
  			  persona: $('persona') ? $('persona').value : null
		    },
		    evalScripts: true,
			  onComplete: function(transport) {
			  
			  if (transport.responseJSON && transport.responseJSON.Status == 'OK')
		    {
				  widget.startThankyou();
			  }
			  else if (transport.responseJSON && transport.responseJSON.Status == 'FAILED')
		      {
			    $('form-errors').innerHTML = '';
				transport.responseJSON.Errors.each(function(s,i) {
				  li = new Element('li');
				  li.innerHTML = s.Error;
				  $('form-errors').insert(li);
			    });
				$('error-container').style.display = 'block';
			  }
			  else {
				  $('widget-container-inner').innerHTML = transport.responseText;
			  }
			  
			  widget.hideLoader();
			}
		  }
	  );
  },
  
  handleConfirm: function()
  {
    var params = {
			  confirm: 1,
			  answers: {},
			  step: 'confirm'
		};
		   
    jQuery('.form-row').each(function(s, i) {
      if(jQuery(i).css('display') == 'block') {
        v = jQuery("input[name='answer_"+i.id.substr(9) + "']:checked").val();
        q = i.id.substr(9);
        params['answer_'+q] = v;
      }
    });
    
    new Ajax.Request(
      this.options.URL,
      {
        parameters: params,
        evalScripts: true,
        onComplete: function(transport) {
          if (transport.responseJSON && transport.responseJSON.Status == 'OK')
          {
              widget.startThankyou();
          }
          else if(transport.responseJSON && transport.responseJSON.Status == 'FAILED')
          {
            $('form-errors').innerHTML = '';
            transport.responseJSON.Errors.each(function(s,i) {
              li = new Element('li');
              li.innerHTML = s.Error;
              $('form-errors').insert(li);
            });
            $('error-container').style.display = 'block';
          }
        }
      }
    );
  },

  startThankyou: function()
  {
	  this.showLoader();
	  new Ajax.Updater(
		'widget-container-inner',
		this.options.URL, 
		{parameters:
		  {
		    step: 'thankyou'
		  },
		  evalScripts: true,
	      onComplete: function(transport) {
			widget.hideLoader();
		  }
		}
	  );
  },
  
  startRegister: function()
  {
	  this.showLoader();
	  new Ajax.Updater(
			  'widget-container-inner',
		  this.options.URL, 
		  {parameters:
		  	{
			  step: 'register'
		    },
		    evalScripts: true,
			onComplete: function(transport) {
			  widget.hideLoader();
			}
		  }
	  );
  },
  
  handleRegister: function()
  {
    this.showLoader();
	  
	params = Form.serialize('register-form', true);
	params.step = 'register';
	params.method = 'post';
	  
	new Ajax.Request(
      this.options.URL,
	  {
		parameters: params,
		evalScripts: true,
		onComplete: function(transport) {
		  
		  if(transport.responseJSON.Status == 'OK') {
		    widget.startConfirm();
		  }
		  else {
		    $('register-form').getElements().each(function(s,i) {
			  s.removeClassName('error');
		    });
			$('form-errors').innerHTML = '';
			transport.responseJSON.Errors.each(function(s,i) {
				li = new Element('li');
				li.innerHTML = s.Error;
				if ($(s.Field)) {
					$(s.Field).addClassName('error');
				}
				$('form-errors').insert(li);
			});
		    $('error-container').style.display = 'block';
		  }
		  
		  widget.hideLoader();
		}
	  });
  },
  
  startLogoff: function()
  {
	this.showLoader();
	  
	new Ajax.Request(
      this.options.URL,
      {
    	parameters: {
          step: 'logoff'
    	},
    	onComplete: function(transport) {
    	  widget.startConsumerData(widget.options.bookingOptions);
    	  widget.hideLoader();
    	}
      }
	);
  },
  
  get_url_parameter: function( param ){
    //param = param.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var r1 = "[\\?&]"+param+"=([^&#]*)";
    var r2 = new RegExp( r1 );
    var r3 = r2.exec( window.location.href );
    if( r3 == null ) { 
        return "";
    }
    else {return r3[1];
    }
  }
};

var widget = new Widget();