<?php

class DeKaagPersonaDiploma extends DeKaagBase {
  public $model = 'persona_diploma';
  
  public $relations = array(
  );
  
  public static function model()
  {
    return new DeKaagPersonaDiploma;
  }
}