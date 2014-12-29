<?php
/**
 * @package De Kaag CRM
 */
/*
Plugin Name: De Kaag CRM
Plugin URI: http://onlineafspraken.nl
Description: Maatwerk CRM plugin voor De Kaag Zeilschool
Version: 1.2.2
Author: OnlineAfspraken.nl
Author URI: http://onlineafspraken.nl
License: GPLv2 or later
Text Domain: dekaagcrm
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

if (!session_id()) session_start();

define( 'DEKAAGCRM_VERSION', '1.2.2' );
define( 'DEKAAGCRM__MINIMUM_WP_VERSION', '3.1' );
define( 'DEKAAGCRM__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DEKAAGCRM__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DEKAAGCRM_DELETE_LIMIT', 100000 );

global $dekaagcrm_db_version;
$dekaagcrm_db_version = '1.1';

register_activation_hook( __FILE__, array( 'DeKaagCRM', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'DeKaagCRM', 'plugin_deactivation' ) );

require_once( DEKAAGCRM__PLUGIN_DIR . 'class.dekaagcrm.php' );
if ( is_admin() ) {
  new WPFDGitHubPluginUpdater( __FILE__, 'mizarit', 'dekaagcrm' );
}

require_once( DEKAAGCRM__PLUGIN_DIR . 'widget/class.dekaagcrm-widget.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'widget/class.dekaagcrm-ideal.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'widget/class.dekaagcrm-login.php' );

require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/base/Base.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/base/Criteria.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/base/PersonaDiploma.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/User.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/Relation.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/Persona.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/Diploma.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/Appointment.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/Invoice.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/InvoiceRow.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/Payment.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/Form.php' );
require_once( DEKAAGCRM__PLUGIN_DIR . 'lib/model/FormRow.php' );

add_action('init', array('DeKaagCRM', 'init'));
add_action('plugins_loaded', array( 'DeKaagCRM', 'get_instance'));

add_action('widgets_init', function(){ 
  register_widget('DeKaagCRM_Widget');
  register_widget('DeKaagCRM_Ideal');
  register_widget('DeKaagCRM_Login');
});

if (is_admin()) {
  require_once( DEKAAGCRM__PLUGIN_DIR . 'class.dekaagcrm-admin-forms.php');
	require_once( DEKAAGCRM__PLUGIN_DIR . 'class.dekaagcrm-admin-invoices.php');
	require_once( DEKAAGCRM__PLUGIN_DIR . 'class.dekaagcrm-admin-consumers.php');
	require_once( DEKAAGCRM__PLUGIN_DIR . 'class.dekaagcrm-admin.php');
	add_action('init', array('DeKaagCRM_Admin', 'init'));
}
else {
  require_once( DEKAAGCRM__PLUGIN_DIR . 'class.dekaagcrm-frontend.php');
  add_action('init', array( 'DeKaagCRM_Frontend', 'init'));
} 
