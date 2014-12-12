<h2><?php echo __('Uw afspraak'); ?></h2>
<div id="appointment-info">
  <?php echo $appointmentInfo; ?>
</div>

<div class="form-button">
	<button type="button" onclick="widget.startBooking();"><?php echo __('Aanpassen'); ?></button>
</div>

<h2><?php echo __('Uw gegevens'); ?></h2>
<div id="customer-info">
  <?php echo $customerInfo; ?>
</div>


<div class="form-button">
	<!--<button type="button" onclick="widget.startRegister();"><?php echo __('Aanpassen'); ?></button>-->
</div>

<h2><?php echo __('Reservering afronden'); ?></h2>

<div id="error-container">
  <ul class="form-errors" id="form-errors">
    <li>&nbsp;</li>
  </ul>
</div>

<form action="#" method="post" id="confirm-form">
  <fieldset>
    <legend>Confirm appointment</legend>
    <?php echo DeKaagForm::render($_SESSION['company']==2?4:2, array('total' => $total)); ?>
    <div class="form-button">
     <button style="float:left;" type="button" onclick="widget.startConsumerData(widget.options.bookingOptions);"><?php echo __('Terug'); ?></button>
     <button type="button" onclick="if(formValidate()) widget.handleConfirm();"><?php echo __('Bevestigen'); ?></button>
    </div>

  </fieldset>
</form>
