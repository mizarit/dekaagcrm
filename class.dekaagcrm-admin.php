<?php

class DeKaagCRM_Admin extends DeKaagCRM_Admin_consumers {

	private static $initiated = false;

	public static function init() {
	  if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	public static function init_hooks() {

		self::$initiated = true;

		add_action( 'admin_init', array( 'DeKaagCRM_Admin', 'load_plugin_textdomain' ));
		add_action( 'admin_init', array( 'DeKaagCRM_Admin', 'admin_init' ));
		add_action( 'admin_menu', array( 'DeKaagCRM_Admin', 'admin_menu' )); 
		add_action( 'admin_notices', array( 'DeKaagCRM_Admin', 'display_notice'));
		add_action('admin_menu', array( 'DeKaagCRM_Admin','plugin_admin_add_page'));
	}
	
	public function plugin_admin_add_page() {
    add_options_page('De Kaag CRM', 'De Kaag CRM', 'manage_options', 'dekaagcrm', array('DeKaagCRM_Admin', 'plugin_options_page'));
  }
	
	public function load_plugin_textdomain()
  {
    load_plugin_textdomain('dekaagcrm', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
  }
  
  public function plugin_options_page() {
?>
<div>
  <h2><?php echo __('De Kaag CRM options'); ?></h2>
  <form action="options.php" method="post">
<?php settings_fields('dekaagcrm_plugin_options'); ?>
<?php do_settings_sections('dekaagcrm_plugin'); ?>
 
    <input name="Submit" type="submit" value="<?php echo __('Save Changes', 'dekaagcrm'); ?>" />
  </form>
</div>
<?php
  } 
	
	public static function admin_init() {

    if (isset($_GET['action']) && in_array($_GET['action'], array('download', 'suggest', 'sort'))) {
      self::display_page();
      exit;
    }
    if (isset($_GET['export']) && ($_GET['export'] == 1 || $_GET['export'] == 2)) {
      $page = 'page_'.$_GET['page'];
  	  if (!is_callable(array(self, $page))) {
  	    die('Invalid URL');
  	  }
  	   
      $rows = self::$page(true);
  	  $keys = array_keys($rows);
  	  $first = array_shift($keys);
      $headrow = $rows[$first];
      
      $fn = $_GET['export'] == 1 ? 'export.csv' : 'export-full.csv';
      
      header('Pragma: public');
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Content-Description: File Transfer');
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename='.$fn.';');
      header('Content-Transfer-Encoding: binary'); 
       
      //open file pointer to standard output
      $fp = fopen('php://output', 'w');
       
      //add BOM to fix UTF-8 in Excel
      fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
      fputcsv($fp, array_keys($headrow),";", '"');
      foreach ($rows as $data) {
        foreach ($data as $k => $v) {
          $v = strip_tags($v); 
          $data[$k] = $v;
        }
        fputcsv($fp, $data, ";", '"');
      }
      fclose($fp);
      exit;
    }
	    
    register_setting( 'dekaagcrm_plugin_options', 'dekaagcrm_plugin_options', array('DeKaagCRM_Admin', 'plugin_options_validate'));
    add_settings_section('plugin_main', __('Invoice settings', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_section_text'), 'dekaagcrm_plugin');
    add_settings_field('plugin_accountholder', __('Accountholder', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_main', array('accountholder'));
    add_settings_field('plugin_accountno', __('IBAN', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_main', array('accountno'));
    add_settings_field('plugin_iban', __('IBAN for payments', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_main', array('iban'));
    add_settings_field('plugin_btw', __('VAT number', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_main', array('btw'));
    add_settings_field('plugin_kvk', __('CoC number', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_main', array('kvk'));
    add_settings_field('plugin_site', __('Website', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_main', array('site'));
    add_settings_field('plugin_email', __('Email address', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_main', array('email'));
    add_settings_field('plugin_reminder', __('Reminder period', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_main', array('reminder'));
    
    add_settings_section('plugin_paymentprovider', __('Payment provider settings', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_section_text_mail'), 'dekaagcrm_plugin');
    add_settings_field('plugin_mollie_key', __('Mollie API key', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_paymentprovider', array('mollie_key'));
     
    add_settings_section('plugin_mail', __('Mail settings', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_section_text_mail'), 'dekaagcrm_plugin');
    add_settings_field('plugin_sender_email', __('Email address', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_mail', array('sender_email'));
    add_settings_field('plugin_sender_name', __('Sender name', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_mail', array('sender_name'));
    
    add_settings_section('plugin_oaapi1', __('OnlineAfspraken API De Kaag', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_section_text_api'), 'dekaagcrm_plugin');
    add_settings_field('plugin_oaapi_key1', __('API key', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_oaapi1', array('oaapi_key1'));
    add_settings_field('plugin_oaapi_secret1', __('API secret', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_oaapi1', array('oaapi_secret1'));
    add_settings_field('plugin_oaapi_url1', __('URL', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_oaapi1', array('oaapi_url1'));
    
    add_settings_section('plugin_oaapi2', __('OnlineAfspraken API De Spaarnwoude', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_section_text_api'), 'dekaagcrm_plugin');
    add_settings_field('plugin_oaapi_key2', __('API key', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_oaapi2', array('oaapi_key2'));
    add_settings_field('plugin_oaapi_secret2', __('API secret', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_oaapi2', array('oaapi_secret2'));
    add_settings_field('plugin_oaapi_url2', __('URL', 'dekaagcrm'), array('DeKaagCRM_Admin', 'plugin_setting_string'), 'dekaagcrm_plugin', 'plugin_oaapi2', array('oaapi_url2'));
	}

	public function plugin_section_text()
	{
	  echo '<p>'.__('Configure these values to be used on the PDF invoices', 'dekaagcrm').'</p>';
	}
	
	public function plugin_section_text_mail()
	{
	  echo '<p>'.__('Configure the name and email sender for all outgoing emails', 'dekaagcrm').'</p>';
	}
	
	public function plugin_section_text_api()
	{
	  echo '<p>'.__('Configure the API connection info for this company', 'dekaagcrm').'</p>';
	}
	
	public function plugin_setting_string($args) 
	{
	  $options = get_option('dekaagcrm_plugin_options');
	  $defaults = array(
	    'account_no' => 'NL08 RABO 0102575568',
	    'account_holder' => 'De Kaag Watersport',
	    'account_iban' => 'NL08 RABO 0102575568',
	    'account_btw' => '813666181B01',
	    'account_kvk' => '28102735',
	    'account_site' => 'http://www.dekaag.nl',
	    'account_email' => 'info@dekaag.nl',
	    'account_reminder' => '+3 days',
	    'account_sender_email' => 'info@dekaag.nl',
	    'account_sender_name' => 'De Kaag Watersport',
	    'account_oaapi_key1' => 'fhlg83culd13-bzld03',
	    'account_oaapi_secret1' => '22571c6007f22bbb9d3d9dbaf5a4b7e2a976fea3',
	    'account_oaapi_url1' => 'http://onlineafspraken.dev.mizar-it.nl/APIREST',
	    'account_oaapi_key2' => 'fhlg83culd13-bzld03',
	    'account_oaapi_secret2' => '22571c6007f22bbb9d3d9dbaf5a4b7e2a976fea3',
	    'account_oaapi_url2' => 'http://onlineafspraken.dev.mizar-it.nl/APIREST',
	  );
	  $v = isset($options['plugin_'.$args[0]]) ? $options['plugin_'.$args[0]] : $defaults['account_'.$args[0]];
    echo "<input id='plugin_{$args[0]}' name='dekaagcrm_plugin_options[plugin_{$args[0]}]' size='40' type='text' value='{$v}'>";
	}
	
	public function plugin_options_validate($input)
	{
	  $options = get_option('dekaagcrm_plugin_options');
	  return $input;
	}
	
	public static function admin_menu() {
	  load_plugin_textdomain('dekaagcrm', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
	  add_menu_page( __( 'Consumers' , 'dekaagcrm'), __( 'Consumers' , 'dekaagcrm'), 'manage_options', 'dekaagcrm_consumers', array( 'DeKaagCRM_Admin', 'display_page'), 'dashicons-groups', 6);
	  add_menu_page( __( 'Transactions' , 'dekaagcrm'), __( 'Transactions' , 'dekaagcrm'), 'manage_options', 'dekaagcrm_transactions', array( 'DeKaagCRM_Admin', 'display_page') , 'dashicons-products', 7);
	  add_menu_page( __( 'Forms' , 'dekaagcrm'), __( 'Forms' , 'dekaagcrm'), 'manage_options', 'dekaagcrm_forms', array( 'DeKaagCRM_Admin', 'display_page' ), 'dashicons-clipboard', 8);
	  
		if ( version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) ) {
			add_action( "load-$hook", array( 'DeKaagCRM_Admin', 'admin_help' ) );
		}
	}
	
	public static function display_page() {
	  $page = 'page_'.$_GET['page'];
	  if (!is_callable(array(self, $page))) {
	    die('Invalid URL');
	  }
	  echo self::$page();
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
	  list($plugin, $module) = explode('_', $_GET['page']);
	  $tpl = dirname(__FILE__).'/tpl/'.$module.'/'.$template.'.php';
	  extract($vars);
	  require($tpl);
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
    
    add_filter( 'wp_mail_content_type', array('DeKaagCRM_Admin', 'set_html_content_type'));
    wp_mail($addressees, $subject, $mail, $headers, $attachments);
    remove_filter( 'wp_mail_content_type', array('DeKaagCRM_Admin', 'set_html_content_type'));
	}
	
	public static function set_html_content_type() {
	  return 'text/html';
  }
	
	private static function page_dekaagcrm()
	{
	  return self::page_dekaagcrm_consumers();
	}

	public static function display_notice() {

	}
}




