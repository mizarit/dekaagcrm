<h2><?php echo __('Uw afspraak'); ?></h2>

<div id="appointment-info">
  <?php echo $appointmentInfo; ?>
</div>

<div class="form-button">
	<button type="button" onclick="widget.startBooking();"><?php echo __('Aanpassen'); ?></button>
</div>

<h2><?php echo __('Wachtwoord vergeten?'); ?></h2>
<p><?php echo __('Vul hieronder het e-mailadres in van uw klantaccount. U ontvangt dan per e-mail een link waarmee u uw wachtwoord opnieuw in kunt stellen.'); ?></p>

<div id="messages-container">
  <ul class="form-messages" id="form-messages">
    <li>&nbsp;</li>
  </ul>
</div>

<div id="error-container">
  <ul class="form-errors" id="form-errors">
    <li>&nbsp;</li>
  </ul>
</div>
<form action="#" method="post" id="passwordReminder-form">
  <fieldset>
    <legend>Wachtwoord herstellen formulier</legend>
    
    <div class="form-row">
      <div class="form-label"><label for="email"><?php echo __('E-mail adres'); ?></label></div>
      <input type="text" name="email" id="email">
    </div>
    
    <div class="form-button">
      <button style="float:left;" type="button" onclick="widget.startConsumerData(widget.options.bookingOptions);"><?php echo __('Terug'); ?></button>
      <button type="button" onclick="widget.handlePasswordReminder();"><?php echo __('Verzenden'); ?></button>
    </div>
  </fieldset>
</form>