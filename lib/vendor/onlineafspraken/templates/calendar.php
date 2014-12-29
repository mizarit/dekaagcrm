<input type="hidden" name="widget-facility" id="widget-facility" value="<?php echo $selectedCategory; ?>">
<input type="hidden" name="widget-facility-text" id="widget-facility-text" value="<?php echo $categories[$selectedCategory]; ?>">
<input type="hidden" name="widget-apptype" id="widget-apptype" value="<?php echo $selectedApptype; ?>">
<button type="button" onclick="window.location.href=window.location.href;"><?php echo __('Back', 'dekaagcrm'); ?></button>
<?php echo DeKaagForm::render($_SESSION['company']==2?3:1); ?>
<div class="layout-6" id="booking-container" style="margin-top:5px;">
  <div id="schedule-container">
    <table id="booking-schedule" class="booking-schedule">
      <thead>
        <tr>
          <th colspan="2">Beschikbaarheid</th>
          <th>Duur</th>
        </tr>
      </thead>
      <tbody id="booking-schedule-tbody">
      </tbody>
    </table>
  </div>
</div> 