jQuery('div.notice.wpbd-message-dismissed').on('click', 'button.notice-dismiss, .wpbd-button-notice-dismiss', function (event) {
        event.preventDefault();
        jQuery.post(ajaxurl, {
          action: 'wpbd_set_admin_notice_viewed',
          notice_id: jQuery(this).closest('.wpbd-message-dismissed').data('notice_id')
        });
        var $wrapperElm = jQuery(this).closest('.wpbd-message-dismissed');
        $wrapperElm.fadeTo(100, 0, function () {
          $wrapperElm.slideUp(100, function () {
            $wrapperElm.remove();
          });
        });
      });