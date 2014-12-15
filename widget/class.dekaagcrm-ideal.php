<?php
/**
 * @package De Kaag CRM
 */
class DeKaagCRM_Ideal extends WP_Widget {

	function __construct() {
		load_plugin_textdomain('dekaagcrm');
		
		parent::__construct(
			'dekaagcrm_ideal',
			__( 'De Kaag CRM Ideal Widget' , 'dekaagcrm'),
			array( 'description' => __( 'Description of the De Kaag CRM iDeal widget' , 'dekaagcrm') )
		);

		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'wp_head', array( $this, 'css' ) );
		}
	}
	
	function css() {
?>
<style type="text/css">
</style>
<?php
	}
	
	public function widget($args, $instance)
	{
	  echo $args['before_widget'];

	  if (in_array($_SERVER['REMOTE_ADDR'], array('94.209.9.24', '192.168.8.1'))) {
	    var_dump($_GET);
	    var_dump($instance);
	    var_dump($_SERVER);
	    var_dump($_REQUEST);
	    var_dump($_SESSION);
	  }
	  if (!empty($instance['mollie_key']) && isset($_GET['invoice'])) {
	    
	    $invoice = DeKaagInvoice::model()->findByCode($_GET['invoice']);
	    
	    if ($invoice->id > 0) {
  	    $hash = crypt($invoice->invoicenr, $invoice->invoicenr.'DEKAAG');
  	    
  	    if (isset($_GET['hash']) && $_GET['hash'] == $hash) {
    	    $total = $invoice->getTotalRemainingStr(false);
    	    $downpayment = false;
    	    if (isset($_GET['downpayment']) && $invoice->downpayment != 'none') {
    	      if ($invoice->getTotalRemainingStr(false) < $invoice->getTotalStr(false)) {
    	        // downpayment was already done
    	      }
    	      else {
      	      // calculate the downpayment
      	      if ($invoice->downpayment == 'fixed') {
      	        $total = $invoice->dpvalue;
      	      }
      	      else {
      	        $total = ($invoice->getTotalStr(false)/100)*round($invoice->dpvalue);
      	      }
      	      $downpayment = true;
    	      }
    	    }
    	    
    	    if ($total == 0) {
    	      echo '<p><strong>'.__('This invoice was already paid','dekaagcrm').'</strong></p>';
    	    }
    	    else {
      	    require_once dirname(__FILE__) . "/../lib/vendor/Mollie/API/Autoloader.php";
      
            $mollie = new Mollie_API_Client;
            $mollie->setApiKey($instance['mollie_key']); 
            
            $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
          	$hostname = $_SERVER['HTTP_HOST'];
          	
          	if (isset($_SESSION['payment_id'])) {
          	  $mollie_payment = $mollie->payments->get($_SESSION['payment_id']);
            	if ($mollie_payment->status == 'paid') {
            	  $payment = new DeKaagPayment;
            	  $payment->total = $mollie_payment->amount;
            	  $payment->paymethod = 'ideal';
            	  $payment->status = 'success';
            	  $payment->remarks = $mollie_payment->id.' '.$mollie_payment->paidDatetime;
            	  $payment->{$payment->prefix().'invoice_id'} = $invoice->id;
            	  $payment->date = date('Y-m-d H:i:s');
            	  $payment->save();
            	  
            	  if($invoice->getTotalRemainingStr(false) == 0) {
            	    $invoice->status = 4;
            	  }
            	  else {
            	    $invoice->status = 3;
            	  }
            	  $invoice->save();
            	  
            	  echo '<p><strong>'.__('Your payment was received succesfully','dekaagcrm').'</strong></p>';
            	}
            	unset($_SESSION['payment_id']);
          	}
            else if ($_SERVER["REQUEST_METHOD"] != "POST")
          	{
          		$issuers = $mollie->issuers->all();
              wp_enqueue_style('dekaagcrm-frontend', '/wp-content/plugins/dekaagcrm/css/frontend.css');
              $what = $downpayment ? 'de aanbetaling' : ( $invoice->getTotalRemainingStr(false) < $invoice->getTotalStr(false) ? 'het resterende bedrag' : 'de factuur');
              
              echo '<p>Je staat op het punt om '.$what.' á <strong>€ '.number_format($total,2,',','.').'</strong> met iDeal af te rekenen.</p>';
          		echo '<div id="widget-container"><form method="post">Selecteer je bank: <select name="issuer">';
          
          		foreach ($issuers as $issuer) {
          			if ($issuer->method == Mollie_API_Object_Method::IDEAL)
          			{
          				echo "<option value=\"{$issuer->id}\">{$issuer->name}</option>";
          			}
          		}
          		echo '<option value="">of selecteer later</option>';
          		echo '</select> <button type="submit">Betaling uitvoeren</button></form></div>';
          	}
           	else {
            	$order_id = $invoice->invoicenr;
            	
            	$payment = $mollie->payments->create(array(
            		"amount"       => $total,
            		"method"       => Mollie_API_Object_Method::IDEAL,
            		"description"  => $downpayment ? __('Downpayment for', 'dekaagcrm').' '.$invoice->invoicenr : ($invoice->getTotalRemainingStr(false) < $invoice->getTotalStr(false) ? __('Remainder for', 'dekaagcrm').' '.$invoice->invoicenr : $invoice->invoicenr),
            		"redirectUrl"  => "{$protocol}://{$hostname}/{$_SERVER['REQUEST_URI']}&order_id={$order_id}",
            		"metadata"     => array(
            			"order_id" => $order_id,
            		),
            		"issuer"       => !empty($_POST["issuer"]) ? $_POST["issuer"] : NULL
            	));
            	
            	$_SESSION['payment_id'] = $payment->id;
          
              echo "<script type=\"text/javascript\">window.location.href='{$payment->getPaymentUrl()}';</script>";
           	}
    	    }
  	    }
  	    else {
  	      echo '<p><strong>'.__('Illegal invoice link','dekaagcrm').'</strong></p>';
  	    }
	    } 
	    else {
	      echo '<p><strong>'.__('Unknown invoice','dekaagcrm').'</strong></p>';
  	  }
	  }
	  echo $args['after_widget'];
	}
	
	public function form($instance)
	{
	  $api_key = isset($instance['mollie_key']) ? $instance[ 'mollie_key' ] : 'test_ldJD0vbfomfwiHsSIN3uhYJyTP4BP7';
		?>
		<p>
  		<label for="<?php echo $this->get_field_id('mollie_key'); ?>"><?php _e( 'API key:' ); ?></label> 
  		<input class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('mollie_key'); ?>" type="text" value="<?php echo esc_attr($api_key); ?>">
		</p>
		
		<?php 
	}
	
	public function update($new_instance, $old_instance)
	{
	  $instance = array();
		$instance['mollie_key'] = ( ! empty( $new_instance['mollie_key'] ) ) ? strip_tags( $new_instance['mollie_key'] ) : '';
		return $instance;
	}
}
