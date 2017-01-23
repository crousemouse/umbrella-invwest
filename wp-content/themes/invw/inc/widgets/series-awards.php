<?php
/**
 * Shows all the 
 */
class series_awards_widget extends WP_Widget {

    function __construct() {
        $widget_ops = array(
            'classname'     => 'series-awards',
            'description'   => __('Shows images of awards won by a series, based on the series associated with the current page. This relies on the menu "Award-Winning Series" where each entry is a series. Within each series menu entry, the award images should be full URLs to the image, delimited by spaces, in the Description field.', 'largo')
        );
        $this->WP_Widget( 'series-awards-widget', __('Series Awards', 'largo'), $widget_ops);
    }

    function get_images_for_series_term( $series_term ) {
        $series_menu_items = wp_get_nav_menu_items( "Award-Winning Series" );
        $image_urls = array();
        foreach( (array) $series_menu_items as $key => $menu_item ) {
            if ( (int) $menu_item->object_id == $series_term->term_id ) {
                $image_urls = explode( " ", trim( $menu_item->post_content ) );
                break;
            }
        }
        return $image_urls;
    }

    function widget( $args, $instance ) {
        extract( $args );

        // Only useful on single posts and archive pages for Series
        if ( !is_archive() && !is_single() ) return;


        echo $before_widget;

        if ( is_archive() ) {
            $term = get_queried_object();
            $title = apply_filters('widget_title', empty( $instance['title'] ) ? __('Series Awards', 'largo') : $instance['title'], $instance, $this->id_base);

            if ( 'series' == $term->taxonomy ) {
                $award_image_urls = $this->get_images_for_series_term( $term );
                
                if ( $title && count($award_image_urls) > 0 ) echo $before_title . $title . $after_title;

                foreach( $award_image_urls as $award_image_url ) {
                    echo '<img src="' . $award_image_url . '" class="award-img" />';
                }
            }
        } elseif ( is_single() ) {
            global $post;
            $terms = wp_get_post_terms( $post->ID, 'series' );
            foreach( $terms as $term ) {
                if ( 'series' == $term->taxonomy ) {
                    $award_image_urls = $this->get_images_for_series_term( $term );
                    $title = 'Awards For<br />'. $term->name;
                    if ( $title && count($award_image_urls) > 0 ) echo $before_title . $title . $after_title;
                    foreach( $award_image_urls as $award_image_url ) {
                        echo '<img src="' . $award_image_url . '" class="award-img" />';
                    }
                }
            }
        }

        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        return $instance;
    }

    function form( $instance ) {
        $defaults = array(
            'title' => __('Series Awards', 'largo'),
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'largo'); ?></label><br/>
                <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
            </p>
        <?php

    }
}
