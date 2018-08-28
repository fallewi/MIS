<?php
/**
 * Template Name: Sidebar Right
 */
 ?>

<?php get_header(); ?>

<?php global $burocrate_bourbon; $sidebar_position = $burocrate_bourbon['sidebar-position']; ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main" role="main">

    <div class="row ">

      <div class="columns large-8">

        <?php while ( have_posts() ) : the_post(); ?>

          <?php get_template_part( 'content', 'page' ); ?>

        <?php endwhile; // end of the loop. ?>

      </div><!-- columns -->

      <?php get_sidebar(); ?>

    </div><!-- row -->
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
