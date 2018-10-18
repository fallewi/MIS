<?php
/**
 * Template Name: Homepage Hero & 2-Grid
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



 <!-- 2 Grid Masonry -->
 <div id="primary" class="content-area">
 	<main id="main" class="site-main" role="main">
    <?php  if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
        elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
        else { $paged = 1; }
        $wp_query = new WP_Query( array(
              'post_type' => 'post',
              'posts_per_page' => 6,
              'paged' => $paged,
        )); ?>

 		<?php if ( $wp_query->have_posts() ) : ?>

 			<div class="row  masonry-container grid__template--2">
 				<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

 					<div class="columns medium-6 masonry-item">
 						<?php if ( get_post_format() == 'quote' ): ?>
 							<?php get_template_part( 'content', 'quote' ); ?>
 						<?php elseif ( get_post_format() == 'image' ): ?>
 							<?php get_template_part( 'content', 'image' ); ?>
 						<?php else: ?>
 							<?php get_template_part( 'content', 'grid' ); ?>
 						<?php endif; ?>
 					</div><!-- columns -->

 				<?php endwhile; ?>
 			</div><!-- row -->

 			<div class="row">
 				<nav class="columns large-12 navigation post-navigation">

 					<?php global $burocrate_bourbon;
 					if ( $burocrate_bourbon['pagination'] == '1' AND !wp_is_mobile() ) {
 				  	the_posts_pagination();
 				  } else {
 				  	bourbon_posts_navigation();
 				  } ?>

 				</nav>
 			</div>

 		<?php else : ?>

 			<?php get_template_part( 'content', 'none' ); ?>

 		<?php endif; ?>

 	</main><!-- #main -->
 </div><!-- #primary -->

 <!-- Infinite scroll + Masonry function -->
 <?php bourbon_infinite_masonry(); ?>

<?php get_footer(); ?>
