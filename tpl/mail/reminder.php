<style type="text/css">
body {
  background: #fff;
}

p {
  width: 740px;
  margin: 0 10px 15px 10px;
  color: #585858;
  font-size: 12px;
  font-family: "trebuchet ms",helvetica,sans-serif;
}

td {
  color: #585858;
  font-size: 12px;
  font-family: "trebuchet ms",helvetica,sans-serif;
  padding: 0;
  margin: 0;
}

table {
  margin: 10px;
  border-collapse: collapse;
  padding: 0;
}

strong {
  color: #00a5e2;;
  font-weight: bold;
}

h1 {
  font-family: "trebuchet ms",helvetica,sans-serif;
  width: 750px;
  margin: 10px 10px 15px 10px;
  border-bottom: #cecece 1px dotted;
  color: #00a5e2;
  font-weight: normal;
  letter-spacing: -1px;
  font-size: 24px;
}

a {
  color: #00a5e2;;
  font-weight: bold;
  font-size: 12px;
  font-family: "trebuchet ms",helvetica,sans-serif;
}

</style>
<img src="http://<?php echo $_SERVER['SERVER_NAME']; ?>/wp-content/plugins/dekaagcrm/data/logo-dekaag-invoice.jpg" alt="">
<h1>Herinnering <?php echo $invoicenr; ?></h1>
<p>Geachte <?php echo $title; ?>,</p>
<p>Bijgevoegd vindt u ter herinnering factuur <strong><?php echo $invoicenr; ?></strong></p>
<table>
  <tr>
    <td style="width: 110px;">Factuurnummer:</td>
    <td><strong><?php echo $invoicenr; ?></strong></td>
  </tr>
  <tr>
    <td style="width: 110px;">Factuurdatum:</td>
    <td><strong><?php echo $date; ?></strong></td>
  </tr>
  <tr>
    <td style="width: 110px;">Totaal incl. BTW:</td>
    <td><strong>€ <?php echo number_format($total,2,',','.'); ?></strong></td>
  </tr>
</table>
<p>De betaaltermijn voor deze factuur is verlopen. Wij verzoeken u vriendelijk deze factuur zo spoedig mogelijk te voldoen. 
<?php if ($downpayment) { ?>
Overige betalingvoorwaarden zoals aanbetaling zijn vermeld op de bijgevoegde factuur.
<?php } ?>
</p>
<?php $hash = crypt($invoicenr, $invoicenr.'DEKAAG'); ?>
<p>U kunt ook de factuur direct online afrekenen met iDeal.</p>
<p><a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/betalen?invoice=<?php echo $invoicenr; ?>&hash=<?php echo $hash; ?>">Klik hier</a> om <strong>de factuur a € <?php echo number_format($total,2,',','.'); ?></strong>, minus eventuele aanbetaling, met iDeal af te rekenen.</p>
<?php if ($downpayment) { ?>
<p><a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/betalen?invoice=<?php echo $invoicenr; ?>&hash=<?php echo $hash; ?>&downpayment=1">Klik hier</a> om <strong>de aanbetaling a € <?php echo number_format($downpayment,2,',','.'); ?></strong> met iDeal af te rekenen.</p>
<?php } ?>
<p>Met vriendelijke groet,</p> 
<p><?php echo $sender_name; ?></p>
<p><br>Deze herinnering is automatisch verzonden op <?php echo date('d-m-Y'); ?>.</p>