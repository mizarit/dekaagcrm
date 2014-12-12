<?php

class DeKaagCRM {

	private static $initiated = false;
	
	protected $templates;
	private static $instance;
	
	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
		//add_action( 'wp_insert_comment', array( 'Akismet', 'auto_check_update_meta' ), 10, 2 );
		//add_action( 'preprocess_comment', array( 'Akismet', 'auto_check_comment' ), 1 );
	}
	
	private function __construct()
	{
	  
	  $this->templates = array();
	 
	  // Add a filter to the attributes metabox to inject template into the cache.
    add_filter(
        'page_attributes_dropdown_pages_args',
         array( $this, 'register_project_templates' )
    );
	 
    // Add a filter to the save post to inject out template into the page cache
    add_filter(
        'wp_insert_post_data',
        array( $this, 'register_project_templates' )
    );
    
    // Add a filter to the template include to determine if the page has our
    // template assigned and return it's path
    add_filter(
        'template_include',
        array( $this, 'view_project_template')
    );
    
    
    // Add your templates to this array.
    $this->templates = array(
      'ideal-transaction-template.php'     => 'iDeal transactie',
      'consumer-history-template.php'     => 'Consument geschiedenis',
      'consumer-invoices-template.php'     => 'Consument facturen',
      'consumer-settings-template.php'     => 'Consument instellingen',
      'consumer-book-template.php'     => 'Consument reservering',
      'consumer-login-template.php'     => 'Consument login',
    );
	}
	
	public static function get_instance() {
    if( null == self::$instance ) {
      self::$instance = new DeKaagCRM();
    } 
  
    return self::$instance;
  } 
	
  public function register_project_templates( $atts ) {

    // Create the key used for the themes cache
    $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

    // Retrieve the cache list. 
    // If it doesn't exist, or it's empty prepare an array
    $templates = wp_get_theme()->get_page_templates();
    if (empty($templates)) {
      $templates = array();
    } 

    // New cache, therefore remove the old one
    wp_cache_delete($cache_key, 'themes');

    // Now add our template to the list of templates by merging our templates
    // with the existing templates array from the cache.
    $templates = array_merge($templates, $this->templates );

    // Add the modified cache to allow WordPress to pick it up for listing
    // available templates
    wp_cache_add($cache_key, $templates, 'themes', 1800 );

    return $atts;

  }
  
	
  /**
   * Checks if the template is assigned to the page
   */
  public function view_project_template( $template ) {

    global $post;

    if (!isset($this->templates[get_post_meta($post->ID, '_wp_page_template', true )] ) ) {
      return $template;
    } 

    $file = plugin_dir_path(__FILE__).'/tpl/layouts/'. get_post_meta( 
    	$post->ID, '_wp_page_template', true 
    );

    // Just to be safe, we check if the file exist first
    if(file_exists($file)) {
      return $file;
    } 
    else { 
      echo $file; 
    }

    return $template;
  } 
  
  
	
	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {
		global $wpdb;
  	global $dekaagcrm_db_version;
  
  	$prefix = $wpdb->prefix.'dekaagcrm_';
  	
  	$charset_collate = $wpdb->get_charset_collate();
  	
  	$sqls = array();
 
    $sqls[] = "INSERT INTO `{$prexix}form` (`id`, `title`) VALUES (3, 'Inschrijfformulier stap 1 Spaarnwoude'), (4, 'Inschrijfformulier stap 2 Spaarnwoude');";
    $sqls[] = "ALTER TABLE `{$prexix}appointment` ADD `company` INT( 11 ) NOT NULL AFTER `info`;";
    $sqls[] = "ALTER TABLE `{$prexix}invoice` ADD `company` INT( 11 ) NOT NULL AFTER `info`;";
    $sqls[] = "ALTER TABLE `{$prexix}relation` ADD `insertions` VARCHAR( 255 ) NOT NULL AFTER `first_name`;";
    $sqls[] = "ALTER TABLE `{$prexix}persona`  ADD `first_name` VARCHAR(255) NOT NULL AFTER `title`,  ADD `insertions` VARCHAR(255) NOT NULL AFTER `first_name`,  ADD `last_name` VARCHAR(255) NOT NULL AFTER `insertions`;";
    
    foreach ($sqls as $sql) {
      $wpdb->query($sql);
    }
    
    /*
  	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$prefix}appointment` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `{$prefix}invoice_id` int(11) NOT NULL,
      `{$prefix}persona_id` int(11) DEFAULT NULL,
      `{$prefix}relation_id` int(11) NOT NULL,
      `date` date NOT NULL,
      `created_at` datetime NOT NULL,
      `apptype_id` int(11) NOT NULL,
      `info` text NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB {$charset_collate};";
  	
  	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$prefix}diploma` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(32) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB {$charset_collate};";
  	
  	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$prefix}form` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB {$charset_collate};";
  	
  	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$prefix}form_row` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `{$prefix}form_id` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `explanation` text NOT NULL,
      `oninvoice` varchar(255) NOT NULL,
      `answers` text NOT NULL,
      `default` int(11) DEFAULT NULL,
      `mutations` text NOT NULL,
      `validators` text NOT NULL,
      `rowtype` enum('question','mutation') NOT NULL,
      `fieldtype` enum('radio','input','select') NOT NULL DEFAULT 'radio',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB {$charset_collate};";
  	
  	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$prefix}invoice` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `invoicenr` varchar(32) NOT NULL,
      `date` date NOT NULL,
      `enddate` date NOT NULL,
      `dpdate` date NOT NULL,
      `reminded` date NOT NULL,
      `remindeddp` date NOT NULL,
      `dpvalue` float(6,2) NOT NULL,
      `downpayment` enum('none','fixed','percent') NOT NULL DEFAULT 'none',
      `{$prefix}relation_id` int(11) DEFAULT NULL,
      `{$prefix}persona_id` int(11) DEFAULT NULL,
      `pdf` varchar(255) NOT NULL,
      `status` int(11) NOT NULL,
      `address` text NOT NULL,
      PRIMARY KEY (`id`),
      KEY `fk_relation` (`{$prefix}relation_id`),
      KEY `fk_persona` (`{$prefix}persona_id`)
    ) ENGINE=InnoDB {$charset_collate};";
  	
  	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$prefix}invoice_row` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `{$prefix}invoice_id` int(11) NOT NULL,
      `description` varchar(255) NOT NULL,
      `total` float(6,2) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `fk_invoice` (`{$prefix}invoice_id`)
    ) ENGINE=InnoDB {$charset_collate};";
  	
  	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$prefix}payment` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `{$prefix}invoice_id` int(11) NOT NULL,
      `total` float(6,2) NOT NULL,
      `paymethod` varchar(16) NOT NULL,
      `status` varchar(16) NOT NULL,
      `remarks` varchar(255) NOT NULL,
      `date` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `id` (`id`),
      KEY `fk_invoice` (`{$prefix}invoice_id`)
    ) ENGINE=InnoDB {$charset_collate};";
  	
  	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$prefix}persona` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `dob` date NOT NULL,
      `gender` enum('m','f') NOT NULL,
      `remarks` text NOT NULL,
      `remarks_private` text NOT NULL,
      `{$prefix}relation_id` int(11) NOT NULL,
      `created_at` datetime NOT NULL,
      `modified_at` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `fk_relation` (`{$prefix}relation_id`)
    ) ENGINE=InnoDB {$charset_collate};";
  	
  	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$prefix}persona_diploma` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `{$prefix}diploma_id` int(11) NOT NULL,
      `{$prefix}persona_id` int(11) NOT NULL,
      `date` date NOT NULL,
      PRIMARY KEY (`id`),
      KEY `fk_diploma` (`{$prefix}diploma_id`),
      KEY `fk_persona` (`{$prefix}persona_id`)
    ) ENGINE=InnoDB {$charset_collate};";
  	
  	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$prefix}relation` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(255) NOT NULL,
      `first_name` varchar(255) NOT NULL,
      `last_name` varchar(255) NOT NULL,
      `address` varchar(64) NOT NULL,
      `zipcode` varchar(6) NOT NULL,
      `city` varchar(32) NOT NULL,
      `phone` varchar(16) NOT NULL,
      `phone_mobile` varchar(255) NOT NULL,
      `phone_extra` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `created_at` datetime NOT NULL,
      `modified_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB {$charset_collate};";
  	
  	$sqls[] = "CREATE TABLE IF NOT EXISTS `{$prefix}user` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `{$prefix}relation_id` int(11) DEFAULT NULL,
      `username` varchar(32) NOT NULL,
      `password` varchar(64) NOT NULL,
      `salt` varchar(22) NOT NULL,
      `role` smallint(6) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB {$charset_collate};";
  	
  	require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
  	
  	$wpdb->query("SET FOREIGN_KEY_CHECKS=0");
  	
  	foreach ($sqls as $sql) {
  	  dbDelta($sql);
  	}
  	
  	// add InnoDB constraints
  	
  	$wpdb->query("ALTER TABLE `{$prefix}invoice`
      ADD CONSTRAINT `{$prefix}invoice_ibfk_1` FOREIGN KEY (`{$prefix}relation_id`) REFERENCES `{$prefix}relation` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
      ADD CONSTRAINT `{$prefix}invoice_ibfk_2` FOREIGN KEY (`{$prefix}persona_id`) REFERENCES `{$prefix}persona` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;");
  	
  	$wpdb->query("ALTER TABLE `{$prefix}invoice_row`
     ADD CONSTRAINT `{$prefix}invoice_row_ibfk_1` FOREIGN KEY (`{$prefix}invoice_id`) REFERENCES `{$prefix}invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
  	
  	$wpdb->query("ALTER TABLE `{$prefix}payment`
      ADD CONSTRAINT `{$prefix}payment_ibfk_1` FOREIGN KEY (`{$prefix}invoice_id`) REFERENCES `{$prefix}invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
  	
  	$wpdb->query("ALTER TABLE `{$prefix}persona`
     ADD CONSTRAINT `{$prefix}persona_ibfk_1` FOREIGN KEY (`{$prefix}relation_id`) REFERENCES `{$prefix}relation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
  	
  	$wpdb->query("ALTER TABLE `{$prefix}persona_diploma`
      ADD CONSTRAINT `{$prefix}persona_diploma_ibfk_2` FOREIGN KEY (`{$prefix}persona_id`) REFERENCES `{$prefix}persona` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      ADD CONSTRAINT `{$prefix}persona_diploma_ibfk_1` FOREIGN KEY (`{$prefix}diploma_id`) REFERENCES `{$prefix}diploma` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");
  	
  	// insert default data
  	$wpdb->query("INSERT INTO `{$prefix}diploma` (`id`, `title`) VALUES
      (3, 'Jeugdzeilen 1-mans CWO I'),
      (4, 'Jeugdzeilen 1-mans CWO II'),
      (5, 'Jeugdzeilen 1-mans CWO III'),
      (6, 'Jeugdzeilen 2-mans CWO I'),
      (7, 'Jeugdzeilen 2-mans CWO II'),
      (8, 'Jeugdzeilen 2-mans CWO III'),
      (9, 'Jeugdzeilen catamaran I'),
      (10, 'Jeugdzeilen Catamaran II'),
      (11, 'Jeugdzeilen Catamaran III'),
      (12, 'Zwaardboot 1-mans CWO I'),
      (13, 'Zwaardboot 1-mans CWO II'),
      (14, 'Zwaardboot 1-mans CWO III'),
      (15, 'Zwaardboot 1-mans CWO IV'),
      (16, 'Zwaardboot 1-mans CWO IV+'),
      (17, 'Zwaardboot 2-mans CWO I'),
      (18, 'Zwaardboot 2-mans CWO II'),
      (19, 'Zwaardboot 2-mans CWO III'),
      (20, 'Zwaardboot 2-mans CWO IV'),
      (21, 'Zwaardboot 2-mans CWO IV+'),
      (22, 'Kielboot CWO I'),
      (23, 'Kielboot CWO II'),
      (24, 'Kielboot CWO III'),
      (25, 'Kielboot CWO IV'),
      (26, 'Kielboot CWO IV+'),
      (27, 'Windsurfen CWO I'),
      (28, 'Windsurfen CWO II'),
      (29, 'Windsurfen CWO III'),
      (30, 'Windsurfen CWO IV'),
      (31, 'Windsurfen CWO IV+'),
      (32, 'Catamaran I'),
      (33, 'Catamaran II'),
      (34, 'Catamaran III'),
      (35, 'Catamaran IV'),
      (36, 'Catamaran IV+');");
  	
  	$wpdb->query("INSERT INTO `{$prefix}form` (`id`, `title`) VALUES
      (1, 'Inschrijfformulier stap 1'),
      (2, 'Inschrijfformulier stap 2');");
  	
  	$wpdb->query("INSERT INTO `{$prefix}form_row` (`id`, `{$prefix}form_id`, `title`, `explanation`, `oninvoice`, `answers`, `default`, `mutations`, `validators`, `rowtype`, `fieldtype`) VALUES
      (1, 1, 'Met welk soort boot wil je varen?', '', 'Type boot', '[\"Type 1\",\"Type 2\"]', NULL, '[{\"mutation\":20,\"type\":\"price\"},{\"mutation\":10,\"type\":\"percent\"}]', '[{\"validate\":\"apptype\",\"0\":{\"validator\":\"in\",\"value\":\"34,35\"}}]', 'question', 'select'),
      (3, 2, 'Wil je waterskieën?', '', 'Waterskieen', '[\"ja\",\"nee\"]', NULL, '[{\"mutation\":0,\"type\":\"price\"},{\"mutation\":0,\"type\":\"price\"}]', '[{\"validate\":\"12\",\"0\":{\"validator\":\"is\",\"value\":0}}]', 'question', 'select'),
      (4, 2, 'Wil je waterskieën? ( € 18,50 )', '', 'Waterskieen', '[\"ja\",\"nee\"]', NULL, '[{\"mutation\":18.5,\"type\":\"price\"},{\"mutation\":0,\"type\":\"price\"}]', '[{\"validate\":\"12\",\"0\":{\"validator\":\"is\",\"value\":1}}]', 'question', 'radio'),
      (5, 2, 'Wil je een keer flyboarden?', '', 'Flyboarden', '[\"ja\",\"nee\"]', NULL, '[{\"mutation\":30,\"type\":\"price\"},{\"mutation\":0,\"type\":\"price\"}]', '[{\"validate\":\"age\",\"0\":{\"validator\":\"greater\",\"value\":14}}]', 'question', 'radio'),
      (6, 2, '', '', '', '[]', 0, '[{\"mutation\":20,\"type\":\"percent\"}]', '[{\"validate\":\"lastbookdate\",\"0\":{\"validator\":\"less\",\"value\":\"1 year\"}}]', 'mutation', ''),
      (7, 2, '', '', '', '[]', 0, '[{\"mutation\":10,\"type\":\"percent\"}]', '[{\"validate\":\"lastbookdate\",\"0\":{\"validator\":\"less\",\"value\":\"1 month\"}}]', 'mutation', ''),
      (8, 2, 'Ga je het hele bedrag in 1x voor 1 februari betalen met iDeal?', '', 'Vroegboekkorting', '[\"ja\",\"nee\"]', NULL, '[{\"mutation\":-30,\"type\":\"price\"},{\"mutation\":0,\"type\":\"price\"}]', '[{\"validate\":\"12\",\"0\":{\"validator\":\"is\",\"value\":1}}]', 'question', 'radio'),
      (9, 2, 'Ga je het hele bedrag in 1x voor 1 februari betalen met iDeal?', '', 'Vroegboekkorting', '[\"ja\",\"nee\"]', NULL, '[{\"mutation\":-15,\"type\":\"price\"},{\"mutation\":0,\"type\":\"price\"}]', '[{\"validate\":\"12\",\"0\":{\"validator\":\"is\",\"value\":0}}]', 'question', 'radio'),
      (10, 1, 'Met welk soort boot wil je varen?', '', 'Type boot', '[\"Type A\",\"Type B\"]', NULL, '[{\"mutation\":20,\"type\":\"price\",\"resource\":\"Pjotr*\"},{\"mutation\":10,\"type\":\"percent\",\"resource\":\"Blonde*\"}]', '[{\"validate\":\"apptype\",\"0\":{\"validator\":\"in\",\"value\":\"36\"}}]', 'question', 'select'),
      (11, 2, 'Reserveer je gelijktijdig met iemand anders', 'Indien je binnen een week reserveert voor een ander kind of met een kennis krijg je € 10,- korting.', 'Gelijktijdige reservering', '[\"ja\",\"nee\"]', 1, '[{\"mutation\":0,\"type\":\"price\"},{\"mutation\":0,\"type\":\"price\"}]', '[]', 'question', 'radio'),
      (12, 1, 'Wil je blijven slapen?', 'Hier wat uitleg over deze vraag', 'Slapen', '[\"ja\",\"nee\"]', NULL, '[{\"mutation\":10,\"type\":\"price\",\"resource\":\"Slaapplek*\"},{\"mutation\":0,\"type\":\"price\"}]', '[]', 'question', 'radio'),
      (13, 2, 'Met wie doe je de reservering', 'Geeft hier aan wie de andere reservering gemaakt heeft zodat we je korting kunnen verwerken.', 'Met wie gelijktijdig', '[\"\"]', 0, '[{\"mutation\":-10,\"type\":\"price\"}]', '[{\"validate\":\"11\",\"0\":{\"validator\":\"is\",\"value\":0}}]', 'question', 'input');
    ");
  	
  	$wpdb->query("SET FOREIGN_KEY_CHECKS=1");
  
  	add_option( 'dekaagcrm_db_version', $dekaagcrm_db_version );
  	*/
	}

	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation( ) {
		//tidy up
		global $wpdb;
		
		$prefix = $wpdb->prefix.'dekaagcrm_';
		
		$wpdb->query("DELETE * FROM {$prefix}diploma");
		
		$wpdb->query("DELETE * FROM {$prefix}form_row");
		
		$wpdb->query("DELETE * FROM {$prefix}form");
	}
	
	private static function bail_on_activation( $message, $deactivate = true ) {
?>
<!doctype html>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<style>
* {
	text-align: center;
	margin: 0;
	padding: 0;
	font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
}
p {
	margin-top: 1em;
	font-size: 18px;
}
</style>
<body>
<p><?php echo esc_html( $message ); ?></p>
</body>
</html>
<?php
		if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$dekaagcrm = plugin_basename( DEKAAGCRM__PLUGIN_DIR . 'dekaagcrm.php' );
			$update  = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $dekaagcrm ) {
					$plugins[$i] = false;
					$update = true;
				}
			}

			if ( $update ) {
				update_option( 'active_plugins', array_filter( $plugins ) );
			}
		}
		exit;
	}
}

class WPFDGitHubPluginUpdater {
    private $slug;
    private $pluginData;
    private $username;
    private $repo;
    private $pluginFile;
    private $githubAPIResult;
    private $accessToken;
    private $pluginActivated;

    /**
     * Class constructor.
     *
     * @param  string $pluginFile
     * @param  string $gitHubUsername
     * @param  string $gitHubProjectName
     * @param  string $accessToken
     * @return null
     */
    function __construct( $pluginFile, $gitHubUsername, $gitHubProjectName, $accessToken = '' )
    {
      add_filter( "pre_set_site_transient_update_plugins", array( $this, "setTransitent" ) );
      add_filter( "plugins_api", array( $this, "setPluginInfo" ), 10, 3 );
      add_filter( "upgrader_pre_install", array( $this, "preInstall" ), 10, 3 );
      add_filter( "upgrader_post_install", array( $this, "postInstall" ), 10, 3 );

      $this->pluginFile 	= $pluginFile;
      $this->username 	= $gitHubUsername;
      $this->repo 		= $gitHubProjectName;
      $this->accessToken 	= $accessToken;
    }

    /**
     * Get information regarding our plugin from WordPress
     *
     * @return null
     */
    private function initPluginData()
    {
  		$this->slug = plugin_basename( $this->pluginFile );
  		$this->pluginData = get_plugin_data( $this->pluginFile );
    }

    /**
     * Get information regarding our plugin from GitHub
     *
     * @return null
     */
    private function getRepoReleaseInfo()
    {
      if ( ! empty( $this->githubAPIResult ) )
      {
  		  return;
		  }

		  // Query the GitHub API
  		$url = "https://api.github.com/repos/{$this->username}/{$this->repo}/releases";
  
  		if ( ! empty( $this->accessToken ) )
  		{
		    $url = add_query_arg( array( "access_token" => $this->accessToken ), $url );
  		}

  		// Get the results
  		$this->githubAPIResult = wp_remote_retrieve_body( wp_remote_get( $url ) );

  		if ( ! empty( $this->githubAPIResult ) )
  		{
		    $this->githubAPIResult = @json_decode( $this->githubAPIResult );
  		}

		  // Use only the latest release
  		if ( is_array( $this->githubAPIResult ) )
  		{
		    $this->githubAPIResult = $this->githubAPIResult[0];
  		}
    }

    /**
     * Push in plugin version information to get the update notification
     *
     * @param  object $transient
     * @return object
     */
    public function setTransitent( $transient )
    {
      if ( empty( $transient->checked ) )
      {
  		  return $transient;
		  }

  		// Get plugin & GitHub release information
  		$this->initPluginData();
  		$this->getRepoReleaseInfo();
  
  		$doUpdate = version_compare( $this->githubAPIResult->tag_name, $transient->checked[$this->slug] );

  		if ( $doUpdate )
  		{
  			$package = $this->githubAPIResult->zipball_url;
  
  			if ( ! empty( $this->accessToken ) )
  			{
			    $package = add_query_arg( array( "access_token" => $this->accessToken ), $package );
  			}
  
  			// Plugin object
  			$obj = new stdClass();
  			$obj->slug = $this->slug;
  			$obj->new_version = $this->githubAPIResult->tag_name;
  			$obj->url = $this->pluginData["PluginURI"];
  			$obj->package = $package;
  
  			$transient->response[$this->slug] = $obj;
  		}

      return $transient;
    }

    /**
     * Push in plugin version information to display in the details lightbox
     *
     * @param  boolean $false
     * @param  string $action
     * @param  object $response
     * @return object
     */
    public function setPluginInfo( $false, $action, $response )
    {
  		$this->initPluginData();
  		$this->getRepoReleaseInfo();
  
  		if ( empty( $response->slug ) || $response->slug != $this->slug )
  		{
  	    return $false;
  		}

  		// Add our plugin information
  		$response->last_updated = $this->githubAPIResult->published_at;
  		$response->slug = $this->slug;
  		$response->plugin_name  = $this->pluginData["Name"];
  		$response->version = $this->githubAPIResult->tag_name;
  		$response->author = $this->pluginData["AuthorName"];
  		$response->homepage = $this->pluginData["PluginURI"];
  
  		// This is our release download zip file
  		$downloadLink = $this->githubAPIResult->zipball_url;
  
  		if ( !empty( $this->accessToken ) )
		{
		    $downloadLink = add_query_arg(
	        array( "access_token" => $this->accessToken ),
	        $downloadLink
		    );
  		}

		$response->download_link = $downloadLink;

		// Load Parsedown
		require_once __DIR__ . DIRECTORY_SEPARATOR . 'Parsedown.php';

		// Create tabs in the lightbox
		$response->sections = array(
			'Description' 	=> $this->pluginData["Description"],
			'changelog' 	=> class_exists( "Parsedown" )
				? Parsedown::instance()->parse( $this->githubAPIResult->body )
				: $this->githubAPIResult->body
		);

		// Gets the required version of WP if available
		$matches = null;
		preg_match( "/requires:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches );
		if ( ! empty( $matches ) ) {
		    if ( is_array( $matches ) ) {
		        if ( count( $matches ) > 1 ) {
		            $response->requires = $matches[1];
		        }
		    }
		}

		// Gets the tested version of WP if available
		$matches = null;
		preg_match( "/tested:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches );
		if ( ! empty( $matches ) ) {
		    if ( is_array( $matches ) ) {
		        if ( count( $matches ) > 1 ) {
		            $response->tested = $matches[1];
		        }
		    }
		}

        return $response;
    }

    /**
     * Perform check before installation starts.
     *
     * @param  boolean $true
     * @param  array   $args
     * @return null
     */
    public function preInstall( $true, $args )
    {
        // Get plugin information
		$this->initPluginData();

		// Check if the plugin was installed before...
    	$this->pluginActivated = is_plugin_active( $this->slug );
    }

    /**
     * Perform additional actions to successfully install our plugin
     *
     * @param  boolean $true
     * @param  string $hook_extra
     * @param  object $result
     * @return object
     */
    public function postInstall( $true, $hook_extra, $result )
    {
		  global $wp_filesystem;

  		// Since we are hosted in GitHub, our plugin folder would have a dirname of
  		// reponame-tagname change it to our original one:
  		$pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname( $this->slug );
  		$wp_filesystem->move( $result['destination'], $pluginFolder );
  		$result['destination'] = $pluginFolder;
  
  		// Re-activate plugin if needed
  		if ( $this->pluginActivated )
  		{
		    $activate = activate_plugin( $this->slug );
  		}

      return $result;
    }
}
