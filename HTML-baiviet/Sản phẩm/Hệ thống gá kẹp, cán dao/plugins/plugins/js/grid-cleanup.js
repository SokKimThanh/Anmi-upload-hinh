/**
 * Grid Cleanup Script Disabled
 *
 * The previous logic removed automatically generated <p> elements inside
 * grid containers. That behavior is no longer desired, so this script now
 * only logs a debug message when debug mode is enabled.
 */

(function() {
    'use strict';

    if (window && window.anmiDebug) {
        console.log('[An Mi Grid Cleanup] Script disabled; no changes applied.');
    }
})();
