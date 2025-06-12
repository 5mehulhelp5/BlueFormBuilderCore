/**
 * Custom Uploader
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global define, require */

(function (factory) {
  "use strict";
  if (typeof define === "function" && define.amd) {
    // Register as an anonymous AMD module:
    define([
      "jquery",
      "Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-image",
      "Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-audio",
      "Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-video",
      "Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.iframe-transport",
    ], factory);
  } else if (typeof exports === "object") {
    // Node/CommonJS:
    factory(
      require("jquery"),
      require("Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-image"),
      require("Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-audio"),
      require("Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.fileupload-video"),
      require("Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/jquery.iframe-transport")
    );
  } else {
    // Browser globals:
    factory(window.jQuery);
  }
})();
