<?php if (count($invoices) > 0) {  ?>
<table class="dekaagcrm-frontend-table">
  <thead>
    <tr>
      <th style="width:120px"><?php echo __('Invoicenr', 'dekaagcrm'); ?></th>
      <th style="width:120px"><?php echo __('Date', 'dekaagcrm'); ?></th>
      <th style="width:240px"><?php echo __('Total', 'dekaagcrm'); ?></th>
      <th style="width:120px"><?php echo __('Status', 'dekaagcrm'); ?></th>
      <th><?php echo __('Actions', 'dekaagcrm'); ?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($invoices as $i => $invoice) {
    if ($invoice->getTotalStr(false) == 0) continue; 
    $downpayment = $invoice->downpayment != 'none' ? ($invoice->downpayment == 'fixed' ? $invoice->dpvalue : ($invoice->getTotalStr(false)/100)*round($invoice->dpvalue) ) : false;
      
    $hash = crypt($invoice->invoicenr, $invoice->invoicenr.'DEKAAG');
?>
    <tr <?php if ($i%2==0) echo ' class="odd"'; ?>>
    
      <td><a href="/facturen?download=<?php echo $invoice->invoicenr; ?>&hash=<?php echo $hash; ?>"><?php echo $invoice->invoicenr; ?></a></td>
      <td><?php echo date('d-m-Y', strtotime($invoice->date)); ?></td>
      <td><?php echo $invoice->getTotalStr(); 
      
      if($invoice->status == 3) {
        echo ' ( '.$invoice->getTotalRemainingStr().' resteert )'; 
      }
      ?></td>
      <td><?php echo $invoice->getStatusStr(); ?></td>
      <td style="text-align:right;">
      <?php 
      if (in_array($invoice->status, array(2,3))) { 
        if ($invoice->status == 2) {
          // downpayment was already done, only show button to pay for the remaining amount
          echo '<button type="button" onclick="window.location.href=\'http://'.$_SERVER['SERVER_NAME'].'/betalen?invoice='.$invoice->invoicenr.'&hash='.$hash.'\';">'.__('Volledig betalen').'</button>';
          if ($downpayment) {
            echo '<button type="button" onclick="window.location.href=\'http://'.$_SERVER['SERVER_NAME'].'/betalen?invoice='.$invoice->invoicenr.'&hash='.$hash.'&downpayment=1\';">'.__('Aanbetaling betalen').'</button>';
          }
        }
        else {
          echo '<button type="button" onclick="window.location.href=\'http://'.$_SERVER['SERVER_NAME'].'/betalen?invoice='.$invoice->invoicenr.'&hash='.$hash.'\';">'.__('Restant betalen').'</button>';
        }
      } ?>
      </td>
    </tr>
  <?php } ?>
  </tbody>
</table>

<?php } else { ?>
<p><strong><?php echo __('You have no invoices.', 'dekaagcrm'); ?></strong></p>
<?php } ?>