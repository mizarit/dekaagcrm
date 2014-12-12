<h2><?php echo __('Uw afspraak'); ?></h2>
<p><?php echo __('Controleer de gegevens hieronder. U kunt de afspraak bevestigen door hieronder in te loggen, of door uzelf te registreren als klant.'); ?></p>

<div id="appointment-info">
  <?php echo $appointmentInfo; ?>
</div>

<div class="form-button">
	<button type="button" onclick="widget.startBooking();"><?php echo __('Aanpassen'); ?></button>
</div>

<?php if ($customerInfo) { ?>
<h2><?php echo __('Uw gegevens'); ?></h2>
<p><?php echo __("U bent al ingelogd als de onderstaande gebruiker. Indien u de afspraak wilt bevestigen kunt u verder gaan met 'Bevestigen'."); ?></p>
<div id="customer-info">
  <?php echo $customerInfo; ?>
</div>

<div class="form-button">
	<button type="button" style="float:left;" onclick="widget.startLogoff();"><?php echo __('Uitloggen'); ?></button>
	<button type="button" onclick="widget.startConfirm();"><?php echo __('Volgende stap'); ?></button>
</div>
<?php } else { ?>

<h2><?php echo __('Inloggen'); ?></h2>
<?php if (FB_USE_LOGIN) { ?>
<p><?php echo __('Meld uzelf aan met uw Facebook account. U hoeft zich niet te registreren om een afspraak te maken.'); ?></p>
<a class="fb_button fb_button_medium" onclick="widget.handleLoginWithFacebook();"><span class="fb_button_text">Inloggen met Facebook</span></a>
<p><?php echo __('Of meld uzelf aan met uw inloggegevens om uw afspraak vast te leggen.'); ?></p>
<?php } else { ?>
<p><?php echo __('Meld uzelf aan met uw inloggegevens om uw afspraak vast te leggen.'); ?></p>
<?php } ?>
<div id="error-container">
  <ul class="form-errors">
    <li><?php echo __('De opgegeven gebruikersnaam of wachtwoord is niet juist.'); ?></li>
  </ul>
</div>

<form action="#" method="post" id="consumerData-form">
	<fieldset>
		<legend>Login formulier</legend>
		
		<div class="form-row">
			<div class="form-label"><label for="username"><?php echo __('Gebruikersnaam'); ?></label></div>
			<input type="text" name="username" id="username">
		</div>
		
		<div class="form-row">
			<div class="form-label"><label for="password"><?php echo __('Wachtwoord'); ?></label></div>
			<input type="password" name="password" id="password">
		</div>
		
		<div class="form-button">
			<button type="button" onclick="widget.handleLogin();"><?php echo __('Inloggen'); ?></button>
		</div>
	</fieldset>
</form>

<p><a href="#" onclick="widget.startPasswordReminder();"><?php echo __('Wachtwoord vergeten?'); ?></a></p>

<h2><?php echo __('Registreren'); ?></h2>
<p><?php echo __('Bent u nog geen klant, of beschikt u nog niet over inloggegevens?'); ?></p>
<div class="form-button">
	<button type="button" onclick="widget.startRegister();"><?php echo __('Gebruikers registratie'); ?></button>
</div>

<?php } ?>