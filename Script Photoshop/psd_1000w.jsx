#target photoshop

(function () {
    var TARGET_WIDTH = 1000;    // px
    var RESOLUTION = 72;        // ppi
    var ALLOW_UPSCALE = false;  // cho phép phóng to ảnh nhỏ hơn 1000px?
    var WEBP_QUALITY = 80;      // chất lượng WebP (0-100)

    var oldRuler = app.preferences.rulerUnits;
    app.preferences.rulerUnits = Units.PIXELS;

    try {
        var inputFolder = Folder.selectDialog("Chọn thư mục chứa ảnh cần xử lý");
        if (!inputFolder) return;

        var outputFolder = new Folder(inputFolder.fsName + "/psd_1000w");
        if (!outputFolder.exists) outputFolder.create();

        var validRegex = /\.(jpg|jpeg|png|tif|tiff|bmp|psd|webp)$/i;
        var files = inputFolder.getFiles(function (f) {
            return (f instanceof File) && validRegex.test(f.name);
        });

        if (!files.length) {
            alert("Không tìm thấy ảnh hợp lệ.");
            return;
        }

        var processed = 0;
        var errors = [];
        var webpAvailable = (typeof WebPOptions !== "undefined");
        var webpNoticeAdded = false;

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            try {
                var doc = app.open(file);
                var baseName = doc.name.replace(/\.[^\.]+$/, "");

                var curW = doc.width.as("px");
                var curH = doc.height.as("px");

                var method;
                if (curW > TARGET_WIDTH) {
                    method = ResampleMethod.BICUBICSHARPER;
                } else if (curW < TARGET_WIDTH && ALLOW_UPSCALE) {
                    method = ResampleMethod.BICUBICSMOOTHER;
                } else {
                    method = ResampleMethod.BICUBIC;
                }

                if (curW !== TARGET_WIDTH || (curW < TARGET_WIDTH && ALLOW_UPSCALE)) {
                    var targetH = (curH / curW) * TARGET_WIDTH;
                    doc.resizeImage(UnitValue(TARGET_WIDTH, "px"), UnitValue(targetH, "px"), RESOLUTION, method);
                }

                // Save PSD
                var psdFile = new File(outputFolder.fsName + "/" + baseName + "_1000w.psd");
                doc.saveAs(psdFile, new PhotoshopSaveOptions(), true, Extension.LOWERCASE);

                // Save WebP
                if (webpAvailable) {
                    try {
                        var webpFile = new File(outputFolder.fsName + "/" + baseName + "_1000w.webp");
                        var webpOpts = new WebPOptions();
                        webpOpts.quality = WEBP_QUALITY;
                        webpOpts.lossless = false;
                        try { webpOpts.alphaQuality = 100; } catch(e){}
                        try { webpOpts.method = 4; } catch(e){}
                        doc.saveAs(webpFile, webpOpts, true, Extension.LOWERCASE);
                    } catch (e) {
                        errors.push("WebP lỗi " + file.name + ": " + e.toString());
                    }
                } else {
                    if (!webpNoticeAdded) {
                        errors.push("⚠ WebPOptions không hỗ trợ trong bản Photoshop này → bỏ qua WebP.");
                        webpNoticeAdded = true;
                    }
                }

                doc.close(SaveOptions.DONOTSAVECHANGES);
                processed++;

            } catch (ex) {
                errors.push(file.name + " → " + ex.toString());
                try { app.activeDocument.close(SaveOptions.DONOTSAVECHANGES); } catch(e){}
            }
        }

        var msg = "✅ Xong " + processed + " ảnh.\nLưu tại: " + outputFolder.fsName;
        if (errors.length) msg += "\n\nChi tiết:\n- " + errors.join("\n- ");
        alert(msg);

    } finally {
        app.preferences.rulerUnits = oldRuler;
    }
})();
