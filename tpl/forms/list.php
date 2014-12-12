    <div class="wrap">
      <div id="icon-users" class="icon32"><br/></div>
      <h2><?php echo __( 'Forms' , 'dekaagcrm'); ?><!-- <a class="add-new-h2" href="/wp-admin/admin.php?page=dekaagcrm_forms&action=create"><?php echo __( 'New form' , 'dekaagcrm'); ?></a>--></h2>
     
      <form id="forms-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php $table->display() ?>
      </form>
    </div>