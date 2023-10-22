jQuery(document).ready(function ($) {
    $('#select-logo').click(function(e) {
        e.preventDefault();

        var image = wp.media({
            title: 'Upload Logo',
            multiple: false
        }).open()
        .on('select', function(e) {
            var uploadedImage = image.state().get('selection').first();
            var image_url = uploadedImage.toJSON().url;
            $('#logo_id').val(uploadedImage.toJSON().id);
        });
    });
    // Handler for the "Select Header Background" button
    $('#select-header-bg').click(function (e) {
        e.preventDefault();
        selectImage('header_bg_id');
    });

    // Handler for the "Select QR Code" button
    $('#select-qr-code').click(function (e) {
        e.preventDefault();
        selectImage('qr_code_id');
    });

    function selectImage(targetField) {
        var customUploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        customUploader.on('select', function () {
            var attachment = customUploader.state().get('selection').first().toJSON();
            $('#' + targetField).val(attachment.id);
        });

        customUploader.open();
    }
});


