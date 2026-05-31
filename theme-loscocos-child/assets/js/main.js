/**
 * Los Cocos Child Theme Main JavaScript
 * Vanilla JS (no jQuery dependency)
 */
(function () {
    'use strict';

    // Header scroll detection
    document.addEventListener('DOMContentLoaded', function () {
        const header = document.getElementById('masthead');
        if (!header) return;

        var isFrontPage = document.body.classList.contains('home');

        if (isFrontPage) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 50) {
                    header.classList.remove('bg-transparent', 'text-white');
                    header.classList.add('bg-white', 'text-primary-dark', 'shadow-sm');
                } else {
                    header.classList.add('bg-transparent', 'text-white');
                    header.classList.remove('bg-white', 'text-primary-dark', 'shadow-sm');
                }
            });
        }
    });

    // Product gallery fix
    document.addEventListener('DOMContentLoaded', function () {
        var gallery = document.querySelector('.woocommerce-product-gallery');
        if (gallery) {
            gallery.style.opacity = '1';
        }
    });

    // Search toggle
    document.addEventListener('DOMContentLoaded', function () {
        var searchToggle = document.getElementById('search-toggle');
        var searchBar = document.getElementById('search-bar');
        if (searchToggle && searchBar) {
            searchToggle.addEventListener('click', function () {
                var mobileNav = document.getElementById('mobile-navigation');
                var menuToggle = document.getElementById('mobile-menu-toggle');

                if (mobileNav && !mobileNav.classList.contains('hidden')) {
                    mobileNav.classList.add('hidden');
                    if (menuToggle) {
                        menuToggle.setAttribute('aria-expanded', 'false');
                    }
                }

                searchBar.classList.toggle('hidden');
                searchToggle.setAttribute('aria-expanded', searchBar.classList.contains('hidden') ? 'false' : 'true');
                if (!searchBar.classList.contains('hidden')) {
                    var searchInput = searchBar.querySelector('input[type="search"]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }
            });
        }
    });

    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function () {
        var menuToggle = document.getElementById('mobile-menu-toggle');
        var mobileNav = document.getElementById('mobile-navigation');
        if (menuToggle && mobileNav) {
            menuToggle.addEventListener('click', function () {
                var searchBar = document.getElementById('search-bar');
                var searchToggle = document.getElementById('search-toggle');

                if (searchBar && !searchBar.classList.contains('hidden')) {
                    searchBar.classList.add('hidden');
                    if (searchToggle) {
                        searchToggle.setAttribute('aria-expanded', 'false');
                    }
                }

                mobileNav.classList.toggle('hidden');
                menuToggle.setAttribute('aria-expanded', mobileNav.classList.contains('hidden') ? 'false' : 'true');
            });
        }
    });

})();
