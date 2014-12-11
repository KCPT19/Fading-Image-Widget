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
                title: "Select Image for Widget",
                library: {
                    type: 'image'
                },
                button: {
                    //Button text
                    text: "Insert Image"
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