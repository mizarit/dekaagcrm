<?php

class DeKaagInvoiceRow extends DeKaagBase {
  public $model = 'invoice_row';
  
  public $relations = array(
  );
  
  public static function model()
  {
    return new DeKaagInvoiceRow;
  }
}