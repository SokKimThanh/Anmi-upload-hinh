/**
 * AN MI VIDEO BANNER PLUGIN JAVASCRIPT
 * Version: 1.6.12 - Video Sound Control
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
            this.$volumeControl = this.$container.find('.anmi-volume-control');
            
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
            this.isMuted = true; // Track mute state
            
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
                        
                        // Show volume control
                        self.$volumeControl.fadeIn(300);
                        
                        // Play video
                        const video = self.$video[0];
                        if (video) {
                            if (video.tagName === 'VIDEO') {
                                // Try to play with sound first
                                video.muted = false;
                                self.isMuted = false;
                                
                                // Update volume icon to unmuted
                                self.$volumeControl.find('.volume-icon-muted').hide();
                                self.$volumeControl.find('.volume-icon-unmuted').show();
                                
                                // Direct video - play with error handling
                                const playPromise = video.play();
                                
                                if (playPromise !== undefined) {
                                    playPromise
                                        .then(() => {
                                            console.log('Mobile video playing with sound');
                                            self.isVideoPlaying = true;
                                        })
                                        .catch((error) => {
                                            console.error('Mobile video play with sound failed, trying muted:', error);
                                            // Fallback: Try muted if unmuted fails
                                            video.muted = true;
                                            self.isMuted = true;
                                            self.$volumeControl.find('.volume-icon-muted').show();
                                            self.$volumeControl.find('.volume-icon-unmuted').hide();
                                            
                                            video.play()
                                                .then(() => {
                                                    console.log('Mobile video playing (muted fallback)');
                                                    self.isVideoPlaying = true;
                                                })
                                                .catch((err) => {
                                                    console.error('Mobile video play failed completely:', err);
                                                    self.$playOverlay.css('opacity', '1').show();
                                                    self.$volumeControl.fadeOut(300);
                                                });
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
                            video.muted = true; // Reset to muted for next autoplay
                            self.isMuted = true;
                        }
                        
                        // Hide volume control
                        self.$volumeControl.fadeOut(300);
                        
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
                            video.muted = true; // Reset to muted for next autoplay
                            self.isMuted = true;
                        }
                        self.isVideoPlaying = false;
                    }
                    
                    // Hide volume control
                    self.$volumeControl.fadeOut(300);
                    
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
                        
                        // Show volume control
                        self.$volumeControl.fadeIn(300);
                        
                        // Play video
                        const video = self.$video[0];
                        if (video) {
                            if (video.tagName === 'VIDEO') {
                                // Keep video muted by default (don't auto-unmute)
                                // User can click volume button to unmute
                                
                                const playPromise = video.play();
                                
                                if (playPromise !== undefined) {
                                    playPromise
                                        .then(() => {
                                            console.log('Desktop video playing (muted)');
                                        })
                                        .catch((error) => {
                                            console.error('Desktop video play failed:', error);
                                            self.$playOverlay.fadeIn(300);
                                            self.$volumeControl.fadeOut(300);
                                            self.isVideoPlaying = false;
                                        });
                                }
                            }
                            // For iframe, autoplay in URL handles it
                        }
                    }
                });
            }
            
            // ============================================
            // VOLUME CONTROL (Both Mobile & Desktop)
            // ============================================
            // Check if video is HTML5 video or iframe (YouTube/Vimeo)
            const video = this.$video[0];
            const isIframe = video && video.tagName === 'IFRAME';
            
            if (isIframe) {
                // Hide volume control for iframe videos (YouTube/Vimeo)
                // These require their own APIs (YouTube IFrame API / Vimeo Player API)
                this.$volumeControl.hide();
                console.log('Volume control disabled for iframe video (YouTube/Vimeo)');
            } else {
                // Show and enable volume control for HTML5 video
                this.$volumeControl.on('click', function(e) {
                    e.stopPropagation(); // Prevent triggering container click
                    
                    if (video && video.tagName === 'VIDEO') {
                        // Toggle mute
                        self.isMuted = !self.isMuted;
                        video.muted = self.isMuted;
                        
                        // Update icon
                        if (self.isMuted) {
                            self.$volumeControl.find('.volume-icon-muted').show();
                            self.$volumeControl.find('.volume-icon-unmuted').hide();
                            self.$volumeControl.attr('title', 'Unmute video');
                        } else {
                            self.$volumeControl.find('.volume-icon-muted').hide();
                            self.$volumeControl.find('.volume-icon-unmuted').show();
                            self.$volumeControl.attr('title', 'Mute video');
                        }
                        
                        console.log('HTML5 Video ' + (self.isMuted ? 'muted' : 'unmuted'));
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
