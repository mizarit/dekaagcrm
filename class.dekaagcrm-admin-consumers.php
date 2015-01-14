<?php

class DeKaagCRM_Admin_consumers extends DeKaagCRM_Admin_invoices {
  protected static function page_dekaagcrm_consumers($return = false)
	{
    ob_start();
        
    $action = isset($_GET['action']) && $_GET['action'] != -1 ? $_GET['action'] : 'list';
    $actionMethod = 'page_dekaagcrm_consumers_'.$action;
    
    if (!is_callable(self, $actionMethod)) {
      $actionMethod = 'list'; 
    }
    $ret = self::$actionMethod($return);
	  
    $ret2 = ob_get_clean();
    return $return ? $ret : $ret2;
	}
	
	protected static function page_dekaagcrm_consumers_suggest()
	{
	  $relations = DeKaagRelation::model()->findAllByAttributes(new DeKaagCriteria(array(
	    'title' => array("%{$_GET['term']}%", 'LIKE')
	  )));
	  
	  $data = array();
	  foreach ($relations as $relation) {
	    $data[] = array(
	      'label' => $relation->title,
	      'link' => $relation->id,
	      'address' => $relation->address.PHP_EOL.$relation->zipcode.' '.$relation->city
	    );
	  }
	  echo json_encode($data);
	  exit;
	}
	
	protected static function page_dekaagcrm_consumers_delete()
	{
	  $model = DeKaagRelation::model()->findByPk($_GET['consumer']);
	  if (!$model) die('Unknown relation');
	  $model->delete();
	  echo "<script type=\"text/javascript\">window.location.href='/wp-admin/admin.php?page=dekaagcrm_consumers';</script>";
    exit;
	}
	
	protected static function page_dekaagcrm_consumers_edit()
	{
	    wp_enqueue_script('jquery-ui-datepicker');
      wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
      wp_enqueue_script( 'password-strength-meter' );
      wp_enqueue_script('dekaagcrm-consumers', plugins_url('js/consumers.js', __FILE__));
      wp_enqueue_style('dekaagcrm-admin', plugins_url('css/admin.css', __FILE__));
      wp_localize_script( 'password-strength-meter', 'pwsL10n', array(
        'empty' => __( 'Strength indicator' ),
        'short' => __( 'Very weak' ),
        'bad' => __( 'Weak' ),
        'good' => _x( 'Medium', 'password strength' ),
        'strong' => __( 'Strong' ),
        'mismatch' => __( 'Mismatch' )
      ) );
    
      $errors = array();
      
      $diplomas = DeKaagDiploma::model()->findAll();
      
        $create = false;
	    if ($_GET['action'] == 'edit') {
	      $object = DeKaagRelation::model()->findByPk($_GET['consumer']);
	      $title = __( 'Edit relation' , 'dekaagcrm');
	    }
	    else {
	      $object = new DeKaagRelation;
	      $title = __( 'New relation' , 'dekaagcrm');
	      $create = true;
	      $object->created_at = date('Y-m-d H:i:s');
	    }
	    
	    $user = $object->user;
	    if (!$user) {
	      $user = new DeKaagUser;
	    }
	    
	    $errors = array();
	    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	      $validate = true;
	      
	      $object->first_name = $_POST['first_name'];
	      $object->insertions = $_POST['insertions'];
	      $object->last_name = $_POST['last_name'];
	      $object->title = $_POST['first_name'].' '.$_POST['insertions'].' '.$_POST['last_name'];
	      $object->email = $_POST['email'];
	      $object->phone = $_POST['phone'];
	      $object->address = $_POST['address'];
	      $object->zipcode = $_POST['zipcode'];
	      $object->city = $_POST['city'];
	      $object->modified_at = date('Y-m-d H:i:s');
	      
	      if (!DeKaagCRM_Admin::validate($_POST['last_name'], 'required')) {
  	      $errors['last_name'] = __('Last name is required', 'dekaagcrm');
  	    }
  	    
  	    if (!DeKaagCRM_Admin::validate($_POST['email'], 'required')) {
  	      $errors['email'] = __('Email is required', 'dekaagcrm');
  	    }
  	    else if (!DeKaagCRM_Admin::validate($_POST['email'], 'email')) {
  	      $errors['email'] = __('Email is invalid', 'dekaagcrm');
  	    }
  	    
  	    if ($_POST['zipcode'] != '') {
  	      if (!DeKaagCRM_Admin::validate($_POST['zipcode'], 'zipcode')) {
    	      $errors['zipcode'] = __('Zipcode is invalid', 'dekaagcrm');
    	    }
  	    }
  	    
  	    if ($_POST['password'] != '') {
    	    if (!DeKaagCRM_Admin::validate($_POST['password'], 'match', array('check' => $_POST['password_retyped']))) {
    	      $errors['password'] = __('The 2 passwords do not match', 'dekaagcrm');
    	    }
  	    }
    	    
  	    if ($_POST['user_login'] != '') {
    	    if (!DeKaagCRM_Admin::validate($_POST['user_login'], 'minlength', array('length' => 5))) {
    	      $errors['user_login'] = __('The username is too short', 'dekaagcrm');
    	    }
    	    else if (!DeKaagCRM_Admin::validate($_POST['user_login'], 'login', array('relation' => $object))) {
    	      $errors['user_login'] = __('The username is already taken', 'dekaagcrm');
    	    }
    	    else if (strstr($_POST['user_login'], ' ')) {
    	      $errors['user_login'] = __('Gebruikersnaam mag geen spaties bevatten', 'dekaagcrm');
    	    }
  	    }
	      
        $user->username = $_POST['user_login'];
	      
	      if (count($errors) > 0) $validate = false;
	      
	      if ($validate) {
  	      $object->save();
  	      
  	      if ($_POST['user_login'] != '') {
  	        $user->{$object->prefix().'relation_id'} = $object->id;
  	        $user->username = $_POST['user_login'];
  	        if($_POST['password'] != '') {
  	          $chars = '_|'.implode('', array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9)));
  	          $charCount = strlen($chars);
              $salt = '';
              for($i=0; $i<22; $i++){
                  $r = rand(0,$charCount-1);
                  $salt .= $chars[$r];
              }
  	          $salt = '$5$'.$salt.'$';
              $password = crypt($_POST['password'], $salt);
              $user->password = $password;
              $user->salt = $salt;
  	        }
  	        $user->role = 1;
  	        $user->save();
  	        
  	        if (isset($_POST['send_password'])) {
  	          $options = get_option('dekaagcrm_plugin_options');
              $sender_name = isset($options['plugin_sender_name']) ? $options['plugin_sender_name'] : 'De Kaag Watersport';

  	          DeKaagCRM_Admin::send_mail(
        	       $object->email,
        	       __('Your account credentials', 'dekaagcrm'),
        	       'credentials',
        	       array(
        	         'username' => $user->username,
        	         'password' => $_POST['password'],
        	         'relation' => $object,
        	         'sender_name' => $sender_name
        	       ),
        	       array()
        	    );
  	        }
  	      }
  	      
  	      $personas = $object->personas;
  	      for ($c = 0; $c < $_POST['personas']; $c++) {
  	        if (!isset($personas[$c])) {
  	          $personas[$c] = new DeKaagPersona;
  	          $link = $object->prefix().'relation_id';
  	          $personas[$c]->$link = $object->id;
  	          $personas[$c]->created_at = date('Y-m-d H:i:s');
  	        }
  	        $persona = $personas[$c];
  	        
  	        if ($_POST['persona_first_name'][$c+1] == '' && $persona->id > 0) {
  	          $persona->delete();  
  	        }
  	        else if($_POST['persona_first_name'][$c+1] != '') {
    	        $persona->modified_at = date('Y-m-d H:i:s');
    	        $persona->title = $_POST['persona_first_name'][$c+1].' '.$_POST['persona_insertions'][$c+1].' '.$_POST['persona_last_name'][$c+1];
    	        $persona->first_name = $_POST['persona_first_name'][$c+1];
    	        $persona->insertions = $_POST['persona_insertions'][$c+1];
    	        $persona->last_name = $_POST['persona_last_name'][$c+1];
    	        if ($_POST['persona_dob'][$c+1] != '') {
    	          $persona->dob = date('Y-m-d', strtotime($_POST['persona_dob'][$c+1]));
    	        }
    	        $persona->gender = isset($_POST['persona_gender'][$c+1]) ? $_POST['persona_gender'][$c+1] : 'm';
    	        $persona->email = $_POST['persona_email'][$c+1];
    	        $persona->remarks = $_POST['persona_remarks'][$c+1];
    	        $persona->remarks_private = $_POST['persona_remarks_private'][$c+1];
    	        $persona->save();
    	        
    	        if (isset($_POST['persona_diploma'][$c+1])) {
    	          // we have diploma's
    	          $diploma_ids = array_keys($_POST['persona_diploma'][$c+1]);
    	          // first of all, remove any preexiting diploma's
    	          $diplomas = $persona->diplomas;
    	          foreach ($diplomas as $k => $diploma) {
    	            if (!in_array($diploma->id, $diploma_ids)) {
    	              $diploma->link->delete();
    	              unset($diplomas[$k]);
    	            }
    	            $persona->diplomas = $diplomas;
    	          }
    	          foreach ($diploma_ids as $diploma_id) {
    	            $found = false;
    	            foreach ($diplomas as $diploma) {
    	              if ($diploma->id == $diploma_id) {
    	                $found = true;
    	                $link = $diploma->link;
    	                break;
    	              }
    	            }
    	            if (!$found) {
    	              $link = new DeKaagPersonaDiploma;
    	              $field = $persona->prefix().'diploma_id';
    	              $link->$field = $diploma_id;
    	              $field = $persona->prefix().'persona_id';
    	              $link->$field = $persona->id;
    	            }
    	            $link->date = date('Y-m-d', strtotime($_POST['persona_diploma_date'][$c+1][$diploma_id]));
    	            $link->save();
    	          }
    	        }
    	        else {
    	          $diplomas = $persona->diplomas;
    	          foreach ($diplomas as $diploma) {
    	            $diploma->link->delete();
    	          }
    	        }
    	        $personas[$c] = $persona;
    	        $object->personas = $personas;
  	        }
  	      }
  	      
  	      echo "<script type=\"text/javascript\">window.location.href='/wp-admin/admin.php?page=dekaagcrm_consumers';</script>";
  	      exit;
	      }
	      else {
	        //var_dump($errors);
	      }
	    }
	   DeKaagCRM_Admin::render('edit', array(
        'object' => $object,
        'user' => $user,
        'diplomas' => $diplomas,
        'title' => $title,
        'errors' => $errors
    ));
	}
	
	protected static function page_dekaagcrm_consumers_create()
	{
	    return self::page_dekaagcrm_consumers_edit();
	}
	
	protected static function page_dekaagcrm_consumers_list($return = false)
	{

	  if (isset($_GET['s'])) {
	    $s = $_GET['s'];
	    $models = DeKaagRelation::model()->findAllByAttributes(new DeKaagCriteria(
	      "title LIKE '%%%s%%' OR address LIKE '%%%s%%' OR zipcode LIKE '%%%s%%' OR phone LIKE '%%%s%%' OR email LIKE '%%%s%%'", array($s, $s, $s, $s, $s)
	    ));
	    $model_ids = array();
	    foreach ($models as $model) {
	      $model_ids[] = $model->id;
	    }
	    $date = $s;
	    if (strpos($date, '-')) {
	      $date = date('Y-m-d', strtotime($date));
	    }
	    $personas = DeKaagPersona::model()->findAllByAttributes(new DeKaagCriteria(
	      "title LIKE '%%%s%%' OR email LIKE '%%%s%%' OR dob = '%s' OR dob LIKE '%%%s%%'", array($s, $s, $date, $date)
	    ));
	    foreach ($personas as $persona) {
	      $relation_id = $persona->{$persona->prefix().'relation_id'};
	      if (!in_array($relation_id, $model_ids)) {
	        $models[] = DeKaagRelation::model()->findByPk($relation_id);
	        $model_ids[] = $relation_id;
	      }
	    }
	  }
	  else {
	    $models = DeKaagRelation::model()->findAll();
	  }
	 	  
	  $data = array();
	  
	  foreach ($models as $model) {
	    $data[] = array(
	      'ID' => $model->id,
	      'title' => $model->title,
	      'first_name' => $model->first_name,
	      'insertions' => $model->insertions,
	      'last_name' => $model->last_name,
	      'email' => $model->email,
	      'dob' => $model->getDOBStr()
	    );
	  }
	  
	  if ($return) {
	    return $data;
	  }
	  
	  $table = new DeKaagCRMListConsumers($data);
    //Fetch, prepare, sort, and filter our data...
    if( isset($_POST['s']) ){
      $table->prepare_items($_POST['s']);
    } else {
      $table->prepare_items();
    }
    
    DeKaagCRM_Admin::render('list', array(
      'table' => $table
    ));
	}
	
}

if(!class_exists('WP_List_Table')){
    require_once(ABSPATH .'wp-admin/includes/class-wp-list-table.php');
}

class DeKaagCRMListConsumers extends WP_List_Table {
    
    var $data = array();

    function __construct($data = array()){
        global $status, $page;
                
        $this->data = $data;
        //Set parent defaults
        parent::__construct( array(
            'singular'  => __('consumer', 'dekaagcrm'),     //singular name of the listed records
            'plural'    => __('consumers', 'dekaagcrm'),    //plural name of the listed records
            'ajax'      => true        //does this table support ajax?
        ));
        
    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'email':
            case 'dob':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_title($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&consumer=%s">%s</a>',$_REQUEST['page'],'edit',$item['ID'], __('Edit', 'dekaagcrm')),
            'delete'    => sprintf('<a href="?page=%s&action=%s&consumer=%s">%s</a>',$_REQUEST['page'],'delete',$item['ID'], __('Delete', 'dekaagcrm')),
        );
        
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
            'title'     => __('Name', 'dekaagcrm'),
            'email'    => __('Email', 'dekaagcrm'),
            'dob'  => __('Date of birth', 'dekaagcrm')
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
            'email'    => array('email',false),
            'dob'  => array('dob',false)
        );
        return $sortable_columns;
    }
   
    function get_bulk_actions() {
        $actions = array(
            'delete'    => __('Delete', 'dekaagcrm')
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
    
    public function extra_tablenav($which)
    {
      if ($which == 'top') {
        echo '<div style="float:left;padding: 3px 15px 0 0;">';
        $this->export_box(__('Export', 'dekaagcrm'), 'consumers-export');
        echo '</div>'; 
        echo '<div style="float:left;padding: 3px 8px 0 0;">';
        $this->search_box(__('Search', 'dekaagcrm'), 'consumers-filter');
        echo '</div>';
        
      }
    }

    function prepare_items() {
        $per_page = 10;
       
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $this->data;
                
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'ID'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        
        /***********************************************************************
         * ---------------------------------------------------------------------
         * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
         * 
         * In a real-world situation, this is where you would place your query.
         *
         * For information on making queries in WordPress, see this Codex entry:
         * http://codex.wordpress.org/Class_Reference/wpdb
         * 
         * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
         * ---------------------------------------------------------------------
         **********************************************************************/
        
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
}
