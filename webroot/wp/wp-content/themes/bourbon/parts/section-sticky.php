<?php
/**
 * Part: Sticky Section
 */
?>

<div class="row section__sticky">

  <?php
  $query = new WP_Query(array(
        'posts_per_page'=> 0,
        'post__in' => get_option( 'sticky_posts' )
        ));
  // while( $query->have_posts() ){
    $query->the_post(); ?>

            <?php
              /* Include the Post-Format-specific template for the content.
               * If you want to override this in a child theme, then include a file
               * called content-___.php (where ___ is the Post Format name) and that will be used instead.
               */

              get_template_part( 'content', get_post_format() );
            ?>


      <?php
    // }
      wp_reset_postdata(); ?>

</div>