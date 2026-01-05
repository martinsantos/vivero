(function(){
  function initCarousel(root){
    var track = root.querySelector('.carousel-track');
    var list = track ? track.querySelector('.products-carousel') : null;
    if (!track || !list) return;

    function maxScroll(){ return track.scrollWidth - track.clientWidth; }
    function atStart(){ return track.scrollLeft <= 2; }
    function atEnd(){ return track.scrollLeft >= maxScroll() - 2; }
    function updateState(){
      root.classList.toggle('at-start', atStart());
      root.classList.toggle('at-end', atEnd());
    }

    // Paso de scroll para gestos (si se quisiera usar por teclado en el futuro)
    var scrollStep = function(){ return Math.max(track.clientWidth * 0.9, 200); };

    // Snap suave al ítem más cercano
    var snapTimer = null;
    function snapToNearest(){
      // Evitar snap si ya está en extremos y alineado
      var items = list.querySelectorAll('li.product');
      if (!items.length) return;
      var trackRect = track.getBoundingClientRect();
      var bestDelta = null, bestItem = null;
      items.forEach(function(item){
        var delta = item.getBoundingClientRect().left - trackRect.left;
        if (bestDelta === null || Math.abs(delta) < Math.abs(bestDelta)) {
          bestDelta = delta;
          bestItem = item;
        }
      });
      if (bestItem && Math.abs(bestDelta) > 2) {
        track.scrollBy({ left: bestDelta, behavior: 'smooth' });
      }
    }
    function scheduleSnap(){
      if (pointerDown) return; // no snap durante drag activo
      if (snapTimer) window.clearTimeout(snapTimer);
      snapTimer = window.setTimeout(snapToNearest, 120);
    }

    // Mouse wheel: convertir vertical a horizontal
    track.addEventListener('wheel', function(e){
      if (e.ctrlKey) return; // respeta zoom del navegador
      if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
        track.scrollLeft += e.deltaY;
        e.preventDefault();
        scheduleSnap();
      }
    }, { passive: false });

    // Drag with pointer (mouse/touch) - clean approach
    var pointerDown = false, startX = 0, startLeft = 0, moved = false;
    track.addEventListener('pointerdown', function(e){
      // Allow buttons and links to work normally
      if (e.target.closest('.button, .add_to_cart_button, .ajax_add_to_cart, .product-image-link, .product-title-link')) {
        return; // Don't start drag on interactive elements
      }
      
      pointerDown = true;
      startX = e.clientX;
      startLeft = track.scrollLeft;
      track.setPointerCapture(e.pointerId);
      if (snapTimer) window.clearTimeout(snapTimer);
      moved = false;
    });
    track.addEventListener('pointermove', function(e){
      if (!pointerDown) return;
      var dx = e.clientX - startX;
      track.scrollLeft = startLeft - dx;
      if (Math.abs(dx) > 5) {
        if (!moved) {
          // Se supera el umbral: activar estado de dragging (deshabilita overlay por CSS)
          root.classList.add('dragging');
        }
        moved = true; // umbral para considerar drag
      }
      updateState();
    });
    function endDrag(e){
      if (!pointerDown) return;
      pointerDown = false;
      try { track.releasePointerCapture(e.pointerId); } catch(_){}
      root.classList.remove('dragging');
      // Si hubo movimiento, marca que fue un drag para cancelar el próximo click
      if (moved) {
        root.dataset.wasDragging = '1';
        window.setTimeout(function(){ delete root.dataset.wasDragging; }, 180);
      }
      scheduleSnap();
    }
    track.addEventListener('pointerup', endDrag);
    track.addEventListener('pointercancel', endDrag);
    track.addEventListener('pointerleave', endDrag);

    // WOOCOMMERCE INTEGRATION: Ensure WooCommerce AJAX buttons work properly
    root.addEventListener('click', function(e) {
      // If it was a drag, don't process clicks
      if (root.dataset.wasDragging === '1') {
        e.preventDefault();
        e.stopPropagation();
        return false;
      }
      
      // Handle WooCommerce add to cart buttons
      const button = e.target.closest('.add_to_cart_button');
      if (button) {
        console.log('WooCommerce add to cart button clicked');
        
        // For AJAX add to cart buttons, enhance with visual feedback
        if (button.classList.contains('ajax_add_to_cart')) {
          console.log('AJAX add to cart - adding visual feedback');
          
          // Add visual feedback but let WooCommerce handle the AJAX
          const originalText = button.innerHTML;
          button.innerHTML = '⌛ Agregando...';
          button.disabled = true;
          
          // Listen for WooCommerce's add to cart success event (if jQuery available)
          if (typeof jQuery !== 'undefined') {
            jQuery(document.body).one('added_to_cart', function(event, fragments, cart_hash, $button) {
              if ($button && $button.get(0) === button) {
                button.innerHTML = '✅ ¡Agregado!';
                setTimeout(function() {
                  button.innerHTML = originalText;
                  button.disabled = false;
                }, 2000);
              }
            });
          }
          
          // Fallback in case WooCommerce event doesn't fire
          setTimeout(function() {
            if (button.disabled) {
              button.innerHTML = '✅ ¡Agregado!';
              setTimeout(function() {
                button.innerHTML = originalText;
                button.disabled = false;
              }, 1000);
            }
          }, 3000);
          
          return; // Let WooCommerce's own AJAX handler work
        }
        
        // For non-AJAX buttons, prevent navigation and handle manually
        if (!button.classList.contains('ajax_add_to_cart')) {
          e.preventDefault();
          console.log('Non-AJAX button - handling manually');
          
          // Trigger the button's form submission via AJAX
          const form = button.closest('form');
          if (form) {
            const formData = new FormData(form);
            
            fetch(form.action || window.location.href, {
              method: 'POST',
              body: formData
            }).then(() => {
              // Show success feedback
              const originalText = button.innerHTML;
              button.innerHTML = '✅ Added!';
              setTimeout(() => {
                button.innerHTML = originalText;
              }, 2000);
            });
          }
        }
        return;
      }
      
      // Let product links work normally - no interference!
      if (e.target.closest('.product-image-link, .product-title-link')) {
        console.log('Product link clicked - normal navigation');
        return; // Let normal links work
      }
    });

    // Clean approach: no overlay conflicts to worry about

    track.addEventListener('scroll', function(){ updateState(); scheduleSnap(); });
    window.addEventListener('resize', function(){ updateState(); scheduleSnap(); });
    // Inicial
    updateState();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function(){
      document.querySelectorAll('[data-carousel]').forEach(initCarousel);
    });
  } else {
    document.querySelectorAll('[data-carousel]').forEach(initCarousel);
  }
})();
