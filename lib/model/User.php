<?php

class DeKaagUser extends DeKaagBase {
  public $model = 'user';
  
  public $relations = array(
    'user' => array('user', 'id'),
    'relation' => 'relation_id'
  );
  
  public static function model()
  {
    return new DeKaagUser;
  }
}