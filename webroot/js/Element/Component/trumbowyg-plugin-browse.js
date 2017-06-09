(function ($) {
  'use strict';
  $.extend(true, $.trumbowyg, {
    langs: {
      en: {
        'attachment-browse': 'Browse',
        'attachment-settings': 'Media Settings'
      },
      fr: {
        'attachment-browse': 'Parcourir',
        'attachment-settings': 'Réglage du média'
      }
    },
    plugins: {
      'attachment-browse': {
        init: function (trumbowyg) {
          var btnDef = { fn: function () { trumbowyg.$c.trigger('attachment-browse',[trumbowyg]);}};
          trumbowyg.addBtnDef('attachment-browse', btnDef);
        }
      }
    }
  });
})(jQuery);
