<?php if (count($appointments) > 0) {  ?>
<table class="dekaagcrm-frontend-table">
  <thead>
    <tr>
      <th style="width:120px"><?php echo __('Persona', 'dekaagcrm'); ?></th>
      <th style="width:120px"><?php echo __('Date', 'dekaagcrm'); ?></th>
      <th><?php echo __('Type reservering', 'dekaagcrm'); ?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($appointments as $i => $appointment) { 
    $invoice = $appointment->invoice;
    //$hash = crypt($invoice->invoicenr, $invoice->invoicenr.'DEKAAG');
    $hash = substr(md5($invoice->invoicenr.'DEKAAG123456789123456789'),8,16); 
    ?>
    <tr <?php if ($i%2==0) echo ' class="odd"'; ?>>
      <td><?php echo $appointment->persona->title; ?></td>
      <td><?php echo date('d-m-Y', strtotime($appointment->date)); ?></td>
      <td><a href="/facturen?download=<?php echo $invoice->invoicenr; ?>&hash=<?php echo $hash; ?>"><?php echo $appointment->getTitleStr(); ?></a></td>
    </tr>
  <?php } ?>
  </tbody>
</table>

<?php } else { ?>
<p><strong><?php echo __('You have no appointments.', 'dekaagcrm'); ?></strong></p>
<?php } ?>