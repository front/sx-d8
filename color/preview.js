/**
 * @file
 * Preview for the Starter X Drupal theme.
 */
(function($, Drupal) {
    Drupal.color = {
        callback(context, settings, $form) {

            const $colorPreview = $form.find('.color-preview');
            const $colorPalette = $form.find('.js-color-palette');

            // Button preview.
            $colorPreview
                .find('.preview-button')
                .css('background', $colorPalette.find('input[name="palette[base]"]').val());

            $colorPreview
                .find('.preview-button')
                .mouseenter(function() {
                    $(this).css('background', $colorPalette.find('input[name="palette[link]"]').val());
                })
                .mouseleave(function() {
                    $(this).css('background', $colorPalette.find('input[name="palette[base]"]').val());
                });

            // Link preview.
            $colorPreview
                .find('.preview-link')
                .css('color', $colorPalette.find('input[name="palette[link]"]').val());

            // Heading preview.
            $colorPreview
                .find('.preview-heading')
                .css('color', $colorPalette.find('input[name="palette[headings]"]').val());

            // Paragraphs preview.
            $colorPreview
                .find('.preview-paragraphs')
                .css('color', $colorPalette.find('input[name="palette[text]"]').val());

            // Paragraphs preview.
            $colorPreview
                .find('.preview-label')
                .css('color', $colorPalette.find('input[name="palette[label-label-text-background]"]').val());

            // Paragraphs preview.
            $colorPreview
                .find('.preview-input')
                .css('color', $colorPalette.find('input[name="palette[input-text]"]').val());

            // Paragraphs preview.
            $colorPreview
                .find('.preview-border')
                .css('border-color', $colorPalette.find('input[name="palette[borders]"]').val());

            // Paragraphs preview.
            $colorPreview
                .find('.preview-background')
                .css('background-color', $colorPalette.find('input[name="palette[backgrounds]"]').val());
        },
    };
})(jQuery, Drupal);
