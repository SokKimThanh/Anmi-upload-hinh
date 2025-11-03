/**
 * An Mi Tools - Image Lightbox
 * Simple lightbox for product images
 * Version: 1.0.0
 * Author: An Mi Tools Vietnam
 */

(function() {
    'use strict';
    
    // Create lightbox HTML structure
    function createLightbox() {
        if (document.getElementById('anmi-lightbox')) {
            return; // Already exists
        }
        
        const lightboxHTML = `
            <div id="anmi-lightbox" class="anmi-lightbox">
                <span class="anmi-lightbox-close">&times;</span>
                <img class="anmi-lightbox-content" id="anmi-lightbox-img" alt="">
                <div class="anmi-lightbox-caption"></div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', lightboxHTML);
    }
    
    // Initialize lightbox
    function initLightbox() {
        createLightbox();
        
        const lightbox = document.getElementById('anmi-lightbox');
        const lightboxImg = document.getElementById('anmi-lightbox-img');
        const lightboxCaption = lightbox.querySelector('.anmi-lightbox-caption');
        const closeBtn = lightbox.querySelector('.anmi-lightbox-close');
        
        // Get all product images (inside figure tags with bordered-img class)
        const productImages = document.querySelectorAll('figure img.bordered-img, .product-images-grid img.bordered-img');
        
        // Add click event to each image
        productImages.forEach(function(img) {
            img.style.cursor = 'pointer';
            img.setAttribute('title', 'Click để phóng to');
            
            img.addEventListener('click', function() {
                lightbox.style.display = 'block';
                lightboxImg.src = this.src;
                lightboxImg.alt = this.alt;
                
                // Get caption from figcaption
                const figcaption = this.closest('figure')?.querySelector('figcaption');
                if (figcaption) {
                    lightboxCaption.textContent = figcaption.textContent;
                } else {
                    lightboxCaption.textContent = this.alt;
                }
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
            });
        });
        
        // Close lightbox on close button click
        closeBtn.addEventListener('click', function() {
            closeLightbox();
        });
        
        // Close lightbox on outside click
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
        
        // Close lightbox on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && lightbox.style.display === 'block') {
                closeLightbox();
            }
        });
        
        function closeLightbox() {
            lightbox.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLightbox);
    } else {
        initLightbox();
    }
    
})();
