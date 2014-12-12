<?php

class DeKaagRelation extends DeKaagBase {
  public $model = 'relation';
  
  public $relations = array(
    'personas' => array('persona', 'id'),
    'user' => array('user', 'id', true),
  );
  
  public static function model()
  {
    return new DeKaagRelation;
  }
  
  
  
  public function getDOBStr()
  {
    $personas = $this->personas;
    if ($personas) {
      $ret = array();
      foreach ($personas as $persona) {
        $ret[] = sprintf('%s <span style="color:silver">(%s)</span>', date('d-m-Y', strtotime($persona->dob)), $persona->title);
      }
      return implode("<br>\n", $ret);
    }
    return '';
    
  }
}