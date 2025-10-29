/**
 * Grid Cleanup Script for An Mi Product Pages
 * 
 * Removes WordPress auto-generated <p> tags from grid containers
 * These <p> tags are created by WordPress Editor when:
 * 1. User adds HTML comments between div elements
 * 2. WordPress wpautop wraps comments in <p><!-- comment --></p>
 * 3. This breaks CSS Grid layout by adding unwanted grid children
 * 
 * @version 1.0.0
 * @since 2.1.1
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
     * Check if a <p> tag is empty or only contains comments/whitespace
     * @param {HTMLElement} p - The <p> element to check
     * @returns {boolean} True if <p> is removable
     */
    function isParagraphRemovable(p) {
        // Check if empty
        if (!p.textContent.trim()) {
            return true;
        }
        
        // Check if only contains &nbsp;
        if (p.innerHTML.trim() === '&nbsp;' || p.innerHTML.trim() === '&#160;') {
            return true;
        }
        
        // Check if only contains whitespace characters
        if (/^[\s\u00A0\u1680\u2000-\u200B\u202F\u205F\u3000]*$/.test(p.textContent)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if a <p> tag only contains HTML comments
     * WordPress Editor wraps comments in <p><!-- comment --></p>
     * @param {HTMLElement} p - The <p> element to check
     * @returns {boolean} True if only contains comments
     */
    function onlyContainsComments(p) {
        // Get all child nodes including text and comment nodes
        const childNodes = Array.from(p.childNodes);
        
        // If no child nodes, it's empty
        if (childNodes.length === 0) {
            return true;
        }
        
        // Check if all children are either:
        // 1. Comment nodes (nodeType === 8)
        // 2. Empty text nodes (nodeType === 3 with only whitespace)
        return childNodes.every(node => {
            if (node.nodeType === 8) { // Comment node
                return true;
            }
            if (node.nodeType === 3) { // Text node
                return !node.textContent.trim(); // Empty or whitespace only
            }
            return false; // Other node types (elements, etc.)
        });
    }
    
    /**
     * Clean up WordPress auto-generated <p> tags in grid containers
     */
    function cleanupGridParagraphs() {
        let removedCount = 0;
        
        GRID_SELECTORS.forEach(selector => {
            const grids = document.querySelectorAll(selector);
            
            grids.forEach(grid => {
                // Get all direct <p> children of grid container
                const paragraphs = grid.querySelectorAll(':scope > p');
                
                paragraphs.forEach(p => {
                    // Remove if empty/whitespace only OR only contains comments
                    if (isParagraphRemovable(p) || onlyContainsComments(p)) {
                        if (window.anmiDebug) {
                            console.log('[An Mi Grid Cleanup] Removing auto-generated <p>:', p);
                        }
                        p.remove();
                        removedCount++;
                    }
                });
            });
        });
        
        if (removedCount > 0 && window.anmiDebug) {
            console.log(`[An Mi Grid Cleanup] Removed ${removedCount} auto-generated <p> tags from grids`);
        }
        
        return removedCount;
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
