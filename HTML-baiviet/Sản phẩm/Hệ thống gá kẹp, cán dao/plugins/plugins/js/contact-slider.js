/**
 * Contact Slider for Mobile
 * File: contact-slider.js
 * Version: 1.0.0
 * Created: November 17, 2025
 * Author: An Mi Tools Technical Team
 * 
 * Purpose: Enable touch swipe slider for contact offices on mobile devices
 * 
 * Features:
 * - Touch swipe support (left/right)
 * - Dot navigation
 * - Auto-detect mobile viewport
 * - Smooth transitions
 * - No jQuery dependency
 * 
 * Browser Support: IE11+, All modern mobile browsers
 */

(function() {
    'use strict';

    // Only run on mobile viewports
    function isMobile() {
        return window.innerWidth <= 768;
    }

    // Initialize contact slider
    function initContactSlider() {
        if (!isMobile()) return;

        const wrapper = document.querySelector('.contact-info-wrapper');
        const slider = document.querySelector('.contact-info');
        const offices = document.querySelectorAll('.contact-info .office');

        if (!wrapper || !slider || offices.length === 0) return;

        // Prefer dots inside the wrapper; if missing, we'll create them dynamically
        let dotsContainer = wrapper.querySelector('.contact-slider-dots') || wrapper;
        let dots = dotsContainer.querySelectorAll('.slider-dot');

        let currentSlide = 0;
        let startX = 0;
        let isDragging = false;
        let currentTranslate = 0;
        let prevTranslate = 0;

        // If no dots exist, create a dots UI inside the dots container
        if (dots.length === 0 && dotsContainer) {
            if (!dotsContainer.classList.contains('contact-slider-dots')) {
                // try to find or create a dedicated dots container inside the wrapper
                const created = document.createElement('div');
                created.className = 'contact-slider-dots';
                created.setAttribute('aria-hidden', 'true');
                wrapper.appendChild(created);
                dotsContainer = created;
            }

            for (let i = 0; i < offices.length; i++) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'slider-dot' + (i === 0 ? ' active' : '');
                btn.setAttribute('aria-label', 'Slide ' + (i + 1));
                dotsContainer.appendChild(btn);
            }
            dots = dotsContainer.querySelectorAll('.slider-dot');
        }

        // Set up dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goToSlide(index);
            });
        });

        // Touch events
        slider.addEventListener('touchstart', touchStart);
        slider.addEventListener('touchmove', touchMove);
        slider.addEventListener('touchend', touchEnd);

        // Mouse events for desktop testing
        slider.addEventListener('mousedown', touchStart);
        slider.addEventListener('mousemove', touchMove);
        slider.addEventListener('mouseup', touchEnd);
        slider.addEventListener('mouseleave', touchEnd);

        function touchStart(event) {
            isDragging = true;
            startX = getPositionX(event);
            slider.style.transition = 'none';
        }

        function touchMove(event) {
            if (!isDragging) return;
            
            const currentX = getPositionX(event);
            const diff = currentX - startX;
            currentTranslate = prevTranslate + diff;

            // Limit dragging
            const maxTranslate = 0;
            const minTranslate = -(slider.scrollWidth - slider.clientWidth);
            
            if (currentTranslate > maxTranslate) {
                currentTranslate = maxTranslate;
            }
            if (currentTranslate < minTranslate) {
                currentTranslate = minTranslate;
            }

            setSliderPosition();
        }

        function touchEnd() {
            if (!isDragging) return;
            isDragging = false;

            const movedBy = currentTranslate - prevTranslate;

            // Determine if swipe was significant enough
            if (movedBy < -50 && currentSlide < offices.length - 1) {
                currentSlide++;
            } else if (movedBy > 50 && currentSlide > 0) {
                currentSlide--;
            }

            goToSlide(currentSlide);
        }

        function getPositionX(event) {
            return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
        }

        function goToSlide(index) {
            currentSlide = index;
            prevTranslate = -(slider.clientWidth * currentSlide);
            currentTranslate = prevTranslate;
            
            slider.style.transition = 'transform 0.3s ease-out';
            setSliderPosition();
            updateDots();
        }

        function setSliderPosition() {
            slider.style.transform = `translateX(${currentTranslate}px)`;
        }

        function updateDots() {
            dots.forEach((dot, index) => {
                if (index === currentSlide) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (!isMobile()) {
                    // Reset slider on desktop
                    slider.style.transform = 'translateX(0)';
                    currentSlide = 0;
                    prevTranslate = 0;
                    currentTranslate = 0;
                } else {
                    // Recalculate position on mobile
                    goToSlide(currentSlide);
                }
            }, 250);
        });

        // If the slider is not visible (e.g. inside a hidden tab), defer final layout until it becomes visible
        if (slider.clientWidth === 0) {
            const parentPanel = slider.closest('.tab-content');
            if (parentPanel) {
                const obs = new MutationObserver((mutations, observer) => {
                    if (slider.clientWidth > 0 && isMobile()) {
                        observer.disconnect();
                        // Force initial layout
                        goToSlide(currentSlide);
                    }
                });
                obs.observe(parentPanel, { attributes: true, attributeFilter: ['hidden', 'style', 'class'] });
                return; // wait for observer to trigger layout
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initContactSlider);
    } else {
        initContactSlider();
    }

    // Re-initialize when tab becomes visible (for tab navigation)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && isMobile()) {
            initContactSlider();
        }
    });

})();
