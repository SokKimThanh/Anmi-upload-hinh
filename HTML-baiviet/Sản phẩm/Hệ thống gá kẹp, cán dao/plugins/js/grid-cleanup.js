/**
 * Grid Cleanup Script for An Mi Product Pages
 * 
 * Removes ONLY WordPress auto-generated <p> tags that are INSIDE GRID CONTAINERS
 * and ONLY contain COMMENTS or are EMPTY
 * 
 * PRESERVES ALL <p> tags with actual text content EVERYWHERE
 * PRESERVES ALL <p> tags OUTSIDE grid containers (product descriptions, etc.)
 * 
 * WordPress Editor auto-wraps HTML comments in <p> tags:
 * Example: <p><!-- Card 1: Description --></p>
 * This breaks CSS Grid layout by adding unwanted grid children
 * 
 * @version 1.2.0
 * @since 2.1.6
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
     * Check if a <p> tag only contains HTML comments or is empty (no visible text)
     * WordPress Editor wraps comments in <p><!-- comment --></p>
     * @param {HTMLElement} p - The <p> element to check
     * @returns {boolean} True if only contains comments or whitespace
     */
    function onlyContainsCommentsOrEmpty(p) {
        // Get text content (excludes comments)
        const textContent = p.textContent.trim();
        
        // If has ANY visible text content → KEEP IT
        if (textContent.length > 0) {
            return false;
        }
        
        // If has child elements (like <strong>, <em>, <a>, etc.) → KEEP IT
        if (p.children.length > 0) {
            return false;
        }
        
        // Check all child nodes
        const childNodes = Array.from(p.childNodes);
        
        // If completely empty → REMOVE
        if (childNodes.length === 0) {
            return true;
        }
        
        // Check if ONLY contains comments and/or whitespace
        return childNodes.every(node => {
            if (node.nodeType === 8) { // Comment node
                return true;
            }
            if (node.nodeType === 3) { // Text node
                // Only whitespace/nbsp → can remove
                const text = node.textContent.trim();
                return text.length === 0;
            }
            // Has other node types (elements) → KEEP IT
            return false;
        });
    }
    
    /**
     * Clean up WordPress auto-generated <p> tags ONLY in grid containers
     * ONLY removes <p> tags that:
     *   1. Are DIRECT children of grid containers
     *   2. Contain ONLY comments or are empty
     * 
     * PRESERVES ALL <p> tags with actual text content
     * PRESERVES ALL <p> tags outside grid containers
     */
    function cleanupGridParagraphs() {
        let removedCount = 0;
        let preservedInGridCount = 0;
        let preservedOutsideGridCount = 0;
        
        GRID_SELECTORS.forEach(selector => {
            const grids = document.querySelectorAll(selector);
            
            grids.forEach(grid => {
                // ✅ IMPORTANT: Only get DIRECT <p> children of grid container
                // This ensures we don't touch <p> tags in product descriptions or other content
                const paragraphs = grid.querySelectorAll(':scope > p');
                
                paragraphs.forEach(p => {
                    if (onlyContainsCommentsOrEmpty(p)) {
                        // ❌ Remove if ONLY contains comments or empty
                        if (window.anmiDebug) {
                            console.log('[An Mi Grid Cleanup] ❌ Removing comment/empty <p> in grid:', p);
                        }
                        p.remove();
                        removedCount++;
                    } else {
                        // ✅ PRESERVE if has actual text content or child elements
                        if (window.anmiDebug) {
                            const preview = p.textContent.substring(0, 60).replace(/\n/g, ' ');
                            console.log('[An Mi Grid Cleanup] ✅ Preserving <p> with content in grid:', preview + '...');
                        }
                        preservedInGridCount++;
                    }
                });
            });
        });
        
        // Count <p> tags outside grid containers (these are NEVER touched)
        const allParagraphs = document.querySelectorAll('p');
        const gridParagraphs = document.querySelectorAll(GRID_SELECTORS.map(s => s + ' > p').join(', '));
        preservedOutsideGridCount = allParagraphs.length - gridParagraphs.length;
        
        if (window.anmiDebug || removedCount > 0) {
            console.log(`[An Mi Grid Cleanup] Summary:
  ❌ Removed: ${removedCount} comment/empty <p> in grids
  ✅ Preserved: ${preservedInGridCount} content <p> in grids
  ✅ Preserved: ${preservedOutsideGridCount} <p> outside grids (never touched)
  📊 Total <p> on page: ${allParagraphs.length}`);
        }
        
        return { 
            removed: removedCount, 
            preservedInGrid: preservedInGridCount,
            preservedOutside: preservedOutsideGridCount
        };
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
