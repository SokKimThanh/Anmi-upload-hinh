/**
 * Grid Cleanup Script for An Mi Product Pages
 * 
 * Removes ONLY WordPress auto-generated <p> tags that are EMPTY or contain COMMENTS
 * PRESERVES <p> tags with actual text content (product descriptions, etc.)
 * 
 * WordPress Editor auto-wraps HTML comments in <p> tags:
 * Example: <p><!-- Card 1: Description --></p>
 * This breaks CSS Grid layout by adding unwanted grid children
 * 
 * @version 1.1.0
 * @since 2.1.2
 * @author An Mi Tools Technical Team
 */

(function() {
    'use strict';
    
    /**
     * Grid container selectors that need cleanup
     */
    const GRID_SELECTORS = [
        '.feature-grid',
        '.application-grid',
        '.performance-grid',
        '.instruction-grid',
        '.support-grid',
        '.contact-info',
        '.spec-grid',
        '.grid'
    ];
    
    /**
     * Check if a <p> tag only contains HTML comments (no visible text)
     * WordPress Editor wraps comments in <p><!-- comment --></p>
     * @param {HTMLElement} p - The <p> element to check
     * @returns {boolean} True if only contains comments or whitespace
     */
    function onlyContainsCommentsOrEmpty(p) {
        // Get all child nodes including text and comment nodes
        const childNodes = Array.from(p.childNodes);
        
        // If no child nodes, it's empty
        if (childNodes.length === 0) {
            return true;
        }
        
        // Check if has any visible text content
        const textContent = p.textContent.trim();
        if (textContent) {
            // Has actual text content → KEEP IT
            return false;
        }
        
        // No visible text, check if only contains comments and/or whitespace
        return childNodes.every(node => {
            if (node.nodeType === 8) { // Comment node
                return true;
            }
            if (node.nodeType === 3) { // Text node
                // Check if only whitespace/nbsp
                return !node.textContent.trim() || /^[\s\u00A0\u1680\u2000-\u200B\u202F\u205F\u3000]*$/.test(node.textContent);
            }
            // Has other elements (like <span>, <strong>) → KEEP IT
            return false;
        });
    }
    
    /**
     * Clean up WordPress auto-generated <p> tags in grid containers
     * ONLY removes <p> tags that contain ONLY comments or are empty
     * PRESERVES <p> tags with actual text content
     */
    function cleanupGridParagraphs() {
        let removedCount = 0;
        let preservedCount = 0;
        
        GRID_SELECTORS.forEach(selector => {
            const grids = document.querySelectorAll(selector);
            
            grids.forEach(grid => {
                // Get all direct <p> children of grid container
                const paragraphs = grid.querySelectorAll(':scope > p');
                
                paragraphs.forEach(p => {
                    if (onlyContainsCommentsOrEmpty(p)) {
                        // Remove if ONLY contains comments or empty
                        if (window.anmiDebug) {
                            console.log('[An Mi Grid Cleanup] ❌ Removing comment/empty <p>:', p);
                        }
                        p.remove();
                        removedCount++;
                    } else {
                        // PRESERVE if has actual text content
                        if (window.anmiDebug) {
                            console.log('[An Mi Grid Cleanup] ✅ Preserving <p> with content:', p.textContent.substring(0, 50) + '...');
                        }
                        preservedCount++;
                    }
                });
            });
        });
        
        if (window.anmiDebug) {
            console.log(`[An Mi Grid Cleanup] Summary: Removed ${removedCount} comment/empty <p>, Preserved ${preservedCount} content <p>`);
        }
        
        return { removed: removedCount, preserved: preservedCount };
    }
    
    /**
     * Initialize cleanup when DOM is ready
     */
    function init() {
        // Run cleanup on DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', cleanupGridParagraphs);
        } else {
            // DOM already loaded, run immediately
            cleanupGridParagraphs();
        }
        
        // Also run after a short delay to catch dynamic content
        setTimeout(cleanupGridParagraphs, 100);
        setTimeout(cleanupGridParagraphs, 500);
    }
    
    // Enable debug mode in console with: window.anmiDebug = true;
    window.anmiDebug = false;
    
    // Start initialization
    init();
    
})();
