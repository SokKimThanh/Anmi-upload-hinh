# 15-Day Learning Plan: Build a WordPress Download Center

## Introduction  
This document guides you step by step from basic concepts to an advanced, production-ready Download Center in WordPress. Over 15 days, you’ll cover theory, hands-on exercises, and focused tasks to absorb each topic effectively.

---

## Prerequisites  
- A local or remote WordPress installation (latest version)  
- Administrator access to the WP dashboard and server (FTP or SSH)  
- Basic knowledge of PHP, HTML, CSS  
- A code editor (VS Code, PhpStorm, etc.)  
- Familiarity with Git (optional but recommended)  

---

## Plan Overview  
Each day is split into two parts:  
1. Theory & Concepts  
2. Practice Exercise  

Allocate 1–2 hours per day and limit scope to fully absorb the material before moving on.

---

## Daily Schedule

### Day 1: WordPress Fundamentals  
Theory & Concepts  
- CMS architecture: themes, plugins, hooks, template hierarchy  
- How WP handles content and URLs  

Practice Exercise  
1. Install WP locally (e.g., Local by Flywheel or XAMPP)  
2. Explore core folders: wp-content, wp-includes, wp-admin  

---

### Day 2: Child Themes & Customization  
Theory & Concepts  
- Child theme purpose and structure  
- Enqueuing styles and scripts  

Practice Exercise  
1. Create a child theme of Twenty Twenty-Three  
2. Enqueue a custom stylesheet and JS file  

---

### Day 3: Custom Post Types & Taxonomies  
Theory & Concepts  
- register_post_type() and register_taxonomy() functions  
- When to use CPT vs. categories/tags  

Practice Exercise  
1. Register a “Download” CPT  
2. Create taxonomies “Document Type” and “Topic”  

---

### Day 4: Advanced Custom Fields (ACF)  
Theory & Concepts  
- ACF plugin overview and field groups  
- Field types for file uploads, selects, text  

Practice Exercise  
1. Install and activate ACF  
2. Add file, text, and select fields to the Download CPT  

---

### Day 5: Media Library & File Management  
Theory & Concepts  
- WP Media Library vs. direct uploads via FTP  
- File URL structure and mime types  

Practice Exercise  
1. Upload a PDF and image via Media Library  
2. Link the media item to a Download post  

---

### Day 6: Plugin-Based Download Center Exploration  
Theory & Concepts  
- Pros and cons of using a dedicated plugin  
- Overview: Download Monitor, WP File Download, Easy Digital Downloads  

Practice Exercise  
1. Install Download Monitor plugin  
2. Create a sample download entry and test basic download link  

---

### Day 7: Customizing Download Monitor  
Theory & Concepts  
- Shortcodes, templates, and hooks provided by the plugin  
- Tracking and limiting downloads  

Practice Exercise  
1. Display download list via shortcode on a page  
2. Enable and view download statistics  

---

### Day 8: Coding a Manual CPT Download Center  
Theory & Concepts  
- Why build custom vs. plugin approach  
- Key functions: add_meta_box(), save_post(), wp_nonce_field()  

Practice Exercise  
1. Add a meta box for file URL input in Download CPT  
2. Save and retrieve the file URL on the front end  

---

### Day 9: Metadata & File Attachments  
Theory & Concepts  
- wp_handle_upload() function  
- Storing attachment IDs vs. raw URLs  

Practice Exercise  
1. Create a form to upload files directly in the WP admin  
2. Validate, save, and attach the file to the Download post  

---

### Day 10: Front-End Templates & Listings  
Theory & Concepts  
- WP Query basics for CPT loops  
- Template hierarchy for archive-download.php  

Practice Exercise  
1. Build an archive template listing all downloads  
2. Include title, excerpt, download button  

---

### Day 11: Filtering & Search  
Theory & Concepts  
- Custom query parameters and tax_query  
- Integrating WP Search or FacetWP  

Practice Exercise  
1. Add dropdown filters by “Document Type” and “Topic”  
2. Implement AJAX search for downloads  

---

### Day 12: Permissions & Access Control  
Theory & Concepts  
- current_user_can() and custom capabilities  
- Restricting content based on roles  

Practice Exercise  
1. Allow only logged-in users to download  
2. Create a new role “Subscriber” with download privileges  

---

### Day 13: Security & Performance  
Theory & Concepts  
- Nonces, sanitization, and capability checks  
- Caching strategies for query results  

Practice Exercise  
1. Add nonce checks to file download handler  
2. Cache download listings with transient API  

---

### Day 14: UX, Styling & Responsiveness  
Theory & Concepts  
- Mobile-first design principles  
- Accessible buttons and ARIA labels  

Practice Exercise  
1. Style the Download Center page with CSS or a front-end framework  
2. Test on mobile viewport and adjust as needed  

---

### Day 15: Testing, Deployment & Documentation  
Theory & Concepts  
- Cross-browser testing, backups, and migration  
- Creating user documentation and changelogs  

Practice Exercise  
1. Test all download links, filters, and permissions  
2. Write a README.md summarizing setup, features, and maintenance  

---

## Next Steps  
- Explore integration with email workflows (notify on new downloads)  
- Advanced: implement paid downloads via Easy Digital Downloads  
- Gather user feedback and iterate on UI/UX  

Save this file as `download-center-plan.md` and follow each day’s steps to build a robust, user-friendly Download Center in WordPress. Good luck!  
