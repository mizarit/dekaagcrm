<h2><?php echo __('Uw reservering'); ?></h2>
<div id="appointment-info">
  <?php echo $appointmentInfo; ?>
</div>

<h2><?php echo __('Uw gegevens'); ?></h2>
<div id="customer-info">
  <?php echo $customerInfo; ?>
</div>

<h2><?php echo __('Bedankt'); ?></h2>

<?php
$vars = array(
  'title' => $model->relation->title,
  'date' => date('d-m-Y', strtotime($model->date)),
  'end_date' => date('d-m-Y', strtotime($model->enddate)),
  'invoicenr' => $model->invoicenr,
  'total' => $model->getTotalStr(false),
  'downpayment' => $model->downpayment != 'none' ? ($model->downpayment == 'fixed' ? $model->dpvalue : ($model->getTotalStr(false)/100)*round($model->dpvalue) ) : false
);
extract($vars);
?>
<p><?php echo __('Uw afspraak is gemaakt. Er is een e-mail bevestiging naar u verzonden met een bijgevoegde factuur.'); ?>
<p>Wij verzoeken u vriendelijk deze factuur voor <?php echo $end_date; ?> te voldoen. 
<?php if ($downpayment) { ?>
Overige betalingvoorwaarden zoals aanbetaling zijn vermeld op de bijgevoegde factuur.
<?php } ?>
</p>
<?php $hash = substr(md5($invoicenr.'DEKAAG123456789123456789'),8,16); ?>
<?php //$hash = crypt($invoicenr, $invoicenr.'DEKAAG'); ?>
<p>U kunt ook de factuur direct online afrekenen met iDeal.</p>
<p>Klik hier om <strong>de factuur a € <?php echo number_format($total,2,',','.'); ?></strong> met iDeal af te rekenen.</p>
<button type="button" onclick="window.location.href='http://<?php echo $_SERVER['SERVER_NAME']; ?>/betalen?invoice=<?php echo $invoicenr; ?>&hash=<?php echo $hash; ?>';"><?php echo __('Betaling starten'); ?></button>

<?php if ($downpayment) { ?>
<p>Klik hier om <strong>de aanbetaling a € <?php echo number_format($downpayment,2,',','.'); ?></strong> met iDeal af te rekenen.</p>
<button type="button" onclick="window.location.href='http://<?php echo $_SERVER['SERVER_NAME']; ?>/betalen?invoice=<?php echo $invoicenr; ?>&hash=<?php echo $hash; ?>&downpayment=1';"><?php echo __('Aanbetaling starten'); ?></button>
<?php } ?>

<!--
<div class="form-button">
  <button type="button" onclick="widget.startBooking(true);"><?php echo __('Maak nog een reservering'); ?></button>
</div>-->