<?php

class DeKaagCRM_Admin_invoices extends DeKaagCRM_Admin_forms {
  protected static function page_dekaagcrm_transactions($return = false)
  {
    ob_start();
    
    $action = isset($_GET['action']) && !is_numeric($_GET['action']) ? $_GET['action'] : 'list';
    $actionMethod = 'page_dekaagcrm_transactions_'.$action;
    
    if (is_callable(self, $actionMethod)) {
      $ret = self::$actionMethod($return);
    }
	  
    $ret2 = ob_get_clean();
    return $return ? $ret : $ret2;
	}
	
  protected static function page_dekaagcrm_transactions_ideal()
  {
    require_once dirname(__FILE__) . "/lib/vendor/Mollie/API/Autoloader.php";

    $mollie = new Mollie_API_Client;
    $mollie->setApiKey("test_ldJD0vbfomfwiHsSIN3uhYJyTP4BP7"); 
    
    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
  	$hostname = $_SERVER['HTTP_HOST'];
  	
  	if (isset($_SESSION['payment_id'])) {
  	  $payment = $mollie->payments->get($_SESSION['payment_id']);
    	echo "<p>Your payment status is '" . htmlspecialchars($payment->status) . "'.</p>";
    	unset($_SESSION['payment_id']);
  	}
    else if ($_SERVER["REQUEST_METHOD"] != "POST")
  	{
  		$issuers = $mollie->issuers->all();
  
  		echo '<form method="post">Select your bank: <select name="issuer">';
  
  		foreach ($issuers as $issuer) {
  			if ($issuer->method == Mollie_API_Object_Method::IDEAL)
  			{
  				echo "<option value=\"{$issuer->id}\">{$issuer->name}</option>";
  			}
  		}
  		echo '<option value="">or select later</option>';
  		echo '</select><button>OK</button></form>';
  		exit;
  	}
   	else {
    	$order_id = time();
    
    	
    
    	/*
    	 * Payment parameters:
    	 *   amount        Amount in EUROs. This example creates a € 27.50 payment.
    	 *   method        Payment method "ideal".
    	 *   description   Description of the payment.
    	 *   redirectUrl   Redirect location. The customer will be redirected there after the payment.
    	 *   metadata      Custom metadata that is stored with the payment.
    	 *   issuer        The customer's bank. If empty the customer can select it later.
    	 */
    	$payment = $mollie->payments->create(array(
    		"amount"       => 27.50,
    		"method"       => Mollie_API_Object_Method::IDEAL,
    		"description"  => "My first iDEAL payment",
    		"redirectUrl"  => "{$protocol}://{$hostname}/wp-admin/admin.php?page=dekaagcrm_transactions&action=ideal&order_id={$order_id}",
    		"metadata"     => array(
    			"order_id" => $order_id,
    		),
    		"issuer"       => !empty($_POST["issuer"]) ? $_POST["issuer"] : NULL
    	));
    	
    	$_SESSION['payment_id'] = $payment->id;
  
      echo "<script type=\"text/javascript\">window.location.href='{$payment->getPaymentUrl()}';</script>";
   	}
    exit; 
  }
  
	protected static function page_dekaagcrm_transactions_update()
	{
    $model = DeKaagInvoice::model()->findByPk($_GET['invoice']);
    if (!$model) die('Unknown invoice');
    // manually adding payment for invoice
    $payment = DeKaagPayment::model();
    $payment->paymethod = 'bank';
    $payment->total = $model->getTotalRemainingStr(false);
    $payment->{$payment->prefix().'invoice_id'} = $model->id;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $payment->date = date('Y-m-d', strtotime($_POST['date']));
      $payment->paymethod = $_POST['paymethod'];
      $payment->total = (float)str_replace(',', '.', str_replace('.', '', $_POST['total']));
      $payment->status = 'success';
      
      $validate = true;
      if ($model->getTotalRemainingStr(false) <= 0) {
        $validate = false;
      }
      
      if ($validate) {
        $payment->save();
      
        if ($model->getTotalRemainingStr(false) == 0) {
          $model->status = 4;
          // TODO send mail notifiction?
        }
        else {
          $model->status = 3;
        }
        $model->save();
        
        echo "<script type=\"text/javascript\">window.location.href='/wp-admin/admin.php?page=dekaagcrm_transactions';</script>";
	      exit;
      }
    }
      
    DeKaagCRM_Admin::render('update', array(
      'model' => $model,
      'payment' => $payment
    ));
	}
	
	protected static function page_dekaagcrm_transactions_view()
	{
    $model = DeKaagInvoice::model()->findByPk($_GET['invoice']);
    if (!$model) die('Unknown invoice');
    // manually adding payment for invoice
    
    $payments = DeKaagPayment::model()->findAllByAttributes(new DeKaagCriteria(array(
      $model->prefix().'invoice_id' => $model->id,
       'status' => 'success'
    )));
    
    DeKaagCRM_Admin::render('view', array(
      'model' => $model,
      'payments' => $payments
    ));
	}
	
	protected static function page_dekaagcrm_transactions_edit()
	{
	  $model = DeKaagInvoice::model()->findByPk($_GET['invoice']);
	  if (!$model) die('Unknown invoice');
	  self::page_dekaagcrm_transactions_create($model);
	}
	
	protected static function page_dekaagcrm_transactions_create($model = null)
	{
	    wp_enqueue_script('jquery-ui-datepicker');
	    wp_enqueue_script('jquery-ui-autocomplete');
	    wp_enqueue_script('dekaagcrm-consumers', plugins_url('js/transactions.js', __FILE__));
      wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
      wp_enqueue_style('dekaagcrm-admin', plugins_url('css/admin.css', __FILE__));
    
      $create = false;
      if (!$model) {
        $create = true;
        $model = new DeKaagInvoice;
        $model->company = 1;
        $model->date = date('Y-m-d');
        $model->enddate = date('Y-m-d', strtotime('+1 month'));
        $model->dpdate = date('Y-m-d', strtotime('+1 month'));
      }
      
      $rows = $model->rows;
      
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $model->company = $_POST['company'];
        $model->date = date('Y-m-d', strtotime($_POST['date']));
        $model->enddate = date('Y-m-d', strtotime($_POST['enddate']));
        $model->address = $_POST['address'];
        $model->{$model->prefix().'relation_id'} = $_POST['consumer-id'];
        if (isset($_POST['allow_downpayment'])) {
          $model->downpayment = $_POST['downpayment'];
          $v = $_POST['downpayment_'.$_POST['downpayment']. '_value'];
          $model->dpvalue = $v;
          $model->dpdate = date('Y-m-d', strtotime($_POST['payedbefore']));
        }
        else {
          $model->downpayment = 'none';
        }
        
        $rows = array();
        $total = 0;
        for ($c = 0; $c < 10; $c++) {
          $desc = $_POST['row_desc'][$c];
          $price = $_POST['row_price'][$c] != '' ? (float)$_POST['row_price'][$c] : '';
          $vat = $_POST['row_vat'][$c] != '' ? (float)$_POST['row_vat'][$c] : '21';
          if ($desc != '') {
            $row = new DeKaagInvoiceRow;
            $row->description = $desc;
            $row->total = $price;
            $row->vat = $vat;
            $rows[] = $row;
            $total += $price;
          }
        }
        $model->rows = $rows;
        
        $validate = true;
        
        if ($validate) {
          if ($total < 0) {
            // this is a credit invoice
            $model->status = 5;
          }
          $model->save();
          $old_rows = DeKaagInvoiceRow::model()->findAllByAttributes(new DeKaagCriteria(array($model->prefix().'invoice_id' => $model->id)));
          foreach($old_rows as $old_row) {
            $old_row->delete();
          }
         
          foreach ($rows as $row) {
            $row->{$row->prefix().'invoice_id'} = $model->id;
            $row->save();
          }
          
          echo "<script type=\"text/javascript\">window.location.href='/wp-admin/admin.php?page=dekaagcrm_transactions';</script>";
  	      exit;
        }
      }
      
      DeKaagCRM_Admin::render('create', array(
        'model' => $model,
        'create' => $create,
        'rows' => $rows
      ));
	}
	
	protected static function page_dekaagcrm_transactions_delete()
	{
	  $model = DeKaagInvoice::model()->findByPk($_GET['invoice']);
	  if (!$model) die('Unknown invoice');
	  $model->delete();
	  echo "<script type=\"text/javascript\">window.location.href='/wp-admin/admin.php?page=dekaagcrm_transactions';</script>";
    exit;
	}
	
	protected static function page_dekaagcrm_transactions_download()
	{
	  $model = DeKaagInvoice::model()->findByPk($_GET['invoice']);
	  if (!$model) die('Unknown invoice');
	  
    $filename = $model->invoicenr.'.pdf';
    
    DeKaagInvoice::generate_pdf($filename, $model);
    
    $url = plugins_url('data/'.$filename, __FILE__);
    
    wp_redirect($url, 302);
    //header('Content-Type: application/pdf');
    //header("Content-Transfer-Encoding: Binary");
    //header("Content-disposition: attachment; filename=".$filename);
    //readfile($path);
    //exit;
	}
	
	protected static function page_dekaagcrm_transactions_list($return = false)
	{
	  if (isset($_GET['s'])) {
	    $s = $_GET['s'];
	    $date = $s;
	    if (strpos($date, '-')) {
	      $date = date('Y-m-d', strtotime($date));
	    }
	    
	    $models = DeKaagInvoice::model()->findAllByAttributes(new DeKaagCriteria(
	      "invoicenr LIKE '%%%s%%' OR address LIKE '%%%s%%' OR date LIKE '%%%s%%' OR enddate LIKE '%%%s%%'", array($s, $s, $date, $date)
	    ));
	   
	  }
	  else {
	    $models = DeKaagInvoice::model()->findAll();
	  }
	  
	  if (isset($_GET['status']) && $_GET['status'] > 0) {
	    foreach ($models as $key => $model) {
	      if ($model->status != $_GET['status']) {
	        unset($models[$key]);
	      }
	    }
	  }
	    
    $data = array();
      
    foreach ($models as $model) {
      if(!isset($_GET['export']) || (isset($_GET['export']) && $_GET['export'] == 2)) {
        $data[$model->id] = array(
          'ID' => $model->id,
          'title' => $model->invoicenr,
          'date' => date('d-m-Y', strtotime($model->date)),
          'total' => $model->getTotalStr(),
          'total_remaining' => $model->status != 5 ? $model->getTotalRemainingStr() : '',
          'enddate' => $model->status != 5 ? date('d-m-Y', strtotime($model->enddate)) : '',
          'status' => $model->getStatusStr(),
          'company' => $model->getCompanyStr(),
          'relation' => $model->getRelationStr(),
          'relation_first_name' => $model->relation->first_name,
          'relation_insertions' => $model->relation->insertions,
          'relation_last_name' => $model->relation->last_name,
          'address' => $model->relation->address,
          'zipcode' => $model->relation->zipcode,
          'city' => $model->relation->city,
          'phone' => $model->relation->phone,
          'phone_mobile' => $model->relation->phone_mobile,
          'phone_extra' => $model->relation->phone_extra
        );
        if ($model->persona) {
          $data[$model->id]['persona'] = $model->persona->title;
          $data[$model->id]['persona_first_name'] = $model->persona->first_name;
          $data[$model->id]['persona_insertions'] = $model->persona->insertions;
          $data[$model->id]['persona_last_name'] = $model->persona->last_name;
          $data[$model->id]['persona_email'] = $model->persona->email;
          $data[$model->id]['persona_dob'] = $model->persona->dob;
          $data[$model->id]['persona_gender'] = $model->persona->gender;
          $data[$model->id]['persona_remarks'] = $model->persona->remarks;
          $data[$model->id]['persona_remarks_private'] = $model->persona->remarks_private;
          $d = array();
          foreach ($model->persona->diplomas as $diploma) {
            $d[] = $diploma->title;
          }
          $data[$model->id]['persona_diplomas'] = implode(', ', $d);
        }
        else {
          $data[$model->id]['persona'] = '';
          $data[$model->id]['persona_email'] = '';
          $data[$model->id]['persona_dob'] = '';
          $data[$model->id]['persona_gender'] = '';
          $data[$model->id]['persona_remarks'] = '';
          $data[$model->id]['persona_remarks_private'] = '';
          $data[$model->id]['persona_diplomas'] = '';
        }
        
        $appointment = DeKaagAppointment::model()->findByAttributes(new DeKaagCriteria(array($model->prefix().'invoice_id' => $model->id)));
        if ($appointment) {
          $info = json_decode($appointment->info, true);
          $cells = array_keys($info);
          $dummy_data = array_fill(0,20,'');
          $tmp = array_slice(array_merge($cells, $dummy_data), 0, 20);
          foreach ($tmp as $k => $v) {
            $data[$model->id]['extra_'.$k] = $v;
          }
        }
        else {
          $dummy_data = array_fill(0,20,'');
          foreach ($dummy_data as $k => $v) {
            $data[$model->id]['extra_'.$k] = $v;
          }
        }
      }
      else {
        $data[$model->id] = array(
          'ID' => $model->id,
          'title' => $model->invoicenr,
          'date' => date('d-m-Y', strtotime($model->date)),
          'total' => $model->getTotalStr(),
          'total_val' => $model->getTotalStr(false),
          'total_remaining' => $model->status != 5 ? $model->getTotalRemainingStr() : '',
          'total_remaining_val' => $model->getTotalRemainingStr(false),
          'enddate' => $model->status != 5 ? date('d-m-Y', strtotime($model->enddate)) : '',
          'relation' => $model->getRelationStr(),
          'status' => $model->getStatusStr(),
          'company' => $model->getCompanyStr()
        );
      }
    }
    
    if ($return) {
	    return $data;
	  }
    $table = new DeKaagCRMListInvoices($data);
    $table->prepare_items();

    DeKaagCRM_Admin::render('list', array(
      'table' => $table
    ));
	}
}

if(!class_exists('WP_List_Table')){
    require_once(ABSPATH .'wp-admin/includes/class-wp-list-table.php');
}

class DeKaagCRMListInvoices extends WP_List_Table {
    
    var $data = array();

    function __construct($data = array()){
        global $status, $page;
                
        $this->data = $data;
        //Set parent defaults
        parent::__construct( array(
            'singular'  => __('invoice', 'dekaagcrm'),     //singular name of the listed records
            'plural'    => __('invoices', 'dekaagcrm'),    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ));
        
    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'title':
            case 'date':
            case 'enddate':
            case 'relation':
            case 'total':
            case 'total_remaining':
            case 'status':
            case 'company':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_title($item){
        //Build row actions
        if ($item['status'] == __('pending', 'dekaagcrm') || $item['status'] == __('credit', 'dekaagcrm')) {
          if ($item['title'] == '') {
            $actions['edit'] = sprintf('<a href="?page=%s&action=%s&invoice=%s">%s</a>',$_REQUEST['page'],'edit',$item['ID'], __('Edit', 'dekaagcrm'));
            $actions['delete'] = sprintf('<a href="?page=%s&action=%s&invoice=%s">%s</a>',$_REQUEST['page'],'delete',$item['ID'], __('Delete', 'dekaagcrm'));
          }
          if ($item['status'] == __('credit', 'dekaagcrm') && $item['title'] != '') {
            $actions['download'] = sprintf('<a href="?page=%s&action=%s&invoice=%s">%s</a>',$_REQUEST['page'],'download',$item['ID'], __('Download PDF', 'dekaagcrm'));
          }
          
        }
        else {
          if ($item['total_remaining_val'] > 0) {
            $actions['update'] = sprintf('<a href="?page=%s&action=%s&invoice=%s">%s</a>',$_REQUEST['page'],'update',$item['ID'], __('Add payment', 'dekaagcrm'));
          }
          if ($item['total_remaining_val'] < $item['total_val']) {
            $actions['view'] = sprintf('<a href="?page=%s&action=%s&invoice=%s">%s</a>',$_REQUEST['page'],'view',$item['ID'], __('View payments', 'dekaagcrm'));
          }
          $actions['download'] = sprintf('<a href="?page=%s&action=%s&invoice=%s">%s</a>',$_REQUEST['page'],'download',$item['ID'], __('Download PDF', 'dekaagcrm'));
          //$actions['delete'] = sprintf('<a href="?page=%s&action=%s&invoice=%s">%s</a>',$_REQUEST['page'],'delete',$item['ID'], __('Delete', 'dekaagcrm'));
        }
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => __('Invoice nr', 'dekaagcrm'),
            'relation'    => __('Relation', 'dekaagcrm'),
            'date'    => __('Date', 'dekaagcrm'),
            'total'    => __('Total', 'dekaagcrm'),
            'total_remaining'    => __('Remaining', 'dekaagcrm'),
            'enddate'    => __('End date', 'dekaagcrm'),
            
            'status'  => __('Status', 'dekaagcrm'),
            'company'  => __('Company', 'dekaagcrm'),
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('invoicenr',false),     //true means it's already sorted
            'date'    => array('date',false),
            'total'    => array('total',false),
            'total_remaining'    => array('total_remaining',false),
            'enddate'    => array('enddate',false),
            'relation'    => array('relation',false),
            'status'  => array('status',false),
            'company'  => array('company',false)
        );
        return $sortable_columns;
    }
   
    function get_bulk_actions() {
        $actions = array(
            //'delete'    => __('Delete', 'dekaagcrm')
        );
        return $actions;
    }
   
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            //wp_die('Items deleted (or they would be if we had items to delete)!');
            $delete = array();
            $field = 'consumer';
            if (!isset($_GET[$field])) {
              $field = __($field, 'dekaagcrm');
            }
            
            $value = $_GET[$field];
            if ($value && !is_array($value)) $value = array($value);
            
            foreach ($value as $v) {
              $model = DeKaagRelation::model()->findByPk($v);
              if ($model) {
                $model->delete();
                foreach ($this->data as $x => $y) {
                  if ($y['ID'] == $v) {
                    unset($this->data[$x]);
                  }
                }
              }
            }
        }
        
    }
    
    public function export_box($label, $id)
    {
      ?>
      <p class="search-box">
      <input type="hidden" name="export" id="export" value="0">
    	<label for="consumers-filter-search-input" class="screen-reader-text"><?php echo $label; ?>:</label>
    	<button type="submit" onclick="jQuery('#export').val(1);" class="button" id="export-submit" name=""><?php echo $label; ?></button></p>
	<?php
    }
    
    public function export_full_box($label, $id)
    {
      ?>
      <p class="search-box">
    	<label for="consumers-filter-search-input" class="screen-reader-text"><?php echo $label; ?>:</label>
    	<button type="submit" onclick="jQuery('#export').val(2);" class="button" id="export-submit" name=""><?php echo $label; ?></button></p>
    	
    	<?php
    }
    
    public function extra_tablenav($which)
    {
      if ($which == 'top') {
        $options = array(
          -1 => __('All statusses', 'dekaagcrm'),
          1 => __('pending', 'dekaagcrm'),
          2 => __('open', 'dekaagcrm'),
          3 => __('payed_partial', 'dekaagcrm'),
          4 => __('payed', 'dekaagcrm'),
          5 => __('credit', 'dekaagcrm'),
        );
        $selected = isset($_GET['status']) ? $_GET['status'] : -1;
        ?>
     <div class="alignleft actions bulkactions" style="position:relative;left:-8px;">
			 <select name="status">
			 <?php foreach ($options as $value => $label) {
			   $checked = $selected == $value ? ' selected="selected"' : '';
			   echo "<option{$checked} value=\"{$value}\">{$label}</option>".PHP_EOL;
			 } ?>
       </select>
       <input type="submit" value="<?php echo __('Apply filter', 'dekaagcrm'); ?>" class="button action" id="doaction" name="">
		</div>
		<div style="float:left;padding: 3px 15px 0 0;">
    <?php $this->export_box(__('Export', 'dekaagcrm'), 'transactions-export'); ?>
		</div>
		<div style="float:left;padding: 3px 15px 0 0;">
    <?php $this->export_full_box(__('Export full', 'dekaagcrm'), 'transactions-export'); ?>
		</div>
		<div style="float:left;padding: 3px 8px 0 0;">
    <?php $this->search_box(__('Search', 'dekaagcrm'), 'transactions-filter'); ?>
    </div>
    <?php
      }
    }


    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        $per_page = 5;
       
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
       
        $this->_column_headers = array($columns, $hidden, $sortable);
     
        $this->process_bulk_action();
       
        $data = $this->data;
          
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
                
     
        $current_page = $this->get_pagenum();
      
        $total_items = count($data);
    
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
      
        $this->items = $data;
      
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
}
