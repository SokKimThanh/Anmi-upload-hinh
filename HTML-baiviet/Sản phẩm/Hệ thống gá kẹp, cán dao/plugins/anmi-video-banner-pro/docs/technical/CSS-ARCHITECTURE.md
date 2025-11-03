# An Mi Video Banner - CSS Architecture

## 📋 Overview
This document explains the CSS structure and specificity strategy for video/iframe rendering.

---

## 🎯 Design Goals

1. **Production (Frontend):** Background video effect with scaling/cropping
2. **Modal Preview (Admin):** Clean display at 100% with proper aspect ratio
3. **No Conflicts:** Clear separation between production and preview styles

---

## 📁 File Structure

### `assets/css/video-banner.css`
**Purpose:** Core styles for video/iframe (both production and modal)

### `assets/css/admin-style.css`
**Purpose:** Admin-specific UI, layout, and modal preview container

---

## 🔧 CSS Class Hierarchy

### **Level 1: Base Production Styles**

```css
.anmi-banner-video {
    /* Background video - hidden by default (opacity: 0) */
    /* Center and scale to cover container */
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    object-fit: cover; /* Fill and crop */
}

.anmi-banner-iframe {
    /* Background iframe - visible */
    /* Scale 300% via media queries for cover effect */
    position: absolute;
    top: 0;
    left: 0;
    object-fit: cover;
}
```

**Use Case:** 
- Production websites
- Background video banners
- Full-screen video effects

---

### **Level 2: Modal Preview Overrides**

```css
.anmi-modal-video {
    /* Override production styles */
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    transform: none !important; /* Remove centering */
    object-fit: contain !important; /* Fit entire video */
    opacity: 1 !important; /* Override default 0 */
}

.anmi-modal-iframe {
    /* Clean display for preview */
    position: absolute !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
}
```

**Key Points:**
- Uses `!important` to override base styles
- `transform: none` removes centering from production
- `object-fit: contain` shows entire video (with letterbox if needed)
- `opacity: 1` makes video visible immediately

**Use Case:**
- Admin modal preview
- Edit page live preview
- Testing/debugging

---

### **Level 3: Container Context**

```css
#anmi-preview-container .anmi-video-banner-container {
    /* Container for modal preview */
    position: relative;
    height: 400px;
    background: #000;
}

#anmi-preview-container .anmi-modal-iframe,
#anmi-preview-container .anmi-modal-video {
    cursor: pointer; /* Allow interaction */
}
```

**Use Case:**
- Modal preview container
- Ensures children can position absolutely
- Black background for professional video player look

---

## 🎨 object-fit Explained

| Value | Behavior | Use Case |
|-------|----------|----------|
| `cover` | Fill container, crop if needed | Production background video |
| `contain` | Fit entire content, letterbox if needed | Modal preview |

---

## 📊 CSS Specificity Strategy

### **Why !important?**
Because we removed ALL inline styles from HTML, we use `!important` to ensure modal rules override base production rules.

### **Specificity Levels:**

```
Level 1: Base styles               → Specificity: 10 (.class)
Level 2: Modal overrides           → Specificity: 10 + !important override
Level 3: Container context         → Specificity: 20 (#id .class)
```

### **Previous Issues (Fixed):**

❌ **Before:** Inline styles (`style="..."`) → Specificity: 1000 (highest)  
✅ **After:** Removed inline styles, use `!important` in CSS → Full CSS control

---

## 🔍 Media Queries Strategy

```css
/* Only apply scaling to production iframes */
@media (min-aspect-ratio: 16/9) {
    .anmi-banner-iframe:not(.anmi-modal-iframe) {
        height: 300%;
        top: -100%;
    }
}
```

**Key Selector:** `.anmi-banner-iframe:not(.anmi-modal-iframe)`

This ensures:
- ✅ Production iframes scale 300% for cover effect
- ✅ Modal preview iframes stay at 100%
- ✅ No conflicts between the two use cases

---

## 🧪 Testing CSS Classes

### **Production Test:**
```html
<div class="anmi-video-banner-container">
    <iframe class="anmi-banner-video anmi-banner-iframe" src="..."></iframe>
</div>
```
**Expected:** Video scales to cover (300%), crops edges

---

### **Modal Preview Test:**
```html
<div id="anmi-preview-container">
    <div class="anmi-video-banner-container">
        <iframe class="anmi-banner-video anmi-banner-iframe anmi-modal-iframe" src="..."></iframe>
    </div>
</div>
```
**Expected:** Video at 100%, fits properly with letterbox

---

## 🔑 Key Takeaways

### **Do's ✅**
- Use `.anmi-modal-video` / `.anmi-modal-iframe` for modal previews
- Use `!important` to override base production styles
- Keep production and modal classes separate
- Use `object-fit: contain` for previews, `cover` for production

### **Don'ts ❌**
- Don't use inline styles (`style="..."`)
- Don't remove `:not(.anmi-modal-iframe)` from media queries
- Don't mix production and modal class logic
- Don't use high specificity unnecessarily

---

## 📝 Class Reference Quick Guide

| Class | Purpose | Context | object-fit |
|-------|---------|---------|------------|
| `.anmi-banner-video` | Base video | Production | `cover` |
| `.anmi-banner-iframe` | Base iframe | Production | `cover` |
| `.anmi-modal-video` | Preview video | Modal | `contain` |
| `.anmi-modal-iframe` | Preview iframe | Modal | `contain` |

---

## 🚀 Maintenance Notes

### **When adding new styles:**
1. Determine if it's for **production** or **modal preview**
2. Use appropriate base class (`.anmi-banner-*`) or override class (`.anmi-modal-*`)
3. Add `!important` only for modal overrides
4. Test both production and modal contexts

### **When debugging:**
1. Check which classes are applied (DevTools → Elements)
2. Verify no inline styles exist
3. Check computed styles (DevTools → Computed)
4. Ensure `:not(.anmi-modal-iframe)` selector works

---

**Last Updated:** 2025-11-03  
**Version:** 1.6.6  
**Author:** An Mi Tools Technical Team
