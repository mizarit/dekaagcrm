<?php if (count($errors) > 0) { ?>
<ul class="form-errors">
<?php foreach ($errors as $error) { ?>
  <li><?php echo $error; ?></li>
<?php } ?>
</ul>
<?php } ?>
<?php if (count($messages) > 0) { ?>
<ul class="form-messages">
<?php foreach ($messages as $message) { ?>
  <li><?php echo $message; ?></li>
<?php } ?>
</ul>
<?php } ?>
    <form novalidate="novalidate" class="validate" id="edituser" name="edituser" method="post" action="">
      <fieldset>
      <div class="form-row">
        <div class="form-label"><label for="first_name"><?php echo __('First name', 'dekaagcrm'); ?></label></div>
        <input type="text" value="<?php echo $object->first_name; ?>" id="first_name" name="first_name">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="insertions"><?php echo __('Tussenvoegsels', 'dekaagcrm'); ?></label></div>
        <input type="text" value="<?php echo $object->insertions; ?>" id="insertions" name="insertions">
      </div>
      <div class="form-row <?php if(isset($errors['last_name'])) echo 'form-invalid'; ?>">
        <div class="form-label"><label for="last_name"><?php echo __('Last name', 'dekaagcrm'); ?> <span class="description">(verplicht)</span></label></div>
        <input type="text" value="<?php echo $object->last_name; ?>" id="last_name" name="last_name">
      </div> 	
      <div class="form-row <?php if(isset($errors['email'])) echo 'form-invalid'; ?>">
        <div class="form-label"><label for="email"><?php echo __('Email', 'dekaagcrm'); ?> <span class="description">(verplicht)</span></label></div>
        <input type="text" value="<?php echo $object->email; ?>" id="email" name="email">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="phone"><?php echo __('Phone', 'dekaagcrm'); ?> <span class="description">(verplicht)</span></label></div>
        <input type="text" value="<?php echo $object->phone; ?>" id="phone" name="phone" style="width:150px;">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="phone_mobile"><?php echo __('Mobile phone', 'dekaagcrm'); ?> <span class="description">(verplicht)</span></label></div>
        <input type="text" value="<?php echo $object->phone_mobile; ?>" id="phone_mobile" name="phone_mobile" style="width:150px;">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="phone_extra"><?php echo __('Alternative phone', 'dekaagcrm'); ?></label></div>
        <input type="text" value="<?php echo $object->phone_extra; ?>" id="phone_extra" name="phone_extra" style="width:150px;">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="address"><?php echo __('Address', 'dekaagcrm'); ?> <span class="description">(verplicht)</span></label></div>
        <input type="text" value="<?php echo $object->address; ?>" id="address" name="address">
      </div>  	
      <div class="form-row">
        <div class="form-label"><label for="zipcode"><?php echo __('Zipcode', 'dekaagcrm'); ?> <span class="description">(verplicht)</span></label></div>
        <input type="text" value="<?php echo $object->zipcode; ?>" id="zipcode" name="zipcode" style="width:80px;"><span class="description">1234AA</span>
      </div>  	
      <div class="form-row">
        <div class="form-label"><label for="city"><?php echo __('City', 'dekaagcrm'); ?> <span class="description">(verplicht)</span></label></div>
        <input type="text" value="<?php echo $object->city; ?>" id="city" name="city">
      </div>
      
      <?php foreach ($personas as $c => $persona) { ?>
      <h3><?php echo __('Kind/ gast', 'dekaagcrm'); ?> <?php echo $c + 1; ?></h3>
    	<div class="form-row">
    	  <div class="form-label"><label for="persona_<?php echo $c; ?>_first_name"><?php echo __('First name', 'dekaagcrm'); ?></label></div>
        <input type="text" value="<?php echo $persona->first_name; ?>" id="persona_<?php echo $c; ?>_first_name" name="persona_first_name[<?php echo $c; ?>]">
      </div>	
      <div class="form-row">
    	  <div class="form-label"><label for="persona_<?php echo $c; ?>_insertions"><?php echo __('Tussenvoegsels', 'dekaagcrm'); ?></label></div>
        <input type="text" value="<?php echo $persona->insertions; ?>" id="persona_<?php echo $c; ?>_insertions" name="persona_insertions[<?php echo $c; ?>]">
      </div>
      <div class="form-row">
    	  <div class="form-label"><label for="persona_<?php echo $c; ?>_last_name"><?php echo __('Last name', 'dekaagcrm'); ?></label></div>
        <input type="text" value="<?php echo $persona->last_name; ?>" id="persona_<?php echo $c; ?>_last_name" name="persona_last_name[<?php echo $c; ?>]">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="persona_<?php echo $c; ?>_dob"><?php echo __('Date of birth', 'dekaagcrm'); ?></label></div>
        <input type="text" value="<?php echo date('d-m-Y', strtotime($persona->dob)); ?>" id="persona_<?php echo $c; ?>_dob" name="persona_dob[<?php echo $c; ?>]" style="width:110px;"> <span class="description">dd-mm-jjjj</span>
      </div>	
      <div class="form-row">
        <div class="form-label"><label for="persona_<?php echo $c; ?>_gender_m"><?php echo __('Gender', 'dekaagcrm'); ?></label></div>
        <div style="margin-left:200px;">
  		  <input style="width:auto;" type="radio"<?php if ($persona->gender=='m') echo ' checked="checked"'; ?> value="m"  id="persona_<?php echo $c; ?>_gender_m" name="persona_gender[<?php echo $c; ?>]"> <label for="persona_<?php echo $c; ?>_gender_m"><?php echo __('male', 'dekaagcrm'); ?></label><br />
  		  <input style="width:auto;" type="radio"<?php if ($persona->gender=='f') echo ' checked="checked"'; ?> value="f"  id="persona_<?php echo $c; ?>_gender_f" name="persona_gender[<?php echo $c; ?>]"> <label for="persona_<?php echo $c; ?>_gender_f"><?php echo __('female', 'dekaagcrm'); ?></label>
  		  </div>
      </div>
      <div class="form-row">
        <div class="form-label"><label for="persona_<?php echo $c; ?>_email"><?php echo __('Email', 'dekaagcrm'); ?> <span class="description"></span></label></div>
    		<input type="text" value="<?php echo $persona->email; ?>" id="persona_<?php echo $c; ?>_email" name="persona_email[<?php echo $c; ?>]">
      </div>
      <div class="form-row">
        <div class="form-label"><label for="persona_<?php echo $c; ?>_remarks"><?php echo __('Remarks', 'dekaagcrm'); ?> <span class="description"></span></label></div>
    		<textarea rows="5" cols="10" style="width:260px;padding:3px;" id="persona_<?php echo $c; ?>_remarks" name="persona_remarks[<?php echo $c; ?>]"><?php echo $persona->remarks; ?></textarea>
      </div>
      <?php
      $diplomas = $persona->diplomas;
      if (count($diplomas) > 0) {
        $d = array();
        foreach ($diplomas as $diploma) {
          $d[] = '<span title="Diploma behaald op '.date('d-m-Y', strtotime($diploma->link->date)).'">'.$diploma->title.'</span>';
        }
        ?>
        <div class="form-row">
          <div class="form-label"><label><?php echo __('Diplomas', 'dekaagcrm'); ?></label></div>
          <div style="margin-left:200px;">
          <?php echo implode('<br>' ,$d); ?>
          </div>
        </div>
        <?php } ?>
      <?php } ?>
        
      <h3><?php echo __('Login credentials', 'dekaagcrm'); ?></h3>
      <p><?php echo __('To change your password, please fill in both password fields. If you leave them empty, your password will not be changed.', 'dekaagcrm'); ?></p>
      <div class="form-row <?php if(isset($errors['user_login'])) echo 'form-invalid'; ?>">
        <div class="form-label"><label for="user_login"><?php echo __('Username'); ?></label></div>
        <input type="text" aria-required="true" value="<?php echo $user->username; ?>" id="user_login" name="user_login">
      </div>  	
      <div class="form-row <?php if(isset($errors['password'])) echo 'form-invalid'; ?>">
        <div class="form-label"><label for="password"><?php echo __('Password'); ?></label></div>
        <input type="password" autocomplete="off" id="password" name="password">
      </div>  		
      <div class="form-row <?php if(isset($errors['password'])) echo 'form-invalid'; ?>">
        <div class="form-label"><label for="pass2"><?php echo __('Wachtwoord controle'); ?></label></div>
        <input type="password" autocomplete="off" id="password_retyped" name="password_retyped">
      </div>         		
      <div class="form-buttons">
        <button type="submit"><?php echo __('Instellingen opslaan', 'dekaagcrm'); ?></button>
      </div>
      </fieldset>
      </form>
