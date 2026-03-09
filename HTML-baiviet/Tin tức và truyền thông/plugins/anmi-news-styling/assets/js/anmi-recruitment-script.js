/**
 * AnMi Recruitment Script
 * Plugin : AnMi News Styling
 * Scope  : trang tuyển dụng (.anmi-recruitment-page)
 */
(function ($) {
  'use strict';

  /* ── Highlight table row on click (mobile UX) ──────────────── */
  $(document).on('click', '.job-wrap table tbody tr', function () {
    $(this).toggleClass('table-active');
  });

  /* ── Smooth scroll for any in-page anchor links ─────────────── */
  $(document).on('click', 'a[href^="#"]', function (e) {
    var target = $(this.getAttribute('href'));
    if (target.length) {
      e.preventDefault();
      $('html, body').animate({ scrollTop: target.offset().top - 80 }, 400);
    }
  });

})(jQuery);
