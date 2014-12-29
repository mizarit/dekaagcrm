	<div class="wrap">
    <div id="icon-users" class="icon32"><br/></div>
      <h2><?php echo $title; ?></h2>
      <form novalidate="novalidate" class="validate" id="editform" name="editform" method="post" action="">
      <input type="hidden" id="saveaction" name="saveaction" value="save">
        <table class="form-table">
        	<tbody>
          	<tr class="form-field">
          		<th scope="row"><label for="title"><?php echo __('Form name', 'dekaagcrm'); ?></label></th>
          		<td><input type="text" value="<?php echo $object->title; ?>" id="title" name="title"></td>
          	</tr>
          </tbody>
        </table>
<?php
$validatorcount = $answercount = array();
$m = 0;
?>
        <?php foreach ($rows as $id => $row) { 
          if ($row->rowtype == 'mutation') $m++;
          ?>
            <div class="postbox " id="row-container-<?php echo $id; ?>" style="margin-bottom:3px;">
              <h3 class="hndle" style="cursor:default;padding-left:10px;margin: 2px 0;"><span><?php echo $row->rowtype == 'question' ? $row->title : __('Mutation', 'dekaagcrm').' '.$m; ?></span>
              <a href="javascript:if(confirm('Weet je zeker dat je deze vraag of mutatie wilt verwijderen? Je kunt dit niet ongedaan magen. Indien vragen afhankelijk zijn van het antwoord op deze vraag moet je deze vragen corrigeren.')) {jQuery('#saveaction').val('deletequestion_<?php echo $row->id; ?>');jQuery('#editform').submit();}" style="top:5px;right:5px;" class="delete-row"><img src="<?php echo plugins_url('../../img/delete.png', __FILE__); ?>" alt="<?php echo __('Delete', 'dekaagcrm'); ?>"></a>
              </h3>
              <div class="inside" style="margin-bottom: 0;">
              
              <button type="button" onclick="if(jQuery('#row-container-inner-<?php echo $id; ?>').css('display')=='none'){jQuery('#row-container-inner-<?php echo $id; ?>').css('display', 'block');this.innerHTML='&laquo; verberg configuratie';} else {jQuery('#row-container-inner-<?php echo $id; ?>').css('display', 'none');this.innerHTML='toon configuratie &raquo;';}" id="row-fold-btn-<?php echo $id; ?>" class="button button-secondary">toon configuratie &raquo;</button>
              <button type="button" onclick="window.location.href='/wp-admin/admin.php?page=dekaagcrm_forms&action=editRow&form=<?php echo $_GET['form']; ?>&row=<?php echo $row->id; ?>';" class="button button-secondary">bewerk configuratie &raquo;</button>
              <div style="display:none;" id="row-container-inner-<?php echo $id; ?>">
                <table class="form-table">
        	       <tbody>
              
              <?php if ($row->rowtype == 'question') { ?>
                    <tr class="form-field">
                		  <th scope="row"><label for="title-<?php echo $row->id; ?>"><?php echo __('Question', 'dekaagcrm'); ?></label></th>
                  		<td>
                  		<div class="admin-form-row">
                  		  <?php echo $row->title; ?>
                  		</div>
                  		</td>
                  	</tr>
                  	<tr class="form-field">
                		  <th scope="row"><label for="explanation-<?php echo $row->id; ?>"><?php echo __('Explanation', 'dekaagcrm'); ?></label></th>
                  		<td>
                  		<div class="admin-form-row">
                  		  <?php echo $row->explanation; ?>
                  		</div>
                  		</td>
                  	</tr>
                  	<tr class="form-field">
                		  <th scope="row"><label for="oninvoice-<?php echo $row->id; ?>"><?php echo __('On invoice', 'dekaagcrm'); ?></label></th>
                  		<td>
                  		<div class="admin-form-row">
                  		  <?php echo $row->oninvoice; ?>
                  		</div>
                  		</td>
                  	</tr>
                  	<tr class="form-field">
                		  <th scope="row"><label for="fieldtype-<?php echo $row->id; ?>"><?php echo __('Field type', 'dekaagcrm'); ?></label></th>
                  		<td>
                  		<?php
                  		$types = array(
                  		  'radio' => 'Radio-button',
                  		  'select' => 'Select',
                  		  'input' => 'Input'
                  		);
                  		echo $types[$row->fieldtype];
                  		?>
                  		</td>
                  	</tr>
                  	<?php if ($row->fieldtype != 'input') { ?>
                  	<tr class="form-field">
                		  <th scope="row"><label for="default-<?php echo $row->id; ?>"><?php echo __('Default answer', 'dekaagcrm'); ?></label></th>
                  		<td>
                  		<div class="admin-form-row">
                  		antwoord <?php echo $row->default + 1; ?>
                  		</div>
                  		</td>
                  	</tr>
                  	<?php } ?>
              <?php } ?>
              <?php  
              $validators = json_decode($row->validators, true);
              $c = count($validators);
              $vnames = array(
                'apptype' => 'afspraaktypen',
                'none' => 'afspraaktypen',
                'age' => 'leeftijd',
                'lastbookdate' => 'laatste boekingsdatum',
                'date' => 'datum'
              );
              $vtypes = array(
                'in' => 'in',
                'notin' => 'niet in',
                'greater' => 'groter dan',
                'smaller' => 'kleiner of gelijk aan',
                'equal' => 'is exact',
              );
              if ($c > 0) { ?>        
                    <tr class="form-field">
                      <th scope="row"><label for=""><?php echo $row->rowtype == 'question'?__('Show question if', 'dekaagcrm'):__('Use mutation if', 'dekaagcrm'); ?></label></th>
                      <td>
                      <?php
                      foreach ($validators as $k => $validator) {
                        $val = $validator[0];
                        ?>
                        <div class="admin-form-row" id="validator-row-<?php echo $row->id; ?>-<?php echo $k; ?>"<?php if ($validator['validate'] == 'none') echo ' style="display:none;"'; ?>>
                          <div style="float:left;">
                          <?php echo isset($vnames[$validator['validate']]) ? $vnames[$validator['validate']] : 'antwoord op vraag'; ?>&nbsp;
                          <?php if (!is_numeric($validator['validate'])) echo $vtypes[$val['validator']]; ?>&nbsp;
                          </div>
                          <?php 
                          switch($validator['validate']) {
                            case 'none':
                            case 'apptype':
                               $checked = explode(',', $val['value']);
                               foreach ($appTypes as $appTypeId => $appType) { 
                                $check = in_array($appTypeId, $checked);
                                if ($check) echo $appType.'; ';
                               }
                               break;
                            case 'age':
                              echo $val['value'];
                              break;
                            case 'lastbookdate':
                              echo date('d-m-Y', strtotime($val['value'])); 
                              break;
                            case 'date':
                              echo date('d-m-Y', strtotime($val['value'])); 
                              break;
                            default:
                              $row2 = DeKaagFormRow::model()->findByPk($validator['validate']);
                              if ($row2) {
                                echo $row2->title.' = ';
                              foreach (json_decode($row2->answers, true) as $ai => $answer) { 
                                if ($ai == $validator[0]['value']) {
                                  echo $answer;
                                }
                              }
                              }
                              break;
                          }
                          ?>
                          </div>
                        <?php } ?>
                      </td>
                    </tr>
                    <?php } ?>
                    <?php if ($row->rowtype == 'question') { ?>
                    
                    <tr class="form-field">
                      <th scope="row"><label for=""><?php echo __('Which ansers are possible', 'dekaagcrm'); ?></label></th>
                      <td>
                      <?php
                      $answers = json_decode($row->answers, true);
                      $mutations = json_decode($row->mutations, true);
                      
                      $answercount[$row->id] = count($answers);
                      for ($i = $answercount[$row->id]; $i < 16; $i++) {
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
                          <?php echo $answer; ?>
                          <?php if ($mutation['mutation'] != 0) { ?>
                          <br>Prijsmutatie 
                          <?php echo $mutation['mutation']; ?> 
                          <?php echo $mutation['type']=='price' ? 'euro' : 'procent'; ?> 
                          <?php echo $mutation['vat']=='6' ? '6% BTW' : '21% BTW'; ?>
                          <?php } ?>
                          <?php if ($mutation['resource'] != '') { ?>
                          <?php echo '<br>Vereist resource '.$mutation['resource']; ?>
                          <?php } ?>
                         </div>
                      <?php 
                      }
                      ?>
                      </td>
                    </tr>
                        <?php } else  { 
                        $mutations = json_decode($row->mutations, true);
                        $mutation = $mutations[0];
                        if (!isset($mutation['vat'])) $mutation['vat'] = 21;
                        if (!isset($mutation['resource'])) $mutation['resource'] = '';
                        
                        if ($mutation['mutation'] != 0) {
                        ?>
                    <tr class="form-field">
                      <th scope="row"><label for=""><?php echo __('Which mutation', 'dekaagcrm'); ?></label></th>
                      <td>
                        <div class="admin-form-row">
                          <br>Prijsmutatie 
                          <?php echo $mutation['mutation']; ?> 
                          <?php echo $mutation['type']=='price' ? 'euro' : 'procent'; ?> 
                          <?php echo $mutation['vat']=='6' ? '6% BTW' : '21% BTW'; ?><br>
                        </div>
                      </td>
                    </tr>
                        <?php } } ?>
                  </tbody>
                </table>
                </div>
          		</div>
            </div>
        <?php } ?>
        
        <button type="button" onclick="jQuery('#saveaction').val('addquestion');jQuery('#editform').submit();" class="button button-secondary">vraag toevoegen</button>
        <button type="button" onclick="jQuery('#saveaction').val('addmutation');jQuery('#editform').submit();" class="button button-secondary">mutatie toevoegen</button>
        
        <p class="submit"><input type="submit" value="<?php echo $create ? __('Create relation', 'dekaagcrm') : __('Save changes', 'dekaagcrm'); ?>" class="button button-primary" id="createusersub" name="createuser"></p>
      </form>
  </div>
