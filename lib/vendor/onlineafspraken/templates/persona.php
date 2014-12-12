<h2><?php echo __('Uw afspraak'); ?></h2>

<div id="appointment-info">
  <?php echo $appointmentInfo; ?>
</div>

<div class="form-button">
	<button type="button" onclick="widget.startBooking();"><?php echo __('Aanpassen'); ?></button>
</div>

<h2><?php echo __('Add persona', 'dekaagcrm'); ?></h2>
<p><?php echo __('Vul het onderstaande formulier in om een persona toe te voegen.'); ?></p>

<div id="error-container">
  <ul class="form-errors" id="form-errors">
    <li>&nbsp;</li>
  </ul>
</div>

<form action="#" method="post" id="register-form">
  <fieldset>
    <legend>Registratie formulier</legend>
    <input type="hidden" name="method" id="method" value="post">
    <div class="form-row">
      <div class="form-label"><label for="PersonaFirstName"><?php echo __('Voornaam'); ?> *</label></div>
      <input type="text" name="PersonaFirstName" id="PersonaFirstName">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="PersonaInsertions"><?php echo __('Tussenvoegsels'); ?></label></div>
      <input type="text" name="PersonaInsertions" id="PersonaInsertions">
    </div>
    <div class="form-row">
      <div class="form-label"><label for="PersonaLastName"><?php echo __('Achternaam'); ?> *</label></div>
      <input type="text" name="PersonaLastName" id="PersonaLastName">
    </div>

    <div class="form-row">
      <div class="form-label"><label for="Gender"><?php echo __('Geslacht'); ?></label></div>
      <select name="Gender" id="Gender">
        <option value="m"><?php echo __('Man'); ?></option>
        <option value="f"><?php echo __('Vrouw'); ?></option>
      </select>
    </div>
    
    <div class="form-row">
      <div class="form-label"><label for="Dob"><?php echo __('Geboortedatum'); ?> *</label></div>
      <input type="text"<?php if(isset($_SESSION['booking']['dob'])) echo ' value="'.$_SESSION['booking']['dob'].'"'; ?> name="Dob" id="Dob" style="width:110px !important;"> ( dd-mm-jjjj )
    </div>
    
    <h2><?php echo __('Opmerkingen'); ?></h2>
    <p><?php echo __('Vul hier bijv. in eventuele allergie-informatie, medische beperkingen, of andere zaken waarvan u het belangrijk acht dat wij hiervan op de hoogte zijn.'); ?></p>

    <div class="form-row">
      <textarea name="Remarks" id="Remarks" cols="40" rows=""></textarea>
    </div>

    <div class="form-button">
      <button style="float:left;" type="button" onclick="widget.startConsumerData(widget.options.bookingOptions);"><?php echo __('Terug'); ?></button>
      <button type="button" onclick="widget.handlePersona();"><?php echo __('Verzenden'); ?></button>
    </div>
  </fieldset>
</form>