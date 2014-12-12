<?php

class DeKaagDiploma extends DeKaagBase {
  public $model = 'diploma';
  
  public $relations = array(
  );
  
  public static function model()
  {
    return new DeKaagDiploma;
  }
}