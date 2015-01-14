<?php

class DeKaagCRM_Frontend {

	private static $initiated = false;

	protected static $the_content = '';
	
	public static function init() {
	  if ( !self::$initiated ) {
			self::init_hooks();
		}
	}

	public static function init_hooks() {
		self::$initiated = true;
	
    load_plugin_textdomain('dekaagcrm', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');

    if (isset($_GET['download'])) {
      $invoice = DeKaagInvoice::model()->findByAttributes(new DeKaagCriteria(array('invoicenr' => $_GET['download'])));
      if (!$invoice) die('Unknown invoice');
      
      $hash = substr(md5(strtoupper($invoice->invoicenr).'DEKAAG123456789123456789'),8,16);
      //$hash = crypt($invoice->invoicenr, $invoice->invoicenr.'DEKAAG');
      if ($hash != $_GET['hash']) die('Unknown invoice');
      
      $filename = $invoice->invoicenr.'.pdf';
      $path = DEKAAGCRM__PLUGIN_DIR.'data/'.$filename;
      header('Content-Type: application/pdf');
      header("Content-Transfer-Encoding: Binary");
      header("Content-disposition: attachment; filename=".$filename);
      readfile($path);
      exit;
    }
    $page = trim($_SERVER['REQUEST_URI'], '/');
    if ($page == 'dekaagcron') {
      self::page_cron();
      exit;
    }
    if (is_callable(array(self, 'page_'.$page))) {
      add_filter('the_content', array('DeKaagCRM_Frontend', 'the_content'));
      self::display_page();
    }
	}
	
	public static function the_content($input)
	{
	  return $input.self::$the_content;
	}
	
	public static function display_page() {
	  $page = 'page_'.trim($_SERVER['REQUEST_URI'], '/');
	  if (is_callable(array(self, $page)) && ($page == 'page_reserveren' || self::check_login())) {
	    wp_enqueue_style('dekaagcrm-frontend', plugins_url('css/frontend.css', __FILE__));
	    self::$page();
	  }
	}
	
	protected static function check_login()
	{
	  if (!isset($_SESSION['dekaag_user_id'])) {
	    wp_enqueue_style('dekaagcrm-frontend', plugins_url('css/frontend.css', __FILE__));
	    DeKaagCRM_Frontend::render('login-required', array(
        
      ));
	  }
	  return true;
	}
	public static function page_widgetAjax()
	{
	  foreach ($_POST as $key => $value) {
      if (substr($key,0,6) == 'answer') {
        $_SESSION['booking']['answers'][substr($key,7)] = $value;  
      }
    }
      
	  require_once(dirname(__FILE__).'/lib/vendor/onlineafspraken/config/config.php');
    require_once(dirname(__FILE__).'/lib/vendor/onlineafspraken/lib/widgetCore.php');
    require_once(dirname(__FILE__).'/lib/vendor/onlineafspraken/lib/widget.php');
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
	
	public static function page_cron()
	{
	  echo '<pre>';
	  $options = get_option('dekaagcrm_plugin_options');
    $period = isset($options['plugin_reminder']) ? $options['plugin_reminder'] : '+3 days';
    $sender_name = isset($options['plugin_sender_name']) ? $options['plugin_sender_name'] : 'De Kaag Watersport';
    $invoices = DeKaagInvoice::model()->findAllByAttributes(new DeKaagCriteria(array('status' => array(array(2,3), 'IN'))));
    echo "Reminder interval is {$period} from enddate or dpdate,\ndepending on payment status and if downpayment is allowed for the invoice.".PHP_EOL;
    echo "Invoice\t\tEnddate\t\tDP date\t\tRemind at".PHP_EOL;
    foreach ($invoices as $invoice) {
      $type = '';
      switch ($invoice->status) {
        case 2: // no payment
          if ($invoice->downpayment == 'none') {
            $test = date('Y-m-d', strtotime($period, strtotime($invoice->enddate)));
          }
          else {
            $test = date('Y-m-d', strtotime($period, strtotime($invoice->dpdate)));
            $type = 'dp';
          }
          break;
          
        case 3: // partially payed
          $test = date('Y-m-d', strtotime($period, strtotime($invoice->enddate)));
          break;
      }
      
      echo $invoice->invoicenr."\t".$invoice->enddate."\t".$invoice->dpdate."\t".$test.PHP_EOL;
      if ($test == date('Y-m-d')) {
        if (($type == 'dp' && $invoice->dpdate == '0000-00-00') || $invoice->enddate == '0000-00-00') {
          echo "This invoice should be reminded, sending email...".PHP_EOL;
          DeKaagCRM_Frontend::send_mail(
    	       $invoice->relation->email,
    	       __('Reminder', 'dekaagcrm'),
    	       'reminder',
    	       array(
    	        'title' => $invoice->relation->title,
              'sender_name' => $sender_name,
              'date' => date('d-m-Y', strtotime($invoice->date)),
              'end_date' => date('d-m-Y', strtotime($invoice->enddate)),
              'invoicenr' => $invoice->invoicenr,
              'total' => $invoice->getTotalStr(false),
              'downpayment' => $invoice->downpayment != 'none' ? ($invoice->downpayment == 'fixed' ? $invoice->dpvalue : ($invoice->getTotalStr(false)/100)*round($invoice->dpvalue) ) : false
    	       ),
    	       array()
    	    );
          $invoice->{'reminded'.$type} = date('Y-m-d');
          $invoice->save();
        }
        else {
          echo "This invoice is already reminded...".PHP_EOL;
        }
      }
    }
	  exit;
	 
	}
	
	public static function page_geschiedenis()
	{
	  $user = DeKaagUser::model()->findByPk($_SESSION['dekaag_user_id']);
	  $relation = $user->relation;
	  $appointments = DeKaagAppointment::model()->findAllByAttributes(new DeKaagCriteria(array(
	   $relation->prefix().'relation_id' => $relation->id
	  )));
	  
	  DeKaagCRM_Frontend::render('history', array(
      'appointments' => $appointments
    ));
	}
	
	public static function page_facturen()
	{
	  $user = DeKaagUser::model()->findByPk($_SESSION['dekaag_user_id']);
	  $relation = $user->relation;
	  $invoices = DeKaagInvoice::model()->findAllByAttributes(new DeKaagCriteria(array(
	   $relation->prefix().'relation_id' => $relation->id,
	   'status' => array(1, '>')
	  )));
	  
	  DeKaagCRM_Frontend::render('invoices', array(
      'invoices' => $invoices
    ));
	}
	
	public static function page_instellingen()
	{
	  $user = DeKaagUser::model()->findByPk($_SESSION['dekaag_user_id']);
	  $relation = $user->relation;
	  $personas = $relation->personas;
	  
	  $errors = array();
	  $messages = array();
	  
	  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    $validate = true;
	    
	    if (!DeKaagCRM_Frontend::validate($_POST['last_name'], 'required')) {
	      $errors['last_name'] = __('Last name is required', 'dekaagcrm');
	    }
	    
	    if (!DeKaagCRM_Frontend::validate($_POST['email'], 'required')) {
	      $errors['email'] = __('Email is required', 'dekaagcrm');
	    }
	    else if (!DeKaagCRM_Frontend::validate($_POST['email'], 'email')) {
	      $errors['email'] = __('Email is invalid', 'dekaagcrm');
	    }
	    
	    if (!DeKaagCRM_Frontend::validate($_POST['address'], 'required')) {
	      $errors['address'] = __('Adres is een verplicht veld', 'dekaagcrm');
	    }
	    if (!DeKaagCRM_Frontend::validate($_POST['zipcode'], 'required')) {
	      $errors['zipcode'] = __('Postcode is een verplicht veld', 'dekaagcrm');
	    }
	    if (!DeKaagCRM_Frontend::validate($_POST['city'], 'required')) {
	      $errors['city'] = __('Plaats is een verplicht veld', 'dekaagcrm');
	    }
	    
	    if ($_POST['zipcode'] != '') {
	      if (!DeKaagCRM_Frontend::validate($_POST['zipcode'], 'zipcode')) {
  	      $errors['zipcode'] = __('Zipcode is invalid', 'dekaagcrm');
  	    }
	    }
	    
	    if (!DeKaagCRM_Frontend::validate($_POST['phone'], 'required')) {
	      $errors['phone'] = __('Phone is required', 'dekaagcrm');
	    }
	    
	    if (!DeKaagCRM_Frontend::validate($_POST['phone_mobile'], 'required')) {
	      $errors['phone_mobile'] = __('Mobile phone is required', 'dekaagcrm');
	    }
	    
	    if ($_POST['password'] != '') {
  	    if (!DeKaagCRM_Frontend::validate($_POST['password'], 'match', array('check' => $_POST['password_retyped']))) {
  	      $errors['password'] = __('The 2 passwords do not match', 'dekaagcrm');
  	    }
	    }
  	    
	    if ($_POST['user_login'] != '') {
  	    if (!DeKaagCRM_Frontend::validate($_POST['user_login'], 'minlength', array('length' => 5))) {
  	      $errors['user_login'] = __('The username is too short', 'dekaagcrm');
  	    }
  	    else if (!DeKaagCRM_Frontend::validate($_POST['user_login'], 'login', array('relation' => $relation))) {
  	      $errors['user_login'] = __('The username is already taken', 'dekaagcrm');
  	    }
  	    else if (strstr($_POST['user_login'], ' ')) {
  	      $errors['user_login'] = __('Gebruikersnaam mag geen spaties bevatten', 'dekaagcrm');
  	    }
	    }
	    
	    $relation->first_name = $_POST['first_name'];
      $relation->insertions = $_POST['insertions'];
      $relation->last_name = $_POST['last_name'];
      $relation->title = $_POST['first_name'].' '.$_POST['last_name'];
      $relation->email = $_POST['email'];
      $relation->phone = $_POST['phone'];
      $relation->phone_mobile = $_POST['phone_mobile'];
      $relation->phone_extra = $_POST['phone_extra'];
      $relation->address = $_POST['address'];
      $relation->zipcode = $_POST['zipcode'];
      $relation->city = $_POST['city'];
	    
      $user->username = $_POST['user_login'];
      $salt = '';
      for($i=0; $i<22; $i++){
          $r = rand(0,$charCount-1);
          $salt .= $chars[$r];
      }
      $salt = '$5$'.$salt.'$';
      $password = crypt($_POST['password'], $salt);
      $user->password = $password;
      $user->salt = $salt;
          
      $c = 0;
      foreach ($personas as $k => $persona) {
        $persona->title = $_POST['persona_first_name'][$c].' '.$_POST['persona_insertions'][$c].' '.$_POST['persona_last_name'][$c];
        $persona->first_name = $_POST['persona_first_name'][$c];
        $persona->insertions = $_POST['persona_insertions'][$c];
        $persona->last_name = $_POST['persona_last_name'][$c];
    	        
        $persona->dob = date('Y-m-d', strtotime($_POST['persona_dob'][$c]));
        $persona->gender = $_POST['persona_gender'][$c];
        $persona->email = $_POST['persona_email'][$c];
        $persona->remarks = $_POST['persona_remarks'][$c];
        
        if ($_POST['persona_dob'][$c] != '') {
          if (!DeKaagCRM_Frontend::validate($_POST['persona_dob'][$c], 'date')) {
    	      $errors['persona_dob_'.$c] = __('Dob is invalid of persona', 'dekaagcrm').' '.($c+1);
    	    }
        }
        if ($_POST['persona_email'][$c] != '') {
          if (!DeKaagCRM_Frontend::validate($_POST['persona_email'][$c], 'email')) {
    	      $errors['persona_email_'.$c] = __('Email is invalid of persona', 'dekaagcrm').' '.($c+1);
    	    }
        }
        $personas[$k] = $persona;
        $c++;
      }
	    if (count($errors) == 0) {
	      $relation->save();
	      
	      if ($_POST['password'] != '') {
          $user->save();
	      }
	      
	      $c = 0;
	      foreach ($personas as $persona) {
	        $persona->save();
	        $c++;
	      }
	      
	      $messages[] = __('Your changes have been saved', 'dekaagcrm');
	      
	      
	    }
	  }
	  DeKaagCRM_Frontend::render('settings', array(
      'object' => $relation,
      'user' => $user,
      'personas' => $personas,
      'errors' => $errors,
      'messages' => $messages
    ));
	}
	
	public static function page_reserveren()
	{
    wp_enqueue_style('oa-widget', plugins_url('lib/vendor/onlineafspraken/theme/default/css/widget.css', __FILE__));
    wp_enqueue_script('oa-widget-prototype', plugins_url('lib/vendor/onlineafspraken/js/prototype.js', __FILE__));
    wp_enqueue_script('oa-widget-calendarview', plugins_url('lib/vendor/onlineafspraken/js/calendarview.js', __FILE__));
    wp_enqueue_script('oa-widget-widget', plugins_url('lib/vendor/onlineafspraken/js/widget.js', __FILE__));

    $_SESSION['company'] = 1;
	  DeKaagCRM_Frontend::render('book', array(
        
    ));
	}
	
	public static function page_reserverenwas()
	{
    wp_enqueue_style('oa-widget', plugins_url('lib/vendor/onlineafspraken/theme/default/css/widget.css', __FILE__));
    wp_enqueue_script('oa-widget-prototype', plugins_url('lib/vendor/onlineafspraken/js/prototype.js', __FILE__));
    wp_enqueue_script('oa-widget-calendarview', plugins_url('lib/vendor/onlineafspraken/js/calendarview.js', __FILE__));
    wp_enqueue_script('oa-widget-widget', plugins_url('lib/vendor/onlineafspraken/js/widget.js', __FILE__));

    $_SESSION['company'] = 2;
	  DeKaagCRM_Frontend::render('book', array(
        
    ));
	}
	
	protected static function validate($value, $validator, $cfg = array())
	{
	  switch($validator) {
	    case 'required':
	      return strlen($value) > 0;
	      break;
	      
	    case 'email':
	      return filter_var($value, FILTER_VALIDATE_EMAIL);
	      break;
	      
	    case 'zipcode':
	      preg_match('/[0-9]{4}[a-zA-Z]{2}/', $value, $ar);
	      return isset($ar[0]);
	      break;
	      
	    case 'date':
	      $parts = explode('-', $value);
	      if (count($parts) <> 3) {
	        return false;
	      }
	      return checkdate((int)$parts[1], (int)$parts[0], (int)$parts[2]);
	      break;
	      
	    case 'password':
	    case 'match':
	      return $value == $cfg['check'];
	      break;
	      
	    case 'minlength':
	      return strlen($value) >= $cfg['length'];
	      break;
	      
	    case 'login':
	      $user_test = DeKaagUser::model()->findByAttributes(new DeKaagCriteria(array('username' => $value)));
        if ($user_test) {
          if ($user_test->{$user_test->prefix().'relation_id'} != $cfg['relation']->id) {
            return false;
          }
        }
        return true;
        break;
	      
	      
	    default:
	      return !is_null($value);
	      break;
	  }
	}
	
	public static function render($template, $vars = array())
	{ 
	  $tpl = dirname(__FILE__).'/tpl/frontend/'.$template.'.php';
	  extract($vars);
	  ob_start();
	  require($tpl);
	  
	  DeKaagCRM_Frontend::$the_content = ob_get_clean();
	}
	
	public static function send_mail($addressees, $subject = '', $template = '', $vars = array(), $attachments = array()) 
	{
    if (!is_array($addressees)) {
      $addressees = array($addressees);
    }
    
    $tpl = dirname(__FILE__).'/tpl/mail/'.$template.'.php';
    extract($vars);
    ob_start();
    require($tpl);
    $mail = ob_get_clean();

    $options = get_option('dekaagcrm_plugin_options');
    $sender_email = isset($options['plugin_sender_email']) ? $options['plugin_sender_email'] : 'info@dekaag.nl';
    $sender_name = isset($options['plugin_sender_name']) ? $options['plugin_sender_name'] : 'De Kaag Watersport';
    $headers = "From: {$sender_name} <{$sender_email}>\r\n";
    
    add_filter( 'wp_mail_content_type', array('DeKaagCRM_Frontend', 'set_html_content_type'));
    wp_mail($addressees, $subject, $mail, $headers, $attachments);
    remove_filter( 'wp_mail_content_type', array('DeKaagCRM_Frontend', 'set_html_content_type'));
	}
	
	public static function set_html_content_type() {
	  return 'text/html';
  }
}




