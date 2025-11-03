/**
 * AN MI VIDEO BANNER PLUGIN JAVASCRIPT
 * Version: 1.6.11 - Mobile Tap-to-Play Fix
 */

(function($) {
    'use strict';
    
    class AnMiVideoBanner {
        constructor(container) {
            this.$container = $(container);
            this.$video = this.$container.find('.anmi-banner-video');
            this.$images = this.$container.find('.anmi-banner-image');
            this.$playOverlay = this.$container.find('.anmi-play-overlay');
            this.$dots = this.$container.find('.anmi-banner-dot');
            this.$loader = this.$container.find('.anmi-banner-loader');
            
            this.autoplayDelay = parseInt(this.$container.data('autoplay-delay')) || 0;
            this.mobileBehavior = this.$container.data('mobile-behavior') || 'image';
            this.sliderSpeed = parseInt(this.$container.data('slider-speed')) || 3000;
            this.sliderEffect = this.$container.data('slider-effect') || 'fade';
            this.videoType = this.$container.data('video-type') || 'direct';
            
            this.isVideoReady = false;
            this.sliderInterval = null;
            this.currentSlide = 0;
            this.isHovered = false;
            this.isVideoPlaying = false;
            
            this.init();
        }
        
        init() {
            // Check if mobile
            if (this.isMobile() && this.mobileBehavior === 'image') {
                this.disableVideoOnMobile();
                this.startSlider();
                return;
            }
            
            // Mark video as ready
            if (this.videoType === 'youtube' || this.videoType === 'vimeo') {
                this.isVideoReady = true;
                this.$loader.removeClass('active');
            } else {
                this.preloadVideo();
            }
            
            // Setup events
            this.setupEvents();
            
            // Start image slider
            if (this.$images.length > 1) {
                this.startSlider();
            }
        }
        
        isMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
                   window.innerWidth <= 768;
        }
        
        disableVideoOnMobile() {
            this.$video.remove();
            this.$playOverlay.remove();
        }
        
        /* ============================================ */
        /* SLIDER FUNCTIONALITY */
        /* ============================================ */
        
        startSlider() {
            const self = this;
            
            // Auto-play slider
            this.sliderInterval = setInterval(() => {
                if (!self.isHovered && !self.isVideoPlaying) {
                    self.nextSlide();
                }
            }, this.sliderSpeed);
            
            // Dot navigation
            this.$dots.on('click', function(e) {
                e.stopPropagation(); // Don't trigger container click
                
                if (self.sliderInterval) {
                    clearInterval(self.sliderInterval);
                }
                
                const slideIndex = $(this).data('slide');
                self.goToSlide(slideIndex);
                self.currentSlide = slideIndex;
            });
        }
        
        stopSlider() {
            if (this.sliderInterval) {
                clearInterval(this.sliderInterval);
                this.sliderInterval = null;
            }
        }
        
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.$images.length;
            this.goToSlide(this.currentSlide);
        }
        
        goToSlide(index) {
            // Update slides
            this.$images.css('opacity', '0');
            this.$images.eq(index).css('opacity', '1');
            
            // Update dots
            this.$dots.css('background', 'rgba(255,255,255,0.5)').removeClass('active');
            this.$dots.eq(index).css('background', '#fff').addClass('active');
        }
        
        
        preloadVideo() {
            const video = this.$video[0];
            
            if (video && video.tagName === 'VIDEO') {
                this.$loader.addClass('active');
                
                const loaderTimeout = setTimeout(() => {
                    this.$loader.removeClass('active');
                }, 3000);
                
                video.addEventListener('loadeddata', () => {
                    this.isVideoReady = true;
                    clearTimeout(loaderTimeout);
                    this.$loader.removeClass('active');
                });
                
                video.addEventListener('canplaythrough', () => {
                    this.isVideoReady = true;
                    clearTimeout(loaderTimeout);
                    this.$loader.removeClass('active');
                });
                
                video.addEventListener('error', (e) => {
                    console.error('Video load error:', e);
                    clearTimeout(loaderTimeout);
                    this.$loader.removeClass('active');
                    this.disableVideoOnMobile();
                });
                
                video.load();
            }
        }
        
        setupEvents() {
            const self = this;
            
            // Check if mobile device
            const isMobileDevice = this.isMobile();
            
            if (isMobileDevice) {
                // ============================================
                // MOBILE BEHAVIOR: Tap to play
                // ============================================
                this.$container.on('touchstart click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (!self.isVideoPlaying) {
                        // Stop slider
                        if (self.sliderInterval) {
                            clearInterval(self.sliderInterval);
                            self.sliderInterval = null;
                        }
                        
                        // Hide all images
                        self.$images.css('opacity', '0');
                        
                        // Show video
                        self.$video.css('opacity', '1');
                        
                        // Hide play button immediately
                        self.$playOverlay.css('opacity', '0').hide();
                        
                        // Play video
                        const video = self.$video[0];
                        if (video) {
                            if (video.tagName === 'VIDEO') {
                                // Direct video - play with error handling
                                const playPromise = video.play();
                                
                                if (playPromise !== undefined) {
                                    playPromise
                                        .then(() => {
                                            console.log('Mobile video playing');
                                            self.isVideoPlaying = true;
                                        })
                                        .catch((error) => {
                                            console.error('Mobile video play failed:', error);
                                            // Show play button again as fallback
                                            self.$playOverlay.css('opacity', '1').show();
                                        });
                                } else {
                                    self.isVideoPlaying = true;
                                }
                            } else if (video.tagName === 'IFRAME') {
                                // YouTube/Vimeo - autoplay handled by embed URL
                                self.isVideoPlaying = true;
                                console.log('Mobile iframe video shown');
                            }
                        }
                    } else {
                        // Video is playing - tap again to pause/reset
                        const video = self.$video[0];
                        if (video && video.tagName === 'VIDEO') {
                            video.pause();
                            video.currentTime = 0;
                        }
                        
                        // Reset to slider
                        self.$video.css('opacity', '0');
                        self.$images.css('opacity', '0');
                        self.$images.eq(self.currentSlide).css('opacity', '1');
                        self.$playOverlay.css('opacity', '1').show();
                        self.isVideoPlaying = false;
                        
                        // Restart slider
                        self.startSlider();
                    }
                });
                
            } else {
                // ============================================
                // DESKTOP BEHAVIOR: Hover + Click
                // ============================================
                
                // HOVER: Stop slider, show video (not playing)
                this.$container.on('mouseenter', function() {
                    self.isHovered = true;
                    
                    if (self.sliderInterval) {
                        clearInterval(self.sliderInterval);
                    }
                    
                    // Fade out slider images
                    self.$images.css('opacity', '0');
                    
                    // Show video (but don't play yet)
                    self.$video.css('opacity', '1');
                    
                    // Show play button
                    self.$playOverlay.css('opacity', '1').show();
                });
                
                // MOUSE LEAVE: Stop video, resume slider
                this.$container.on('mouseleave', function() {
                    self.isHovered = false;
                    
                    // Stop video if playing
                    if (self.isVideoPlaying) {
                        const video = self.$video[0];
                        if (video && video.tagName === 'VIDEO') {
                            video.pause();
                            video.currentTime = 0;
                        }
                        self.isVideoPlaying = false;
                    }
                    
                    // Hide video
                    self.$video.css('opacity', '0');
                    
                    // Reset play button
                    self.$playOverlay.css('opacity', '0').hide();
                    
                    // Show current slide
                    self.$images.css('opacity', '0');
                    self.$images.eq(self.currentSlide).css('opacity', '1');
                    
                    // Resume slider
                    if (self.$images.length > 1) {
                        if (self.sliderInterval) {
                            clearInterval(self.sliderInterval);
                        }
                        
                        self.sliderInterval = setInterval(function() {
                            if (!self.isHovered && !self.isVideoPlaying) {
                                self.$images.eq(self.currentSlide).css('opacity', '0');
                                self.$dots.eq(self.currentSlide).css('background', 'rgba(255,255,255,0.5)').removeClass('active');
                                
                                self.currentSlide = (self.currentSlide + 1) % self.$images.length;
                                
                                self.$images.eq(self.currentSlide).css('opacity', '1');
                                self.$dots.eq(self.currentSlide).css('background', '#fff').addClass('active');
                            }
                        }, self.sliderSpeed);
                    }
                });
                
                // CLICK: Play video
                this.$container.on('click', function(e) {
                    if (!self.isVideoPlaying && self.isHovered) {
                        self.isVideoPlaying = true;
                        
                        // Hide play button
                        self.$playOverlay.fadeOut(300);
                        
                        // Play video
                        const video = self.$video[0];
                        if (video) {
                            if (video.tagName === 'VIDEO') {
                                const playPromise = video.play();
                                
                                if (playPromise !== undefined) {
                                    playPromise
                                        .then(() => {
                                            console.log('Desktop video playing');
                                        })
                                        .catch((error) => {
                                            console.error('Desktop video play failed:', error);
                                            self.$playOverlay.fadeIn(300);
                                            self.isVideoPlaying = false;
                                        });
                                }
                            }
                            // For iframe, autoplay in URL handles it
                        }
                    }
                });
            }
        }
    }
    
    // Expose AnMiVideoBanner to global scope for admin preview
    window.AnMiVideoBanner = AnMiVideoBanner;
    
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
