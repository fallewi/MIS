<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package bourbon
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function bourbon_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'bourbon_body_classes' );

if ( version_compare( $GLOBALS['wp_version'], '4.1', '<' ) ) :
	/**
	 * Filters wp_title to print a neat <title> tag based on what is being viewed.
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 * @return string The filtered title.
	 */
	function bourbon_wp_title( $title, $sep ) {
		if ( is_feed() ) {
			return $title;
		}

		global $page, $paged;

		// Add the blog name
		$title .= get_bloginfo( 'name', 'display' );

		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title .= " $sep $site_description";
		}

		// Add a page number if necessary:
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( __( 'Page %s', 'bourbon' ), max( $paged, $page ) );
		}

		return $title;
	}
	add_filter( 'wp_title', 'bourbon_wp_title', 10, 2 );

endif;


/*******************************************
* Navigation
********************************************/
class bourbon_walker extends Walker_Nav_Menu {

    function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
        $element->has_children = !empty( $children_elements[$element->ID] );
        $element->classes[] = ( $element->current || $element->current_item_ancestor ) ? 'active' : '';
        $element->classes[] = ( $element->has_children && $max_depth !== 1 ) ? 'has-dropdown' : '';

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

    function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
        $item_html = '';
        parent::start_el( $item_html, $object, $depth, $args );

        $output .= ( $depth == 0 ) ? '' : ''; //To create divider place in '' this <li class="divider"></li>

        $classes = empty( $object->classes ) ? array() : (array) $object->classes;

        if( in_array('label', $classes) ) {
            $output .= '<li class="divider"></li>';
            $item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU', '<label>$1</label>', $item_html );
        }

    if ( in_array('divider', $classes) ) {
        $item_html = preg_replace( '/<a[^>]*>( .* )<\/a>/iU', '', $item_html );
    }

        $output .= $item_html;
    }

    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $output .= "\n<ul class=\"sub-menu dropdown\">\n";
    }

}




/*******************************************
 * Excerpt
 ******************************************/
function bourbon_excerpt_length( $length ) {
    return 35;
}
add_filter( 'excerpt_length', 'bourbon_excerpt_length', 10, 1);

/* Excerpt symbol */
function bourbon_excerpt_more($more) {
    global $post;
    return '...';
}
add_filter('excerpt_more', 'bourbon_excerpt_more');


/*******************************************
* Custom archive title
********************************************/
function bourbon_get_the_archive_title() {
    if ( is_category() ) {
        $title = sprintf( __( '<h1 class="category__title">%s</h1>' ), single_cat_title( '', false ) );
    } elseif ( is_tag() ) {
        $title = sprintf( __( '<h2 class="archive__name">Tag</h2><h1 class="archive__title">%s</h1>' ), single_tag_title( '', false ) );
    } elseif ( is_author() ) {
        $title = sprintf( __( '<h2 class="archive__name">Author</h2><h1 class="archive__title">%s</h1>' ), get_the_author()  );
    } elseif ( is_year() ) {
        $title = sprintf( __( '<h2 class="archive__name">Year</h2><h1 class="archive__title">%s</h1>' ), get_the_date( _x( 'Y', 'yearly archives date format', 'bourbon' ) )  );
    } elseif ( is_month() ) {
        $title = sprintf( __( '<h2 class="archive__name">Month</h2><h1 class="archive__title">%s</h1>' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'bourbon' ) )  );
    } elseif ( is_day() ) {
        $title = sprintf( __( '<h2 class="archive__name">Day</h2><h1 class="archive__title">%s</h1>' ), get_the_date( _x( 'F j, Y', 'daily archives date format', 'bourbon' ) ) );
    } elseif ( is_tax( 'post_format' ) ) {
        if ( is_tax( 'post_format', 'post-format-aside' ) ) {
            $title = _x( '<h3 class="page__subtitle">Type</h3><h1 class="archive__title">Asides</h1>', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
            $title = _x( '<h3 class="page__subtitle">Type</h3><h1 class="archive__title">Galleries</h1>', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
            $title = _x( '<h3 class="page__subtitle">Type</h3><h1 class="archive__title">Images</h1>', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
            $title = _x( '<h3 class="page__subtitle">Type</h3><h1 class="archive__title">Videos</h1>', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
            $title = _x( '<h3 class="page__subtitle">Type</h3><h1 class="archive__title">Quotes</h1>', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
            $title = _x( '<h3 class="page__subtitle">Type</h3><h1 class="archive__title">Links</h1>', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
            $title = _x( '<h3 class="page__subtitle">Type</h3><h1 class="archive__title">Statuses</h1>', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
            $title = _x( '<h3 class="page__subtitle">Type</h3><h1 class="archive__title">Audio</h1>', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
            $title = _x( '<h3 class="page__subtitle">Type</h3><h1 class="archive__title">Chats</h1>', 'post format archive title' );
        }
    } elseif ( is_post_type_archive() ) {
        $title = sprintf( __( 'Archives: %s', 'bourbon' ), post_type_archive_title( '', false ) );
    } elseif ( is_tax() ) {
        $tax = get_taxonomy( get_queried_object()->taxonomy );
        /* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
        $title = sprintf( __( '%1$s: %2$s', 'bourbon' ), $tax->labels->singular_name, single_term_title( '', false ) );
    } else {
        $title = __( 'Archives', 'bourbon'  );
    }

    /**
     * Filter the archive title.
     *
     * @since 4.1.0
     *
     * @param string $title Archive title to be displayed.
     */
    return apply_filters( 'get_the_archive_title', $title );

}

/*******************************************
* Remove <iframe> from content
********************************************/
function bourbon_content_without_iframe() {
  $content = get_the_content( sprintf(
        __( 'Continue %s <i class="fa fa-angle-right"></i>', 'soda-theme' ),
        the_title( '<span class="screen-reader-text">"', '"</span>', false )
      ) );
  $content = apply_filters( 'the_content' , $content );
  echo preg_replace( '~<iframe[^\>]*?\>(.*)</iframe>~', '', $content );
}

/*******************************************
* Extract <iframe> and add filter to the_content
********************************************/
function bourbon_content_extract_iframe() {
  $before_iframe = "";
  $pattern = '~<iframe[^\>]*?\>(.*)</iframe>~';
  $content = apply_filters( 'the_content' , get_the_content() );
  $content = str_replace(']]>' , ']]&gt;' , $content);
  preg_match_all( $pattern, $content, $extracted_iframe );
    if ( isset( $extracted_iframe[0][0] ) ) {
         echo esc_attr( $before_iframe ) . $extracted_iframe[0][0] ;
    };
}

/*******************************************
* SVG Images MIME types
********************************************/
function bourbon_cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'bourbon_cc_mime_types');


/*******************************************
* POST Extra Fields
*
********************************************/
add_action('add_meta_boxes', 'bourbon_extra_fields', 1);

function bourbon_extra_fields() {
    add_meta_box( 'extra_fields_post', 'Additional Fields', 'bourbon_extra_fields_box_func', 'post', 'normal', 'high'  );
    add_meta_box( 'extra_fields_page', 'Hero Options', 'bourbon_extra_fields_box_page_func', 'page', 'normal', 'high'  );
}

function bourbon_extra_fields_box_func( $post ){
    ?>
<style type="text/css" media="screen">
    .w100 {width: 100%; }
    .w30 {width: 30%; }
    .vatop {vertical-align: top; }
</style>
    <table class="w100">
        <tbody class="vatop">
                <tr>
                        <td class="w30">
                            <!-- Label field -->
                            <label><h4><?php _e( 'Post label:', 'bourbon' ) ?></h4>
                                <input type="text" name="extra[post_label]" value="<?php echo get_post_meta($post->ID, 'post_label', true); ?>"/>
                                <p class="howto"><?php _e( 'Example: HOT', 'bourbon' ) ?></p>
                            </label>
                        </td>

                        <td class="w30">
                            <!-- Color picker -->
                            <?php wp_enqueue_script('wp-color-picker'); wp_enqueue_style( 'wp-color-picker' ); ?>
                            <label><h4><?php _e( 'Post color:', 'bourbon' ) ?></h4>
                                <input name="extra[post_color]" type="text" id="post_custom_color" value="<?php echo get_post_meta($post->ID, 'post_color', true); ?>" data-default-color="#ffffff">
                                <p class="howto"><?php _e( 'The background color of the post. Shows in the Best Section', 'bourbon' ) ?></p>
                            </label>
                            <script type="text/javascript">
                                jQuery(document).ready(function($) {
                                    $('#post_custom_color').wpColorPicker({
                                        palettes: ['#FFC107', '#FFA000', '#FFECB3', '#607D8B', '#4CAF50', '#388E3C', '#C8E6C9', '#FF5722', '#303F9F', '#7C4DFF', '#7B1FA2', '#00BCD4', '#AFB42B', '#795548', '#1976D2', '#FF5252']
                                    });
                                });
                            </script>
                        </td>

                        <td class="w30">
                            <!-- Best Post -->
                            <label><h4><?php _e( 'Best section:', 'bourbon') ?></h4> <?php $mark_v = get_post_meta( $post->ID, 'post_best', true ); ?>
                                 <input type="radio" name="extra[post_best]" value="" <?php checked( $mark_v, '' ); ?> /> <?php _e( 'Default', 'bourbon' ) ?><br>
                                 <input type="radio" name="extra[post_best]" value="best" <?php checked( $mark_v, 'best' ); ?> /> <?php _e( 'Best Post', 'bourbon') ?>
                                 <p class="howto"><?php _e( 'Shows the post in the Best Slider', 'bourbon' ) ?></p>
                            </label>
                        </td>

                        <!-- Hidden Field for Check Save -->
                        <input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
                </tr>
        </tbody>
    </table>
    <?php
}

/********************************
 * PAGE Extra Fields
 *
 ********************************/
function bourbon_extra_fields_box_page_func( $post ){
    ?>
        <!-- Label field -->
        <label>
        <p><?php _e( 'This Section shows only in Templates with Hero', 'bourbon' ) ?></p>
        <h4><?php _e( 'Title', 'bourbon' ) ?></h4>
            <input type="text" name="extra[page_hero_title]" value="<?php echo esc_attr( get_post_meta($post->ID, 'page_hero_title', true) ) ?>"/><br>
            <h4><?php _e( 'Description', 'bourbon' ) ?></h4>
            <textarea type="textarea" cols="80" rows="4" name="extra[page_hero_description]"><?php echo esc_textarea( get_post_meta($post->ID, 'page_hero_description', true) ); ?></textarea>
        </label>

        <label><h4><?php _e( 'Hero text:', 'bourbon' ) ?></h4>
        <?php $mark_v = get_post_meta( $post->ID, 'page_hero_inverse', true ); ?>
             <input type="radio" name="extra[page_hero_inverse]" value="" <?php checked( $mark_v, '' ); ?> /><?php _e( ' Default', 'bourbon' ) ?><br>
             <input type="radio" name="extra[page_hero_inverse]" value="inverse" <?php checked( $mark_v, 'inverse' ); ?> /><?php _e( ' Inverse', 'bourbon' ) ?>
        </label>

        <?php wp_enqueue_script('wp-color-picker'); wp_enqueue_style( 'wp-color-picker' ); ?>
        <label><h4><?php _e( 'Hero color:', 'bourbon' ) ?></h4>
            <input name="extra[page_hero_color]" type="text" id="page_custom_color" value="<?php echo esc_attr( get_post_meta($post->ID, 'page_hero_color', true) ); ?>" data-default-color="#ffffff">
            <p class="howto"><?php _e( 'The background color of the Hero', 'bourbon' ) ?></p>
        </label>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#page_custom_color').wpColorPicker({
                    palettes: ['#FFC107', '#FFA000', '#FFECB3', '#607D8B', '#4CAF50', '#388E3C', '#C8E6C9', '#FF5722', '#303F9F', '#7C4DFF', '#7B1FA2', '#00BCD4', '#AFB42B', '#795548', '#1976D2', '#FF5252']
                });
            });
        </script>

        <label><h4><?php _e( 'Hero opacity (ex: .5):', 'bourbon' ) ?></h4>
        <input type="text" name="extra[page_hero_opacity]" value="<?php echo esc_attr( get_post_meta( $post->ID, 'page_hero_opacity', true) ); ?>"/></label><br>


        <!-- Hidden Field for Check Save -->
        <input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
    <?php
}


/**
*  <?php echo get_post_meta($post->ID, 'post_color', true) ?>
*/

add_action('save_post', 'bourbon_extra_fields_update', 0);
function bourbon_extra_fields_update( $post_id ){
    if ( !wp_verify_nonce( $_POST['extra_fields_nonce'], __FILE__) ) return false;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE  ) return false;
    if ( !current_user_can( 'edit_post', $post_id ) ) return false;
    if ( !isset( $_POST['extra'] ) ) return false;

    $_POST['extra'] = array_map('trim', $_POST['extra']);
    foreach( $_POST['extra'] as $key=>$value ){
        if( empty( $value ) ){
            delete_post_meta($post_id, $key);
            continue;
        }
        update_post_meta( $post_id, $key, $value );
    }
    return $post_id;
}


/*******************************************
* Extra Fields Category
*
********************************************/
add_action ( 'edit_category_form_fields', 'bourbon_category_fields');
// Add extra fields to category edit form callback function
function bourbon_category_fields( $tag ) {    // Check for existing featured ID
    $t_id = $tag->term_id;
    $cat_meta = get_option( "category_$t_id" );
    ?>

    <!-- Category Color -->
    <tr class="form-fielda">
        <?php wp_enqueue_script('wp-color-picker'); wp_enqueue_style( 'wp-color-picker' ); ?>
        <th scope="row" valign="top"><label><?php _e('Title color', 'bourbon'); ?></label></th>
        <td>
            <input name="Cat_meta[color]" type="text" id="category_color" value="<?php echo esc_attr( $cat_meta['color'] ) ? esc_attr( $cat_meta['color'] ) : ''; ?>" data-default-color=""><br>
            <p class="description"><?php _e('Select category link color and Hero Line color', 'bourbon'); ?></p>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('#category_color').wpColorPicker({
                        palettes: ['#FFC107', '#FFA000', '#FFECB3', '#607D8B', '#4CAF50', '#388E3C', '#C8E6C9', '#FF5722', '#303F9F', '#7C4DFF', '#7B1FA2', '#00BCD4', '#AFB42B', '#795548', '#1976D2', '#FF5252']
                    });
                });
            </script>
        </td>
     </tr>

    <!-- Hero Image -->
    <tr class="form-fielda">
        <th scope="row" valign="top"><label for="cat_Template_url"><?php _e('Category image', 'bourbon'); ?></label></th>
        <td>
            <?php if( function_exists( 'bourbon_image_uploader_field' ) ) { bourbon_image_uploader_field( 'Cat_meta[image]', $cat_meta['image'] ); } ?>
        </td>
    </tr>

    <!-- Hero Text color -->
    <tr class="form-fielda">
        <th scope="row" valign="top"><label for="text_color"><?php _e('Category Text', 'bourbon'); ?></label></th>
        <td>
            <fieldset><label><input type="radio" name="Cat_meta[text_color]" value="" <?php checked( $cat_meta['text_color'], '' ); ?> /><?php _e( ' Black', 'bourbon' ) ?></label><br/>
                <label><input type="radio" name="Cat_meta[text_color]" value="inverse" <?php checked( $cat_meta['text_color'], 'inverse' ); ?> /><?php _e( ' White', 'bourbon' ) ?></label><br/>
                        </fieldset></td>
    </tr>

    <!-- Hero Mask Color -->
    <tr class="form-fielda">

        <th scope="row" valign="top"><label><?php _e('Background Color', 'bourbon'); ?></label></th>
        <td>
            <input name="Cat_meta[category_mask_color]" type="text" id="category_mask_color" value="<?php echo esc_attr( $cat_meta['category_mask_color'] ) ? esc_attr( $cat_meta['category_mask_color'] ) : ''; ?>" data-default-color=""><br>
            <input name="Cat_meta[category_mask_opacity]" type="number" step=".1" min="0" max="1" class="small-number" id="category_mask_opacity" value="<?php echo esc_attr( $cat_meta['category_mask_opacity'] ) ? esc_attr( $cat_meta['category_mask_opacity'] ) : '0.5'; ?>" ><?php _e( ' Opacity', 'bourbon' ) ?><br>
            <p class="description"><?php _e('Select Hero Mask color and change opacity', 'bourbon'); ?></p>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('#category_mask_color').wpColorPicker({
                        palettes: ['#FFC107', '#FFA000', '#FFECB3', '#607D8B', '#4CAF50', '#388E3C', '#C8E6C9', '#FF5722', '#303F9F', '#7C4DFF', '#7B1FA2', '#00BCD4', '#AFB42B', '#795548', '#1976D2', '#FF5252']
                    });
                });
            </script>
        </td>
     </tr>

    <?php }


add_action ( 'edited_category', 'bourbon_save_category_fileds');
function bourbon_save_category_fileds( $term_id ) {
    if ( isset( $_POST['Cat_meta'] ) ) {
        $t_id = $term_id;
        $cat_meta = get_option( "category_$t_id");
        $cat_keys = array_keys($_POST['Cat_meta']);
            foreach ( $cat_keys as $key ){
            if (isset($_POST['Cat_meta'][$key])){
                $cat_meta[$key] = $_POST['Cat_meta'][$key];
            }
        }
        update_option( "category_$t_id", $cat_meta );
    }
}
