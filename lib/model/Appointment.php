<?php

class DeKaagAppointment extends DeKaagBase {
  public $model = 'appointment';
  
  public $relations = array(
    'persona' => 'persona_id',
    'invoice' => 'invoice_id',
  );
  
  public static function model()
  {
    return new DeKaagAppointment;
  }
  
  public function getTitleStr()
  {
    $rows = json_decode($this->info, true);
    $keys = array_keys($rows);
    return $keys[0];
  }
}