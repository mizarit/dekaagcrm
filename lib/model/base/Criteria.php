<?php

class DeKaagCriteria
{
  private $validators;
  private $variables;
  
  public function __construct($validators = null, $variables = null)
  {
    $this->validators = $validators;  
    $this->variables = $variables;  
  }
  
  public function sql()
  {
    if (is_array($this->validators)) {
      $wheres = array();
      foreach ($this->validators as $key => $value) {
        if (is_array($value)) {
          list($value, $method) = $value;
          if (is_array($value)) {
            // todo: escape when strings are passed
            $value = '('.implode(', ', $value).')';
          }
          else {
            $value = is_numeric($value) ? $value : "'{$value}'";
          }
          $wheres[] = "{$key} {$method} {$value}";
        }
        else {
          $value = is_numeric($value) ? $value : "'{$value}'";
          $wheres[] = "{$key} = {$value}";
        }
      }
      return 'WHERE '.implode(' AND ', $wheres);
    }
    else if (is_string($this->validators)) {
      return 'WHERE '.call_user_func_array('sprintf', array_merge(array($this->validators), $this->variables));
    }
    return '';
  }
}