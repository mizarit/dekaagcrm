  <div class="wrap">
          <div id="icon-users" class="icon32"><br/></div>
          <h2><?php echo __( 'Add payment for invoice' , 'dekaagcrm'); ?> <?php echo $model->invoicenr; ?></h2>
        
          <div class="postbox">
            <h3 class="hndle" style="padding-left:10px;"><span><?php echo __('Current payment status', 'dekaagcrm'); ?></span></h3>
            <div class="inside">
            
              <table class="form-table">
              	<tbody>
                  <tr class="form-field">
                		<th scope="row"><?php echo __('Invoice number', 'dekaagcrm'); ?></th>
                		<td><a href="/wp-admin/admin.php?page=dekaagcrm_transactions&action=download&invoice=<?php echo $model->id; ?>"><?php echo $model->invoicenr; ?></a></td>
                	</tr>	
              	 <tr class="form-field">
                		<th scope="row"><?php echo __('Relation', 'dekaagcrm'); ?></th>
                		<td><a href="/wp-admin/admin.php?page=dekaagcrm_consumers&action=edit&consumer=<?php echo $model->relation->id; ?>"><?php echo $model->getRelationStr(); ?></a></td>
                	</tr>
                	<tr class="form-field">
                		<th scope="row"><?php echo __('Invoice date', 'dekaagcrm'); ?></th>
                		<td><?php echo $model->date; ?></td>
                	</tr>
              	 <tr class="form-field">
                		<th scope="row"><?php echo __('Exp date', 'dekaagcrm'); ?></th>
                		<td><?php echo $model->enddate; ?></td>
                	</tr>
              	 <tr class="form-field">
                		<th scope="row"><?php echo __('Total sum', 'dekaagcrm'); ?></th>
                		<td><?php echo $model->getTotalStr(); ?></td>
                	</tr>
                	<tr class="form-field">
                		<th scope="row"><?php echo __('Payed', 'dekaagcrm'); ?></th>
                		<td><?php echo $model->getPayedStr(); ?></td>
                	</tr>
                	<tr class="form-field">
                		<th scope="row"><?php echo __('Remaining', 'dekaagcrm'); ?></th>
                		<td><?php echo $model->getTotalRemainingStr(); ?></td>
                	</tr>
               </tbody>
             </table>
           </div>
         </div>
         <?php if($model->getTotalRemainingStr(false) > 0) { ?>
         <form action="#" method="POST">
           <table class="form-table">
            	<tbody>
              	<tr class="form-field">
              		<th scope="row"><label for="date"><?php echo __('Date', 'dekaagcrm'); ?></label></th>
              		<td><input type="text" name="date" value="<?php echo date('d-m-Y'); ?>" id="date" style="width:90px;"><span class="description">dd-mm-jjjj</span></td>
              	</tr>
              	<tr class="form-field">
              		<th scope="row"><label for="paymethod_bank"><?php echo __('Paymethod', 'dekaagcrm'); ?></label></th>
              		<td>
              		  <input style="width:auto;" type="radio"<?php if ($payment->paymethod=='bank') echo ' checked="checked"'; ?> value="bank"  id="paymethod_bank" name="paymethod"> <label for="paymethod_bank"><?php echo __('banking', 'dekaagcrm'); ?></label><br />
              		  <input style="width:auto;" type="radio"<?php if ($payment->paymethod=='ideal') echo ' checked="checked"'; ?> value="ideal"  id="paymethod_ideal" name="paymethod"> <label for="paymethod_ideal"><?php echo __('ideal', 'dekaagcrm'); ?></label>
              		</td>
              	</tr>
              	<tr class="form-field">
              		<th scope="row"><label for="total"><?php echo __('Total', 'dekaagcrm'); ?> <span class="description"></span></label></th>
              		<td>€ <input type="text" value="<?php echo number_format($payment->total,2,',','.'); ?>" id="total" name="total" style="width:80px;"></td>
              	</tr>
              </tbody>
            </table>
  
            <p class="submit"><input type="submit" value="<?php echo __('Add payment', 'dekaagcrm'); ?>" class="button button-primary"></p>
         </form>
         <?php } ?>
       </div>