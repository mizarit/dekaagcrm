	  <div class="wrap">
      <div id="icon-users" class="icon32"><br/></div>
      <h2><?php echo __( 'Transactions' , 'dekaagcrm'); ?> <a class="add-new-h2" href="/wp-admin/admin.php?page=dekaagcrm_transactions&action=create"><?php echo __( 'New invoice' , 'dekaagcrm'); ?></a></h2>
     
      <form method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php $table->display() ?>
      </form>
    </div>