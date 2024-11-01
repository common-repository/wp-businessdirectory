<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WNetwork
 */

get_header(); 
?>
	<div class="container">
		<div id="primary" class="col-lg-12 col-md-12">
			<main id="main" class="site-main content">
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="row">
						<div class="col-12">
							<?php the_content();?>
						</div>
					</div>
				<?php endwhile; // End of the loop.?>
			</main><!-- #main -->
		</div><!-- #primary -->
	</div>
<?php
get_footer();
