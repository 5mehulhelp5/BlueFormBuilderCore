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
      "BlueFormBuilder_Core/js/jquery/fileUploader/jquery.fileupload-image",
      "BlueFormBuilder_Core/js/jquery/fileUploader/jquery.fileupload-audio",
      "BlueFormBuilder_Core/js/jquery/fileUploader/jquery.fileupload-video",
      "BlueFormBuilder_Core/js/jquery/fileUploader/jquery.iframe-transport",
    ], factory);
  } else if (typeof exports === "object") {
    // Node/CommonJS:
    factory(
      require("jquery"),
      require("BlueFormBuilder_Core/js/jquery/fileUploader/jquery.fileupload-image"),
      require("BlueFormBuilder_Core/js/jquery/fileUploader/jquery.fileupload-audio"),
      require("BlueFormBuilder_Core/js/jquery/fileUploader/jquery.fileupload-video"),
      require("BlueFormBuilder_Core/js/jquery/fileUploader/jquery.iframe-transport")
    );
  } else {
    // Browser globals:
    factory(window.jQuery);
  }
})();
