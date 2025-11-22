/**
 * Tab Navigation Functionality
 * File: tab-navigation.js
 * Version: 1.0.0
 * Created: November 17, 2025
 * Author: An Mi Tools Technical Team
 * 
 * Purpose: Handles tab switching, state persistence, and smooth scrolling
 * Used with: anmi-holder-products.css tab navigation styles
 * 
 * Features:
 * - Click-to-switch tabs with active state management
 * - sessionStorage persistence (remembers last viewed tab per browser tab/session)
 * - Smooth scroll to tab content when switching
 * - Fade-in animation via CSS class toggle
 * - Keyboard navigation support (future enhancement)
 * 
 * Dependencies: None (vanilla JavaScript)
 * Browser Support: IE11+, Modern browsers
 */

(function() {
    'use strict';

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        initTabNavigation();
    });

    /**
     * Initialize tab navigation system
     */
    function initTabNavigation() {
        // Use the ARIA-based tablist if present (matches inline page script)
        const tabs = Array.from(document.querySelectorAll('.tab-buttons [role="tab"]'));
        const panels = Array.from(document.querySelectorAll('[role="tabpanel"]'));

        // If no ARIA tabs present, fallback to legacy selectors
        if (tabs.length === 0 || panels.length === 0) {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            if (tabButtons.length === 0 || tabContents.length === 0) return;
            // Attach simple handlers (legacy behavior)
            tabButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    handleTabClick(this, tabButtons, tabContents);
                });
            });
            restoreLastActiveTab(tabButtons, tabContents);
            addKeyboardNavigation(tabButtons, tabContents);
            return;
        }

        // Attach click + keyboard handlers to ARIA tabs
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                activateTab(tab, true);
            });

            tab.addEventListener('keydown', function(e) {
                const idx = tabs.indexOf(tab);
                let newIdx = null;

                switch (e.key) {
                    case 'ArrowLeft': newIdx = (idx - 1 + tabs.length) % tabs.length; break;
                    case 'ArrowRight': newIdx = (idx + 1) % tabs.length; break;
                    case 'Home': newIdx = 0; break;
                    case 'End': newIdx = tabs.length - 1; break;
                    case 'Enter':
                    case ' ':
                    case 'Spacebar': // legacy
                        activateTab(tab, true);
                        e.preventDefault();
                        return;
                }

                if (newIdx !== null) {
                    tabs[newIdx].focus();
                    e.preventDefault();
                }
            });
        });

        // Restore persisted tab (if available) or keep existing active state
        restoreLastActiveTabARIA(tabs, panels);
    }

    /**
     * Handle tab button click
     * @param {HTMLElement} clickedButton - The clicked tab button
     * @param {NodeList} allButtons - All tab buttons
     * @param {NodeList} allContents - All tab contents
     */
    function handleTabClick(clickedButton, allButtons, allContents) {
        const targetTab = clickedButton.getAttribute('data-tab');

        if (!targetTab) {
            console.warn('Tab button missing data-tab attribute');
            return;
        }

        // Remove active class from all buttons and contents
        allButtons.forEach(function(btn) {
            btn.classList.remove('active');
            btn.setAttribute('aria-selected', 'false');
        });

        allContents.forEach(function(content) {
            content.classList.remove('active');
        });

        // Add active class to clicked button and corresponding content
        clickedButton.classList.add('active');
        clickedButton.setAttribute('aria-selected', 'true');

        const targetContent = document.getElementById(targetTab);
        if (targetContent) {
            targetContent.classList.add('active');

            // Save active tab to localStorage
            saveActiveTab(targetTab);

            // Scroll to top of tab content smoothly
            // scrollToTabs();
        } else {
            console.warn('Tab content not found for:', targetTab);
        }
    }

    /**
     * Save active tab to localStorage
     * @param {string} tabId - The ID of the active tab
     */
    function saveActiveTab(tabId) {
        try {
            // Use sessionStorage so the tab selection is not stored long-term
            sessionStorage.setItem('anmi_activeTab', tabId);
        } catch (e) {
            // localStorage might be disabled or full
            console.warn('Could not save tab state to localStorage:', e);
        }
    }

    /**
     * Restore last active tab from localStorage
     * @param {NodeList} tabButtons - All tab buttons
     * @param {NodeList} tabContents - All tab contents
     */
    function restoreLastActiveTab(tabButtons, tabContents) {
        try {
            const savedTab = sessionStorage.getItem('anmi_activeTab');

            if (savedTab && document.getElementById(savedTab)) {
                // Remove active class from all
                tabButtons.forEach(function(btn) {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                });

                tabContents.forEach(function(content) {
                    content.classList.remove('active');
                });

                // Activate saved tab
                const savedButton = document.querySelector('[data-tab="' + savedTab + '"]');
                const savedContent = document.getElementById(savedTab);

                if (savedButton && savedContent) {
                    savedButton.classList.add('active');
                    savedButton.setAttribute('aria-selected', 'true');
                    savedContent.classList.add('active');
                }
            }
        } catch (e) {
            console.warn('Could not restore tab state from localStorage:', e);
        }
    }

    /**
     * Restore active tab for ARIA-based tabs
     */
    function restoreLastActiveTabARIA(tabs, panels) {
        try {
            const saved = sessionStorage.getItem('anmi_activeTab');
            if (saved) {
                // Look for tab whose aria-controls matches saved
                const savedTab = tabs.find(t => t.getAttribute('aria-controls') === saved || t.dataset.tab === saved);
                if (savedTab) {
                    activateTab(savedTab, false);
                    return;
                }
            }
        } catch (e) {
            // ignore
        }

        // If no saved tab, ensure the DOM's default active tab/panel state is respected.
        // If none marked active, activate first tab.
        const alreadyActive = tabs.some(t => t.classList.contains('active') || t.getAttribute('aria-selected') === 'true');
        if (!alreadyActive && tabs.length) {
            activateTab(tabs[0], false);
        }
    }

    /**
     * Scroll to tabs section smoothly
     */
    function scrollToTabs() {
        const tabsContainer = document.querySelector('.product-tabs');

        if (tabsContainer) {
            // Use smooth scroll if supported
            if ('scrollBehavior' in document.documentElement.style) {
                tabsContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else {
                // Fallback for older browsers
                tabsContainer.scrollIntoView(true);
            }
        }
    }

    /**
     * Add keyboard navigation support
     * @param {NodeList} tabButtons - All tab buttons
     * @param {NodeList} tabContents - All tab contents
     */
    function addKeyboardNavigation(tabButtons, tabContents) {
        const buttonsArray = Array.from(tabButtons);

        buttonsArray.forEach(function(button, index) {
            button.addEventListener('keydown', function(e) {
                let targetIndex = -1;

                // Arrow Right or Down: Next tab
                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    targetIndex = (index + 1) % buttonsArray.length;
                }
                // Arrow Left or Up: Previous tab
                else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    targetIndex = (index - 1 + buttonsArray.length) % buttonsArray.length;
                }
                // Home: First tab
                else if (e.key === 'Home') {
                    e.preventDefault();
                    targetIndex = 0;
                }
                // End: Last tab
                else if (e.key === 'End') {
                    e.preventDefault();
                    targetIndex = buttonsArray.length - 1;
                }

                // Activate target tab if valid
                if (targetIndex >= 0) {
                    buttonsArray[targetIndex].focus();
                    buttonsArray[targetIndex].click();
                }
            });
        });
    }

    /**
     * Public API (if needed for external access)
     */
    window.AnmiTabNavigation = {
        version: '1.0.0',
        init: initTabNavigation,
        saveActiveTab: saveActiveTab
    };

})();
