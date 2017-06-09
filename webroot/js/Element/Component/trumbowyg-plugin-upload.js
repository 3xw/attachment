(function ($) {
  'use strict';
  $.extend(true, $.trumbowyg, {
    langs: {
      en: {
        'attachment-upload': 'Upload',
        'attachment-settings': 'Media Settings'
      },
      fr: {
        'attachment-upload': 'Téléverser',
        'attachment-settings': 'Réglage du média'
      }
    },
    plugins: {
      'attachment-upload': {
        init: function (trumbowyg) {
          var btnDef = { fn: function () { trumbowyg.$c.trigger('attachment-upload',[trumbowyg]);}};
          trumbowyg.addBtnDef('attachment-upload', btnDef);
        }
      }
    }
  });
})(jQuery);
