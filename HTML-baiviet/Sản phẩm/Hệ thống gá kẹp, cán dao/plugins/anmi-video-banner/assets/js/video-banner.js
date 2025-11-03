/**
 * AN MI VIDEO BANNER PLUGIN JAVASCRIPT
 * Version: 1.0.0
 */

(function($) {
    'use strict';
    
    class AnMiVideoBanner {
        constructor(container) {
            this.$container = $(container);
            this.$video = this.$container.find('.anmi-banner-video');
            this.$image = this.$container.find('.anmi-banner-image');
            this.$loader = this.$container.find('.anmi-banner-loader');
            
            this.autoplayDelay = parseInt(this.$container.data('autoplay-delay')) || 0;
            this.mobileBehavior = this.$container.data('mobile-behavior') || 'image';
            this.isVideoReady = false;
            this.hoverTimeout = null;
            
            this.init();
        }
        
        init() {
            // Check if mobile
            if (this.isMobile() && this.mobileBehavior === 'image') {
                this.disableVideoOnMobile();
                return;
            }
            
            // Preload video
            this.preloadVideo();
            
            // Setup events
            this.setupEvents();
            
            // Video ready check
            this.checkVideoReady();
        }
        
        isMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
                   window.innerWidth <= 768;
        }
        
        disableVideoOnMobile() {
            this.$video.remove();
            this.$image.css('opacity', '1');
        }
        
        preloadVideo() {
            const video = this.$video[0];
            
            if (video) {
                // Show loader
                this.$loader.addClass('active');
                
                // Load video metadata
                video.load();
                
                // Video loaded event
                video.addEventListener('loadeddata', () => {
                    this.isVideoReady = true;
                    this.$loader.removeClass('active');
                    console.log('Video ready to play');
                });
                
                // Error handling
                video.addEventListener('error', (e) => {
                    console.error('Video load error:', e);
                    this.$loader.removeClass('active');
                    this.disableVideoOnMobile(); // Fallback to image
                });
            }
        }
        
        setupEvents() {
            const self = this;
            
            // Mouse enter event
            this.$container.on('mouseenter', function() {
                if (self.autoplayDelay > 0) {
                    self.hoverTimeout = setTimeout(() => {
                        self.playVideo();
                    }, self.autoplayDelay * 1000);
                } else {
                    self.playVideo();
                }
            });
            
            // Mouse leave event
            this.$container.on('mouseleave', function() {
                clearTimeout(self.hoverTimeout);
                self.pauseVideo();
            });
            
            // Touch events for mobile (if enabled)
            if (this.mobileBehavior === 'video' || this.mobileBehavior === 'both') {
                this.$container.on('touchstart', function() {
                    if (self.$video.css('opacity') === '0') {
                        self.playVideo();
                    } else {
                        self.pauseVideo();
                    }
                });
            }
        }
        
        playVideo() {
            if (!this.isVideoReady) {
                console.warn('Video not ready yet');
                return;
            }
            
            const video = this.$video[0];
            
            if (video) {
                const playPromise = video.play();
                
                if (playPromise !== undefined) {
                    playPromise
                        .then(() => {
                            console.log('Video playing');
                        })
                        .catch(error => {
                            console.error('Video play error:', error);
                            // Try to play muted
                            video.muted = true;
                            video.play();
                        });
                }
            }
        }
        
        pauseVideo() {
            const video = this.$video[0];
            
            if (video) {
                video.pause();
                video.currentTime = 0; // Reset to beginning
            }
        }
        
        checkVideoReady() {
            const video = this.$video[0];
            
            if (video && video.readyState >= 3) {
                // Video can play through
                this.isVideoReady = true;
                this.$loader.removeClass('active');
            }
        }
    }
    
    // Initialize all video banners on page load
    $(document).ready(function() {
        $('.anmi-video-banner-container').each(function() {
            new AnMiVideoBanner(this);
        });
    });
    
    // Reinitialize for dynamically loaded content (AJAX, Elementor preview, etc.)
    $(window).on('load', function() {
        $('.anmi-video-banner-container').each(function() {
            if (!$(this).data('anmi-initialized')) {
                new AnMiVideoBanner(this);
                $(this).data('anmi-initialized', true);
            }
        });
    });
    
    // Elementor preview support
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
            $scope.find('.anmi-video-banner-container').each(function() {
                new AnMiVideoBanner(this);
            });
        });
    });
    
})(jQuery);
