<?php

class DeKaagBase
{
  public $values = array();
  protected $relations = array();
  
  public function prefix()
  {
    global $wpdb;
    return $wpdb->prefix.'dekaagcrm_';
  }
  
  public function table()
  { 
    return $this->prefix().$this->model;
  }
  
  public function __get($key)
  {
    if (isset($this->values[$key])) {
      return $this->values[$key];
    }
    
    // try to find it in relations
    if (isset($this->relations[$key])) {
      // autoload the relation
      if (is_string($this->relations[$key])) {
        // simple one-to-many relation
        list($foreign_table, $foreign_key) = explode('_', $this->relations[$key]);
        
        $local_key = $this->{$this->prefix().$this->relations[$key]};
        $class = $this->classMap($foreign_table);
        $object = $class::model()->findByPk($local_key);
        $this->values[$key] = $object;
        return $object;
      }
      else {
        // many-to-?
        $foreign_table = $this->prefix().$this->relations[$key][0];
        $foreign_key = $this->table().'_'.$this->relations[$key][1];
        $local_key = $this->{$this->relations[$key][1]};
        $class = $this->classMap($this->relations[$key][0]);
        $peer = $class::model();
        $objects = $peer->findAllByAttributes(new DeKaagCriteria(array($foreign_key => $local_key)));
        if (strpos($this->relations[$key][0], '_')) {
          $parts = explode('_', $this->relations[$key][0]);
          $class = $this->classMap($parts[1]);
          if (class_exists($class)) {
            // many-to-many
            $objects2 = array();
            foreach ($objects as $object) {
              $k = $this->prefix().$parts[1].'_'.$this->relations[$key][1];
              $object2 = $class::model()->findByPk($object->$k);
              if ($object2) {
                $object2->link = $object;
                $objects2[] = $object2;
              }
            }
            $objects = $objects2;
          }
        }
        
        if (isset($this->relations[$key][2]) && $this->relations[$key][2]) {
          // return single object
          $objects = array_shift($objects);
        }
      }
      
      $this->values[$key] = $objects;
      return $objects;
    }
    
    return null;
  }
  
  public function __set($key, $value)
  {
    $this->values[$key] = $value;
  }
  
  private function classMap($model)
  {
    return str_replace(' ', '', 'DeKaag'.ucwords(str_replace('_', ' ', $model)));
  }

  public function findByPk($pk)
  {
    global $wpdb;
    $row = $wpdb->get_row(sprintf('SELECT * FROM %s WHERE id = %s', $this->table(), (int)$pk));
    if (!$row) {
      return null;
    }
    $class = $this->classMap($this->model);
    $object = new $class;
    foreach ($row as $key => $value) {
      $object->$key = $value;
    }
    return $object;
  }
  
  public function findByAttributes($criteria = null)
  {
    global $wpdb;
    $where = $criteria?$criteria->sql():'';
    $sql = sprintf('SELECT * FROM %s %s LIMIT 1', $this->table(), $where);
    $records = $wpdb->get_results($sql, ARRAY_A); 
    $return = array();
    $class = $this->classMap($this->model);
    foreach ($records as $record) {
      $object = new $class;
      foreach ($record as $key => $value) {
        $object->$key = $value;
      }
      return $object;
    }
    return null;
  }
  
  public function findAll()
  {
    global $wpdb;
    $records = $wpdb->get_results(sprintf('SELECT * FROM %s', $this->table()), ARRAY_A); 
    $return = array();
    $class = $this->classMap($this->model);
    foreach ($records as $record) {
      $object = new $class;
      foreach ($record as $key => $value) {
        $object->$key = $value;
      }
      $return[] = $object;
    }
    return $return;
  }
  
  public function findAllByAttributes($criteria = null)
  {
    global $wpdb;
    $where = $criteria?$criteria->sql():'';
    $sql = sprintf('SELECT * FROM %s %s', $this->table(), $where);
    //echo $sql;
    $records = $wpdb->get_results($sql, ARRAY_A); 
    $return = array();
    $class = $this->classMap($this->model);
    foreach ($records as $record) {
      $object = new $class;
      foreach ($record as $key => $value) {
        $object->$key = $value;
      }
      $return[] = $object;
    }
    return $return;
  }
  
  public function count($criteria = null)
  {
    global $wpdb;
    $where = $criteria?$criteria->sql():'';
    $sql = sprintf('SELECT COUNT(*) FROM %s %s', $this->table(), $where);
    $total = $wpdb->get_var($sql);
    return $total;
  }
  
  public function delete()
  {
    global $wpdb;
    return $wpdb->delete($this->table(), array('id' => $this->id));
  }
  
  public function deleteAll($criteria)
  {
    
  }
  
  public function save($forceNew = false, $send = false)
  {
    global $wpdb;
    $values = $this->values;
    foreach ($values as $k => $v) {
      $x = json_decode($v);
      if (!$x) {
        $values[$k] = addslashes($v);
      }
    }
    //echo '<pre>';
    if (isset($values['id']) && $forceNew === false) {
      $id = $values['id'];
      unset($values['id']);
      $fields = array();
      foreach ($values as $key => $value) {
        if (isset($this->relations[$key])) continue;
        //if ($value) {
          if (is_numeric($value)) {
            $fields[] = "`{$key}` = {$value}";
          }
          else if (is_null($value)) {
            $fields[] = "`{$key}` = NULL";
          }
          else {
            $value = mysql_escape_string($value);
            $fields[] = "`{$key}` = '{$value}'";
          }
        //}
      } 
      $sql = sprintf('UPDATE %s SET %s WHERE id = %s', $this->table(), implode(', ', $fields), $id);
      //echo $sql."\n";
      //var_dump($wpdb->query($sql));
      $wpdb->query($sql);
    }
    else {
      foreach ($this->relations as $key => $value) {
        unset($values[$key]);
      }
      $fields = '`'.implode('`, `', array_keys($values)).'`';
      $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->table(), $fields, "'".implode("', '", array_values($values))."'");
      //echo $sql;
      
      $wpdb->query($sql);
      $this->id = $wpdb->insert_id;
      
      if ($send) {
        mail('ricardo.matters@mizar-it.nl', 'SQL', $sql."\n\nInsert ID ".$this->id);
      }
    }
   // var_dump($this->values);
   // echo $sql;
  }
}