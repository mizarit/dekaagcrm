<?php
/**
 * @package De Kaag CRM
 */
class DeKaagCRM_Login extends WP_Widget {

	function __construct() {
		load_plugin_textdomain('dekaagcrm');
		
		parent::__construct(
			'dekaagcrm_login',
			__( 'De Kaag CRM Login Widget' , 'dekaagcrm'),
			array( 'description' => __( 'Description of the De Kaag CRM login widget' , 'dekaagcrm') )
		);

		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'wp_head', array( $this, 'css' ) );
		}
	}
	
	function css() {

	}
	
	public function widget($args, $instance)
	{
	  if (!session_id()) session_start();
	  wp_enqueue_style('dekaagcrm-frontend', plugins_url('../css/frontend.css', __FILE__));
	  echo $args['before_widget'];
	  if (!empty($instance['redirect_url'])) {
	    
	    if (isset($_POST['username']) && isset($_POST['password'])) {
	      $errors = array();
	      
	      $user = DeKaagUser::model()->findByAttributes(new DeKaagCriteria(array('username' => $_POST['username'])));
	      if (!$user) {
	        $errors[] = __('Unknown username or wrong password');
	      }
	      else {
	        $salt = $user->salt;
	        $password = crypt($_POST['password'], $salt);
	        if($password != $user->password) {
	          $errors[] = __('Unknown username or wrong password');
	        }
	      }
	      
	      if (count($errors) == 0) {
	        $_SESSION['dekaag_user_id'] = $user->id;
	        $_SESSION['dekaag_relation_id'] = $user->{$user->prefix().'relation_id'};
	        echo "<script type=\"text/javascript\">window.location.href='{$instance['redirect_url']}';</script>";
  	      exit;
	        
	      }
	      else {
	        echo '<ul class="form-errors">';
	        foreach ($errors as $error) {
	          echo '<li>'.$error.'</li>';
	        }
	        echo '</ul>';
	      }
	    }
	    if (isset($_GET['logoff'])) {
	      unset($_SESSION['dekaag_user_id']);
	      unset($_SESSION['dekaag_relation_id']);
	      unset($_SESSION['booking']);
	      echo "<script type=\"text/javascript\">window.location.href='/';</script>";
  	    exit;
	    }
	    
	    if (isset($_SESSION['dekaag_user_id'])) {
	      $user = DeKaagUser::model()->findByPk($_SESSION['dekaag_user_id']);
	      $url = trim($_SERVER['REQUEST_URI'], '/');
	      ?>
	      
	      <h3><?php echo __('Welcome'); ?> <?php echo $user->relation->title; ?></h3>
  	 <ul id="consumer-nav">
  	   <li <?php if ($url == 'geschiedenis') echo ' class="active-item"'; ?>><a href="/geschiedenis"><?php echo __('history', 'dekaagcrm'); ?></a></li>
  	   <li <?php if ($url == 'facturen') echo ' class="active-item"'; ?>><a href="/facturen"><?php echo __('invoices', 'dekaagcrm'); ?></a></li>
  	   <li <?php if ($url == 'instellingen') echo ' class="active-item"'; ?>><a href="/instellingen"><?php echo __('my settings', 'dekaagcrm'); ?></a></li>
  	   <li><a href="?logoff"><?php echo __('logout', 'dekaagcrm'); ?></a></li>
  	 </ul>
  	 <div style="clear:both;"></div>
	 <?php 
	    }
	    else {
	  ?>
	  <div id="widget-container">
	  <form action="#" class="login-form" method="POST" role="login">
	    <div class="form-row">
	      <div class="form-label"><label for="username"><?php echo __('Username', 'dekaagcrm'); ?></label></div>
  	    <input type="text" title="<?php echo __('Username'); ?>" name="username" value="" placeholder="<?php echo __('Username'); ?> …" class="login-field">
			</div>
			<div class="form-row">
  			<div class="form-label"><label for="password"><?php echo __('Password', 'dekaagcrm'); ?></label></div>
				<input type="password" title="<?php echo __('Password'); ?>" name="password" value="" placeholder="<?php echo __('Password'); ?> …" class="login-field">
			</div>
			<div class="form-buttons">
			 <button type="submit"><?php echo __('Login'); ?></button>
			</div>
		</form>
		</div>
	  <?php
	    }
	  }
	  echo $args['after_widget'];
	}
	
	public function form($instance)
	{
	  $redirect_url = isset($instance['redirect_url']) ? $instance[ 'redirect_url' ] : '/geschiedenis';
		?>
		<p>
  		<label for="<?php echo $this->get_field_id('redirect_url'); ?>"><?php echo __('Redirect URL after login:'); ?></label> 
  		<input class="widefat" id="<?php echo $this->get_field_id('redirect_url'); ?>" name="<?php echo $this->get_field_name('redirect_url'); ?>" type="text" value="<?php echo esc_attr($redirect_url); ?>">
		</p>
		<?php 
	}
	
	public function update($new_instance, $old_instance)
	{
	  $instance = array();
		$instance['redirect_url'] = ( ! empty( $new_instance['redirect_url'] ) ) ? strip_tags( $new_instance['redirect_url'] ) : '/geschiedenis';
		return $instance;
	}
}
