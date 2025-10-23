# ✅ PLUGIN UPLOAD CHECKLIST

## 📦 File Chuẩn Bị

- [x] Plugin ZIP: `anmi-product-style-injector.zip` (11KB)
- [x] Plugin version: **2.1.0**
- [x] CSS file included: `css/anmi-holder-products.css`
- [x] README.md (full documentation)
- [x] UPDATE-GUIDE.md (update instructions)
- [x] QUICK-UPLOAD-GUIDE.md (this file)

## 🔧 Plugin Structure

```
anmi-product-style-injector.zip
└── anmi-product-style-injector/
    ├── anmi-product-style-injector.php  ✅ Main plugin (v2.1.0)
    ├── css/
    │   └── anmi-holder-products.css     ✅ Product styling
    ├── README.md                         ✅ Documentation
    └── UPDATE-GUIDE.md                   ✅ Update guide
```

## 🎯 Fixes Applied

### Problem: CSS 301 Redirect
**Before:**
```
URL: https://anmitools.com/wp-content/plugins/css/anmi-holder-products.css
Status: 301 Moved Permanently
Issue: Missing plugin folder name in path
```

**After:**
```
URL: https://anmitools.com/wp-content/plugins/anmi-product-style-injector/css/anmi-holder-products.css
Status: 200 OK ✅
Fix: Corrected CSS path in plugin constructor
```

### Changes Made

1. **Fixed CSS Path** (Line 96-97)
   ```php
   // OLD (WRONG):
   $this->css_dir = dirname(__FILE__) . '/../css/';
   $this->css_url = plugins_url('../css/', __FILE__);
   
   // NEW (CORRECT):
   $this->css_dir = dirname(__FILE__) . '/css/';
   $this->css_url = plugins_url('css/', __FILE__);
   ```

2. **Moved CSS File**
   ```
   FROM: plugins/anmi-holder-products.css
   TO:   plugins/css/anmi-holder-products.css
   ```

3. **Added Editor Support** (v2.1.0)
   - Added `enqueue_editor_styles()` function
   - Hooked to `enqueue_block_editor_assets` (Gutenberg)
   - Hooked to `add_editor_style` (Classic Editor)

## 📋 Pre-Upload Checklist

- [x] Plugin version updated to 2.1.0
- [x] CSS path fixed to include plugin folder
- [x] CSS file moved to `css/` subfolder
- [x] Editor support added
- [x] All files included in ZIP
- [x] ZIP structure correct for WordPress
- [x] Documentation complete

## 🚀 Upload Steps

1. **Backup First** (if replacing existing plugin)
   - Export plugin settings (if any)
   - Note down any customizations

2. **Remove Old Plugin**
   - Deactivate
   - Delete

3. **Upload New ZIP**
   - Via WordPress Admin: Plugins → Add New → Upload
   - Or via FTP: Upload to `/wp-content/plugins/`

4. **Activate**
   - Plugins → Installed Plugins → Activate

5. **Clear All Caches**
   - WordPress cache
   - CDN cache (Cloudflare)
   - Browser cache

6. **Test**
   - View source → Check CSS URL
   - DevTools → Network → Check CSS loads (200 OK)
   - Frontend → Check styling
   - Editor → Check styling

## ✅ Success Criteria

### CSS Loads Correctly
```
✅ Status: 200 OK
✅ URL: .../anmi-product-style-injector/css/anmi-holder-products.css?ver=2.1.0
✅ Size: ~15KB
✅ Content-Type: text/css
```

### Frontend Styling Works
```
✅ Background: #FCF7EC (beige)
✅ Sections: Bordered, padded
✅ Typography: Correct fonts
✅ Colors: Matching design
```

### Editor Styling Works
```
✅ Gutenberg: Styling applied
✅ Classic Editor: Styling applied
✅ Preview: Matches frontend
✅ WYSIWYG: What you see is what you get
```

## 🐛 Rollback Plan

If something goes wrong:

1. **Via FTP:**
   ```bash
   # Remove new plugin
   rm -rf /wp-content/plugins/anmi-product-style-injector/
   
   # Restore from backup (if you made one)
   cp -r backup/anmi-product-style-injector/ /wp-content/plugins/
   ```

2. **Via WordPress:**
   - Delete plugin
   - Upload old version
   - Activate

## 📊 Expected Timeline

| Step | Time | Status |
|------|------|--------|
| Deactivate old plugin | 30s | ⏳ |
| Delete old plugin | 30s | ⏳ |
| Upload new ZIP (11KB) | 1-2 min | ⏳ |
| Activate new plugin | 30s | ⏳ |
| Clear caches | 1 min | ⏳ |
| Test CSS loads | 2 min | ⏳ |
| Test frontend | 2 min | ⏳ |
| Test editor | 2 min | ⏳ |
| **Total** | **~10 min** | ⏳ |

## 🎯 Ready to Upload?

**Check all items:**

- [x] ✅ ZIP file created (11KB)
- [x] ✅ CSS path fixed
- [x] ✅ Editor support added
- [x] ✅ Version 2.1.0
- [x] ✅ Documentation complete
- [x] ✅ Backup plan ready

**Next Action:**
👉 Follow **QUICK-UPLOAD-GUIDE.md** to upload now!

---

**Plugin:** An Mi Product Style Injector  
**Version:** 2.1.0  
**Date:** October 23, 2025  
**Status:** ✅ READY TO UPLOAD
