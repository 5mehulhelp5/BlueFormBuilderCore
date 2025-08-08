/*
 * JavaScript Load Image Fetch
 * https://github.com/blueimp/JavaScript-Load-Image
 */

/* global define, module, require */

(function (factory) {
  "use strict";
  if (typeof define === "function" && define.amd) {
    define([
      "Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/vendor/blueimp-load-image/js/load-image"
    ], factory);
  } else if (typeof module === "object" && module.exports) {
    module.exports = factory(
      require("Cytracon_BlueFormBuilderCore/js/jquery/fileUploader/vendor/blueimp-load-image/js/load-image")
    );
  } else {
    factory(window.loadImage);
  }
})(function (loadImage) {
  "use strict";

  var g = (typeof window !== "undefined" ? window : (typeof self !== "undefined" ? self : this));

  if (
    g && g.fetch && g.Request && g.Response &&
    g.Response.prototype && typeof g.Response.prototype.blob === "function"
  ) {
    loadImage.fetchBlob = function (url, callback, options) {
      options = options || {};
      var req = new g.Request(url, options);

      var p = g.fetch(req).then(function (resp) {
        if (!resp || !resp.ok) throw new Error("fetch failed: " + (resp && resp.status));
        return resp.blob();
      });

      if (typeof callback === "function") {
        p.then(function (blob) { callback(blob); }, function (err) { callback(err); });
        return;
      }
      return p;
    };
  }

  return loadImage;
});
        }
        return response.blob();
      }

      var promise = global.fetch(req).then(toBlob);

      // Promise API if no callback supplied
      if (global.Promise && typeof callback !== "function") {
        return promise;
      }

      // Callback API
      promise
        .then(function (blob) {
          try {
            callback && callback(blob);
          } catch (e) {}
        })
        .catch(function (err) {
          try {
            callback && callback(err);
          } catch (e) {}
        });
    };
  }

  return loadImage;
});
        options = options || {}; // eslint-disable-line no-param-reassign
        var req = new XMLHttpRequest();
        req.open(options.method || "GET", url);
        if (options.headers) {
          Object.keys(options.headers).forEach(function (key) {
            req.setRequestHeader(key, options.headers[key]);
          });
        }
        req.withCredentials = options.credentials === "include";
        req.responseType = "blob";
        req.onload = function () {
          resolve(req.response);
        };
        req.onerror =
          req.onabort =
          req.ontimeout =
            function (err) {
              if (resolve === reject) {
                // Not using Promises
                reject(null, err);
              } else {
                reject(err);
              }
            };
        req.send(options.body);
      }
      if (global.Promise && typeof callback !== "function") {
        options = callback; // eslint-disable-line no-param-reassign
        return new Promise(executor);
      }
      return executor(callback, callback);
    };
  }
});
