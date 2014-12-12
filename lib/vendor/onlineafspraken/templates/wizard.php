<?php echo DeKaagForm::render(1); ?>
<button type="button" onclick="window.location.href=window.location.href;"><?php echo __('Back', 'dekaagcrm'); ?></button>
<button type="button" onclick="if(formValidate())widget.startCalendar();"><?php echo __('Volgende stap', 'dekaagcrm'); ?></button>