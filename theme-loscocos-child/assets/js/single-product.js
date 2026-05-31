/**
 * Single Product - Quantity buttons & Custom Tabs
 */
document.addEventListener('DOMContentLoaded', function() {
    // Quantity buttons
    document.querySelectorAll('.qty-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = this.parentNode.querySelector('input.qty');
            var currentVal = parseInt(input.value) || 1;
            var max = parseInt(input.getAttribute('max')) || 9999;
            var min = parseInt(input.getAttribute('min')) || 1;

            if (this.classList.contains('plus')) {
                if (currentVal < max) input.value = currentVal + 1;
            } else {
                if (currentVal > min) input.value = currentVal - 1;
            }
        });
    });

    // Custom Tabs
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tabId = this.getAttribute('data-tab');

            // Update buttons
            document.querySelectorAll('.tab-btn').forEach(function(b) {
                b.classList.remove('active', 'text-primary', 'border-primary');
                b.classList.add('text-neutral-500', 'border-transparent');
            });
            this.classList.add('active', 'text-primary', 'border-primary');
            this.classList.remove('text-neutral-500', 'border-transparent');

            // Update content
            document.querySelectorAll('.tab-content').forEach(function(c) {
                c.classList.add('hidden');
                c.classList.remove('active');
            });
            var selectedPanel = document.getElementById('tab-' + tabId);
            if (selectedPanel) {
                selectedPanel.classList.remove('hidden');
                selectedPanel.classList.add('active');
            }
        });
    });

    // Initialize first tab
    var firstTab = document.querySelector('.tab-btn');
    if (firstTab) {
        firstTab.classList.add('text-primary', 'border-primary');
    }
});
