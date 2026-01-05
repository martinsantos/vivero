/**
 * Los Cocos Theme - Performance Optimizations
 * Handles lazy loading, memory management, and performance improvements
 */

jQuery(document).ready(function($) {
    'use strict';

    // Performance configuration
    const PERF_CONFIG = {
        lazyLoad: {
            rootMargin: '200px 0px',
            threshold: 0.01
        },
        cache: {
            ttl: 300000, // 5 minutes
            prefix: 'loscocos_'
        },
        animation: {
            duration: 300
        }
    };

    /**
     * Initialize performance optimizations
     */
    function initPerformance() {
        initLazyLoading();
        optimizeEventHandlers();
        initIntersectionObserver();
        initRequestIdleCallback();
    }

    /**
     * Initialize lazy loading for images and iframes
     */
    function initLazyLoading() {
        if ('loading' in HTMLImageElement.prototype) {
            // Native lazy loading is supported
            $('img[loading="lazy"]').each(function() {
                this.loading = 'lazy';
            });
        } else {
            // Fallback for browsers without native lazy loading
            const lazyImages = [].slice.call(document.querySelectorAll('img[loading="lazy"]'));
            
            if ('IntersectionObserver' in window) {
                const lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src || '';
                            lazyImage.srcset = lazyImage.dataset.srcset || '';
                            lazyImage.classList.remove('lazy');
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                }, PERF_CONFIG.lazyLoad);

                lazyImages.forEach(function(lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            }
        }
    }

    /**
     * Optimize event handlers
     */
    function optimizeEventHandlers() {
        // Use event delegation for dynamic elements
        $(document)
            .off('click', '.add_to_cart_button')
            .on('click', '.add_to_cart_button', handleAddToCart);
            
        $(document)
            .off('change input', '.qty')
            .on('change input', '.qty', handleQuantityChange);
            
        $(document)
            .off('click', '.remove')
            .on('click', '.remove', handleRemoveItem);
    }

    /**
     * Initialize Intersection Observer for performance
     */
    function initIntersectionObserver() {
        if (!('IntersectionObserver' in window)) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;
                    if (target.dataset.src) {
                        target.src = target.dataset.src;
                        target.removeAttribute('data-src');
                    }
                    observer.unobserve(target);
                }
            });
        }, PERF_CONFIG.lazyLoad);

        // Observe all lazy-loaded elements
        document.querySelectorAll('[data-src]').forEach(el => observer.observe(el));
    }

    /**
     * Initialize requestIdleCallback for non-critical work
     */
    function initRequestIdleCallback() {
        if (!('requestIdleCallback' in window)) return;
        
        window.requestIdleCallback(processIdleWork, { timeout: 2000 });
    }

    /**
     * Process non-critical work during idle periods
     */
    function processIdleWork(deadline) {
        while ((deadline.timeRemaining() > 0 || deadline.didTimeout) && idleWorkQueue.length) {
            const task = idleWorkQueue.shift();
            if (task && typeof task === 'function') {
                task();
            }
        }
        
        if (idleWorkQueue.length) {
            window.requestIdleCallback(processIdleWork, { timeout: 2000 });
        }
    }

    /**
     * Cache management
     */
    const cacheManager = {
        set: function(key, value, ttl = PERF_CONFIG.cache.ttl) {
            const item = {
                value: value,
                expiry: Date.now() + ttl
            };
            localStorage.setItem(PERF_CONFIG.cache.prefix + key, JSON.stringify(item));
        },
        
        get: function(key) {
            const itemStr = localStorage.getItem(PERF_CONFIG.cache.prefix + key);
            if (!itemStr) return null;
            
            const item = JSON.parse(itemStr);
            if (Date.now() > item.expiry) {
                localStorage.removeItem(PERF_CONFIG.cache.prefix + key);
                return null;
            }
            
            return item.value;
        },
        
        remove: function(key) {
            localStorage.removeItem(PERF_CONFIG.cache.prefix + key);
        }
    };

    /**
     * Optimized animation frame handling
     */
    const animationManager = {
        animations: new Map(),
        
        add: function(id, callback) {
            if (!this.animations.has(id)) {
                this.animations.set(id, {
                    id: id,
                    callback: callback,
                    running: false,
                    frameId: null
                });
                this.startAnimation(id);
            }
        },
        
        startAnimation: function(id) {
            const animation = this.animations.get(id);
            if (!animation || animation.running) return;
            
            animation.running = true;
            
            const animate = () => {
                if (!animation.running) return;
                animation.callback();
                animation.frameId = requestAnimationFrame(animate);
            };
            
            animation.frameId = requestAnimationFrame(animate);
        },
        
        stopAnimation: function(id) {
            const animation = this.animations.get(id);
            if (!animation || !animation.running) return;
            
            cancelAnimationFrame(animation.frameId);
            animation.running = false;
        },
        
        remove: function(id) {
            this.stopAnimation(id);
            this.animations.delete(id);
        },
        
        clearAll: function() {
            this.animations.forEach((_, id) => this.remove(id));
        }
    };

    // Initialize optimizations
    initPerformance();

    // Clean up on page unload
    $(window).on('unload', function() {
        animationManager.clearAll();
    });

    // Export public methods
    window.LosCocosPerf = {
        cache: cacheManager,
        animation: animationManager
    };
});
