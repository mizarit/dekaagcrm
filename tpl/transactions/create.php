        <div class="wrap">
          <div id="icon-users" class="icon32"><br/></div>
          <h2><?php echo __($create?'Create invoice':'Edit invoice' , 'dekaagcrm'); ?></h2>
        
          
        <form action="#" method="POST">
          <input type="hidden" name="consumer-id" id="consumer-id" value="<?php echo $model->{$model->prefix().'relation_id'}; ?>">
           <table class="form-table">
            	<tbody>
              	<tr class="form-field">
              		<th scope="row"><label for="consumer"><?php echo __('Consumer', 'dekaagcrm'); ?></label></th>
              		<td><input type="text" value="<?php echo $model->relation->title; ?>" id="consumer" name="consumer" style="width:350px;"> <a class="add-new-h2" style="top:0;" href="/wp-admin/admin.php?page=dekaagcrm_consumers&action=create"><?php echo __( 'New relation' , 'dekaagcrm'); ?></a></td>
              	</tr>
              	
              	<tr class="form-field">
              		<th scope="row"><label for="company"><?php echo __('Company', 'dekaagcrm'); ?></label></th>
              		<td>
              		<select id="company" name="company" style="width:350px;">
              		  <option value="1">De Kaag</option>
              		  <option value="2">Spaarnwoude</option>
              		</select>
              		</td>
              	</tr>
              
              	<tr class="form-field consumer-required">
              		<th scope="row"><label for="address"><?php echo __('Address', 'dekaagcrm'); ?></label></th>
              		<td><textarea rows="3" cols="10" id="address" name="address" style="width:350px;"><?php echo $model->address; ?></textarea></td>
              	</tr>
              	
              	<tr class="form-field consumer-required">
              		<th scope="row"><label for="date"><?php echo __('Invoice date', 'dekaagcrm'); ?></label></th>
              		<td><input type="text" name="date" value="<?php echo date('d-m-Y', strtotime($model->date)); ?>" id="date" style="width:90px;"><span class="description">dd-mm-jjjj</span></td>
              	</tr>
              	<tr class="form-field consumer-required">
              		<th scope="row"><label for="enddate"><?php echo __('Invoice enddate', 'dekaagcrm'); ?></label></th>
              		<td><input type="text" name="enddate" value="<?php echo date('d-m-Y', strtotime($model->enddate)); ?>" id="enddate" style="width:90px;"><span class="description">dd-mm-jjjj</span></td>
              	</tr>
              	
              	<tr class="form-field consumer-required">
              		<th scope="row"><label for="rows"><?php echo __('Rows', 'dekaagcrm'); ?></label></th>
              		<td>
              	<?php for ($row = 0; $row < 10; $row++) { 
              	  $desc = isset($rows[$row]) ? $rows[$row]->description : '';
              	  $total = isset($rows[$row]) ? $rows[$row]->total : '';
              	  $vat = isset($rows[$row]) ? $rows[$row]->vat : 21;
              	  
              	  ?>
              	
          		        <input type="text" value="<?php echo $desc; ?>" id="row_<?php echo $row; ?>_desc" name="row_desc[<?php echo $row; ?>]" style="width:550px;">
          		        € <input type="text" value="<?php if(is_numeric($total)) echo number_format($total,2,',','.'); ?>" id="row_<?php echo $row; ?>_price" name="row_price[<?php echo $row; ?>]" style="text-align:right;width:80px;">
          		        <select name="row_vat[<?php echo $row; ?>]" id="row_<?php echo $row; ?>_vat" style="width:60px;">
          		          <option <?php if($vat==6) echo ' selected="selected"'; ?> value="6">6%</option>
          		          <option <?php if($vat==21) echo ' selected="selected"'; ?> value="21">21%</option>
          		        </select><br />
              	<?php } ?>
              	         <p class="description indicator-hint"><?php echo __('Prices are including VAT.'); ?></p>
              	     </td>
              	</tr>
              	
              	<tr class="form-field consumer-required">
              		<th scope="row"><label for="allow_downpayment"><?php echo __('Allow downpayment?') ?></label></th>
              		<td><label for="allow_downpayment"><input <?php if ($model->downpayment != 'none' && $model->downpayment != '')echo ' checked="checked"';?> onchange="jQuery('.downpayment-required').each(function(s,i){ v = jQuery('#allow_downpayment').attr('checked');if (v) {jQuery(i).addClass('has-downpayment'); } else {jQuery(i).removeClass('has-downpayment'); } });" type="checkbox" name="allow_downpayment" id="allow_downpayment" value="1"> <?php echo __('Allow downpayment for this invoice.'); ?></label>
              		</td>
              	</tr>
              	
              	<tr class="form-field consumer-required downpayment-required">
              		<th scope="row"><label for="downpayment"><?php echo __('Downpayment', 'dekaagcrm'); ?></label></th>
              		<td>
              		    <table>
              		        <tr>
              		            <td style="padding:0;margin:0;"><input style="width:auto;"<?php if ($model->downpayment == 'fixed') echo ' checked="checked"'; ?> type="radio" value="fixed"  id="downpayment_fixed" name="downpayment"> <label for="downpayment_fixed"><?php echo __('fixed', 'dekaagcrm'); ?></label></td>
              		            <td style="padding:0;margin:0;">€</td>
              		            <td style="padding:0;margin:0;"><input type="text" value="<?php if ($model->downpayment == 'fixed') echo number_format($model->dpvalue,2,',','.'); ?>" id="downpayment_fixed_value" name="downpayment_fixed_value" style="width:80px;"></td>
              		            <td style="padding:0;margin:0;"></td>
              		            <td style="padding:0;margin:0;"><?php echo __('payed before', 'dekaagcrm'); ?></td>
              		            <td style="padding:0;margin:0;"><input type="text" name="payedbefore" value="<?php echo date('d-m-Y', strtotime($model->dpdate)); ?>" id="payedbefore" style="width:90px;"><span class="description">dd-mm-jjjj</span></td>
              		        </tr>
              		        <tr>
              		            <td style="padding:0;margin:0;"><input style="width:auto;"<?php if ($model->downpayment == 'percent') echo ' checked="checked"'; ?> type="radio" value="percent"  id="downpayment_percent" name="downpayment"> <label for="downpayment_percent"><?php echo __('percent', 'dekaagcrm'); ?></label></td>
              		            <td style="padding:0;margin:0;"></td>
              		            <td style="padding:0;margin:0;"><input type="text" value="<?php if ($model->downpayment == 'percent') echo round($model->dpvalue); ?>" id="downpayment_percent_value" name="downpayment_percent_value" style="width:80px;"></td>
              		            <td style="padding:0;margin:0;">%</td>
              		            <td style="padding:0;margin:0;"></td>
              		            <td style="padding:0;margin:0;"></td>
              		        </tr>
              		    </table>
              		</td>
              	</tr>
              	
              	<tr class="form-field consumer-required">
              		<th scope="row"><label for="send_invoice"><?php echo __('Send invoice to consumer?') ?></label></th>
              		<td><label for="send_invoice"><input onchange="console.log(this.checked);if(this.checked){jQuery('#save-button').val('<?php echo __($create?'Create invoice, finalize and send':'Save invoice, finalize and send', 'dekaagcrm'); ?>');}else{jQuery('#save-button').val('<?php echo __($create?'Create invoice':'Save invoice', 'dekaagcrm'); ?>');}" type="checkbox" name="send_invoice" id="send_invoice" value="1"> <?php echo __('Send this invoice to the consumer by email.'); ?></label>
              		<p class="description indicator-hint"><?php echo __('Note: Invoices that are sent to consumer cannot be edited.'); ?></p>
              		</td>
              	</tr>
              
                <tr class="consumer-required">
                  <td colspan="2">
                    <p class="submit"><input type="submit" id="save-button" value="<?php echo __($create?'Create invoice':'Save invoice', 'dekaagcrm'); ?>" class="button button-primary"></p>
                  </td>
                </tr>
              </tbody>
            </table>
         </form>  
        </div>
