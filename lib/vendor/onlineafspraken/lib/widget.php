<?php
/*
* This file is part of the OnlineAfspraken.nl Widget package
* (c) 2011 OnlineAfspraken <info@onlineafspraken.nl>
*
* @author Ricardo Matters <ricardo.matters@mizar-it.nl>
*
*/

require_once(dirname(__FILE__).'/../config/config.php');
require_once(dirname(__FILE__).'/widgetCore.php');
/**
 * 
 * Implements a basic Widget with all required features to book an appointment
 * 
 * @author Ricardo Matters <ricardo.matters@mizar-it.nl>
 *
 */
class Widget extends WidgetCore
{

  /**
	 * 
	 * Handles the first screen of the widget, i.e. selecting an apptype, resource, date and time
	 */
  public function executeAppointment()
  {
    $categories = array();
    $appTypesArr = array();
    $resourcesArr = array();

    $appointmentTypes = $this->sendRequest('getAppointmentTypes', array());
    $firstCategory = false;

    $resourceIds = array();

    $apptypeDurations = array();

    if (isset($_SESSION['booking']['answers'])) {
      unset($_SESSION['booking']['answers']);
    }
    
    if (isset($appointmentTypes['AppointmentType'])) {
      foreach ($appointmentTypes['AppointmentType'] as $key => $appointmentType) {
        if (!(bool)$appointmentType['CanBeBookedByConsumer']) {
          unset($appointmentTypes[$key]);
        }
        else {
          $categoryName = ucfirst(str_replace('&', '**', str_replace('_', ' ',trim($appointmentType['Category'], '~'))));
          $appTypesArr[$categoryName][$appointmentType['Id']] = $appointmentType['Name'];
          $categories[$categoryName] = true;
          if (!$firstCategory) {
            $firstCategory = $categoryName;
          }

          if ($appointmentType['SchedulingType'] != 'capacity') {
            $resourceIdsTmp = explode(',', $appointmentType['ResourceId']);
            foreach ($resourceIdsTmp as $id) {
              $resourceIds[$id] = true;
            }
          }

          $apptypeDurations[$appointmentType['Id']] = array('Duration' => $appointmentType['Duration'], 'Buffer' => $appointmentType['Buffer']);
        }
      }
    }

    $categories = array_keys($categories);

    $resources = $this->sendRequest('getResources');

    $resources = isset($resources['Resource']) ? $resources['Resource'] : array();
    foreach ($resources as $resource) {
      if (isset($resourceIds[$resource['Id']])) {
        foreach ($appointmentTypes['AppointmentType'] as $appointmentType) {
          if (in_array($resource['Id'], explode(',', $appointmentType['ResourceId']))) {
            $resourcesArr[$appointmentType['Id']][$resource['Id']] = $resource['Name'];
          }
        }

      }
    }

    foreach ($appointmentTypes['AppointmentType'] as $appointmentType) {
      $tmp = array();
      $tmp[-1] = 'Toon alle beschikbare tijden';
      if (isset($appointmentType['Id']) && isset($resourcesArr[$appointmentType['Id']])) {
      
        foreach ($resourcesArr[$appointmentType['Id']] as $k => $v) {
          $tmp[$k] = $v;
        }

      }
      $resourcesArr[$appointmentType['Id']] = $tmp;
    }

    $bookableDays = array();
    $bookableTimes = array();

    $selectedCategory = $selectedApptype = $selectedResource = '';
    $selectedDate = date('Y-n-j');

    if (isset($_SESSION['booking']) && isset($_SESSION['booking']['category'])) {
      $selectedCategory = $_SESSION['booking']['category'];
      $selectedApptype = $_SESSION['booking']['apptypeId'];
      $selectedResource = $_SESSION['booking']['resourceId'];
      $selectedDate = $_SESSION['booking']['date'];
    }

    echo $this->renderPartial('appointment', array(
    'categories' => $categories,
    'appTypes' => $appTypesArr,
    'appTypesDurations' => $apptypeDurations,
    'resources' => $resourcesArr,
    'bookableDays' => $bookableDays,
    'bookableTimes' => $bookableTimes,
    'firstCategory' => $firstCategory,
    'selectedCategory' => $selectedCategory,
    'selectedApptype' => $selectedApptype,
    'selectedResource' => $selectedResource,
    'selectedDate' => $selectedDate
    ));

    $_SESSION['booking']['resourcesArr'] = $resourcesArr;
    $_SESSION['booking']['appTypesArr'] = $appTypesArr;
    $_SESSION['booking']['appTypesDurations'] = $apptypeDurations;
    $_SESSION['booking']['categoriesArr'] = $categories;

  }

  /**
	 * 
	 * Handles the login/register choice form
	 */
  public function executeConsumerData()
  {
    $_SESSION['booking']['category'] = $this->getRequestParameter('category');
    $_SESSION['booking']['apptypeId'] = $this->getRequestParameter('apptypeId');
    $_SESSION['booking']['resourceId'] = $this->getRequestParameter('resourceId');
    $_SESSION['booking']['date'] = $this->getRequestParameter('date');
    
    foreach ($_POST as $key => $value) {
      if (substr($key,0,6) == 'answer') {
        $_SESSION['booking']['answers'][substr($key,7)] = $value;  
      }
    }
    
    $agendas = $this->sendRequest('getAgendas');
    $agenda = array_shift($agendas['Agenda']);
    
    $bookableTimes = $this->sendRequest('getBookableTimes', array(
      'AgendaId' => $agenda['Id'],
      'Date' => $this->getRequestParameter('date'),
      'AppointmentTypeId' => $this->getRequestParameter('apptypeId'),
      'ResourceId'=> -1
    ));
    if (isset($bookableTimes['BookableTime'])) {
      $time = array_shift($bookableTimes['BookableTime']);
      $_SESSION['booking']['time'] = $time['StartTime'];  
    }
    
    if (!$this->getCustomerInfo() && isset($_SESSION['dekaag_user_id'])) {
      $user = DeKaagUser::model()->findByPk($_SESSION['dekaag_user_id']);
      $email = $user->relation->email;
      $done = false;
      $limit = 50;
      $offset = 0;
      // login was performed in Wordpress site, try to find corresponding OA customer
      while(!$done) {
        $consumers = $this->sendRequest('getCustomers', array('Limit' => $limit, 'Offset' => $offset));
        foreach ($consumers['Customer'] as $consumer) {
          if($consumer['Email'] == $email) {
            $_SESSION['booking']['customer'] = $consumer;
          
            $done = true;
          }
        }
        if (count($consumers['Customer']) < $limit) {
          $done = true;
        }
        $offset += $limit;
      }
    }
   
    echo $this->renderPartial('consumerData', array('appointmentInfo' => $this->getAppointmentInfo(), 'customerInfo' => $this->getCustomerInfo()));
  }

  /**
	 * 
	 * Handles the login procedure
	 */
  public function executeLogin()
  {
    $consumer = $this->sendRequest('loginCustomer', array(
      'Username' => $this->getRequestParameter('username'),
      'Password' => $this->getRequestParameter('password')
    ));

    if (isset($consumer['Customer'])) {
      $_SESSION['booking']['customer'] = array_shift($consumer['Customer']);
      $dekaag_user = DeKaagUser::model()->findByAttributes(new DeKaagCriteria(array('username' => $this->getRequestParameter('username'))));
      if ($dekaag_user) {
        $_SESSION['dekaag_user_id'] = $dekaag_user->id;
        $_SESSION['dekaag_relation_id'] = $dekaag_user->{$dekaag_user->prefix().'relation_id'};
      }
      echo 'OK';
      exit;
    }
    else {
      $dekaag_user = DeKaagUser::model()->findByAttributes(new DeKaagCriteria(array('username' => $this->getRequestParameter('username'))));
      if ($dekaag_user) {
        // OA user does not exist, but De Kaag user does.
        $salt = $dekaag_user->salt;
	      $password = crypt($this->getRequestParameter('password'), $salt);
	      if ($password == $dekaag_user->password) {
	        $_SESSION['dekaag_user_id'] = $dekaag_user->id;
	        $_SESSION['dekaag_relation_id'] = $dekaag_user->{$dekaag_user->prefix().'relation_id'};
	        
          // Create OA account for this user
          $vars = array(
            'LastName' => $dekaag_user->relation->last_name,
            'Email' => $dekaag_user->relation->email,
            'FirstName' => $dekaag_user->relation->first_name,
            'Username' => $this->getRequestParameter('username'),
            'Password' => $this->getRequestParameter('password'),
            'Street' => $dekaag_user->relation->address,
            'ZipCode' => $dekaag_user->relation->zipcode,
            'City' => $dekaag_user->relation->city
          );
          $consumer = $this->sendRequest('setCustomer', $vars);
          if (isset($consumer['Customer'])) {
            $consumer_id = $consumer['Customer'][0]['Id'];
            $consumer = $this->sendRequest('getCustomer', array(
              'id' => $consumer_id,
            ));
            $_SESSION['booking']['customer'] = array_shift($consumer['Customer']);
            echo 'OK';
            exit;
          } 
	      }
      }
    }
    echo 'FAILED';
    exit;
  }


  public function executeLoginWithFacebook()
  {
    if ($this->hasRequestParameter('code') && $this->getRequestParameter('code') != '') {

      $url = 'https://graph.facebook.com/oauth/access_token?client_id='.FB_APP_ID.'&redirect_uri='.substr($this->getRequestParameter('url'),0,strpos($this->getRequestParameter('url'), '?')).'&client_secret='.FB_APP_SECRET.'&code='.$this->getRequestParameter('code');

      $access_token = @file_get_contents($url);


      if ($access_token) {
        $graph_url = "https://graph.facebook.com/me?" . $access_token;

        $user = json_decode(file_get_contents($graph_url));
        if (isset($user->location)) {
          if (strpos($user->location->name, ',')) {
            list($city, $country) = explode(',', $user->location->name);
            $country = 'Nederland'; // Facebook can also return city, state which is undistinguishable from city, country
          } else {
            $country = 'Nederland';
            $city = $user->location->name;
          }
          $user->city = $city;

          $country = trim($country);

          // TODO: Proper mangling back of english or dutch country names
          if ($country == 'Netherlands') {
            $country = 'Nederland';
          }

          $user->country = $country;
        }
        elseif(isset($user->work)) {
          $work = array_shift($user->work);
          if (isset($work->location)) {
            if (strpos($work->location->name, ',')) {
              list($city, $country) = explode(',', $work->location->name);
              $country = 'Nederland'; // Facebook can also return city, state which is undistinguishable from city, country
            } else {
              $country = 'Nederland';
              $city = $work->location->name;
            }
            $user->city = $city;

            $country = trim($country);

            // TODO: Proper mangling back of english or dutch country names
            if ($country == 'Netherlands') {
              $country = 'Nederland';
            }

            $user->country = $country;
          }
        }
        elseif (isset($user->hometown)) {
          if (strpos($user->hometown->name, ',')) {
            list($city, $country) = explode(',', $user->hometown->name);
            $country = 'Nederland'; // Facebook can also return city, state which is undistinguishable from city, country
          }
          else {
            $country = 'Nederland';
            $city = $user->hometown->name;
          }
          $user->city = $city;

          $country = trim($country);

          // TODO: Proper mangling back of english or dutch country names
          if ($culture->locale == 'nl_NL' && $country == 'Netherlands') {
            $country = 'Nederland';
          }

          $user->country = $country;
        } else {
          //Nothing is set, so set to empty
          $user->city = '';
          $user->country = '';
        }
        $user->city = ucwords(strtolower($user->city)); // Facebook returns city with CAPS sometimes

        list($m,$d,$y) = explode('/', $user->birthday);
        $user->birthday = date('d/m/Y', mktime(0,0,0,$m,$d,$y));

        $userdata['FirstName'] = $user->first_name;
        $userdata['LastName'] = $user->last_name;

        $userdata['City'] = $user->city;
        $userdata['Country'] = $user->country;
        $userdata['Email'] = $user->email;
        $userdata['DateOfBirth'] = $user->birthday;
        $userdata['Gender'] = $user->gender == 'male' ? 'm' : 'f';

        $password = substr(md5(time()), 0,6);

        $userdata['Username'] = substr($user->email,0, strpos($user->email, '@')).rand(1000,9999);
        $userdata['Password'] = $password;
        $userdata['Password2'] = $password;
        $userdata['FacebookId'] = $user->id;

        $consumer = $this->sendRequest('loginCustomerWithFacebook', array('FacebookId' => $userdata['FacebookId']));
        if (!$consumer) {
          $consumer = $this->sendRequest('setCustomer', $userdata);
        }

        if ($consumer) {
          $_SESSION['booking']['customer'] = array_shift($consumer['Customer']);
          $this->sendResponse(array('Status' => 'OK', 'Callback' => 'widget.startConfirm();'));
          exit;
        }
      }

      $this->sendResponse(array('FAILED', 'Errors' => array(array('Error' => 'Het is niet gelukt om met uw Facebook account in te loggen.'))));
      exit;
    }

    $fburl = 'https://www.facebook.com/dialog/oauth?client_id='.FB_APP_ID.'&scope=email,user_birthday&redirect_uri='.$this->getRequestParameter('url');
    $this->sendResponse(array('Status' => 'OK', 'Redirect' => $fburl));
    exit;
  }

  /**
	 * 
	 * Handles the logoff procedure
	 */
  public function executeLogoff()
  {
    unset($_SESSION['booking']['customer']);
    unset($_SESSION['dekaag_user_id']);
    unset($_SESSION['dekaag_relation_id']);
    echo 'OK';
    exit;
  }

  /**
	 * 
	 * Handles the confirm procedure
	 */
  public function executeConfirm()
  {
    if ($this->hasRequestParameter('persona')) {
      $_SESSION['dekaag_persona_id'] = $this->getRequestParameter('persona');      
    }
    
    foreach ($_POST as $key => $value) {
      if (substr($key,0,6) == 'answer') {
        $_SESSION['booking']['answers'][substr($key,7)] = $value;  
      }
    }

    if ($this->hasRequestParameter('confirmCode')) {
      $confirmCode = trim($this->getRequestParameter('confirmCode'));
      $appId = $_SESSION['booking']['appointment']['Id'];
      $response = $this->sendRequest('confirmAppointment', array('id' => $appId, 'ConfirmationCode' => $confirmCode));
      if (isset($response['Confirmation'])) {
        $confirmation = array_shift($response['Confirmation']);
        if ((bool)$confirmation['Confirmed']) {
          $this->sendResponse(array('Status' => 'OK'));
        }
        else {
          $this->sendResponse(array('Status' => 'FAILED', 'Errors' => array(array('Error' => 'De bevestigingscode is niet juist of is verlopen.'))));
        }
        exit;
      }

      $this->sendResponse(array('Status' => 'FAILED', 'Errors' => array(array('Error' => 'De bevestigingscode is niet juist of is verlopen.'))));
      exit;
    }


    $agendas = $this->sendRequest('getAgendas');
    foreach ($agendas['Agenda'] as $agenda) {
      if ($agenda['IsDefault']) break;
    }

    $apptype = $this->sendRequest('getAppointmentType', array(
    'id' => $_SESSION['booking']['apptypeId']));
    $apptype = array_shift($apptype['AppointmentType']);

    list($h, $m) = explode(':', $_SESSION['booking']['time']);
    $minutes = ($h * 60) + $m;
    $minutes += $apptype['Duration'];
    $h = floor($minutes / 60);
    $endTime = $h . ':' .($minutes - ($h*60));

    $booking = array(
      'AgendaId' => $agenda['Id'],
      'CustomerId' => $_SESSION['booking']['customer']['Id'],
      'AppointmentTypeId' => $_SESSION['booking']['apptypeId'],
      'Date' => $_SESSION['booking']['date'],
      'StartTime' => $_SESSION['booking']['time'],
      'EndTime' => $endTime,
      'Name' => $apptype['Name'],
      'Description' => isset($_SESSION['booking']['customer']['Remarks']) ? $_SESSION['booking']['customer']['Remarks'] : ''
    );

    $tmpFields = $this->sendRequest('getFields', array('AgendaId' => $agenda['Id']));

    $fields = array();

    if (isset($tmpFields['Field'])) {
      foreach ($tmpFields['Field'] as $field) {
        if (isset($fields[$field['Id']])) {
          if (!(bool)$fields[$field['Id']]['Required']) {
            $fields[$field['Id']] = $field;
          }
        }
        else {
          $fields[$field['Id']] = $field;
        }
      }
    }

    foreach ($fields as $field) {
      $booking[$field['Label']] = isset($_SESSION['booking']['customer'][$field['Label']]) ? $_SESSION['booking']['customer'][$field['Label']] : '';
    }

    if (!$this->hasRequestParameter('confirm')) {
      
      $apptype = $this->sendRequest('getAppointmentType', array(
      'id' => $_SESSION['booking']['apptypeId']));
      $apptype = array_shift($apptype['AppointmentType']);
   
      $total = 0;
      $total += (float)$apptype['Price'];

      echo $this->renderPartial('confirm', array('total' => $total, 'appointmentInfo' => $this->getAppointmentInfo(), 'customerInfo' => $this->getCustomerInfo()));
    } 
    else {

      $resIds = array();
      foreach ($_SESSION['booking']['answers'] as $key => $value) {
        $formrow = DeKaagFormRow::model()->findByPk($key);
        $mutations = json_decode($formrow->mutations);
        if($mutations[$value]->resource != '') {
          $resourceName = $mutations[$value]->resource;
          $resources = $this->sendRequest('getResources', array('AgendaId' => $agenda['Id']));
          $resourceNames = array();
          foreach ($resources['Resource'] as $resource) {
            if(fnmatch($resourceName, $resource['Name'])) {
              $resourceNames[$resource['Id']] = $resource['Name'];
            }
          }
          if (count($resourceNames) > 0) {
            foreach ($resourceNames as $resourceId => $resource) {
              $bookableDays = $this->sendRequest('getBookableDays', array(
                'AgendaId' => $agenda['Id'],
                'StartDate' => $booking['Date'],
                'EndDate' => $booking['Date'],
                'AppointmentTypeId' => $booking['AppointmentTypeId'],
                'ResourceId'=> $resourceId
              ));
              if ($bookableDays && isset($bookableDays['BookableDay'])) {
                foreach ($bookableDays['BookableDay'] as $bookableDay) {
                  if (isset($bookableDay['Date'])) {
                    $resIds[] = $resourceId;
                  }
                }
              } 
            }
          }
        }
      }
      // laser bug = 18
      // optimist = 19
      //$booking['ResourceId'] = $resId;
      $booking['ResourceId'] = implode(',', $resIds);
      
      $response = $this->sendRequest('setAppointment', $booking);

      if (isset($response['Appointment'])) {
        $appointment = array_shift($response['Appointment']);
        $_SESSION['booking']['appointment'] = $appointment;
      
        $this->sendResponse(array('Status' => 'OK'));
      }
      elseif (isset($this->error)) {
        $errors = explode("\n", $this->error);
        array_shift($errors);
        $errorsTmp = array();
        foreach ($errors as $error) {
          $errorsTmp[] = array('Field' => '', 'Error' => $error);
        }
  
        if (count($errorsTmp) > 0) {
          $this->sendResponse(array('Status' => 'FAILED', 'Errors' => $errorsTmp));
          exit;
        }
  
        $this->sendResponse(array('Status' => 'OK'));
      }
      else {
        $this->sendResponse(array('Status' => 'FAILED', 'Errors' => array('Afspraak kon niet worden opgeslagen')));
      }
    }
  }

  /**
	 * 
	 * Handles the thankyou page
	 */
  public function executeThankyou()
  {
    $apptype = $this->sendRequest('getAppointmentType', array(
    'id' => $_SESSION['booking']['apptypeId']));
    $apptype = array_shift($apptype['AppointmentType']);
    
    $relation = DeKaagRelation::model()->findByPk($_SESSION['dekaag_relation_id']);
    $model = new DeKaagInvoice;
    $model->company = $_SESSION['company'];
    $model->date = date('Y-m-d');
    $model->enddate = date('Y-m-d', strtotime('+1 weeks')); // 1 maand voor aanvang reis
    $model->dpdate = date('Y-m-d', strtotime('+2 weeks'));
    $model->address = $relation->address."\n".$relation->zipcode.' '.$relation->city;
    $model->{$model->prefix().'relation_id'} = $relation->id; 
    $model->{$model->prefix().'persona_id'} = $_SESSION['dekaag_persona_id']; 

    $info = array(); 
    $rows = array();
    $total = 0;
  
    $row = new DeKaagInvoiceRow;
    $row->description = $apptype['Name'];
    
    $total = $grandtotal = (float)$apptype['Price'];
    
    if(isset($_SESSION['booking']['answers'])) {
      foreach ($_SESSION['booking']['answers'] as $key => $value) {
        $formrow = DeKaagFormRow::model()->findByPk($key);
        
        if (!$formrow->isVisible()) continue;
        
        $mutations = json_decode($formrow->mutations);
        $mutation = $mutations[$value];
        $v = $mutation->type == 'price' ? $mutation->mutation : ($total / 100) * $mutation->mutation;
        if ($v > 0 && !is_numeric($value) && trim($value) == '') {
          // entered string is empty, do no mutation
          $v = 0;
        }
        if ($formrow->oninvoice != '') {
          $row2 = new DeKaagInvoiceRow;
          $answers = json_decode($formrow->answers);
          $row2->description = $formrow->oninvoice.' '.(is_numeric($value) ? $answers[$value] : $value);
          $row2->total = $v;
          $row2->vat = $mutation->vat > 0 ? $mutation->vat : 21;
          $rows[] = $row2;
        } 
        else {
          $total = $total + $v;
        }
        $grandtotal += $v;
      }
    }
    
    $row->total = $total;
    $row->vat = 6;
    $rows = array_merge(array($row), $rows);
    foreach ($rows as $row) {
      $info[$row->description] = $row->total;
    }
    
    $model->rows = $rows;
    $total = $grandtotal;
    // TODO: insert DP values here
    if ($total < 270) {
      $model->downpayment = 'none';
    }
    else {
      $model->downpayment = 'fixed';
      $model->dpvalue = 200;
      $model->dpdate = date('Y-m-d', strtotime('+2 weeks'));
    }
    
    $_POST['send_invoice'] = true; // to trigger the sending of the PDF invoice and payment links
    require_once(DEKAAGCRM__PLUGIN_DIR.'class.dekaagcrm-admin-forms.php');
  	require_once(DEKAAGCRM__PLUGIN_DIR.'class.dekaagcrm-admin-invoices.php');
  	require_once(DEKAAGCRM__PLUGIN_DIR.'class.dekaagcrm-admin-consumers.php');
  	require_once(DEKAAGCRM__PLUGIN_DIR.'class.dekaagcrm-admin.php');
	  $model->status = 2;
  	
    $model->save();
    $old_rows = DeKaagInvoiceRow::model()->findAllByAttributes(new DeKaagCriteria(array($model->prefix().'invoice_id' => $model->id)));
    foreach($old_rows as $old_row) {
      $old_row->delete();
    }
   
    foreach ($rows as $row) {
      $row->{$row->prefix().'invoice_id'} = $model->id;
      $row->save();
    }
    
    $appointment = new DeKaagAppointment;
    $appointment->{$model->prefix().'invoice_id'} = $model->id;
    $appointment->{$model->prefix().'relation_id'} = $_SESSION['dekaag_relation_id'];
    $appointment->{$model->prefix().'persona_id'} = $_SESSION['dekaag_persona_id'];
    $appointment->date  = $_SESSION['booking']['date'];
    $appointment->created_at = date('Y-m-d H:i:s');
    $appointment->apptype_id = $_SESSION['booking']['apptypeId'];
    $appointment->info = json_encode($info);
    $appointment->company = $_SESSION['company'];
    $appointment->save();
    
    echo $this->renderPartial('thankyou', array('appointmentInfo' => $this->getAppointmentInfo(), 'customerInfo' => $this->getCustomerInfo(), 'model' => $model));

    $customer = $_SESSION['booking']['customer'];
    unset($_SESSION['booking']);
    $_SESSION['booking']['customer'] = $customer;
  }

  /**
	 * 
	 * Handles the password reminder
	 */
  public function executePasswordReminder()
  {
    if ($this->hasRequestParameter('email')) {
      $email = trim($this->getRequestParameter('email'));
      $response = $this->sendRequest('passwordRecovery', array('Email' => $email));

      if ($response && isset($response['Customer'])) {
        $this->sendResponse(array('Status' => 'OK', 'Messages' => array(array('Message' => 'Er is een e-mail verzonden met instructies om uw wachtwoord opnieuw in te stellen.'))));
        exit;
      }

      $this->sendResponse(array('Status' => 'FAILED', 'Errors' => array(array('Error' => 'Het e-mailadres komt niet voor in het systeem.'))));
      exit;
    }


    echo $this->renderPartial('passwordReminder', array('appointmentInfo' => $this->getAppointmentInfo()));
  }
  
  public function executeWizard()
  {
    if ($this->hasRequestParameter('apptype')) {
      $_SESSION['booking']['apptypeId'] = $this->getRequestParameter('apptype');
      $_SESSION['booking']['category'] = $this->getRequestParameter('category');
      $_SESSION['booking']['dob'] = $this->getRequestParameter('dob');
    }
  
    echo $this->renderPartial('wizard', array());
  }
  
  public function executeCalendar()
  {
    $resourcesArr = $_SESSION['booking']['resourcesArr'];
    $appTypesArr = $_SESSION['booking']['appTypesArr'];
    $apptypeDurations = $_SESSION['booking']['appTypesDurations'];
    $categories = $_SESSION['booking']['categoriesArr'];
    if ($this->hasRequestParameter('apptype')) {
      $_SESSION['booking']['apptypeId'] = $this->getRequestParameter('apptype');
      $_SESSION['booking']['category'] = $this->getRequestParameter('category');
      $_SESSION['booking']['dob'] = $this->getRequestParameter('dob');
    }
    
    foreach ($_POST as $key => $value) {
      if (substr($key,0,6) == 'answer') {
        $_SESSION['booking']['answers'][substr($key,7)] = $value;  
      }
    }
    $bookableDays = array();
    $bookableTimes = array();

    $selectedCategory = $selectedApptype = $selectedResource = '';
    $selectedDate = date('Y-n-j');

    if (isset($_SESSION['booking']) && isset($_SESSION['booking']['category'])) {
      $selectedCategory = $_SESSION['booking']['category'];
      $selectedApptype = $_SESSION['booking']['apptypeId'];
      $selectedResource = $_SESSION['booking']['resourceId'];
      $selectedDate = $_SESSION['booking']['date'];
    }

    echo $this->renderPartial('calendar', array(
      'categories' => $categories,
      'appTypes' => $appTypesArr,
      'appTypesDurations' => $apptypeDurations,
      'resources' => $resourcesArr,
      'bookableDays' => $bookableDays,
      'bookableTimes' => $bookableTimes,
      'firstCategory' => $firstCategory,
      'selectedCategory' => $selectedCategory,
      'selectedApptype' => $selectedApptype,
      'selectedResource' => $selectedResource,
      'selectedDate' => $selectedDate
    ));

    $_SESSION['booking']['resourcesArr'] = $resourcesArr;
    $_SESSION['booking']['appTypesArr'] = $appTypesArr;
    $_SESSION['booking']['appTypesDurations'] = $apptypeDurations;
    $_SESSION['booking']['categoriesArr'] = $categories;
  }
  
  

  /**
	 * 
	 * Handles the registration form
	 */
  public function executeRegister()
  {
    if (!$this->hasRequestParameter('Dob') && isset($_SESSION['booking']['dob'])) {
      $this->setRequestParameter('Dob', $_SESSION['booking']['dob']);
    }
  
    $agendas = $this->sendRequest('getAgendas');
    foreach ($agendas['Agenda'] as $agenda) {
      if ($agenda['IsDefault']) break;
    }
    $tmpFields = $this->sendRequest('getFields', array('AgendaId' => $agenda['Id']));

    $fields = array();

    if (isset($tmpFields['Field'])) {
      foreach ($tmpFields['Field'] as $field) {
        if (isset($fields[$field['Id']])) {
          if (!(bool)$fields[$field['Id']]['Required']) {
            $fields[$field['Id']] = $field;
          }
        }
        else {
          $fields[$field['Id']] = $field;
        }
      }
    }

    if ($this->hasRequestParameter('method') && $this->getRequestParameter('method') == 'post') {
      $errors = array();

      if ($this->getRequestParameter('Username') == '') {
        $errors[] = array('Field' => 'Username', 'Error' => __('Gebruikersnaam is een verplicht veld'));
      }

      if ($this->getRequestParameter('FirstName') == '') {
        $errors[] = array('Field' => 'FirstName', 'Error' => __('Voornaam is een verplicht veld'));
      }

      if ($this->getRequestParameter('LastName') == '') {
        $errors[] = array('Field' => 'LastName', 'Error' => __('Achternaam is een verplicht veld'));
      }

      if ($this->getRequestParameter('Email') == '') {
        $errors[] = array('Field' => 'Email', 'Error' => __('E-mail adres is een verplicht veld'));
      }

      if ($this->getRequestParameter('Password') == '') {
        $errors[] = array('Field' => 'Password', 'Error' => __('Wachtwoord is een verplicht veld'));
      }
      elseif(strlen($this->getRequestParameter('Password')) < 6) {
        $errors[] = array('Field' => 'Password', 'Error' => __('Wachtwoord is te kort'));
      }
      elseif($this->getRequestParameter('Password') != $this->getRequestParameter('Password2')) {
        $errors[] = array('Field' => 'Password', 'Error' => __('Wachtwoord is niet gelijk aan het controle-wachtwoord'));
      }

      if (!$this->hasRequestParameter('Legal')) {
        $errors[] = array('Field' => 'Legal', 'Error' => __('U moet de algemene voorwaarden accepteren'));
      }
      
      if ($this->getRequestParameter('PersonaFirstName') == '') {
        $errors[] = array('Field' => 'PersonaFirstName', 'Error' => __('Voornaam persona is een verplicht veld'));
      }
      
      if ($this->getRequestParameter('PersonaLastName') == '') {
        $errors[] = array('Field' => 'PersonaLastName', 'Error' => __('Achternaam persona is een verplicht veld'));
      }
      
      
      if ($this->getRequestParameter('Dob') == '') {
        $errors[] = array('Field' => 'Dob', 'Error' => __('Geboortedatum persona is een verplicht veld'));
      }
      
      if ($this->getRequestParameter('Phone') == '') {
        $errors[] = array('Field' => 'Phone', 'Error' => __('Telefoonnummer is een verplicht veld'));
      }
      
      if ($this->getRequestParameter('MobilePhone') == '') {
        $errors[] = array('Field' => 'MobilePhone', 'Error' => __('Telefoonnummer mobiel is een verplicht veld'));
      }

      if (count($errors) == 0) {
        $data = array(
          //'Gender' => $this->hasRequestParameter('Gender') ? $this->getRequestParameter('Gender') : 'm',
          'FirstName' => $this->hasRequestParameter('FirstName') ? $this->getRequestParameter('FirstName') : '',
          'Insertions' => $this->hasRequestParameter('Insertions') ? $this->getRequestParameter('Insertions') : '',
          'LastName' => $this->hasRequestParameter('LastName') ? $this->getRequestParameter('LastName') : '',
          'Street' => $this->hasRequestParameter('Street') ? $this->getRequestParameter('Street') : '',
          'HouseNr' => $this->hasRequestParameter('HouseNr') ? $this->getRequestParameter('HouseNr') : '',
          'HouseNrAddition' => $this->hasRequestParameter('HouseNrAddition') ? $this->getRequestParameter('HouseNrAddition') : '',
          'ZipCode' => $this->hasRequestParameter('ZipCode') ? $this->getRequestParameter('ZipCode') : '',
          'City' => $this->hasRequestParameter('City') ? $this->getRequestParameter('City') : '',
          'Country' => $this->hasRequestParameter('Country') ? $this->getRequestParameter('Country') : '',
          'Email' => $this->hasRequestParameter('Email') ? $this->getRequestParameter('Email') : '',
          'Phone' => $this->hasRequestParameter('Phone') ? $this->getRequestParameter('Phone') : '',
          'MobilePhone' => $this->hasRequestParameter('MobilePhone') ? $this->getRequestParameter('MobilePhone') : '',
          'Username' => $this->hasRequestParameter('Username') ? $this->getRequestParameter('Username') : '',
          'Password' => $this->hasRequestParameter('Password') ? $this->getRequestParameter('Password') : '',
          'Remarks' => $this->hasRequestParameter('Remarks') ? $this->getRequestParameter('Remarks') : ''
        );

        $response = $this->sendRequest('setCustomer', $data);

        if (isset($this->error)) {
          $errors = explode("\n", $this->error);
          array_shift($errors);
          foreach ($errors as $error) {
            $errorsTmp[] = array('Field' => '', 'Error' => $error);
          }
          $this->sendResponse(array('Status' => 'FAILED', 'Errors' => $errorsTmp));
          exit;
        }

        $customer = array_shift($response['Customer']);
        $data['Id'] = $customer['Id'];

        foreach ($fields as $field_id => $field) {
          $data[$field['Label']] = $this->getRequestParameter('Custom_'.$field['Id']);
        }

        $_SESSION['booking']['customer'] = $data;

        $relation = new DeKaagRelation;
        $relation->first_name = $this->getRequestParameter('FirstName');
        $relation->insertions = $this->getRequestParameter('Insertions');
        $relation->last_name = $this->getRequestParameter('LastName');
        $relation->title = $this->getRequestParameter('FirstName').' '.$this->getRequestParameter('Insertions').' '.$this->getRequestParameter('LastName');
        $relation->address = $this->getRequestParameter('Street');
        $relation->zipcode = $this->getRequestParameter('ZipCode');
        $relation->city = $this->getRequestParameter('City');
        $relation->phone = $this->getRequestParameter('Phone');
        $relation->phone_mobile = $this->getRequestParameter('MobilePhone');
        $relation->phone_extra = $this->getRequestParameter('PhoneExtra');
        $relation->email = $this->getRequestParameter('Email');
        $relation->created_at = date('Y-m-d H:i:s');
        $relation->modified_at = date('Y-m-d H:i:s');
        $relation->save();
        $_SESSION['dekaag_relation_id'] = $relation->id;
        
        $persona = new DeKaagPersona;
        $persona->{$persona->prefix().'relation_id'} = $_SESSION['dekaag_relation_id'];
        $persona->first_name = $this->getRequestParameter('PersonaFirstName');
        $persona->insertions = $this->getRequestParameter('PersonaInsertions');
        $persona->last_name = $this->getRequestParameter('PersonaLastName');
        $persona->title = $this->getRequestParameter('PersonaFirstName').' '.$this->getRequestParameter('PersonaInsertions').' '.$this->getRequestParameter('PersonaLastName');
        $persona->gender = $this->getRequestParameter('Gender');
        $persona->dob = date('Y-m-d', strtotime($this->getRequestParameter('Dob')));
        $persona->remarks = $this->getRequestParameter('Remarks');
        $persona->save();
        $_SESSION['dekaag_persona_id'] = $persona->id;
        
        $user =  new DeKaagUser;
        $user->{$persona->prefix().'relation_id'} = $_SESSION['dekaag_relation_id'];
        $user->username = $this->getRequestParameter('Username');
        $salt = '';
        for($i=0; $i<22; $i++){
            $r = rand(0,$charCount-1);
            $salt .= $chars[$r];
        }
        $salt = '$5$'.$salt.'$';
        $password = crypt($this->getRequestParameter('Password'), $salt);
        $user->password = $password;
        $user->salt = $salt;
        $user->role = 1;
        $user->save();
        $_SESSION['dekaag_user_id'] = $user->id;
        
        $this->sendResponse(array('Status' => 'OK'));
        exit;
      }
      else {
        $this->sendResponse(array('Status' => 'FAILED', 'Errors' => $errors));
        exit;
      }
    }

    echo $this->renderPartial('register', array('appointmentInfo' => $this->getAppointmentInfo(), 'fields' => $fields));
  }

  /**
	 * 
	 * Handles the registration form
	 */
  public function executePersona()
  {
    if (!$this->hasRequestParameter('Dob') && isset($_SESSION['booking']['dob'])) {
      $this->setRequestParameter('Dob', $_SESSION['booking']['dob']);
    }
    
    if ($this->hasRequestParameter('method') && $this->getRequestParameter('method') == 'post') {
      $errors = array();

      if ($this->getRequestParameter('first_name') == '') {
        $errors[] = array('Field' => 'first_name', 'Error' => __('Voornaam is een verplicht veld'));
      }

      if ($this->getRequestParameter('last_name') == '') {
        $errors[] = array('Field' => 'last_name', 'Error' => __('Achternaam is een verplicht veld'));
      }
      
      if ($this->getRequestParameter('dob') == '') {
        $errors[] = array('Field' => 'Dob', 'Error' => __('Geboortedatum persona is een verplicht veld'));
      }
     

      if (count($errors) == 0) {
        
        $persona = new DeKaagPersona;
        $persona->{$persona->prefix().'relation_id'} = $_SESSION['dekaag_relation_id'];
        $persona->title = $this->getRequestParameter('first_name').' '.$this->getRequestParameter('insertions').' '.$this->getRequestParameter('last_name');
        $persona->first_name = $this->getRequestParameter('first_name');
        $persona->insertions = $this->getRequestParameter('insertions');
        $persona->last_name = $this->getRequestParameter('last_name');
        $persona->gender = $this->getRequestParameter('gender');
        $persona->dob = date('Y-m-d', strtotime($this->getRequestParameter('dob')));
        $persona->remarks = $this->getRequestParameter('remarks');
        $persona->save();
        $_SESSION['dekaag_persona_id'] = $persona->id;

        $this->sendResponse(array('Status' => 'OK'));
        exit;
      }
      else {
        $this->sendResponse(array('Status' => 'FAILED', 'Errors' => $errors));
        exit;
      }
    }

    echo $this->renderPartial('persona', array('appointmentInfo' => $this->getAppointmentInfo(), 'fields' => $fields));
  }
  /**
	 * Creates a small partial containing the booking information
	 *
	 * @return string
	 */
  private function getAppointmentInfo()
  {
    $apptype = isset($_SESSION['booking']) ? $_SESSION['booking']['appTypesArr'][$_SESSION['booking']['categoriesArr'][ $_SESSION['booking']['category']]][$_SESSION['booking']['apptypeId']] : '';
    $resource = isset($_SESSION['booking']['resourcesArr'][$_SESSION['booking']['resourceId']]) ? $_SESSION['booking']['resourcesArr'][$_SESSION['booking']['resourceId']] : '';
//    setlocale(LC_TIME, Widget::$locale);
//    setlocale(LC_TIME, 'en_US');
    $date = strftime('%A %e %B %Y', strtotime($_SESSION['booking']['date']));
    $time = $_SESSION['booking']['time'];

    $enddate = strftime('%A %e %B %Y', strtotime('+'.$_SESSION['booking']['appTypesDurations'][$_SESSION['booking']['apptypeId']]['Buffer'].' days', strtotime($_SESSION['booking']['date'])));

    // great, no locale support on WP site 
    $replacements = array(
      'Monday' => 'maandag',
      'Tuesday' => 'dinsdag',
      'Wednesday' => 'woensdag',
      'Thursday' => 'donderdag',
      'Friday' => 'vrijdag',
      'Saturday' => 'zaterdag',
      'Sunday' => 'zondag',
      'January' => 'januar',
      'February' => 'februari',
      'March' => 'maart',
      'April' => 'april',
      'May' => 'mei',
      'June' => 'juni',
      'July' => 'juli',
      'August' => 'augustus',
      'September' => 'september',
      'October' => 'oktober',
      'November' => 'november',
      'December' => 'december'
    );
    foreach ($replacements as $a => $b) {
      $date = str_replace($a, $b, $date);
      $enddate = str_replace($a, $b, $enddate);
    }
     
    $appointmentBlock = <<<EOT
<p><strong>{$apptype}</strong><br>
Aankomst op {$date}<br>
Vertrek op {$enddate}</p>\n
EOT;
    return $appointmentBlock;
}

/**
	 * Creates a small partial containing the customer information
	 *
	 * @return string
	 */
private function getCustomerInfo()
{
  if (!isset($_SESSION['booking']['customer'])) return false;

  $name = $_SESSION['booking']['customer']['FirstName'].' '.$_SESSION['booking']['customer']['Insertions'].' '.$_SESSION['booking']['customer']['LastName'];
  $email = $_SESSION['booking']['customer']['Email'];
  $address1 = $_SESSION['booking']['customer']['Street'].' '.$_SESSION['booking']['customer']['HouseNr'].' '.$_SESSION['booking']['customer']['HouseNrAddition'];
  $address2 =  $_SESSION['booking']['customer']['ZipCode'].' '.$_SESSION['booking']['customer']['City'];

  $customerBlock = <<<EOT
<p><strong>{$name}</strong><br>
{$email}
EOT;
  if (trim($address1) != '') $customerBlock .= '<br>'.$address1.PHP_EOL;
  if (trim($address2) != '') $customerBlock .= '<br>'.$address2.PHP_EOL;
  
  if ($this->getRequestParameter('step') == 'consumerData') {
    $customerBlock .= "<p><strong>".__('Kind/ gast', 'dekaagcrm')."</strong><br>";

    $relation = DeKaagRelation::model()->findByPk($_SESSION['dekaag_relation_id']);
    if(count($relation->personas) > 1) {
      $selected = isset($_SESSION['dekaag_persona_id']) ? $_SESSION['dekaag_persona_id'] : 0;
      $customerBlock .= '<select name="persona" id="persona" style="width:300px;margin:5px 0 10px 0;">';
      foreach ($relation->personas as $persona) {
        $checked = $persona->id == $selected ? ' selected="selected"' : '';
      
        $customerBlock .= '<option'.$checked.' value="'.$persona->id.'">'.$persona->title.' ('.date('d-m-Y', strtotime($persona->dob)).')</option>';
      }
      $customerBlock .= '</select>';
    }
    else if (count($relation->personas) == 1) {
      $persona = array_shift($relation->personas);
      $customerBlock .= '<input type="hidden" name="persona" id="persona" value="'.$persona->id.'">';
      $customerBlock .= $persona->title.' ('.date('d-m-Y', strtotime($persona->dob)).')';
    }
    $customerBlock .= ' <button type="button" onclick="widget.startPersona();">'.__('Add new persona', 'dekaagcrm').'</button><br>';
  }
  if ($this->getRequestParameter('step') == 'thankyou' || $this->getRequestParameter('step') == 'confirm') {
    $persona = DeKaagPersona::model()->findByPk($_SESSION['dekaag_persona_id']);
    $customerBlock .= "<p><strong>".__('Kind/ gast', 'dekaagcrm')."</strong><br>";
    $customerBlock .= $persona->title.' ('.date('d-m-Y', strtotime($persona->dob)).')';
  }
  $customerBlock .= '</p>'.PHP_EOL;
  return $customerBlock;
}
}

/**
 * 
 * Handles the server-side part of the application
 * i.e. AJAX-calls
 */
function __server()
{
  if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
  {
    // The script is called directly as an AJAX-call
    $widget = Widget::getInstance();
    if ($widget->hasRequestParameter('step')) {
      $method = 'execute'.ucfirst($widget->getRequestParameter('step'));
      $widget->$method();
    }
    else {
      $response = $widget->handleRequest();
      $widget->sendResponse($response);
    }
    exit;
  }
}

__server();
?>