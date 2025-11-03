/**
 * AN MI VIDEO BANNER PLUGIN JAVASCRIPT
 * Version: 1.1.0 - With Image Slider Support
 */

(function($) {
    'use strict';
    
    class AnMiVideoBanner {
        constructor(container) {
            this.$container = $(container);
            this.$video = this.$container.find('.anmi-banner-video');
            this.$slider = this.$container.find('.anmi-banner-slider');
            this.$slides = this.$container.find('.anmi-slider-slide');
            this.$dots = this.$container.find('.anmi-slider-dots .dot');
            this.$loader = this.$container.find('.anmi-banner-loader');
            
            this.autoplayDelay = parseInt(this.$container.data('autoplay-delay')) || 0;
            this.mobileBehavior = this.$container.data('mobile-behavior') || 'image';
            this.sliderSpeed = parseInt(this.$container.data('slider-speed')) || 3000;
            this.sliderEffect = this.$container.data('slider-effect') || 'fade';
            this.videoType = this.$container.data('video-type') || 'direct'; // youtube, vimeo, or direct
            
            this.isVideoReady = false;
            this.hoverTimeout = null;
            this.sliderInterval = null;
            this.currentSlide = 0;
            this.isHovering = false;
            
            this.init();
        }
        
        init() {
            // Check if mobile
            if (this.isMobile() && this.mobileBehavior === 'image') {
                this.disableVideoOnMobile();
                this.startSlider(); // Still run slider on mobile
                return;
            }
            
            // For YouTube/Vimeo iframe, mark as ready immediately
            if (this.videoType === 'youtube' || this.videoType === 'vimeo') {
                this.isVideoReady = true;
                this.$loader = this.$container.find('.anmi-banner-loader');
                this.$loader.removeClass('active');
            } else {
                // Preload direct video
                this.preloadVideo();
            }
            
            // Setup events
            this.setupEvents();
            
            // Start image slider
            if (this.$slides.length > 1) {
                this.startSlider();
            }
        }
        
        isMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
                   window.innerWidth <= 768;
        }
        
        disableVideoOnMobile() {
            this.$video.remove();
        }
        
        /* ============================================ */
        /* SLIDER FUNCTIONALITY */
        /* ============================================ */
        
        startSlider() {
            // Auto-play slider
            this.sliderInterval = setInterval(() => {
                if (!this.isHovering) {
                    this.nextSlide();
                }
            }, this.sliderSpeed);
            
            // Dot navigation
            this.$dots.on('click', (e) => {
                const slideIndex = $(e.target).data('slide');
                this.goToSlide(slideIndex);
            });
        }
        
        stopSlider() {
            if (this.sliderInterval) {
                clearInterval(this.sliderInterval);
                this.sliderInterval = null;
            }
        }
        
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.$slides.length;
            this.goToSlide(this.currentSlide);
        }
        
        goToSlide(index) {
            this.currentSlide = index;
            
            // Update slides
            this.$slides.removeClass('active');
            this.$slides.eq(index).addClass('active');
            
            // Update dots
            this.$dots.removeClass('active');
            this.$dots.eq(index).addClass('active');
        }
        
        preloadVideo() {
            const video = this.$video[0];
            
            if (video) {
                // Show loader initially
                this.$loader.addClass('active');
                
                // Set timeout to hide loader after 3 seconds (fallback)
                const loaderTimeout = setTimeout(() => {
                    this.$loader.removeClass('active');
                    console.log('Video preload timeout - showing slider');
                }, 3000);
                
                // Video loaded event - hide loader immediately when ready
                video.addEventListener('loadeddata', () => {
                    this.isVideoReady = true;
                    clearTimeout(loaderTimeout);
                    this.$loader.removeClass('active');
                    console.log('Video ready to play');
                });
                
                // Video can play through - optimal state
                video.addEventListener('canplaythrough', () => {
                    this.isVideoReady = true;
                    clearTimeout(loaderTimeout);
                    this.$loader.removeClass('active');
                    console.log('Video fully loaded');
                });
                
                // Error handling
                video.addEventListener('error', (e) => {
                    console.error('Video load error:', e);
                    clearTimeout(loaderTimeout);
                    this.$loader.removeClass('active');
                    this.disableVideoOnMobile(); // Fallback to image
                });
                
                // Start loading video
                video.load();
            }
        }
        
        
        setupEvents() {
            const self = this;
            
            // Mouse enter event - Stop slider and play video
            this.$container.on('mouseenter', function() {
                self.isHovering = true;
                self.stopSlider(); // Pause slider when hovering
                
                if (self.autoplayDelay > 0) {
                    self.hoverTimeout = setTimeout(() => {
                        self.playVideo();
                    }, self.autoplayDelay * 1000);
                } else {
                    self.playVideo();
                }
            });
            
            // Mouse leave event - Resume slider and stop video
            this.$container.on('mouseleave', function() {
                self.isHovering = false;
                clearTimeout(self.hoverTimeout);
                self.pauseVideo();
                self.startSlider(); // Resume slider when not hovering
            });
            
            // Touch events for mobile (if enabled)
            if (this.mobileBehavior === 'video' || this.mobileBehavior === 'both') {
                this.$container.on('touchstart', function() {
                    if (self.$video.css('opacity') === '0') {
                        self.isHovering = true;
                        self.stopSlider();
                        self.playVideo();
                    } else {
                        self.isHovering = false;
                        self.pauseVideo();
                        self.startSlider();
                    }
                });
            }
        }
        
        playVideo() {
            if (!this.isVideoReady) {
                console.warn('Video not ready yet');
                return;
            }
            
            // For YouTube/Vimeo iframe, just show it (autoplay is handled by embed URL)
            if (this.videoType === 'youtube' || this.videoType === 'vimeo') {
                this.$video.css('opacity', '1');
                this.$slider.css('opacity', '0');
                console.log('Showing iframe video:', this.videoType);
                return;
            }
            
            // For direct video
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
            // For YouTube/Vimeo iframe, just hide it
            if (this.videoType === 'youtube' || this.videoType === 'vimeo') {
                this.$video.css('opacity', '0');
                this.$slider.css('opacity', '1');
                console.log('Hiding iframe video');
                return;
            }
            
            // For direct video
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
