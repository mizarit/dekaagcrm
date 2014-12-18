<?php

class DeKaagFormRow extends DeKaagBase {
  public $model = 'form_row';
  
  public $relations = array(
  );
  
  public static function model()
  {
    return new DeKaagFormRow;
  }
  
  public function isVisible()
  {
    $validators = json_decode($this->validators, true);
    $visible = true;
    foreach ($validators as $validator) {
      switch($validator['validate']) {
        case 'age':
          if (isset($_SESSION['booking']['dob']) && $_SESSION['booking']['dob'] != '') {
            list($d,$m,$y) = explode('-', $_SESSION['booking']['dob']);
            $dob = @mktime(0,0,0,$m,$d,$y);
           
            $age = date_diff(date_create(date('Y-m-d', $dob)), date_create('today'))->y;
            switch ($validator['0']['validator']) {
              case 'greater':
                if ($age <= $validator['0']['value']) $visible = false;
                break;
              case 'smaller':
                if ($age > $validator['0']['value']) $visible = false;
                break;
              case 'equal':
                if ($age != $validator['0']['value']) $visible = false;
                break;
                
            }
          }
          else {
            $visible =  $validator['0']['value'] == 0 ? true : false;
          }
          break;
        case 'date':
          $date = time();
          switch ($validator['0']['validator']) {
            case 'greater':
              if ($date < strtotime($validator['0']['value'])) $visible = false;
              break;
            case 'smaller':
              if ($date >= strtotime($validator['0']['value'])) $visible = false;
              break;
            case 'equal':
              if ($date != strtotime($validator['0']['value'])) $visible = false;
              break;
              
          }
          
          break;
          
        case 'lastbookdate':
          if (isset($_SESSION['dekaag_relation_id'])) {
            $apps = DeKaagAppointment::model()->findAllByAttributes(new DeKaagCriteria(array($this->prefix().'relation_id' => $_SESSION['dekaag_relation_id'])));
            $date = 0;
            foreach ($apps as $app) {
              $test = strtotime($app->date);
              $date = max(array($date, $test));
            }
            
            switch ($validator['0']['validator']) {
              case 'greater':
                if ($date < strtotime($validator['0']['value'])) $visible = false;
                break;
              case 'smaller':
                if ($date >= strtotime($validator['0']['value'])) $visible = false;
                break;
              case 'equal':
                if ($date != strtotime($validator['0']['value'])) $visible = false;
                break;
            }
          }
          break;
          
        case 'apptype':
          $apptypes = explode(',', $validator['0']['value']);
          $appTypeId = $_SESSION['booking']['apptypeId'];
          //if ($appTypeId != -1) {
            switch ($validator['0']['validator']) {
              case 'in':
                if (!in_array($appTypeId, $apptypes)) $visible = false;
                break;  
              case 'notin':
                if (in_array($appTypeId, $apptypes)) $visible = false;
                break;  
              
            }
          //}
          break;
      }
    }
    return $visible;
  }
  
  /**
   * returns the field this question is depending on, or false
   *
   * @return unknown
   */
  public function visibleIf()
  {
    $validators = json_decode($this->validators, true);
    foreach($validators as $validator) {
      if(is_numeric($validator['validate'])) {
        return array((int)$validator['validate'], $validator[0]['value']);
      }
    }
    if ($this->{$this->prefix()}.'form_id' == 2) {
      $form = DeKaagForm::model()->findByPk(1);
      $validators = json_decode($form->validators, true);
      foreach($validators as $validator) {
        if(is_numeric($validator['validate'])) {
          return array((int)$validator['validate'], $validator[0]['value']);
        }
      }
    }
    return false;
  }
  
  public function answers()
  {
    return json_decode($this->answers);
  }
  
}