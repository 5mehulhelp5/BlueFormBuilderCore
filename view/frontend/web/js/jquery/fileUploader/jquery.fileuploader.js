/**
 * Custom Uploader
 * Copyright © Magento
 */

/* global define, require */
(function (factory) {
  "use strict";
  if (typeof define === "function" && define.amd) {
    define([
      "jquery",
      "Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-image",
      "Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-audio",
      "Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-video",
      "Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.iframe-transport",
    ], factory);
  } else if (typeof exports === "object") {
    factory(
      require("jquery"),
      require("Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-image"),
      require("Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-audio"),
      require("Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-video"),
      require("Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.iframe-transport")
    );
  } else {
    factory(window.jQuery);
  }
})(function ($) {
  "use strict";
  // ...existing code...
});
});
})();
