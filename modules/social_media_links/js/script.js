/**
 * @file
 * Simple JavaScript hello world file.
 */

(function ($, Drupal, settings) {
  Drupal.behaviors.socialMediaBlock = {
    attach: function (context) {
      //@TODO Remove required attribute in another way
      $('.block-form input').removeAttr('required');
      $('.block-form select').removeAttr('required');

      $( "#edit-settings-instances" ).sortable({
        update: function( event, ui ) {
          $('#edit-settings-instances .order-number-input').each(function (index, value) {
            $(this).val(index);
          });

          var alertMessage = Drupal.t('You have unsaved changes');
          $(".block-form").prepend('<div class="tabledrag-changed-warning messages messages--warning">' + alertMessage + '</div>');
        }
      }).disableSelection();
    }
  }

})(jQuery, Drupal, drupalSettings);