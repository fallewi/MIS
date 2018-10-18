<?php
/**
 * Template Name: Fullwidth
 */
 ?>

<?php get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main" role="main">
    <div class="row ">
      <div class="columns large-12">

            <?php while ( have_posts() ) : the_post(); ?>

              <?php get_template_part( 'content', 'page' ); ?>

              <!-- <?php the_post_navigation(); ?> -->

              <?php
                // If comments are open or we have at least one comment, load up the comment template
                if ( comments_open() || get_comments_number() ) :
                  comments_template();
                endif;
              ?>

            <?php endwhile; // end of the loop. ?>

        </div><!-- columns -->

    </div><!-- row -->
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

