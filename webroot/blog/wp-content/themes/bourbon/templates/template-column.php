<?php
/**
 * Template Name: Column
 */
 ?>

<?php get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main" role="main">
    <div class="row">
      <div class="columns large-9 large-centered">

            <?php while ( have_posts() ) : the_post(); ?>

              <?php get_template_part( 'content', 'page' ); ?>

            <?php endwhile; // end of the loop. ?>

      </div><!-- columns -->
    </div><!-- row -->
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
