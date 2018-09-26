<?php
/**
 * Template Name: Column & Hero
 */
 ?>

<?php get_header(); ?>

<div class="page__hero" style="background-image: url('<?php get_thumbnail_url_only( 'bourbon_thumbnail_xlarge' ); ?>'); ">
<div class="page__fill" style="background-color: <?php bourbon_get_page_hero_color( $post->ID ); ?>;"></div>
  <div class="row ">
    <div class="columns large-12">
      <header class="page__header <?php bourbon_get_page_hero_inverse( $post->ID ); ?>">
          <h1 class="page__title"><?php bourbon_get_page_hero_title( $post->ID ); ?></h1>
          <div class="page__description"><?php bourbon_get_page_hero_description( $post->ID ); ?></div>
      </header><!-- .page-header -->
    </div>
  </div>
</div>

<div id="primary" class="content-area">
  <main id="main" class="site-main" role="main">

    <div class="row">
      <div class="columns large-9 large-centered">

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
