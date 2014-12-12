<?php
/**
 * @package De Kaag CRM
 */
class DeKaagCRM_Widget extends WP_Widget {

	function __construct() {
		load_plugin_textdomain('dekaagcrm');
		
		parent::__construct(
			'dekaagcrm_widget',
			__( 'De Kaag CRM Widget' , 'dekaagcrm'),
			array( 'description' => __( 'Description of the De Kaag CRM widget' , 'dekaagcrm') )
		);

		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'wp_head', array( $this, 'css' ) );
		}
	}
	
	function css() {
?>

<style type="text/css">
</style>
<?php
	}
	
	public function widget($args, $instance)
	{
	  echo $args['before_widget'];
	  if (!empty($instance['api_key'])) {
	  ?>
	  <script type="text/javascript">document.domain='onlineafspraken.nl';</script><iframe src="https://widget.onlineafspraken.nl/iFrame/book/key/<?php echo $instance['api_key']; ?>/w/400/h/1500/f/1/fs2/12/fs1/14/fs3/14/c0/ffffff/c1/00aeef/c2/7dc473/c3/111111/c4/00aeef/c5/ffffff/l/1/si/0/sr/1/siteLanguageId/3/fb/0/logo/0/at/0/rs/0/th/default" frameborder="0" scrolling="no" width="400" height="1500"></iframe>
	  <?php
	  }
	  echo $args['after_widget'];
	}
	
	public function form($instance)
	{
	  $api_key = isset($instance['api_key']) ? $instance[ 'api_key' ] : 'bldk25woqu91-blza01';
	  $api_secret = isset($instance['api_secret']) ? $instance[ 'api_secret' ] : '1071b3303a40dc574a18d2ec6f780024f9b3681b';
	  $api_url = isset($instance['api_url']) ? $instance[ 'api_url' ] : 'http://agenda.onlineafspraken.nl/APIREST';
	
		?>
		<p>
  		<label for="<?php echo $this->get_field_id('api_key'); ?>"><?php _e( 'API key:' ); ?></label> 
  		<input class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo esc_attr($api_key); ?>">
		</p>
		<p>
  		<label for="<?php echo $this->get_field_id('api_secret'); ?>"><?php _e( 'API secret:' ); ?></label> 
  		<input class="widefat" id="<?php echo $this->get_field_id('api_secret'); ?>" name="<?php echo $this->get_field_name('api_secret'); ?>" type="text" value="<?php echo esc_attr($api_secret); ?>">
		</p>
		<p>
  		<label for="<?php echo $this->get_field_id('api_url'); ?>"><?php _e( 'API URL:' ); ?></label> 
  		<input class="widefat" id="<?php echo $this->get_field_id('api_url'); ?>" name="<?php echo $this->get_field_name('api_url'); ?>" type="text" value="<?php echo esc_attr($api_url); ?>">
		</p>
		<?php 
	}
	
	public function update($new_instance, $old_instance)
	{
	  $instance = array();
		$instance['api_key'] = ( ! empty( $new_instance['api_key'] ) ) ? strip_tags( $new_instance['api_key'] ) : '';
		$instance['api_secret'] = ( ! empty( $new_instance['api_secret'] ) ) ? strip_tags( $new_instance['api_secret'] ) : '';
		$instance['api_url'] = ( ! empty( $new_instance['api_url'] ) ) ? strip_tags( $new_instance['api_url'] ) : '';
		return $instance;
	}
}
