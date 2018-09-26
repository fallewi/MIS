<?php
/**
 * Template Name: Homepage Hero & Rows
 */
 ?>
<?php get_header(); ?>
 <?php global $burocrate_bourbon; ?>

 <div class="section__hero" style="background-image: url(<?php echo esc_url( $burocrate_bourbon['hero-background-image']['url'] ); ?>);">

   <div class="hero__fill" style="background-color: <?php echo esc_attr( $burocrate_bourbon['hero-background-color']['rgba'] ); ?>;"></div>

   <div class="row">
     <div class="columns large-12">

         <header class="page__header <?php echo esc_attr( $burocrate_bourbon['hero-text-inverse'] )  ? 'inverse' : ''; ?>" >

             <!-- Hero logo -->
             <?php if ( $burocrate_bourbon['hero-logo']['url'] ): ?>
               <div class="hero__logo"><img src="<?php echo esc_url( $burocrate_bourbon['hero-logo']['url'] ); ?>"></div>
             <?php endif ?>

             <!-- Hero title -->
             <?php if ( $burocrate_bourbon['hero-title'] ) : ?>
               <h1 class="page__title"><?php echo esc_attr( $burocrate_bourbon['hero-title'] ); ?></h1>
             <?php endif ?>

             <div class="page__description"><?php echo wp_kses( $burocrate_bourbon['hero-description'], 'default'); ?>
               <br><a href="<?php echo esc_url( $burocrate_bourbon['hero-extralink-url'] ); ?>" class="extralink"><?php echo esc_attr( $burocrate_bourbon['hero-extralink-title'] ); ?></a>
             </div>
         </header><!-- .page-header -->

     </div> <!-- columns -->
   </div> <!-- row -->
 </div>




<?php  if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
    elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
    else { $paged = 1; }
    $wp_query = new WP_Query( array(
          'post_type' => 'post',
          'posts_per_page' => 6,
          'paged' => $paged,
    )); ?>

<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

					<?php if ( $wp_query->have_posts() ) : ?>
					<div class="row ">
						<div class="columns large-9 large-centered">
							<div class="section_rows">

								<?php /* Start the Loop */ ?>
								<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

									<div class="rows">
						          <?php if ( has_post_thumbnail() ) { ?>
						            <?php echo '<a href=' . get_permalink( $post->ID ) . '>'; ?>
						                <div class="rows__thumbnail hide-for-small-only" style="background-image: url('<?php get_thumbnail_url_only( 'bourbon_thumbnail_medium' ); ?>'); ">
						                </div>
						            <?php echo '</a>'; ?>
						          <?php }  ?>

						        <div class="rows__wrap">
						          <?php the_title( sprintf( '<h2 class="news__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
						          <div class="rows__content hide-for-small-only"><?php the_excerpt(); ?></div>

						          <div class="rows__meta">
						            <div class="rows__author"><?php printf('%s<span class="rows__author--in">in</span>', bourbon_author() )?></div>
						            <div class="rows__category"><?php bourbon_entry_category(); ?></div>
						            <div class="rows__posted_on right "><?php if ( 'post' == get_post_type() ) bourbon_posted_on(); ?></div>
						          </div>

						        </div>
						      </div>

						<?php endwhile; ?>
						</div>
					</div><!-- columns -->
				</div><!-- row -->

					<div class="row">
						<div class="columns large-9 large-centered">
							<?php global $burocrate_bourbon;
							if ( $burocrate_bourbon['pagination'] == '1' ) {
						  	the_posts_pagination();
						  } else {
						  	bourbon_posts_navigation();
						  } ?>
						</div>
					</div>


					<?php else : ?>

						<?php get_template_part( 'content', 'none' ); ?>

					<?php endif; ?>

	</main><!-- #main -->
</div><!-- #primary -->


<?php get_footer(); ?>
