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

  /* ── Region selector (CTA box) ───────────────────────────────── */
  $(document).on('click', '.region-btn', function () {
    var $btn   = $(this);
    var target = $btn.data('target');
    var $cta   = $btn.closest('.cta-box');

    // Đổi trạng thái nút
    $cta.find('.region-btn')
      .removeClass('btn-primary').addClass('btn-outline-primary')
      .attr('aria-pressed', 'false');
    $btn.removeClass('btn-outline-primary').addClass('btn-primary')
      .attr('aria-pressed', 'true');

    // Hiện panel được chọn, ẩn panel còn lại
    $cta.find('.region-panel').hide();
    $cta.find('.region-panel[data-region="' + target + '"]').show();
  });

})(jQuery);
