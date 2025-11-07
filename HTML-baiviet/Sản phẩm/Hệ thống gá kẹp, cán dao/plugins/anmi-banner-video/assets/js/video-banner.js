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
            this.$sliderWrapper = this.$container.find('.abvp-slider-wrapper');
            this.$navDotsWrapper = this.$container.find('.abvp-nav-dots');
            
            // Check if video is wrapped by WP core widget
            this.$wpVideoWrapper = this.$container.find('.abvp-wp-video-wrapper');
            if (this.$wpVideoWrapper.length > 0) {
                // Find actual video element within WP widget wrapper
                const $wpVideo = this.$wpVideoWrapper.find('video');
                if ($wpVideo.length > 0) {
                    this.$video = $wpVideo;
                    this.isWPCoreVideo = true;
                }
            } else {
                this.isWPCoreVideo = false;
            }
            
            this.autoplayDelay = parseInt(this.$container.data('autoplay-delay'), 10) || 0;
            this.mobileBehavior = this.$container.data('mobile-behavior') || 'image';
            this.sliderSpeed = parseInt(this.$container.data('slider-speed'), 10) || 3000;
            this.sliderEffect = this.$container.data('slider-effect') || 'fade';
            this.videoType = this.$container.data('video-type') || 'direct';
            this.enableAutoplay = parseInt(this.$container.data('enable-autoplay'), 10) === 1;
            this.enableMuted = parseInt(this.$container.data('enable-muted'), 10) !== 0;
            this.enableLoop = parseInt(this.$container.data('enable-loop'), 10) === 1;
            this.enableControls = parseInt(this.$container.data('enable-controls'), 10) === 1;
            const hasSliderData = parseInt(this.$container.data('has-slider'), 10);
            this.imageCount = parseInt(this.$container.data('image-count'), 10) || this.$images.length;
            this.hasSlider = (Number.isNaN(hasSliderData) ? this.$images.length > 1 : hasSliderData === 1) && this.imageCount > 1;
            this.previewDevice = (this.$container.data('preview-device') || '').toString().toLowerCase();
            this.isMobileDevice = this.determineMobileState();
            this.isVideoOnlyMobile = this.isMobileDevice && this.mobileBehavior === 'video';
            this.isIframe = this.$video.length > 0 && this.$video[0].tagName === 'IFRAME';
            this.iframeBaseSrc = this.isIframe ? this.$video.attr('src') : '';
            
            this.isVideoReady = false;
            this.sliderInterval = null;
            this.currentSlide = 0;
            this.isHovered = false;
            this.isVideoPlaying = false;
            this.isMuted = this.enableMuted;
            
            this.init();
        }
        
        init() {
            // Prevent duplicate initialization when re-running scripts
            this.$container.data('anmi-initialized', true);
            this.applyVideoPreferences();
            
            // Check if mobile - but allow WP core video widget to handle mobile itself
            if (this.isMobileDevice && this.mobileBehavior === 'image' && !this.isWPCoreVideo) {
                this.disableVideoOnMobile();
                this.startSlider();
                return;
            }

            if (this.isVideoOnlyMobile) {
                this.prepareVideoOnlyMobile();
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
            if (this.hasSlider && !this.isVideoOnlyMobile) {
                this.startSlider();
            }
        }
        
        isMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
                   window.innerWidth <= 768;
        }

        determineMobileState() {
            if (this.previewDevice === 'mobile') {
                return true;
            }

            if (this.previewDevice === 'desktop') {
                return false;
            }

            return this.isMobile();
        }
        
        disableVideoOnMobile() {
            this.stopSlider();
            this.$video.remove();
            this.$playOverlay.remove();
            this.$volumeControl.remove();
            this.$loader.remove();
        }
        
        applyVideoPreferences() {
            const video = this.$video[0];
            if (!video) {
                return;
            }

            if (this.isMobileDevice && this.enableAutoplay) {
                this.enableMuted = true;
            }

            if (video.tagName === 'VIDEO') {
                video.loop = this.enableLoop;
                video.muted = this.enableMuted;
                video.autoplay = this.enableAutoplay;
                video.controls = this.enableControls;
                this.isMuted = video.muted;

                if (this.enableControls) {
                    this.$volumeControl.remove();
                } else {
                    this.updateVolumeIcon();
                    this.$volumeControl.hide();
                }
                this.bindVideoEvents();
            } else if (this.isIframe) {
                // Iframe players use their own controls; remove custom volume control
                this.$volumeControl.remove();
            }
        }

        prepareVideoOnlyMobile() {
            this.stopSlider();
            this.hasSlider = false;

            if (this.$sliderWrapper.length) {
                this.$sliderWrapper.hide();
            }

            if (this.$navDotsWrapper.length) {
                this.$navDotsWrapper.hide();
            }

            this.$images.css('opacity', '0');
            this.$container.addClass('anmi-mobile-video-only');
            this.$video.css('opacity', '1');

            if (this.enableAutoplay && !this.isIframe) {
                this.$playOverlay.hide();
            } else {
                this.$playOverlay.css('opacity', '1').show();
            }

            this.attemptAutoplay();
        }

        attemptAutoplay() {
            const video = this.$video[0];

            if (!this.enableAutoplay || !video) {
                return;
            }

            if (video.tagName !== 'VIDEO') {
                this.playIframeVideo(true, false);
                return;
            }

            // Handle WP core video wrapper - ensure poster is shown
            if (this.isWPCoreVideo && this.isMobileDevice) {
                this.setupWPCoreVideoMobileInteraction(video);
            }

            const playPromise = video.play();

            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        this.isVideoPlaying = true;
                        this.$playOverlay.hide();
                        if (this.$volumeControl.length) {
                            this.$volumeControl.fadeIn(0);
                        }
                    })
                    .catch(() => {
                        // Autoplay blocked - show overlay and poster
                        this.isVideoPlaying = false;
                        this.$playOverlay.css('opacity', '1').show();
                        
                        // For WP core video, ensure poster is visible
                        if (this.isWPCoreVideo) {
                            this.ensureWPVideoPosterVisible(video);
                        }
                    });
            } else {
                this.isVideoPlaying = true;
                this.$playOverlay.hide();
            }
        }

        updateVolumeIcon() {
            if (!this.$volumeControl.length) {
                return;
            }

            if (this.isMuted) {
                this.$volumeControl.find('.volume-icon-muted').show();
                this.$volumeControl.find('.volume-icon-unmuted').hide();
                this.$volumeControl.attr('title', 'Unmute video');
            } else {
                this.$volumeControl.find('.volume-icon-muted').hide();
                this.$volumeControl.find('.volume-icon-unmuted').show();
                this.$volumeControl.attr('title', 'Mute video');
            }
        }

        playVideoElement(video) {
            if (!video) {
                return;
            }

            if (video.tagName === 'VIDEO') {
                const playPromise = video.play();

                if (playPromise !== undefined) {
                    playPromise
                        .then(() => {
                            this.isVideoPlaying = true;
                            this.$playOverlay.fadeOut(200);
                            if (this.$volumeControl.length && !this.enableControls) {
                                this.$volumeControl.fadeIn(300);
                            }
                        })
                        .catch((error) => {
                            console.error('Video play failed:', error);
                            this.isVideoPlaying = false;
                            this.$playOverlay.css('opacity', '1').show();
                            if (this.$volumeControl.length) {
                                this.$volumeControl.fadeOut(200);
                            }
                        });
                } else {
                    this.isVideoPlaying = true;
                    this.$playOverlay.fadeOut(200);
                    if (this.$volumeControl.length && !this.enableControls) {
                        this.$volumeControl.fadeIn(300);
                    }
                }
            } else {
                this.playIframeVideo(true, true);
            }
        }

        pauseAndResetVideo(video, resetToStart = true, showOverlay = true) {
            if (!video || video.tagName !== 'VIDEO') {
                this.isVideoPlaying = false;
                return;
            }

            video.pause();

            if (resetToStart) {
                video.currentTime = 0;
            }

            if (this.enableMuted) {
                video.muted = true;
                this.isMuted = true;
                this.updateVolumeIcon();
            }

            if (this.$volumeControl.length) {
                this.$volumeControl.fadeOut(200);
            }

            if (showOverlay) {
                this.$playOverlay.css('opacity', '1').show();
            }
            this.isVideoPlaying = false;
        }

        setupVolumeControl() {
            const video = this.$video[0];

            if (!video || this.isIframe || this.enableControls || !this.$volumeControl.length) {
                if (this.$volumeControl.length) {
                    this.$volumeControl.remove();
                }
                return;
            }

            this.updateVolumeIcon();

            this.$volumeControl.off('click').on('click', (e) => {
                e.stopPropagation();

                this.isMuted = !this.isMuted;
                video.muted = this.isMuted;
                this.updateVolumeIcon();
            });
        }

        setupMobileVideoOnlyEvents(video) {
            if (!video) {
                return;
            }

            this.$container.off('touchstart.anmiVideoOnly click.anmiVideoOnly')
                    .on('touchstart.anmiVideoOnly click.anmiVideoOnly', (e) => {
                e.preventDefault();
                e.stopPropagation();

                if (!this.isVideoPlaying) {
                    if (this.isIframe) {
                                this.playIframeVideo(true, true);
                    } else {
                        this.playVideoElement(video);
                    }
                } else if (this.isIframe) {
                    this.resetIframeVideo();
                } else {
                    this.pauseAndResetVideo(video);
                }
                });
        }

        playIframeVideo(forceAutoplay = false, userInitiated = false) {
            if (!this.isIframe || !this.$video.length) {
                return;
            }

            const autoplay = forceAutoplay || this.enableAutoplay;
            const mute = this.enableMuted;
            const baseSrc = this.iframeBaseSrc || this.$video.attr('src');
            const newSrc = this.buildIframeSrc(baseSrc, autoplay, mute);

            if (newSrc) {
                this.$video.attr('src', newSrc);
            }

            this.isVideoPlaying = userInitiated || !this.isMobileDevice;

            if (userInitiated || !this.isMobileDevice) {
                this.$playOverlay.fadeOut(200);
            } else {
                this.$playOverlay.css('opacity', '1').show();
            }
            this.$loader.removeClass('active');
        }

        resetIframeVideo() {
            if (!this.isIframe || !this.$video.length) {
                return;
            }

            const baseSrc = this.iframeBaseSrc || this.$video.attr('src');
            const newSrc = this.buildIframeSrc(baseSrc, false, true);

            if (newSrc) {
                this.$video.attr('src', newSrc);
            }

            this.isVideoPlaying = false;
            this.$playOverlay.css('opacity', '1').show();
        }

        buildIframeSrc(src, autoplay, mute) {
            if (!src) {
                return src;
            }

            try {
                const url = new URL(src, window.location.href);
                url.searchParams.set('autoplay', autoplay ? '1' : '0');
                url.searchParams.set('mute', mute ? '1' : '0');
                url.searchParams.set('muted', mute ? '1' : '0');

                if (this.videoType === 'vimeo') {
                    url.searchParams.set('loop', this.enableLoop ? '1' : '0');
                    url.searchParams.set('background', this.enableControls ? '0' : '1');
                    url.searchParams.set('controls', this.enableControls ? '1' : '0');
                }

                if (this.videoType === 'youtube' && this.enableLoop) {
                    const playlist = url.searchParams.get('playlist') || this.extractYouTubeId(url.pathname);
                    if (playlist) {
                        url.searchParams.set('playlist', playlist);
                    }
                }

                return url.toString();
            } catch (error) {
                let newSrc = src.replace(/([?&])(autoplay|mute|muted)=[^&]*/g, '');
                const separator = newSrc.includes('?') ? '&' : '?';
                const params = [
                    `autoplay=${autoplay ? 1 : 0}`,
                    `mute=${mute ? 1 : 0}`,
                    `muted=${mute ? 1 : 0}`
                ];
                return newSrc + separator + params.join('&');
            }
        }

        extractYouTubeId(pathname) {
            const match = pathname ? pathname.match(/\/embed\/([a-zA-Z0-9_-]{11})/) : null;
            return match ? match[1] : '';
        }

        bindVideoEvents() {
            if (!this.$video.length || this.$video[0].tagName !== 'VIDEO') {
                return;
            }

            this.$video.off('ended.anmiVideo').on('ended.anmiVideo', () => {
                if (this.enableLoop) {
                    return;
                }

                const video = this.$video[0];
                const showOverlay = this.isVideoOnlyMobile || !this.hasSlider;

                this.pauseAndResetVideo(video, true, showOverlay);

                if (this.isVideoOnlyMobile) {
                    this.$video.css('opacity', '1');
                    return;
                }

                if (this.isMobileDevice) {
                    this.$video.css('opacity', '0');
                    this.$images.css('opacity', '0');
                    this.$images.eq(this.currentSlide).css('opacity', '1');
                    this.startSlider();
                } else {
                    this.$video.css('opacity', '0');
                    this.$playOverlay.css('opacity', '0').hide();

                    if (this.hasSlider) {
                        this.$images.css('opacity', '0');
                        this.$images.eq(this.currentSlide).css('opacity', '1');
                        this.startSlider();
                    }
                }
            });
        }

        /* ============================================ */
        /* SLIDER FUNCTIONALITY */
        /* ============================================ */
        
        startSlider() {
            if (!this.hasSlider || this.$images.length <= 1) {
                return;
            }

            this.stopSlider();

            const self = this;
            
            // Auto-play slider
            this.sliderInterval = setInterval(() => {
                if (!self.isHovered && !self.isVideoPlaying) {
                    self.nextSlide();
                }
            }, this.sliderSpeed);
            
            // Dot navigation
            this.$dots.off('click').on('click', function(e) {
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

        /**
         * Ensure WP core video poster is visible on mobile when autoplay fails.
         */
        ensureWPVideoPosterVisible(video) {
            if (!video || !this.$wpVideoWrapper.length) {
                return;
            }

            const posterUrl = this.$wpVideoWrapper.data('poster');
            
            if (posterUrl && !video.poster) {
                video.poster = posterUrl;
            }

            // Reset video to show poster
            video.load();
        }

        /**
         * Setup mobile interaction for WP core video - handle tap to play.
         */
        setupWPCoreVideoMobileInteraction(video) {
            if (!this.isMobileDevice || !this.$wpVideoWrapper.length) {
                return;
            }

            // Add mobile-specific event handler
            this.$playOverlay.off('click.wpVideoMobile').on('click.wpVideoMobile', (e) => {
                e.stopPropagation();
                
                if (!this.isVideoPlaying) {
                    // User tapped play - try to start video
                    const playPromise = video.play();
                    
                    if (playPromise !== undefined) {
                        playPromise
                            .then(() => {
                                this.isVideoPlaying = true;
                                this.$playOverlay.fadeOut(200);
                                
                                // Add playing class to video for CSS styling
                                $(video).addClass('playing');
                                
                                // Stop slider when video plays
                                if (this.hasSlider) {
                                    this.stopSlider();
                                }
                            })
                            .catch((error) => {
                                console.error('Mobile video play failed:', error);
                                // Keep showing overlay
                                this.$playOverlay.show();
                            });
                    }
                }
            });

            // Make overlay pointer-events enabled for mobile
            this.$playOverlay.css('pointer-events', 'auto');
            
            // Listen for video ended event to reset
            $(video).on('ended', () => {
                $(video).removeClass('playing');
                this.$playOverlay.css('opacity', '1').show();
                this.isVideoPlaying = false;
                
                // Restart slider if available
                if (this.hasSlider) {
                    this.startSlider();
                }
            });
        }
        
        setupEvents() {
            const videoElement = this.$video[0];

            if (!videoElement) {
                return;
            }

            if (this.isVideoOnlyMobile) {
                this.setupMobileVideoOnlyEvents(videoElement);
                this.setupVolumeControl();
                return;
            }

            if (this.isMobileDevice) {
                this.$container.off('touchstart.anmiVideo click.anmiVideo')
                    .on('touchstart.anmiVideo click.anmiVideo', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    if (!this.isVideoPlaying) {
                        this.stopSlider();
                        this.$images.css('opacity', '0');
                        this.$video.css('opacity', '1');
                        if (this.isIframe) {
                           this.playIframeVideo(true, true);
                        } else {
                            this.playVideoElement(videoElement);
                        }
                    } else {
                        if (this.isIframe) {
                            this.resetIframeVideo();
                        } else {
                            this.pauseAndResetVideo(videoElement, true, false);
                        }
                        this.$video.css('opacity', '0');
                        this.$images.css('opacity', '0');
                        this.$images.eq(this.currentSlide).css('opacity', '1');
                        this.startSlider();
                    }
                });
            } else {
                // Desktop hover + click behaviour
                this.$container.off('mouseenter.anmiVideo mouseleave.anmiVideo click.anmiVideo');

                this.$container.on('mouseenter.anmiVideo', () => {
                    this.isHovered = true;
                    this.stopSlider();
                    this.$images.css('opacity', '0');
                    this.$video.css('opacity', '1');
                    this.$playOverlay.css('opacity', '1').show();
                });

                this.$container.on('mouseleave.anmiVideo', () => {
                    this.isHovered = false;

                    if (this.isVideoPlaying) {
                        this.pauseAndResetVideo(videoElement, true, false);
                    }

                    if (this.$volumeControl.length) {
                        this.$volumeControl.fadeOut(300);
                    }

                    this.$video.css('opacity', '0');
                    this.$playOverlay.css('opacity', '0').hide();
                    this.$images.css('opacity', '0');
                    this.$images.eq(this.currentSlide).css('opacity', '1');

                    if (this.hasSlider) {
                        this.startSlider();
                    }
                });

                this.$container.on('click.anmiVideo', () => {
                    if (!this.isVideoPlaying && this.isHovered) {
                        if (this.isIframe) {
                           this.playIframeVideo(true, true);
                        } else {
                            this.playVideoElement(videoElement);
                        }
                    }
                });
            }

            this.setupVolumeControl();
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
        // Generic widget ready handler
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
            $scope.find('.anmi-video-banner-container').each(function() {
                if (!$(this).data('anmi-initialized')) {
                    new AnMiVideoBanner(this);
                    $(this).data('anmi-initialized', true);
                }
            });
        });
        
        // Specific handler for our widget
        elementorFrontend.hooks.addAction('frontend/element_ready/anmi_video_banner.default', function($scope) {
            $scope.find('.anmi-video-banner-container').each(function() {
                new AnMiVideoBanner(this);
                $(this).data('anmi-initialized', true);
            });
        });
    });
    
})(jQuery);
