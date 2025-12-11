(function ($) {

    $(document).on('ready', function () {
        
        
            $('.cmb2-postbox .cmb-row').css({
                'border-bottom': 'none',
                'margin-bottom': '0'
            });
            $('#_folioedge_post_metabox').hide(0);
            $('.cmb2-id--folioedge-post-gallery').hide(0);
            $('.cmb2-id--folioedge-post-video-embed').hide(0);
            $('.cmb2-id--folioedge-post-audio-embed').hide(0);

            var id = $('input[name="post_format"]:checked').attr('id');

            if (id == 'post-format-gallery') {
                $('#_folioedge_post_metabox').show(0);
                $('.cmb2-id--folioedge-post-gallery').show();
            } else {
                $('.cmb2-id--folioedge-post-gallery').hide();
            }
            if (id == 'post-format-video') {
                $('#_folioedge_post_metabox').show(0);
                $('.cmb2-id--folioedge-post-video-embed').show();
            } else {
                $('.cmb2-id--folioedge-post-video-embed').hide();
            }
            if (id == 'post-format-audio') {
                $('#_folioedge_post_metabox').show(0);
                $('.cmb2-id--folioedge-post-audio-embed').show();
            } else {
                $('.cmb2-id--folioedge-post-audio-embed').hide();
            }
            $('#post-formats-select .post-format').on('change', function() {
                $('#_folioedge_post_metabox').hide(0);
                $('.cmb2-id--folioedge-post-gallery').hide(0);
                $('.cmb2-id--folioedge-post-video-embed').hide(0);
                $('.cmb2-id--folioedge-post-audio-embed').hide(0);
                var id = $('input[name="post_format"]:checked').attr('id');
                if (id == 'post-format-gallery') {
                    $('#_folioedge_post_metabox').show(0);
                    $('.cmb2-id--folioedge-post-gallery').show();
                } else {
                    $('.cmb2-id--folioedge-post-gallery').hide();
                }
                if (id == 'post-format-video') {
                    $('#_folioedge_post_metabox').show(0);
                    $('.cmb2-id--folioedge-post-video-embed').show();
                } else {
                    $('.cmb2-id--folioedge-post-video-embed').hide();
                }
                if (id == 'post-format-audio') {
                    $('#_folioedge_post_metabox').show(0);
                    $('.cmb2-id--folioedge-post-audio-embed').show();
                } else {
                    $('.cmb2-id--folioedge-post-audio-embed').hide();
                }
            });

        $(document).on("click", ".upload_image_button", function (e) {
            e.preventDefault();
            var $button = $(this);
            // Create the media frame.
            var file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select or upload image',
                library: { // remove these to show all
                    type: 'image' // specific mime
                },
                button: {
                    text: 'Select'
                },
                multiple: false // Set to true to allow multiple files to be selected
            });
            // When an image is selected, run a callback.
            file_frame.on('select', function () {
                // We set multiple to false so only get one image from the uploader 
                var attachment = file_frame.state().get('selection').first().toJSON();
                $button.parent().siblings('.author_image_url').val(attachment.url);
                $button.parents('.image_upload_part').find('.dm_image').attr('src', attachment.url);
            });
            // Finally, open the modal
            file_frame.open();
        });
    });
})(jQuery);