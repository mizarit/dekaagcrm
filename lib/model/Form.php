<?php

class DeKaagForm extends DeKaagBase {
  public $model = 'form';
  
  public $relations = array(
    'rows' => array('form_row', 'id')
  );
  
  public static function model()
  {
    return new DeKaagForm;
  }
  
  public static function render($form_id, $vars = array())
  {
    extract($vars);
    ob_start();
    $form = DeKaagForm::model()->findByPk($form_id);
    
    
    $offset = 0;
    
    if ($form_id == 2 || $form_id == 4) {  
      if(isset($_SESSION['booking']['answers'])) {
        foreach ($_SESSION['booking']['answers'] as $key => $value) {
          echo '<input type="hidden" name="answer_'.$key.'" id="answer_'.$key.'" value="'.$value.'">';
          $row = DeKaagFormRow::model()->findByPk($key);
          if ($row) {
            if ($row->{$row->prefix().'form_id'} == 1 || $row->{$row->prefix().'form_id'} == 3) {
              $mutations = json_decode($row->mutations);
              $mutation = $mutations[$value];
              $v = $mutation->type == 'price' ? $mutation->mutation : ($total / 100) * $mutation->mutation;
              $total = $total + $v;
            }
          }
        }
        // TODO: foreach all visible form rows on the current form, which have a default answer that also has a mutation
        // ie. vroegboekkorting is default yes, and this default answer has a mutation of -30 euro
      }
    }
    
    if ($form) {
      $rows = DeKaagFormRow::model()->findAllByAttributes(new DeKaagCriteria(array($form->prefix().'form_id' => $form_id)));
      $mutationkeys = array();
    ?>
      <?php 
      foreach ($rows as $row) { 
        $hasJS = false;
        if ($row->isVisible()) { 
          $mutationkeys[$row->id] = 0;
          if ($row->rowtype =='mutation') {
            
          }
          else {
            $is_visible = true;
          ?>
      <div class="form-row" id="question_<?php echo $row->id; ?>" <?php if($row->visibleIf()) { 
        list($question_id, $answer_id) = $row->visibleIf();
        if ($form->id == 1 || $form->id == 3 || $_SESSION['booking']['answers'][$question_id] != $answer_id) {  
          echo ' style="display:none;"';
          $is_visible = false;
        }
      } ?>>
        <h3><?php echo $row->title; ?></h3>
        <?php if ($row->explanation != '') echo '<p class="hint">'.$row->explanation.'</p>'; ?>
        <div>
      <?php 
      $first = true;
      $mutations = json_decode($row->mutations);
      switch ($row->fieldtype) {
        case 'radio':
          foreach ($row->answers() as $key => $answer) { 
            $mutation = $mutations[$key];
            $checked = '';
            if (!is_null($row->default)) {
              if ($row->default == $key) {
                $checked = ' checked="checked"';  
                if ($is_visible && $mutation->mutation != 0) {
                  $offset_m = ($mutation->type=='price' ? $mutation->mutation : ($total / 100) * $mutation->mutation);
                  $offset += $offset_m;
                  $mutationkeys[$row->id] = $offset_m;
                }
              }
            }
            else {
              if ($first && $form->id == 1) {
                $checked = ' checked="checked"';  
                if ($is_visible && $mutation->mutation != 0) {
                  $offset_m = ($mutation->type=='price' ? $mutation->mutation : ($total / 100) * $mutation->mutation);
                  $offset += $offset_m;
                  $mutationkeys[$row->id] = $offset_m;
                }
                $first = false;
              }
            }
            ?>
            <input <?php echo $checked; ?> onclick="mutateTotal(<?php echo $row->id; ?>, '<?php echo $mutation->mutation!=''?$mutation->mutation:0; ?>', '<?php echo $mutation->type; ?>'<?php if ($form_id == 1) { ?>,'' , true<?php } ?>);" type="radio" name="answer_<?php echo $row->id; ?>" value="<?php echo $key; ?>" id="answer_<?php echo $row->id; ?>_<?php echo $key; ?>" style="width:auto;"> <label for="answer_<?php echo $row->id; ?>_<?php echo $key; ?>"><?php echo $answer; ?></label><br />
          <?php 
          }
          break;
          
        case 'select':
          ?>
          <select style="width:450px;" onchange="mutateTotal(<?php echo $row->id; ?>, '<?php echo $mutation->mutation!=''?$mutation->mutation:0; ?>', '<?php echo $mutation->type; ?>'<?php if ($form_id == 1) { ?>,'', true<?php } ?>);" name="answer_<?php echo $row->id; ?>" id="answer_<?php echo $row->id; ?>_<?php echo $key; ?>" style="width:auto;">
          <?php
          foreach ($row->answers() as $key => $answer) { 
            $mutation = $mutations[$key];
            $checked = '';
            if (!is_null($row->default)) {
              if ($row->default == $key) {
                $checked = ' selected="selected"';  
                if ($is_visible && $mutation->mutation != 0) {
                  $offset_m = ($mutation->type=='price' ? $mutation->mutation : ($total / 100) * $mutation->mutation);
                  $offset += $offset_m;
                  $mutationkeys[$row->id] = $offset_m;
                }
              }
            }
            else {
              if ($first && $form->id == 1) {
                $checked = ' selected="selected"';  
                if ($is_visible && $mutation->mutation != 0) {
                  $offset_m = ($mutation->type=='price' ? $mutation->mutation : ($total / 100) * $mutation->mutation);
                  $offset += $offset_m;
                  $mutationkeys[$row->id] = $offset_m;
                }
                $first = false;
              }
            }
            ?>
            <option value="<?php echo $key; ?>" <?php echo $checked; ?>><?php echo $answer; ?></option>
          <?php 
          }
          ?>
          </select>
          <?php 
          break;
          
        case 'input':
            $mutation = $mutations[0]; ?>
            <input onkeyup="mutateTotal(this.value, '<?php echo $mutation->mutation!=''?$mutation->mutation:0; ?>', '<?php echo $mutation->type; ?>', <?php echo $row->id; ?><?php if ($form_id == 1) { ?>, true<?php } ?>);" type="text" name="answer_<?php echo $row->id; ?>" id="answer_<?php echo $row->id; ?>_<?php echo $key; ?>" style="width:450px;"><br />
          <?php 
          break;
      }
       ?>
        </div>
      </div>
      <?php
        }
        
        if ($row->visibleIf()) { 
          list($question_id, $answer_value) = $row->visibleIf();
          if (!$hasJS) {
            
          ?>
        <script type="text/javascript">
        jQuery('input[name=answer_<?php echo $question_id; ?>]').change(function(i, s) {
          jQuery('#question_<?php echo $row->id; ?>').css({display: jQuery(this).val() == '<?php echo $answer_value; ?>' ? 'block' : 'none'});
          mutateTotal(false, false);
        });
        </script>
        <?php 
              $hasJS = true;
            }
          }
        } 
      }
      ?>
<?php if ($form_id == 1 || $form_id == 3) { ?>  
<script type="text/javascript">
window.formValidate = function()
{
  valid = true;
  jQuery('.form-row').each(function(s, i) {
    if(jQuery(i).css('display') == 'block') {
      if (!jQuery("input[name='answer_"+i.id.substr(9) + "']:checked").val()) {
        if (jQuery("input[name='answer_"+i.id.substr(9) + "']").val()=='') {
          valid = false;
        }
      }
    }
  });
  if (!valid) {
    alert('Je hebt niet alle vragen beantwoord.');
  }
  return valid;
};

window.mutateTotal = function(key, mutation, type, row, refresh)
{
  if (mutation ) {
    v = parseFloat(type == 'price' ? mutation : (total / 100) * mutation);
    k = isNaN(key) || key == '' ? row : key;
    
    // store the mutation for this key ( radio ) or row ( input ),
    // for further use when other answers change
    if(isNaN(key) || key == '') {
    // handle the text inputs
      if (key.length == 0) {
        v = 0;
      }
    }
    mutationkeys[k] = v;
  }
  
  newtotal = parseFloat(total);
  for (i in mutationkeys) {
    if(!jQuery('#question_'+i) || jQuery('#question_'+i).css('display') == 'block') {
      x = parseFloat(mutationkeys[i]);
      newtotal = newtotal + x;
    }
  }
  if ($('total-sum')) {
    $('total-sum').innerHTML = newtotal.toFixed(2).replace('.',',');
  }
  
  if (refresh) {
    widget.handleCalendar();
  }
};
</script>
<?php } ?>

<?php if ($form_id == 2 || $form_id == 4) {  
  ?>
<script>
mutationkeys = <?php echo json_encode($mutationkeys); ?>;
total = <?php echo $total; ?>;

//console.log(<?php echo $total; ?>);
//console.log(<?php echo $offset; ?>);
//console.log(mutationkeys);
//console.log(total);
</script>

<div id="pricing-info">
  <p>De totaalprijs wordt <strong>€ <span id="total-sum"><?php echo number_format($total + $offset,2,',','.'); ?></span></strong></p>
</div>      
<?php } ?>
<?php
    }
    return ob_get_clean();
  }
}
