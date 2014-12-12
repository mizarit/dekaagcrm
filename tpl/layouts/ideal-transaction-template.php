<?php get_header(); ?>

<div id="main-content" class="main-content">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
<?php
the_post();
echo '<h1>';
the_title();
echo '</h1>';
the_content();
$options = get_option('dekaagcrm_plugin_options');
the_widget('DeKaagCRM_Ideal', array('mollie_key' => $options['plugin_mollie_key'])); 
?>

		</div>
	</div>
	<?php get_sidebar( 'content' ); ?>
</div>
<?php
get_sidebar();
get_footer();
?>