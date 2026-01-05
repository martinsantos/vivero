/**
 * Debug utilities for Los Cocos Theme
 */

(function($) {
    'use strict';

    /**
     * Debug element inspection utility
     * @param {string} id - Element ID to inspect
     * @returns {object} Element debug information
     */
    window.debugElement = function(id) {
        const element = document.getElementById(id);
        if (!element) {
            console.warn(`Element with ID "${id}" not found`);
            return null;
        }

        const debugInfo = {
            element: element,
            type: element.tagName.toLowerCase(),
            classes: Array.from(element.classList),
            attributes: {},
            styles: window.getComputedStyle(element),
            position: element.getBoundingClientRect(),
            children: element.children.length,
            innerHTML: element.innerHTML,
            eventListeners: getElementListeners(element)
        };

        // Get all attributes
        Array.from(element.attributes).forEach(attr => {
            debugInfo.attributes[attr.name] = attr.value;
        });

        console.log('Debug Info for #' + id + ':', debugInfo);
        return debugInfo;
    };

    /**
     * Get registered event listeners for an element
     * @param {HTMLElement} element - DOM element to inspect
     * @returns {object} Registered event listeners
     */
    function getElementListeners(element) {
        const listeners = {};
        const events = ['click', 'change', 'submit', 'keyup', 'keydown', 'mouseenter', 'mouseleave'];
        
        events.forEach(event => {
            if (element['on' + event]) {
                listeners[event] = 'Inline handler present';
            }
            if (element.getAttribute('on' + event)) {
                listeners[event] = 'Attribute handler present';
            }
            // jQuery events
            const jqEvents = $._data(element, 'events');
            if (jqEvents && jqEvents[event]) {
                listeners[event] = (listeners[event] || []).concat(
                    'jQuery handlers: ' + jqEvents[event].length
                );
            }
        });

        return listeners;
    }

})(jQuery);
