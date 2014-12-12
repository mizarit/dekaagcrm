<?php

class DeKaagPayment extends DeKaagBase {
  public $model = 'payment';
  
  public $relations = array(
  );
  
  public static function model()
  {
    return new DeKaagPayment;
  }
}