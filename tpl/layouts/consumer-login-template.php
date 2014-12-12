<?php get_header(); ?>
<div id="main-content" class="main-content">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
<?php
// use the_post to trigger loading the content page, otherwise the_content is empty
the_post(); ?>
<h1><?php the_title(); ?></h1>
<?php 
the_content(); 
$options = get_option('dekaagcrm_plugin_options');
the_widget('DeKaagCRM_Login', array('redirect_url' => '/geschiedenis'));  
?>
		</div>
	</div>
	<?php get_sidebar( 'content' ); ?>
</div>
<?php
get_sidebar();
get_footer();
?>