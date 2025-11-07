/**
 * AN MI VIDEO BANNER PLUGIN JAVASCRIPT
 * Version: 2.7.0 - Unified Dedicated Overlay System (Old Overlay Removed)
 */

(function($) {
    'use strict';
    
    class AnMiVideoBanner {
        constructor(container) {
            this.$container = $(container);
            this.$video = this.$container.find('.anmi-banner-video');
            this.$images = this.$container.find('.anmi-banner-image');
            // Dedicated overlay only (old overlay system removed)
            this.$dedicatedOverlay = this.$container.find('.abvp-dedicated-overlay');
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
            this.enableSlider = parseInt(this.$container.data('enable-slider'), 10) !== 0;
            this.enableSliderDesktop = parseInt(this.$container.data('enable-slider-desktop'), 10) !== 0;
            this.enableSliderMobile = parseInt(this.$container.data('enable-slider-mobile'), 10) !== 0;
            this.enableOverlay = parseInt(this.$container.data('enable-overlay'), 10) !== 0;
            this.enableOverlayDesktop = parseInt(this.$container.data('enable-overlay-desktop'), 10) !== 0;
            this.enableOverlayMobile = parseInt(this.$container.data('enable-overlay-mobile'), 10) !== 0;
            this.enableHover = parseInt(this.$container.data('enable-hover'), 10) !== 0;
            this.enableHoverDesktop = parseInt(this.$container.data('enable-hover-desktop'), 10) !== 0;
            this.enableHoverMobile = parseInt(this.$container.data('enable-hover-mobile'), 10) !== 0;
            const hasSliderData = parseInt(this.$container.data('has-slider'), 10);
            this.imageCount = parseInt(this.$container.data('image-count'), 10) || this.$images.length;
            this.hasSlider = this.enableSlider && (Number.isNaN(hasSliderData) ? this.$images.length > 1 : hasSliderData === 1) && this.imageCount > 1;
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
            
            // Apply device-specific logic
            this.applyDeviceSliderLogic();
            this.applyDeviceOverlayLogic();
            this.applyDeviceHoverLogic();
            
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
            
            // Start image slider based on device settings
            if (this.hasSlider && !this.isVideoOnlyMobile && this.shouldShowSlider()) {
                this.startSlider();
            } else if (!this.shouldShowSlider()) {
                // Hide slider images if disabled for this device
                this.$images.hide();
                console.log('Slider hidden for current device');
            }
        }
        
        /**
         * Apply device-specific slider visibility logic
         */
        applyDeviceSliderLogic() {
            const isMobile = this.isMobileDevice;
            const isDesktop = !isMobile;
            
            // Determine if slider should be shown on current device
            if (isMobile && !this.enableSliderMobile) {
                this.hasSlider = false;
                console.log('Slider disabled on mobile');
            } else if (isDesktop && !this.enableSliderDesktop) {
                this.hasSlider = false;
                console.log('Slider disabled on desktop');
            }
        }
        
        /**
         * Apply device-specific overlay visibility logic
         */
        applyDeviceOverlayLogic() {
            const isMobile = this.isMobileDevice;
            const isDesktop = !isMobile;
            const $overlay = this.$container.find('.elementor-custom-embed-image-overlay');
            
            if (!this.enableOverlay) {
                $overlay.hide();
                console.log('Overlay disabled (master switch)');
                return;
            }
            
            if (isMobile && !this.enableOverlayMobile) {
                $overlay.hide();
                console.log('Overlay disabled on mobile');
            } else if (isDesktop && !this.enableOverlayDesktop) {
                $overlay.hide();
                console.log('Overlay disabled on desktop');
            }
        }
        
        /**
         * Apply device-specific hover effect logic
         */
        applyDeviceHoverLogic() {
            const isMobile = this.isMobileDevice;
            const isDesktop = !isMobile;
            
            if (!this.enableHover) {
                console.log('Hover disabled (master switch)');
                return;
            }
            
            if (isMobile && !this.enableHoverMobile) {
                console.log('Hover disabled on mobile');
            } else if (isDesktop && !this.enableHoverDesktop) {
                console.log('Hover disabled on desktop');
            }
            // Note: CSS handles the actual hover behavior via data attributes
        }
        
        /**
         * Check if slider should be visible on current device
         */
        shouldShowSlider() {
            if (!this.enableSlider) {
                return false; // Master switch off
            }
            
            const isMobile = this.isMobileDevice;
            const isDesktop = !isMobile;
            
            if (isMobile && !this.enableSliderMobile) {
                return false;
            }
            
            if (isDesktop && !this.enableSliderDesktop) {
                return false;
            }
            
            return this.hasSlider;
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
            if (!this.enableSlider || !this.hasSlider || this.$images.length <= 1) {
                console.log('Slider disabled or no images:', {
                    enableSlider: this.enableSlider,
                    hasSlider: this.hasSlider,
                    imageCount: this.$images.length
                });
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
            if (!this.enableSlider) {
                return;
            }
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

        // OLD OVERLAY REMOVED: setupElementorOverlay() has been removed
        // Now using setupDedicatedOverlay() only (see below)
        
        /**
         * Setup dedicated overlay with custom image and play button
         * This overlay is independent from slider transitions
         */
        setupDedicatedOverlay() {
            if (this.$dedicatedOverlay.length === 0) {
                return; // No dedicated overlay element
            }
            
            // Check overlay settings - if disabled, hide overlay
            const isMobile = this.isMobileDevice;
            const isDesktop = !isMobile;
            
            if (!this.enableOverlay || 
                (isMobile && !this.enableOverlayMobile) || 
                (isDesktop && !this.enableOverlayDesktop)) {
                this.$dedicatedOverlay.hide();
                console.log('Dedicated overlay disabled by settings');
                return;
            }
            
            const handleDedicatedOverlayClick = (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const video = this.$video[0];
                const $iframe = this.$container.find('.abvp-oembed-container iframe, .elementor-fit-aspect-ratio iframe');
                
                // Mark video as playing - CSS will hide overlay via .video-playing class
                this.$container.addClass('video-is-playing video-playing');
                
                // STOP image slider when video plays
                if (this.hasSlider) {
                    this.stopSlider();
                    console.log('Slider stopped - Video playing from dedicated overlay');
                }
                
                // Hide images completely
                this.$images.css('opacity', '0');
                
                // Play video or iframe
                if (video && video.tagName === 'VIDEO') {
                    $(video).addClass('playing');
                    const playPromise = video.play();
                    
                    if (playPromise !== undefined) {
                        playPromise
                            .then(() => {
                                this.isVideoPlaying = true;
                            })
                            .catch((error) => {
                                console.error('Video play failed from dedicated overlay:', error);
                                // Show overlay again and restart slider
                                this.$container.removeClass('video-is-playing video-playing');
                                if (this.hasSlider) {
                                    this.startSlider();
                                }
                            });
                    }
                } else if ($iframe.length > 0) {
                    // For iframe (YouTube/Vimeo via oEmbed)
                    $iframe.addClass('playing');
                    this.isVideoPlaying = true;
                    
                    // Try to play iframe
                    const iframe = $iframe[0];
                    if (iframe && iframe.contentWindow) {
                        try {
                            // Post message to play (works for YouTube/Vimeo)
                            iframe.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
                        } catch (e) {
                            // Ignore if postMessage fails
                        }
                    }
                }
            };
            
            // Attach click handler to overlay
            this.$dedicatedOverlay.off('click.dedicatedOverlay').on('click.dedicatedOverlay', handleDedicatedOverlayClick);
            
            // Handle video ended event to show overlay and restart slider
            const video = this.$video[0];
            if (video && video.tagName === 'VIDEO') {
                $(video).off('ended.dedicatedOverlay').on('ended.dedicatedOverlay', () => {
                    this.$container.removeClass('video-is-playing video-playing');
                    $(video).removeClass('playing');
                    this.isVideoPlaying = false;
                    
                    // RESTART slider when video ends
                    if (this.hasSlider) {
                        this.startSlider();
                        console.log('Slider restarted from dedicated overlay - Video ended');
                    }
                });
                
                // Handle video pause event
                $(video).off('pause.dedicatedOverlay').on('pause.dedicatedOverlay', () => {
                    if (!video.ended) {
                        this.$container.removeClass('video-is-playing video-playing');
                        this.isVideoPlaying = false;
                        
                        // RESTART slider when video paused
                        if (this.hasSlider) {
                            this.startSlider();
                            console.log('Slider restarted from dedicated overlay - Video paused');
                        }
                    }
                });
                
                // Handle video play event
                $(video).off('play.dedicatedOverlay').on('play.dedicatedOverlay', () => {
                    this.$container.addClass('video-is-playing video-playing');
                    this.isVideoPlaying = true;
                    
                    // STOP slider when video plays
                    if (this.hasSlider) {
                        this.stopSlider();
                        console.log('Slider stopped from dedicated overlay - Video playing');
                    }
                });
            }
        }
        
        setupEvents() {
            const videoElement = this.$video[0];

            if (!videoElement) {
                return;
            }

            // Setup dedicated overlay click handler (old Elementor overlay removed)
            this.setupDedicatedOverlay();

            if (this.isVideoOnlyMobile) {
                this.setupMobileVideoOnlyEvents(videoElement);
                this.setupVolumeControl();
                return;
            }

            if (this.isMobileDevice) {
                // Mobile tap to play
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
