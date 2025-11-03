# Embed Code Preservation - Test Guide

## Problem Fixed
Previously, when using "Mã Nhúng" option:
1. User pastes iframe code: `<iframe src="https://youtube.com/embed/VIDEO_ID" ...></iframe>`
2. Plugin extracts URL: `https://youtube.com/embed/VIDEO_ID`
3. **Saves only URL** to database
4. When editing again, shows URL instead of full iframe code ❌

**Now Fixed:**
1. User pastes iframe code
2. Plugin **saves full iframe code** to database
3. When editing again, **shows original iframe code** ✅
4. When rendering, extracts URL automatically

---

## How It Works

### 1. Save Process (admin-edit.php)
```javascript
if (videoType === 'embed') {
    var embedCode = $('#video_embed_code').val().trim();
    // Save FULL embed code (not extracted URL)
    videoUrl = embedCode;
}
```

**Database:** `video_url` column stores full iframe code when `video_type = 'embed'`

### 2. Edit Display (admin-edit.php)
```php
<textarea id="video_embed_code">
<?php 
    if ($is_edit && $banner->video_type == 'embed') {
        echo esc_textarea($banner->video_url); // Shows full iframe code
    }
?>
</textarea>
```

**Result:** When editing, textarea shows original iframe code

### 3. Frontend Rendering (anmi-video-banner.php)
```php
// Extract URL from embed code before parsing
if (isset($banner->video_type) && $banner->video_type === 'embed') {
    if (preg_match('/src=["\'](https?:\/\/[^"\']+)["\']/i', $banner->video_url, $match)) {
        $video_url = $match[1]; // Extract src URL
    }
}
```

**Result:** Frontend automatically extracts URL from iframe code

### 4. Modal Preview (admin-list.php)
```javascript
// Extract URL from embed code if needed
if (videoUrl.indexOf('<iframe') !== -1) {
    var srcMatch = videoUrl.match(/src=["']([^"']+)["']/i);
    if (srcMatch && srcMatch[1]) {
        videoUrl = srcMatch[1];
    }
}
```

**Result:** Preview modal works correctly with embed codes

---

## Test Procedure

### Test 1: Create New Banner with Embed Code

**Steps:**
1. Go to **Admin → Video Banners → Add New**
2. Fill in **Banner Name:** "Test Embed Code"
3. Select **Loại Video:** "Mã Nhúng"
4. Paste this iframe code:
```html
<iframe width="560" height="315" src="https://www.youtube.com/embed/egbA1RHO8MY?si=JU44lYQyb2XM6dlT" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
```
5. Upload at least 1 image
6. Click **Save**

**Expected Results:**
- ✅ Save successful
- ✅ No validation errors
- ✅ Redirects to banner list

---

### Test 2: Edit Banner - Verify Embed Code Preserved

**Steps:**
1. Go to **Admin → Video Banners → Edit** (the banner you just created)
2. Check **Loại Video** dropdown → Should show "Mã Nhúng" selected
3. Check textarea field

**Expected Results:**
- ✅ Textarea shows **full iframe code** (not just URL)
- ✅ Code includes all attributes: `width`, `height`, `title`, `frameborder`, `allow`, etc.
- ✅ Exactly matches what you pasted originally

**Screenshot comparison:**
```
BEFORE (Bug):
Textarea shows: https://www.youtube.com/embed/egbA1RHO8MY?si=JU44lYQyb2XM6dlT

AFTER (Fixed):
Textarea shows: <iframe width="560" height="315" src="https://www.youtube.com/embed/egbA1RHO8MY?si=JU44lYQyb2XM6dlT" title="YouTube video player"...></iframe>
```

---

### Test 3: Frontend Display

**Steps:**
1. Copy shortcode from banner list: `[anmi_video_banner id="X"]`
2. Create/edit a page
3. Paste shortcode
4. Publish and view page

**Expected Results:**
- ✅ Banner displays correctly
- ✅ Slider images visible
- ✅ Hover interaction works
- ✅ Video plays from correct YouTube URL

---

### Test 4: Modal Preview

**Steps:**
1. Go to **Admin → Video Banners** (list page)
2. Click **Preview** button on the embed code banner
3. Open browser console (F12)

**Expected Results:**
- ✅ Modal opens
- ✅ Console shows: `Extracted URL from embed code: https://www.youtube.com/embed/...`
- ✅ Banner renders correctly in modal
- ✅ No JavaScript errors
- ✅ Video iframe displays

---

### Test 5: Live Preview in Edit Page

**Steps:**
1. Edit the embed code banner
2. Check **Live Preview** sidebar on the right

**Expected Results:**
- ✅ Preview shows video
- ✅ Slider images visible
- ✅ Hover works

---

### Test 6: Modify Embed Code

**Steps:**
1. Edit banner
2. Change embed code (different video ID or attributes)
3. Save
4. Edit again

**Expected Results:**
- ✅ New embed code is preserved
- ✅ Textarea shows updated iframe code
- ✅ Frontend renders new video

---

## Database Inspection

**Query to check:**
```sql
SELECT id, name, video_type, video_url 
FROM wp_anmi_video_banners 
WHERE video_type = 'embed';
```

**Expected Result:**
```
id | name              | video_type | video_url
---|-------------------|------------|----------------------------
3  | Test Embed Code   | embed      | <iframe width="560" height="315" src="https://www.youtube.com/embed/egbA1RHO8MY?si=JU44lYQyb2XM6dlT" ...></iframe>
```

**Important:** 
- `video_url` should contain **full iframe code**
- NOT just the URL
- This is intentional and correct behavior

---

## Rollback Instructions (If Needed)

If you need to revert to URL-only storage:

1. Revert commit:
```bash
git revert b7b2f34
```

2. Or manually change in `admin-edit.php`:
```javascript
// Change back to:
var srcMatch = embedCode.match(/src=["']([^"']+)["']/i);
if (srcMatch && srcMatch[1]) {
    videoUrl = srcMatch[1]; // Extract URL only
}
```

---

## Technical Notes

### Pros of Current Approach:
✅ Preserves original embed code  
✅ User can see exactly what they pasted  
✅ Easy to copy/modify embed parameters  
✅ No data loss  
✅ Backward compatible (non-embed banners unaffected)

### Cons:
⚠️ `video_url` column contains HTML instead of URL (when `video_type='embed'`)  
⚠️ Requires extraction logic in multiple places

### Alternative Approach (Not Implemented):
- Add new database column `embed_code TEXT`
- Store URL in `video_url`, full code in `embed_code`
- Pros: Cleaner data structure
- Cons: Requires database migration

---

## Support

If issues arise:
1. Check console for extraction logs
2. Verify `video_type` column value in database
3. Check if iframe code has valid `src` attribute
4. Contact: https://anmitools.com
