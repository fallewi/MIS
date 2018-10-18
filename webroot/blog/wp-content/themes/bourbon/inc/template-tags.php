<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package bourbon
 */

if ( ! function_exists( 'bourbon_posts_navigation' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 */
function bourbon_posts_navigation() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation posts__navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Posts navigation', 'bourbon' ); ?></h2>
		<div class="nav__wrap">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav__older"><?php next_posts_link( __( 'Older posts', 'bourbon' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav__newer"><?php previous_posts_link( __( 'Newer posts', 'bourbon' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'bourbon_post_navigation' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function bourbon_post_navigation() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post__navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Post navigation', 'bourbon' ); ?></h2>
		<div class="nav__wrap">
			<?php
				previous_post_link( '%link', '<div class="nav__post--previous"><span class="nav__name">'. __( 'Previous', 'bourbon' ) .'</span>%title</div>' );
				next_post_link( '%link', '<div class="nav__post--next"><span class="nav__name">'. __( 'Next', 'bourbon' ) .'</span>%title</div>' );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'bourbon_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function bourbon_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		_x( '%s', 'post date', 'bourbon' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	global $burocrate_bourbon;
	if ( $burocrate_bourbon['human-date'] == '1' ) {
		$published = get_the_time( 'U' ); //get_the_time('F j')
		echo '<span class="posted-on"> ' . human_time_diff( $published ) . '</span>';
	} else {
		echo '<span class="posted-on">' . $posted_on . '</span>';
	}

}
endif;


/**
 * Author
 */
if ( ! function_exists( 'bourbon_author' ) ) :

	function bourbon_author( $avatar = NULL ) {
		$byline = sprintf(
			_x( '%s', 'post author', 'bourbon' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . $avatar . esc_html( get_the_author() ) . '</a></span>'
		);
		echo '<span class="byline hide-for-small-only"> ' . $byline . '</span>';
	}

endif;


/**
* Category link
*/
function bourbon_entry_category( $cat_count = NULL, $color = true ) {
	if ( 'post' == get_post_type() ) {
		$category = get_the_category();
		$result = ( $cat_count == NULL ? count( $category ) : $cat_count );
		$category = array_slice( $category , 0 , $result ); //Item count to view
		$result = count( $category );
		$i = 1;

		foreach ( $category as $catname ) {
			echo "<div class='category_link category_link-underline' style='border-color: " . ( ($color == true ) ? bourbon_get_category_color( $catname->cat_ID ) : 'transparent') . ";'><a href='" . esc_url( get_category_link( $catname->cat_ID ) ) . "'>" . esc_attr( $catname->cat_name ) . "</a></div>";
			if ( $i < $result ) {
					echo " &#183; ";
			}
			$i++;
		}

	}
}


/**
* Tags
*/
function bourbon_entry_tags() {
	if ( 'post' == get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( '', 'bourbon' ) );
		if ( $tags_list ) {
			print_r( $tags_list );
		}
	}
}

/**
* Comments
*/
function bourbon_entry_comments() {
	$num_comments = get_comments_number();
	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) && $num_comments > 1 ) {
		echo '<span class="comments-link">';
			comments_popup_link( __( '', 'bourbon' ), __( '1 <i class="fa fa-comment-o"></i>', 'bourbon' ), __( '% <i class="fa fa-comment-o"></i>', 'bourbon' ) );
		echo '</span>';
	}
}


if ( ! function_exists( 'bourbon_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function bourbon_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' == get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', 'bourbon' ) );
		if ( $categories_list && bourbon_categorized_blog() ) {
			printf( '<span class="cat-links">' . __( 'Posted in %1$s', 'bourbon' ) . '</span>', $categories_list );
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( ', ', 'bourbon' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . __( 'Tagged %1$s', 'bourbon' ) . '</span>', $tags_list );
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( __( 'Leave a comment', 'bourbon' ), __( '1 Comment', 'bourbon' ), __( '% Comments', 'bourbon' ) );
		echo '</span>';
	}

	edit_post_link( __( 'Edit', 'bourbon' ), '<span class="edit-link">', '</span>' );
}
endif;


if ( ! function_exists( 'bourbon_the_archive_description' ) ) :
/**
 * Shim for `the_archive_description()`.
 *
 * Display category, tag, or term description.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 *
 * @param string $before Optional. Content to prepend to the description. Default empty.
 * @param string $after  Optional. Content to append to the description. Default empty.
 */
function bourbon_the_archive_description( $before = '', $after = '' ) {
	$description = apply_filters( 'get_the_archive_description', term_description() );

	if ( ! empty( $description ) ) {
		/**
		 * Filter the archive description.
		 *
		 * @see term_description()
		 *
		 * @param string $description Archive description to be displayed.
		 */
		return $before . $description . $after ;
	}
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function bourbon_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'bourbon_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'bourbon_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so bourbon_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so bourbon_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in bourbon_categorized_blog.
 */
function bourbon_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'bourbon_categories' );
}
add_action( 'edit_category', 'bourbon_category_transient_flusher' );
add_action( 'save_post',     'bourbon_category_transient_flusher' );


/**
 * Post label
 */
function bourbon_get_post_label( $hot ) {
	$post_label = ( get_post_meta( $hot, 'post_label', true ) );
	if ($post_label !== '') {
		echo '<div class="hot"><i class="fa fa-flash"></i>' . esc_attr( $post_label ) . '</div>';
	}
}

/**
 * Post Color
 */
function bourbon_get_post_color( $id_post, $opacity = 0.93 ) {
	$post_color = get_post_meta( $id_post, 'post_color', true );
	$post_color_rgba = bourbon_hex2rgba( $post_color, $opacity);
	return $post_color_rgba;
}
/**
 * Page Hero Title
 */
function bourbon_get_page_hero_title( $post ) {
	$page_hero_title = get_post_meta( $post, 'page_hero_title', true );
	if ( $page_hero_title !== '' ) {
		echo esc_attr( $page_hero_title );
	}
}

/**
 * Page Hero Description
 */
function bourbon_get_page_hero_description( $post ) {
	$page_hero_description = get_post_meta( $post, 'page_hero_description', true );
	if ( $page_hero_description !== '' ) {
		echo esc_attr( $page_hero_description );
	}
}

/**
 * Page Hero Invert
 */
function bourbon_get_page_hero_inverse( $post ) {
	$page_hero_description = get_post_meta( $post, 'page_hero_inverse', true );
	if ( $page_hero_description == 'inverse' ) {
		echo esc_attr( $page_hero_description );
	}
}


/**
 * Page Hero Color
 */
function bourbon_get_page_hero_color( $post ) {
	$page_hero_color = get_post_meta( $post, 'page_hero_color', true );
	$page_hero_color_rgba = bourbon_hex2rgba( $page_hero_color, get_post_meta( $post, 'page_hero_opacity', true ) );
	echo esc_attr( $page_hero_color_rgba );
}


/**
 * Thumbnail src only
 */
function get_thumbnail_url_only( $size ){
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id() , $size ) ;
	echo esc_url( $thumbnail[0] );
}






/**
 * Color Functions
 */
function bourbon_getRandomColor() {
    $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
    $color = '#'.$rand[rand(0,7)].$rand[rand(0,7)].$rand[rand(0,7)].$rand[rand(0,7)].$rand[rand(0,7)].$rand[rand(0,7)];
    return bourbon_hex2rgba($color, .9);
}

function bourbon_hex2rgba($color, $opacity = false) {

	$default = 'rgba(30,30,30,.95)';

	//Return default if no color provided
	if(empty($color))
          return esc_attr( $default );

	//Sanitize $color if "#" is provided
        if ($color[0] == '#' ) {
        	$color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
                return esc_attr( $default );
        }

        //Convert hexadec to rgb
        $rgb =  array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity){
        	if(abs($opacity) > 1)
        		$opacity = 1.0;
        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        } else {
        	$output = 'rgb('.implode(",",$rgb).')';
        }

        //Return rgb(a) color string
        return $output;
}

/*******************************************
 * Extra fields
 *
********************************************/

		/**
		 * Get category image
		 */
		function bourbon_get_category_image( $cat_id ) {
			$cat_data = get_option("category_$cat_id");
			if ( isset($cat_data['image']) ){
				return $cat_data['image'];
			}
		}

		/**
		 * Get Category Color
		 */
		function bourbon_get_category_color( $cat_id ) {
			$cat_data = get_option("category_$cat_id");
			if ( isset($cat_data['color']) && $cat_data['color'] !== ''){
				return $cat_data['color'];
			} else {
				return;
			}
		}

		/**
		 * Get Category Hero Text Color
		 */
		function bourbon_get_hero_text_color( $cat_id ) {
			$cat_data = get_option("category_$cat_id");
			if ( isset($cat_data['text_color']) && $cat_data['text_color'] !== ''){
				return $cat_data['text_color'];
			} else {
				return;
			}
		}

		/**
		 * Get Category Hero Mask Color
		 */
		function bourbon_get_mask_color( $cat_id ) {
			$cat_data = get_option("category_$cat_id");
			$mask_color = $cat_data['category_mask_color'];
			$mask_opacity = $cat_data['category_mask_opacity'];
			if ( isset( $mask_color ) && $mask_color !== ''){
				return bourbon_hex2rgba( $mask_color, $mask_opacity );
			} else {
				return;
			}
		}

/*******************************************
* Gallery Slick Slider
********************************************/
if ( ! function_exists( 'soda_theme_gallery' ) ) :
/**
 * Prints HTML with gallery.
 */
function bourbon_theme_gallery() {
			global $post;
			$gallery = get_post_gallery( get_the_ID(), false );
			if( !isset( $gallery['ids'] )) return __( 'Nothing there', 'bourbon' );
			$ids     = explode( "," , $gallery['ids'] );
			$media   = get_attached_media( 'image' , $post->ID );

			echo "<div class='slick-gallery'>";
				$i=0;
				foreach ( $ids AS $id ) {
					$link  = wp_get_attachment_image_src ( $id , 'bourbon_thumbnail_xlarge');
					$alt_text = get_post_meta($id, '_wp_attachment_image_alt', true);
	 				echo "<div class='slick-gallery__image'><img src='" . esc_url( $link[0] ) . "' alt='". esc_attr( $alt_text ) . "' /></div>";
				}
			echo "</div>";
}
endif;

/*******************************************
 * Remove shortcode [gallery] from content
 ********************************************/
function bourbon_strip_shortcode_gallery( $content ) {
    preg_match_all( '/'. get_shortcode_regex() .'/s', $content, $matches, PREG_SET_ORDER );
    if ( ! empty( $matches ) ) {
        foreach ( $matches as $shortcode ) {
            if ( 'gallery' === $shortcode[2] ) {
                $pos = strpos( $content, $shortcode[0] );
                if ($pos !== false)
                    return substr_replace( $content, '', $pos, strlen($shortcode[0]) );
            }
        }
    }

    return $content;
}


/************************************************************
* Change output Gallery Shortcode to Foundation Lightbox
*************************************************************/
add_filter('post_gallery', 'bourbon_gallery_output', 10, 2);
function bourbon_gallery_output( $output, $attr ){

	if( !isset( $attr['ids'] )) return __( 'Nothing there', 'bourbon' );

	$columns = isset( $attr['columns'] ) ? $attr['columns'] : 3;
	$ids_arr = explode(',', $attr['ids'] );
	$ids_arr = array_map('trim', $ids_arr );
	$pictures = get_posts( array(
		'posts_per_page' => -1,
		'post__in'       => $ids_arr,
		'post_type'      => 'attachment',
		'orderby'        => 'post__in',
	) );

	if( ! $pictures ) return __( 'Nothing there', 'bourbon' );

	// Output
	$out = "<ul class='lightbox clearing-thumbs small-block-grid-$columns' data-clearing>";
	foreach( $pictures as $pic ){
		$src = $pic->guid;
		$t = esc_attr( $pic->post_title );
		$title = ( $t && false === strpos($src, $t)  ) ? $t : '';
		$caption = ( $pic->post_excerpt != '' ? $pic->post_excerpt : $title );
		$out .= '<li class="lightbox__item"><a href="'. esc_url( $src ) .'"><img src="'. esc_url( $src ) .'" alt="'. esc_attr( $title ) .'" /></a></li>';
			// If you want use caption add this code:
			// ( $caption ? "<span class='clearing__caption'>$caption</span>" : '' ) .
	}
	$out .= '</ul>';
	return $out;
}


/************
 * Share post
 ************/
if ( ! function_exists( 'bourbon_theme_post_share' ) ) :

function bourbon_theme_post_share() {
			global $post;
			global $burocrate_bourbon;

			$facebook = "<div class='entry__share--icon'><a target='_blank' href='" . esc_url( "http://www.facebook.com/sharer.php?u=". get_permalink( $post->ID ) . "&t=". get_the_title() ) . "' title='" . __( 'Click to share this post on Facebook' , 'bourbon' ) . "'><i class='fa fa-facebook'></i></a></div>";
	    $twitter  = "<div class='entry__share--icon'><a target='_blank' href='" . esc_url( "http://twitter.com/share?text=" ) . esc_attr( __( 'Currently reading ', 'bourbon' ) ) . esc_attr( get_the_title() ) . "&url=" . get_permalink( $post->ID ) . "' title='" . __( 'Click to share this post on Twitter' , 'bourbon' ) . "'><i class='fa fa-twitter'></i></a></div>";
	    $linkedin = "<div class='entry__share--icon'><a target='_blank' href='" . esc_url( "http://www.linkedin.com/shareArticle?mini=true&url=". get_permalink( $post->ID ) . "&title=" . get_the_title() . "&summary=&source=" . get_bloginfo('name') ) . "' title='" . __( 'Click to share this post on LinkedIn' , 'bourbon' ) . "'><i class='fa fa-linkedin'></i></a></div>";
	    $google   = "<div class='entry__share--icon'><a target='_blank' href='" . esc_url( "https://plus.google.com/share?url=". get_permalink( $post->ID ) ) . "' title='" . __( 'Click to share this post on Google Plus' , 'bourbon' ) . "'><i class='fa fa-google-plus'></i></a></div>";
	    $pinterest = "<div class='entry__share--icon'><a target='_blank' href='" . esc_url( "http://pinterest.com/pin/create/button/?url=" . get_permalink( $post->ID ) . "&media=" . wp_get_attachment_url( get_post_thumbnail_id( $post->ID) ) ) . esc_attr( "&description=" . get_the_title() . " on " . get_bloginfo('name') . " " ) . esc_url( site_url() ) . "'  title='" . __( 'Click to share this post on Pinterest' , 'bourbon' ) . "' class='pin-it-button' count-layout='horizontal'><i class='fa fa-pinterest-square'></i></a></div>";

			$output = '';
			foreach ( $burocrate_bourbon['share-icons'] as $key => $value ) {
				$output .= ( $value == '1' ) ? $$key : '';
			}

	    printf( '<div class="entry__share">%s</div>', $output );

}
endif;


/**
 * Masonry + Infinite Scroll
 */
function bourbon_infinite_masonry() {
	global $burocrate_bourbon;
	if ( $burocrate_bourbon['infinite-scroll'] == false ) {

		echo "<script>
		jQuery(window).bind('load', function () {
		    var container = document.querySelector('.masonry-container');
		    var msnry = new Masonry( container, {
		      itemSelector: '.masonry-item',
		    });
		});
		</script>";

	} else {

		echo "<script type='text/javascript'>
			jQuery(function(){
			      var container = jQuery('.masonry-container');
			      container.imagesLoaded(function(){
			        container.masonry({
			          itemSelector: '.masonry-item',
			        });
			    });
			    container.infinitescroll({
			    	loading: {
							img: '" . esc_url( get_template_directory_uri() . '/img/preloader.gif' ) . "',
				      msgText: '',
				      finishedMsg  : '<p>" . __( 'All loaded', 'bourbon' ) . "</p>',
							speed: 'fast',
			    	},
			      navSelector  : '.navigation',
			      nextSelector : '.navigation a',
			      itemSelector : '.masonry-item',
			      debug: false,
						animate: true,
			      errorCallback: function() {
			        jQuery('#infscr-loading').animate({opacity: .5},2000).fadeOut('normal');
			      }
			      },
			      function( newElements ) {
			        var newElems = jQuery( newElements );
			        newElems.imagesLoaded(function(){
			          container.masonry( 'appended', newElems, true );
			        });
			      }
			    );

			  });
			</script>";
	}
}


/**
 * Admin Category Media Select
 */
function bourbon_image_uploader_field( $name, $value = '', $w = 115, $h = 90) {
	$default = get_stylesheet_directory_uri() . '/img/default.png';
	if( $value ) {
		$image_attributes = wp_get_attachment_image_src( $value, array($w, $h) );
		$src = $value;
	} else {
		$src = $default;
	}
	echo '
	<div>
		<img data-src="' . esc_url( $default ) . '" src="' . esc_url( $src ) . '" width="' . esc_attr( $w ) . 'px" height="' . esc_attr( $h ) . 'px" />
		<div>
			<input type="text" name="' . $name . '" id="' . $name . '" value="' . esc_attr( $value ) . '"/>
			<button type="submit" class="upload_image_button button">' . __( 'Media Library', 'bourbon' ) . '</button>
			<button type="submit" class="remove_image_button button">&times;</button>
		</div>
		<p class="description">' . __( 'Select image or enter URL', 'bourbon' ) . '</p>
	</div>
	';
}

/**
 * Shortcodes
 */
function bourbon_extralink_shortcode( $atts, $content = null ) {
  extract(shortcode_atts(array(
				"url" => 'http://burocrate.com',
	), $atts));
   return '<a class="hero__extralink" href="' . esc_url( $url ) . '">' . esc_attr( $content ) . '</a>';
}
add_shortcode('extralink', 'bourbon_extralink_shortcode');
