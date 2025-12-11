/**
 * AnMi News Script - JavaScript cho các trang tin tức
 * Version: 1.0.0
 * Author: Thanh - Content Marketing / Nội dung Kỹ thuật
 */

(function($) {
    'use strict';

    // Wait for DOM to be ready
    $(document).ready(function() {
        
        /**
         * Initialize AnMi News features
         */
        const AnMiNews = {
            
            /**
             * Initialize all features
             */
            init: function() {
                this.setupLightbox();
                this.setupSmoothScroll();
                this.setupImageLazyLoad();
                this.setupReadingProgress();
                this.setupPrintButton();
                this.setupShareButtons();
                this.trackAnalytics();
                
                console.log('AnMi News Script initialized');
            },
            
            /**
             * Configure Lightbox
             */
            setupLightbox: function() {
                if (typeof lightbox !== 'undefined') {
                    lightbox.option({
                        'resizeDuration': 200,
                        'wrapAround': true,
                        'albumLabel': 'Ảnh %1 / %2',
                        'fadeDuration': 300,
                        'imageFadeDuration': 300,
                        'disableScrolling': true,
                        'fitImagesInViewport': true,
                        'maxWidth': 1200,
                        'maxHeight': 800,
                        'positionFromTop': 50,
                        'showImageNumberLabel': true
                    });
                    
                    console.log('Lightbox configured');
                }
            },
            
            /**
             * Smooth scroll to anchor links
             */
            setupSmoothScroll: function() {
                $('a[href^="#"]').on('click', function(e) {
                    const target = $(this.getAttribute('href'));
                    
                    if (target.length) {
                        e.preventDefault();
                        
                        $('html, body').stop().animate({
                            scrollTop: target.offset().top - 100
                        }, 800, 'swing');
                    }
                });
                
                console.log('Smooth scroll enabled');
            },
            
            /**
             * Lazy load images (native browser support)
             */
            setupImageLazyLoad: function() {
                const images = document.querySelectorAll('img[loading="lazy"]');
                
                if ('loading' in HTMLImageElement.prototype) {
                    // Browser supports native lazy loading
                    console.log('Native lazy loading supported');
                } else {
                    // Fallback for older browsers
                    if (typeof IntersectionObserver !== 'undefined') {
                        const imageObserver = new IntersectionObserver((entries, observer) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    const img = entry.target;
                                    img.src = img.dataset.src || img.src;
                                    img.classList.add('loaded');
                                    observer.unobserve(img);
                                }
                            });
                        });
                        
                        images.forEach(img => imageObserver.observe(img));
                        console.log('IntersectionObserver lazy loading enabled');
                    }
                }
            },
            
            /**
             * Reading progress indicator
             */
            setupReadingProgress: function() {
                // Create progress bar
                const progressBar = $('<div class="anmi-reading-progress"></div>');
                progressBar.css({
                    'position': 'fixed',
                    'top': '0',
                    'left': '0',
                    'width': '0%',
                    'height': '4px',
                    'background': 'linear-gradient(90deg, #003087, #e31e24)',
                    'z-index': '9999',
                    'transition': 'width 0.2s ease'
                });
                $('body').prepend(progressBar);
                
                // Update progress on scroll
                $(window).on('scroll', function() {
                    const windowHeight = $(window).height();
                    const documentHeight = $(document).height();
                    const scrollTop = $(window).scrollTop();
                    const progress = (scrollTop / (documentHeight - windowHeight)) * 100;
                    
                    progressBar.css('width', progress + '%');
                });
                
                console.log('Reading progress indicator added');
            },
            
            /**
             * Add print button
             */
            setupPrintButton: function() {
                const printButton = $('<button class="anmi-print-btn" title="In bài viết">🖨️ In</button>');
                printButton.css({
                    'position': 'fixed',
                    'bottom': '20px',
                    'right': '20px',
                    'padding': '12px 20px',
                    'background-color': '#003087',
                    'color': '#ffffff',
                    'border': 'none',
                    'border-radius': '8px',
                    'cursor': 'pointer',
                    'font-size': '14px',
                    'font-weight': '600',
                    'box-shadow': '0 4px 8px rgba(0, 0, 0, 0.2)',
                    'z-index': '1000',
                    'transition': 'all 0.3s ease'
                });
                
                printButton.hover(
                    function() {
                        $(this).css({
                            'background-color': '#002566',
                            'transform': 'translateY(-2px)',
                            'box-shadow': '0 6px 12px rgba(0, 0, 0, 0.3)'
                        });
                    },
                    function() {
                        $(this).css({
                            'background-color': '#003087',
                            'transform': 'translateY(0)',
                            'box-shadow': '0 4px 8px rgba(0, 0, 0, 0.2)'
                        });
                    }
                );
                
                printButton.on('click', function() {
                    window.print();
                });
                
                $('body').append(printButton);
                console.log('Print button added');
            },
            
            /**
             * Add social share buttons
             */
            setupShareButtons: function() {
                const shareContainer = $('<div class="anmi-share-buttons"></div>');
                shareContainer.css({
                    'position': 'fixed',
                    'bottom': '80px',
                    'right': '20px',
                    'display': 'flex',
                    'flex-direction': 'column',
                    'gap': '10px',
                    'z-index': '1000'
                });
                
                const pageUrl = encodeURIComponent(window.location.href);
                const pageTitle = encodeURIComponent(document.title);
                
                const shareButtons = [
                    {
                        name: 'Facebook',
                        icon: '📘',
                        url: `https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`,
                        color: '#1877f2'
                    },
                    {
                        name: 'LinkedIn',
                        icon: '💼',
                        url: `https://www.linkedin.com/sharing/share-offsite/?url=${pageUrl}`,
                        color: '#0077b5'
                    },
                    {
                        name: 'Email',
                        icon: '📧',
                        url: `mailto:?subject=${pageTitle}&body=${pageUrl}`,
                        color: '#666666'
                    }
                ];
                
                shareButtons.forEach(btn => {
                    const button = $(`<a href="${btn.url}" target="_blank" rel="noopener noreferrer" class="anmi-share-btn" title="Chia sẻ qua ${btn.name}">${btn.icon}</a>`);
                    button.css({
                        'display': 'flex',
                        'align-items': 'center',
                        'justify-content': 'center',
                        'width': '45px',
                        'height': '45px',
                        'background-color': btn.color,
                        'color': '#ffffff',
                        'border-radius': '50%',
                        'font-size': '20px',
                        'text-decoration': 'none',
                        'box-shadow': '0 4px 8px rgba(0, 0, 0, 0.2)',
                        'transition': 'all 0.3s ease'
                    });
                    
                    button.hover(
                        function() {
                            $(this).css({
                                'transform': 'translateY(-2px) scale(1.1)',
                                'box-shadow': '0 6px 12px rgba(0, 0, 0, 0.3)'
                            });
                        },
                        function() {
                            $(this).css({
                                'transform': 'translateY(0) scale(1)',
                                'box-shadow': '0 4px 8px rgba(0, 0, 0, 0.2)'
                            });
                        }
                    );
                    
                    shareContainer.append(button);
                });
                
                $('body').append(shareContainer);
                console.log('Share buttons added');
            },
            
            /**
             * Track analytics events
             */
            trackAnalytics: function() {
                // Track article view
                this.trackEvent('article_view', {
                    'article_title': document.title,
                    'article_url': window.location.href
                });
                
                // Track image clicks
                $('.image-wrapper a, .image-wrapper-full a').on('click', function() {
                    const imageSrc = $(this).find('img').attr('src');
                    AnMiNews.trackEvent('image_click', {
                        'image_src': imageSrc
                    });
                });
                
                // Track time spent on page
                let startTime = Date.now();
                $(window).on('beforeunload', function() {
                    const timeSpent = Math.round((Date.now() - startTime) / 1000);
                    AnMiNews.trackEvent('time_on_page', {
                        'seconds': timeSpent
                    });
                });
                
                console.log('Analytics tracking enabled');
            },
            
            /**
             * Generic event tracking
             */
            trackEvent: function(eventName, eventData) {
                // Google Analytics (if available)
                if (typeof gtag !== 'undefined') {
                    gtag('event', eventName, eventData);
                }
                
                // Facebook Pixel (if available)
                if (typeof fbq !== 'undefined') {
                    fbq('trackCustom', eventName, eventData);
                }
                
                // Console log for debugging
                console.log('Event tracked:', eventName, eventData);
            }
        };
        
        // Initialize
        AnMiNews.init();
        
        /**
         * Responsive mobile menu toggle (if needed)
         */
        $('.anmi-mobile-menu-toggle').on('click', function() {
            $('.anmi-mobile-menu').toggleClass('active');
        });
        
        /**
         * Back to top button
         */
        const backToTopBtn = $('<button class="anmi-back-to-top" title="Lên đầu trang">↑</button>');
        backToTopBtn.css({
            'position': 'fixed',
            'bottom': '20px',
            'left': '20px',
            'width': '45px',
            'height': '45px',
            'background-color': '#e31e24',
            'color': '#ffffff',
            'border': 'none',
            'border-radius': '50%',
            'cursor': 'pointer',
            'font-size': '24px',
            'font-weight': 'bold',
            'box-shadow': '0 4px 8px rgba(0, 0, 0, 0.2)',
            'z-index': '1000',
            'opacity': '0',
            'visibility': 'hidden',
            'transition': 'all 0.3s ease'
        });
        
        backToTopBtn.on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 600);
        });
        
        $(window).on('scroll', function() {
            if ($(window).scrollTop() > 300) {
                backToTopBtn.css({
                    'opacity': '1',
                    'visibility': 'visible'
                });
            } else {
                backToTopBtn.css({
                    'opacity': '0',
                    'visibility': 'hidden'
                });
            }
        });
        
        $('body').append(backToTopBtn);
        
    }); // End document ready

})(jQuery);
