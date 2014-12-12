  <div class="wrap">
          <div id="icon-users" class="icon32"><br/></div>
          <h2><?php echo __( 'View payments for invoice' , 'dekaagcrm'); ?> <?php echo $model->invoicenr; ?></h2>
        
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
         
         <div class="postbox">
            <h3 class="hndle" style="padding-left:10px;"><span><?php echo __('Payments', 'dekaagcrm'); ?></span></h3>
            <div class="inside">
            
              <table class="form-table">
              	<tbody>
              	 <tr>
              	   <th style="width:120px;"><?php echo __('Date', 'dekaagcrm'); ?></th>
              	   <th style="width:120px;"><?php echo __('Total', 'dekaagcrm'); ?></th>
              	   <th style="width:120px;"><?php echo __('Paymethod', 'dekaagcrm'); ?></th>
              	   <th style="width:auto;"><?php echo __('Remarks', 'dekaagcrm'); ?></th>
              	 </tr>
              	<?php foreach ($payments as $payment) { ?>
                  <tr class="form-field">
                		<td style="padding-left:0;"><?php echo date('d-m-Y', strtotime($payment->date)); ?></th>
                		<td style="padding-left:0;">€ <?php echo number_format($payment->total, 2, ',', '.'); ?></td>
                		<td style="padding-left:0;"><?php echo __($payment->paymethod, 'dekaagcrm'); ?></td>
                		<td style="padding-left:0;"><?php echo $payment->remarks; ?></td>
                	</tr>	
              	 <?php } ?>
               </tbody>
             </table>
           </div>
         </div>
       </div>