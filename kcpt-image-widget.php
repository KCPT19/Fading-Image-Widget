<?php
/**
 * Plugin Name: KCPT Fading Image Widget
 * Plugin URI: http://www.KCPT.org/
 * Description: A simple image widget which will fade another image through on hover.
 * Version: 0.0.5
 * Author: Steven Kohlmeyer
 * Author URI: http://StevenKohlmeyer.com
 * License: LGPL2
 */

class KCPT_Fading_Image_Widget extends WP_Widget {

    function __construct() {

        parent::__construct(

            'kcpt_image_widget',
            'Fading Image Widget',
            array( 'description' => 'A widget that displays an image box - linkable and can fade into a 2nd image.' )

        );


    }

    public $option_name = "";

    function widget( $args, $instance ) {

        $before_widget  = "";
        $after_widget   = "";

        extract( $args );

        $title      = $instance['title'];
        $img1       = $instance['img1'];
        $img2       = $instance['img2'];
        $href       = $instance['href'];

        echo $before_widget;

        // Display the widget title
        if( $title ): ?>
            <?php if( $href ): ?>
                <a href="<?php echo $href; ?>">
            <?php endif; ?>
            <h4>
                <?php echo $title; ?>
            </h4>
            <?php if( $href ): ?>
                </a>
            <?php endif; ?>
        <?php endif; ?>
        <?php if( $img1 or $img2 ): ?>
            <div class="img-widget">
        <?php endif; ?>
        <?php if( $href ): ?>
            <a href="<?php echo $href; ?>">
        <?php endif; ?>
        <?php

        if ( $img1 ) {

            $image_attributes = wp_get_attachment_image_src($img1, 'small');
            $imageOneClasses = "";

            if ($img2) {

                $imageOneClasses .= " fade";

            }

            ?><img class="image-widget-image <?php echo $imageOneClasses; ?>" src="<?php echo $image_attributes[0]; ?>"
                   width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" /><?php

        }

        if ( $img2 ) {

            $imageOneClasses = "";

            if ($img1) {

                $imageOneClasses .= " fade";

            }

            $image_attributes_ro = wp_get_attachment_image_src($img2, 'small');

            ?><img class="image-widget-hover-image <?php echo $imageOneClasses; ?>"
                   src="<?php echo $image_attributes_ro[0]; ?>" width="<?php echo $image_attributes_ro[1]; ?>"
                   height="<?php echo $image_attributes_ro[2]; ?>" /><?php

        }

        ?>
        <?php if( $href ): ?>
            </a>
        <?php endif; ?>
        <?php if( $img1 or $img2 ): ?>
            </div>
        <?php endif; ?>

        <?php echo $after_widget; ?>
        <?php

    }

    //Update the widget

    function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        //Strip tags from title and name to remove HTML
        $instance['title']  = strip_tags( $new_instance['title'] );
        $instance['img1']   = strip_tags( $new_instance['img1'] );
        $instance['img2']   = strip_tags( $new_instance['img2'] );
        $instance['href']   = strip_tags( $new_instance['href'] );

        return $instance;

    }


    function form( $instance ) {

        wp_enqueue_media();

        //Set up some default widget settings.
        $defaults = array(
            'href'          => false,
            'img1'          => false,
            'img2'          => false,
            'title'         => false
        );

        $img_thumb      = false;
        $img_thumb2     = false;
        $instance       = wp_parse_args( (array) $instance, $defaults );

        if( $instance['img1'] ) {

            $img_thumb  = wp_get_attachment_image_src($instance['img1'], 'thumbnail');

        }
        if( $instance['img2'] ) {

            $img_thumb2 = wp_get_attachment_image_src($instance['img2'], 'thumbnail');

        }

        ?>

        <p>
            To remove image, either click Insert image to replace, or remove image ID number and click save to delete.
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title (Optional)</label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'img1' ); ?>">Image 1 ID</label>
            <input type="text" id="<?php echo $this->get_field_id( 'img1' ); ?>" name="<?php echo $this->get_field_name( 'img1' ); ?>" value="<?php echo $instance['img1']; ?>" style="width:100%;" />
            <?php if( $img_thumb ): ?>
                <br /><img src="<?php echo $img_thumb[0]; ?>" width="<?php echo $img_thumb[1]; ?>" height="<?php echo $img_thumb[2]; ?>" /><br />
            <?php endif; ?>
            <button class="insert-kcpt-image">Insert Image</button>
        </p>


        <p>
            <label for="<?php echo $this->get_field_id( 'img2' ); ?>">Image 2 ID (Rollover)</label>
            <input type="text" id="<?php echo $this->get_field_id( 'img2' ); ?>" name="<?php echo $this->get_field_name( 'img2' ); ?>" value="<?php echo $instance['img2']; ?>" style="width:100%;" />
            <?php if( $img_thumb2 ): ?>
                <br /><img src="<?php echo $img_thumb2[0]; ?>" width="<?php echo $img_thumb2[1]; ?>" height="<?php echo $img_thumb2[2]; ?>" /><br />
            <?php endif; ?>
            <button class="insert-kcpt-image">Insert Image</button>
        </p>


        <p>
            <label for="<?php echo $this->get_field_id( 'href' ); ?>">URL</label>
            <input id="<?php echo $this->get_field_id( 'href' ); ?>" name="<?php echo $this->get_field_name( 'href' ); ?>" value="<?php echo $instance['href']; ?>" style="width:100%;" />
        </p>
        <?php

    }
}

function kcpt_image_widgets_init() {

    register_widget( 'KCPT_Fading_Image_Widget' );

}

class KCPT_Fading_Image_Widget_Helper {

    public function __construct() {

        $this->loadJS = false;
        add_action( 'current_screen', array( $this, 'currentScreen' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 1 );
        add_action( 'admin_enqueue_scripts', array( $this, 'adminEnqueue' ), 1 );

    }

    function currentScreen() {

        $thisScreen = get_current_screen();

        if( $thisScreen->id === "widgets" ) {

            $this->loadJS = true;

            add_action( 'wp_enqueue_scripts', array( $this, 'adminEnqueue' ), 1 );

        }

    }

    function adminEnqueue() {

        if( $this->loadJS === true ) {

            wp_enqueue_script( 'kcpt-fading-image-widget-js', plugins_url( 'kcpt-fading-image-widget.js', __FILE__ ), array(), array(), 10 );

        }

    }

    function enqueue() {

        wp_enqueue_style( 'kcpt-fading-image-widget-css', plugins_url( 'kcpt-fading-image-widget.css', __FILE__ ), array(), array(), 'all' );

    }

}

add_action( 'widgets_init', 'kcpt_image_widgets_init' );


$KCPTFadingImageWidgetHelper = new KCPT_Fading_Image_Widget_Helper();