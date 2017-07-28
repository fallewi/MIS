<?php

/****************************************************************
* Tag Cloud Widget Settings
*****************************************************************/
add_filter('widget_tag_cloud_args','bourbon_set_tag_cloud_args');
function bourbon_set_tag_cloud_args( $args ) {
    $args['number'] = 30;
    $args['largest'] = 0.8;
    $args['smallest'] = 0.8;
    $args['unit'] = 'rem';
    return $args;
}


/****************************************************************
* bourbon Recent Posts Widget
*****************************************************************/
class Bourbon_Recent_Posts_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'bourbon_picture_posts_widget',
            'bourbon Recent Posts',
            array( 'description' =>  __( 'Display recent posts with thumbnails', 'bourbon' ) )
        );
    }

    public function widget( $args, $instance ) {


        if ( $instance['thumbnail'] == 'on' ) {
            $thumbnail = '_thumbnail_id';
        } else {
            $thumbnail = '';
        }

        $query = array( 'posts_per_page' => $instance['posts_per_page'],
                        'post_status'    => 'publish',
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                        'ignore_sticky_posts' => true,
                        );

        $wp_query = new WP_Query( $query );

        if( $wp_query->have_posts() ):

            printf( '%s<ul class="pp__list">', $args['before_widget'] );

            $title = apply_filters( 'widget_title', $instance['title'] );
            if ( ! empty( $title ) ) {
                // $args is a WordPress array, no need validate
                echo $args['before_title'] . esc_attr( $title ) . $args['after_title'];
            }

            while( $wp_query->have_posts() ): $wp_query->the_post();

                echo '<li class="pp__item">';

                ?> <h3 class="pp__header"><a href="<?php echo esc_url( the_permalink() ); ?>"><?php echo esc_attr( the_title() ); ?></a></h3> <?php
                echo '<div class="pp__content">';
                  the_excerpt();
                echo '</div>';
                ?>
                <footer class="pp__footer">
                  <div class="pp__author"><?php printf('%s<span class="pp__author--in">in</span>', bourbon_author() )?></div>
                    <div class="pp__category"><?php bourbon_entry_category(1); ?></div>
                  <div class="pp__date right"><?php if ( 'post' == get_post_type() ) bourbon_posted_on(); ?></div>
                </footer>
                <?php

                echo '</li>';

            endwhile;
            printf( '</ul>%s', $args['after_widget'] );

        endif;

        wp_reset_postdata();
    }

    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) )
            $title = $instance[ 'title' ];

        if ( isset( $instance[ 'posts_per_page' ] ) )
            $posts_per_page = $instance[ 'posts_per_page' ];

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'bourbon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php _e( 'Limit', 'bourbon' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="text" value="<?php echo ($posts_per_page) ? esc_attr( $posts_per_page ) : '5'; ?>" size="3" />
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['posts_per_page'] = ( is_numeric( $new_instance['posts_per_page'] ) ) ? $new_instance['posts_per_page'] : '5'; // по умолчанию выводятся 5 постов
        $instance['thumbnail'] = $new_instance['thumbnail'];
        return $instance;
    }
}

function bourbon_recent_posts_widget_load() {
    register_widget( 'Bourbon_Recent_Posts_Widget' );
}
add_action( 'widgets_init', 'bourbon_recent_posts_widget_load' );


/****************************************************************
* bourbon Instagram Widget
*
*****************************************************************/
use Bourbon\Instagram\Instagram;

class Bourbon_Instagram_Widget extends WP_Widget
{

    public function __construct() {
        parent::__construct(
            'bourbon_instagram_widget',
            'bourbon Instagram Widget',
            array( 'description' => __( 'Show Instagram recent photos', 'bourbon' ) )
        );
    }

    public function form( $instance ) {
        $title = '';
        $user_id = '';
        $client_id = '';
        $limit = 6;
        $background = "";

        if ( isset( $instance['client_id'] ) ) {
            $client_id = $instance['client_id'];
        }

        if ( isset( $instance['user_id'] ) ) {
            $user_id = $instance['user_id'];
        }

        if ( isset( $instance['limit'] ) ) {
            $limit = $instance['limit'];
        }

        if ( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        }

        if ( isset( $instance['background'] ) ) {
            $background = $instance['background'];
        }

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'bourbon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'user_id' ); ?>"><?php _e( 'User ID* ', 'bourbon' ); ?>(<a href="http://jelled.com/instagram/lookup-user-id"><?php _e( 'Look here', 'bourbon' ); ?></a>)</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'user_id' ); ?>" name="<?php echo $this->get_field_name( 'user_id' ); ?>" type="text" value="<?php echo esc_attr( $user_id ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'client_id' ); ?>">CLIENT ID* (<a href="http://instagram.com/developer/clients/manage/"><?php _e( 'Look here', 'bourbon' ); ?></a>)</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'client_id' ); ?>" name="<?php echo $this->get_field_name( 'client_id' ); ?>" type="text" value="<?php echo esc_attr( $client_id ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit', 'bourbon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'background' ); ?>"><?php _e( 'Background (CSS)', 'bourbon' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'background' ); ?>" name="<?php echo $this->get_field_name( 'background' ); ?>" type="text" value="<?php echo esc_attr( $background ); ?>" />
        </p>
        <?php

    }


    public function update( $newInstance, $oldInstance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $newInstance['title'] ) ) ? strip_tags( $newInstance['title'] ) : '';
        $instance['client_id'] = ( ! empty( $newInstance['client_id'] ) ) ? strip_tags( $newInstance['client_id'] ) : '';
        $instance['user_id'] = ( ! empty( $newInstance['user_id'] ) ) ? strip_tags( $newInstance['user_id'] ) : '';
        $instance['background'] = ( ! empty( $newInstance['background'] ) ) ? strip_tags( $newInstance['background'] ) : '';
        $instance['limit'] = ( is_numeric( $newInstance['limit'] ) ) ? $newInstance['limit'] : '6'; //Default limit = 6
        return $instance;
    }


    public function widget( $args, $instance ) {
        $title = $instance['title'];
        $limit = $instance['limit'];
        $user_id = $instance['user_id'];
        $client_id = $instance['client_id'];
        $background = $instance['background'];
        $size = '50%';


        require get_template_directory() . '/inc/' . sanitize_file_name( 'Instagram.php' );

        $instagram = new Instagram ( $client_id );
        $result = $instagram->getUserMedia( $user_id, $limit );

        if ( $result ) {

            printf( "%s<div class='instagram__wrap' style='background:" . esc_attr( $background ) . "'>", $args['before_widget'] );
            if ( ! empty( $title ) )  echo $args['before_title'] . esc_attr( $title ) . $args['after_title'];
            foreach( $result->data as $media ) {
                echo '<div class="image_instagram"><a href="' . esc_url( $media->link ) . '" target="_blank">';
                echo "<img src='" . esc_url( $media->images->low_resolution->url ) . "' />";
                echo '</a><a href="' . esc_url( $media->link ) . '" target="_blank"><div class="image_popup"></div></a></div>';
            }
            printf("</div>%s", $args['after_widget'] ) ;

        }
    }

}
add_action( 'widgets_init', function () {
    register_widget( 'Bourbon_Instagram_Widget' );
});


/****************************************************************
* bourbon Social Widget
*
*****************************************************************/
class Bourbon_Social_Widget extends WP_Widget
{

    public function __construct() {
        parent::__construct(
            'bourbon_social_widget',
            'bourbon Social Widget',
            array( 'description' => __( 'A simple widget to show social icons', 'bourbon' ) )
        );
    }

    public function form( $instance ) {
        $linker = array(
            'twitter'   => '',
            'facebook'  => '',
            'linkedin'  => '',
            'instagram' => '',
            'google-plus' => '',
            'behance' => '',
            );

        if ( ! empty( $instance ) ) {
            foreach ( $linker as $key => $value) {
                $linker[$key] = $instance[$key];
            }
        }

        echo '<p>' . __( 'Full path (with', 'bourbon') . ' http://...)</p><br>';

        foreach ($linker as $key => $value) {
            $linkerid[$key] = $this->get_field_id($key);
            $linkername[$key] = $this->get_field_name($key);
            echo '<label for="' . esc_attr( $linkerid[$key] ) . '">' . esc_attr( $key ) . '</label><br>';
            echo '<input id="' . esc_attr( $linkerid[$key] ) . '" name="' . esc_attr( $linkername[$key] ) . '"  value="' . esc_url( $value ) . '" /><br>';
        }

    }

    public function update($newInstance, $oldInstance) {
        $instance = array(
            'twitter'   => '',
            'facebook'  => '',
            'linkedin'  => '',
            'instagram' => '',
            'google-plus' => '',
            'behance' => '',
            );
        foreach ($instance as $key => $value ) {
            $instance[$key] = htmlentities($newInstance[$key]);
        }
        return $instance;
    }


    public function widget($args, $instance) {

        echo $args['before_widget']; // $args is a WordPress array, no need validate

        foreach ($instance as $key => $value) {
            if ( "" != $value ) {
                echo "<a class='widget__social__link' href='". esc_url( $value ) . "'><i class='fa fa-". esc_attr( $key ) . "'></i></a>";
            }
        }

        echo $args['after_widget']; // $args is a WordPress array, no need validate
    }
}
add_action("widgets_init", function () {
    register_widget("Bourbon_Social_Widget");
});

/****************************************************************
* bourbon Ads Widget
*
*****************************************************************/
class Bourbon_Advert_Widget extends WP_Widget
{

    public function __construct() {
        parent::__construct(
            'bourbon_advert_widget',
            'bourbon Advert Widget',
            array( 'description' => __( 'Advertise widget', 'bourbon' ) )
        );
    }

    public function form( $instance ) {
        $image = "";
        $link  = "";

        if ( !empty( $instance ) ) {
            $image = $instance["image"];
            $link = $instance["link"];
        }

        $imageId = $this->get_field_id("image");
        $imageName = $this->get_field_name("image");
        echo '<label for="' . esc_attr( $imageId ) . '">' . __( 'Image Url', 'bourbon' ) . '</label><br>';
        echo '<input id="' . esc_attr( $imageId ) . '" type="text" name="' . esc_attr( $imageName ). '" value="' . esc_url( $image ) . '"><br>';

        $linkId = $this->get_field_id("link");
        $linkName = $this->get_field_name("link");
        echo '<label for="' . esc_attr( $linkId ) . '">' . __( 'Link', 'bourbon' ) . '</label><br>';
        echo '<textarea id="' . esc_attr( $linkId ) . '" name="' . esc_attr( $linkName ). '">' . esc_url( $link ) . '</textarea>';
    }

    public function update( $newInstance, $oldInstance ) {
        $values = array();
        $values["image"] = htmlentities( $newInstance["image"] );
        $values["link"] = htmlentities( $newInstance["link"] );
        return $values;
    }


    public function widget($args, $instance) {

        echo $args['before_widget'];// $args is a WordPress array, no need validate

        $image = $instance["image"];
        $link = $instance["link"];
        ?>

        <div class="advert_wrap">
            <a href="<?php echo esc_url( $link ); ?>">
                <img src="<?php echo esc_url( $image ); ?>" />
            </a>
        </div>

        <?php
        echo $args['after_widget'];// $args is a WordPress array, no need validate
    }
}
add_action("widgets_init", function () {
    register_widget("Bourbon_Advert_Widget");
});
