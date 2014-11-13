<?php
/**
 * Plugin Name: KCPT Image Widget
 * Plugin URI: http://www.KCPT.org/
 * Description: A simple image widget which will fade another image through on hover.
 * Version: 0.0.1
 * Author: Steven Kohlmeyer
 * Author URI: http://StevenKohlmeyer.com
 * License: LGPL2
 */

class KCPT_Fading_Image_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'kcpt_image_widget',
            'Image Widget',
            array( 'description' => 'A widget that displays image boxes' )
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

        if ( $img1 ):
            $image_attributes = wp_get_attachment_image_src($img1, 'small');
            ?><img class="image-widget-image" src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[1]; ?>" height="<?php $image_attributes[2]; ?>" /><?php
        endif;

        if ( $img1 and $img2 ):
            $image_attributes_ro = wp_get_attachment_image_src($img2, 'small');
            ?><img class="image-widget-hover-image" src="<?php echo $image_attributes_ro[0]; ?>" width="<?php echo $image_attributes_ro[1]; ?>" height="<?php echo $image_attributes_ro[2]; ?>" /><?php
        endif;

        ?>
        <?php if( $href ): ?>
            </a>
        <?php endif; ?>
        <?php if( $img1 or $img2 ): ?>
            </div>
        <?php endif; ?>

        <?php echo $after_widget; ?>

        <style>
            .img-widget {
                position: relative;
            }
            .img-widget img {
                height: auto;
                width: 100%;
            }
            .img-widget .image-widget-hover-image {
                left: 0px;
                opacity: 0;
                position: absolute;
                top: 0px;
                width: 100%;

                -webkit-transition: opacity 0.5s ease-in-out;
                -moz-transition: opacity 0.5s ease-in-out;
                -ms-transition: opacity 0.5s ease-in-out;
                -o-transition: opacity 0.5s ease-in-out;
                transition: opacity 0.5s ease-in-out;
            }
            .img-widget:hover .image-widget-hover-image {
                opacity: 1;
            }
        </style>
        <?php

    }

    //Update the widget

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        //Strip tags from title and name to remove HTML
        $instance['title']                      = strip_tags( $new_instance['title'] );
        $instance['img1']                       = strip_tags( $new_instance['img1'] );
        $instance['img2']                       = strip_tags( $new_instance['img2'] );
        $instance['href']                       = strip_tags( $new_instance['href'] );

        return $instance;
    }


    function form( $instance ) {

        wp_enqueue_media();

        //Set up some default widget settings.
        $defaults = array(
            'href'                      => false,
            'img1'                      => false,
            'img2'                      => false,
            'title'                     => false
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

        <script>
            (function($) {
                $(document).ready(function() {
                    $('.insert-kcpt-image').click(function(e) {

                        e.preventDefault();

                        var inputToFill = $(e.currentTarget).prev('input[type="text"]');
                        
                        //If the frame already exists, reopen it
                        if (typeof(custom_file_frame)!=="undefined") {
                            custom_file_frame.close();
                        }

                        //Create WP media frame.
                        custom_file_frame = wp.media.frames.customHeader = wp.media({
                            //Title of media manager frame
                            title: "Sample title of WP Media Uploader Frame",
                            library: {
                                type: 'image'
                            },
                            button: {
                                //Button text
                                text: "insert text"
                            },
                            //Do not allow multiple files, if you want multiple, set true
                            multiple: false
                        });

                        //callback for selected image
                        custom_file_frame.on('select', function() {
                            var attachment = custom_file_frame.state().get('selection').first().toJSON();
                            $(inputToFill).val(attachment.id);
                        });

                        //Open modal
                        custom_file_frame.open();
                    })
                });
            })(jQuery);
        </script>

    <?php
    }
}

function kcpt_image_widgets_init() {
    register_widget( 'KCPT_Fading_Image_Widget' );
}

add_action( 'widgets_init', 'kcpt_image_widgets_init' );


