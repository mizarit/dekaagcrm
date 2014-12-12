	<div class="wrap">
    <div id="icon-users" class="icon32"><br/></div>
      <h2><?php echo $title; ?></h2>
      <form novalidate="novalidate" class="validate" id="createuser" name="createuser" method="post" action="">
        <table class="form-table">
        	<tbody>
          	<tr class="form-field">
          		<th scope="row"><label for="first_name"><?php echo __('First name', 'dekaagcrm'); ?></label></th>
          		<td><input type="text" value="<?php echo $object->first_name; ?>" id="first_name" name="first_name"></td>
          	</tr>
          	<tr class="form-field">
          		<th scope="row"><label for="insertions"><?php echo __('Insertions', 'dekaagcrm'); ?></label></th>
          		<td><input type="text" value="<?php echo $object->insertions; ?>" id="insertions" name="insertions"></td>
          	</tr>
          	<tr class="form-field <?php if(isset($errors['last_name'])) echo 'form-invalid'; ?>">
          		<th scope="row"><label for="last_name"><?php echo __('Last name', 'dekaagcrm'); ?> <span class="description">(verplicht)</span></label></th>
          		<td><input type="text" value="<?php echo $object->last_name; ?>" id="last_name" name="last_name"></td>
          	</tr>
          	<tr class="form-field form-required <?php if(isset($errors['email'])) echo 'form-invalid'; ?>">
          		<th scope="row"><label for="email"><?php echo __('Email', 'dekaagcrm'); ?> <span class="description">(verplicht)</span></label></th>
          		<td><input type="text" value="<?php echo $object->email; ?>" id="email" name="email"></td>
          	</tr>
          	<tr class="form-field">
          		<th scope="row"><label for="phone"><?php echo __('Phone', 'dekaagcrm'); ?></label></th>
          		<td><input type="text" value="<?php echo $object->phone; ?>" id="phone" name="phone"></td>
          	</tr>
          	<tr class="form-field">
          		<th scope="row"><label for="phone_mobile"><?php echo __('Mobile phone', 'dekaagcrm'); ?></label></th>
          		<td><input type="text" value="<?php echo $object->phone_mobile; ?>" id="phone_mobile" name="phone_mobile"></td>
          	</tr>
          	<tr class="form-field">
          		<th scope="row"><label for="phone_extra"><?php echo __('Alternative phone', 'dekaagcrm'); ?></label></th>
          		<td><input type="text" value="<?php echo $object->phone_extra; ?>" id="phone_extra" name="phone_extra"></td>
          	</tr>
          	<tr class="form-field">
          		<th scope="row"><label for="address"><?php echo __('Address', 'dekaagcrm'); ?></label></th>
          		<td><input type="text" value="<?php echo $object->address; ?>" id="address" name="address"></td>
          	</tr>
          	<tr class="form-field">
          		<th scope="row"><label for="zipcode"><?php echo __('Zipcode', 'dekaagcrm'); ?></label></th>
          		<td><input type="text" value="<?php echo $object->zipcode; ?>" id="zipcode" name="zipcode" style="width:80px;"><span class="description">1234AA</span></td>
          	</tr> 
          	<tr class="form-field">
          		<th scope="row"><label for="city"><?php echo __('City', 'dekaagcrm'); ?></label></th>
          		<td><input type="text" value="<?php echo $object->city; ?>" id="city" name="city"></td>
          	</tr>
          	<tr class="form-field">
          	  <td colspan="2" style="padding: 0 0 8px 0;">          	
          	    <button type="button" class="button button-secondary" onclick="addAccount(this);" id="add-account-button"><?php echo __('Set login account', 'dekaagcrm'); ?></button>
          	  </td>
          	</tr>

          	<tr class="form-field account-required <?php if(isset($errors['user_login'])) echo 'form-invalid'; ?>">
          		<th scope="row"><label for="user_login"><?php echo __('Username'); ?></label></th>
          		<td><input type="text" aria-required="true" value="<?php echo $user->username; ?>" id="user_login" name="user_login"></td>
          	</tr>
          	<tr class="form-field account-required <?php if(isset($errors['password'])) echo 'form-invalid'; ?>">
          		<th scope="row"><label for="password"><?php echo __('Password'); ?></label></th>
          		<td>
          			<input value=" " class="hidden"><!-- #24364 workaround -->
          			<input type="password" autocomplete="off" id="password" name="password">
          		</td>
          	</tr>
          	<tr class="form-field account-required <?php if(isset($errors['password'])) echo 'form-invalid'; ?>">
          		<th scope="row"><label for="pass2"><?php echo __('Repeat Password'); ?></label></th>
          		<td>
          		<input type="password" autocomplete="off" id="password_retyped" name="password_retyped">
          		<br>
          		<div id="pass-strength-result" style="display: block;" class="empty"><?php echo __('Strength indicator'); ?></div><div style="clear:both;"></div>
          		<p class="description indicator-hint" style="max-width:600px;"><?php echo __('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; ).'); ?></p>
          		</td>
          	</tr>
          	<tr class="form-field account-required password-required">
          		<th scope="row"><label for="send_password"><?php echo __('Send Password?') ?></label></th>
          		<td><label for="send_password"><input style="width:auto;" type="checkbox" name="send_password" id="send_password" value="1"> <?php echo __('Send this password to the new user by email.'); ?></label></td>
          	</tr>
	
	
        	</tbody>
        </table>
        
        <?php 
        $c = 0;
        $personas = $object->personas;
        $total_personas = count($personas);
        $x = $total_personas;
        while($x < 10) {
          $personas[] = new DeKaagPersona;
          $x++;
        }
        foreach ($personas as $persona) { 
          $dob = strtotime($persona->dob);
          $c++; ?>
          <div class="postbox " id="persona-container-<?php echo $c; ?>"<?php if ($c > $total_personas) echo ' style="display:none;"'; ?>>
            <h3 class="hndle" style="padding-left:10px;"><span><?php echo __('Persona', 'dekaagcrm'); ?> <?php echo $c; ?></span></h3>
            <div class="inside">
          
            <table class="form-table">
            	<tbody>
              	<tr class="form-field">
              		<th scope="row"><label for="persona_<?php echo $c; ?>_first_name"><?php echo __('First name', 'dekaagcrm'); ?></label></th>
              		<td><input type="text" value="<?php echo $persona->first_name; ?>" id="persona_<?php echo $c; ?>_first_name" name="persona_first_name[<?php echo $c; ?>]"></td>
              	</tr>
              	<tr class="form-field">
              		<th scope="row"><label for="persona_<?php echo $c; ?>_insertions"><?php echo __('Insertions', 'dekaagcrm'); ?></label></th>
              		<td><input type="text" value="<?php echo $persona->insertions; ?>" id="persona_<?php echo $c; ?>_insertions" name="persona_insertions[<?php echo $c; ?>]"></td>
              	</tr>
              	<tr class="form-field">
              		<th scope="row"><label for="persona_<?php echo $c; ?>_last_name"><?php echo __('Last name', 'dekaagcrm'); ?></label></th>
              		<td><input type="text" value="<?php echo $persona->last_name; ?>" id="persona_<?php echo $c; ?>_last_name" name="persona_last_name[<?php echo $c; ?>]"></td>
              	</tr>
              	<tr class="form-field">
              		<th scope="row"><label for="persona_<?php echo $c; ?>_dob"><?php echo __('Date of birth', 'dekaagcrm'); ?></label></th>
              		<td><input type="text" value="<?php echo $dob && $dob > 0 ? date('d-m-Y', $dob) : ''; ?>" id="persona_<?php echo $c; ?>_dob" name="persona_dob[<?php echo $c; ?>]" style="width:90px;"><span class="description">dd-mm-jjjj</span></td>
              	</tr>
              	<tr class="form-field">
              		<th scope="row"><label for="persona_<?php echo $c; ?>_gender_m"><?php echo __('Gender', 'dekaagcrm'); ?></label></th>
              		<td>
              		  <input style="width:auto;" type="radio"<?php if ($persona->gender=='m') echo ' checked="checked"'; ?> value="m"  id="persona_<?php echo $c; ?>_gender_m" name="persona_gender[<?php echo $c; ?>]"> <label for="persona_<?php echo $c; ?>_gender_m"><?php echo __('male', 'dekaagcrm'); ?></label><br />
              		  <input style="width:auto;" type="radio"<?php if ($persona->gender=='f') echo ' checked="checked"'; ?> value="f"  id="persona_<?php echo $c; ?>_gender_f" name="persona_gender[<?php echo $c; ?>]"> <label for="persona_<?php echo $c; ?>_gender_f"><?php echo __('female', 'dekaagcrm'); ?></label>
              		</td>
              	</tr>
              	<tr class="form-field">
              		<th scope="row"><label for="persona_<?php echo $c; ?>_email"><?php echo __('Email', 'dekaagcrm'); ?> <span class="description"></span></label></th>
              		<td><input type="email" value="<?php echo $persona->email; ?>" id="persona_<?php echo $c; ?>_email" name="persona_email[<?php echo $c; ?>]"></td>
              	</tr>
              	
              	<tr class="form-field">
              		<th scope="row" colspan="2" style="padding-bottom: 0;"><label for="persona_<?php echo $c; ?>_diploma"><?php echo __('Diplomas', 'dekaagcrm'); ?></label></th>
                </tr>
                <tr class="form-field">
                  <td colspan="2">
              		  <table style="border-collapse:collapse;padding:0;margin:0;">
              	<?php 
              	$diplomas2 = $persona->diplomas;
              	$i = -1;
              	foreach ($diplomas as $diploma) { 
              	  $checked = false;
              	  $date = '';
              	  foreach ($diplomas2 as $diploma2) {
              	    if ($diploma2->id == $diploma->id) {
              	      $checked = true;
              	      $d = strtotime($diploma2->link->date);
              	      if ($d && $d > 0) {
              	        $date = date('d-m-Y', $d);
              	      }
              	    }
              	  }
              	  
              	  $i++;
              	  
              	  if ($i % 2 == 0) { ?>
              	  <tr style="padding:0;margin:0;">
              	  <?php } ?>
              	   
              	     <td style="padding:0;margin:0;">
              	       <input onchange="jQuery('#persona_<?php echo $c; ?>_diploma_<?php echo $diploma->id; ?>_date').attr('disabled', !this.checked?'disabled':null);" style="width:auto;"<?php if($checked) echo ' checked="checked"'; ?> type="checkbox" id="persona_<?php echo $c; ?>_diploma_<?php echo $diploma->id; ?>" name="persona_diploma[<?php echo $c; ?>][<?php echo $diploma->id; ?>]">
              		     <label for="persona_<?php echo $c; ?>_diploma_<?php echo $diploma->id; ?>"><?php echo $diploma->title; ?></label>
              		   </td>
              	     <td style="padding:0 20px 0 3px;margin:0;"> per <input <?php if (!$checked) { echo ' disabled="disabled" '; } ?> type="text" name="persona_diploma_date[<?php echo $c; ?>][<?php echo $diploma->id; ?>]" id="persona_<?php echo $c; ?>_diploma_<?php echo $diploma->id; ?>_date" value="<?php echo $date; ?>" style="width:100px;"> <span class="description">( dd-mm-jjjj)</span></td>
              	   <?php if ($i % 2 == 1) { ?>
              	     </tr>
              	   <?php } ?>
              	  
              		
              		
              	<?php } ?>
              	    </table>
              	  </td>
              	</tr>
              	<tr class="form-field">
              		<th scope="row"><label for="persona_<?php echo $c; ?>_remarks"><?php echo __('Remarks', 'dekaagcrm'); ?></label></th>
              		<td><textarea rows="5" cols="10" id="persona_<?php echo $c; ?>_remarks" name="persona_remarks[<?php echo $c; ?>]"><?php echo $persona->remarks; ?></textarea></td>
              	</tr>
              	<tr class="form-field">
              		<th scope="row"><label for="persona_<?php echo $c; ?>_remarks"><?php echo __('Remarks private', 'dekaagcrm'); ?></label></th>
              		<td><textarea rows="5" cols="10" id="persona_<?php echo $c; ?>_remarks_private" name="persona_remarks_private[<?php echo $c; ?>]"><?php echo $persona->remarks_private; ?></textarea></td>
              	</tr>
            	</tbody>
            </table>
          		
        	</div>
        </div>


        <?php } ?>
<input type="hidden" name="personas" id="personas" value="<?php echo $total_personas; ?>">
<script type="text/javascript">
<?php
$diploma_ids = array();
foreach ($diplomas as $diploma) {
  $diploma_ids[] = $diploma->id;
}
?>
var personas = <?php echo $total_personas; ?>;
var diplomas = <?php echo json_encode($diploma_ids); ?>;
</script>
        <button type="button" class="button button-secondary" onclick="addPersona();" id="add-persona-button"><?php echo __('Add persona', 'dekaagcrm'); ?></button>
        <p class="submit"><input type="submit" value="<?php echo $create ? __('Create relation', 'dekaagcrm') : __('Save changes', 'dekaagcrm'); ?>" class="button button-primary" id="createusersub" name="createuser"></p>
      </form>
  </div>
<?php if ($_POST['user_login'] != '') { ?>
<script type="text/javascript">
jQuery(document).ready(function($) {
  addAccount(jQuery('#add-account-button'));
});
</script>
<?php } ?>