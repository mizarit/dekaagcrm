	<div class="wrap">
    <div id="icon-users" class="icon32"><br/></div>
      <h2><?php echo $title; ?></h2>
      <form novalidate="novalidate" class="validate" id="editform" name="editform" method="post" action="">
      <input type="hidden" id="saveaction" name="saveaction" value="save">
      
<?php
$validatorcount = $answercount = array();
$m = 0;
?>
        <?php foreach ($rows as $id => $row) { 
          if ($row->id != $_GET['row']) continue;
          
          if ($row->rowtype == 'mutation') $m++;
          ?>
            <div class="postbox " id="row-container-<?php echo $id; ?>" style="margin-bottom:3px;">
              <h3 class="hndle" style="padding-left:10px;margin: 2px 0;"><span><?php echo $row->rowtype == 'question' ? $row->title : __('Mutation', 'dekaagcrm').' '.$m; ?></span>
              <a href="javascript:if(confirm('Weet je zeker dat je deze vraag of mutatie wilt verwijderen? Je kunt dit niet ongedaan magen. Indien vragen afhankelijk zijn van het antwoord op deze vraag moet je deze vragen corrigeren.')) {jQuery('#saveaction').val('deletequestion_<?php echo $row->id; ?>');jQuery('#editform').submit();}" style="top:5px;right:5px;" class="delete-row"><img src="<?php echo plugins_url('../../img/delete.png', __FILE__); ?>" alt="<?php echo __('Delete', 'dekaagcrm'); ?>"></a>
              </h3>
              <div class="inside" style="margin-bottom: 0;">
              
              <div style="display:block;" id="row-container-inner-<?php echo $id; ?>">
                <table class="form-table">
        	       <tbody>
              
              <?php if ($row->rowtype == 'question') { ?>
                    <tr class="form-field">
                		  <th scope="row"><label for="title-<?php echo $row->id; ?>"><?php echo __('Question', 'dekaagcrm'); ?></label></th>
                  		<td>
                  		<div class="admin-form-row">
                  		  <input type="text" value="<?php echo $row->title; ?>" id="title-<?php echo $row->id; ?>" name="title-<?php echo $row->id; ?>" style="width: 470px;">
                  		</div>
                  		</td>
                  	</tr>
                  	<tr class="form-field">
                		  <th scope="row"><label for="explanation-<?php echo $row->id; ?>"><?php echo __('Explanation', 'dekaagcrm'); ?></label></th>
                  		<td>
                  		<div class="admin-form-row">
                  		  <textarea id="title-<?php echo $row->id; ?>" name="explanation-<?php echo $row->id; ?>" style="width: 470px;"><?php echo $row->explanation; ?></textarea>
                  		</div>
                  		</td>
                  	</tr>
                  	<tr class="form-field">
                		  <th scope="row"><label for="oninvoice-<?php echo $row->id; ?>"><?php echo __('On invoice', 'dekaagcrm'); ?></label></th>
                  		<td>
                  		<div class="admin-form-row">
                  		  <input type="text" value="<?php echo $row->oninvoice; ?>" id="oninvoice-<?php echo $row->id; ?>" name="oninvoice-<?php echo $row->id; ?>" style="width: 470px;">
                  		</div>
                  		</td>
                  	</tr>
                  	<tr class="form-field">
                		  <th scope="row"><label for="fieldtype-<?php echo $row->id; ?>"><?php echo __('Field type', 'dekaagcrm'); ?></label></th>
                  		<td>
                  		<div class="admin-form-row">
                  		  <select id="fieldtype-<?php echo $row->id; ?>" name="fieldtype-<?php echo $row->id; ?>" style="width: 470px;">
                  		    <option value="radio"<?php if ($row->fieldtype=='radio') echo ' selected="selected"'; ?>>Radio-button</option>
                  		    <option value="select"<?php if ($row->fieldtype=='select') echo ' selected="selected"'; ?>>Select</option>
                  		    <option value="input"<?php if ($row->fieldtype=='input') echo ' selected="selected"'; ?>>Input</option>
                  		  </select>
                  		</div>
                  		</td>
                  	</tr>
                  	<?php if ($row->fieldtype != 'input') { ?>
                  	<tr class="form-field">
                		  <th scope="row"><label for="default-<?php echo $row->id; ?>"><?php echo __('Default answer', 'dekaagcrm'); ?></label></th>
                  		<td>
                  		<div class="admin-form-row">
                  		  <select id="default-<?php echo $row->id; ?>" name="default-<?php echo $row->id; ?>" style="width: 470px;">
                  		    <option value="none"<?php if (is_null($row->default)) echo ' selected="selected"'; ?>>geen voorselectie</option>
                  		    <option value="0"<?php if ($row->default=='0') echo ' selected="selected"'; ?>>antwoord 1</option>
                  		    <option value="1"<?php if ($row->default=='1') echo ' selected="selected"'; ?>>antwoord 2</option>
                  		    <option value="2"<?php if ($row->default=='2') echo ' selected="selected"'; ?>>antwoord 3</option>
                  		    <option value="3"<?php if ($row->default=='3') echo ' selected="selected"'; ?>>antwoord 4</option>
                  		    <option value="4"<?php if ($row->default=='4') echo ' selected="selected"'; ?>>antwoord 5</option>
                  		    <option value="5"<?php if ($row->default=='5') echo ' selected="selected"'; ?>>antwoord 6</option>
                  		    <option value="6"<?php if ($row->default=='6') echo ' selected="selected"'; ?>>antwoord 7</option>
                  		    <option value="7"<?php if ($row->default=='7') echo ' selected="selected"'; ?>>antwoord 8</option>
                  		    <option value="8"<?php if ($row->default=='8') echo ' selected="selected"'; ?>>antwoord 9</option>
                  		    <option value="9"<?php if ($row->default=='9') echo ' selected="selected"'; ?>>antwoord 10</option>
                  		  </select>
                  		</div>
                  		</td>
                  	</tr>
                  	<?php } ?>
              <?php } ?>
                    <tr class="form-field">
                      <th scope="row"><label for=""><?php echo $row->rowtype == 'question'?__('Show question if', 'dekaagcrm'):__('Use mutation if', 'dekaagcrm'); ?></label></th>
                      <td>
                      <?php
                      $validators = json_decode($row->validators, true);
                      $c = count($validators);
                      $validatorcount[$row->id] = $c;
                      for ($i = $c; $i < 6; $i++) {
                        $validators[$i] = array(
                          'validate' => 'none',
                          array('validator' => 'in', 'value' => '')
                        );
                      }
                      foreach ($validators as $k => $validator) {
                        $val = $validator[0];
                        ?>
                        <div class="admin-form-row" id="validator-row-<?php echo $row->id; ?>-<?php echo $k; ?>"<?php if ($validator['validate'] == 'none') echo ' style="display:none;"'; ?>>
                          <div style="float:left;">
                            <select onchange="changeValidator(<?php echo $row->id; ?>, <?php echo $k; ?>, this);" name="validator-<?php echo $row->id; ?>-<?php echo $k; ?>" id="validator-<?php echo $row->id; ?>-<?php echo $k; ?>">
                              <option <?php if(in_array($validator['validate'], array('apptype', 'none'))) echo ' selected="selected"'; ?> value="apptype">afspraaktypen</option>
                              <option <?php if($validator['validate'] == 'age') echo ' selected="selected"'; ?> value="age">leeftijd</option>
                              <option <?php if($validator['validate'] == 'lastbookdate') echo ' selected="selected"'; ?> value="lastbookdate">laatste boekingsdatum</option>
                              <option <?php if($validator['validate'] == 'date') echo ' selected="selected"'; ?> value="date">datum</option>
                              <option <?php if(is_numeric($validator['validate'])) echo ' selected="selected"'; ?> value="answer">antwoord op vraag</option>
                            </select>
                            <select <?php if (is_numeric($validator['validate'])) echo ' style="display:none;"'; ?> name="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-validator" id="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-validator">
                              <option <?php if(!in_array($validator['validate'], array('apptype', 'none'))) echo ' style="display:none;"'; ?> <?php if($val['validator'] == 'in') echo ' selected="selected"'; ?> value="in">in</option>
                              <option <?php if(!in_array($validator['validate'], array('apptype', 'none'))) echo ' style="display:none;"'; ?> <?php if($val['validator'] == 'notin') echo ' selected="selected"'; ?> value="notin">niet in</option>
                              <option <?php if(in_array($validator['validate'], array('apptype', 'none'))) echo ' style="display:none;"'; ?> <?php if($val['validator'] == 'greater') echo ' selected="selected"'; ?> value="greater">groter dan</option>
                              <option <?php if(in_array($validator['validate'], array('apptype', 'none'))) echo ' style="display:none;"'; ?> <?php if($val['validator'] == 'smaller') echo ' selected="selected"'; ?> value="smaller">kleiner of gelijk aan</option>
                              <option <?php if(in_array($validator['validate'], array('apptype', 'none'))) echo ' style="display:none;"'; ?> <?php if($val['validator'] == 'equal') echo ' selected="selected"'; ?> value="equal">is exact</option>
                            </select>
                          </div>
                          <div id="validator-apptype-<?php echo $row->id; ?>-<?php echo $k; ?>" style="float: left;<?php if (!in_array($validator['validate'], array('apptype', 'none'))) echo 'display: none;'; ?>">
                            <?php 
                            $checked = explode(',', $val['value']);
                            foreach ($appTypes as $appTypeId => $appType) { 
                              $check = in_array($appTypeId, $checked) ? ' checked="checked"' : '';
                              ?>
                            <input <?php echo $check; ?>style="width: auto;" value="<?php echo $appTypeId; ?>" type="checkbox" name="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-apptype[<?php echo $appTypeId; ?>]" id="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-<?php echo $appTypeId; ?>"> <label for=""><?php echo $appType; ?></label><br>
                            <?php } ?>
                          </div>
                          <div id="validator-age-<?php echo $row->id; ?>-<?php echo $k; ?>" style="float:left;<?php if ($validator['validate'] != 'age') echo 'display: none;'; ?>">
                            <input type="text" name="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-value-age" id="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-value-age" value="<?php echo $val['value']; ?>" style="width:120px;">
                          </div>
                          <div id="validator-lastbookdate-<?php echo $row->id; ?>-<?php echo $k; ?>" style="float:left;<?php if ($validator['validate'] != 'lastbookdate') echo 'display: none;'; ?>">
                            <input type="text" name="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-value-lastbookdate" id="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-value" value="<?php echo date('d-m-Y', strtotime($val['value'])); ?>" style="width:120px;">
                          </div>
                          <div id="validator-date-<?php echo $row->id; ?>-<?php echo $k; ?>" style="float:left;<?php if ($validator['validate'] != 'date') echo 'display: none;'; ?>">
                            <input type="text" name="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-value-date" id="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-value" value="<?php echo date('d-m-Y', strtotime($val['value'])); ?>" style="width:120px;">
                          </div>
                          <div id="validator-answer-<?php echo $row->id; ?>-<?php echo $k; ?>" style="float:left;<?php if (!is_numeric($validator['validate'])) echo 'display: none;'; ?>">
                            <select onchange="selectQuestion(<?php echo $row->id; ?>,<?php echo $k; ?>, this);" name="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-q" id="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-q">
                            <?php 
                            $rows2 = DeKaagFormRow::model()->findAll();
                            foreach ($rows2 as $id2 => $row2) {
                              //if ($id2 > $id && $row2->rowtype == 'question') { 
                              if ($row2->rowtype == 'question') { 
                                if(in_array($object->id, array(1,2)) && !in_array($row2->{$row2->prefix().'form_id'}, array(1,2))) continue;
                                if(in_array($object->id, array(3,4)) && !in_array($row2->{$row2->prefix().'form_id'}, array(3,4))) continue;
                                if($row2->fieldtype == 'radio' || $row2->fieldtype == 'select') {
                                ?>
                              <option <?php if ($row2->id == $validator['validate']) echo ' selected="selected"'; ?> value="<?php echo $row2->id; ?>"><?php echo $row2->title; ?></option>
                                <?php
                                }
                              }
                            } ?>
                            </select>
                            <select name="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-a" id="validator-<?php echo $row->id; ?>-<?php echo $k; ?>-a">
                            <?php
                            $row2 = DeKaagFormRow::model()->findByPk($validator['validate']);
                            foreach (json_decode($row2->answers, true) as $ai => $answer) { ?>
                            <option <?php if ($ai == $validator[0]['value']) echo ' selected="selected"'; ?> value="<?php echo $ai; ?>"><?php echo $answer; ?></option>
                            <?php } ?>
                            </select>
                          </div>
                          <div style="clear:both;"></div>
                          <a href="javascript:if(confirm('Weet je zeker dat je deze voorwaarde wilt verwijderen? Je kunt dit niet ongedaan maken.')){javascript:removeValidator(<?php echo $row->id; ?>, <?php echo $k; ?>);}" class="delete-row"><img src="<?php echo plugins_url('../../img/delete.png', __FILE__); ?>" alt="<?php echo __('Delete', 'dekaagcrm'); ?>"></a>
                        </div>
                        <?php } ?>
                          
                        <div class="admin-form-row-button">
                          <button type="button" class="button button-secondary" onclick="addValidator(<?php echo $row->id; ?>);">voorwaarde toevoegen</button>
                        </div>
                      </td>
                    </tr>
                    
                    <?php if ($row->rowtype == 'question') { ?>
                    
                    <tr class="form-field">
                      <th scope="row"><label for=""><?php echo __('Which ansers are possible', 'dekaagcrm'); ?></label></th>
                      <td>
                      <?php
                      $answers = json_decode($row->answers, true);
                      $mutations = json_decode($row->mutations, true);
                      
                      $answercount[$row->id] = count($answers);
                      for ($i = $answercount[$row->id]; $i < 21; $i++) {
                        $answers[$i] = '';
                        $mutations[$i] = array(
                          'vat' => 21,
                          'type' => 'price',
                          'value' => 0,
                          'resource' => ''
                        );
                      }
                      
                      foreach ($answers as $key => $answer) { 
                        $mutation = $mutations[$key];
                        if (!isset($mutation['vat'])) $mutation['vat'] = 21;
                        if (!isset($mutation['resource'])) $mutation['resource'] = '';
                        ?>
                        <div class="admin-form-row" id="answer-row-<?php echo $row->id; ?>-<?php echo $key; ?>" <?php if ($answer == '') echo ' style="display:none;"'; ?>>
                          <input type="text" value="<?php echo $answer; ?>" id="answer-<?php echo $row->id; ?>-<?php echo $key; ?>" name="answer-<?php echo $row->id; ?>-<?php echo $key; ?>" style="width: 470px;">
                          <br>Prijsmutatie voor dit antwoord
                          <input type="text" name="mutation-<?php echo $row->id; ?>-<?php echo $key; ?>" id="mutation-<?php echo $row->id; ?>-<?php echo $key; ?>" value="<?php echo $mutation['mutation']; ?>" style="width:80px;"> 
                          <select name="mutationtype-<?php echo $row->id; ?>-<?php echo $key; ?>" id="mutationtype-<?php echo $row->id; ?>-<?php echo $key; ?>">
                            <option value="price" <?php if($mutation['type']=='price') echo ' selected="selected"'; ?>>euro</option>
                            <option value="percent" <?php if($mutation['type']=='percent') echo ' selected="selected"'; ?>>procent</option>
                          </select>
                          <select name="vat-<?php echo $row->id; ?>-<?php echo $key; ?>" id="vat-<?php echo $row->id; ?>-<?php echo $key; ?>">
                            <option value="6" <?php if($mutation['vat']=='6') echo ' selected="selected"'; ?>>6% BTW</option>
                            <option value="21" <?php if($mutation['vat']=='21') echo ' selected="selected"'; ?>>21% BTW</option>
                          </select>
                          <img src="<?php echo plugins_url('../../img/help.png', __FILE__); ?>" title="<?php echo __('Specify a mutation to the total sum if this answer is selected. The value can be positive and negative. For example \'10 percent\' or \'-30 euro\'. The calculation is aplied to the current total, which can be modified by questions and mutations above.', 'dekaagcrm'); ?>" alt=""><br>
                          Vereist beschikbare resource <input type="text" name="resource-<?php echo $row->id; ?>-<?php echo $key; ?>" id="resource-<?php echo $row->id; ?>-<?php echo $key; ?>" value="<?php echo $mutation['resource']; ?>" style="width:250px;"> <img src="<?php echo plugins_url('../../img/help.png', __FILE__); ?>" title="<?php echo __('Enter the name of the resource within your OA environment. You can use * as a wildcard to include multiple resources. For example \'slaapplek\' or \'slaapplek*\'', 'dekaagcrm'); ?>" alt="">
                          <a href="javascript:if(confirm('Weet je zeker dat je dit antwoord wilt verwijderen? Je kunt dit niet ongedaan maken. Indien er andere vragen afhankelijk zijn van deze vraag moet je deze vragen corrigeren.')){removeAnswer(<?php echo $row->id; ?>, <?php echo $key; ?>);}" class="delete-row"><img src="<?php echo plugins_url('../../img/delete.png', __FILE__); ?>" alt="<?php echo __('Delete', 'dekaagcrm'); ?>"></a>
                        </div>
                      <?php 
                      }
                      ?>
                        <div class="admin-form-row-button">
                          <button type="button" class="button button-secondary" onclick="addAnswer(<?php echo $row->id; ?>);">antwoord toevoegen</button>
                        </div>
                      </td>
                    </tr>
                        <?php } else  { 
                      $mutations = json_decode($row->mutations, true);
                      
                        $mutation = $mutations[0];
                        if (!isset($mutation['vat'])) $mutation['vat'] = 21;
                        if (!isset($mutation['resource'])) $mutation['resource'] = '';
                        ?>
                    <tr class="form-field">
                      <th scope="row"><label for=""><?php echo __('Which mutation', 'dekaagcrm'); ?></label></th>
                      <td>
                        <div class="admin-form-row">
                        <input type="hidden" value="mutation" id="answer-<?php echo $row->id; ?>-<?php echo $key; ?>" name="answer-<?php echo $row->id; ?>-<?php echo $key; ?>">
                          <input type="text" name="mutation-<?php echo $row->id; ?>-<?php echo $key; ?>" id="mutation-<?php echo $row->id; ?>-<?php echo $key; ?>" value="<?php echo $mutation['mutation']; ?>" style="width:80px;"> 
                          <select name="mutationtype-<?php echo $row->id; ?>-<?php echo $key; ?>" id="mutationtype-<?php echo $row->id; ?>-<?php echo $key; ?>">
                            <option value="price" <?php if($mutation['type']=='price') echo ' selected="selected"'; ?>>euro</option>
                            <option value="percent" <?php if($mutation['type']=='percent') echo ' selected="selected"'; ?>>procent</option>
                          </select>
                          <select name="vat-<?php echo $row->id; ?>-<?php echo $key; ?>" id="vat-<?php echo $row->id; ?>-<?php echo $key; ?>">
                            <option value="6" <?php if($mutation['vat']=='6') echo ' selected="selected"'; ?>>6% BTW</option>
                            <option value="21" <?php if($mutation['vat']=='21') echo ' selected="selected"'; ?>>21% BTW</option>
                          </select>
                        </div>
                      </td>
                    </tr>
                        <?php } ?>
                  </tbody>
                </table>
                </div>
          		</div>
            </div>
          
        <?php } ?>
        
        <p class="submit">
        <input type="button" onclick="window.location.href='/wp-admin/admin.php?page=dekaagcrm_forms&action=edit&form=<?php echo $_GET['form']; ?>';" value="<?php echo __('Back to form', 'dekaagcrm'); ?>" class="button button-secundary" id="createusersub" name="createuser"> <input type="submit" value="<?php echo $create ? __('Create relation', 'dekaagcrm') : __('Save changes', 'dekaagcrm'); ?>" class="button button-primary" id="createusersub" name="createuser"></p>
      </form>
  </div>
<script type="text/javascript">
var validatorcount = <?php echo json_encode($validatorcount); ?>;
var answercount = <?php echo json_encode($answercount); ?>;
<?php 
$answersRows =  DeKaagFormRow::model()->findAll();
foreach ($answersRows as $answerRow) {
  $answers["{$answerRow->id}"] = json_decode($answerRow->answers, true);
}
?>
var answers = <?php echo json_encode($answers); ?>;
                            
function selectQuestion(row_id, validator_id, elem)
{
  v = jQuery(elem).val();
  var s = jQuery('#validator-'+row_id+'-'+validator_id+'-a');
  s.children().remove().end();
  for ( i in answers[v]) {
    s.append(jQuery('<option>', { value: i }).text(answers[v][i]));
  }
}

function addValidator(row_id)
{
  validatorcount[row_id]++;
  jQuery('#validator-row-'+row_id+'-'+validatorcount[row_id]).show();
  jQuery('#validator-'+row_id+'-'+validatorcount[row_id]).val('apptype');
  changeValidator(row_id, validatorcount[row_id], '#validator-'+row_id+'-'+validatorcount[row_id]);
  jQuery('#validator-'+row_id+'-'+validatorcount[row_id]).change();
}

function removeValidator(row_id, validator_id)
{
  jQuery('#validator-row-'+row_id+'-'+validator_id).hide();
  jQuery('#validator-'+row_id+'-'+validator_id).val('none');
}

function addAnswer(row_id)
{
  answercount[row_id]++;
  jQuery('#answer-row-'+row_id+'-'+answercount[row_id]).show();
}

function removeAnswer(row_id, answer_id)
{
  jQuery('#answer-row-'+row_id+'-'+answer_id).hide();
  jQuery('#answer-'+row_id+'-'+answer_id).val('');
}

function changeValidator(row_id, validator_id, elem)
{
  var tt = elem;
  jQuery('#validator-age-'+row_id+'-'+validator_id).css('display', jQuery(elem).val() == 'age' ? 'block' : 'none');
  jQuery('#validator-date-'+row_id+'-'+validator_id).css('display', jQuery(elem).val() == 'date' ? 'block' : 'none');
  jQuery('#validator-lastbookdate-'+row_id+'-'+validator_id).css('display', jQuery(elem).val() == 'lastbookdate' ? 'block' : 'none');
  jQuery('#validator-answer-'+row_id+'-'+validator_id).css('display', jQuery(elem).val() == 'answer' ? 'block' : 'none');
  jQuery('#validator-apptype-'+row_id+'-'+validator_id).css('display', jQuery(elem).val() == 'apptype' ? 'block' : 'none');
  jQuery('#validator-'+row_id+'-'+validator_id+'-validator').css('display', jQuery(elem).val() == 'answer' ? 'none' : 'inline');
  jQuery('#validator-'+row_id+'-'+validator_id+'-validator option').each(function(i){
    switch (jQuery(this).val()) {
      case 'in':
      case 'notin':
        if (jQuery(tt).val() == 'apptype') {
          jQuery(this).show();
        }
        else {
          jQuery(this).hide();
          jQuery('#validator-'+row_id+'-'+validator_id+'-validator').val(false);
        }
        break;
      case 'greater':
      case 'smaller':
      case 'equal':
        if (jQuery(tt).val() != 'apptype') {
          jQuery(this).show();
        }
        else {
          jQuery(this).hide();
          jQuery('#validator-'+row_id+'-'+validator_id+'-validator').val(false);
        }
        break;
    }
  });
  if (jQuery(tt).val() == 'apptype') {
    selectQuestion(row_id, validator_id, jQuery('#validator-'+row_id+'-'+validator_id+'-q'));
  }
}
</script>	