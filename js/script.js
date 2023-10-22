jQuery(document).ready(function ($) {
    // Logo image selection
    $('#select-logo').on('click', function (e) {
        e.preventDefault();
        var image = wp.media({
            title: 'Select Logo',
            multiple: false,
            library: { type: 'image' },
            button: { text: 'Select' },
        }).on('select', function () {
            var attachment = image.state().get('selection').first().toJSON();
            $('#logo_url').val(attachment.url);
            // Display the selected image
            $('#logo_preview').attr('src', attachment.url);
        }).open();
    });

    // Header Background image selection
    $('#select-header-bg').on('click', function (e) {
        e.preventDefault();
        var image = wp.media({
            title: 'Select Header Background',
            multiple: false,
            library: { type: 'image' },
            button: { text: 'Select' },
        }).on('select', function () {
            var attachment = image.state().get('selection').first().toJSON();
            $('#header_bg_url').val(attachment.url);
            // Display the selected image
            $('#header_bg_preview').attr('src', attachment.url);
        }).open();
    });

    // QR Code image selection
    $('#select-qr-code').on('click', function (e) {
        e.preventDefault();
        var image = wp.media({
            title: 'Select QR Code',
            multiple: false,
            library: { type: 'image' },
            button: { text: 'Select' },
        }).on('select', function () {
            var attachment = image.state().get('selection').first().toJSON();
            $('#qr_code_url').val(attachment.url);
            // Display the selected image
            $('#qr_code_preview').attr('src', attachment.url);
        }).open();
    });
});
