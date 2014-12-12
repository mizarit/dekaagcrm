<?php

class DeKaagPersona extends DeKaagBase {
  public $model = 'persona';
  
  public $relations = array(
    'diplomas' => array('persona_diploma', 'id'),
    'relation' => array('relation', 'id')
  );
  
  public static function model()
  {
    return new DeKaagPersona;
  }
}