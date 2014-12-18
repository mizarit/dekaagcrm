<form action="#" method="post" id="appointment-form">
	<fieldset>
		<legend>Make appointment form</legend>
		
		<div id="widget-facility-container">
      <div class="form-row" id="widget-row-facility">
        <div class="form-label" style="display:block;width:300px;"><label for="widget-apptype"><?php echo __('Maak je keuze'); ?></label></div>
        <?php echo __select_tag('widget-facility', $selectedCategory, $categories, array('onchange' => 'widget.update(\'facility\', \'on facility change\');')); ?>
      </div>
		</div>
		<?php 
		if ($selectedCategory == '') {
		  $selectedCategory = $firstCategory; 
      $selectedCategory = array_search($selectedCategory, $categories); 
		}
		if (isset($_GET['apptype'])) {
      $selectedApptype = $_GET['apptype'];
    }
		?>
		<div class="form-row" id="widget-row-apptype">
			<div class="form-label" style="display:block;width:300px;"><label for="widget-apptype"><?php echo __('Wat wil je bij ons reserveren?'); ?></label></div>
			<?php echo __select_tag('widget-apptype', $selectedApptype, (isset($categories[$selectedCategory]) && isset($appTypes[$categories[$selectedCategory]])) ? $appTypes[$categories[$selectedCategory]] : array(), array('onchange' => 'widget.update(\'apptype\', \'on apptype change\');')); ?>
		</div>
		<div style="display:none;">
		<h2><?php echo __('Kies een medewerker of artikel'); ?></h2>
		<div class="form-row" id="widget-row-resource">
			<div class="form-label"><label for="widget-resource"><?php echo __('Resource'); ?></label></div>
			<?php echo __select_tag('widget-resource', $selectedResource, isset($resources[$selectedApptype]) ? $resources[$selectedApptype] : array(__('Zonder resource')), array('onchange' => 'widget.update(\'resource\', \'on resource change\');')); ?>
		</div>
		</div>
		<div class="form-row">
		  <div class="form-label" style="display:block; width:218px;"><label for="dob"><?php echo __('Wat is je geboortedatum?', 'dekaagcrm'); ?></div>
		  <input type="text" name="dob" id="dob" <?php if(isset($_SESSION['booking']['dob'])) echo ' value="'.$_SESSION['booking']['dob'].'"'; ?> style="padding:4px;width:110px"> (dd-mm-jjjj)
		  <p class="hint">Je geboortedatum is niet verplicht maar maakt het voor ons mogelijk om alleen te tonen wat voor jou geschikt is.</p>
		</div>
		<button type="button" onclick="widget.startCalendar();">Volgende stap</button>
	</fieldset> 
</form>
<script type="text/javascript">
<?php if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') { ?>
var calendar = null;
Event.observe(window, 'load', function() {
<?php 
} 

echo 'widget.options.appTypes = '.json_encode($appTypes).PHP_EOL;
echo 'widget.options.resources = '.json_encode($resources).PHP_EOL;
echo 'widget.options.duration = '.json_encode($appTypesDurations).PHP_EOL;

foreach ($bookableDays as $bookableDay) { ?>
	if ($('day-<?php echo $bookableDay; ?>')) { 
		$('day-<?php echo $bookableDay; ?>').addClassName('bookable');
	}
<?php } ?>

	widget.options.selectedDate = '<?php echo date('Y-n-j'); ?>'; 
	widget.update('facility', 'initial for ' + widget.options.selectedDate);
<?php if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') { ?>
});
<?php } ?>
</script>