<?php

class DeKaagCRM_Admin_forms {
  protected static function page_dekaagcrm_forms()
	{
    ob_start();
        
    $action = isset($_GET['action']) && $_GET['action'] != -1 ? $_GET['action'] : 'list';
    $actionMethod = 'page_dekaagcrm_forms_'.$action;
    
    if (!is_callable(self, $actionMethod)) {
      $actionMethod = 'list'; 
    }
    self::$actionMethod();
	  
    return ob_get_clean();
	} 
	
	protected static function page_dekaagcrm_forms_list()
	{
	  if (isset($_GET['s'])) {
	    $s = $_GET['s'];
	    $models = DeKaagForm::model()->findAllByAttributes(new DeKaagCriteria(
	      "title LIKE '%%%s%%'", array($s)
	    ));
	    $model_ids = array();
	    foreach ($models as $model) {
	      $model_ids[] = $model->id;
	    }
	    $date = $s;
	    if (strpos($date, '-')) {
	      $date = date('Y-m-d', strtotime($date));
	    }
	  }
	  else {
	    $models = DeKaagForm::model()->findAll();
	  }
	 	  
	  $data = array();
	  
	  foreach ($models as $model) {
	    $data[] = array(
	      'ID' => $model->id,
	      'title' => $model->title,
	    );
	  }
	  $table = new DeKaagCRMListForms($data);
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
	
	protected static function page_dekaagcrm_form_delete()
	{
	  $model = DeKaagForm::model()->findByPk($_GET['form']);
	  if (!$model) die('Unknown form');
	  $model->delete();
	  echo "<script type=\"text/javascript\">window.location.href='/wp-admin/admin.php?page=dekaagcrm_forms';</script>";
    exit;
	}
	
	protected static function page_dekaagcrm_forms_edit()
	{
	  $errors = array();
      
	  $_SESSION['company'] = in_array($_GET['form'], array(3,4)) ? 2 : 1;
	  
	  wp_enqueue_style('dekaagcrm-admin', plugins_url('css/admin.css', __FILE__));
	  require_once( DEKAAGCRM__PLUGIN_DIR .'lib/vendor/onlineafspraken/lib/widget.php');
    $widget = Widget::getInstance();
    $appointmentTypes = $widget->sendRequest('getAppointmentTypes', array());

    $appTypes = array();
    if (isset($appointmentTypes['AppointmentType'])) {
      foreach ($appointmentTypes['AppointmentType'] as $key => $appointmentType) {
        if ((bool)$appointmentType['CanBeBookedByConsumer']) {
          $appTypes[$appointmentType['Id']] = $appointmentType['Name'];
        }
      }
    }
    
    $create = false;
    if ($_GET['action'] == 'edit') {
      $object = DeKaagForm::model()->findByPk($_GET['form']);
      $title = __( 'Edit form' , 'dekaagcrm');
    }
    else {
      $object = new DeKaagForm;
      $title = __( 'New form' , 'dekaagcrm');
      $create = true;
    }

    $rows = $object->rows;
    if (!$rows) $rows = array();
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($_POST['saveaction'] == 'save') {
        $validate = true;
        $object->title = $_POST['title'];
       
        if (!DeKaagCRM_Admin::validate($_POST['title'], 'required')) {
  	      $errors['title'] = __('Title is required', 'dekaagcrm');
  	    }
    	    
        if (count($errors) > 0) $validate = false;
        
        if ($validate) {
  	      $object->save();
  	      foreach ($rows as $row) {
  	        if ($row->title == '') {
  	          $row->delete(); 
  	        }
  	      }
  	      if ($_POST['saveaction'] == 'save') {
  	        echo "<script type=\"text/javascript\">window.location.href='/wp-admin/admin.php?page=dekaagcrm_forms';</script>";
  	        exit;
  	      }
        }
      }
      
      if ($_POST['saveaction'] != 'save') {
        if (substr($_POST['saveaction'],0,14) == 'deletequestion') {
          $row_id = substr($_POST['saveaction'],15);
          foreach ($rows as $k => $row) {
            if ($row->id == $row_id) {
              $row->delete();
              unset($rows[$k]);
            }
          }
        }
        else {
          $row = new DeKaagFormRow;
          $row->{$row->prefix().'form_id'} = $object->id;
          $row->rowtype = $_POST['saveaction'] == 'addquestion' ? 'question' : 'mutation';
          $row->title = $_POST['saveaction'] == 'addquestion' ? 'Nieuwe vraag' : 'mutation';
          $row->answers = json_encode(array());
          $row->mutations = json_encode(array());
          $row->validators = json_encode(array());
          $rows[] = $row;
          foreach ($rows as $row) {
            $row->save();
          }
        }
      }
    }
  
	  DeKaagCRM_Admin::render('edit', array(
        'object' => $object,
        'rows' => $rows,
        'title' => $title,
        'appTypes' => $appTypes,
        'errors' => $errors
    ));
	}
	
	protected static function page_dekaagcrm_forms_editRow()
	{
	  $errors = array();
      
	  $_SESSION['company'] = in_array($_GET['form'], array(3,4)) ? 2 : 1;
	  
	  wp_enqueue_style('dekaagcrm-admin', plugins_url('css/admin.css', __FILE__));
	  require_once( DEKAAGCRM__PLUGIN_DIR .'lib/vendor/onlineafspraken/lib/widget.php');
    $widget = Widget::getInstance();
    $appointmentTypes = $widget->sendRequest('getAppointmentTypes', array());

    $appTypes = array();
    if (isset($appointmentTypes['AppointmentType'])) {
      foreach ($appointmentTypes['AppointmentType'] as $key => $appointmentType) {
        if ((bool)$appointmentType['CanBeBookedByConsumer']) {
          $appTypes[$appointmentType['Id']] = $appointmentType['Name'];
        }
      }
    }
    
    $create = false;
    if ($_GET['action'] == 'editRow') {
      $object = DeKaagForm::model()->findByPk($_GET['form']);
      $title = __( 'Edit form row' , 'dekaagcrm');
    }
    else {
      $object = new DeKaagForm;
      $title = __( 'New form row' , 'dekaagcrm');
      $create = true;
    }

    $rows = $object->rows;
    if (!$rows) $rows = array();
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($_POST['saveaction'] == 'save') {
        $validate = true;
        
        foreach ($rows as $k => $row) {
          
          if ($row->id != $_GET['row']) continue;
          
          $row->title = $row->rowtype == 'question' ? $_POST['title-'.$row->id] : 'mutation'; 
          $row->explanation = $_POST['explanation-'.$row->id]; 
          $row->fieldtype = $_POST['fieldtype-'.$row->id]; 
          $row->default = $_POST['default-'.$row->id] == 'none' ? null : (int)$_POST['default-'.$row->id]; 
          $row->oninvoice = $_POST['oninvoice-'.$row->id]; 
          
          $answers = $mutations = $validators = array();
          
          foreach($_POST as $key => $value) {
            $l = strlen($row->id);
            if (substr($key,0,8+$l) == 'answer-'.$row->id.'-') {
              $answer = $value;
              if ($answer != '') {
                $answer_id = substr($key,8+$l);
                $answers[$answer_id] = $answer;
              }
            }
            if (substr($key,0,10+$l) == 'mutation-'.$row->id.'-') {
              $mutation = $value;
              $mutation_id = substr($key,10+$l);
              if (isset($answers[$mutation_id])) {
                $type = $_POST['mutationtype-'.$row->id.'-'.$mutation_id];
                $vat = $_POST['vat-'.$row->id.'-'.$mutation_id];
                $resource = $_POST['resource-'.$row->id.'-'.$mutation_id];
                $mutations[$mutation_id] = array(
                  'mutation' => $mutation,
                  'type' => $type,
                  'resource' => $resource,
                  'vat' => $vat
                );
              }
            }
            
            if (substr($key,0,11+$l) == 'validator-'.$row->id.'-') {
              $validate = $value;
              $validator_id = substr($key,11+$l);
              $validator = $_POST['validator-'.$row->id.'-'.$validator_id.'-validator'];
              $value = $_POST['validator-'.$row->id.'-'.$validator_id.'-value'];
              $at = $_POST['validator-'.$row->id.'-'.$validator_id.'-apptype'];
              if ($validate == 'answer') {
                $validate = $_POST['validator-'.$row->id.'-'.$validator_id.'-q'];
                $val = 'is';
                $value = $_POST['validator-'.$row->id.'-'.$validator_id.'-a'];
              }
              if ($validate == 'lastbookdate') {
                $value = date('Y-m-d', strtotime($_POST['validator-'.$row->id.'-'.$validator_id.'-value-lastbookdate']));
              }
              if ($validate == 'date') {
                $value = date('Y-m-d', strtotime($_POST['validator-'.$row->id.'-'.$validator_id.'-value-date']));
              }
              if ($validate == 'age') {
                $value = $_POST['validator-'.$row->id.'-'.$validator_id.'-value-age'];
              }
              if ($validate == 'apptype') {
                $value = is_array($at) ? implode(',',$at) : '';
                $validator = $_POST['validator-'.$row->id.'-'.$validator_id.'-validator'];
              }
              if (is_numeric($validator_id)) {
                if ($validate == 'apptype' && $validator == 'greater') {
                  // impossible setting, this is a new unused row
                }
                else if (!$validate || is_null(validate)) {
                  
                }
                else if ($validate == 'apptype' && $value == '') {
                  // impossible setting, this is a new unused row
                }
                else {
                  $validators[$validator_id] = array(
                    'validate' => $validate,
                    array(
                      'validator' => $validator,
                      'value' => $value              
                    )
                  );
                }
              }
            }
          }
          
          $test = array();
          foreach ($validators as $k => $v) {
            $hash = $v['validate'].'xx'.$v[0]['validator'].'xx'.$v[0]['value'];
            if (isset($test[$hash])) {
              unset($validators[$k]);
            }
            $test[$hash] = true;
          }
          $answers = array_values($answers);
          $mutations = array_values($mutations);
          $validators = array_values($validators);
          
          $row->answers = json_encode($answers);
          $row->mutations = json_encode($mutations);
          $row->validators = json_encode($validators);
          
        }
    	    
        if (count($errors) > 0) $validate = false;
        
        if ($validate) {
  	      foreach ($rows as $row) {
  	        if ($row->id == $_GET['row']) {
  	          $row->save();
  	        }
  	      }
  	      if ($_POST['saveaction'] == 'save') {
  	        echo "<script type=\"text/javascript\">window.location.href='/wp-admin/admin.php?page=dekaagcrm_forms&action=edit&form=".$object->id."';</script>";
  	        exit;
  	      }
        }
      }
      
    }
  
	  DeKaagCRM_Admin::render('editRow', array(
        'object' => $object,
        'rows' => $rows,
        'title' => $title,
        'appTypes' => $appTypes,
        'errors' => $errors
    ));
	}
	
  protected static function page_dekaagcrm_forms_create()
	{
	    return self::page_dekaagcrm_forms_edit();
	}  
}

if(!class_exists('WP_List_Table')){
    require_once(ABSPATH .'wp-admin/includes/class-wp-list-table.php');
}

class DeKaagCRMListForms extends WP_List_Table {
    
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
        $actions = in_array($item['ID'], array(1,2)) ? array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&form=%s">%s</a>',$_REQUEST['page'],'edit',$item['ID'], __('Edit', 'dekaagcrm'))
        ) : array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&form=%s">%s</a>',$_REQUEST['page'],'edit',$item['ID'], __('Edit', 'dekaagcrm')),
            'delete'    => sprintf('<a href="?page=%s&action=%s&form=%s">%s</a>',$_REQUEST['page'],'delete',$item['ID'], __('Delete', 'dekaagcrm')),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_cb($item){
      return '';
        return in_array($item['ID'], array(1,2)) ? '' : sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => __('Name', 'dekaagcrm'),
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
        );
        return $sortable_columns;
    }
   
    function get_bulk_actions() {
        $actions = array(
           // 'delete'    => __('Delete', 'dekaagcrm')
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
              $model = DeKaagForm::model()->findByPk($v);
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
    
    public function extra_tablenav($which)
    {
      if ($which == 'top') {
        //echo '<div style="float:left;padding: 3px 8px 0 0;">';
        //$this->search_box(__('Search', 'dekaagcrm'), 'consumers-filter');
        //echo '</div>';
      }
    }

    function prepare_items() {
        $per_page = 5;
       
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
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
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
